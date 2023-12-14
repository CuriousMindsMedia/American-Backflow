<?php

include BACKFLOW_IMPORT_PATH . 'inc/BaseBackflowImporter.php';

/**
 * Class ProductsBackflowImporter
 */
class ProductsBackflowImporter extends BaseBackflowImporter {

    /**
     * Import Products.
     */
    public function import() {

        foreach ( $this->csv->data as $item ) {
            $this->item = $item;

            if (!empty($this->getSKU()) && !empty($this->getTitle())) {
                // Set meta data.
                $this->setPrice();
                $this->setWeight();
                $this->setProductCategoryDescription();
                $this->setPartNumber();
                $this->setKitIncludes();
                $this->setCatalogID();
                $this->setUPC();
                $this->setSKU();
                $this->setDDIID();
                $this->setName();
                $this->setSpecSheet();
                $this->setRepairInstructions();
                $this->setRepairVideo();
                //$this->setAddOnJSONScript();

                // Set attachments
                $this->setFeaturedImage();

                // Import action.
                $this->importProduct();
            }

            // reset meta data
            $this->metaInput = [];
        }

    }

    /**
     * Import product.
     */
    protected function importProduct() {
        if ($id = $this->getProductBySku($this->getSKU())) {
            $this->updateProduct($id);
        } else {
            $this->insertProduct();
        }
    }

    /**
     * Insert product.
     */
    protected function insertProduct() {

        $post_id = wp_insert_post([
            'post_type' => 'product',
            'post_title' => wp_strip_all_tags($this->getTitle()),
            'post_content' => sanitize_textarea_field($this->getDescription()),
            'post_status' => 'publish',
            'post_author' => get_current_user_id(),
            'post_date' => current_time( 'Y-m-d H:i:s' ),
            'meta_input' => $this->metaInput,
        ]);

        $this->setSizeAttribute($post_id);
        $this->setBrandAttribute($post_id);
		$this->setShippingClassTaxonomyTerm($post_id);
        //$this->setModelRelation($post_id);

    }

    /**
     * Update product.
     */
    protected function updateProduct($id) {

        $this->setSizeAttribute($id);
        $this->setBrandAttribute($id);
        $this->setShippingClassTaxonomyTerm($id);
        //$this->setModelRelation($id);

        $post_id = wp_update_post([
            'ID' => $id,
            'post_type' => 'product',
            'post_title' => wp_strip_all_tags($this->getTitle()),
            'post_content' => sanitize_textarea_field($this->getDescription()),
            'post_status' => 'publish',
            //'post_author' => get_current_user_id(),
            //'post_date' => current_time( 'Y-m-d H:i:s' ),
            'meta_input' => $this->metaInput,
        ]);

    }

    protected function setModelRelation($product_id) {
        if ($model_id = $this->getProductBySku($this->getModel())) {
            $model_parts_products = get_field('model_parts_products', $model_id, false);

            if(!is_array($model_parts_products))
                $model_parts_products = [];

            array_push($model_parts_products, $product_id);


            $model_parts_products = array_unique($model_parts_products);
            return update_field('field_607aa9a3ece2b', $model_parts_products, $model_id);
        }

        return false;
    }

	/**
	 * Set shipping class.
	 */
	protected function setShippingClassTaxonomyTerm($product_id) {
		if ($shipping_class = $this->getShippingClass()) {
			$term_id = get_term_by('name', $shipping_class, 'product_shipping_class')->term_id;
			wp_set_object_terms($product_id, $term_id, 'product_shipping_class');
		}
	}

}
