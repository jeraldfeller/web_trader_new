<?php

class Options{
  public $debug = FALSE;
  protected $db_pdo;


  public function getOption($option){
    $pdo = $this->getPdo();
    $sql = 'SELECT `value` FROM `settings` WHERE `name` = "'.$option.'"';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $value = $stmt->fetch(PDO::FETCH_ASSOC)['value'];
    $pdo = null;

    return $value;
  }

  public function updateOption($name, $value){
    $pdo = $this->getPdo();
    $sql = 'UPDATE `settings` SET `value` = "'.$value.'" WHERE `name` = "'.$name.'"';
    $stmt = $pdo->prepare($sql);
    $return = $stmt->execute();
    $pdo = null;

    return $return;
  }


  public function pdoQuoteValue($value)
  {
      $pdo = $this->getPdo();
      return $pdo->quote($value);
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

?>
