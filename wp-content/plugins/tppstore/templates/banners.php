<section class="header-slideshow" id="header_slideshow">
    <ul>
        <?php if (count($banners) > 0): ?>
            <?php foreach ($banners as $i => $banner): ?>
            <li <?php echo $i > 0 ?'style="display:none"':'' ?>><a href="<?php echo $banner->getPermalink() ?>" class="img" style="background-image:url(<?php echo $banner->getSrc() ?>)"></a></li>
            <?php endforeach; ?>
        <?php else: ?>
        <li><a href="/shop/taylor-barnes-photography/product/illuminated-masterclass-in-fine-art-photography-and-styling/" class="img" style="background-image:url(/assets/images/homepage/homepage-illuminated-masterclass.jpg)"></a></li>
        <li style="display:none"><a href="/?sf=1&s=debs+ivelja" class="img" style="background-image:url(/assets/images/homepage/homepage-banner-debs-ivelja.jpg)"></a></li>
        <li style="display:none"><a href="/shop/category/marketing/" class="img" style="display:none;background-image:url(/assets/images/homepage/homepage-banner-marketing.jpg)"></a></li>
        <li style="display:none"><a href="/shop/category/mentors/" class="img" style="display:none;background-image:url(/assets/images/homepage/homepage-banner-mentors.jpg)"></a></li>
        <?php /*
 <li><a href="/?sf=1&s=dasha+caffrey" class="img" style="display:none;background-image:url(/assets/images/homepage/homepage-banner-dasha-caffrey-3.jpg)"></a></li>
 */ ?>
        <li style="display:none"><a href="/?sf=1&s=taylor%20barnes" class="img" style="display:none;background-image:url(/assets/images/homepage/homepage-banner-ashlee.jpg)"></a></li>
        <?php endif; ?>
    </ul>
</section>