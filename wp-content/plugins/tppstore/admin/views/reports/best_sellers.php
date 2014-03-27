<?php
/**
 * User: leeparsons
 * Date: 02/03/2014
 * Time: 20:58
 */


require TPP_STORE_PLUGIN_DIR . 'helpers/paginator.php';

$paginator = new TppStoreHelperPaginator();

$paginator->total_results = $total;


if (count($products) > 0): ?>

    <div class="wrap">
        <?php echo $paginator->renderAdmin(); ?>
    </div>
    <table class="wp-list-table">
        <thead>
        <tr>
            <th>Title</th>
            <th>Type</th>
            <th>Quantity</th>
            <th>Commission</th>
            <th>Sales</th>
            <th>Amount (in foreign currency)</th>
        </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
            <tr>
                <td>
                    <?php echo $product->product_title ?>
                </td>
                <td>
                    <?php

                    switch ($product->product_type) {
                        case '1':
                            echo 'Download';
                            break;
                        case '2':
                            echo 'Service';
                            break;
                        case '3':
                            echo 'Product';
                            break;
                        case '4':
                            echo 'Mentor Session';
                            break;
                        case '5':
                            echo 'Event/ Workshop';
                            break;
                    }

                    ?>
                </td>
                <td>
                    <?php echo $product->quantity ?>
                </td>
                <td>
                    <?php

                    switch ($product->currency) {
                        case 'GBP':
                            $currency = '&pound;';
                            break;
                        default:
                            $currency = '&dollar;';
                            break;
                    }





                   echo $currency . ' ' . number_format($product->commission, 2);




                    ?> <?php  ?>
                </td>
                <td><?php echo $currency . ' ' . number_format($product->total, 2) ?></td>
                <td><?php

                    echo $currency . ' ' . number_format($product->alternate_currency_total, 2);

                    ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>