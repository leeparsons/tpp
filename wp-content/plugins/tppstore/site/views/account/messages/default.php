<?php
/**
 * User: leeparsons
 * Date: 24/02/2014
 * Time: 13:26
 */


get_header();

?>
<article class="wrap">

    <?php TppStoreMessages::getInstance()->render(); ?>

    <header>
        <h1>Correspondance with: <?php echo $message->getSender()->first_name . ' ' . $message->getSender()->last_name ?></h1>
        <h1><?php echo $message->subject ?></h1>
        <a class="btn btn-primary" href="/shop/myaccount/messages/">&lt; Back to messages</a>
        <br>
        <br>
    </header>

    <table>
        <tbody>
        <tr>
            <td>From: </td>
            <td><?php echo $message->getSender()->first_name . ' ' . $message->getSender()->last_name ?></td>
        </tr>
        <tr>
            <td>Received:</td>
            <td><?php echo $message->getReceivedDate() ?></td>
        </tr>
        <tr>
            <td>Status:</td>
            <td><?php echo ucwords($message->status) ?></td>
        </tr>
        <tr>
            <td>Message</td>
            <td><?php

                echo $message->getHtmlMessage();

                ?></td>
        </tr>
        </tbody>
    </table>


    <?php if (count($message_history) > 0): ?>
        <a href="#" id="history_toggle">View previous messages</a>
        <div id="history_expander" data-expanded="closed" class="wrap">
        <?php foreach ($message_history as $message_h): ?>
            <table class="message-table wrap <?php echo $message_h->receiver == $user->user_id && $message_h->status =='deleted'?'deleted':'' ?>">
                <tbody>
                <tr class="first">
                    <td class="lft">Received:</td>
                    <td><?php echo $message_h->getReceivedDate() ?></td>
                </tr>
                <tr class="second">
                    <td class="lft">Status:</td>
                    <td><?php

                        if ($message_h->receiver == $user->user_id) {
                            echo ucwords($message_h->status);
                        } else {
                            echo 'Sent';
                        }


                        ?></td>
                </tr>
                <tr>
                    <td class="lft">Message</td>
                    <td><?php

                        echo $message_h->getHtmlMessage();

                        ?></td>
                </tr>
                </tbody>
            </table>
        <?php endforeach; ?>
        </div>
        <script>

            var h = document.getElementById('history_expander').clientHeight;
            document.getElementById('history_expander').setAttribute('data-height', h);
            document.getElementById('history_expander').style.transition = 'height 0.25s ease-in';
            document.getElementById('history_expander').style.height = '0px';
            document.getElementById('history_expander').style.overflow = 'hidden';
            document.getElementById('history_toggle').onclick = function() {
                var h = document.getElementById('history_expander');
                if (h.getAttribute('data-expanded') == 'closed') {
                    h.style.height = h.getAttribute('data-height') + 'px';
                    h.setAttribute('data-expanded', 'open');
                    this.innerHTML = 'Hide previous messages';
                } else {
                    this.innerHTML = 'View previous messages';
                    h.style.height = '0px';
                    h.style.overflow = 'hidden';
                    h.setAttribute('data-expanded', 'closed');
                }
                return false;
            }

        </script>
    <?php endif; ?>


    <div class="wrap">
        <a id="reply" href="#" class="btn btn-primary">Reply</a>
        <a href="/shop/myaccount/messages/" class="btn btn-default">Cancel</a>
        <a id="delete" href="#" class="btn btn-danger">Delete</a>
    </div>



    <form id="respond_form" class="wrap" method="post" style="display:none;" action="/shop/myaccount/message/reply/">

        <br><br>

        <fieldset>
            <legend>Write your response</legend>
        </fieldset>

        <input type="hidden" name="respond_nonce" value="<?php echo md5(AUTH_SALT . $message->message_id . 'respond') ?>">

        <div class="form-group">
            <label for="message">Message</label>
            <textarea class="form-control" name="message" id="message" rows="5"></textarea>
        </div>

        <input type="hidden" name="m" value="<?php echo $message->message_id ?>">

        <div class="form-group">
            <input type="submit" value="Send" class="btn btn-primary">
        </div>

    </form>

    <form id="delete_form" method="post" class="hidden" action="/shop/myaccount/message/delete/">
        <input type="hidden" name="delete_nonce" value="<?php echo md5(AUTH_SALT . $message->message_id . 'delete') ?>">
    </form>

</article>
<script>
    document.getElementById('delete').onclick = function() {
        var c = confirm('Do you really want to delete this message?');

        if (c === true) {
            var inpt = document.createElement('input');
            inpt.setAttribute('type', 'hidden');
            inpt.setAttribute('name', 'm');
            inpt.setAttribute('value', '<?php echo $message->message_id ?>');
            document.getElementById('delete_form').appendChild(inpt);
            document.getElementById('delete_form').submit();
        }
        return false;
    }

    document.getElementById('reply').onclick = function() {
        document.getElementById('respond_form').style.display = 'block';
        return false;
    }
</script>

<?php

get_footer();