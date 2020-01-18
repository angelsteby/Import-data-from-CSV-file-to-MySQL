<?php
include 'config.php';

//Initialize parameters
$db_name = "task1";
$table = "Users";

//get the directives
$create_table = getopt(null, ["create_table"]);
$dry_run = getopt(null, ["dry_run"]);
$file = getopt(null,["file:"]);


//try connecting to MySQL and catch any exception
try{
  $conn= new PDO("mysql:host=$host;unix_socket=/tmp/mysql.sock;dbname=$db_name",$user_name,$password);
}catch(PDOException $e){
  echo "\n Error:".$e->getMessage();
  exit("\n Please enter valid MySQL credentials \n");
}

// create/reset table if directive present
if(!empty($create_table)){
  checkAndCreate($conn,$table);
  exit(" \n Users Table Created \n");
}
//If table exists continue with the code otherwise create table
$table_exists = tableExists($conn, $table);
if(!$table_exists){
  checkAndCreate($conn,$table);
}
echo "All good";


/**
 * Check if a table exists in the current database.
 *
 * @param CONN $conn PDO instance connected to a database.
 * @param string $table Table to search for.
 */
function tableExists($conn, $table) {
    $results = $conn->query("SHOW TABLES LIKE '$table'");
    //If table not existing return TRUE
    if(!$results) { return FALSE; }
    //if table exists, return TRUE
    if($results->rowCount()>0){ return TRUE; }

}

/**
 * Check if a table exists in the current database.
 *
 * @param CONN $conn PDO instance connected to a database.
 * @param string $table Table to search for.
 */
 function checkAndCreate($conn,$table){
   //tableExists($pdo, $table);
   $sql = "CREATE TABLE Users (
     `id` INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
     `first_name` VARCHAR(50) NOT NULL,
     `last_name` VARCHAR(50) NOT NULL,
     `email` VARCHAR(100),
     UNIQUE KEY email (`email`)
   )";

   try {
     $conn->exec("DROP TABLE if exists '$table'");
     $conn->exec($sql);
     var_dump($conn);
   } catch (PDOException $e) {
     exit( "\n Error:".$e->getMessage());
   }
   return TRUE;
 }

?>
