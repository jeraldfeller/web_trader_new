<?php

/**
 * Created by PhpStorm.
 * User: Grabe Grabe
 * Date: 2/25/2018
 * Time: 12:40 PM
 */
class History
{
    public $debug = FALSE;
    protected $db_pdo;

    public function pdoQuoteValue($value)
    {
        $pdo = $this->getPdo();
        return $pdo->quote($value);
    }
    public function getCoinHistoryApi($coin){
      $pdo = $this->getPdo();
      $sql = 'SELECT *
              FROM `'.$coin.'_table`
              ORDER BY `timestamp` DESC LIMIT 1';
      $stmt = $pdo->prepare($sql);
      $stmt->execute();

      $row = $stmt->fetch(PDO::FETCH_ASSOC);

      return json_encode($row);
    }
    public function getCoinHistory($coin, $device){

        if($device == 'mobile'){
          $dateBefore = date('Y-m-d H:i:s', strtotime('-120 minutes'));
        }else if($device == 'desktop'){
          $dateBefore = date('Y-m-d H:i:s', strtotime('-250 minutes'));
        }else{
          $dateBefore = date('Y-m-d H:i:s', strtotime('-2 minutes'));
          $dateAfter = date('Y-m-d H:i:s', strtotime('-1 minutes'));
        }


        $pdo = $this->getPdo();
        if($device == 'api'){
          $sql = 'SELECT *
                  FROM `'.$coin.'_table`
                  WHERE `timestamp` >= "'.$dateBefore.'" && `timestamp` < "'.$dateAfter.'"';
          $stmt = $pdo->prepare($sql);
          $stmt->execute();
          $result = array();
          $group = array();
        }else{
          $sql = 'SELECT *
                  FROM `'.$coin.'_table`
                  WHERE `timestamp` > "'.$dateBefore.'"';
          $stmt = $pdo->prepare($sql);
          $stmt->execute();
          $result = array();
          $group = array();
        }



        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $timestampGroup = explode(' ', $row['timestamp']);
            $timestampHourMin = explode(':', $timestampGroup[1]);
            $timestamp = $timestampGroup[0].'T'.$timestampHourMin[0].':'.$timestampHourMin[1].':00.000Z';

              if(isset($minuteGroup[$timestamp])){
                $group[] = $row['dollar_price'];
                $ohcl = $this->computeOHLC($group);
                $minuteGroup[$timestamp] = array(
                    'timestamp' => $row['timestamp'],
                    'open' => $ohcl[0],
                    'max' => $ohcl[1],
                    'min' => $ohcl[2],
                    'close' => $ohcl[3]
                );
              }else{
                $group = array();

                $group[] = $row['dollar_price'];
                $ohcl = $this->computeOHLC($group);
                $minuteGroup[$timestamp] = array(
                    'timestamp' => $row['timestamp'],
                    'open' => $ohcl[0],
                    'max' => $ohcl[1],
                    'min' => $ohcl[2],
                    'close' => $ohcl[3]
                );

              }


        }
        return json_encode(array_values($minuteGroup));
    }

    public function getCoinHistoryMM($coin, $device, $mm){

        if($device == 'mobile'){
          $dateBefore = date('Y-m-d H:i:s', strtotime('-120 minutes'));
        }else if($device == 'desktop'){
          $dateBefore = date('Y-m-d H:i:s', strtotime('-250 minutes'));
        }else{
          $dateBefore = date('Y-m-d H:i:s', strtotime('-2 minutes'));
          $dateAfter = date('Y-m-d H:i:s', strtotime('-1 minutes'));
        }


        $pdo = $this->getPdo();
        if($device == 'api'){
          $sql = 'SELECT *
                  FROM `'.$coin.'_table`
                  WHERE `timestamp` >= "'.$dateBefore.'" && `timestamp` < "'.$dateAfter.'"';
          $stmt = $pdo->prepare($sql);
          $stmt->execute();
          $result = array();
          $group = array();
        }else{
          $sql = 'SELECT *
                  FROM `'.$coin.'_table`
                  WHERE `timestamp` > "'.$dateBefore.'"';
          $stmt = $pdo->prepare($sql);
          $stmt->execute();
          $result = array();
          $group = array();
        }


        $startDate = $dateBefore;
        $currentMinute = ceil(date('i') / 5) * 5;

        if($currentMinute == 60){
          $endDate = date("Y-m-d H:00:s", strtotime('+1 hours'));
        }else{
          $endDate = date("Y-m-d H:$currentMinute:s");
        }

        $start=strtotime($startDate);
        $end = strtotime($endDate);

        while($start<$end) {
        	$start = ceil($start/300)*300;

          $y = date('Y', $start);
          $m = date('m', $start);
          $d = date('d', $start);
          $h = date('H', $start);
          $i = date('i', $start);
          $minuteGroup[$y.'-'.$m.'-'.$d.'T'.$h.':'.$i.':00.000Z'] = array();
          $start++;
        }


        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $timestampGroup = explode(' ', $row['timestamp']);
            $timestampHourMin = explode(':', $timestampGroup[1]);
            $timestamp = $timestampGroup[0].'T'.$timestampHourMin[0].':'.$timestampHourMin[1].':00.000Z';
              foreach($minuteGroup as $key => $val){
                $keyDate = date('Y-m-d H:i:00', strtotime($key));
                $keyDatePrev = date('Y-m-d H:i:00', strtotime($keyDate. '-5 mins'));
                $recordTimestamp = date('Y-m-d H:i:00', strtotime($timestamp));

                if($recordTimestamp > $keyDatePrev && $recordTimestamp <= $keyDate){

                  $minuteGroup[$key][] = $row['dollar_price'];
                }

              }




        }


        $finalGroup = array();
        foreach($minuteGroup as $key => $val){
          $group = array();
          for($i = 0; $i < count($val); $i++){
            $group[] = $val[$i];
          }
          if(count($group) > 0){
            $ohcl = $this->computeOHLC($group);
            $finalGroup[] = array(
                'timestamp' => $key,
                'open' => $ohcl[0],
                'max' => $ohcl[1],
                'min' => $ohcl[2],
                'close' => $ohcl[3]
            );
          }

        }

        return json_encode($finalGroup);
    }

    public function getCoinHistoryMMNew($coin, $device, $mm){



        switch ($mm) {
          case '5mm':
            $deducMin = 5;
            $intervalUnix = 300;
            //$historyRange = 250 * 5;
            $historyRange = 24 * 5;
            break;
          case '30mm':
            $deducMin = 30;
            $intervalUnix = 1800;
            //$historyRange = 250 * 30;
            $historyRange = 24 * 30;
            break;
          case '60mm':
            $deducMin = 60;
            $intervalUnix = 3600;
            //$historyRange = 250 * 60;
            $historyRange = 24 * 60;
            break;
          case '240mm':
            $deducMin = 240;
            $intervalUnix = 14400;
            //$historyRange = 250 * 240;
            $historyRange = 24 * 240;
            break;
        }

        if($device == 'mobile'){
          $historyRange = $historyRange / 2;
        }

        $dateBefore = date('Y-m-d H:i:s', strtotime('-'.$historyRange.' minutes'));

        $startDate = $dateBefore;
        $currentMinute = ceil(date('i') / 5) * 5;

        if($currentMinute == 60){
          $endDate = date("Y-m-d H:00:s", strtotime('+1 hours'));
        }else{
          $endDate = date("Y-m-d H:$currentMinute:s");
        }

        $start=strtotime($startDate);
        $end = strtotime($endDate);

        while($start<$end) {
        	$start = ceil($start/$intervalUnix)*$intervalUnix;

          $y = date('Y', $start);
          $m = date('m', $start);
          $d = date('d', $start);
          $h = date('H', $start);
          $i = date('i', $start);
          $minuteGroup[$y.'-'.$m.'-'.$d.'T'.$h.':'.$i.':00.000Z'] = array();

          $start++;
        }




        foreach($minuteGroup as $key => $val){
          $keyDate = date('Y-m-d H:i:00', strtotime($key));
          $keyDatePrev = date('Y-m-d H:i:00', strtotime($keyDate. '-'.$deducMin.' mins'));
          $pdo = $this->getPdo();
          $sql = 'SELECT *
                  FROM `'.$coin.'_table`
                  WHERE `timestamp` > "'.$keyDatePrev.'" AND `timestamp` <= "'.$keyDate.'"';
          $stmt = $pdo->prepare($sql);
          $stmt->execute();
          $result = array();
          $group = array();
          while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

              $timestampGroup = explode(' ', $row['timestamp']);
              $timestampHourMin = explode(':', $timestampGroup[1]);
              $timestamp = $timestampGroup[0].'T'.$timestampHourMin[0].':'.$timestampHourMin[1].':00.000Z';
              $minuteGroup[$key][] = $row['dollar_price'];
          }

        }
        $finalGroup = array();
        foreach($minuteGroup as $key => $val){
          $group = array();
          for($i = 0; $i < count($val); $i++){
            $group[] = $val[$i];
          }
          if(count($group) > 0){
            $ohcl = $this->computeOHLC($group);
            $finalGroup[] = array(
                'timestamp' => $key,
                'open' => $ohcl[0],
                'max' => $ohcl[1],
                'min' => $ohcl[2],
                'close' => $ohcl[3]
            );
          }

        }

        return json_encode($finalGroup);
    }


    public function getCoinHistoryNew($coin, $device){

        if($device == 'mobile'){
          $dateBefore = date('Y-m-d H:i:s', strtotime('-120 minutes'));
        }else{
          $dateBefore = date('Y-m-d H:i:s', strtotime('-250 minutes'));
        }


        $pdo = $this->getPdo();
        $sql = 'SELECT *
                FROM `'.$coin.'_table`
                WHERE `timestamp` > "'.$dateBefore.'"  ORDER BY `timestamp` ASC';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = array();
        $group = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $timestampGroup = explode(' ', $row['timestamp']);
            $timestampHourMin = explode(':', $timestampGroup[1]);
            $timestamp = $timestampGroup[0].' '.$timestampHourMin[0].':'.$timestampHourMin[1].':00';

            if(isset($minuteGroup[$timestamp])){
              $group[] = $row['dollar_price'];
              $ohcl = $this->computeOHLC($group);
              $minuteGroup[$timestamp] = array(
                  'timestamp' => $timestamp,
                  'open' => $ohcl[0],
                  'close' => $ohcl[3],
                  'min' => $ohcl[2],
                  'max' => $ohcl[1]
              );
            }else{
              $group = array();

              $group[] = $row['dollar_price'];
              $ohcl = $this->computeOHLC($group);
              $minuteGroup[$timestamp] = array(
                  'timestamp' => $timestamp,
                  'open' => $ohcl[0],
                  'close' => $ohcl[3],
                  'min' => $ohcl[2],
                  'max' => $ohcl[1]
              );

            }


        }
        return json_encode(array_values($minuteGroup));
    }

    public function generateCoinHistory($data, $userId, $device){
      $csv = 'data/data-'.$device.'-'.$userId.'.csv';
      $csvData[] = implode('","', array(
          'Date',
          'Open',
          'High',
          'Low',
          'Close'
      ));
      foreach($data as $row){
        // record url

        $csvData[] = implode('","', array(
            $row['timestamp'],
            $row['open'],
            $row['max'],
            $row['min'],
            $row['close']
          )
        );

        $file = fopen($csv,"w");
        foreach ($csvData as $line){
        fputcsv($file, explode('","',$line));
        }
        fclose($file);

      }

      return true;
    }

    public function computeOHLC($values){
        $low = min($values);
        $high = max($values);
        $open = $values[0];
        $close = $values[count($values) - 1];

        return array($open, $high, $low, $close);
    }


    public function getTradeHistory($userId, $from, $to){
      $from = date('Y-m-d', strtotime($from));
      $to = date('Y-m-d', strtotime($to . '+1 days'));

      $pdo = $this->getPdo();
      $sql = "SELECT *
              FROM `trades`
              WHERE `user` = $userId AND `status` != 'live' AND `exec_time` >= '$from' AND `exec_time` <= '$to' ORDER BY `id` DESC";
      $stmt = $pdo->prepare($sql);
      $stmt->execute();
      $result = array();

      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $status = $row['status'];
        $payout = $row['payout'];
        $leverage = $row['leverage'];
        $entryTime = date('m/d/Y H:i', $row['entry_time']);
        $closingTime = date('m/d/Y H:i', $row['closing_time']);
        if($status == 'lost'){
          $pnl = $leverage;
        }else if($status == 'win'){
          $pnl = $payout - $leverage;
        }else{
          $pnl = $leverage;
        }

        if($row['type'] == 'Deposit' || $row['type'] == 'Withdraw'){
          $pnl = $row['payout'];
          $entryTime = date('m/d/Y H:i', strtotime($row['exec_time']));
          $closingTime = date('m/d/Y H:i', strtotime($row['exec_time']));
          $row['open'] = $row['type'];
          $row['close'] = $row['type'];
          $leverage = $row['type'];
          $status = $row['type'];
          $row['pair'] = $row['type'];
          $row['trade_percent_cut_amount'] = $row['type'];
        }
        $result[] = array(
          'entryTime' => $entryTime,
          'closingTime' => $closingTime,
          'position' => $row['type'],
          'pnl' => $pnl,
          'entryPrice' => $row['open'],
          'closingPrice' => $row['close'],
          'status' => $status,
          'leverage' => $leverage,
          'pair'  => $row['pair'],
          'trade_percent_cut' => $row['trade_percent_cut'],
          'trade_percentage_amount' => $row['trade_percent_cut_amount']
        );
      }

      return $result;
    }


    public function getPdo()
    {
        if (!$this->db_pdo)
        {
            if ($this->debug)
            {
                $this->db_pdo = new PDO(DB_DSN, DB_USER, DB_PWD, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
            }
            else
            {
                $this->db_pdo = new PDO(DB_DSN, DB_USER, DB_PWD);
            }
        }
        return $this->db_pdo;
    }
}
