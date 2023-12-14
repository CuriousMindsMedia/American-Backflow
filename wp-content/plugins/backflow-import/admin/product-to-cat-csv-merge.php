<?php
// open updated product desc

require_once("../../../../wp-load.php");


$original = fopen('update-descriptions.csv', 'r');

$loopcount = 0;
$batch_count = 500;
$start = (int) $_GET["start"];
$end = $start + $batch_count;

while (($line = fgetcsv($original, 0, ",")) !== FALSE) {

	if($loopcount > $end){
		echo "NEW START $loopcount";
		break;
	}

	if($loopcount === 0){


	} else {
		if($loopcount >= $start){
			// echo $line[3] . "<br>";
			$post_by_title = get_page_by_title( $line[3], "OBJECT", "product")->ID;
			if($post_by_title != ""){
					$post_content = get_post($post_by_title );
					$post_meta = get_post_meta($post_by_title);
					if($post_meta["parent_or_part"] != "yes"){
							$content = $post_content->post_content;
							update_post_meta($post_by_title, "product_category_description", $content);
							// echo $content . " - " . $line[3] . " - " . $post_by_title . "<br>";
					
							 $my_post = array(
						      'ID'           => $post_by_title,
						      'post_content' => "$line[4]",
						  );
						 
						// Update the post into the database
						  wp_update_post( $my_post );					
					}

			}			
		}



		

	}


		$loopcount++;

}



?>