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

}