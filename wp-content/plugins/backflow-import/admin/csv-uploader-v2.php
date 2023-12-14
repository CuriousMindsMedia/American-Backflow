<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<?php
if($_GET["skey"] === "kGJmqEpRR25f2b"){
$starttime = microtime(true); // Top of page
$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";


require_once("../../../../wp-load.php");

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

function delete_the_product($sku){
	echo "delete $sku";
	global $wpdb;
	$table = "ddi_product_filtering_newest";
	$filter_statement = "SELECT * FROM $table WHERE sku = '$sku' ";
   	$query = $wpdb->prepare($filter_statement);
    $result = $wpdb->get_results($query, ARRAY_A);

    foreach($result as $product){
    	echo "delete post id " . $product['post_id'] . " - " . $sku . "<br>";
    	wp_delete_post( $product['post_id'], false );
    	$wpdb->delete( $table, array( 'id' => $product["id"] ) );
    }

}
function update_child_part_ids(){
	global $wpdb;

	$table = "ddi_product_filtering_newest";
	$filter_statement = "SELECT * FROM $table WHERE is_parent = 'yes' ";
   	$query = $wpdb->prepare($filter_statement);
    $result = $wpdb->get_results($query, ARRAY_A);


    foreach($result as $product){
    	 $child_ids = array();

    	// echo $product["product_category_slug"] . "<br>";
    	$product_category = $product["product_category_slug"];

		$child_filter_statement = "SELECT * FROM $table WHERE is_parent = 'no' AND product_category_slug = '$product_category'";
	   	$child_query = $wpdb->prepare($child_filter_statement);
	    $child_result = $wpdb->get_results($child_query, ARRAY_A);



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

function ddi_db_update($array, $insert_update){


				// echo "<pre>" . var_export($array, true). "</pre>";
global $wpdb;
$table = "ddi_product_filtering_newest";

if($insert_update == "insert"){
	// insert new product
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
		 "part_spec_sheet" => $array["product_data"]["meta_input"]["part_repair_procedures"],
		 "part_repair_procedures" => $array["product_data"]["meta_input"]["part_spec_sheet"]

		 // "has_child_parts" => $parent_part
		 ));
} else if( $insert_update == "update") {
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
		 "part_spec_sheet" => $array["product_data"]["meta_input"]["part_repair_procedures"],
		 "part_repair_procedures" => $array["product_data"]["meta_input"]["part_spec_sheet"]
		 ), 
		// where
		array("post_id" => $array["post_id"])
		);

}



if($wpdb->last_error !== '') :
    $wpdb->print_error();
endif;

}



// wp_defer_term_counting( true );
// wp_defer_comment_counting( true );

		$product_skus = array();
		// upload products...
		// loop through products
		// convert to array...
		// set key as sku
		$row = 0;
		$product_sku_file = fopen('product-data/products.csv', 'r');
		while (($line = fgetcsv($product_sku_file, 0, ",")) !== FALSE) {
			// if($row > 50){
			// 	break;
			// }
			if($row === 0){
				// header
			} else {

				$price = $line[7];
				if($line[7] == ""){
					// for woocommerce...
					$price = 0;
				}
				$product_skus[$line[0]] = array("product_name" => $line[1],
					"permalink" => $line[2],
					"description_suffix" => "",
					"part_number" => $line[3],
					"catalog_number" => $line[4],
					"kit_includes" => $line[5],
					"upc" => $line[6],
					"price" => $price,
					"repair_procedures" => $line[8],
					"spec_sheet" => $line[9],
					"primary_photo" => $line[10],
					"secondary_photo" => $line[11],
					"tertiary_photo" => $line[12],
					"weight" => $line[13],
					"shipping_class" => $line[14],
					"manufacturer" => $line[15],
					"suggested_products" => $line[16],
					"action" => $line[17],
					"product_categories" => array(),
					"parent_categories" => array()
					);
				
			}

		  $row++;
		}

		// echo "<pre>" . var_export($product_skus, true). "</pre>";
		fclose($product_sku_file);


// 		// loop through product to categories PRODUCT ARRAY
	


		$row = 0;
		$product_categories_arr = array();
		$product_categories = fopen('product-data/productCategories.csv', 'r');
		while (($line = fgetcsv($product_categories, 0, ",")) !== FALSE) {
			if($row === 0){
			} else {
				$product_categories_arr[$line[2]."-".$line[0]] = array("name" => $line[0], "parent_category" => $line[2], "description" => $line[3], "device_image" => $line[4], "parts_breakdown" => $line[5], "repair_instructions" => $line[6], "spec_sheet" => $line[7], "repair_video" => $line[8], "repair_guys_articles" => $line[9], "device_header_line" => $line[10], "manufacturer" => $line[11], "size" => $line[12], "model" => $line[13]);	
			}

			$row++;

		}



		// CREATE PRODUCT ARRAY...
		$single_product_array = array();
		// upload product to category
		// loop through product to category data...
		// find array data...
		// merge the data...
		// PUSH TO PRODUCT ARRAY...
		$row = 0;
		$product_single_file = fopen('product-data/producttoCategories.csv', 'r');
		while (($line = fgetcsv($product_single_file, 0, ",")) !== FALSE) {

			if($row === 0){

			} else {
			$sku = $line[0];

			// echo sanitize_title($line[0] . " " . $line[1] . " " . $line[2]) . "<br>";

			$single_product_array[ sanitize_title($line[0] . " " . $line[1] . " " . $line[2]) ] = array($line[3],$line[4]);


				if(array_key_exists($sku, $product_skus)){


				if(!in_array($line[1], $product_skus[$sku]["parent_categories"])){
						array_push($product_skus[$sku]["parent_categories"], $line[1]);
				}
		
				
				if(!in_array($line[2], $product_skus[$sku]["product_categories"])){
					array_push($product_skus[$sku]["product_categories"], array("title" => $line[2], "product_cat_data" => $product_categories_arr[$line[1]."-".$line[2]]));


				} 

}


			}
				$row++;
			

		}


		// echo "<pre>" . var_export($single_product_array, true). "</pre>";

		echo "<h3>Uploading / updating ".count($product_skus)." products</h3>";
		// echo "<pre>" . var_export($product_skus, true). "</pre>";

	

			// loop  through all skus...
		$product_count = 0;
	$loopcount = 0;
	$batch_count = 100;
	$start = (int) $_GET["start"];
	$end = $start + $batch_count;

	if( $end >= count($product_skus)){

// uncomment
	update_child_part_ids();


	echo "<h1>Product update is now complete...</h1><h2>You can now close this browser tab.</h2>";
} else {
	echo "<h1 style='color:red;'>Products are updating... do not close this tab</h1>";
}


		echo "TOTAL: ". count($product_skus);

		// echo "<br> PRODUCT SKUS" . count($product_skus) . " end" . $end . "<br>";
		echo "<h4>Product $start of ".count($product_skus)." </h4>";
		foreach($product_skus as $key => $product_sku){
				// for testing purposes
				if($loopcount >= $start){

				$sku = $key;
				$description_suffix = $product_sku["description_suffix"];
				$part_number = $product_sku["part_number"];
				$catalog_number = $product_sku["catalog_number"];
				$kit_includes = $product_sku["kit_includes"];
				$upc = $product_sku["upc"];
				$suggested_products = $product_sku["suggested_products"];
				$price = $product_sku["price"];
				$part_spec = $product_sku["spec_sheet"];
				$part_repair_procedures = $product_sku["repair_procedures"];

				echo $product_sku["action"] . "<br>";




				if($price == ""){
					$price = "0";
				}
				$weight = $product_sku["weight"];
				$shipping_class = $product_sku["shipping_class"];
				$photo = $product_sku["primary_photo"];

			foreach($product_sku["product_categories"] as $product){
				$product_count++;



				// echo $sku . " " . $product_cat_data["parent_category"] . " " . $product["title"] . " -- " . $sku . " " . $single_product_category[0] . "<br>";

				// echo "<pre>" . var_export($single_product_category, true). "</pre>";

				


				$post_title = $product["title"] . " " . $key;
				$product_cat_data = $product["product_cat_data"];

				$manufacturer = $product_cat_data["manufacturer"];
				$size = $product_cat_data["size"];
				$model = $product_cat_data["model"];
				$parent_category = $product_cat_data["parent_category"];

				$parent_description = $product_cat_data["description"];

				$device_image = $product_cat_data["device_image"];
				$parts_breakdown = $product_cat_data["parts_breakdown"];
				$repair_instructions = $product_cat_data["repair_instructions"];
				$spec_sheet = $product_cat_data["spec_sheet"];
				$repair_guys_articles = $product_cat_data["repair_guys_articles"];
				$device_header_line = $product_cat_data["device_header_line"];
				$repair_video = $product_cat_data["repair_video"];

				$parent_or_part = "no";
				if($parent_category == "Complete Assemblies"){
					$parent_or_part = "yes";
				} 
				
				if (substr($photo, 0, 1) === '.') { 
					$photo = preg_replace('/^./', '_', $photo);

				}

				$size_slug =  sanitize_title($size);
				$model_slug =  sanitize_title($model);
				$manufacturer_slug =  sanitize_title($manufacturer);


								$single_product_category = $single_product_array[ sanitize_title($sku . " " . $product_cat_data["parent_category"] . " " . $product["title"])];
				$description = $single_product_category[0];

				echo sanitize_title($sku . " " . $product_cat_data["parent_category"] . " " . $product["title"]);

 			$post_data = array(
            'post_title' => $post_title,
            'post_content' => $description,
            'post_status' => 'publish',
            'post_type' => "product",
            'post_name' => $post_title,
            'meta_input' => array(
            "product_category_description" => "$parent_description",
            "_sku" => $sku,
            '_price' => $price,
            '_regular_price' =>$price,
            '_weight' => $weight,
            'part_number' => $part_number,
            'kit_includes' => $kit_includes,
            'upc' => $upc,
            'catalog_number' => $catalog_number,
            'description_suffix' => $description_suffix,
            'part_photo' => $photo,
            'product_brand' => $manufacturer,
            'parent_or_part' => $parent_or_part,
            "product_category" => $product["title"],
            "parent_category" => $parent_category,
            "device_photo" => $device_image,
            "repair_video" => $repair_video,
            "repair_procedure" => $repair_instructions,
            "repair_guys_article" => $repair_guys_articles,
            "parts_breakdown" => $parts_breakdown,
            "spec_sheet" => $spec_sheet,
            "device_header_line" => $device_header_line,
            "suggested_products" => $suggested_products,
            "product_size" => $size,
            "product_size_slug" => $size_slug,
            "product_model_slug" => $model_slug,
            "product_model" => $model,
            "product_brand_slug" => $manufacturer_slug,
            "shipping_class" => $shipping_class,
            "product_order_number" => $loopcount,
            "part_spec_sheet" => $part_spec,
            "part_repair_procedures" => $part_repair_procedures

              )
            );

     				echo "<pre>" . var_export($post_data, true). "</pre>";






            // does post exist?
 // uncomment
      
            $post_by_title = get_page_by_title( $post_title, "OBJECT", "product")->ID;
            if($post_by_title){
            	// update post...
            	$post_data["ID"] = $post_by_title;
            	wp_update_post( $post_data );
            	$post_id = $post_by_title;
            	// ddi db update
            	$ddi_push = array("post_id" => $post_id, "product_data" => $post_data);
            	ddi_db_update( $ddi_push, "update" );

            } else {
            	// insert post
            	// echo "title not found...";
            	$post_id = wp_insert_post( $post_data ); 
            	// ddi db insert
            	$ddi_push = array("post_id" => $post_id, "product_data" => $post_data);
            	ddi_db_update( $ddi_push, "insert" );

            }


	        $shipping_term_id = get_term_by( "name", $shipping_class, "product_shipping_class" )->term_id;
	        wp_set_post_terms( $post_id , array($shipping_term_id), "product_shipping_class" );
				


			 }



		

				// echo "<pre>" . var_export($product_sku, true). "</pre>";
	

	}
					if($product_sku["action"] == "Delete Product"){
// uncomment

					delete_the_product($sku);
				} else {

				}

	$loopcount++;



if($loopcount === $end){
			// echo $loopcount;
			echo "<a style='opacity:0;' class='next-product-update' href='?skey=kGJmqEpRR25f2b&start=".$loopcount."'>Next</a>";
			?>
<script>
$(document).ready(function(){
setTimeout(function(){
	console.log("loaded");

	window.location.href = $(".next-product-update").attr("href");
}, 1000)
});
</script>
			<?php
			break;
		}

		}

// echo $product_count;


// // ddi database update... loop through and insert same data as product...





// wp_defer_term_counting( false );
// wp_defer_comment_counting( false );

$endtime = microtime(true); // Bottom of page

// printf("Page loaded in %f seconds", $endtime - $starttime );

} else {
	echo "Not authorized.";
}


?>