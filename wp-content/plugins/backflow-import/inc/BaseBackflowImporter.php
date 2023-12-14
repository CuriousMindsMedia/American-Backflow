<?php

/**
 * Class BaseBackflowImporter
 */
class BaseBackflowImporter {

    /**
     * @var \ParseCsv\Csv
     */
    protected $csv;

    /**
     * @var array
     */
    protected $columnAliases = [
        'sku' => 'sku',
        'ddi_id' => 'ddi_id',
        'catalog_id' => 'catalog_id',
        'title' => 'title',
        'name' => 'name',
        'description' => 'description',
        'weight' => 'weight',
        'price' => 'price',
        'shipping_class' => 'shipping_class',
        'product_category_description' => 'product_category_description',
        'part_number' => 'part_number',
        'kit_includes' => 'kit_includes',
        'upc' => 'upc',
        'featured_image' => 'featured_image',
        'parent_or_part' => 'parent_or_part',
        'product_category' => 'product_category',
        'parent_category' => 'parent_category',
        'device_photo' => 'device_photo',
        'repair_video' => 'repair_video',
        'repair_procedures' => 'repair_procedures',
        'repair_instructions' => 'repair_instructions',
        'repair_guys_article' => 'repair_guys_article',
        'parts_breakdown' => 'parts_breakdown',
        'spec_sheet' => 'spec_sheet',
        'device_header_line' => 'device_header_line',
        'size_attr' => 'size',
        'brand_attr' => 'brand',
        'model' => 'model',
        'part_spec_sheet' => 'part_spec_sheet',
        'part_repair_procedures' => 'part_repair_procedures',
        'part_repair_video' => 'part_repair_video',
        'product_nice_name' => 'product_nice_name',
        'add_on_json_script' => 'add_on_json_script',
    ];

    /**
     * @var array
     */
    protected $metaInput = [];

    /**
     * @var array
     */
    protected $item;

    public function __construct(ParseCsv\Csv $csv, $column_aliases = []) {
        $this->csv = $csv;
        $this->columnAliases = array_merge($this->columnAliases, $column_aliases);
    }

    /**
     * Set product price
     */
    protected function setPrice() {
        if ($price = $this->getPrice()) {
            $this->metaInput['_price'] = wc_format_decimal($price);
            $this->metaInput['_regular_price'] = wc_format_decimal($price);
        } else {
            $this->metaInput['_price'] = null;
            $this->metaInput['_regular_price'] = null;
        }
    }

    /**
     * Set weight.
     */
    protected function setWeight() {
        if ($weight = $this->getWeight()) {
            $this->metaInput['_weight'] = floatval($weight);
        } else {
            $this->metaInput['_weight'] = null;
        }
    }

    /**
     * Set shipping class.
     */
    protected function setShippingClass() {
        if ($shipping_class = $this->getShippingClass()) {
            $this->metaInput['shipping_class'] = sanitize_text_field($shipping_class);
        } else {
            $this->metaInput['shipping_class'] = null;
        }
    }

    /**
     * Set product category description.
     */
    protected function setProductCategoryDescription() {
        if ($product_category_description = $this->getProductCategoryDescription()) {
            $this->metaInput['product_category_description'] = sanitize_text_field($product_category_description);
        } else {
            $this->metaInput['product_category_description'] = null;
        }
    }

    /**
     * Set part number.
     */
    protected function setPartNumber() {
        if ($part_number = $this->getPartNumber()) {
            $this->metaInput['part_number'] = sanitize_text_field($part_number);
        } else {
            $this->metaInput['part_number'] = null;
        }
    }

    /**
     * Set kit includes.
     */
    protected function setKitIncludes() {
        if ($kit_includes = $this->getKitIncludes()) {
            $this->metaInput['kit_includes'] = sanitize_text_field($kit_includes);
        } else {
            $this->metaInput['kit_includes'] = null;
        }
    }

    /**
     * Set UPC.
     */
    protected function setUPC() {
        if ($upc = $this->getUPC()) {
            $this->metaInput['upc'] = sanitize_text_field($upc);
        } else {
            $this->metaInput['upc'] = null;
        }
    }

    protected function setCatalogID() {
        if ($catalog_id = $this->getCatalogID()) {
            $this->metaInput['catalog_id'] = sanitize_text_field($catalog_id);
        } else {
            $this->metaInput['catalog_id'] = null;
        }
    }

    /**
     * Set SKU.
     */
    protected function setSKU() {
        if ($sku = $this->getSKU()) {
            $this->metaInput['_sku'] = sanitize_text_field($sku);
        }
    }

    /**
     * Set DDI ID.
     */
    protected function setDDIID() {
        if ($ddi_id = $this->getDDIID()) {
            $this->metaInput['ddi_id'] = sanitize_text_field($ddi_id);
        } else {
            $this->metaInput['ddi_id'] = null;
        }
    }

    /**
     * Set Name.
     */
    protected function setName() {
        if ($name = $this->getName()) {
            $this->metaInput['name'] = sanitize_text_field($name);
        } else {
            $this->metaInput['name'] = null;
        }
    }

    /**
     * Set Name.
     */
    protected function setAddOnJSONScript() {
        if ($add_on_json_script = $this->getAddOnJSONScript()) {
            $this->metaInput['add_on_json_script'] = sanitize_text_field($add_on_json_script);
		} else {
			$this->metaInput['add_on_json_script'] = null;
		}
    }

    /**
     * Set Repair Video.
     */
    protected function setRepairVideo() {
        if ($repair_video = $this->getRepairVideo()) {
            $this->metaInput['repair_video'] = sanitize_text_field($repair_video);
        } else {
            $this->metaInput['repair_video'] = null;
        }
    }

    /**
     * Set Product Category.
     */
    protected function setProductCategory($product_id) {
        if ($product_category = $this->getProductCategory()) {
            $this->setProductTaxonomy($product_id, $product_category, 'product_cat');
        }
    }

    /**
     * Set Size Attribute.
     */
    protected function setSizeAttribute($product_id, $is_variation = 0) {
        if ($size_attr = $this->getSizeAttribute()) {
            $this->addProductAttributes($product_id, explode(',', $size_attr), 'pa_size', $is_variation);
        }
    }

    /**
     * Set Brand Attribute.
     */
    protected function setBrandAttribute($product_id, $is_variation = 0) {
        if ($brand_attr = $this->getBrandAttribute()) {
            $this->addProductAttributes($product_id, $brand_attr, 'pa_brand', $is_variation);
        }
    }

    /**
     * Set Featured Image.
     */
    protected function setFeaturedImage() {

        if ($featured_image = $this->getFeaturedImage()) {
            //$attachment_url = ABSPATH . 'product-photos/' . $featured_image;
            $attachment_url = ABSPATH . 'backflow-files/' . $featured_image;

            if ($attachment_id = $this->setAttachment($attachment_url)) {
                $this->metaInput['_thumbnail_id'] = $attachment_id;
            } else {
                $this->metaInput['_thumbnail_id'] = null;
            }
        } else {
            $this->metaInput['_thumbnail_id'] = null;
        }

    }

    /**
     * Set Spec Sheet.
     */
    protected function setSpecSheet() {

        if ($spec_sheet = $this->getSpecSheet()) {
            //$attachment_url = ABSPATH . 'spec-sheets/' . $spec_sheet;
            $attachment_url = ABSPATH . 'backflow-files/' . $spec_sheet;

            if ($attachment_id = $this->setAttachment($attachment_url)) {
                $this->metaInput['spec_sheet'] = $attachment_id;
                $this->metaInput['_spec_sheet'] = 'field_608ea8c894f66';
            } else {
                $this->metaInput['spec_sheet'] = null;
            }
        } else {
            $this->metaInput['spec_sheet'] = null;
        }

    }

    /**
     * Set Repair Procedures.
     */
    protected function setRepairProcedures() {

        if ($repair_procedures = $this->getRepairProcedures()) {
            //$attachment_url = ABSPATH . 'repair-procedures/' . $repair_procedures;
            $attachment_url = ABSPATH . 'backflow-files/' . $repair_procedures;

            if ($attachment_id = $this->setAttachment($attachment_url)) {
                $this->metaInput['repair_procedures'] = $attachment_id;
                $this->metaInput['_repair_procedures'] = 'field_608ea99394f6d';
            } else {
                $this->metaInput['repair_procedures'] = null;
            }
        } else {
            $this->metaInput['repair_procedures'] = null;
        }

    }

    /**
     * Set Repair Instructions.
     */
    protected function setRepairInstructions() {

        if ($repair_instructions = $this->getRepairInstructions()) {
            //$attachment_url = ABSPATH . 'repair-instructions/' . $repair_procedures;
            $attachment_url = ABSPATH . 'backflow-files/' . $repair_instructions;

            if ($attachment_id = $this->setAttachment($attachment_url)) {
                $this->metaInput['repair_instructions'] = $attachment_id;
                $this->metaInput['_repair_instructions'] = 'field_608ea90594f69';
            } else {
                $this->metaInput['repair_instructions'] = null;
            }
        } else {
            $this->metaInput['repair_instructions'] = null;
        }

    }

    /**
     * Set Parts Breakdown.
     */
    protected function setPartsBreakdown() {

        if ($parts_breakdown = $this->getPartsBreakdown()) {
            //$attachment_url = ABSPATH . 'parts-breakdown/' . $parts_breakdown;
            $attachment_url = ABSPATH . 'backflow-files/' . $parts_breakdown;

            if ($attachment_id = $this->setAttachment($attachment_url)) {
                $this->metaInput['parts_breakdown'] = $attachment_id;
                $this->metaInput['_parts_breakdown'] = 'field_60ab13be7e4aa';
            } else {
                $this->metaInput['parts_breakdown'] = null;
            }
        } else {
            $this->metaInput['parts_breakdown'] = null;
        }

    }

    protected function getTitle() {
        return $this->item[$this->columnAliases['title']];
    }

    protected function getDescription() {
        return $this->item[$this->columnAliases['description']];
    }

    protected function getPrice() {
        return $this->item[$this->columnAliases['price']];
    }

    protected function getSKU() {
        return $this->item[$this->columnAliases['sku']];
    }

    protected function getModel() {
        return $this->item[$this->columnAliases['model']];
    }

    protected function getWeight() {
        return $this->item[$this->columnAliases['weight']];
    }

    protected function getShippingClass() {
        return $this->item[$this->columnAliases['shipping_class']];
    }

    protected function getProductCategoryDescription() {
        return $this->item[$this->columnAliases['product_category_description']];
    }

    protected function getProductCategory() {
        return $this->item[$this->columnAliases['product_category']];
    }

    protected function getPartNumber() {
        return $this->item[$this->columnAliases['part_number']];
    }

    protected function getFeaturedImage() {
        return $this->item[$this->columnAliases['featured_image']];
    }

    protected function getKitIncludes() {
        return $this->item[$this->columnAliases['kit_includes']];
    }

    protected function getUPC() {
        return $this->item[$this->columnAliases['upc']];
    }

    protected function getCatalogID() {
        return $this->item[$this->columnAliases['catalog_id']];
    }

    protected function getDDIID() {
        return $this->item[$this->columnAliases['ddi_id']];
    }

    protected function getName() {
        return $this->item[$this->columnAliases['name']];
    }

    protected function getSpecSheet() {
        return $this->item[$this->columnAliases['spec_sheet']];
    }

    protected function getRepairProcedures() {
        return $this->item[$this->columnAliases['repair_procedures']];
    }

    protected function getRepairInstructions() {
        return $this->item[$this->columnAliases['repair_instructions']];
    }

    protected function getPartsBreakdown() {
        return $this->item[$this->columnAliases['parts_breakdown']];
    }

    protected function getAddOnJSONScript() {
        return $this->item[$this->columnAliases['add_on_json_script']];
    }

    protected function getRepairVideo() {
        return $this->item[$this->columnAliases['repair_video']];
    }

    protected function getSizeAttribute() {
        return $this->item[$this->columnAliases['size_attr']];
    }

    protected function getBrandAttribute() {
        return $this->item[$this->columnAliases['brand_attr']];
    }

    protected function setAttachment($attachment_url) {

        if (file_exists($attachment_url )) {

            $upload_dir = wp_upload_dir();
            $image_data = file_get_contents($attachment_url);
            $filename = basename($attachment_url);

            $title = sanitize_file_name($filename);

            if( post_exists( $title ) ){
                $attachment = get_page_by_title( $filename, OBJECT, 'attachment');

                if( !empty( $attachment ) ){

                	/*if ($attachment->post_mime_type == 'image/jpeg') {
						$meta = \wp_get_attachment_metadata( $attachment->ID );
						$backup_sizes = get_post_meta( $attachment->ID, '_wp_attachment_backup_sizes', true );

						// this must be -scaled if that exists, since wp_delete_attachment_files checks for original_files but doesn't recheck if scaled is included since that the one 'that exists' in WP . $this->source_file replaces original image, not the -scaled one.
						$source_file = wp_get_original_image_path($attachment->ID);
						$result = \wp_delete_attachment_files($attachment->ID, $meta, $backup_sizes, $source_file );

						// If Attached file is not the same path as file, this indicates a -scaled images is in play.
						$attached_file = get_attached_file($attachment->ID);
						if ($source_file !== $attached_file && file_exists($attached_file))
						{
							@unlink($attached_file);
						}

						copy($attachment_url, $source_file);

						$metadata = wp_generate_attachment_metadata( $attachment->ID, $source_file );
						wp_update_attachment_metadata( $attachment->ID, $metadata );
					}*/

                    return $attachment->ID;
                }

            } else {

                if ( wp_mkdir_p( $upload_dir['path'] ) ) {
                    $file = $upload_dir['path'] . '/' . $filename;
                } else {
                    $file = $upload_dir['basedir'] . '/' . $filename;
                }

                file_put_contents( $file, $image_data );
                $wp_filetype = wp_check_filetype( $filename, null );

                $attachment = [
                    'post_mime_type' => $wp_filetype['type'],
                    'post_title' => sanitize_file_name( $filename ),
                    'post_content' => '',
                    'post_status' => 'inherit'
                ];

                $attach_id = wp_insert_attachment( $attachment, $file );

                if ( $attach_id ) {
                    $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
                    wp_update_attachment_metadata( $attach_id, $attach_data );

                    return $attach_id;
                }
            }
        }

        return false;
    }

    protected function getProductBySku($sku) {
        global $wpdb;
        //$product_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_sku' AND meta_value='%s' LIMIT 1", $sku ) );
        $product_id = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT DISTINCT $wpdb->posts.ID FROM $wpdb->posts, $wpdb->postmeta
                  WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id AND
                  $wpdb->posts.post_status = 'publish' AND
                  $wpdb->posts.post_type = 'product' AND
                  $wpdb->postmeta.meta_key = %s AND
                  meta_value = %s LIMIT 1",
                '_sku',
                $sku
            )
        );
        return $product_id ?: null;
    }

    protected function createProductVariation( $product_id, $variation_data ){
        $variation_exist = false;
        // Get the Variable product object (parent)
        $product = wc_get_product($product_id);
        if ($product) {
            $term_slug = get_term_by('name', $variation_data['attributes']['size'], 'pa_size' ); // Get the term slug
            if ($term_slug) {

                // Check if product variation exists.
                $product_children = new WP_Query([
                    'post_type' => 'product_variation',
                    'post_parent' => $product_id,
                    'meta_query' => [
                        [
                            'key' => 'attribute_pa_size',
                            'value' => $term_slug->slug,
                        ]
                    ]
                ]);

                if ($product_children->have_posts()) {
                    $first_post = $product_children->posts[0];
                    $variation_exist = $first_post->ID;
                }

            }

            // Set meta data
            $meta_input = [];
            $meta_input['_variation_description'] = $this->getDescription();

            if ($featured_image = $this->getFeaturedImage()) {
                //$attachment_url = ABSPATH . 'product-photos/' . $featured_image;
                $attachment_url = ABSPATH . 'backflow-files/' . $featured_image;
                $meta_input['_thumbnail_id'] = $this->setAttachment($attachment_url);
            }

            if ($parts_breakdown = $this->getPartsBreakdown()) {
                //$attachment_url = ABSPATH . 'parts-breakdown/' . $parts_breakdown;
                $attachment_url = ABSPATH . 'backflow-files/' . $parts_breakdown;
                $meta_input['_parts_breakdown'] = $this->setAttachment($attachment_url);
            }

            if ($repair_procedures = $this->getRepairProcedures()) {
                //$attachment_url = ABSPATH . 'repair-procedures/' . $repair_procedures;
                $attachment_url = ABSPATH . 'backflow-files/' . $repair_procedures;
                $meta_input['_repair_procedures'] = $this->setAttachment($attachment_url);
            }

            if ($spec_sheet = $this->getSpecSheet()) {
                //$attachment_url = ABSPATH . 'repair-procedures/' . $repair_procedures;
                $attachment_url = ABSPATH . 'backflow-files/' . $spec_sheet;
                $meta_input['_spec_sheet'] = $this->setAttachment($attachment_url);
            }

            if ($repair_video = $this->getRepairVideo()) {
                $meta_input['_repair_video'] = $repair_video;
            }

            // Create variation
            $variation_post = array(
                'post_title'  => $product->get_name(),
                'post_name'   => 'product-'.$product_id.'-variation',
                'post_status' => 'publish',
                'post_parent' => $product_id,
                'post_type'   => 'product_variation',
                'guid'        => $product->get_permalink(),
                'meta_input' => $meta_input,
            );


            if ($variation_exist) {
                // Updating the product variation
                $variation_post['ID'] = $variation_exist;
                $variation_id = wp_update_post( $variation_post );
            } else {
                // Creating the product variation
                $variation_id = wp_insert_post( $variation_post );
            }

            // Get an instance of the WC_Product_Variation object
            $variation = new WC_Product_Variation( $variation_id );

            // Iterating through the variations attributes
            foreach ($variation_data['attributes'] as $attribute => $term_name )
            {
                $taxonomy = 'pa_'.$attribute; // The attribute taxonomy

                // If taxonomy doesn't exists we create it (Thanks to Carl F. Corneil)
                if( ! taxonomy_exists( $taxonomy ) ){
                    register_taxonomy(
                        $taxonomy,
                        'product_variation',
                        array(
                            'hierarchical' => false,
                            'label' => ucfirst( $attribute ),
                            'query_var' => true,
                            'rewrite' => array( 'slug' => sanitize_title($attribute) ), // The base slug
                        ),
                    );
                }

                // Check if the Term name exist and if not we create it.
                if( ! term_exists( $term_name, $taxonomy ) )
                    wp_insert_term( $term_name, $taxonomy ); // Create the term

                $term_slug = get_term_by('name', $term_name, $taxonomy )->slug; // Get the term slug

                // Get the post Terms names from the parent variable product.
                $post_term_names =  wp_get_post_terms( $product_id, $taxonomy, array('fields' => 'names') );

                // Check if the post term exist and if not we set it in the parent variable product.
                if( ! in_array( $term_name, $post_term_names ) )
                    wp_set_post_terms( $product_id, $term_name, $taxonomy, true );

                // Set/save the attribute data in the product variation
                update_post_meta( $variation_id, 'attribute_'.$taxonomy, $term_slug );
            }

            ## Set/save all other data

            // SKU
            if( ! empty( $variation_data['sku'] ) )
                $variation->set_sku( $variation_data['sku'] );

            // Prices
            if( empty( $variation_data['sale_price'] ) ){
                $variation->set_price( $variation_data['regular_price'] );
            } else {
                $variation->set_price( $variation_data['sale_price'] );
                $variation->set_sale_price( $variation_data['sale_price'] );
            }
            $variation->set_regular_price( $variation_data['regular_price'] );

            // Stock
            if( ! empty($variation_data['stock_qty']) ){
                $variation->set_stock_quantity( $variation_data['stock_qty'] );
                $variation->set_manage_stock(true);
                $variation->set_stock_status('');
            } else {
                $variation->set_manage_stock(false);
            }

            $variation->set_weight(''); // weight (reseting)

            $variation->save(); // Save the data
        }
    }

    protected function addProductAttributes($productID, $terms, $taxonomy, $is_variation = 0 ) {
        wp_set_object_terms($productID, $terms, $taxonomy, true);
        $_product_attributes = get_post_meta($productID, '_product_attributes', true);

        if (isset($_product_attributes[$taxonomy]['value']) && !empty($_product_attributes[$taxonomy]['value']) && is_numeric($terms)) {
            $_product_attributes[$taxonomy]['value'] += $terms;
        } else {

            if (is_array($_product_attributes) && !empty($_product_attributes)) {
                $_product_attributes[$taxonomy] = [
                    'name'=> $taxonomy,
                    'value'=> $terms,
                    'is_visible' => 1,
                    'is_taxonomy' => 1,
                    'is_variation' => $is_variation,
                ];
            } else {
                $_product_attributes = [];
                $_product_attributes[$taxonomy] = [
                    'name'=> $taxonomy,
                    'value'=> $terms,
                    'is_visible' => 1,
                    'is_taxonomy' => 1,
                    'is_variation' => $is_variation,
                ];
            }

        }

        update_post_meta( $productID, '_product_attributes', $_product_attributes);
    }

    protected function setProductTaxonomy($product_id, $term_name, $taxonomy) {
        if (isset($product_id) && isset($term_name) && isset($taxonomy)) {

            // Check if the Term name exist and if not we create it.
            if( ! term_exists( $term_name, $taxonomy ) )
                wp_insert_term( $term_name, $taxonomy ); // Create the term

            $term_id = get_term_by( 'name', $term_name, $taxonomy)->term_id;
            wp_set_object_terms($product_id, $term_id, $taxonomy, true);
        }
    }
}
