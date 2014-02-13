<?php get_header(); ?>

<article class="page-article">
    <header>
        <h1>Ask <?php echo $store->getOwner() ?> a question</h1>
    </header>

    <div class="wrap">
        <form method="post" action="/shop/ask">
            <input type="hidden" value="<?php echo $store->store_id ?>" name="store">
            <div class="form-group">
                <label for="question">Your question</label>
                <textarea rows="5" name="message" placeholder="question" id="question" class="form-control"><?php echo $question->getSafeMessage() ?></textarea>
            </div>

            <div class="form-group">
                <input type="submit" value="Send" class="btn btn-primary align-left">
                &nbsp;
                <a class="btn btn-default" href="<?php echo $store->getPermalink() ?>">Cancel</a>
            </div>

        </form>
    </div>

</article>

<?php get_footer(); ?>