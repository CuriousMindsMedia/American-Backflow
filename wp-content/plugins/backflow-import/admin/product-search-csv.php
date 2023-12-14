<?php
add_action( 'admin_menu', 'bf_product_search_csv' );

// backflow pricing page
function bf_product_search_csv() {
  add_menu_page( 'Product Search Products CSV', 'Product Search Products CSV', 'manage_options', 'backflow-import/search-product-csv.php', 'bf_product_search_csv_update', 'dashicons-tickets', 6  );
}

function bf_product_search_csv_update(){
	if ( isset($_POST["submit"]) ) {
   		if ( isset($_FILES["search-csv-upload"])) {
   			$full_url = get_site_url()."/wp-content/plugins/backflow-import/admin/product-search-updater.php?skey=kGJmqEpRR25f2b";
   			echo '<iframe style="float:left;width:100%;height:600px;" src="'.$full_url.'"></iframe>';

		}
	}


 	?>
 	<div class="container">
 	<h2>Product Search CSV</h2>

	<form method="post" enctype="multipart/form-data">
	    Select CSV to upload:
	    <input type="file" name="search-csv-upload" id="search-csv-upload">
	    <input type="submit" value="Upload CSV" name="submit">
	</form>

 	</div>

 	<?php

 }
?>
