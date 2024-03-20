<?php
/*
Plugin Name: Backflow Import
Version:     1.0.0
*/

defined('ABSPATH') or die('Nope, not accessing this');
require_once(ABSPATH . 'wp-includes/pluggable.php');

require_once __DIR__ . '/vendor/autoload.php';

define('BACKFLOW_IMPORT_PATH', plugin_dir_path(__FILE__));
define('BACKFLOW_IMPORT_URL', plugin_dir_url(__FILE__));

class BackflowImport {

    public function __construct()
    {
        $this->register_activation_hooks();
        $this->add_base_actions();
    }

    public function register_activation_hooks()
    {
        // add plugin pages
        register_activation_hook(__FILE__, [$this, 'create_plugin_pages']);
    }

    public function add_base_actions()
    {
        // admin_menu
        add_action('admin_menu', array($this, 'admin_menu'));
        // admin_enqueue_scripts
        add_action('admin_enqueue_scripts', [$this, 'import_scripts']);

        add_action('admin_post_backflow_products_import', [$this, 'backflow_products_import']);
        add_action('admin_post_backflow_models_import', [$this, 'backflow_models_import']);
        add_action('admin_post_backflow_models_products_relations_import', [$this, 'backflow_models_products_relations_import']);
        add_action('admin_post_backflow_prices_import', [$this, 'backflow_prices_import']);
        add_action('admin_post_backflow_orders_ddi_import', [$this, 'backflow_orders_ddi_import']);

        add_action('admin_notices', [$this, 'backflow_products_import_success']);
        add_action('admin_notices', [$this, 'backflow_models_import_success']);
        add_action('admin_notices', [$this, 'backflow_models_products_relations_import_success']);
    }

    public function admin_menu()
    {
        add_menu_page(
            __('Backflow Products Import', 'backflow'),
            __('Backflow Products Import', 'backflow'),
            'manage_options',
            'backflow-products-import',
            [$this, 'import_products_init']
        );

        //Backflow Models Import
        add_submenu_page(
                'backflow-products-import',
                'Backflow Models Import',
                'Backflow Models Import',
                'manage_options',
                'backflow-models-import',
            [$this, 'import_models_init']
        );

        // Backflow Models/Products Relations Import
        add_submenu_page(
            'backflow-products-import',
            'Backflow Models/Products Relations Import',
            'Backflow Models/Products Relations Import',
            'manage_options',
            'backflow-models-products-relations-import',
            [$this, 'import_models_products_relations_init']
        );

        // Backflow Prices Import
        add_submenu_page(
            'backflow-products-import',
            'Backflow Prices Import',
            'Backflow Prices Import',
            'manage_options',
            'backflow-prices-import',
            [$this, 'import_prices_init']
        );

        // Backflow Orders DDI Import
        add_submenu_page(
            'backflow-products-import',
            'Backflow Orders DDI Import',
            'Backflow Orders DDI Import',
            'manage_options',
            'backflow-orders-ddi-import',
            [$this, 'import_orders_ddi_init']
        );

    }

    public function import_products_init() {
        include BACKFLOW_IMPORT_PATH . 'templates/admin/import-products-index.php';
    }

    public function import_models_init() {
        include BACKFLOW_IMPORT_PATH . 'templates/admin/import-models-index.php';
    }

    public function import_models_products_relations_init() {
        include BACKFLOW_IMPORT_PATH . 'templates/admin/import-models-products-relations-index.php';
    }

    public function import_prices_init() {
        include BACKFLOW_IMPORT_PATH . 'templates/admin/import-prices-index.php';
    }

    public function import_orders_ddi_init() {
        include BACKFLOW_IMPORT_PATH . 'templates/admin/import-orders-ddi-index.php';
    }

    public static function backflow_products_import_success() {
        if ( ! empty( $_GET['backflow-import'] ) && $_GET['backflow-import'] == 'success' ) : ?>
            <div class="notice notice-success">
                <p>Backflow Products have been imported.</p>
            </div>
        <?php endif;
    }

    public static function backflow_models_import_success() {
        if ( ! empty( $_GET['backflow-models-import'] ) && $_GET['backflow-models-import'] == 'success' ) : ?>
            <div class="notice notice-success">
                <p>Backflow Models have been imported.</p>
            </div>
        <?php endif;
    }

    public static function backflow_models_products_relations_import_success() {
        if ( ! empty( $_GET['backflow-models-products-relations-import'] ) && $_GET['backflow-models-products-relations-import'] == 'success' ) : ?>
            <div class="notice notice-success">
                <p>Backflow Models/Products Relations have been imported.</p>
            </div>
        <?php endif;
    }

    public static function backflow_orders_ddi_import_success() {
        if ( ! empty( $_GET['backflow-orders-ddi-import'] ) && $_GET['backflow-orders-ddi-import'] == 'success' ) : ?>
            <div class="notice notice-success">
                <p>Backflow Orders DDI have been imported.</p>
            </div>
        <?php endif;
    }

    public static function import_scripts()
    {
        wp_enqueue_style('backflow-import-style', plugin_dir_url( __FILE__ ) . 'assets/css/style.css', []);
        wp_enqueue_script('backflow-import-script', plugin_dir_url( __FILE__ ) . 'assets/js/backflow-import.js', array( 'jquery' ), '', true);
    }

    /**
     * Backflow Products Import.
     */
    public static function backflow_products_import() {
        include BACKFLOW_IMPORT_PATH . 'inc/ProductsBackflowImporter.php';

        if ( ! empty( $_POST ) && check_admin_referer( 'backflow_products_import', 'backflow_products_import_nonce_field' ) ) {

            if ( ! empty( $_FILES['csv']['name'] ) ) {
                $csvname = $_FILES['csv']['name'];
                $ext = pathinfo( $csvname, PATHINFO_EXTENSION );

                if ( ! $ext == 'csv' ) {
                    wp_die( '<p>Invalid file format!</p>' );
                } else {

                    $csv = new ParseCsv\Csv();

                    $csv_file_content = file_get_contents( $_FILES['csv']['tmp_name'] );

//                    $csv->load_data($csv_file_content);
//                    $total_count = $csv->getTotalDataRowCount();

                    $paged = isset($_POST['paged']) && !empty($_POST['paged']) ? $_POST['paged'] : 1;
                    $total_count = isset($_POST['total_count']) && !empty($_POST['total_count']) ? (int) $_POST['total_count'] : false;

                    if (!$total_count) {
                        $total_count = self::getTotalCountRows();
                    }

                    //$limit = isset($_POST['limit']) && !empty($_POST['limit']) ? $_POST['limit'] : 10;

                    $limit = 10;
                    $offset = ($paged * $limit) - $limit;

                    $csv->parse($csv_file_content, $offset, $limit);

                    $productsBackflowImporter = new ProductsBackflowImporter($csv, [
                        'title' => 'Display Name',
                        'name' => 'Name',
                        'featured_image' => 'Primary Photo',
                        'sku' => 'SKU',
                        'ddi_id' => 'DDI #',
                        //'model' => 'Model',
                        'size_attr' => 'Size',
                        'catalog_id' => 'Catalog #',
                        'kit_includes' => 'Kit Includes',
                        'upc' => 'UPC',
                        'weight' => 'Weight',
                        'shipping_class' => 'Shipping Class',
                        'spec_sheet' => 'Spec Sheet',
                        'repair_instructions' => 'Repair Instructions',
                        'repair_video' => 'Repair Video',
                        //'add_on_json_script' => 'Add on JSON Script',
                    ]);
                    $productsBackflowImporter->import();

                    $import_done = ceil($total_count / $limit) <= $paged;

                    if (! $import_done) {
                        $status = 'in_progress';
                        $imported_count = $limit * $paged;
                    } else {
                        $status = 'done';
                        $imported_count = $total_count;

                        wc_update_product_lookup_tables();
                    }

                    echo json_encode([
                        'success' => true,
                        'status' => $status,
                        'total_count' => $total_count,
                        'imported_count' => $imported_count,
                        'limit' => $limit,
                        'paged' => $paged + 1,
                    ]);

                    //wp_redirect( admin_url( 'admin.php?page=backflow-import&backflow-import=success' ) );
                    exit;
                }
            } else {
                wp_die( '<p>File is empty!</p>' );
            }
        } else {
            wp_die( 'Access denied!' );
        }
    }

    /**
     * Backflow Models Import.
     */
    public static function backflow_models_import() {
        include BACKFLOW_IMPORT_PATH . 'inc/ModelsBackflowImporter.php';
        add_option('backflow_models_import_current_sku', '');

        if ( ! empty( $_POST ) && check_admin_referer( 'backflow_models_import', 'backflow_models_import_nonce_field' ) ) {

            if ( ! empty( $_FILES['csv']['name'] ) ) {
                $csvname = $_FILES['csv']['name'];
                $ext = pathinfo( $csvname, PATHINFO_EXTENSION );

                if ( ! $ext == 'csv' ) {
                    wp_die( '<p>Invalid file format!</p>' );
                } else {

                    $csv = new ParseCsv\Csv();
                    $csv_file_content = file_get_contents( $_FILES['csv']['tmp_name'] );

//                    $csv->load_data($csv_file_content);
//                    $total_count = $csv->getTotalDataRowCount();

                    $paged = isset($_POST['paged']) && !empty($_POST['paged']) ? $_POST['paged'] : 1;
                    $total_count = isset($_POST['total_count']) && !empty($_POST['total_count']) ? (int) $_POST['total_count'] : false;

                    if (!$total_count) {
                        $total_count = self::getTotalCountRows();
                    }

                    //$limit = isset($_POST['limit']) && !empty($_POST['limit']) ? $_POST['limit'] : 10;

                    $limit = 5;
                    $offset = ($paged * $limit) - $limit;

                    $csv->parse($csv_file_content, $offset, $limit);

                    $modelsBackflowImporter = new ModelsBackflowImporter($csv, [
                        'title' => 'Name',
                        'sku' => 'Model ID',
                        'featured_image' => 'Image',
                        'parts_breakdown' => 'Parts Breakdown Button',
                        'spec_sheet' => 'Spec Sheet Button',
                        'repair_procedures' => 'Repair Procedures',
                        'repair_video' => 'Repair Video',
                        'size_attr' => 'Size',
                        'brand_attr' => 'Manufacturer ID',
                        'description' => 'Description',
                    ]);

                    // Set first row data to the model.
                    if ($backflow_models_import_current_sku = get_option('backflow_models_import_current_sku')) {
                        $modelsBackflowImporter->currentModelSKU = $backflow_models_import_current_sku;
                    }

                    $modelsBackflowImporter->import();


                    $import_done = ceil($total_count / $limit) <= $paged;

                    if (! $import_done) {
                        $status = 'in_progress';
                        $imported_count = $limit * $paged;
                    } else {
                        $status = 'done';
                        $imported_count = $total_count;

                        wc_update_product_lookup_tables();
                    }

                    echo json_encode([
                        'success' => true,
                        'status' => $status,
                        'total_count' => $total_count,
                        'imported_count' => $imported_count,
                        'limit' => $limit,
                        'paged' => $paged + 1,
                    ]);

                    //wp_redirect( admin_url( 'admin.php?page=backflow-models-import&backflow-models-import=success' ) );
                    exit;
                }
            } else {
                wp_die( '<p>File is empty!</p>' );
            }
        } else {
            wp_die( 'Access denied!' );
        }
    }

    /**
     * Backflow Models/Products Relations Import.
     */
    public static function backflow_models_products_relations_import() {
        include BACKFLOW_IMPORT_PATH . 'inc/ModelsProductsRelationsBackflowImporter.php';

        if ( ! empty( $_POST ) && check_admin_referer( 'backflow_models_products_relations_import', 'backflow_models_products_relations_import_nonce_field' ) ) {

            if ( ! empty( $_FILES['csv']['name'] ) ) {
                $csvname = $_FILES['csv']['name'];
                $ext = pathinfo( $csvname, PATHINFO_EXTENSION );

                if ( ! $ext == 'csv' ) {
                    wp_die( '<p>Invalid file format!</p>' );
                } else {

                    $csv = new ParseCsv\Csv();
                    $csv_file_content = file_get_contents( $_FILES['csv']['tmp_name'] );

//                    $csv->load_data($csv_file_content);
//                    $total_count = $csv->getTotalDataRowCount();

                    $paged = isset($_POST['paged']) && !empty($_POST['paged']) ? $_POST['paged'] : 1;
                    $total_count = isset($_POST['total_count']) && !empty($_POST['total_count']) ? (int) $_POST['total_count'] : false;

                    if (!$total_count) {
                        $total_count = self::getTotalCountRows();
                    }

                    //$limit = isset($_POST['limit']) && !empty($_POST['limit']) ? $_POST['limit'] : 10;

                    $limit = 10;
                    $offset = ($paged * $limit) - $limit;

                    $csv->parse($csv_file_content, $offset, $limit);

                    $modelsProductsRelationsBackflowImporter = new ModelsProductsRelationsBackflowImporter($csv, [
                        'sku' => 'SKU',
                        'model' => 'Model',
                        'product_category' => 'Category',
                        'brand_attr' => 'Manufacturer',
                        'size_attr' => 'Size',
                        'add_on_json_script' => 'Add on JSON Script',

                    ]);

                    if ($paged == 1) {
                        $modelsProductsRelationsBackflowImporter->clearCustomProductsOrder();
                    }

                    $modelsProductsRelationsBackflowImporter->import();


                    $import_done = ceil($total_count / $limit) <= $paged;

                    if (! $import_done) {
                        $status = 'in_progress';
                        $imported_count = $limit * $paged;
                    } else {
                        $status = 'done';
                        $imported_count = $total_count;

                        wc_update_product_lookup_tables();
                    }

                    echo json_encode([
                        'success' => true,
                        'status' => $status,
                        'total_count' => $total_count,
                        'imported_count' => $imported_count,
                        'limit' => $limit,
                        'paged' => $paged + 1,
                    ]);

                    //wp_redirect( admin_url( 'admin.php?page=backflow-models-import&backflow-models-import=success' ) );
                    exit;
                }
            } else {
                wp_die( '<p>File is empty!</p>' );
            }
        } else {
            wp_die( 'Access denied!' );
        }
    }

    /**
     * Backflow Prices Import.
     */
    public static function backflow_prices_import() {
        include BACKFLOW_IMPORT_PATH . 'inc/PricesBackflowImporter.php';

        if ( ! empty( $_POST ) && check_admin_referer( 'backflow_prices_import', 'backflow_prices_import_nonce_field' ) ) {

            if ( ! empty( $_FILES['csv']['name'] ) ) {
                $csvname = $_FILES['csv']['name'];
                $ext = pathinfo( $csvname, PATHINFO_EXTENSION );

                if ( ! $ext == 'csv' ) {
                    wp_die( '<p>Invalid file format!</p>' );
                } else {

                    $csv = new ParseCsv\Csv();
                    $csv->heading = false;

                    $csv_file_content = file_get_contents( $_FILES['csv']['tmp_name'] );

//                    $csv->load_data($csv_file_content);
//                    $total_count = $csv->getTotalDataRowCount();

                    $paged = isset($_POST['paged']) && !empty($_POST['paged']) ? $_POST['paged'] : 1;
                    $total_count = isset($_POST['total_count']) && !empty($_POST['total_count']) ? (int) $_POST['total_count'] : false;

                    if (!$total_count) {
                        $total_count = self::getTotalCountRows();
                    }

                    //$limit = isset($_POST['limit']) && !empty($_POST['limit']) ? $_POST['limit'] : 10;

                    $limit = 100;
                    $offset = ($paged * $limit) - $limit;

                    $csv->parse($csv_file_content, $offset, $limit);

                    $pricesBackflowImporter = new PricesBackflowImporter($csv);

                    $pricesBackflowImporter->offset = $offset;
                    $pricesBackflowImporter->user_id = isset($_POST['user_id']) && !empty($_POST['user_id']) ? (int) $_POST['user_id'] : false;
                    $pricesBackflowImporter->columns_count = isset($_POST['columns_count']) && !empty($_POST['columns_count']) ? (int) $_POST['columns_count'] : false;
                    $pricesBackflowImporter->column_index = isset($_POST['column_index']) && !empty($_POST['column_index']) ? (int) $_POST['column_index'] : 1;

                    $pricesBackflowImporter->import();

                    $import_done = ceil($total_count / $limit) <= $paged;

                    if (! $import_done) {
                        $status = 'in_progress';
                        $imported_count = $limit * $paged;
                    } else {

                        if ($pricesBackflowImporter->column_index < ($pricesBackflowImporter->columns_count - 1)) {
                            ++$pricesBackflowImporter->column_index;
                            $paged = 0;
                            $status = 'in_progress';
                            $imported_count = $limit * $paged;

                            // fix send user id.
                            $csv->parse($csv_file_content, 0, 1);
                            $pricesBackflowImporter->user_id = $csv->data[0][$pricesBackflowImporter->column_index];

                        } else {
                            $status = 'done';
                            $imported_count = $total_count;

                            wc_update_product_lookup_tables();
                        }

                    }

                    echo json_encode([
                        'success' => true,
                        'status' => $status,
                        'total_count' => $total_count,
                        'user_id' => $pricesBackflowImporter->user_id,
                        'column_index' => $pricesBackflowImporter->column_index,
                        'columns_count' => $pricesBackflowImporter->columns_count,
                        'imported_count' => $imported_count,
                        'limit' => $limit,
                        'paged' => $paged + 1,
                    ]);

                    //wp_redirect( admin_url( 'admin.php?page=backflow-models-import&backflow-models-import=success' ) );
                    exit;
                }
            } else {
                wp_die( '<p>File is empty!</p>' );
            }
        } else {
            wp_die( 'Access denied!' );
        }
    }

    /**
     * Orders DDI Backflow Import.
     */
    public static function backflow_orders_ddi_import() {
        include BACKFLOW_IMPORT_PATH . 'inc/OrdersDDIBackflowImporter.php';

        if ( ! empty( $_POST ) && check_admin_referer( 'backflow_orders_ddi_import', 'backflow_orders_ddi_import_nonce_field' ) ) {

            if ( ! empty( $_FILES['csv']['name'] ) ) {
                $csvname = $_FILES['csv']['name'];
                $ext = pathinfo( $csvname, PATHINFO_EXTENSION );

                if ( ! $ext == 'csv' ) {
                    wp_die( '<p>Invalid file format!</p>' );
                } else {

                    $csv = new ParseCsv\Csv();

                    $csv_file_content = file_get_contents( $_FILES['csv']['tmp_name'] );

//                    $csv->load_data($csv_file_content);
//                    $total_count = $csv->getTotalDataRowCount();

                    $paged = isset($_POST['paged']) && !empty($_POST['paged']) ? $_POST['paged'] : 1;
                    $total_count = isset($_POST['total_count']) && !empty($_POST['total_count']) ? (int) $_POST['total_count'] : false;

                    if (!$total_count) {
                        $total_count = self::getTotalCountRows();
                    }

                    //$limit = isset($_POST['limit']) && !empty($_POST['limit']) ? $_POST['limit'] : 10;

                    $limit = 10;
                    $offset = ($paged * $limit) - $limit;

                    $csv->parse($csv_file_content, $offset, $limit);

                    $productsBackflowImporter = new OrdersDDIBackflowImporter($csv, [
                        'accountNumber' => 'accountNumber',
                        'purchaseOrder' => 'purchaseOrder',
                        'shipCompanyName' => 'shipCompanyName',
                        'shipAddress1' => 'shipAddress1',
                        'shipAddress2' => 'shipAddress2',
                        'shipAddress3' => 'shipAddress3',
                        'shipCity' => 'shipCity',
                        'shipState' => 'shipState',
                        'shipPostCode' => 'shipPostCode',
                        'shipAttention' => 'shipAttention',
                        'specialInstructions' => 'specialInstructions',
                        'specialPayInstructions' => 'specialPayInstructions',
                        'stockNum' => 'stockNum',
                        'qty' => 'qty',
                        'price' => 'price',
                        'jobName' => 'jobName',
                        'emailTo' => 'emailTo',
                        'emailCC' => 'emailCC',
                        'shipMethod' => 'shipMethod',
                        'backOrderMethod' => 'backOrderMethod',
                        'billAttention' => 'billAttention',
                        'orderType' => 'orderType',
                        'orderTypeDescription' => 'orderTypeDescription',
                    ]);
                    $productsBackflowImporter->import();

                    $import_done = ceil($total_count / $limit) <= $paged;

                    if (! $import_done) {
                        $status = 'in_progress';
                        $imported_count = $limit * $paged;
                    } else {
                        $status = 'done';
                        $imported_count = $total_count;
                    }

                    echo json_encode([
                        'success' => true,
                        'status' => $status,
                        'total_count' => $total_count,
                        'imported_count' => $imported_count,
                        'limit' => $limit,
                        'paged' => $paged + 1,
                    ]);

                    //wp_redirect( admin_url( 'admin.php?page=backflow-import&backflow-import=success' ) );
                    exit;
                }
            } else {
                wp_die( '<p>File is empty!</p>' );
            }
        } else {
            wp_die( 'Access denied!' );
        }
    }

    protected static function getTotalCountRows() {
        $data_found = 0;
        $handle = fopen($_FILES['csv']['tmp_name'], "r");
        while ($data = fgetcsv($handle)) if ($data[1] != '') $data_found ++;
        return $data_found;
    }

}

$csvImport = new BackflowImport();

if(function_exists('vi')) {
    vi($csvImport);
};