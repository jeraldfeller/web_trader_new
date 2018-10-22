<?php

/**
 * Created by PhpStorm.
 * User: Grabe Grabe
 * Date: 2/25/2018
 * Time: 12:40 PM
 */
class Crypto
{
    public $debug = FALSE;
    protected $db_pdo;

    public function pdoQuoteValue($value)
    {
        $pdo = $this->getPdo();
        return $pdo->quote($value);
    }

    public function updatePairing($pair, $amount){
        $pdo = $this->getPdo();
        $sql = 'UPDATE `pairing` SET `price` = ' . $amount . ' WHERE `pair` = "' . $pair . '"';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
    }

    public function getPairing(){
        $pdo = $this->getPdo();
        $sql = 'SELECT * FROM `pairing`';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = $row;
        }

        return json_encode($result);
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