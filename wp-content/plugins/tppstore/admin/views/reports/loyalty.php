<?php
/**
 * User: leeparsons
 * Date: 02/03/2014
 * Time: 20:58
 */

if (!class_exists('geo')) {
    include get_template_directory() . '/classes/ip2location/locator.php';
}

require TPP_STORE_PLUGIN_DIR . 'helpers/paginator.php';

$paginator = new TppStoreHelperPaginator();

$paginator->total_results = $total;


if (count($orders) > 0): ?>

    <div class="wrap">
        <?php echo $paginator->renderAdmin(); ?>
    </div>
    <div class="wrap">
        <form action="<?php echo admin_url('admin.php') ?>">
            <input type="hidden" name="page" value="tpp-store-loyalty-report">
            <input type="hidden" name="paged" value="1">
        <select name="status">

            <option value="">-- All --</option>
            <option value="complete">Complete</option>
            <option value="pending">Pending</option>
            <option value="failed">Failed</option>
            <option value="cancelled">Cancelled</option>
        </select>
            <input type="submit" value="Filter" class="button-primary">
        </form>
    </div>
    <div class="wrap">
    <table class="wp-list-table">
        <thead>
        <tr>
            <th>Name</th>
            <th>Orders</th>
            <th>Status</th>
            <th>Avg GBP Order Value</th>
            <th>GBP Sales</th>

            <th>Avg Order Value</th>
            <th>Sales</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($orders as $order): ?>
            <tr>
                <td>
                    <?php echo $order->name ?>
                    <br>
                    <span class="expander">Show Details</span>
                </td>
                <td>
                    <?php echo $order->orders ?>
                </td>
                <td>
                    <?php echo $order->status ?>
                </td>
                <td>
                    &pound;<?php echo number_format($order->gbp_avg, 2) ?>
                </td>
                <td>
                    &pound;<?php echo number_format($order->gbp_total, 2) ?>
                </td>
                <td>
                    <?php echo geo::getCurrencyHtml($order->currency) . number_format($order->avg, 2) ?>
                </td>
                <td><?php echo geo::getCurrencyHtml($order->currency) . number_format($order->total, 2) ?></td>
            </tr>
            <tr style="display:none">
                <td colspan="7" style="background:#fff">
                    <?php if (isset($order_lines[$order->order_id])): ?>
                        <?php foreach ($order_lines[$order->order_id] AS $line_item): ?>
                            <?php echo $line_item->product_name ?>

                        <?php endforeach; ?>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
<?php endif; ?>
<script>
    var expanders = document.getElementsByClassName('expander');
    if (expanders) {
        for (var x = 0; x < expanders.length; x++) {
            expanders[x].onclick = function() {

                this.parentNode.parentNode.nextSibling.nextSibling.style.display = 'table-row';
            }
        }
    }
</script>