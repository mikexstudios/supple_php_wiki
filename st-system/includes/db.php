<?php


try{
	$Stdb = new PDO('mysql:dbname='.DB_NAME.';host='.DB_HOST, DB_USER, DB_PASSWORD);
} catch(PDOException $e) {
	echo 'Error connecting to the database: '.$e->getMessage();
	exit();
}

//We want exceptions to be thrown.
$Stdb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

?>