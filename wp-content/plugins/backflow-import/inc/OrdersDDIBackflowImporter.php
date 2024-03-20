<?php

include BACKFLOW_IMPORT_PATH . 'inc/BaseBackflowImporter.php';

/**
 * Class OrdersDDIBackflowImporter
 */
class OrdersDDIBackflowImporter extends BaseBackflowImporter {

    /**
     * Import Products.
     */
    public function import() {

        foreach ( $this->csv->data as $item ) {
            $this->item = $item;

            $accountNumber = $this->item['accountNumber'];
            $purchaseOrder = $this->item['purchaseOrder'];
            $shipCompanyName = $this->item['shipCompanyName'];
            $shipAddress1 = $this->item['shipAddress1'];
            $shipAddress2 = $this->item['shipAddress2'];
            $shipAddress3 = $this->item['shipAddress3'];
            $shipCity = $this->item['shipCity'];
            $shipState = $this->item['shipState'];
            $shipPostCode = $this->item['shipPostCode'];
            $shipAttention = $this->item['shipAttention'];
            $specialInstructions = $this->item['specialInstructions'];
            //$orderID = $this->item['orderID'];
            $specialPayInstructions = $this->item['specialPayInstructions'];
            $stockNum = $this->item['stockNum'];
            $qty = $this->item['qty'];
            $price = $this->item['price'];
            $jobName = $this->item['jobName'];
            $emailTo = $this->item['emailTo'];
            $emailCC = $this->item['emailCC'];
            $shipMethod = $this->item['shipMethod'];
            $backOrderMethod = $this->item['backOrderMethod'];
            $billAttention = $this->item['billAttention'];
            $orderType = $this->item['orderType'];
            $orderTypeDescription = $this->item['orderTypeDescription'];



            $endpoint = "http://170.249.164.234:8080/RESTApi.svc/datasync/RESTAPI_SUBMITORDER";

// get logged in user (if applicable)


//$user = get_user_by( '_ddi_accountNumber', $accountNumber );


			global $wpdb;
			$user = $wpdb->get_results("SELECT user_id FROM $wpdb->usermeta WHERE (meta_key = '_ddi_accountNumber' AND meta_value = '". $accountNumber ."')");
			$user_meta =  get_user_meta($user[0]->user_id);

			$user_ddi = get_user_by('id', $user[0]->user_id);

			ddi_login("", $user_ddi);

			$user = $wpdb->get_results("SELECT user_id FROM $wpdb->usermeta WHERE (meta_key = '_ddi_accountNumber' AND meta_value = '". $accountNumber ."')");
			$user_meta =  get_user_meta($user[0]->user_id);

//$user_meta =  get_user_meta(get_current_user_id());

			$ddi_token = $user_meta["_ddi_token"][0];
			$ddi_branch = $user_meta["_ddi_branch"][0];

//$ddi_token = get_field("token", "option");
//$ddi_branch = get_field("ddi_branch", "option");
			$ddi_account_number = get_field("ddi_account_number", "option");
			$ddi_user_id = get_field("ddi_user_id", "option");
			$ddi_user_name = get_field("ddi_username", "option");
			$user_firstname = $order_billing['first_name'];
			$user_lastname = $order_billing['last_name'];
			$ddi_email =  $order_billing['email'];

            $token = bin2hex(random_bytes(64));
            $json_items = array();

            $lineItems = '';
            $lineItemsArr = json_decode($this->item['items'], true);
            if ($lineItemsArr) {
                $orderItems = $lineItemsArr['items'];
                $i = 0;
                foreach ($orderItems as $orderItem) {
                    if ($i) {
                        $lineItems .= ',';
                    }

                    $lineItems .= '{
                         "stockNum":"' . $orderItem['stockNum'] . '",
                         "qty":"' . $orderItem['qty'] . '",
                         "uom":"EA",
                         "price":"' . $orderItem['price'] . '",
                         "mfgNum":"",
                         "description":""
                      }';
                    $i++;
                }
            }


            $json_out = '{ "DDIRequest" : {
        "schema":"SubmitOrder",
   "token":"' . $ddi_token . '",
   "branch":"' . $ddi_branch . '",
   "accountNumber":"' . $accountNumber . '",
    "orderToken":"' . $token . '",
    "user":{
       "userId":"' . $ddi_user_id . '",
       "userName":"' . $ddi_user_name . '",
       "firstName":"' . $user_firstname . '",
       "lastName":"' . $user_lastname . '",
       "email":""
    },
    "purchaseOrder":"' . $purchaseOrder . '",
    "jobName":"' . $jobName . '",
    "specialInstructions":"' . $specialInstructions . '",
    "specialPayInstructions":"' . $specialPayInstructions . '",
    "emailTo":"info@backflowparts.com",
    "emailCC":"",
    "shipMethod":"3",
    "freightCharge": "",
    "backOrderMethod":"Ship backorders as available",
    "billAttention":" ' . $emailTo . '",
    "orderType":"' . $orderType . '",
    "orderTypeDescription":"' . $orderTypeDescription . '",
    "shipAddress":{
       "shipId":"99999999",
       "shipCompanyName":"' . $shipCompanyName . '",
       "shipAddress1":"' . $shipAddress1 . '",
       "shipAddress2":"' . $shipAddress2 . '",
       "shipAddress3":"' . $shipAddress3 . '",
       "shipCity":"' . $shipCity . '",
       "shipState":"' . $shipState . '",
       "shipPostCode":"' . $shipPostCode . '",
       "shipCountry":"United States",
       "shipPhone":"",
       "shipFax":"",
       "shipAttention":"' . $shipAttention . '"
    },
    "lineItems":{
       "itemData":[';

                /*$json_out .= '{
             "stockNum":"' . $stockNum . '",
             "qty":"' . $qty . '",
             "uom":"EA",
             "price":"' . $price . '",
             "mfgNum":"",
             "description":""
          }';*/
            $json_out .= $lineItems;

            $json_out .= ']
    },
    "attachments":{
       "document":[

       ]
    }
 }
}';

            // echo $json_out;
            $user_data = json_encode($user_data);

            // POST ORDER TO DDI
            $ddi_response = curlJSON($endpoint, $json_out, "post");

            $ddi_check = ddi_data_parse($ddi_response);

            $file = 'logs/order-log.txt';
            $current = file_get_contents($file);
            $order_log = json_encode($ddi_response);
            $current .= "$order_log \n";
            file_put_contents($file, $current);

        }

    }


}
