<?php

/**
 * Created by PhpStorm.
 * User: Grabe Grabe
 * Date: 2/25/2018
 * Time: 12:40 PM
 */
class Socket
{
    public $debug = FALSE;
    protected $db_pdo;

    public function pdoQuoteValue($value)
    {
        $pdo = $this->getPdo();
        return $pdo->quote($value);
    }

    public function postCurrentPrice($table, $dollarPrice, $btcPrice, $timestamp, $coin){
        $pdo = $this->getPdo();
        $sql = 'INSERT INTO `'.$table.'` (`dollar_price`, `btc_price`, `timestamp`, `coin`) VALUES ('.$dollarPrice.','.$btcPrice.', "'.$timestamp.'", "'.$coin.'")';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();


    }


    public function getExchangeRates(){

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://api.coincap.io/v2/exchanges",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
          return $err;
        } else {
          return $response;
        }
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
