<?php
/**
 * User: leeparsons
 * Date: 24/02/2014
 * Time: 08:09
 */
get_header();

include TPP_STORE_PLUGIN_DIR . 'helpers/paginator.php';

$paginator = new TppStoreHelperPaginator();

$paginator->total_results = $total_messages;

?>
<section class="wrap">
    <header>
        <h1>My Messages</h1>
        <a class="btn btn-primary" href="/shop/<?php echo $user->user_type == 'buyer'?'myaccount':'dashboard' ?>/">&lt; Back to <?php echo $user->user_type == 'buyer'?'my account':'dashboard' ?></a>
        <br><br>
    </header>
    <?php TppStoreMessages::getInstance()->render(); ?>
    <?php if (count($messages) > 0):  ?>
        <div class="wrap text-right">
            <?php echo $paginator->render(); ?>
        </div>
        <table class="dashboard-list">
            <thead>
                <tr>
                    <th>From</th>
                    <th>Message</th>
                    <th>Received</th>
<!--                    <th>Responded</th>-->
                </tr>
            </thead>
            <tbody>
                <?php foreach ($messages as $message): ?>
                    <?php $href = '/shop/myaccount/message/' . $message->message_id; ?>

                    <tr class="message <?php echo str_replace(' ', '-', $message->status) ?>">
                        <td><a href="<?php echo $href ?>"><?php echo $message->getSender()->getName() ?></a></td>
                        <td><a href="<?php echo $href ?>"><?php echo $message->subject ?><span class="shorttext"><?php

                                    echo $message->getSafeMessage(200);

                                    ?></span></a></td>
                        <td><a href="<?php echo $href ?>"><?php echo $message->getReceivedLapseTime() ?></a></td>
<!--                        <td><a href="--><?php //echo $href ?><!--">--><?php //echo $message->status ?><!--</a></td>-->
                        <!--td><a class="btn btn-<?php echo $message->status == 'unread'?'default':'primary' ?>" href="<?php echo $href; ?>">Open</a></td-->
<!--                        <td>-->
<!--                            <span>--><?php
//
//                                echo $message->responded_on == '0000-00-00 00:00:00'?'':$message->getRespondedDate();
//
//                                ?><!--</span>-->
<!--                        </td>-->
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>You have no messages</p>
    <?php endif; ?>

</section>

<?php

get_footer();
