<?php
// Database credentials
$host = 'localhost'; 
$db   = 'gestion_factures'; 
$user = 'root'; 
$pass = ''; 
$charset = 'utf8mb4'; 
$port = 3308; 

$options = [
  PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::ATTR_EMULATE_PREPARES   => false,
];

class Database
{
  private static $dbh = null;

  public static function connect()
  {
    global $host, $db, $user, $port, $pass, $charset, $options;

    if (self::$dbh === null) {
      $dsn = "mysql:host=$host;dbname=$db;charset=$charset;port=$port";
      try {
        self::$dbh = new PDO($dsn, $user, $pass, $options);
      } catch (\PDOException $e) {
        throw new \PDOException($e->getMessage(), (int)$e->getCode());
      }
    }
    return self::$dbh;
  }
}
