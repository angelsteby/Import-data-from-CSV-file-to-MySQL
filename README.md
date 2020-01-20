# Import data from CSV file to MySQL
PHP Script for importing data from CSV file to MySQL database

### Prerequisites:
1) PHP version 7.2.x

2) MySQL database server version 5.7

3) Database named task1 must be created prior to execution of PHP script / or change $db_name in user_upload.php to the database name created.

### Usage:
1) Run php user_upload.php --help from command line which will outline all directives that can be used with the script.

2) Nomal execution will be, php user_upload.php -u [MySQL user name] -p [password] -h [host] --file [file name/path to csv file]

3) --create_table option can be used to just create the database table.

4) --dry_run option can be used with the --file directive in case we want to run the script but not insert into the DB. All other functions will be executed, but the database won't be altered.
