<?php

	/*********************************************************************************************
	 * MANAGE CATEGORIES (ADD | EDIT | DELETE)
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
	 	<?php if (isset($_SESSION['viewError'])) { ?>
	 		<div class="alert alert-danger">
	 			<?php echo $_SESSION['viewError']; ?>
	 		</div>
	 	<?php unset($_SESSION['viewError']); } ?>

		<?php
		/*****************************************************************************************
		 * MANAGE MAIN PANEL
		 *****************************************************************************************/
		?>

		<div class="panel panel-default">
		<div class="panel-heading">
			Categories
		</div>
		<div class="panel-body">
		<div class="col-md-8">

			<?php
			/*****************************************************************************************
			 * MANAGE CATEGORIES PANEL
			 *****************************************************************************************/
			?>

		 	<div class="panel panel-default">
		 		<div class="panel-heading">
		 			Manage Categories
		 		</div>
		 		<div class="panel-body">
		 			<?php
		 			$query = "SELECT * FROM categories ORDER BY id";
		 			$stmt = $conn->prepare($query);
		 			$stmt->execute();
		 			if (!$stmt->rowCount()) { ?>
                        <h2>No Categories To Show</h2>
                    <?php } else { ?>
		 				<table class="table table-stripped table-hover text-center categories-table">
		 					<thead>
			 					<tr>
									<th>ID</th>
		 							<th>Category</th>
		 							<th>Parent Category</th>
			 						<th>Edit</th>
			 						<th>Delete</th>
			 					</tr>
		 					</thead>
		 					<tbody>
				 				<?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
					 					extract($row); ?>
					 				<!-- printing categories data -->
					 				<tr>
					 					<td><?php echo $id; ?></td>
						 				<td>
						 					<a href="dashboard.php?manage=category&do=view&categoryId=<?php echo $id; ?>">
						 						<?php echo $category; ?>
						 					</a>
										</td>
					 					<?php
					 						$subquery = "SELECT * FROM categories WHERE id = :id";
					 						$substmt = $conn->prepare($subquery);
					 						$substmt->bindParam(':id', $parentId);
											$substmt->execute();
				 						?>
					 					<td>
						 					<?php
						 					if ($substmt->rowCount()) {
						 						$subcategory = $substmt->fetch(PDO::FETCH_ASSOC);
												echo $subcategory['category'].' | '.$subcategory['id'];
					 						}
					 						else{
					 							echo ""; 
					 						}
					 						?>
						 				</td>
						 				<td>
                                            <a href="dashboard.php?manage=category&do=edit&id=<?php echo $id; ?>" class="btn btn-primary btn-md">
                                                <i class="fa fa-edit"></i> Edit
                                            </a>
                                        </td>
					 					<td>
					 						<form action="dashboard.php?manage=category&do=delete&id=<?php echo $id; ?>" method="POST">
					 							<input type="hidden" name="id" value="<?php echo $id; ?>">
					 							<input type="submit" value="Delete" class="btn btn-danger btn-md">
						 					</form>
						 				</td>
						 			</tr>
						 		<?php } // End while loop ?>
					 		</tbody>
					 	</table>
			 		<?php } // End printing categories ?>
		 		</div> <!-- End panel body -->
		 		<div class="panel-footer">
		 			<a href="dashboard.php?manage=category&do=add" class="btn btn-primary btn-md">
                        <i class="fa fa-plus"></i> Add New Category
                    </a>
		 		</div>
		 	</div> <!-- End panel -->
		</div> <!-- End panel col-md -->

		<?php
		/*****************************************************************************************
		 * END CATEGORIES PANEL
		 *****************************************************************************************/
		?>

		<?php
		/*****************************************************************************************
		 * MANAGE TREEVIEW
		 *****************************************************************************************/
		?>

		<div class="col-md-4">
			<div class="treeview">

		 	</div> <!-- End treeview -->
		</div> <!-- End tree view col-md -->

	    </div> <!-- End main panel body -->
		</div> <!-- End main panel -->
	<?php } // End manage view

	/*********************************************************************************************
	 * START EDIT VIEW
	 *********************************************************************************************/

	elseif ($do == 'edit') { 
		$id = (isset($_GET['id']) && is_numeric($_GET['id'])) ? intval($_GET['id']) : 0;

		/*********************************************************************************************
		 * ID DOESN'T NUMERIC OR IS NOT SET
		 *********************************************************************************************/

		if (!$id) {
 			redirect404();
	 	}

		/*********************************************************************************************
		 * ID IS NUMERIC AND IS SET
		 *********************************************************************************************/

	 	else {
			$query = "SELECT * FROM categories WHERE id = :id";
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
					<div class="panel-heading">Edit Category</div>
					<div class="panel-body">
						<form class="form-horizontal" action="dashboard.php?manage=category&do=update" method="POST">
							<input type="hidden" name="id" value="<?php echo $data['id']; ?>">
							<div class="form-group">
								<label class="col-sm-2 control-label">Category Name</label>
							 	<div class="col-sm-9">
							 		<input type="text" name="category" class="form-control" autocomplete="off" value="<?php echo $data['category']; ?>" required="required">
							 	</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label">Sub-Category</label>
								<div class="col-sm-9">
									<select name="parentId" class="form-control">
										<?php
										$query = "SELECT * FROM categories WHERE id != :id";
										$stmt = $conn->prepare($query);
										$stmt->bindParam(':id', $id);
										$stmt->execute();
										$subquery = "SELECT * FROM categories WHERE id = :id";
										$substmt = $conn->prepare($subquery);
										$substmt->bindParam(':id', $id);
										$substmt->execute();
										$category = $substmt->fetch(PDO::FETCH_ASSOC);
										$defaultParent = $category['parentId'];
										?>
										<option value="<?php echo $defaultParent?>">Default</option>
										<option value="0">Not A Child</option>
										<?php
										while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
											extract($row);
										?>
											<option value="<?php echo $id; ?>"><?php echo $category.' | '.$id; ?></option>
										<?php } ?>
									</select>
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
		    <?php } ?> <!-- End Edit Category -->
	    <?php } ?> <!-- End ID NUMERIC AND IS SET -->
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
		 	 * TESTING AND VALIDATING CATEGORY NAME
		 	 **************************************************************************/

	 		$category = TestInput::test($_POST['category']);
	 		if (strlen($category) > 50 || strlen($category) < 4) {
	 			$_SESSION['updateError'] = "ERROR: CATEGORY NAME MUST BE AT LEAST 4 CHARACTER AND AT MOST 50 CHARACTERS LONG";
	 			header('Location: dashboard.php?manage=category&do=edit&id='.$_POST['id']);
	 			exit();
	 		}

			/**************************************************************************
		 	 * VALID CATEGORY NAME
		 	 **************************************************************************/

	 		else {

				/**************************************************************************
			 	 * CATEGORY IS A CHILD
			 	 **************************************************************************/

	 			if ($_POST['parentId'] != 0) {
		 			$query = "UPDATE categories SET category = :category, parentId = :parentId WHERE id = :id";
		 			$stmt = $conn->prepare($query);
		 			$stmt->bindParam(':category', $category, PDO::PARAM_STR);
		 			$stmt->bindParam(':parentId', $_POST['parentId'], PDO::PARAM_INT);
		 			$stmt->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
		 			$stmt->execute();
		 		}

				/**************************************************************************
			 	 * CATEGORY IS NOT A CHILD
			 	 **************************************************************************/

		 		else {
		 			$query = "UPDATE categories SET category = :category, parentId = NULL WHERE id = :id";
		 			$stmt = $conn->prepare($query);
		 			$stmt->bindParam(':category', $category, PDO::PARAM_STR);
		 			$stmt->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
		 			$stmt->execute();
		 		}

				/**************************************************************************
			 	 * CHECKING ERRORS
			 	 **************************************************************************/

	 			if (isset($stmt->errorInfo()[2])) {
	 				$_SESSION['updateError'] = "ERROR: ".$stmt->errorInfo()[2];
	 			} else {
	 				$_SESSION['updateSuccess'] = "Category Updated Successfully";
	 			}

	 			header('Location: dashboard.php?manage=category&do=edit&id='.$_POST['id']);
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
	 	<div class="col-md-10 col-md-offset-1">

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
			 	<div class="panel-heading">Add Category</div>
			 	<div class="panel-body">
				 	<form class="form-horizontal" action="dashboard.php?manage=category&do=store" method="POST">
				 		<div class="form-group">
					 		<label class="col-sm-2 control-label">Category Name</label>
					 		<div class="col-sm-9">
					 			<input type="text" name="category" class="form-control" autocomplete="off" placeholder="Category Name" required="required">
					 		</div>
					 	</div>
					 	<div class="form-group">
					 		<label class="col-sm-2 control-label">Sub-Category</label>
					 		<div class="col-sm-9">
								<select name="parentId" class="form-control">
									<?php
									$query = "SELECT * FROM categories";
									$stmt = $conn->prepare($query);
									$stmt->execute();
									?>
									<option value="0">Not A Child</option>
									<?php
									while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
										extract($row);
										?>
										<option value="<?php echo $id; ?>"><?php echo $category.' | '.$id; ?></option>
										<?php } ?>
								</select>
					 		</div>
					 	</div>
					 	<div class="form-group">
					 		<div class="col-sm-offset-2 col-sm-10">
					 			<input type="submit" value="Save" class="btn btn-primary">
					 		</div>
					 	</div>
					</form>
				</div> <!-- End panel body -->
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
		 	 * TESTING AND VALIDATING CATEGORY NAME
		 	 **************************************************************************/

	 		$category = TestInput::test($_POST['category']);
	 		if (strlen($category) > 50 || strlen($category) < 4) {
	 			$_SESSION['addError'] = "ERROR: CATEGORY NAME MUST BE AT LEAST 4 CHARACTER AND AT MOST 50 CHARACTERS LONG";
	 			header('Location: dashboard.php?manage=category&do=add');
	 			exit();
	 		}

			/**************************************************************************
		 	 * VALID CATEGORY NAME
		 	 **************************************************************************/

 			else {

				/**************************************************************************
			 	 * IF IT HAS THE SAME PARENT ID, IT CANNOT HAS THE SAME CATEGORY NAME
			 	 **************************************************************************/

			 	$parentId = intval($_POST['parentId']);

				/**************************************************************************
			 	 * CATEGORY DOESN'T HAVE A PARENT
			 	 **************************************************************************/

			 	if (!$parentId) {
					$query = "SELECT * FROM categories WHERE category = :category AND parentId IS NULL";
					$stmt = $conn->prepare($query);
					$stmt->bindParam(':category', $category, PDO::PARAM_STR);
			 	}

				/**************************************************************************
			 	 * CATEGORY DOES HAVE A PARENT
			 	 **************************************************************************/

			 	else {
					$query = "SELECT * FROM categories WHERE category = :category AND parentId = :parentId";
					$stmt = $conn->prepare($query);
					$stmt->bindParam(':category', $category, PDO::PARAM_STR);
					$stmt->bindParam(':parentId', $parentId, PDO::PARAM_INT);
			 	}
			 	$stmt->execute();

				/**************************************************************************
			 	 * CATEGORY IS REPEATED
			 	 **************************************************************************/

			 	if ($stmt->rowCount()) {
			 		$_SESSION['addError'] = "ERROR: DUPLICATE ENTRY \" YOU CANNOT HAVE TWO CATEGORIES ON THE SAME LEVEL WITH THE SAME CATEGORY NAME\"";
			 		header('Location: dashboard.php?manage=category&do=add');
			 		exit();
			 	}

				/**************************************************************************
			 	 * CATEGORY IS NOT REPEATED
			 	 **************************************************************************/

			 	else {
					/**************************************************************************
			 	  	 * CATEGORY DOESN'T HAVE A PARENT
				 	 **************************************************************************/

	 				if (!$parentId) {
				 		$query = "INSERT INTO categories (category) VALUES (:category)";
				 		$stmt = $conn->prepare($query);
				 		$stmt->bindParam(':category', $category, PDO::PARAM_STR);
				 		$stmt->execute();
			 		}

					/**************************************************************************
				 	 * CATEGORY DOES HAVE A PARENT
				 	 **************************************************************************/

			 		else {
			 			$query = "INSERT INTO categories (category, parentId) VALUES (:category, :parentId)";
				 		$stmt = $conn->prepare($query);
				 		$stmt->bindParam(':category', $category, PDO::PARAM_STR);
				 		$stmt->bindParam(':parentId', $parentId, PDO::PARAM_INT);
				 		$stmt->execute();	
			 		}
		 		}

				/**************************************************************************
			 	 * CHECKING ERRORS
			 	 **************************************************************************/

		 		if (isset($stmt->errorInfo()[2])) {
		 			$_SESSION['addError'] = "ERROR: ".$stmt->errorInfo()[2];
		 		}
		 		else {
		 			$_SESSION['addSuccess'] = "CATEGORY HAS BEEN INSERTED SUCCESSFULLY";
		 		}
	 			header('Location: dashboard.php?manage=category&do=add');
	 			exit();
	 		} // End inserting category
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
	 		$query = "DELETE FROM categories WHERE id = :id";
	 		$stmt = $conn->prepare($query);
	 		$stmt->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
	 		$stmt->execute();

			/**************************************************************************
		 	 * CHECKING ERRORS
		 	 **************************************************************************/

	 		if (isset($stmt->errorInfo()[2])) {
	 			$_SESSION['deleteError'] = "ERROR: ".$stmt->errorInfo()[2];
	 		}
	 		else {
	 			$_SESSION['deleteSuccess'] = "CATEGORY HAS BEEN DELETED SUCCESSFULLY";
	 		}
	 		header('Location: dashboard.php?manage=category');
	 		exit();
	 	} // End POST request

		/**************************************************************************
	 	 * NOT A POST REQUEST
	 	 **************************************************************************/

	 	else {
 			redirect404();
	 	}
	} // End delete view

	/**************************************************************************
 	 * START VIEW PAGE
 	 **************************************************************************/

 	elseif ($do == 'view') {

		/**************************************************************************
	 	 * CATEGORY ID PROVIDED -> VALIDATE
	 	 **************************************************************************/

 		if (isset($_GET['categoryId']) && is_numeric($_GET['categoryId']) && filter_var(TestInput::test($_GET['categoryId']))) {
 			$categoryId = filter_var(TestInput::test($_GET['categoryId']), FILTER_VALIDATE_INT);
 			$query = "SELECT * FROM products WHERE categoryId = :categoryId";
 			$stmt = $conn->prepare($query);
 			$stmt->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);
 			$stmt->execute();

			/**************************************************************************
			 * CATEGORY HAS PRODUCTS
		 	 **************************************************************************/

 			if ($stmt->rowCount()) { ?>
 				<div class="col-md-8">
					<table class="table table-stripped table-hover text-center products-table">
						<thead>
							<tr>
								<th>ID</th>
								<th>Product</th>
								<th>Thumbnail</th>
								<th>Category</th>
								<th>Edit</th>
								<th>Delete</th>
							</tr>
						</thead>
						<tbody>
		 				<?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
							extract($row); ?>
			 				<!-- printing products data -->
			 				<tr>
			 					<td><?php echo $id; ?></td>
			 					<td><?php echo $name; ?></td>
			 					<td><img style="width: 25%;"src="storage/productThumbnails/<?php echo $image; ?>" alt="Image Not Found!"></td>
                                <?php
                                $subquery = "SELECT * FROM categories WHERE id = :id";
                                $substmt = $conn->prepare($subquery);
                                $substmt->bindParam(':id', $categoryId);
                                $substmt->execute();
                                $category = $substmt->fetch(PDO::FETCH_ASSOC);
                                ?>
			 					<td>
			 						<?php echo $category['category'].' | '.$category['id']; ?>
			 					</td>
			 					<td>
			 						<a href="dashboard.php?manage=category&do=edit&id=<?php echo $id; ?>" class="btn btn-primary btn-md">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>
                                </td>
			 					<td>
			 						<form action="dashboard.php?manage=category&do=delete&id=<?php echo $id; ?>" method="POST">
			 							<input type="hidden" name="id" value="<?php echo $id; ?>">
			 							<input type="submit" value="Delete" class="btn btn-danger btn-md">
			 						</form>
			 					</td>
			 				</tr>
			 			<?php } // End while loop ?>
		 			    </tbody>
		 		    </table>
		 	    </div> <!-- End table col-md -->
		 	    <div class="col-md-4">
		 	    	<div class="treeview">

		 	    	</div> <!-- End treeview -->
		 	    </div> <!-- End tree view col-md -->
 	    	<?php } // End category has products

			/**************************************************************************
		 	 * CATEGORY DOESN'T HAVE PRODUCTS
		 	 **************************************************************************/

 			else { ?>
 				<h2>No Products To Show</h2>
 			<?php }

 		} // End category id provided

		/**************************************************************************
	 	 * CATEGORY ID NOT PROVIDED OR INVALID
	 	 **************************************************************************/

	 	else {
	 		$_SESSION['viewError'] = 'ERROR: YOU MUST PROVED A VALID CATEGORY ID';
	 		header('Location: dashboard.php?manage=category');
	 	}
 	} // End view view

	/**************************************************************************
 	 * PAGE NOT FOUND REDIRECT
 	 **************************************************************************/

 	else {
 		redirect404();
 	}
