<?php
	header("Access-Control-Allow-Origin: *");
	header("Content-Type: application/json; charset=UTF-8");
	session_Start();
	if (isset($_SESSION['id'])) {
		include_once 'config.php';

		$query = "SELECT * FROM categories";
		$stmt = $conn->prepare($query);
		$stmt->execute();

		// Preparing indexed data array
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			extract($row);
			$sub_data['id'] = $id;
			$sub_data['category'] = $category;
			$sub_data['parentId'] = $parentId;
			$sub_data['text'] = $category;
			$sub_data['href'] = 'dashboard.php?manage=category&do=view&categoryId='.$id;
			$data[] = $sub_data;
		}

		// Data array indexed with the id of the category
		foreach($data as $key => &$value) {
			$output[$value["id"]] = &$value;
		}

		// Prepare child relations
		foreach($data as $key => &$value) {
			if($value["parentId"] && isset($output[$value["parentId"]])) {
				$output[$value["parentId"]]["nodes"][] = &$value;
			}
		}

		// Removing single nodes
		foreach($data as $key => &$value) {
			if ($value["parentId"] && isset($output[$value["parentId"]])) {
				unset($data[$key]);
			}
		}

		echo json_encode($data);

		
		// echo '<pre>';
		// print_r($output);
		// echo '</pre>';
		

	} else {
		header('Location: index.php');
		exit();
	}