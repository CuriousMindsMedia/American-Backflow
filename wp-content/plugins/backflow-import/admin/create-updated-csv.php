<?php
require_once("../../../../wp-load.php");


 function from_db(){
   global $wpdb;
   $table = "ddi_product_filtering_newest";
   $query = "SELECT * FROM $table ORDER BY id";
   $result = $wpdb->get_results($query, ARRAY_A);
   return $result;
 }




$results = from_db();

$products_csv = array();

$product_to_category = array();

$product_categories = array();
$product_categories_list = array();
foreach($results as $result){
	$p = get_post($result["post_id"]);
	$pm = get_post_meta($result["post_id"]);
	$pc = str_replace("”", '"', $p->post_content);
	$pc = str_replace("″", '"', $pc);
	$pc = str_replace("“", '"', $pc);
	$pc = utf8_encode($pc);

// name, slug, cparent category, description, device image, parts breakdown, repair instructions, specsheet, repair video
// repair guys article, device header line, manufactuerer, size, model
	// $product_categories[$result["product_category"]." ".$result["parent_category"]] = "test";
	
	$product_categories[sanitize_title($pm["parent_category"][0] . " " .$pm["product_category"][0] ) ] = array(
			$pm["product_category"][0],
			sanitize_title($pm["product_brand"][0] . " " . $result["sku"]),
			$pm["parent_category"][0],
			$pm["product_category_description"][0],
			$pm["device_photo"][0],
			$pm["parts_breakdown"][0],
			$pm["repair_procedure"][0],
			$pm["spec_sheet"][0],
			$pm["repair_video"][0],
			$pm["repair_guys_article"][0],
			$pm["device_header_line"][0],
			$pm["product_brand"][0],
			$pm["product_size"][0],
			$pm["product_model"][0]
		);

// echo "<pre>".var_export($pm, true)."</pre>";




	
	
	// echo $pc . "<br><br>";
	array_push($product_to_category,
		array($result["sku"], 
			$result["parent_category"], 
			$result["product_category"],
			$pc,
			$result["related_products"])
	);


	// products csv
	// sku, product name, product permalik, description suffix, part #, catalog #, kit includes, upc, price, repair prodcedures,
	// spec sheet, primaryphoto, secondary photo, tertiary photo, weight, shippingclass, manufactururer, suggestedproducts
$repair = "";
$spec = "";
		if($pm["parent_category"][0] != "Complete Assemblies" && $pm["parent_category"][0] != "Repair Parts"){
			$repair = $pm["repair_procedure"][0];
			$spec = $pm["spec_sheet"][0];
		}
	$products_csv[$result["sku"]] = array(
		$result["sku"],
		$pm["product_brand"][0] . " " . $result["sku"],
		sanitize_title($pm["product_brand"][0] . " " . $result["sku"]),
		$pm["description_suffix"][0],
		$pm["part_number"][0],
		$pm["catalog_number"][0],
		$pm["kit_includes"][0],
		$pm["upc"][0],
		$pm["_price"][0],
		"$repair",
		"$spec",
		$pm["part_photo"][0],
		"",
		"",
		$pm["_weight"][0],
		$pm["shipping_class"][0],
		$pm["product_brand"][0],
		$pm["suggested_products"][0]
		);
}

echo "Product Categories count: " . count($product_categories) . "<br>";
// echo "<pre>".var_export($product_categories, true)."</pre>";

echo "Product count: " . count($products_csv) . "<br>";
// echo "<pre>".var_export($products_csv, true)."</pre>";

echo "Product to category count: " . count($product_to_category) . "<br>";
// echo "<pre>".var_export($product_to_category, true)."</pre>";

$fp = fopen('product-to-category-new.csv', 'wb');

foreach ($product_to_category as $fields) {
	$fields = array_map("utf8_encode", $fields);

    fputcsv($fp, $fields);
}

fclose($fp);



$fp = fopen('products-new.csv', 'wb');

foreach ($products_csv as $fields) {
    fputcsv($fp, $fields);
}

fclose($fp);


$fp = fopen('productCategoriesNew.csv', 'wb');

foreach ($product_categories as $fields) {
    fputcsv($fp, $fields);
}

fclose($fp);
?>