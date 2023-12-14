<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<?php
require_once("../../../../wp-load.php");
// global $wpdb;
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
// error_reporting(0);

$starttime = microtime(true); // Top of page


 function pricing_insert_update_db($sku, $user_id, $price, $insert_update, $update_id = 0){
    global $wpdb;
    $table = "ddi_pricing";
    if($insert_update == "insert"){

        // $wpdb->insert($table , array(
        //   "sku" => $sku,
        //   "user_id" => $user_id,
        //   "price" => $price
        //   ), array("%s", "%s", "%s"));

        return '("'.$sku.'", "'.$user_id.'", "'.$price.'"),';

    } else if($insert_update == "update"){

        // $wpdb->update($table , array(
        //   "price" => $price
        //   ), array("id" => $update_id));
        $wpdb->query("UPDATE $table SET price='$price' WHERE id=$update_id ;");



    }

 }



function insert_update_product_pricing($file){
    global $wpdb;
    $insert_statement = "INSERT INTO ddi_pricing (sku, user_id, price)
    VALUES";
    $update_statement = "";
    // headers as user ids...
    // echo "insert... " . $file;
    $csv = array_map("str_getcsv", file($file,FILE_SKIP_EMPTY_LINES));
    $keys = array_shift($csv);
    // echo "<pre>".var_export($csv,true) . "</pre>";
    $limit = 20;
    $start = 0;
    if($_GET["start"]){
        $start = $_GET["start"];
    }

    $end = $start + $limit;
    echo "end $end";

    foreach ($csv as $i=>$row) {
        $csv[$i] = array_combine($keys, $row);
    }

    if($end < count($csv) ){
       echo "<h2 style='color:red'>Updating / Inserting product pricing... DO NOT CLOSE THIS WINDOW</h2>";
    }

    echo "csv count:" . count($csv) . " $end";

    // echo "<pre>".var_export($csv,true) . "</pre>";
    $counter = 0;

    $total_c = 0;

    // $csv = array_reverse($csv);


    foreach($csv as $sku_target){

        if($counter > $end){
            break;
        }

        if($end - $limit >= count($csv)){
                echo "<h2 style='color:green'>Complete. You may now close this window...</h2>";
                break;
        }

        $sku = $sku_target["SKU"];
            if($counter >= $start){
                // echo "Counter $counter - $start <br>";

        foreach($sku_target as $sku_key => $sku_target_data){

            if($sku_key != "SKU"){
                $total_c++;
                // echo $total_c . "<br>";
                // echo "SKU $sku" . " - " . $sku_key . " - " . $sku_target_data . "<br>";

                // check if item exists in db...
                // echo $sku . " - " . $sku_key . "<br>";
                $price_check = pricing_filtering_db($sku, $sku_key);

                // var_dump($price_check);

                // var_dump($price_check);
                $price_check_count = count($price_check);



                // echo "COUNT: " . count($price_check) . "<br>";

                if($price_check_count > 0){
                    $update_statement .= pricing_insert_update_db($sku, $sku_key, $sku_target_data, "update", $price_check[0]["id"]);
                } else {
                    $insert_statement .= pricing_insert_update_db($sku, $sku_key, $sku_target_data, "insert");
                }

                // if not insert

                // else update


            }


        }
        // echo "<pre>".var_export($sku_target,true) . "</pre>";
        // echo "counter $counter";
        // if($counter > 0){
        //  break;
        // }




    }
    $counter++;
    }
    // echo "TOTAL C: $total_c <BR>";
    if($end < count($csv) ){
echo "<a style='opacity:1;' class='next-product-update' href='?skey=kGJmqEpRR25f2b&start=".($end)."'>Next</a>";
}

// echo $insert_statement;
// echo $insert_statement;
$insert_statement = rtrim($insert_statement, ',');
// echo $update_statement;
$wpdb->query($insert_statement);
// $wpdb->query($update_statement);


echo $update_statement;

}

if($_GET["skey"] === "kGJmqEpRR25f2b"){
require_once("../../../../wp-load.php");
// echo "good";
// echo getcwd()."/product-data/productPricing.csv";

?>
<script>
$(document).ready(function(){
setTimeout(function(){
    console.log("loaded");
    console.log($(".next-product-update").attr("href"));
    if($(".next-product-update").attr("href") != ""  && typeof $(".next-product-update").attr("href") != "undefined"){
        window.location.href = $(".next-product-update").attr("href");
    }

}, 3000)
});
</script>
<?php

insert_update_product_pricing(getcwd()."/product-data/productPricing.csv");

$endtime = microtime(true); // Bottom of page

// printf("Page loaded in %f seconds", $endtime - $starttime );

} else {
    echo "Not authorized";
}



function pricing_filtering_db($sku, $user_id){
	global $wpdb;
	$table = "ddi_pricing";
	$where = "sku='$sku' AND user_id='$user_id'";
	$statement = "SELECT id,price FROM $table WHERE $where LIMIT 1";
	// $query = $wpdb->prepare("SELECT * FROM $table WHERE $where LIMIT 1");
	$result = $wpdb->get_results($statement, ARRAY_A);

	return $result;
}
?>
