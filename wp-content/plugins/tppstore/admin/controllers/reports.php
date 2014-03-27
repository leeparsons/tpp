<?php
/**
 * User: leeparsons
 * Date: 15/02/2014
 * Time: 20:04
 */
 
class TppStoreAdminControllerReports extends TppStoreAbstractBase {


    public static function renderReports()
    {
        include TPP_STORE_PLUGIN_DIR . 'admin/views/reports/list.php';
    }


    public function renderReport()
    {
        $report = $_GET['report'];

        $incomes = new TppStoreAdminModelOrders();

        $year = filter_input(INPUT_GET, 'year', FILTER_SANITIZE_NUMBER_INT);

        $year = intval($year);

        if ($year == 0) {
            $year = date('Y');
        }

        $years = $incomes->getYears();

        $incomes = $incomes->getIncomeByYear($year);



        include TPP_STORE_PLUGIN_DIR . 'admin/views/reports/' . $report . '.php';
    }

    public function renderBestSellers()
    {

        wp_enqueue_style('tpp_admin', TPP_STORE_PLUGIN_URL . '/admin/assets/css/style.css');
        $incomes = new TppStoreAdminModelOrders();

        $products = $incomes->getBestSellers();


        include TPP_STORE_PLUGIN_DIR . 'admin/views/reports/best_sellers.php';
    }

}