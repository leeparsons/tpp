<div class="wrap">
    <form>
    <table>
        <thead>
            <tr>
                <th style="width:30%">Product</th>
                <th style="width:30%">Store</th>
                <th style="width:10%;">On Sidebar</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($products as $product): ?>
        <tr>
            <td><?php echo $product->product_title ?></td>
            <td><?php echo $product->store_name ?></td>
            <td><img class="toggle" data-id="<?php echo $product->product_id ?>" data-on="<?php echo $product->position == 'sidebar'?'1':'0' ?>" src="/assets/images/<?php






                switch ($product->position) {
                    case 'sidebar':
                echo 'tick.png';

                        break;
                    default:
                        echo 'cross.png';

                        break;
                }

                ?>"/>
                <input style="display:none" id="checkbox_<?php echo $product->product_id ?>" type="checkbox" name="sidebar[<?php echo $product->product_id ?>]" <?php echo $product->position == 'sidebar'?'checked="checked"':'' ?>>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </form>
</div>
<script>
    var tgs = document.getElementsByClassName('toggle');
    var src = 'tick.png';
    for (var x = 0; x < tgs.length; x++) {
        tgs[x].onclick = function() {
            if (this.getAttribute('data-on') == 1) {
                this.setAttribute('data-on', 0);
                src = 'cross.png';
                document.getElementById('checkbox_' + this.getAttribute('data-id')).checked = false;

            } else {
                src = 'tick.png';
                this.setAttribute('data-on', 1);
                document.getElementById('checkbox_' + this.getAttribute('data-id')).checked = true;
            }
            this.setAttribute('src', '/assets/images/' + src);

        }
    }

</script>