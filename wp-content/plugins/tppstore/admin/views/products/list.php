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
            <th>Price</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($products as $product): ?>
            <?php

            $url = admin_url('admin.php?page=tpp-store-product&id=' . $product->product_id);

            ?>
            <tr>
                <td>
                    <a href="<?php echo $url; ?>">
                        <?php echo $product->product_title ?>
                    </a>
                </td>
                <td>
                    <a href="<?php echo $url; ?>">
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
                    </a>
                </td>
                <td>
                    <a href="<?php echo $url; ?>">
                        <?php
                        switch ($product->currency) {
                            case 'GBP':
                                echo '&pound;';
                                break;
                            default:
                                echo '&dollar;';
                                break;
                        }
                         ?> <?php echo $product->getFormattedPrice(false, false); ?>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>