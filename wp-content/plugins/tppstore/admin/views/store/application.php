<div class="wrap">

    <div class="icon32 icon32-posts-page" id="icon-edit-pages"><br></div>
    <h2>Application: <?php echo $application->store_name ?></h2>

    <div id="poststuff">
        <form method="POST" action="admin.php">

            <input type="hidden" name="action" value="tpp_save_application">
            <?php wp_nonce_field('save_tpp_store_application', 'save_tpp_application') ?>

            <input type="hidden" name="approve" value="1">
            <input type="hidden" name="store" value="<?php echo $application->store_id ?>">

            <input type="submit" value="Approve" class="button button-primary"> <a class="button button-secondary" href="<?php echo admin_url('admin.php?page=tpp-store-approvals') ?>">Cancel</a>

            <br><br>

            <table class="wp-list-table widefat fixed pages">
                <tr>
                    <td class="post-title page-title column-title"><strong>Application Date:</strong></td>
                    <td class="post-title page-title column-title"><span><?php echo date('jS F, Y', strtotime($application->created_on)); ?></span></td>
                </tr>
            </table>

            <h2>User Details</h2>

            <table class="wp-list-table widefat fixed pages">
                <tr>
                    <td class="post-title page-title column-title"><strong>Name:</strong></td>
                    <td class="post-title page-title column-title"><span><?php echo $user->getName(true) ?></span></td>
                </tr>
                <tr>
                    <td class="post-title page-title column-title"><Strong>Email:</Strong></td>
                    <td class="post-title page-title column-title"><?php echo $user->email ?></td>
                </tr>
                <tr>
                    <td class="post-title page-title column-title"><strong>Website:</strong></td>
                    <td class="post-title page-title column-title"><span><a href="<?php echo $application->website ?>" target="_blank"><?php echo $application->website ?></a></span></td>
                </tr>
                <tr>
                    <td class="post-title page-title column-title"><strong>How you were found:</strong></td>
                    <td class="post-title page-title column-title"><span><?php echo $application->how ?></span></td>
                </tr>
                <tr>
                    <td class="post-title page-title column-title"><strong>Signed up to newsletter:</strong></td>
                    <td class="post-title page-title column-title"><span><?php echo $application->newsletter == 1?'Yes':'No' ?></span></td>
                </tr>
            </table>

            <h2>Store Details</h2>

            <table class="wp-list-table widefat fixed pages">
                <tr>
                    <td class="post-title page-title column-title"><strong>Products:</strong></td>
                    <td class="post-title page-title column-title"><span><?php echo $application->product_count ?></span></td>
                </tr>
                <tr>
                    <td class="post-title page-title column-title"><strong>Approved:</strong></td>
                    <td class="post-title page-title column-title"><img src="/assets/images/<?php



                        if ($store->approved == 1) {
                            echo 'tick.png';
                        } elseif ($store->approved == 0) {
                            echo 'cross.png';
                        } else {
                            echo 'declined.png';
                        }
                        ?>" width="15" height="15"></td>
                </tr>

                <tr>
                    <td class="post-title page-title column-title"><strong>Description:</strong></td>
                    <td class="post-title page-title column-title"><pre><?php echo $store->description ?></pre></td>
                </tr>

                <tr>
                    <td class="post-title page-title column-title"><strong>Country:</strong></td>
                    <td class="post-title page-title column-title"><?php echo $store->country ?></td>
                </tr>

            </table>
            <br><br>

            <input type="submit" value="Approve" class="button button-primary">

            <a href="#" id="decline" class="button button-secondary">Decline</a>

            <a class="button button-cancel" href="<?php echo admin_url('admin.php?page=tpp-store-approvals') ?>">Cancel</a>
            <input type="hidden" name="store" value="<?php echo $store->store_id ?>">
        </form>

        <form id="reason_wrap" style="display:none;" method="POST" action="admin.php">
            <h2>Give a reason</h2>
            <div id="tholder">

             </div>
            <input type="submit" value="Decline" class="button button-primary" id="decline2">
            <input type="hidden" name="decline" value="1">

            <input type="hidden" name="action" value="tpp_save_application">
            <?php wp_nonce_field('save_tpp_store_application', 'save_tpp_application') ?>
            <input type="hidden" name="store" value="<?php echo $store->store_id ?>">
        </form>
    </div>
</div>
<script>
    document.getElementById('decline').onclick = function() {
        if (!document.getElementById('reason')) {
            var t = document.createElement('textarea');
            t.setAttribute('id', 'reason');
            t.setAttribute('name', 'reason');
            t.setAttribute('rows', '10');
            t.setAttribute('cols', '75');
            t.setAttribute('placeholder', 'reason');
            document.getElementById('tholder').appendChild(t);
            document.getElementById('reason_wrap').style.display = 'block';
            window.scrollBy(0,550);
        } else {
            window.scrollBy(0,550);
        }
        return false;
    }
    document.getElementById('decline2').onclick = function() {
        if (document.getElementById('reason').value.replace(/\s/g, '') == '') {
            alert('Please enter a reason');
            return false;
        }
    }



</script>
<?php flush();