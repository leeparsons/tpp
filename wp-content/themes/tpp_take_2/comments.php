<div class="comments-form wrap">
    <?php comment_form(array(
        'title_reply'           =>  'Add your comment',
        'comment_notes_before'  =>  '',
        'comment_notes_after'   =>  '',
        'fields'                =>  '',
        'comment_field'         =>  '<p class="comment-form-comment"><label for="comment">' . _x( 'Comment', 'noun' ) . '</label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true" class="form-control" placeholder="Comment"></textarea></p>',
        'fields'                =>  array(
            'author' => '<p class="comment-form-author">' . '<label for="author">' . __( 'Name' ) . ( $req ? ' <span class="required">*</span>' : '' ) . '</label> ' .
                '<input class="form-control" placeholder="Your Name" id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' /></p>',
            'email'  => '<p class="comment-form-email"><label for="email">' . __( 'Email' ) . ( $req ? ' <span class="required">*</span>' : '' ) . '</label> ' .
                '<input class="form-control" placeholder="Email" id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' /></p>',
            'url'    => ''
        ),
        'label_submit'          =>  'Comment'
    )) ?>
    <script>document.getElementById('submit').setAttribute('class', 'btn btn-primary align-right');</script>
</div>
<div id="comments" class="wrap">
<?php if (have_comments()): ?>

    <header class="wrap">
        <div class="blog-divider-top"></div>
        <h3>Comments</h3>
        <div class="blog-divider-bottom"></div>
    </header>

    <ul class="comments-list wrap">

        <?php wp_list_comments( array( 'callback' => 'tpp_comment' ) ); ?>

    </ul>


<?php endif; ?></div>