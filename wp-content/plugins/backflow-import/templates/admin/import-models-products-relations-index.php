<div class="wrap">
    <h1><?php _e('Backflow Models/Products Relations Import','backflow') ?></h1>

    <div class="section-form-wrap">
        <h3>Import CSV File with columns:</h3>
        <ul>
            <li><strong>SKU</strong></li>
            <li><strong>Category</strong></li>
            <li><strong>Manufacturer</strong></li>
            <li><strong>Model</strong></li>
            <li><strong>Size</strong></li>
            <li><strong>Add on JSON Script</strong></li>
        </ul>
        <form id="backflow-data-import" action="<?php echo admin_url( 'admin-post.php' ); ?>" method="post" enctype="multipart/form-data">
            <p><label>Upload CSV:<br><input type="file" name="csv"></label></p>
            <input type="hidden" name="action" value="backflow_models_products_relations_import">
            <?php wp_nonce_field( 'backflow_models_products_relations_import', 'backflow_models_products_relations_import_nonce_field' ); ?>
            <button type="submit"><?php _e('Import','backflow') ?></button>
        </form>
    </div>

    <h3 class="success-msg"><?php _e('Done!','backflow') ?></h3>

    <div class="import-data-status">

        <h3><?php _e('Import Models/Products Relations','backflow') ?></h3>
        <div id="import-data" class="cssProgress">
            <div class="progress2">
            <div class="cssProgress-bar cssProgress-success cssProgress-active" data-percent="0" style="width: 0%;"></div>
            </div>
            <div class="cssProgress-label2 cssProgress-label2-center cssProgress-bar-label">0%</div>
        </div>

    </div>

    <h3>Run CLI Import</h3>
    <button id="run-cli-import" data-action="<?php echo home_url('/csv-import/run.php'); ?>">Run CLI Import</button>
    <h3 class="success-msg-cli"><?php _e('Import is started. You will receive a message on e-mail about its completion!' ,'backflow') ?></h3>

</div>