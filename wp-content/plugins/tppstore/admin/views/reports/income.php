<select name="year" id="year">
    <?php foreach ($years as $_year): ?>
        <option <?php echo $year == $_year?'selected="selected"':'' ?> value="<?php echo $_year ?>"><?php echo $_year ?></option>
    <?php endforeach; ?>
</select>
<?php

$total_commission = 0;
$total_transaction = 0;

?>

<table class="wp-list-table">
    <thead>
        <tr>
            <th>Year</th>
            <th>Currency</th>
            <th>Commission Income</th>
            <th>Total Transaction</th>
            <th>Total to Stores</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($incomes as $income): ?>
            <?php if ($income->currency == 'GBP'): ?>
                <?php

                $total_commission += $income->commission;
                $total_transaction += $income->total;

                ?>
                <tr>
                    <td><?php echo date('jS M, Y', $income->order_date) ?></td>
                    <td><?php echo $income->currency ?></td>
                    <td>&pound;<?php echo $income->commission ?></td>
                    <td>&pound;<?php echo $income->total ?></td>
                    <td>&pound;<?php echo $income->total - $income->commission ?></td>
                </tr>
            <?php else: ?>
                <?php

                $exchanges = unserialize($income->exchange_rates);


                switch ($income->currency) {
                    case 'GBP':
                        $currency_html = '&pound;';
                        break;
                    default:
                        $currency_html = '&dollar;';
                        break;
                }

                $total_commission += $income->commission / $exchanges[$income->currency];
                $total_transaction += $income->total / $exchanges[$income->currency];

                ?>
                <tr>
                    <td><?php echo date('jS M, Y', $income->order_date) ?></td>
                    <td><?php echo $income->currency ?></td>
                    <td>&pound;<?php echo number_format($income->commission / $exchanges[$income->currency], 2) . ' (' . $currency_html .  $income->commission . ')' ?></td>
                    <td>&pound;<?php echo number_format($income->total / $exchanges[$income->currency], 2) . ' (' . $currency_html .  $income->total . ')' ?></td>
                    <td>&pound;<?php echo number_format(($income->total - $income->commission) / $exchanges[$income->currency], 2) . ' (' . $currency_html . ($income->total - $income->commission) . ')' ?></td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
    </tbody>
</table>

<h1>Totals</h1>

<p>Commission: &pound;<?php echo number_format($total_commission, 2) ?></p>
<p>Transactions: &pound;<?php echo number_format($total_transaction, 2); ?></p>
<script>
    document.getElementById('year').onchange = function() {
        window.location.href = '<?php echo admin_url('admin.php?page=tpp-store-report&report=income'); ?>&year=' + this.value;
    }
</script>