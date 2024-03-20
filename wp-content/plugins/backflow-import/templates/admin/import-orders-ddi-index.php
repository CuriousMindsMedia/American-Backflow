<div class="wrap">
    <h1><?php _e('Backflow Orders DDI Import','backflow') ?></h1>

    <div class="section-form-wrap">
        <h3>Import CSV File with columns:</h3>
        <ul>
            <li><strong>accountNumber</strong></li>
            <li><strong>purchaseOrder</strong></li>
            <li><strong>shipCompanyName</strong></li>
            <li><strong>shipAddress1</strong></li>
            <li><strong>shipAddress2</strong></li>
            <li><strong>shipAddress3</strong></li>
            <li><strong>shipCity</strong></li>
            <li><strong>shipState</strong></li>
            <li><strong>shipPostCode</strong></li>
            <li><strong>shipAttention</strong></li>
            <li><strong>specialInstructions</strong></li>
            <li><strong>orderID</strong></li>
            <li><strong>specialPayInstructions</strong></li>
            <li><strong>items</strong></li>
            <li><strong>jobName</strong></li>
            <li><strong>emailTo</strong></li>
            <li><strong>emailCC</strong></li>
            <li><strong>shipMethod</strong></li>
            <li><strong>backOrderMethod</strong></li>
            <li><strong>billAttention</strong></li>
            <li><strong>orderType</strong></li>
            <li><strong>orderTypeDescription</strong></li>
        </ul>
        <form id="backflow-data-import" action="<?php echo admin_url( 'admin-post.php' ); ?>" method="post" enctype="multipart/form-data">
            <p><label>Upload CSV:<br><input type="file" name="csv"></label></p>
            <input type="hidden" name="action" value="backflow_orders_ddi_import">
            <?php wp_nonce_field( 'backflow_orders_ddi_import', 'backflow_orders_ddi_import_nonce_field' ); ?>
            <button type="submit"><?php _e('Import','backflow') ?></button>
        </form>
    </div>

    <h3 class="success-msg"><?php _e('Done!','backflow') ?></h3>

    <div class="import-data-status">

        <h3><?php _e('Import Orders DDI','backflow') ?></h3>
        <div id="import-data" class="cssProgress">
            <div class="progress2">
            <div class="cssProgress-bar cssProgress-success cssProgress-active" data-percent="0" style="width: 0%;"></div>
            </div>
            <div class="cssProgress-label2 cssProgress-label2-center cssProgress-bar-label">0%</div>
        </div>

    </div>
</div>