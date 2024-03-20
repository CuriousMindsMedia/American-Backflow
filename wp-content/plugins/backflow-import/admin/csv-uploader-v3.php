<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
error_reporting(0);

if($_GET["skey"] === "kGJmqEpRR25f2b"){
require_once("../../../../wp-load.php");
global $wpdb;

function update_child_part_ids(){
	global $wpdb;

	$table = "ddi_product_filtering_newest";
	$filter_statement = "SELECT * FROM $table WHERE is_parent = 'yes' ";
   	// $query = $wpdb->prepare($filter_statement);
    $result = $wpdb->get_results($filter_statement, ARRAY_A);


    foreach($result as $product){
    	 $child_ids = array();

    	// echo $product["product_category_slug"] . "<br>";
    	$product_category = $product["product_category_slug"];

		$child_filter_statement = "SELECT * FROM $table WHERE is_parent = 'no' AND product_category_slug = '$product_category'";
	   	// $child_query = $wpdb->prepare($child_filter_statement);
	    $child_result = $wpdb->get_results($child_filter_statement, ARRAY_A);



	    foreach($child_result as $child_item){

	    	if(!in_array($child_item["post_id"], $child_ids)){
	    		array_push($child_ids, $child_item["post_id"]);
	    	}

	    }
	    // var_dump($child_ids);

 //    // update child ids for parents
	$product_id = $product["id"];
	// echo "product id: $product_id <br>";
	$child_ids = implode(',', $child_ids);
	// echo $child_ids . "<br>";
 	$child_id_update = "UPDATE $table SET has_child_parts = '$child_ids' WHERE id = '$product_id'";
 	$update_child_id = $wpdb->query($child_id_update);


    }

    // echo "updated child product ids";

}

function insert_new_search_product($product){
	global $wpdb;
	$product["meta_input"]["is_this_a_search_product"] = true;
	$product["post_name"] = $product["post_content"];
	// $product["post_title"] = $product["meta_input"]["product_category_description"];
	// $product["post_content"] = $product["meta_input"]["product_category_description"];


	$post_id = wp_insert_post( $product );
	return $post_id;
}
function update_new_search_product($product){
	global $wpdb;
	$product["post_title"] = $product["post_content"];
	if($product["product_nice_name"] != ""){
		$product["post_title"] = $product["product_nice_name"];
	}

	$product["post_name"] = $product["post_content"];
	$product["meta_input"]["is_this_a_search_product"] = true;

	// $product["post_content"] = $product["meta_input"]["product_category_description"];

	$update_post = wp_update_post( $product );
	echo "<pre>".var_export($product, true)."</pre>";
	return $update_post;

}
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
			$sql_statement .= "('".$product["meta_input"]["_sku"]."','".$product["post_id"]."')$delimiter ";

		}

		// $query = $wpdb->prepare($sql_statement);
		$input = $wpdb->query($sql_statement);

	}

	return;
}

function cleanPhotoSrc($src){
	if (substr($src, 0, 1) === '.') {
		$src = preg_replace('/^./', '_', $src);
	}
	return $src;
}
function ddi_db_update($array, $insert_update){

	// var_dump($array);


				// echo "<pre>" . var_export($array, true). "</pre>";
global $wpdb;
$table = "ddi_product_filtering_newest";

if($insert_update == "insert"){
	// insert new product
	if( $array["product_data"]["meta_input"]["part_not_buyable"] == "true"){
		$part_photo = $array["product_data"]["meta_input"]["part_photo"];
		if($part_photo == NULL){
			$part_photo = "";
		}
		$device_photo = $array["product_data"]["meta_input"]["device_photo"];
		if($part_photo == NULL){
			$device_photo = "";
		}

	$wpdb->insert($table , array('id' => "" ,
		 "post_id" => $array["post_id"],
		 "name" => $array["product_data"]["post_title"],
		 "sku" => $array["product_data"]["meta_input"]["_sku"],
		 "part_number" => $array["product_data"]["meta_input"]["part_number"],
		 "parent_category" => $array["product_data"]["meta_input"]["parent_category"],
		 "parent_category_slug" => sanitize_title($array["product_data"]["meta_input"]["parent_category"]),
		 "product_category" => $array["product_data"]["meta_input"]["product_category"],
		 "product_category_slug" => sanitize_title($array["product_data"]["meta_input"]["product_category"]),
		 "brand" => $array["product_data"]["meta_input"]["product_brand_slug"],
		 "size" => $array["product_data"]["meta_input"]["product_size_slug"],
		 "size_name" => $array["product_data"]["meta_input"]["product_size"],
		 "model" => $array["product_data"]["meta_input"]["product_model"],
		 "model_slug" => $array["product_data"]["meta_input"]["product_model_slug"],
		 "parts_breakdown" => $array["product_data"]["meta_input"]["parts_breakdown"],
		 "repair_procedures" =>$array["product_data"]["meta_input"]["repair_procedure"],
		 "repair_video" => $array["product_data"]["meta_input"]["repair_video"],
		 "spec_sheet" => $array["product_data"]["meta_input"]["spec_sheet"],
		 "repair_guys_article" => $array["product_data"]["meta_input"]["repair_guys_article"],
		 "brand_name" => $array["product_data"]["meta_input"]["product_brand"],
		 "part_photo" => $part_photo,
		 "device_photo" => $device_photo,
		 "is_parent" => $array["product_data"]["meta_input"]["parent_or_part"],
		 "related_products" =>$array["product_data"]["meta_input"]["suggested_products"],
		 "not_buyable" => $array["product_data"]["meta_input"]["part_not_buyable"]
		 ));
	} else {
			$wpdb->insert($table , array('id' => "" ,
		 "post_id" => $array["post_id"],
		 "name" => $array["product_data"]["post_title"],
		 "sku" => $array["product_data"]["meta_input"]["_sku"],
		 "part_number" => $array["product_data"]["meta_input"]["part_number"],
		 "parent_category" => $array["product_data"]["meta_input"]["parent_category"],
		 "parent_category_slug" => sanitize_title($array["product_data"]["meta_input"]["parent_category"]),
		 "product_category" => $array["product_data"]["meta_input"]["product_category"],
		 "product_category_slug" => sanitize_title($array["product_data"]["meta_input"]["product_category"]),
		 "brand" => $array["product_data"]["meta_input"]["product_brand_slug"],
		 "size" => $array["product_data"]["meta_input"]["product_size_slug"],
		 "size_name" => $array["product_data"]["meta_input"]["product_size"],
		 "model" => $array["product_data"]["meta_input"]["product_model"],
		 "model_slug" => $array["product_data"]["meta_input"]["product_model_slug"],
		 "parts_breakdown" => $array["product_data"]["meta_input"]["parts_breakdown"],
		 "repair_procedures" =>$array["product_data"]["meta_input"]["repair_procedure"],
		 "repair_video" => $array["product_data"]["meta_input"]["repair_video"],
		 "spec_sheet" => $array["product_data"]["meta_input"]["spec_sheet"],
		 "repair_guys_article" => $array["product_data"]["meta_input"]["repair_guys_article"],
		 "brand_name" => $array["product_data"]["meta_input"]["product_brand"],
		 "part_photo" => $array["product_data"]["meta_input"]["part_photo"],
		 "device_photo" => $array["product_data"]["meta_input"]["device_photo"],
		 "is_parent" => $array["product_data"]["meta_input"]["parent_or_part"],
		 "related_products" =>$array["product_data"]["meta_input"]["suggested_products"],
		 "not_buyable" => $array["product_data"]["meta_input"]["part_not_buyable"]
		 ));
	}

} else if( $insert_update == "update") {
			echo "BRAND: ". $array["product_data"]["meta_input"]["product_brand_slug"];

	// update product...
	$wpdb->update($table,
		// fields to update
		array(
		 "name" => $array["product_data"]["post_title"],
		 "sku" => $array["product_data"]["meta_input"]["_sku"],
		 "part_number" => $array["product_data"]["meta_input"]["part_number"],
		 "parent_category" => $array["product_data"]["meta_input"]["parent_category"],
		 "parent_category_slug" => sanitize_title($array["product_data"]["meta_input"]["parent_category"]),
		 "product_category" => $array["product_data"]["meta_input"]["product_category"],
		 "product_category_slug" => sanitize_title($array["product_data"]["meta_input"]["product_category"]),
		 "brand" => $array["product_data"]["meta_input"]["product_brand_slug"],
		 "size" => $array["product_data"]["meta_input"]["product_size_slug"],
		 "size_name" => $array["product_data"]["meta_input"]["product_size"],
		 "model" => $array["product_data"]["meta_input"]["product_model"],
		 "model_slug" => $array["product_data"]["meta_input"]["product_model_slug"],
		 "parts_breakdown" => $array["product_data"]["meta_input"]["parts_breakdown"],
		 "repair_procedures" =>$array["product_data"]["meta_input"]["repair_procedure"],
		 "repair_video" => $array["product_data"]["meta_input"]["repair_video"],
		 "spec_sheet" => $array["product_data"]["meta_input"]["spec_sheet"],
		 "repair_guys_article" => $array["product_data"]["meta_input"]["repair_guys_article"],
		 "brand_name" => $array["product_data"]["meta_input"]["product_brand"],
		 "part_photo" => $array["product_data"]["meta_input"]["part_photo"],
		 "device_photo" => $array["product_data"]["meta_input"]["device_photo"],
		 "is_parent" => $array["product_data"]["meta_input"]["parent_or_part"],
		 "related_products" =>$array["product_data"]["meta_input"]["suggested_products"],
		 "not_buyable" => $array["product_data"]["meta_input"]["part_not_buyable"]
		 ),
		// where
		array("post_id" => $array["post_id"])
		);



}
	// echo "<pre>".var_export(array(
	// 	"post_id" => $array["post_id"],
	// 	 "name" => $array["product_data"]["post_title"],
	// 	 "sku" => $array["product_data"]["meta_input"]["_sku"],
	// 	 "part_number" => $array["product_data"]["meta_input"]["part_number"],
	// 	 "parent_category" => $array["product_data"]["meta_input"]["parent_category"],
	// 	 "parent_category_slug" => sanitize_title($array["product_data"]["meta_input"]["parent_category"]),
	// 	 "product_category" => $array["product_data"]["meta_input"]["product_category"],
	// 	 "product_category_slug" => sanitize_title($array["product_data"]["meta_input"]["product_category"]),
	// 	 "brand" => $array["product_data"]["meta_input"]["product_brand_slug"],
	// 	 "size" => $array["product_data"]["meta_input"]["product_size_slug"],
	// 	 "size_name" => $array["product_data"]["meta_input"]["product_size"],
	// 	 "model" => $array["product_data"]["meta_input"]["product_model"],
	// 	 "model_slug" => $array["product_data"]["meta_input"]["product_model_slug"],
	// 	 "parts_breakdown" => $array["product_data"]["meta_input"]["parts_breakdown"],
	// 	 "repair_procedures" =>$array["product_data"]["meta_input"]["repair_procedure"],
	// 	 "repair_video" => $array["product_data"]["meta_input"]["repair_video"],
	// 	 "spec_sheet" => $array["product_data"]["meta_input"]["spec_sheet"],
	// 	 "repair_guys_article" => $array["product_data"]["meta_input"]["repair_guys_article"],
	// 	 "brand_name" => $array["product_data"]["meta_input"]["product_brand"],
	// 	 "part_photo" => $array["product_data"]["meta_input"]["part_photo"],
	// 	 "device_photo" => $array["product_data"]["meta_input"]["device_photo"],
	// 	 "is_parent" => $array["product_data"]["meta_input"]["parent_or_part"],
	// 	 "related_products" =>$array["product_data"]["meta_input"]["suggested_products"],
	// 	 "not_buyable" => $array["product_data"]["meta_input"]["part_not_buyable"]
	// 	 ), true)."</pre>";


if($wpdb->last_error !== '') :
    $wpdb->print_error();
endif;

}


function fetchProductIDByName($name){
	global $wpdb;
	$table = "ddi_product_filtering_newest";
	$filter_statement = "SELECT post_id FROM $table WHERE name = '$name' LIMIT 1";
   	// $query = $wpdb->prepare($filter_statement);
    $result = $wpdb->get_results($filter_statement, ARRAY_A);
    if(count($result) > 0){
       return $result[0]["post_id"];
    } else {
    	return false;
    }
}

function mergeProductCSVS($merged,$productCategories,$productToProductCategories){
	$mergedItems = array();

	foreach($productToProductCategories as $key => $productToProductCategory){
		$key_split = explode("::",$key);
		$parent_cat = $key_split[0];
		$product_cat = $key_split[1];
		$sku = $key_split[2];

		$singleProduct = $merged[$sku];
		$productCategory = $productCategories[$parent_cat."::".$product_cat];

		$formatProductData = $productToProductCategory;
		$formatProductData["categoryData"] = $productCategory;
		$formatProductData["productData"] = $singleProduct;


		array_push($mergedItems, $formatProductData);



	}

	// loop through product to product categories

	// foreach($productToProductCategories as $key => $mergeData){
	// 	$key_split = explode("::",$key);
	// 	$parent_cat = $key_split[0];
	// 	$product_cat = $key_split[1];
	// 	$sku = $key_split[2];

	// 	// find product category
	// 	foreach($productCategories as $key1 => $productCategory){
	// 		$key_split_2 = explode("::",$key1);
	// 		$product_cat_2 = $key_split_2[1];
	// 		$parent_cat_2 = $key_split_2[0];

	// 		if($product_cat_2 == $product_cat && $parent_cat == $parent_cat_2){

	// 			echo $sku."::".$productCategory["manufacturer"] . "<br>";

	// 			$associated_product = $merged[$sku."::".$productCategory["manufacturer"]];
	// 			echo "<pre>".var_export($associated_product,true)."</pre>";


	// 			if($associated_product !== null){

	// 				$mergedItems[$key]["categoryData"] = $productCategory;
	// 			$mergedItems[$key]["productData"] = $associated_product;

	// 			}


	// 		}

	// 	}



	return $mergedItems;

	foreach($merged as $key => $product){
		$key_split = explode("::", $key);
		$productCatKey = $key_split[0]."::".$key_split[1];

		$merged[$key]["categoryData"] = $productCategories[$productCatKey];
	}

	return $merged;

}

function  mergeSingleProductCategories($products, $categories){

	foreach($categories as $key => $category){
		$key_split = explode("::",$key);
		$sku = $key_split[2];

		// find matching sku data

		$categories[$key]["productData"] = $products[$sku];

		// echo $key . "<br>";

   // echo "<pre>".var_export($categories[$key], true). "</pre>";

	}
	return $categories;

}

function loadProductCategories(){
		$row = 0;
		$products = array();
		$product_single_file = fopen('product-data/productCategories.csv', 'r');
		while (($line = fgetcsv($product_single_file, 0, ",")) !== FALSE) {
			if($row === 0){
				// column header row
			} else {
				//	0 = cat name, 1 = parent cat, 2 = description, 3 = device image
				// 4 = parts breakdown, 5 = repair instructions, 6 = spec heet, 7 = repair video, 8 = repair guys
				// 9 = device header line, 10 = manufacturer, 11 = size, 12 = model
				$products[$line[1]."::".$line[0]] = array(
						"description" => $line[2],
						"device_image" => cleanPhotoSrc($line[3]),
						"parts_breakdown" => $line[4],
						"repair_instructions" => $line[5],
						"spec_sheet" => $line[6],
						"repair_video" => $line[7],
						"repair_guys" => $line[8],
						"device_header_line" => $line[9],
						"manufacturer" => $line[10],
						"size" => $line[11],
						"model" => $line[12],
					);

			}

		$row++;
	}

	return $products;
}


function loadProductToProductCategories($withSKU = true){
		$row = 0;
		$products = array();
		$product_single_file = fopen('product-data/producttoCategories.csv', 'r');
		while (($line = fgetcsv($product_single_file, 0, ",")) !== FALSE) {
			if($row === 0){
				// column header row
			} else {
				//	0 = sku, 1 = parent category, 2 = product category, 3 = new description
				// 4 = parts breakdown, 5 = repair instructions, 6 = spec sheet , 7 = repair video
				// 8 = repair guys article, 9 addon json
				// echo $line[1]."::".$line[2]."::".$line[0]."<br>";
				$theSku = $line[0];
				$notBuyable = "false";
				if($theSku == "NOT-BUYABLE"){
					$notBuyable = "true";
				}

				$generate_key = $line[1]."::".$line[2]."::".$line[0];
				if(!$withSKU){
					$generate_key = $line[1]."::".$line[2];
				}

				$products[$generate_key] = array(
						"sku" => $line[0],
						"parent_category" => $line[1],
						"product_category" => $line[2],
						"product_title" => $line[3],
						"description" => $line[4],
						"parts_breakdown" => $line[5],
						"repair_instructions" => $line[6],
						"spec_sheet" => $line[7],
						"repair_video" => $line[8],
						"repair_guys" => $line[9],
						"addon_products" => $line[10],
						"not_buyable" => $notBuyable,
						"loopcount" => $row
					);

				// echo "ADDON $line[10] <br>";

			}

		$row++;
	}

	return $products;
}

function loadSingleProducts(){
		$products = array();
		$row = 0;
		$product_sku_file = fopen('product-data/products.csv', 'r');
		while (($line = fgetcsv($product_sku_file, 0, ",")) !== FALSE) {
			if($row === 0){

			} else {
				// 0 = sku, 1 - catalog #, 2 = kit includes, 3 = upc, price = 4, photo = 5, photo2 = 6, photo3 = 7
				// 8 = weight, 9 = shipping class, 10 = shipping manufacturer
				$price = $line[4];
				if($line[4] == ""){
					$price = 0;
				}
				$products[$line[0]] = array(
						"_sku" => $line[0],
						"part_number" => $line[0],
						"catalog_number" => $line[1],
						"kit_includes" => $line[2],
						"upc" => $line[3],
						"price" => $price,
						"primary_photo" => cleanPhotoSrc($line[5]),
						"secondary_photo" => cleanPhotoSrc($line[6]),
						"tertiary_photo" => cleanPhotoSrc($line[7]),
						"weight" => $line[8],
						"shipping_class" => $line[9],
						"manufacturer" => $line[10],
						"loopcount" => $row
					);

			}

			$row++;
		}
		return $products;
}

function insertUpdateNewProducts($products){
	$productPostData = array();
	foreach($products as $key => $product){
		$product_title = $product["product_category"] . " " . $product["sku"];
		$parent_category = $product["parent_category"];
		$parent_or_part = "no";
		if($parent_category == "Complete Assemblies"){
			$parent_or_part = "yes";
		}

		$size_slug = sanitize_title($product["categoryData"]["size"]);
		$model_slug = sanitize_title($product["categoryData"]["model"]);
		$manufacturer_slug = sanitize_title($product["categoryData"]["manufacturer"]);

		$post_data = array(
            'post_title' => $product_title,
            'post_content' => $product["description"],
            'post_status' => 'publish',
            'post_type' => "product",
            'post_name' => $product_title,
            "product_nice_name" => $product["product_title"],
            'meta_input' => array(
            "product_category_description" => $product["categoryData"]["description"],
            "product_nice_name" => $product["product_title"],
            "_sku" => $product["sku"],
            '_price' => $product["productData"]["price"],
            '_regular_price' =>$product["productData"]["price"],
            '_weight' => $product["productData"]["weight"],
            'part_number' => $product["sku"],
            'kit_includes' => $product["productData"]["kit_includes"],
            'upc' => $product["productData"]["upc"],
            'catalog_number' => $product["productData"]["catalog_number"],
            'part_photo' => $product["productData"]["primary_photo"],
            'product_brand' => $product["categoryData"]["manufacturer"],
            'parent_or_part' => $parent_or_part,
            "product_category" => $product["product_category"],
            "parent_category" => $parent_category,
            "device_photo" => $product["categoryData"]["device_image"],
            "repair_video" => $product["categoryData"]["repair_video"],
            "repair_procedure" => $product["categoryData"]["repair_instructions"],
            "repair_guys_article" => $product["categoryData"]["repair_guys"],
            "parts_breakdown" => $product["categoryData"]["parts_breakdown"],
            "spec_sheet" => $product["categoryData"]["spec_sheet"],
            "device_header_line" => $product["categoryData"]["device_header_line"],
            "suggested_products" => $product["addon_products"],
            "product_size" => $product["categoryData"]["size"],
            "product_size_slug" => $size_slug,
            "product_model_slug" => $model_slug,
            "product_model" => $product["categoryData"]["model"],
            "product_brand_slug" => $manufacturer_slug,
            "shipping_class" => $product["productData"]["shipping_class"],
            "product_order_number" => $product["loopcount"],
            "part_spec_sheet" => $product["spec_sheet"],
            "part_repair_procedures" => $product["repair_instructions"],
            "part_parts_breakdown" => $product["parts_breakdown"],
            "part_repair_video" => $product["repair_video"],
            "part_repair_guys_article" => $product["repair_guys"],
            "part_not_buyable" => $product["not_buyable"]
            )
		);

			// echo "post data<pre>".var_export($post_data, true). "</pre>";

		// if($parent_category == "Searchable"){
		// 	echo "post data<pre>".var_export($post_data, true). "</pre>";

		// }


		array_push($productPostData, $post_data);

	}

	return $productPostData;
}


function runInsertUpdater($productPostData){
	$insert_rows = array();
	$start = 0;
	if(isset($_GET["start"])){
		$start = $_GET["start"];
	}

	$limit = 400;
	$end = $start + $limit;
	$counter = 0;

	$productCount = count($productPostData);
	echo "Updating $productCount products...<br>";
	echo "Updating $start to $end<br>";
	// start, limit, end...
	$full_url = get_site_url()."/wp-content/plugins/backflow-import/admin/csv-uploader-v3.php?skey=kGJmqEpRR25f2b";

	foreach($productPostData as $product){

		if($end > $productCount){
			update_child_part_ids();
			echo "All complete... You can close this tab now.";
			return;
		}
					// echo "<pre>".var_export($product, true). "</pre>";


		// if($product["meta_input"]["parent_category"] == "Searchable"){
		// 	// echo "<pre>".var_export($product, true). "</pre>";
		// }
		if($counter >= $start && $counter <= $end){
			echo "weeee";

			echo "<div id='next-search-upload' data-next-start=".($end + 1)." data-next-url='".$full_url."&start=".($end+1)."'></div>";


			if($product["meta_input"]["parent_category"] == "Searchable"){
				echo "IS SEARCH PRODUCT" . "<br>";
				// its a search product
				$product_result = bf_product_db($product["meta_input"]["_sku"]);
				if(count($product_result) > 0){





					// update
					$product["ID"]  = $product_result[0]["post_id"];
					$update_search_product = update_new_search_product($product);
					echo "<span style='color:orange'>".$product['post_title']." Product exists with ID ".$product_result[0]["post_id"]." and has been updated.</span> <br>";
					$shipping_term_id = get_term_by( "name", $product["meta_input"]["shipping_class"], "product_shipping_class" )->term_id;
	        		wp_set_post_terms( $product["ID"] , array($shipping_term_id), "product_shipping_class" );
				} else {
					// insert
						$insert_new_product = insert_new_search_product($product);
						$product["post_id"] = $insert_new_product;
						array_push($insert_rows, $product);
						echo "<span style='color:red'>".$product['post_title']." is a new product and has been added.</span> <br>";

				}



				echo "Its a search product...<br>";
			} else {
				// its a normal product...
				$fetchProductID = fetchProductIDByName($product["post_title"]);
				if($fetchProductID){
					// product exists lets update...
					$product["ID"] = $fetchProductID;

					// update wordpress post data
					// echo "<pre>".var_export($product, true)."</pre>";
					wp_update_post($product);
					// update ddi db data
					$ddi_push = array("post_id" => $fetchProductID, "product_data" => $product);
					ddi_db_update( $ddi_push, "update" );

					echo "<span style='color:orange'>".$product['post_title']." Product exists with ID $fetchProductID and has been updated.</span> <br>";
					$shipping_term_id = get_term_by( "name", $product["meta_input"]["shipping_class"], "product_shipping_class" )->term_id;
	        		wp_set_post_terms( $fetchProductID , array($shipping_term_id), "product_shipping_class" );

				} else {
					// product does not exist lets insert a new one...
					echo "<span style='color:red'>".$product['post_title']." is a new product and has been added.</span> <br>";

										// echo "<pre>".var_export($product, true)."</pre>";



					$post_id = wp_insert_post( $product );
					echo "insert id data";
					var_dump($post_id);
					$ddi_push = array("post_id" => $post_id, "product_data" => $product);
					ddi_db_update( $ddi_push, "insert" );
					if($product["meta_input"]["part_not_buyable"] == "false"){
						$shipping_term_id = get_term_by( "name", $product["meta_input"]["shipping_class"], "product_shipping_class" )->term_id;
		        		wp_set_post_terms( $post_id , array($shipping_term_id), "product_shipping_class" );
					}

				}


			}


		}

		$counter++;


	}


	if(count($insert_rows) > 0){
		// lets insert new skus
		echo "Insert query rows...";
		 bf_search_query_builder($insert_rows, "insert");
	}
}

// load single products
$singleProducts = loadSingleProducts();
// load product to product categories
$productToProductCategories = loadProductToProductCategories();

$productCategories = loadProductCategories();
$mergedData = mergeProductCSVS($singleProducts, $productCategories, $productToProductCategories);
$productPostData = insertUpdateNewProducts($mergedData);
$insertUpdateProducts = runInsertUpdater($productPostData);

// echo "<pre>".var_export($mergedData, true). "</pre>";

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
