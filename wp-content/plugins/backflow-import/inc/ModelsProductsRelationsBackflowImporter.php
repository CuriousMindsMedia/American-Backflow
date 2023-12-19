<?php

include BACKFLOW_IMPORT_PATH . 'inc/BaseBackflowImporter.php';

/**
 * Class ModelsProductsRelationsBackflowImporter
 */
class ModelsProductsRelationsBackflowImporter extends BaseBackflowImporter {

    /**
     * Import Backflow Models/Products Relations.
     */
    public function import() {

        if(function_exists('vi')) {
            vi($this->csv->data);
        };
        foreach ( $this->csv->data as $item ) {
            $this->item = $item;

            if (!empty($this->getSKU())) {
                // Set meta data.
                $this->setAddOnJSONScript();

                // Import action.
                $this->importModelsProductsRelations();
            }

            // reset meta data
            $this->metaInput = [];
        }

    }

    /**
     * Import product.
     */
    protected function importModelsProductsRelations() {
        $id = $this->getProductBySku($this->getSKU());
        
        if(function_exists('vi')) {
            vi($id);
        };
        if ($id == $this->getProductBySku($this->getSKU())) {

            $this->setSizeAttribute($id);
            $this->setBrandAttribute($id);

            $this->setProductCategory($id);

            if (!empty($this->getModel())) {
                $this->setModelRelation($id);
            }

            $post_id = wp_update_post([
                'ID' => $id,
                'meta_input' => $this->metaInput,
            ]);

            $this->setCustomProductOrder();
        }
    }

    protected function setModelRelation($product_id) {
        $model_id = $this->getProductBySku($this->getModel());
        if ($model_id == $this->getProductBySku($this->getModel())) {
            $this->setProductCategory($model_id);

            $model_parts_products = get_field('model_parts_products', $model_id, false);

            if(!is_array($model_parts_products))
                $model_parts_products = [];

            array_push($model_parts_products, $product_id);

            $model_parts_products = array_unique($model_parts_products);
            return update_field('field_607aa9a3ece2b', $model_parts_products, $model_id);

        }

        return false;
    }

    protected function setCustomProductOrder() {
        global $wpdb;

        if(function_exists('vi')) {
            vi($this);
        };
        $table = 'custom_product_order';
        $sku = $this->getSKU() ?: null;
        $category = $this->getProductCategory() ?: null;
        $manufacturer = $this->getBrandAttribute() ?: null;
        $model = $this->getModel() ?: null;
        $size = $this->getSizeAttribute() ?: null;

        if ($sku || $category || $manufacturer || $model || $size) {
            $wpdb->insert($table, [
                'sku' => $sku,
                'category' => $category,
                'manufacturer' => $manufacturer,
                'model' => $model,
                'size' => $size,
            ], [ '%s', '%s', '%s', '%s', '%s' ]);
        }
    }

    public function clearCustomProductsOrder() {
        global $wpdb;
        $delete = $wpdb->query("TRUNCATE TABLE `custom_product_order`");
    }

}
