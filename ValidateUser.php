<?php

require_once 'directories.php';
require_once $cls.'TestInput.php';

class ValidateUser {
	public static function validateLogin($username, $password) {

		$dsn = 'mysql:host=localhost;port=3306;dbname=shop';
		try {
			$conn = new PDO($dsn, 'alansary', '123456');
			$conn->exec('set names utf8');
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
		} catch(PDOException $e) {
			echo $e->getMessage();
		}

		$username = TestInput::test($username);
		$username = filter_var($username, FILTER_SANITIZE_STRING);
		$password = TestInput::test($password);
		$password = filter_var($password, FILTER_SANITIZE_STRING);

		$query = "SELECT * FROM users WHERE username = :username";
		$stmt = $conn->prepare($query);
		$stmt->bindParam(':username', $username);
		$stmt->execute();
		if ($stmt->rowCount()) {
			$user = $stmt->fetch(PDO::FETCH_ASSOC);
			return password_verify($password, $user['password']);
		}
		return 0;
	}
}