<?php
/**
 * User: leeparsons
 * Date: 16/12/2013
 * Time: 07:27
 */
 

class TppStoreHelperPaginator {


    public $start_display_pagination_number = 1;
    public $last_display_pagination_number = 1;
    public $total_results = 0;
    public $results_per_page = 20;
    public $link_base = '/';
    public $page = 1;
    public $query_string = '';
    private $max_pages = 1;

    public function __construct()
    {
        $this->page = get_query_var('page');

        //default the results per page to 20

        if (intval($this->page) == 0) {
            $this->page = 1;
        }

        $this->base = $_SERVER['REQUEST_URI'];



        if (false !== ($question_mark = strpos($this->base, '?'))) {
            $this->query_string = substr($this->base, $question_mark);
            $this->base = substr($this->base, 0, $question_mark);
        }


        if (false !== ($pos = strpos($this->base, '/page/'))) {
            $this->base = substr($this->base, 0, $pos);
        } elseif ($this->base == '/') {
            $this->base = '';
        }

    }

    private function calculatePaginationParameters()
    {
        //start page = page / (total results / results per page)

        //if start page > 5 then start page is 2
        //if (total results / results per page) - 5 <= 0 then start page is 1 regardless and end page is (total results / results per page)

        $this->max_pages = ceil($this->total_results / $this->results_per_page);

        if ($this->page > $this->max_pages) {
            $this->page = $this->max_pages;
        }

        if ($this->max_pages <= 5 || $this->max_pages - $this->page <= 5) {
            //show max_pages links
            $this->start_display_pagination_number = 1;
        } elseif ($this->page > 5) {
            //start at max_pages - page
            $this->start_display_pagination_number = $this->max_pages - $this->page;
        }


        if ($this->start_display_pagination_number + 5 >= $this->max_pages) {
            $this->last_display_pagination_number = $this->max_pages;
        } else {
            $this->last_display_pagination_number = $this->start_display_pagination_number + 5;
        }

    }

    public function render()
    {

        $this->calculatePaginationParameters();

        ob_start();

        include TPP_STORE_PLUGIN_DIR . 'templates/paginator.php';

        $contents = ob_get_contents();

        ob_end_clean();

        return $contents;

    }

}