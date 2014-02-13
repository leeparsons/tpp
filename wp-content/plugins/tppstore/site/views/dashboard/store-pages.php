<?php include 'header.php'; ?>
<?php TppStoreMessages::getInstance()->render() ?>
<article class="page-article-part mystore">


    <header>
        <h1>Store Pages</h1>
    </header>

    <form method="post">


        <fieldset class="bt">
            <input type="submit" class="btn btn-primary" value="Save">
            <a href="/shop/dashboard" class="btn btn-default">Cancel</a>
        </fieldset>

        <fieldset>
            <legend>Terms and Conditions</legend>
            <div class="form-group">
                <pre>Here tell your customers any terms and conditions you have for your store, including refunds and exchanges, what happens in the case of cancellations of services/workshops etc.</pre>
                <?php

                wp_editor($store_pages->getTerms(), 'terms', array(
                    'media_buttons' =>  false,
                    'teeny'         =>  true
                ));

                ?>
            </div>
        </fieldset>

        <?php /*
        <fieldset>
            <legend>Refunds</legend>
            <div class="form-group">
                <?php

                wp_editor($store->getPages()->getRefunds(), 'refunds', array(
                    'media_buttons' =>  false,
                    'teeny'         =>  true
                ));

                ?>
            </div>
        </fieldset>



        <fieldset>
            <legend>Privacy Policy</legend>
            <div class="form-group">
                <?php

                wp_editor($store->getPages()->getPrivacy(), 'privacy', array(
                    'media_buttons' =>  false,
                    'teeny'         =>  true
                ));

                ?>
            </div>
        </fieldset>
*/ ?>
        <fieldset class="bt">
            <input type="submit" class="btn btn-primary" value="Save">
            <a href="/shop/dashboard" class="btn btn-default">Cancel</a>
        </fieldset>


    </form>

</article>
<?php

include 'footer.php';