<legend>The Mentor</legend>

<div class="form-group">

    <div class="control-group" id="mentor_section">
        <pre>Thanks for choosing to offer mentor sessions through The Photography Parlour!

The listing process is slightly different to normal products. Please start by entering the name of the person the mentor session will be with, and the company name.</pre>
<?php /*
        <div class="form-group">
            <label for="mentor_name">Mentor Name</label>
            <input type="text" placeholder="Mentor Name" class="form-control" value="<?php echo $product->getMentor()->mentor_name ?>" name="mentor_name" id="mentor_name">
        </div>

        <div class="form-group">
            <label for="mentor_company">Company/ Business Name</label>
            <input type="text" placeholder="Company/ Business Name" class="form-control" value="<?php echo $product->getMentor()->mentor_company ?>" name="mentor_company" id="mentor_company">
        </div>

        <div class="form-group">
            <label for="mentor_city">City you are based in</label>

            <input type="text" placeholder="City" class="form-control" value="<?php echo $product->getMentor()->mentor_city ?>" name="mentor_city" id="mentor_city">
        </div>

        <div class="form-group">
            <label for="mentor_country">Country</label>
            <?php

            $select_name = 'mentor_country';
            $select_id = 'mentor_country';
            $selected_value = $product->getMentor()->mentor_country;

            flush(); include TPP_STORE_PLUGIN_DIR . 'templates/countries.php';flush();

            unset($select_id);
            unset($select_name);

            ?>

        </div>

*/ ?>



        <div class="form-group">
            <?php if (count($mentors) > 0): ?>
                <label for="mentor">Select the mentor</label>
                <select name="mentor" id="mentor">
                    <?php foreach ($mentors as $mentor): ?>
                        <option value="<?php echo $mentor->mentor_id ?>"><?php echo $mentor->mentor_name ?> <?php echo $mentor->mentor_company == ''?:' (' . $mentor->mentor_company . ')' ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($selected_mentor) && $selected_mentor != ''): ?>
                    <script>document.getElementById('mentor').value = '<?php echo $selected_mentor ?>';</script>
                <?php endif; ?>
            <?php endif; ?>

        </div>

    </div>
</div>