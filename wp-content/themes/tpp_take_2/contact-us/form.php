<div id="contact_us_response"><?php

    TppContactUs::getInstance()->renderMessages();

    ?></div>
<form method="post" id="contact_form">

    <div class="form-group">
        <label for="cu_name">Name</label>
        <input type="text" name="cu_name" id="cu_name" placeholder="Name" class="form-control" value="<?php echo $this->countErrors() > 0?$this->name:'' ?>">
    </div>


    <div class="form-group">
        <label for="cu_email">Email Address</label>
        <input value="<?php echo $this->countErrors() > 0?$this->email:'' ?>" type="text" name="cu_email" id="cu_email" placeholder="Email Address" value="" class="form-control">
    </div>

    <div class="form-group">
        <label for="cu_message">Message</label>
        <textarea rows="5" cols="27" name="cu_message" id="cu_message" class="form-control" placeHolder="How can we help?"><?php echo $this->countErrors() > 0?$this->body:'' ?></textarea>
    </div>

    <div class="form-group">
        <input type="submit" value="Send" class="btn btn-primary">
    </div>

    <div style="position:absolute;left:-10000px">
        <?php //catch bots ?>
        <input type="text" name="cu_url">
    </div>

    <?php wp_nonce_field('contact_submission', 'contact_submission') ?>
    <input type="hidden" name="action" value="contact_submission">

</form>
 