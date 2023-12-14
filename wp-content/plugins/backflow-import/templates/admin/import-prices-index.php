<div class="wrap">
    <h1><?php _e('Backflow Prices Import','backflow') ?></h1>

    <div class="section-form-wrap">
        <h3>Import CSV File with columns:</h3>
        <ul>
            <li><strong>Rows of SKU's products / Columns of User ID's</strong></li>
        </ul>
        <form id="backflow-data-import" action="<?php echo admin_url( 'admin-post.php' ); ?>" method="post" enctype="multipart/form-data">
            <p><label>Upload CSV:<br><input type="file" name="csv"></label></p>
            <input type="hidden" name="action" value="backflow_prices_import">
            <?php wp_nonce_field( 'backflow_prices_import', 'backflow_prices_import_nonce_field' ); ?>
            <button type="submit"><?php _e('Import','backflow') ?></button>
        </form>
    </div>

    <h3 class="success-msg"><?php _e('Done!','backflow') ?></h3>

    <div class="import-data-status">

        <h3><?php _e('Import Prices','backflow') ?></h3>
        <div id="import-data" class="cssProgress">
            <div class="progress2">
            <div class="cssProgress-bar cssProgress-success cssProgress-active" data-percent="0" style="width: 0%;"></div>
            </div>
            <div class="cssProgress-label2 cssProgress-label2-center cssProgress-bar-label">0%</div>
        </div>

    </div>
</div>