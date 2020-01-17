<?php
include 'config.php';

//Initialize database name
$db_name = "task1";

//try connecting to MySQL and catch any exception
try{
  $conn= new PDO("mysql:host=$host;unix_socket=/tmp/mysql.sock;dbname=$db_name",$user_name,$password);
}catch(PDOException $e){
  echo "\n Error:".$e->getMessage();
  exit("\n Please enter valid MySQL credentials \n");
}

echo "All good";

?>
