<legend>The Mentor</legend>

<div class="form-group">

    <div class="control-group" id="mentor_section">
        <pre>Thanks for choosing to offer mentor sessions through The Photography Parlour!

The listing process is slightly different to normal products. Please start by entering the name of the person the mentor session will be with, and the company name.</pre>

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

        <?php /*
        <h3>Specialities</h3>

        <div class="form-group">
            <pre>Enter three key areas related to photography, marketing or business that you'd particularly like to provide advice on.

These will appear with all the other mentor listings so try to make them stand out - good examples would be:

"Creating Marketing Plans",
"7 Hour Wedding Workflow",
"Upselling Album Packages"</pre>

            <div class="control-section">
                <label for="specialism_one">First Specialism</label>
                <input type="text" name="specialism_one" value="<?php echo $product->getMentor()->getSpecialism()->specialism_one ?>" id="specialism_one" placeholder="Main specialism" class="form-control">
            </div>
        </div>

        <div class="form-group">


            <div class="control-section">
                <label for="specialism_two">Second Specialism</label>
                <input type="text" name="specialism_two" value="<?php echo $product->getMentor()->getSpecialism()->specialism_two ?>" id="specialism_two" placeholder="Secondary specialism" class="form-control">
            </div>

        </div>
        <div class="form-group">

            <div class="control-section">
                <label for="specialism_three">Third Specialism</label>
                <input type="text" name="specialism_three" value="<?php echo $product->getMentor()->getSpecialism()->specialism_three ?>" id="specialism_three" placeholder="Third specialism" class="form-control">
            </div>

        </div>
*/ ?>
    </div>
</div>