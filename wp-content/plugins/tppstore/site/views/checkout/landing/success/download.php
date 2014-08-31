<!DOCTYPE html>
<!--[if IE 7 ]>    <html lang="cs" class="ie7 no-js"> <![endif]-->
<!--[if IE 8 ]>    <html lang="cs" class="ie8 no-js"> <![endif]-->
<!--[if IE 9 ]>    <html lang="cs" class="ie9 no-js"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html lang="cs" class="no-js"> <!--<![endif]-->
	<head>
		<meta charset="utf-8" />
		<!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"><![endif]-->
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0" />
		<title>Checkout Success</title>
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway:400,800,700,600,500" />
		<link rel="stylesheet" href="/assets/css/landing-pages/style.css" media="screen, projection" />
		<link rel="stylesheet" href="/assets/css/landing-pages/developers.css" media="screen, projection" />
		<link rel="stylesheet" href="/assets/css/landing-pages/print.css" media="print" />
		<script>document.documentElement.className = document.documentElement.className.replace('no-js', 'js');</script>
		<script src="/assets/js/landing-pages/modernizr.js"></script>
	</head>

	<body>
		<form method="post" action="/shop/landingpage/send_email" id="send" class="content" style="display:none">
			<h3>Email yourself the link</h3>
			<p class="desc">Enter your email address and send the download link to yourself.</p>
			<p class="inp-fix">
				<label for="email" class="vhide">Email</label>
				<input type="email" name="yemail" id="email" placeholder="Your Email Address" class="inp-text"/>
			</p>
			<input type="hidden" name="order" value="<?php echo $order_id ?>">
			<p style="text-align:right">
				<button class="btn" type="submit" onclick="document.getElementById('send').getElementsByClassName('inp-text')[0].value = this.parentNode.parentNode.getElementsByClassName('inp-text')[0].value;document.getElementById('send').submit();">
					<span>
						<span class="underline">Send</span>
					</span>
				</button>
			</p>

			
		</form>
		<header id="header">
			<div class="row-main">
				<div id="logo">SEO for Photographers</div>
				<nav id="menu-main">
					<ul class="reset">
						<li><a href="#download">Your Download</a></li>
					</ul>
				</nav>
			</div>
		</header>

		<section id="main">



			<article class="box-annot">
				<?php TppStoreMessages::getInstance()->render() ?>
				<div class="row-main">
					<div class="col col-h-1 grid-h">
						<p class="center">
							<img src="/assets/images/landing-pages/seoebook.jpg" alt="" class="book" />
						</p>
					</div>
				<div class="col col-h-2 grid-h">
						<h1>Your Download Link:</h1>
						<p class="annot">Thanks for your order, you can download the ebook using the link below for the next 7 days:</p>
						<?php foreach ($order_items as $item): ?>
							<a href="<?php echo $item->getDownloadUrl(true, false, true, array('guest_order_id'	=>	$order->order_id)); ?>" class="btn btn-primary"><span><span class="underline">Download the </span>ebook</span></a>
						<?php endforeach; ?>

						<br><br>

						<a href="#send" class="thickbox btn">
							<span><span class="underline">Send the link </span>to your email >></span>
						</a>

						<p class="annot"><pre>This download link will expire in 7 days</pre></p>

					</div>
				</div>
			</article>


			<div class="box-social" id="contact">
				<div class="row-main">
					<h2>Let’s Get In Touch</h2>
					<p>
						<a href="#" onclick="window.location.href = 'mailto:' + 'parsolee' + '@' + 'gmail' + '.com'";>Email</a>
						<a href="https://www.facebook.com/seoandwebdevelopment" class="facebook">Facebook</a>
						<!-- <a href="#" class="google">Google+</a> -->
						<a href="https://twitter.com/lee_parsons" class="twitter">Twitter</a>
					</p>
				</div>
			</div>

	
		</section>

		
		<footer id="footer">
			<div class="row-main">
				<p class="center">
					Copyright &copy; <?php echo date('Y') ?> The Photography Parlour
				</p>
			</div>
		</footer>
		<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>

		<div id="fb-root"></div>
		<script>(function(d, s, id) {
			var js, fjs = d.getElementsByTagName(s)[0];
			if (d.getElementById(id)) return;
			js = d.createElement(s); js.id = id;
			js.src = "//connect.facebook.net/cs_CZ/all.js#xfbml=1";
			fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));</script>
		<script src="/assets/js/landing-pages/jquery.js"></script>
		<script src="/assets/js/landing-pages/sk/sk.js"></script>
		<script src="/assets/js/landing-pages/sk/sk.widgets.thickbox.js"></script>
		<script src="/assets/js/landing-pages/app.js"></script>
		<script>
			App.run({})
		</script>

	</body>

</html>