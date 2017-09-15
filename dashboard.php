<?php

	error_reporting (E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
	ini_set('display_errors', 1);

    session_Start();

	/*********************************************************************************************
	 * SESSION IS SET (AUTHORIZED USER)
	 *********************************************************************************************/

     if (isset($_SESSION['id']) || isset($_COOKIE['remember_username'])) {

     	if (!isset($_SESSION['id'])) {
     		// Check the cookie
     		require_once 'ValidateUser.php';
     		if (!ValidateUser::ValidateLogin($_COOKIE['remember_username'], $_COOKIE['remember_password'])) {
     			header('Location: index.php');
     			exit();
     		}
     	}

        // import database connection
        require_once 'config.php';

        require_once 'directories.php';

        // include TestInput class
        require_once $cls.'TestInput.php';

        // include functions
        require_once $func.'functions.php';

        // include header
        require_once $tpl.'header.php';
        ?>

        <!-- slider stylesheet and script -->
        <link rel="stylesheet" type="text/css" href="<?php echo $css; ?>slider.css" />
        <script src="<?php echo $js; ?>modernizr.custom.63321.js"></script>
        </head>
        <body>
            <div class="container">

		<?php
		/*********************************************************************************************
		 * NAVBAR
		 *********************************************************************************************/
		?>

		<div class="header clearfix">
		<nav>
		  <ul class="nav nav-pills pull-right">
		  	<li><a href="dashboard.php?manage=product">Manage Products</a></li>
		    <li><a href="dashboard.php?manage=category">Manage Categories</a></li>
            <li><a href="dashboard.php?manage=user">Manage Users</a></li>
		    <li role="presentation"><a href="dashboard.php?manage=user&do=edit&id=<?php echo $_SESSION['id']; ?>">Edit Account</a></li>
		    <li role="presentation"><a href="logout.php">Logout</a></li>
		  </ul>
		</nav>
		<h3 class="text-muted">Control Panel</h3>
		</div>

		<?php
		/*********************************************************************************************
		 * SLIDER
		 *********************************************************************************************/
		?>

			<div class="main">
				<div id="mi-slider" class="mi-slider">
					<ul>
						<li><a href="#"><img src="<?php echo $img; ?>1.jpg" alt="img01"><h4>Boots</h4></a></li>
						<li><a href="#"><img src="<?php echo $img; ?>2.jpg" alt="img02"><h4>Oxfords</h4></a></li>
						<li><a href="#"><img src="<?php echo $img; ?>3.jpg" alt="img03"><h4>Loafers</h4></a></li>
						<li><a href="#"><img src="<?php echo $img; ?>4.jpg" alt="img04"><h4>Sneakers</h4></a></li>
					</ul>
					<ul>
						<li><a href="#"><img src="<?php echo $img; ?>5.jpg" alt="img05"><h4>Belts</h4></a></li>
						<li><a href="#"><img src="<?php echo $img; ?>6.jpg" alt="img06"><h4>Hats &amp; Caps</h4></a></li>
						<li><a href="#"><img src="<?php echo $img; ?>7.jpg" alt="img07"><h4>Sunglasses</h4></a></li>
						<li><a href="#"><img src="<?php echo $img; ?>8.jpg" alt="img08"><h4>Scarves</h4></a></li>
					</ul>
					<ul>
						<li><a href="#"><img src="<?php echo $img; ?>9.jpg" alt="img09"><h4>Casual</h4></a></li>
						<li><a href="#"><img src="<?php echo $img; ?>10.jpg" alt="img10"><h4>Luxury</h4></a></li>
						<li><a href="#"><img src="<?php echo $img; ?>11.jpg" alt="img11"><h4>Sport</h4></a></li>
					</ul>
					<ul>
						<li><a href="#"><img src="<?php echo $img; ?>12.jpg" alt="img12"><h4>Carry-Ons</h4></a></li>
						<li><a href="#"><img src="<?php echo $img; ?>13.jpg" alt="img13"><h4>Duffel Bags</h4></a></li>
						<li><a href="#"><img src="<?php echo $img; ?>14.jpg" alt="img14"><h4>Laptop Bags</h4></a></li>
						<li><a href="#"><img src="<?php echo $img; ?>15.jpg" alt="img15"><h4>Briefcases</h4></a></li>
					</ul>
					<nav>
						<a href="#">Shoes</a>
						<a href="#">Accessories</a>
						<a href="#">Watches</a>
						<a href="#">Bags</a>
					</nav>
				</div>
			</div>

		<?php
		/*********************************************************************************************
		 * PRODUCTS VIEW
		 *********************************************************************************************/
		?>

        <?php
        if (isset($_GET['manage']) && $_GET['manage'] == 'product') {
        ?>
            <h2 class="page-header">Products</h2>
            <?php include_once 'products.php'; ?>
        <?php } // End manage=product ?>

		<?php
		/*********************************************************************************************
		 * CATEGORIES VIEW
		 *********************************************************************************************/
		?>

        <?php
        if (isset($_GET['manage']) && $_GET['manage'] == 'category') {
        ?>
            <h2 class="page-header">Categories</h2>
            <?php include_once 'categories.php'; ?>
        <?php } // End manage=category ?>

		<?php
		/*********************************************************************************************
		 * USERS VIEW
		 *********************************************************************************************/
		?>

        <?php
        if (isset($_GET['manage']) && $_GET['manage'] == 'user') {
        ?>
            <h2 class="page-header">Users</h2>
            <?php include_once 'users.php'; ?>
        <?php } // End manage=user ?>

		<?php
		/*********************************************************************************************
		 * FOOTER
		 *********************************************************************************************/
        ?>

         <div class="footer">
         
             </div>
             <!-- Bootstrap and jQuery scripts -->
             <script src="<?php echo $js; ?>jquery-3.2.1.min.js"></script>
             <script src="<?php echo $js; ?>bootstrap.min.js"></script>
             <!-- Datatable scripts -->
             <script src="<?php echo $js; ?>jquery.dataTables.min.js"></script>
             <script src="<?php echo $js; ?>dataTables.bootstrap.min.js"></script>
             <script>
                 $('.table').dataTable();
             </script>
             <!-- Treeview scripts -->
             <script src="<?php echo $js; ?>bootstrap-treeview.min.js"></script>
             <script>
                 var request = new XMLHttpRequest();
                 responseURL = "http://localhost/ControlPanel/fetch.php";
                 request.open('POST', responseURL, true);
                 request.responseType = 'text';
                 request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                 request.send();
                 request.onload = function() {
                     var data = JSON.parse(this.responseText);
                     $('.treeview').treeview({
                         data:data,
                         enableLinks:true
                     });
                     console.log(data);
                 }
             </script>
             <!-- Slider scripts -->
            <script src="<?php echo $js; ?>jquery.catslider.js"></script>
            <script>
                $(function() {
    
                    $( '#mi-slider' ).catslider();
    
                });
            </script>
            <!-- Custom script -->
            <script src="<?php echo $js; ?>custom.js"></script>
             </div>
             </body>
         </html>
    <?php }
	/*********************************************************************************************
	 * SESSION IS NOT SET
	 *********************************************************************************************/
	else {
		header('Location: index.php');
		exit();
	}