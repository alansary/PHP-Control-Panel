<?php

	session_Start();

	/*********************************************************************************************
	 * AUTHORIZED USER
     *********************************************************************************************/
    if (isset($_SESSION['id'])) {
		header('Location: dashboard.php');
		exit();
	}
    
    require_once 'config.php';
    require_once 'directories.php';
	require_once 'Hash.php';
    require_once $cls.'TestInput.php';
    require_once $func.'functions.php';
    require_once $tpl.'header.php';
    ?>
    	</head>
        <body>
            <div class="container">
    <?php
	// Check if user HTTP Request is POST Request
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$username = $_POST['username'];
		$username = TestInput::test($username);
		$password = $_POST['password'];
		$password = TestInput::test($password);

		// Check if the user exist in database
		$query = "SELECT * FROM users WHERE username = :username";
		$stmt = $conn->prepare($query);
		$stmt->bindParam(':username', $username, PDO::PARAM_STR);
		$stmt->execute();

		if (isset($stmt->errorInfo()[2])) {
			$_SESSION['error'] = 'ERROR: '.$stmt->errorInfo()[2];
		} else {
			if ($stmt->rowCount() == 1) {
				$data = $stmt->fetch(PDO::FETCH_ASSOC);
				if (Hash::check($password, $data['password'])) {
					if (isset($_POST['remember'])&& $_POST['remember'] == "1") {
						$year = 31536000;
						setcookie('remember_username', $_POST['username'], time() + $year, "");
						setcookie('remember_password', $_POST['password'], time() + $year, "");
					}
					else {
						if (isset($_COOKIE['remember_username'])) {
							setcookie('remember_username', $_POST['username'], time() - $year, "");
						}
						if (isset($_COOKIE['remember_password'])) {
							setcookie('remember_password', $_POST['password'], time() - $year, "");
						}
					}
					$_SESSION['id'] = $data['id'];
					$_SESSION['username'] = $data['username'];
					header('Location: dashboard.php');
					exit();
				} else {
					$_SESSION['error'] = "The username and password you entered did not match our records. Please double-check and try again.";
				}
			} else {
				$_SESSION['error'] = "The username and password you entered did not match our records. Please double-check and try again.";
			}
		}
	}
?>
	<?php if (isset($_SESSION['error'])) { ?>
		<div class="alert alert-danger">
			<?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
		</div>
	<?php } ?>
	<form class="login" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
		<h4 class="text-center">Admin Login</h4>
		<input class="form-control" type="text" name="username" placeholder="<?php echo isset($_COOKIE['remember_username']) ? $_COOKIE['remember_username'] : 'Username'; ?>" autocomplete="off" value="<?php echo isset($_COOKIE['remember_username']) ? $_COOKIE['remember_username'] : ''; ?>">
		<input class="form-control" type="password" name="password" placeholder="<?php echo isset($_COOKIE['remember_username']) ? $_COOKIE['remember_username'] : 'Password'; ?>" autocomplete="new-password" value="<?php echo isset($_COOKIE['remember_password']) ? $_COOKIE['remember_password'] : ''; ?>">
		<?php if (isset($_COOKIE['remember_username']) && isset($_COOKIE['remember_password'])) { ?>
			<input type="checkbox" name="remember" value="1" checked> Remember Me
		<?php } else { ?>
			<input type="checkbox" name="remember" value="1"> Rememeber Me
		<?php } ?>
		<input class="btn btn-primary btn-block" type="submit" value="Login">
	</form>
</body>
</html>