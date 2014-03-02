<legend>The event</legend>
<div class="form-group">
    <label for="product_title">Event Title</label>
    <pre>The maximum number of letters and spaces allowed is 140.</pre>
    <input type="text" name="product_title" id="product_title" value="<?php echo $product->product_title ?>" class="form-control" placeholder="Event title">
</div>

<div class="form-group">
    <label for="address">Event Location (address)</label>
    <textarea name="address" id="address" class="form-control" rows="5"><?php echo esc_textarea($product->address) ?></textarea>
    <input type="hidden" name="lat" id="lat" value="<?php echo $product->lat ?>">
    <input type="hidden" name="lng" id="lng" value="<?php echo $product->lng ?>">
</div>

<?php if (false !== ($map = $product->getMap())): ?>
    <div class="form-group">
        <label>Event Map</label>
        <?php echo $map; ?>

    </div>
<?php endif; ?>

<script>
    document.getElementById('address').onchange = function() {
        document.getElementById('lat').value = '';
        document.getElementById('lng').value = '';
    }
</script>

<div class="form-group">
    <label for="event_start_date">Event Start Date</label>
    <pre>The date the event starts</pre>
    <input type="text" name="event_start_date" id="event_start_date" value="<?php echo $product->getFormattedEventStartDate() ?>" class="form-control date" placeholder="Event Start Date">
</div>

<div class="form-group">
    <label for="event_end_date">Event End Date</label>
    <pre>The date the event ends (leave blank if it is a one day event)</pre>
    <input type="text" name="event_end_date" id="event_end_date" value="<?php echo $product->getFormattedEventEndDate() ?>" class="form-control date" placeholder="Event End Date">
</div>

<?php /*
<div class="form-group">
    <label for="listing_expire">Listing End Date</label>
    <pre>The date you would like the event listing to be removed from the website's listings</pre>
    <input type="text" name="listing_expire" id="listing_expire" value="<?php echo $product->getFormattedListingExpireDate() ?>" class="form-control date" placeholder="Listing Expiry Date">
</div>
 */ ?>