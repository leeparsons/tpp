<?php get_header(); ?>
    <article class="page-article cart-page">

        <header>
            <h1>One Off Payment</h1>
        </header>

        <p>
            <pre>
            Choose your store that you wish to make your one off payment to and enter the amount you wish to pay.
            <br>
            If you have a reference or note to add please use the boxes below to enter those details.
            </pre>
        </p>

        <section class="row">
            <form method="post" action="/shop/oneoffpayment/pay/">
                <fieldset>

                    <div class="form-group">
                        <label for="store">Store:</label>
                        <select name="store" id="store" class="form-control">
                            <?php foreach ($stores as $_store): ?>
                                <option <?php echo $selected_store == $_store->store_slug?'selected':'' ?> value="<?php echo $_store->store_id ?>"><?php echo $_store->store_name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="currency">Currency:</label>
                        <select name="currency" id="currency" class="form-control">
                            <?php foreach (TppStoreModelCurrency::$currencies as $currency_code =>  $html): ?>
                            <option value="<?php echo $currency_code; ?>"><?php echo $html; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="amount">Amount: (<?php echo geo::getInstance()->getCurrencyHtml() ?>)</label>
                        <input type="text" name="amount" id="amount" value="" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="reference">Reference: (optional)</label>
                        <input type="text" name="reference" id="reference" value="" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="notes">Notes: (optional)</label>
                        <textarea name="notes" id="notes" class="form-control"></textarea>
                    </div>

                    <div class="form-group">
                        <input type="submit" value="Make Payment" class="btn btn-primary">
                    </div>

                </fieldset>

            </form>
        </section>


    </article>
<?php get_footer() ?>