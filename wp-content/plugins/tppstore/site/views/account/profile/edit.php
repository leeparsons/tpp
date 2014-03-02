<?php include TPP_STORE_PLUGIN_DIR . 'site/views/dashboard/header.php'; ?>

<article class="article-page">
    <header>
        <h1>Edit Your Profile</h1>
    </header>

    <?php TppStoreMessages::getInstance()->render() ?>

    <div class="wrap">
        <form method="post" enctype="multipart/form-data">
            <br>
            <fieldset class="notoggle">
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Save">
                    <a href="/shop/dashboard" class="btn btn-default">Cancel</a>
                </div>

            </fieldset>


            <fieldset>
                <legend>Personal Details</legend>
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" class="form-control" name="fname" id="first_name" placeholder="First name" value="<?php echo $user->first_name ?>">
                </div>

                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" class="form-control" name="lname" id="last_name" placeholder="Last name" value="<?php echo $user->last_name ?>">
                </div>

                </fieldset>
                <fieldset>

                <legend>Profile Image</legend>
                    <div class="form-group">

                        <pre>This should be an image of yourself.

The ideal image size is below 500KB.</pre>
                        <div id="dropbox" class="store-dropbox">
                            <div class="drop-wrap">
                                <div class="photo-box">
                                    <div class="handle" style="background:none"></div>
                                    <div class="delete-icon"></div>
                                    <div class="preview">
                                        <?php $src = $user->getSrc(); ?>
                                        <?php if ($src): ?>
                                            <img src="<?php echo $src ?>">
                                            <input type="hidden" name="original_pic[]" value="<?php echo $user->user_src ?>">
                                        <?php else: ?>
                                            <div class="upload-icon"></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="message"></div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="upload_destination" value="/shop/myaccount/profile/upload">

    </fieldset>

            <fieldset>
                <legend>Contact Details</legend>
                <div class="form-group">
                    <label for="email">Email (also used for logging in)</label>
                    <input type="text" class="form-control" name="email" id="email" placeholder="Email" value="<?php echo $user->email ?>">
                </div>

                <div class="form-group">
                    <label for="telephone">Telephone</label>
                    <input type="text" class="form-control" name="telephone" id="telephone" placeholder="Telephone" value="<?php echo $user->telephone ?>">
                </div>

                <div class="form-group">
                    <label for="address">Address (for receipts)</label>
                    <textarea class="form-control" rows="5" name="address" id="address" placeholder="Address"><?php echo $user->address ?></textarea>
                </div>
            </fieldset>

            <fieldset id="change_password">
                <legend>Reset Password</legend>
                <div class="form-group">
                    <label for="password">Enter your new password</label>
                    <input type="password" class="form-control" name="pswd" id="password">
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm your new password</label>
                    <input type="password" class="form-control" name="cpswd" id="confirm_password">
                </div>
            </fieldset>

            <fieldset class="notoggle bt">
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Save">
                    <a href="/shop/dashboard" class="btn btn-default">Cancel</a>
                </div>

            </fieldset>

        </form>
    </div>

</article>
<?php include TPP_STORE_PLUGIN_DIR . 'site/views/dashboard/footer.php';
