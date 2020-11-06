<?php
// include_once('./crud/database.php');
include_once('../../../crud/database.php');



define('SS_DB_NAME', 'include');
define('SS_DB_USER', 'root');
define('SS_DB_PASSWORD', '');
define('SS_DB_HOST', 'localhost');
$dsn	= 	"mysql:dbname=".SS_DB_NAME.";host=".SS_DB_HOST."";
$pdo	=	"";
     $conn = new mysqli(SS_DB_HOST, SS_DB_USER, SS_DB_PASSWORD, SS_DB_NAME);

try {
	$pdo = new PDO($dsn, SS_DB_USER, SS_DB_PASSWORD);
}catch (PDOException $e) {
	echo "Connection failed: " . $e->getMessage();
}
$db 	=	new Database($pdo);
?>