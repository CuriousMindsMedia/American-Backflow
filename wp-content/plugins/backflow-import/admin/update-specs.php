<?php
require_once("../../../../wp-load.php");


 function from_db($sku){
   global $wpdb;
   $table = "ddi_product_filtering_newest";
   $query = "SELECT * FROM $table WHERE sku='$sku'";
   $result = $wpdb->get_results($query, ARRAY_A);
   return $result;
 }



$product_categories = fopen('update-specs.csv', 'r');
	while (($line = fgetcsv($product_categories, 0, ",")) !== FALSE) {
		$pids = from_db($line[0]);

		foreach($pids as $pid){
			echo $pid["post_id"] . " " . $line[9] . " " . $line[10] ."<br>";

			if($line[9] != ""){
				update_post_meta($pid["post_id"], "repair_procedure", $line[9]);
			}

			if($line[10] != ""){
				update_post_meta($pid["post_id"], "spec_sheet", $line[10]);
			}


		}

	}



?>