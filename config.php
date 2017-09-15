<?php

	/*********************************************************************************************
	 * DATABASE CONNECTION
     *********************************************************************************************/
     
	$dsn = 'mysql:host=localhost;port=3306;dbname=shop';
	$username = 'alansary';
	$password = '123456';
	try {
		$conn = new PDO($dsn, $username, $password);
		$conn->exec('set names utf8');
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
	} catch(PDOException $e) {
		echo $e->getMessage();
	}