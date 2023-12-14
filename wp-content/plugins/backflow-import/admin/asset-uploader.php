<?php


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

?>
<h1>Asset Uploader</h1>
<p>You can start uploading by selecting the type of asset you'd like to upload, selecting a file then click upload.</p>

<form enctype="multipart/form-data" method="post" action="">
	<label>Select Asset Type</label>
	<br><br>
	<label><input name="asset_type" type="radio" value="device-photos" /> Device Photos</label> <br /> <br>
	<label><input name="asset_type" type="radio" value="product-photos" /> Product Photos</label> <br />  <br>
	<label><input name="asset_type" type="radio" value="parts-breakdown" /> Parts Breakdown</label> <br />  <br>
	<label><input name="asset_type" type="radio" value="repair-procedures" /> Repair Procedures</label> <br />  <br>
	<label><input name="asset_type" type="radio" value="spec-sheets" /> Spec Sheets</label> <br />  <br>
	<label><input name="asset_type" type="radio" value="repair-guys-articles" /> Repair Guys Articles</label> <br />  <br><br>

	<label>Files to Upload (.zip)</label><br><br>
    <input type="file" name="zip_file" /><br><br>
    <input type="submit" value="Upload Assets" name="submit">
    <input type="hidden" name="upload-asset" value="true">
</form>


<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if (isset($_POST["asset_type"]) && !empty($_POST["asset_type"])) {

	$asset_type = $_POST["asset_type"];
	

	if($_FILES["zip_file"]["name"]) {
	$filename = $_FILES["zip_file"]["name"];
	$source = $_FILES["zip_file"]["tmp_name"];
	$type = $_FILES["zip_file"]["type"];

	echo $filename;
	
	$name = explode(".", $filename);
	$accepted_types = array('application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed');
	foreach($accepted_types as $mime_type) {
		if($mime_type == $type) {
			$okay = true;
			break;
		} 
	}
	
	$continue = strtolower($name[1]) == 'zip' ? true : false;
	if(!$continue) {
		$message = "The file you are trying to upload is not a .zip file. Please try again.";
	}

	$target_path = get_home_path()."$asset_type/".$filename;  // change this to the correct site path
	if(move_uploaded_file($source, $target_path)) {
		$zip = new ZipArchive();
		$x = $zip->open($target_path);
		if ($x === true) {
			$zip->extractTo(get_home_path()."$asset_type/"); // change this to the correct site path
			$zip->close();
	
			unlink($target_path);
		}
		$message = "Your .zip file was uploaded and unpacked.";
	} else {	
		$message = "There was a problem with the upload. Please try again.";
	}
}
	}





}



?>

<?php

?>