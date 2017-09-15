<?php

	/*********************************************************************************************
	 * MANAGE PRODUCTS (ADD | EDIT | DELETE)
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

        <?php
        /*****************************************************************************************
         * MANAGE PRODUCTS PANEL
         *****************************************************************************************/
        ?>

        <div class="panel panel-default">
            <div class="panel-heading">
                Manage Products
            </div>
            <div class="panel-body">
                <?php
                    $query = "SELECT * FROM products";
                    $stmt = $conn->prepare($query);
                    $stmt->execute();
                    if (!$stmt->rowCount()) { 
                ?>
                    <h2>No Products To Show</h2>
                <?php } else { ?>
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
                                extract($row);
                            ?>
                                <!-- printing products data -->
                                <tr>
                                    <td><?php echo $id; ?></td>
                                    <td><?php echo $name; ?></td>
                                    <td>
                                        <img style="width: 25%;"src="storage/productThumbnails/<?php echo $image; ?>" alt="Image Not Found!">
                                    </td>
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
                                        <a href="dashboard.php?manage=product&do=edit&id=<?php echo $id; ?>" class="btn btn-primary btn-md">
                                            <i class="fa fa-edit"></i> Edit
                                        </a>
                                    </td>
                                    <td>
                                        <form action="dashboard.php?manage=product&do=delete&id=<?php echo $id; ?>" method="POST">
                                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                                            <input type="submit" value="Delete" class="btn btn-danger btn-md">
                                        </form>
                                    </td>
                                </tr>
                            <?php } // End while loop ?>
                        </tbody>
                    </table>
                <?php } // End printing products ?>
            </div> <!-- End panel body -->
            <div class="panel-footer">
                <a href="dashboard.php?manage=product&do=add" class="btn btn-primary btn-md">
                    <i class="fa fa-plus"></i> Add New Product
                </a>
            </div>
        </div> <!-- End panel -->
    <?php } // end manage products

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
		 	$query = "SELECT * FROM products WHERE id = :id";
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
					 	<div class="panel-heading">Edit Product</div>
                        <div class="panel-body">
                            <form class="form-horizontal" action="dashboard.php?manage=product&do=update" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="id" value="<?php echo $data['id']; ?>">
                                <input type="hidden" name="image" value="<?php echo $data['image'] ?>">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Product Name</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="name" class="form-control" autocomplete="off" value="<?php echo $data['name']; ?>" required="required">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Product Thumbnail</label>
                                    <div class="col-sm-9">
                                        <input type="file" name="image">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Category</label>
                                    <div class="col-sm-9">
                                        <select name="categoryId" class="form-control">
                                            <?php
                                                $query = "SELECT * FROM categories WHERE id != :id";
                                                $stmt = $conn->prepare($query);
                                                $stmt->bindParam(':id', $data['categoryId']);
                                                $stmt->execute();
                                                $subQuery = "SELECT * FROM categories WHERE id = :id";
                                                $subStmt = $conn->prepare($subQuery);
                                                $subStmt->bindParam(':id', $data['categoryId'], PDO::PARAM_INT);
                                                $subStmt->execute();
                                                $selectedCategory = $subStmt->fetch(PDO::FETCH_ASSOC)['category'];
                                            ?>
                                            <option value="<?php echo $data['categoryId']; ?>"><?php echo $selectedCategory.' | '.$data['categoryId']; ?></option>
                                            <?php
                                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                extract($row);
                                                ?>
                                                <option value="<?php echo $id; ?>"><?php echo $category.' | '.$id; ?></option>
                                                <?php
                                            } ?>
                                        </select>
                                    </div>
                                </div> <!-- End select form-group -->
                                <div class="form-group">
                                    <div class="col-sm-offset-2 col-sm-10">
                                        <input type="submit" value="Save" class="btn btn-primary">
                                    </div>
                                </div>
                            </form>
                        </div> <!-- End panel body -->
					</div> <!-- End panel -->
				</div> <!-- End col-md -->
			<?php } ?> <!-- End Edit Product -->
		<?php } ?> <!-- End ID Passed -->

	<?php } elseif ($do == 'update') {

		/**************************************************************************
	 	 * POST REQUEST
	 	 **************************************************************************/

	 	if ($_SERVER['REQUEST_METHOD'] == 'POST') {

			/**************************************************************************
		 	* TESTING AND VALIDATING PRODUCT NAME
		 	**************************************************************************/

	 		$name = TestInput::test($_POST['name']);
	 		$name = filter_var($name, FILTER_SANITIZE_STRING);
	 		if (strlen($name) > 30 || strlen($name) < 4) {
	 			$_SESSION['updateError'] = "ERROR: PRODUCT NAME MUST BE AT LEAST 4 CHARACTER AND AT MOST 30 CHARACTERS LONG";
	 			header('Location: dashboard.php?manage=product&do=edit&id='.$_POST['id']);
	 			exit();
	 		}

			/**************************************************************************
		 	 * TESTING AND VALIDATING IMAGE
		 	 **************************************************************************/

			/**************************************************************************
		 	 * CHECK IF IMAGE IS UPLOADED -> NO UPLOADED IMAGE
		 	 **************************************************************************/

		 	if (empty($_FILES['image']['name'])) {
		 		$image = $_POST['image'];
		 	}

		 	else {

				/**************************************************************************
				 * IMAGE IS UPLOADED -> DELETING THE EXISTING IMAGE
				 **************************************************************************/

				if (file_exists('storage/productThumbnails/'.$_POST['image'])) {
					unlink('storage/productThumbnails/'.$_POST['image']);
				}

				/**************************************************************************
			 	* IMAGE IS UPLOADED -> VALIDATING THE NEW IMAGE
			 	**************************************************************************/

		 		$imageName = $_FILES['image']['name'];
		 		$imageSize = $_FILES['image']['size'];
		 		$imageTmp = $_FILES['image']['tmp_name'];
		 		$imageType = $_FILES['image']['type'];

		 		$imageAllowedExtensions = array('jpeg', 'jpg', 'png', 'gif');

		 		$imageExtension = strtolower(end(explode('.', $imageName)));

		 		if (!empty($imageName) && !in_array($imageExtension, $imageAllowedExtensions)) {
		 			$_SESSION['updateError'] = "ERROR: EXTENSION IS NOT ALLOWED, PLEASE UPLOAD A VALID IMAGE";
		 			header('Location: dashboard.php?manage=product&do=edit&id='.$_POST['id']);
		 			exit();
		 		}
		 		if (empty($imageName)) {
		 			$_SESSION['updateError'] = "ERROR: YOU MUST UPLOAD AN IMAGE OF THE PRODUCT";
		 			header('Location: dashboard.php?manage=product&do=edit&id='.$_POST['id']);
		 			exit();
		 		}
		 		if (!in_array($imageExtension, $imageAllowedExtensions)) {
		 			$_SESSION['updateError'] = "ERROR: INVALID EXTENSION, ALLOWED EXTENSIONS ARE (jpeg, jpg, png, gif)";
		 			header('Location: dashboard.php?manage=product&do=edit&id='.$_POST['id']);
		 			exit();
		 		}
		 		if ($imageSize > 4194304) {
		 			$_SESSION['updateError'] = "ERROR: IMAGE MUST NOT EXCEED 4 MBs";
		 			header('Location: dashboard.php?manage=product&do=edit&id='.$_POST['id']);
		 			exit();
		 		}

				/**************************************************************************
			 	 * STORING THE IMAGE
			 	 **************************************************************************/

			 	$image = time().'_'.$imageName;
	 			move_uploaded_file($imageTmp, "storage/productThumbnails/".$image);
	 		} // image uploaded

			/**************************************************************************
		 	 * UPDATING THE DATABASE
		 	 **************************************************************************/

		 	$categoryId = $_POST['categoryId'];
		 	$id = $_POST['id'];
 			$query = "UPDATE products SET name = :name, image = :image, categoryId = :categoryId WHERE id = :id";
 			$stmt = $conn->prepare($query);
 			$stmt->bindParam(':name', $name, PDO::PARAM_STR);
 			$stmt->bindParam(':image', $image);
 			$stmt->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);
 			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
 			$stmt->execute();

			/**************************************************************************
		 	* CHECKING ERRORS
		 	**************************************************************************/

 			if (isset($stmt->errorInfo()[2])) {
 				$_SESSION['updateError'] = "ERROR: ".$stmt->errorInfo()[2];
 			} else {
 				$_SESSION['updateSuccess'] = "Product Updated Successfully";
 			}

 			header('Location: dashboard.php?manage=product&do=edit&id='.$_POST['id']);
 			exit();
	 	} // End Server Method Is POST

		/**************************************************************************
	 	 * NOT A POST REQUEST
	 	 **************************************************************************/

	 	else {
 			redirect404();
	 	} // End Not POST Request
	} 

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
			 	<div class="panel-heading">Add Product</div>
			 	<div class="panel-body">
				 	<form class="form-horizontal" action="dashboard.php?manage=product&do=store" method="POST", enctype="multipart/form-data">
					 	<div class="form-group">
					 		<label class="col-sm-2 control-label">Product Name</label>
					 		<div class="col-sm-9">
					 			<input type="text" name="name" class="form-control" autocomplete="off" placeholder="Product Name" required="required">
					 		</div>
					 	</div>
						<div class="form-group">
				 			<label class="col-sm-2 control-label">Product Thumbnail</label>
				 			<div class="col-sm-9">
				 				<input type="file" name="image" required="required">
				 			</div>
				 		</div>
					 	<div class="form-group">
							<label class="col-sm-2 control-label">Category</label>
				 			<div class="col-sm-9">
								<select name="categoryId" class="form-control">
									<?php
										$query = "SELECT * FROM categories";
										$stmt = $conn->prepare($query);
										$stmt->execute();
									?>
									<option value="">SELECT</option>
									<?php
									while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
										extract($row);
									?>
									<option value="<?php echo $id; ?>"><?php echo $category.' | '.$id; ?></option>
									<?php } // end while loop ?>
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
		 	 * TESTING AND VALIDATING PRODUCT NAME
		 	 **************************************************************************/

	 		$name = TestInput::test($_POST['name']);
	 		$name = filter_var($name, FILTER_SANITIZE_STRING);
	 		if (strlen($name) > 30 || strlen($name) < 4) {
	 			$_SESSION['addError'] = "ERROR: PRODUCT NAME MUST BE AT LEAST 4 CHARACTER AND AT MOST 30 CHARACTERS LONG";
	 			header('Location: dashboard.php?manage=product&do=add');
	 			exit();
	 		}

			/**************************************************************************
		 	 * TESTING AND VALIDATING CATEGORY ID
		 	 **************************************************************************/

		 	if (empty($_POST['categoryId'])) {
		 		$_SESSION['addError'] = "ERROR: YOU MUST SELECT A CATEGORY FOR THE PRODUCT";
		 		header('Location: dashboard.php?manage=product&do=add');
		 	}

			/**************************************************************************
		 	 * TESTING AND VALIDATING IMAGE
		 	 **************************************************************************/

	 		$imageName = $_FILES['image']['name'];
	 		$imageSize = $_FILES['image']['size'];
	 		$imageTmp = $_FILES['image']['tmp_name'];
	 		$imageType = $_FILES['image']['type'];

	 		$imageAllowedExtensions = array('jpeg', 'jpg', 'png', 'gif');

	 		$imageExtension = strtolower(end(explode('.', $imageName)));

	 		if (!empty($imageName) && !in_array($imageExtension, $imageAllowedExtensions)) {
	 			$_SESSION['updateError'] = "ERROR: EXTENSION IS NOT ALLOWED, PLEASE UPLOAD A VALID IMAGE";
	 			header('Location: dashboard.php?manage=product&do=edit&id='.$_POST['id']);
	 			exit();
	 		}
	 		elseif (empty($imageName)) {
	 			$_SESSION['updateError'] = "ERROR: YOU MUST UPLOAD AN IMAGE OF THE PRODUCT";
	 			header('Location: dashboard.php?manage=product&do=edit&id='.$_POST['id']);
	 			exit();
	 		}
	 		elseif (!in_array($imageExtension, $imageAllowedExtensions)) {
	 			$_SESSION['updateError'] = "ERROR: INVALID EXTENSION, ALLOWED EXTENSIONS ARE (jpeg, jpg, png, gif)";
	 			header('Location: dashboard.php?manage=product&do=edit&id='.$_POST['id']);
	 			exit();
	 		}
	 		elseif ($imageSize > 4194304) { // maximum 4 MB
	 			$_SESSION['updateError'] = "ERROR: IMAGE MUST NOT EXCEED 4 MBs";
	 			header('Location: dashboard.php?manage=product&do=edit&id='.$_POST['id']);
	 			exit();
	 		}

			/**************************************************************************
		 	 * VALID IMAGE STORE AND ADD IN DATABASE
		 	 **************************************************************************/

	 		else {

				/**************************************************************************
			 	 * STORING THE IMAGE
			 	 **************************************************************************/
			 	$image = time().'_'.$imageName;
	 			move_uploaded_file($imageTmp, "storage/productThumbnails/".$image);

				/**************************************************************************
			 	 * ADD PRODUCT IN DATABASE
			 	 **************************************************************************/

			 	$categoryId = $_POST['categoryId'];
			 	$query = "INSERT INTO products (name, image, categoryId) VALUES (:name, :image, :categoryId)";
			 	$stmt = $conn->prepare($query);
			 	$stmt->bindParam(':name', $name, PDO::PARAM_STR);
			 	$stmt->bindParam(':image', $image);
			 	$stmt->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);
			 	$stmt->execute();

	 		}

			/**************************************************************************
			 * CHECKING ERRORS
			 **************************************************************************/

		 	if (isset($stmt->errorInfo()[2])) {
		 		$_SESSION['addError'] = "ERROR: ".$stmt->errorInfo()[2];
		 	}
		 	else {
		 		$_SESSION['addSuccess'] = "PRODUCT HAS BEEN INSERTED SUCCESSFULLY";
		 	}
	 		header('Location: dashboard.php?manage=product&do=add');
	 		exit();
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
	 		$query = "SELECT * FROM products WHERE id = :id";
	 		$stmt = $conn->prepare($query);
	 		$stmt->bindParam(':id', $_POST['id']);
	 		$stmt->execute();
	 		$stmt = $stmt->fetch(PDO::FETCH_ASSOC);
	 		$image = $stmt['image'];
	 		if (file_exists('storage/productThumbnails/'.$image)) {
	 			unlink('storage/productThumbnails/'.$image);
	 		}
	 		$query = "DELETE FROM products WHERE id = :id";
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
	 			$_SESSION['deleteSuccess'] = "PRODUCT HAS BEEN DELETED SUCCESSFULLY";
	 		}
	 		header('Location: dashboard.php?manage=product');
	 		exit();
	 	} // End POST request

		/**************************************************************************
	 	 * NOT A POST REQUEST
	 	 **************************************************************************/

	 	else {
 			redirect404();
	 	}
	} // End delete view ?>
