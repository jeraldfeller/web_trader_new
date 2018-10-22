<?php



/**

 * Created by PhpStorm.

 * User: Grabe Grabe

 * Date: 2/25/2018

 * Time: 12:40 PM

 */

class HistoryNew

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

              FROM `'.$coin.'_table_new`

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

                  FROM `'.$coin.'_table_new`

                  WHERE `timestamp` >= "'.$dateBefore.'" && `timestamp` < "'.$dateAfter.'"';

          $stmt = $pdo->prepare($sql);

          $stmt->execute();

          $result = array();

          $group = array();

        }else{

          $sql = 'SELECT *

                  FROM `'.$coin.'_table_new`

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

                  'timestamp' => $timestamp,

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

                  'timestamp' => $timestamp,

                  'open' => $ohcl[0],

                  'max' => $ohcl[1],

                  'min' => $ohcl[2],

                  'close' => $ohcl[3]

              );



            }





        }

        return json_encode(array_values($minuteGroup));

    }





    public function getCoinHistoryNew($coin, $device){



        if($device == 'mobile'){

          $dateBefore = date('Y-m-d H:i:s', strtotime('-120 minutes'));

        }else{

          $dateBefore = date('Y-m-d H:i:s', strtotime('-250 minutes'));

        }





        $pdo = $this->getPdo();

        $sql = 'SELECT *

                FROM `'.$coin.'_table_new`

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

