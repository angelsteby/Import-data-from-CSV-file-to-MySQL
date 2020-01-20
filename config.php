<?php
//Obtain the credentials for mysql
$user_name = getopt("u:");
$host = getopt("h:");
$password = getopt("p:");
$help = getopt(null,["help"]);

//if asked for help option just return
if(!empty($help)){
  return;
}

//validate the input values
if(empty($user_name)){
  exit("Please enter MySQL user name \n");
}
if(empty($host)){
  exit("Please enter MySQL host \n");
}

$user_name = $user_name['u'];
$host = $host['h'];

//sometimes on local machine mysql is set to not have password. So following line is to include that condition
$password = (empty($password))?'':$password['p'];

?>
