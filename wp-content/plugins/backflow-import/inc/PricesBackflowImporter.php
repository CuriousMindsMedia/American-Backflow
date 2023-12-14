<?php

include BACKFLOW_IMPORT_PATH . 'inc/BaseBackflowImporter.php';

/**
 * Class PricesBackflowImporter
 */
class PricesBackflowImporter extends BaseBackflowImporter {

    /**
     * User ID.
     */
    public $user_id;

    /**
     * Offset.
     */
    public $offset;

    /**
     * Column Index.
     */
    public $column_index;

    /**
     * Columns Count.
     */
    public $columns_count;

    /**
     * Import Prices.
     */
    public function import() {

        foreach ( $this->csv->data as $item ) {

            $this->item = $item;

            if ($this->offset == 0) {

                $this->user_id = $this->item[$this->column_index];
                $this->columns_count = count($this->item);

                $this->offset++;
                continue;
            }

            if (
                isset($this->item[0]) && !empty($this->item[0])
                && isset($this->item[$this->column_index]) && !empty($this->item[$this->column_index])
                && isset($this->user_id) && !empty($this->user_id)
            ) {
                $this->importPrice();
            }
        }

    }

    /**
     * Import price.
     */
    protected function importPrice() {
        global $wpdb;

        $sku = $this->item[0];
        $id = $wpdb->get_var("SELECT id FROM `ddi_pricing` WHERE user_id = '$this->user_id' AND sku = '$sku'");

        if ($id == NULL) {
            $this->insertPrice();
        } else {
            $this->updatePrice($id);
        }


    }

    /**
     * Insert price.
     */
    protected function insertPrice() {
        global $wpdb;

        $wpdb->insert('ddi_pricing', [
            'sku' => $this->item[0],
            'user_id' => $this->user_id,
            'price' => $this->item[$this->column_index],
        ], [ '%s', '%d', '%f' ]);
    }

    /**
     * Update price.
     */
    protected function updatePrice($id) {
        global $wpdb;

        $wpdb->update(
            'ddi_pricing',
            ['price' => $this->item[$this->column_index]],
            ['id' => $id]
        );
    }

}
