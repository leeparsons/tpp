<legend class="wrap">SEO and Meta</legend>

<div class="form-group" >
    <label>Enter a short description for your product (used on search results and for SEO)</label>
    <br><br>
    <span id="populate_seo" class="btn btn-primary">Generate from the description</span>
    <br><br>
    <textarea name="short_description" id="short_description" class="form-control"><?php echo $product->excerpt ?></textarea>
    <span><span id="short_character_count">150 </span> <span id="short_character_term">characters</span> remaining</span>
</div>
