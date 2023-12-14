<?php

include BACKFLOW_IMPORT_PATH . 'inc/BaseBackflowImporter.php';

/**
 * Class ModelsBackflowImporter
 */
class ModelsBackflowImporter extends BaseBackflowImporter {

    /**
     * @var string
     */
    public $currentModelSKU;

    /**
     * Import Models.
     */
    public function import() {

        foreach ( $this->csv->data as $item ) {
            $this->item = $item;

            if (!empty($this->getSKU()) && !empty($this->getTitle())) {
                // Set meta data.
                $this->setSKU();

                $this->setSpecSheet();
                $this->setPartsBreakdown();
                $this->setRepairProcedures();
                $this->setRepairVideo();

                // Set attachments
                $this->setFeaturedImage();

                // Import action.
                $this->importModel();
            }

            // reset meta data
            $this->metaInput = [];
        }

    }

    /**
     * Import model.
     */
    protected function importModel() {

        $sku = $this->getSKU();
        $id = $this->getProductBySku($sku);

        if ($id) {
            $this->updateModel($id);
        } else {
            $this->insertModel();
        }
    }

    /**
     * Insert product.
     */
    protected function insertModel() {

        $this->metaInput['is_model'] = 1;

        $post_id = wp_insert_post([
            'post_type' => 'product',
            'post_title' => wp_strip_all_tags($this->getTitle()),
            'post_content' => sanitize_textarea_field($this->getDescription()),
            'post_status' => 'publish',
            'post_author' => get_current_user_id(),
            'post_date' => current_time( 'Y-m-d H:i:s' ),
            'meta_input' => $this->metaInput,
        ]);

        wp_remove_object_terms( $post_id, 'simple', 'product_type' );
        wp_set_object_terms( $post_id, 'variable', 'product_type', true );

        $this->setSizeAttribute($post_id, 1);
        $this->setBrandAttribute($post_id);

        $sizes = explode(',', $this->getSizeAttribute());
        foreach ($sizes as $size) {
            $this->createProductVariation($post_id, [
                'attributes' => [
                    'size'  => $size,
                ],
                'sku'           => '',
                'regular_price' => '',
                'sale_price'    => '',
            ]);
        }
    }

    /**
     * Update product.
     */
    protected function updateModel($id) {

        $this->setSizeAttribute($id, 1);
        $this->setBrandAttribute($id);

        $sizes = explode(',', $this->getSizeAttribute());
        foreach ($sizes as $size) {
            $this->createProductVariation($id, [
                'attributes' => [
                    'size'  => $size,
                ],
                'sku'           => '',
                'regular_price' => '',
                'sale_price'    => '',
            ]);
        }

        // Set first row data to the model.
        if ($this->currentModelSKU != $this->getSKU()) {

            $this->currentModelSKU = $this->getSKU();
            update_option('backflow_models_import_current_sku', $this->getSKU(), false);

            $post_id = wp_update_post([
                'ID' => $id,
                'post_type' => 'product',
                'post_title' => wp_strip_all_tags($this->getTitle()),
                'post_content' => sanitize_textarea_field($this->getDescription()),
                'post_status' => 'publish',
                'meta_input' => $this->metaInput,
            ]);
        }
    }

}

