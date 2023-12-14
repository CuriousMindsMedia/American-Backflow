<?php

function mergeProductCSVS($merged,$productCategories){

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

	}
	return $categories;
   // echo "<pre>".var_export($categories, true). "</pre>";

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
						"device_image" => $line[3],
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

				$generate_key = $line[1]."::".$line[2]."::".$line[0];
				if(!$withSKU){
					$generate_key = $line[1]."::".$line[2];
				}

				$products[$generate_key] = array(
						"sku" => $line[0],
						"parent_category" => $line[1],
						"product_category" => $line[2],
						"description" => $line[3],
						"parts_breakdown" => $line[4],
						"repair_instructions" => $line[5],
						"spec_sheet" => $line[6],
						"repair_video" => $line[7],
						"repair_guys" => $line[8],
						"addon_products" => $line[9]
					);
			
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
						"primary_photo" => $line[5],
						"secondary_photo" => $line[6],
						"tertiary_photo" => $line[7],
						"weight" => $line[8],
						"shipping_class" => $line[9],
						"manufacturer" => $line[10]
					);

			}

			$row++;
		}
		return $products;	
}

function insertUpdateNewProducts($products){
	foreach($products as $key => $product){
		$product_title = $product["product_category"] . " - " . $product["sku"];
		$parent_category = $product["parent_category"];
		$parent_or_part = "no";
		if($parent_category == "Complete Assemblies"){
			$parent_or_part = "yes";
		}

		$size_slug = "";
		$model_slug = "";
		$manufacturer_slug = "";

		$post_data = array(
            'post_title' => $product_title,
            'post_content' => $product["description"],
            'post_status' => 'publish',
            'post_type' => "product",
            'post_name' => $product_title,
            'meta_input' => array(
            "product_category_description" => $product["categoryData"]["description"],
            "_sku" => $product["sku"],
            '_price' => $product["productData"]["price"],
            '_regular_price' =>$product["productData"]["price"],
            '_weight' => $product["productData"]["weight"],
            'part_number' => $product["sku"],
            'kit_includes' => $product["productData"]["kit_includes"],
            'upc' => $product["productData"]["upc"],
            'catalog_number' => $product["productData"]["catalog_number"],
            'part_photo' => $product["productData"]["primary_photo"],
            'product_brand' => $product["productData"]["manufacturer"],
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
            "product_model" => $product["categoryData"]["model"],,
            "product_brand_slug" => $manufacturer_slug,
            "shipping_class" => $product["productData"]["shipping_class"],
            "product_order_number" => $loopcount
            )				
		);

	echo "<pre>".var_export($post_data, true). "</pre>";	

	}
}

// load single products
$singleProducts = loadSingleProducts();
// load product to product categories
$productToProductCategories = loadProductToProductCategories();
// merge single products with product  to product categories
$mergeSingleProductWithCategories = mergeSingleProductCategories($singleProducts, $productToProductCategories);

// load product categories
$productCategories = loadProductCategories();

// merge product categories with merged data...
$mergedData = mergeProductCSVS($mergeSingleProductWithCategories, $productCategories);
// echo "<pre>".var_export($mergedData, true). "</pre>";

// begin updating / inserting posts
insertUpdateNewProducts($mergedData);

// echo "<pre>".var_export($sku, true). "</pre>";
// echo "<pre>".var_export($mergeSingleProductWithCategories, true). "</pre>";
// echo "<pre>".var_export($productCategories, true). "</pre>";
