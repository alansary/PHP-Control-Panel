<?php

	/*
	 * Title function that echo the page title
	 * echo $pageTitle if $pageTitle isset else default title
	 */
	function getTitle() {
		global $pageTitle;

		if (isset($pageTitle))
			echo $pageTitle;
		else
			echo 'CPanel';
	}

	/*
	 * Redirect to 404 page
	 */
     function redirect404() {
		header("Location: 404.php");
		exit();
	}