<?php
include 'config.php';

//Initialize parameters
$db_name = "task1";
$table = "Users";

//get the directives
$data = getopt(null,["create_table","dry_run","file:","help"]);
$dry_run = (array_key_exists('dry_run',$data))?$data["dry_run"]:null;
$file_name = (array_key_exists('file',$data))?$data["file"]:null;

if(array_key_exists('help',$data)){
  scriptHelp();
  exit;
}

//try connecting to MySQL and catch any exception
try{
  $conn= new PDO("mysql:host=$host;unix_socket=/tmp/mysql.sock;dbname=$db_name",$user_name,$password);
}catch(PDOException $e){
  echo ("\n Error:".$e->getMessage());
  exit("\n Please enter valid MySQL credentials \n");
}
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// create/reset table if directive present
if(array_key_exists('create_table',$data)){
  checkAndCreate($conn,$table);
  exit(" \n Users Table Created \n");
}
//If table exists continue with the code otherwise create table
$table_exists = tableExists($conn, $table);
if(!$table_exists && !(array_key_exists('dry_run',$data))){
  checkAndCreate($conn,$table);
}

// validate the file
if(empty($file_name)){
  exit("\n Please enter the csv file that needs to be processed \n");
}

validateFile($conn, $file_name);
processCsv($conn, $file_name,$data);




/**
 * Validate the csv file.
 *
 * @param CONN $conn PDO instance connected to a database.
 * @param string $file_name file that needs to be validated.
 */

 function validateFile($conn, $file_name){

   //validate the file given is csv
   $csv_mime_types = [ 'text/csv','text/plain','application/csv','text/comma-separated-values',
              'application/excel','application/vnd.ms-excel','application/vnd.msexcel',
              'text/anytext','application/octet-stream','application/txt',];

   $finfo = finfo_open( FILEINFO_MIME_TYPE );
   $mime_type = @finfo_file( $finfo, $file_name );
   finfo_close($finfo);

   if(!in_array($mime_type,$csv_mime_types)){
     exit("\n Please enter a valid csv file \n");
   }

   if(filesize($file_name) == 0){
     exit("\n Please enter a valid csv file \n");
   }

 }

 /**
  * Validate the details and insert details in to database from csv file.
  *
  * @param CONN $conn PDO instance connected to a database.
  * @param string $file_name file that needs to be parsed.
  * @param array $data to check status of dry_run directive
  */

function processCsv($conn, $file_name,$data){

  $file = fopen($file_name, "r");
  while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {

    $name = ucwords(strtolower($column[0]));
    $surname = ucwords(strtolower($column[1]));
    $email = strtolower($column[2]);
    $email = rtrim($email);

    //Surpass the heading if present
    if($email == "email"){
        continue;
    }

    //check email is valid or not
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      echo("\n $email is not a valid email address \n" );
      continue;
    }

    //sql to insert data
    $sqlInsert = "INSERT into users (name,surname,email) values (?,?,?)";
    //if dry_run no need to insert the data
    if(array_key_exists('dry_run',$data)){
      continue;
    }

    try {
      $result =  $conn->prepare($sqlInsert);
      $result->execute([$name,$surname,$email]);
    } catch (PDOException $e) {
      $error_info = $e->errorInfo;
      if($error_info[0] == 23000){
        echo("\n $email already exists \n ");
      }else{
        echo( "\n Error:".$e->getMessage());
      }
    }

  }

}


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
   $sql = "CREATE TABLE Users (
     `id` INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
     `name` VARCHAR(50) NOT NULL,
     `surname` VARCHAR(50) NOT NULL,
     `email` VARCHAR(100),
     UNIQUE KEY email (`email`)
   )";

   try {
     $conn->exec("DROP TABLE if exists $table");
     $conn->exec($sql);
   } catch (PDOException $e) {
     exit( "\n Error:".$e->getMessage());
   }
   return TRUE;
 }

 /**
  * Display command line directives for the php script
  *
  */

  function scriptHelp(){
    echo("\n --file [csv file name] – Required field, this is the name of the CSV to be parsed. \n");
    echo("\n --create_table – Optional field, this will cause the MySQL users table to be built (and no further action will be taken).\n");
    echo("\n --dry_run – Optional field, this will be used with the --file directive in case we want to run the
            script but not insert into the DB. All other functions will be executed, but the
            database won't be altered.\n");
    echo("\n -u – Required field, MySQL username \n \n -p – Required field, MySQL password \n \n -h – Required field, MySQL host \n");
    echo("\n --help - Optional field, display details of directives that can be used along with the script.\n\n");

  }

?>
