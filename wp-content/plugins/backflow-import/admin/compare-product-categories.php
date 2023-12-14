<?php
		$row = 0;
		$oldsheet = array();
		$newsheet = array();


		$product_categories = fopen('productCategoriesOld.csv', 'r');
		$product_categories_new = fopen('productCategoriesNew.csv', 'r');
		while (($line = fgetcsv($product_categories, 0, ",")) !== FALSE) {
			if($row == 0){
			} else {
				array_push($oldsheet,$line[0] . " " . $line[2]);
			}
			$row++;		
		}

		while (($line = fgetcsv($product_categories_new, 0, ",")) !== FALSE) {
			if($row == 0){
			} else {
				array_push($newsheet,$line[0]);
			}
			$row++;		
		}


		echo "old: " . count($oldsheet) . "<br>";
		echo "new: " . count($newsheet);

		$notin = array();

		foreach($oldsheet as $old){
			$in = 0;
			foreach($newsheet as $new){
				if($old == $new){
					$in = 1;
				}
			}

			if($in == 0){
				array_push($notin, $old);
			}
		}

		echo "<pre>" . var_export($notin, true)."</pre>";
?>
