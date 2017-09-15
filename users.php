<?php

	/*********************************************************************************************
	 * MANAGE USERS (ADD | EDIT | DELETE)
	 *********************************************************************************************/

	// error_reporting (E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
	// ini_set('display_errors', 1);

	if (!isset($_SESSION['id']) && !isset($_COOKIE['remember_username'])) {
		header('Location: index.php');
		exit();
	}

 	if (!isset($_SESSION['id'])) {
 		// Check the cookie
 		require_once 'ValidateUser.php';
 		if (!ValidateUser::ValidateLogin($_COOKIE['remember_username'], $_COOKIE['remember_password'])) {
 			header('Location: index.php');
 			exit();
 		}
 	}

	require_once 'Hash.php';

	$do = isset($_GET['do']) ? $_GET['do'] : 'manage';

	/*********************************************************************************************
	 * START MANAGE VIEW
	 *********************************************************************************************/

	if ($do == 'manage') { ?>

		<?php
		/*****************************************************************************************
		 * ERROR MESSAGES
		 *****************************************************************************************/
		?>

        <?php if (isset($_SESSION['deleteError'])) { ?>
            <div class="alert alert-danger">
                <?php echo $_SESSION['deleteError']; ?>
            </div>
        <?php unset($_SESSION['deleteError']); } ?>
        <?php if (isset($_SESSION['deleteSuccess'])) { ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['deleteSuccess']; ?>
            </div>
        <?php unset($_SESSION['deleteSuccess']); } ?>

		<?php
		/*****************************************************************************************
		 * MANAGE VIEW BODY
		 *****************************************************************************************/
		?>

	 	<div class="panel panel-default">
	 		<div class="panel-heading">
	 			Manage Users
			</div>
	 		<div class="panel-body">
	 			<?php
	 			$query = "SELECT * FROM users ORDER BY id";
	 			$stmt = $conn->prepare($query);
	 			$stmt->execute();
	 			if (!$stmt->rowCount()) { ?>
                    <h2>No Users To Show</h2>
                <?php } else { ?>
	 				<table class="table table-stripped table-hover text-center users-table">
	 					<thead>
		 					<tr>
		 						<th>ID</th>
		 						<th>Username</th>
		 						<th>Registeration Date</th>
		 						<th>Edit</th>
		 						<th>Delete</th>
							</tr>
	 					</thead>
	 					<tbody>
							<?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
								extract($row); ?>
								<!-- printing users data -->
					 			<tr>
					 				<td><?php echo $id; ?></td>
					 				<td><?php echo $username; ?></td>
					 				<td><?php echo $regDate; ?></td>
					 				<td><a href="dashboard.php?manage=user&do=edit&id=<?php echo $id; ?>" class="btn btn-primary btn-md"><i class="fa fa-edit"></i> Edit</a></td>
					 				<td>
					 					<form action="dashboard.php?manage=user&do=delete&id=<?php echo $id; ?>" method="POST">
					 						<input type="hidden" name="id" value="<?php echo $id; ?>">
					 						<input type="submit" value="Delete" class="btn btn-danger btn-md">
					 					</form>
									</td>
			 					</tr>
			 				<?php } // End while loop ?>
				 		</tbody>
					</table>
				<?php } // End printing users ?>
	 		</div> <!-- End panel body -->
	 		<div class="panel-footer">
	 			<a href="dashboard.php?manage=user&do=add" class="btn btn-primary btn-md"><i class="fa fa-plus"></i> Add New User</a>
	 		</div>
	 	</div> <!-- End panel -->
	<?php } // End manage view

	/*********************************************************************************************
	 * START EDIT VIEW
	 *********************************************************************************************/

	elseif ($do == 'edit') { 
		$id = (isset($_GET['id']) && is_numeric($_GET['id'])) ? intval($_GET['id']) : 0;

		/*********************************************************************************************
		 * ID DOESN'T VALID
		 *********************************************************************************************/

		if (!$id) {
 			redirect404();
		}

		/*********************************************************************************************
		 * ID IS VALID
		 *********************************************************************************************/

		else {
	 		$query = "SELECT * FROM users WHERE id = :id";
	 		$stmt = $conn->prepare($query);
	 		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
	 		$stmt->execute();

			/**************************************************************************
			 * ID DOESN'T EXIST
			 **************************************************************************/

			if (!$stmt->rowCount()) {
	 			redirect404();
			}

			/**************************************************************************
			 * ID DOES EXIST
			 **************************************************************************/

		 	else {
		 		$data = $stmt->fetch(PDO::FETCH_ASSOC);
		 		?>
				<div class="col-md-8 col-md-offset-2">

                    <?php
                    /**************************************************************************
                    * CHECK ERRORS
                    **************************************************************************/
                    ?>
                    <?php if (isset($_SESSION['updateError'])) { ?>
                        <div class="alert alert-danger">
                            <?php echo $_SESSION['updateError']; ?>
                        </div>
                    <?php
                        unset($_SESSION['updateError']);
                    } ?>
                    <?php if (isset($_SESSION['updateSuccess'])) { ?>
                        <div class="alert alert-success">
                            <?php echo $_SESSION['updateSuccess']; ?>
                        </div>
                    <?php
                        unset($_SESSION['updateSuccess']);
                    }?>

					<?php
					/**************************************************************************
				 	 * START EDIT FORM
				 	 **************************************************************************/
					?>

					<div class="panel panel-primary">
					 	<div class="panel-heading">Edit User</div>
					 	<div class="panel-body">
						 	<form class="form-horizontal" action="dashboard.php?manage=user&do=update" method="POST">
							 	<input type="hidden" name="id" value="<?php echo $data['id']; ?>">
							 	<div class="form-group">
							 		<label class="col-sm-2 control-label">Username</label>
									<div class="col-sm-9">
						 				<input type="text" name="username" class="form-control" autocomplete="off" value="<?php echo $data['username']; ?>" required="required">
						 			</div>
						 		</div>
						 		<div class="form-group">
						 			<label class="col-sm-2 control-label">Password</label>
						 			<div class="col-sm-9">
							 			<input type="password" name="password" class="form-control" autocomplete="new-password" value="<?php echo $data['password']; ?>" required="required">
							 		</div>
							 	</div>
								<div class="form-group">
						 			<div class="col-sm-offset-2 col-sm-10">
						 				<input type="submit" value="Save" class="btn btn-primary">
						 			</div>
						 		</div>
						 	</form>
						</div> <!-- End panel body -->
					</div> <!-- End panel -->
				</div> <!-- End col-md -->
			<?php } ?> <!-- End Edit User -->
		<?php } ?> <!-- End ID Passed -->
	<?php } // End edit view

	/*********************************************************************************************
	 * START UPDATE VIEW
	 *********************************************************************************************/

	elseif ($do == 'update') {

		/**************************************************************************
	 	 * POST REQUEST
	 	 **************************************************************************/

	 	if ($_SERVER['REQUEST_METHOD'] == 'POST') {

			/**************************************************************************
		 	 * TESTING INPUT DATA AND VALIDATING USERNAME
		 	 **************************************************************************/

	 		$username = TestInput::test($_POST['username']);
	 		$password = TestInput::test($_POST['password']);
	 		if (strlen($username) > 25 || strlen($username) < 4) {
	 			$_SESSION['updateError'] = "ERROR: USERNAME MUST BE AT LEAST 4 CHARACTERS AND AT MOST 25 CHARACTERS LONG";
	 			header('Location: dashboard.php?manage=user&do=edit&id='.$_POST['id']);
	 			exit();
	 		}

			/**************************************************************************
		 	 * VALID USERNAME
		 	 **************************************************************************/

	 		else {
		 		$query = "SELECT * FROM users WHERE id = :id";
				$stmt = $conn->prepare($query);
	 			$stmt->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
	 			$stmt->execute();
				$data = $stmt->fetch(PDO::FETCH_ASSOC);

				/**************************************************************************
			 	 * PASSWORD IS NOT UPDATED
			 	 **************************************************************************/

		 		if ($password == $data['password']) {
		 			$query = "UPDATE users SET username = :username WHERE id = :id";
		 			$stmt = $conn->prepare($query);
		 			$stmt->bindParam(':username', $username, PDO::PARAM_STR);
		 			$stmt->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
		 			$stmt->execute();
		 			if (isset($stmt->errorInfo()[2])) {
		 				$_SESSION['updateError'] = "ERROR: ".$stmt->errorInfo()[2];
		 			} else {
		 				$_SESSION['updateSuccess'] = "User Updated Successfully";
		 			}
		 		}

				/**************************************************************************
			 	 * PASSWORD IS UPDATED
			 	 **************************************************************************/

		 		else {

					/**************************************************************************
					 * VALIDATING PASSWORD
				 	 **************************************************************************/

		 			if (strlen($password) < 8 || strlen($password) > 25) {
		 				$_SESSION['updateError'] = "ERROR: PASSWORD MUST BE AT LEAST 8 CHARACTERS AND AT MOST 25 CHARACTERS LONG";
		 				header('Location: dashboard.php?manage=user&do=edit&id='.$_POST['id']);
		 				exit();
		 			}

					/**************************************************************************
				 	 * PASSWORD IS VALID
				 	 **************************************************************************/

		 			else {
			 			// Hashing the password
			 			$hashedPassword = Hash::create($password);
			 			$query = "UPDATE users SET username = :username, password = :password WHERE id = :id";
			 			$stmt = $conn->prepare($query);
			 			$stmt->bindParam(':username', $username, PDO::PARAM_STR);
			 			$stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
			 			$stmt->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
			 			$stmt->execute();
			 			if (isset($stmt->errorInfo()[2])) {
			 				$_SESSION['updateError'] = "ERROR: ".$stmt->errorInfo()[2];
			 			} else {
			 				$_SESSION['updateSuccess'] = "User Updated Successfully";
			 			}
			 		}
		 		} // End password is updated

	 			header('Location: dashboard.php?manage=user&do=edit&id='.$_POST['id']);
	 			exit();
	 		} // End Update Process
	 	} // End Server Method Is POST

		/**************************************************************************
	 	 * NOT A POST REQUEST
	 	 **************************************************************************/

	 	else {
 			redirect404();
	 	} // End Not POST Request
	} // End update view

	/*********************************************************************************************
	 * START ADD VIEW
	 *********************************************************************************************/

	elseif ($do == "add") { ?>
		<div class="col-md-8 col-md-offset-2">
		 	<?php
			/*****************************************************************************************
			 * ERROR MESSAGES
			 *****************************************************************************************/
			?>

            <?php if (isset($_SESSION['addError'])) { ?>
                <div class="alert alert-danger">
                    <?php echo $_SESSION['addError']; ?>
                </div>
            <?php unset($_SESSION['addError']); } ?>
            <?php if (isset($_SESSION['addSuccess'])) { ?>
                <div class="alert alert-success">
                    <?php echo $_SESSION['addSuccess']; ?>
                </div>
            <?php unset($_SESSION['addSuccess']); } ?>

			<?php
			/**************************************************************************
		 	 * ADD FORM
		 	 **************************************************************************/
		 	?>

			<div class="panel panel-primary">
			 	<div class="panel-heading">Add User</div>
			 	<div class="panel-body">
					<form class="form-horizontal" action="dashboard.php?manage=user&do=store" method="POST">
					 	<div class="form-group">
							<label class="col-sm-2 control-label">Username</label>
				 			<div class="col-sm-9">
				 				<input type="text" name="username" class="form-control" autocomplete="off" required="required" placeholder="Username">
					 		</div>
					 	</div>
						<div class="form-group">
				 			<label class="col-sm-2 control-label">Password</label>
				 			<div class="col-sm-9">
				 				<input type="password" name="password" class="form-control password" autocomplete="new-password" required="required" placeholder="Password">
					 			<i class="show-pass fa fa-eye fa-2x"></i>
					 		</div>
					 	</div>
						<div class="form-group">
				 			<div class="col-sm-offset-2 col-sm-10">
				 				<input type="submit" value="Save" class="btn btn-primary">
				 			</div>
				 		</div>
					</form>
				</div>
			</div> <!-- End Add Form -->
		</div> <!-- End col-md -->
	<?php } // End add view

	/*********************************************************************************************
	 * START STORE VIEW
	 *********************************************************************************************/

	elseif ($do == 'store') {

		/**************************************************************************
	 	 * POST REQUEST
	 	 **************************************************************************/

	 	if ($_SERVER['REQUEST_METHOD'] == 'POST') {

			/**************************************************************************
		 	 * TESTING INPUT DATA AND VALIDATION
		 	 **************************************************************************/

	 		$username = TestInput::test($_POST['username']);
	 		$password = TestInput::test($_POST['password']);
	 		if (strlen($username) > 25 || strlen($username) < 4) {
	 			$_SESSION['addError'] = "ERROR: USERNAME MUST BE AT LEAST 4 CHARACTERS AND AT MOST 25 CHARACTERS LONG";
	 			header('Location: dashboard.php?manage=user&do=add');
	 			exit();
	 		}
			else if (strlen($password) < 8 || strlen($password) > 25) {
 				$_SESSION['addError'] = "ERROR: PASSWORD MUST BE AT LEAST 8 CHARACTERS AND AT MOST 25 CHARACTERS LONG";
 				header('Location: dashboard.php?manage=user&do=add');
 				exit();
 			}

			/**************************************************************************
		 	 * VALID USERNAME AND PASSWORD
		 	 **************************************************************************/

 			else {
		 		$query = "INSERT INTO users (username, password, regDate) VALUES (:username, :password, now())";
				$hashedPassword = Hash::create($password);
	 			$stmt = $conn->prepare($query);
	 			$stmt->bindParam(':username', $username, PDO::PARAM_STR);
		 		$stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
		 		$stmt->execute();
				if (isset($stmt->errorInfo()[2])) {
	 				$_SESSION['addError'] = "ERROR: ".$stmt->errorInfo()[2];
	 			}
		 		else {
		 			$_SESSION['addSuccess'] = "USER HAS BEEN INSERTED SUCCESSFULLY";
				}
				header('Location: dashboard.php?manage=user&do=add');
	 			exit();
	 		} // End inserting user
	 	} // End POST request

		/**************************************************************************
	 	 * NOT A POST REQUEST
	 	 **************************************************************************/

	 	else {
 			redirect404();
 		} // End Not POST Request
 	} // End store view

	/*********************************************************************************************
	 * START DELETE VIEW
	 *********************************************************************************************/

	elseif ($do == 'delete') {

		/**************************************************************************
	 	 * POST REQUEST
	 	 **************************************************************************/

	 	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	 		$query = "DELETE FROM users WHERE id = :id";
	 		$stmt = $conn->prepare($query);
	 		$stmt->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
	 		$stmt->execute();
	 		if (isset($stmt->errorInfo()[2])) {
	 			$_SESSION['deleteError'] = "ERROR: ".$stmt->errorInfo()[2];
	 		}
	 		else {
	 			$_SESSION['deleteSuccess'] = "USER HAS BEEN DELETED SUCCESSFULLY";
	 		}
	 		header('Location: dashboard.php?manage=user');
	 		exit();
	 	}

		/**************************************************************************
	 	 * NOT A POST REQUEST
	 	 **************************************************************************/

	 	else {
 			redirect404();
	 	}
	} // End delete page
