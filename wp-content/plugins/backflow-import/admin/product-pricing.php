<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

?>

<?php






if($_GET["upload-csv"] == "true"){
	function uploadFile($file_input_name){
		$target_dir = plugin_dir_path( __FILE__ ) . "product-data/";
		$target_file = $target_dir . basename($file_input_name).".csv";

		$uploadOk = 1;
		$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

		if ($uploadOk == 0) {
		    echo "Sorry, your file was not uploaded.";
		// if everything is ok, try to upload file
		} else {
		    if (move_uploaded_file($_FILES[$file_input_name]["tmp_name"], $target_file)) {
		        echo "The file ". basename( $_FILES[$file_input_name]["name"]). " has been uploaded.";

		        // insert update
		        // echo "$target_file";

		    } else {
		        echo "Sorry, there was an error uploading your file.";
		    }
		}

	}

	$file_inputs = array("productPricing");

		if(isset($_REQUEST['upload-csv']) && $_REQUEST['upload-csv'] == "true"){
			foreach($file_inputs as $file_input){
				uploadFile($file_input);
			}
		}




	?>
<h1 style="color:red">Upload / Updating Products... DO NOT close this window...</h1>
<iframe src="<?php echo get_site_url(); ?>/wp-content/plugins/backflow-import/admin/product-price-updater.php?skey=kGJmqEpRR25f2b" style="width:100%;height:400px">
<?php
} else { ?>
<h1>Upload / Update Product Data</h1>
<h2>Make sure to do a BACKUP before updating / upload new products</h2>
<form method="post" id="csvform" enctype="multipart/form-data" action="<?php echo $actual_link; ?>&upload-csv=true">

<div class="col-sm-4">
<h2>Product Pricing CSV</h2>
<label for="products">ProductPricing.csv Upload</label>
<input type="file" name="productPricing" id="productPricing">
</div>

<div class="row" style="float:left;width:100%;text-align:right">
    <input type="submit" value="Upload Store Data" name="submit">
</div>

</form>
<?php
}
?>


<style>
*{
	box-sizing:border-box;
}
.col-sm-4{
	width:28%;
	margin:1%;
	background-color:#fff;
	padding:24px;
	display:inline-block;
}
#csvform{
	float:left;
	width:100%;
	text-align:center;
}
#csvform h2{
	margin-top:0px;
}
</style>
