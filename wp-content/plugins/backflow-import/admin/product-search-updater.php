<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<?php
if($_GET["skey"] === "kGJmqEpRR25f2b"){
require_once("../../../../wp-load.php");
global $wpdb;


function bf_search_query_builder($products, $type){
   				// type == insert, update
   				// products == array of products
   				global $wpdb;
   				$table = "ddi_product_search";

   				if($type == "insert"){
   					$sql_statement = "INSERT INTO $table (sku,post_id)";
   					$sql_statement .= " VALUES ";
   					$product_counter = 0;

   					foreach($products as $product){
   						$product_counter++;
						$delimiter = ",";
   						if($product_counter >= count($products)){
   							$delimiter = ";";
   						}
   						$sql_statement .= "('".$product["sku"]."','".$product["post_id"]."')$delimiter ";

   					}

   					echo $sql_statement;
   					// $query = $wpdb->prepare($sql_statement);
   					$input = $wpdb->query($sql_statement);

   					var_dump($input);


   				} else if($type == "update"){

   				}

   				return;
   			}

   			function update_new_search_product($product){

	 			$post_data = array(
	 			'ID' => $product["post_id"],
	            'post_title' => $product["title"],
	            'post_content' => $product["description"],
	            'post_status' => 'publish',
	            'post_type' => "product",
	            'post_name' => $product["title"],
		            'meta_input' => array(
		            "_sku" => $product["sku"],
		            '_price' => $product["price"],
		            '_regular_price' => $product["price"],
		            '_weight' => $product["weight"],
		            'part_number' => $product["part_number"],
		            'kit_includes' => $product["kit_includes"],
		            'upc' => $product["upc"],
		            'catalog_number' => $product["catalog_number"],
		            'part_photo' => $product["photo"],
		            "repair_video" => $product["repair_video"],
		            "repair_procedure" => $product["repair_instructions"],
		            "repair_guys_article" => $product["repair_guys"],
		            "parts_breakdown" => $product["parts_breakdown"],
		            "spec_sheet" => $product["spec_sheet"],
		            "suggested_products" => $product["suggested_products"],
		            "shipping_class" => $product["shipping_class"],
		            "is_this_a_search_product" => true
		             )
	            );

	            $update_post = wp_update_post( $post_data );

	            return $update_post;

   			}

   			function insert_new_search_product($product){

   				// title, description, sku, price, weight, part num, kit includes, upc, cat #, part_photo, repair video,
   				// repair procedure, repair guys, parts breakdown, spec sheet, suggested products, shipping class

 			$post_data = array(
            'post_title' => $product["title"],
            'post_content' => $product["description"],
            'post_status' => 'publish',
            'post_type' => "product",
            'post_name' => $product["title"],
	            'meta_input' => array(
	            "_sku" => $product["sku"],
	            '_price' => $product["price"],
	            '_regular_price' => $product["price"],
	            '_weight' => $product["weight"],
	            'part_number' => $product["part_number"],
	            'kit_includes' => $product["kit_includes"],
	            'upc' => $product["upc"],
	            'catalog_number' => $product["catalog_number"],
	            'part_photo' => $product["photo"],
	            "repair_video" => $product["repair_video"],
	            "repair_procedure" => $product["repair_instructions"],
	            "repair_guys_article" => $product["repair_guys"],
	            "parts_breakdown" => $product["parts_breakdown"],
	            "spec_sheet" => $product["spec_sheet"],
	            "suggested_products" => $product["suggested_products"],
	            "shipping_class" => $product["shipping_class"],
	            "is_this_a_search_product" => true
	             )
            );

            $post_id = wp_insert_post( $post_data );

            return $post_id;

   			}

			$tmpName = "product-data/search-product.csv";
			$searchCSV = array_map('str_getcsv', file($tmpName));

			// var_dump($searchCSV);

			$start = 0;
			if(isset($_GET["start"])){
				$start = $_GET["start"];
			}

			$limit = 100;

			$end = $start + $limit;

			$csv_row_count = 0;
			$update_rows = array();
			$insert_rows = array();

			$total_csv_rows = count($searchCSV);

			$search_ended = 0;

			echo "Start: $start - End: $end - Limit: $limit";
   			$full_url = get_site_url()."/wp-content/plugins/backflow-import/admin/product-search-updater.php?skey=kGJmqEpRR25f2b";

			foreach($searchCSV as $search_item){
				if($end > $total_csv_rows){
					echo "All complete... You can close this tab now.";
					break;
				}
				if($csv_row_count !== 0){

					// start end limit check...
					if($csv_row_count >= $start && $csv_row_count <= ($end + $limit) ){
						echo $csv_row_count . "<br>";



					$product_data = array(
							"title" => $search_item[9],
							"description" => $search_item[10],
							"sku" => $search_item[0],
							"price" => $search_item[5],
							"weight" => $search_item[7],
							"part_number" => $search_item[1],
							"kit_lincludes" => $search_item[3],
							"upc" => $search_item[4],
							"catalog_number" => $search_item[2],
							"photo" => $search_item[6],
							"repair_video" => $search_item[14],
							"repair_instructions" => $search_item[12],
							"repair_guys" => $search_item[15],
							"parts_breakdown" => $search_item[11],
							"spec_sheet" => $search_item[13],
							"suggested_products" => $search_item[16],
							"shipping_class" => $search_item[8]
						);


					// echo "<pre>".var_export($product_data,true)."</pre>";

					// does the sku already exist?
					$product_result = bf_product_db($product_data["sku"]);
					if(count($product_result) > 0){
						// update
						$product_data["post_id"] = $product_result[0]["post_id"];
						$update_search_product = update_new_search_product($product_data);

						// var_dump($update_search_product);

					} else {
						// insert
						$insert_new_product = insert_new_search_product($product_data);
						$product_data["post_id"] = $insert_new_product;
						array_push($insert_rows, $product_data);
					}


					} else {
						if($search_ended == 0){
							echo "<div id='next-search-upload' data-next-start=".($end + 1)." data-next-url='".$full_url = get_site_url()."/wp-content/plugins/backflow-import/admin/product-search-updater.php?skey=kGJmqEpRR25f2b&start=".($end+1)."'></div>";
							$search_ended = 1;
						}
					}

					// end is it within start end limit parameters

				}



				$csv_row_count++;
			}
			echo "update rows";

			if(count($insert_rows) > 0){
				// lets insert new skus
				 bf_search_query_builder($insert_rows, "insert");
			}
?>
<script>
$("document").ready(function(){
	if($("#next-search-upload").length > 0){
		setTimeout(function(){
			window.location.href = $("#next-search-upload").attr("data-next-url");

		}, 2000);
	}
});
</script>
<?php
} else {
	echo "Not authorized.";
}
?>
