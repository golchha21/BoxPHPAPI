<?php
	require_once( 'library/BoxAPI.class.php' );
?>
<!DOCTYPE html>
<html lang="en">

	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title><?php echo _CODENAME; ?> v <?php echo _VERSION; ?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="keywords" content="<?php echo _CODENAME; ?>, Webmaster, ToolKit, GitHub Project, Ulhas Vardhan Golchha">
		<meta name="description" content="Unofficial BoxPHPAPI v2 based on the API documentations.">
		<meta name="author" content="Ulhas Vardhan Golchha">
		
		<link href="https://plus.google.com/100459034318170995980" rel="author">
		<meta property="fb:admins" content="619145803">
		<meta property="fb:profile_id" content="619145803">
		<meta content="mid:35579902" name="linkedin:owner">

		<meta property="og:title" content="<?php echo _CODENAME; ?> v <?php echo _VERSION; ?> - Demo">
		<meta property="og:type" content="website">
		<meta property="og:url" content="http://www.ulhas.net/GitHub/<?php echo _CODENAME; ?>">
		<meta property="og:image" content="http://www.ulhas.net/GitHub/<?php echo _CODENAME; ?>/images/chart.png">
		<meta property="og:description" content="<?php echo _CODENAME; ?> - Unofficial BoxPHPAPI v2 based on the API documentations.">
		
		<meta name="twitter:card" content="summary">
		<meta name="twitter:url" content="http://www.ulhas.net/GitHub/<?php echo _CODENAME; ?>">
		<meta name="twitter:title" content="<?php echo _CODENAME; ?>">
		<meta name="twitter:description" content="<?php echo _CODENAME; ?> - Unofficial BoxPHPAPI v2 based on the API documentations.">
		<meta name="twitter:image" content="http://www.ulhas.net/GitHub/<?php echo _CODENAME; ?>/images/chart.png">
		<meta name="twitter:site" content="@golchha21">
		<meta name="twitter:creator" content="@golchha21">
		
		<!--[if lt IE 9]>
		<script src="https://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		<link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
		<link rel="stylesheet" href="css/bootstrap-responsive.min.css" type="text/css">
		<link rel="stylesheet" href="css/bootstrap-extended.min.css" type="text/css">	
	</head>

	<body data-spy="scroll">

		<div class="navbar navbar-fixed-top">
			<div class="navbar-inner">
				<div class="container">
					<a class="brand" href="./" onclick="_gaq.push(['_trackEvent', '<?php echo _CODENAME; ?> Navigation actions', '<?php echo _CODENAME; ?> Navigation links', '<?php echo _CODENAME; ?> Logo']);"><?php echo _CODENAME; ?></a>
					<ul class="social_icons pull-right">
						<li class="facebook"><a data-original-title="Find me on Facebook" href="https://www.facebook.com/golchha21" rel="tooltip" target="_blank" onclick="_gaq.push(['_trackEvent', '<?php echo _CODENAME; ?> Navigation actions', 'Social links', 'Facebook']);">Facebook<span></span></a></li>
						<li class="twitter"><a data-original-title="Follow me on Twitter" href="https://www.twitter.com/golchha21" rel="tooltip" target="_blank" onclick="_gaq.push(['_trackEvent', '<?php echo _CODENAME; ?> Navigation actions', 'Social links', 'Twitter']);">Twitter<span></span></a></li>
						<li class="googleplus"><a data-original-title="Follow me on Google+" href="https://plus.google.com/100459034318170995980" rel="tooltip" target="_blank" onclick="_gaq.push(['_trackEvent', '<?php echo _CODENAME; ?> Navigation actions', 'Social links', 'Google+']);">Google+<span></span></a></li>
						<li class="linkedin"><a data-original-title="Follow me on LinkedIn" href="http://www.linkedin.com/in/golchha21" rel="tooltip" target="_blank" onclick="_gaq.push(['_trackEvent', '<?php echo _CODENAME; ?> Navigation actions', 'Social links', 'LinkedIn']);">LinkedIn+<span></span></a></li>
						<li class="github"><a data-original-title="Follow me on GitHub" href="https://github.com/golchha21" rel="tooltip" target="_blank" onclick="_gaq.push(['_trackEvent', '<?php echo _CODENAME; ?> Navigation actions', 'Social links', 'GitHub']);">GitHub<span></span></a></li>
					</ul>
				</div>
			</div>
		</div>

		<div class="container">
			<section>
				<div class="row">
					<div class="span12">
						<div class="page-header">
							<h1><?php echo _CODENAME; ?><small>v <?php echo _VERSION; ?></small><small><a href="<?php echo _URL; ?>" target="_blank" onclick="_gaq.push(['_trackEvent', '<?php echo _CODENAME; ?> Body actions', '<?php echo _CODENAME; ?> Body links', 'GitHub Project']);" title="<?php echo _CODENAME; ?>">GitHub</a></small></h1>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="span12">
						<div class="page-header">
							<h3>Usage</h3>
						</div>
						<pre class="prettyprint linenums">&lt;?php
						
	include('library/BoxAPI.class.php');

	$client_id	= 'CLIENT ID';
	$client_secret 	= 'CLIENT SECRET';
	$redirect_uri 	= 'REDIRECT URL';
	
	$box = new Box_API($client_id, $client_secret, $redirect_uri);
	
	if(!$box->load_token()){
		if(isset($_GET['code'])){
			$token = $box->get_token($_GET['code'], true);
			if($box->write_token($token, 'file')){
				$box->load_token();
			}
		} else {
			$box->get_code();
		}
	}
	
	// User details
	$box->get_user();
	
	// Get folder details
	$box->get_folder_details('FOLDER ID');

	// Get folder items list
	$box->get_folder_items('FOLDER ID');
	
	// All folders in particular folder
	$box->get_folders('FOLDER ID');
	
	// All Files in a particular folder
	$box->get_files('FOLDER ID');
	
	// Get folder collaborators list
	$box->get_folder_collaborators('FOLDER ID');
	
	// Create folder
	$box->create_folder('FOLDER NAME', 'PARENT FOLDER ID');
	
	// Update folder details
	$details['name'] = 'NEW FOLDER NAME';
	$box->update_folder('FOLDER ID', $details);
	
	// Delete folder
	$opts['recursive'] = 'true';
	$box->delete_folder('FOLDER ID', $opts);
	
	// Get file details
	$box->get_file_details('FILE ID');
	
	// Upload file
	$box->put_file('RELATIVE FILE URL', '0');
	
	// Update file details
	$details['name'] = 'NEW FILE NAME';
	$details['description'] = 'NEW DESCRIPTION FOR THE FILE';
	$box->update_file('FILE ID', $details);
	
	// Delete file
	$box->delete_file('FILE ID');
	
	if (isset($box->error)){
		echo $box->error . "\n";
	}
?&gt;</pre>
					</div>
				</div>
			</section>

			<footer class="footer">
				<div class="row">
					<div class="span12">
						<div class="social">
							<ul class="social-buttons">
								<li><iframe class="github-btn" src="github-btn.html?user=golchha21&amp;repo=<?php echo _CODENAME; ?>&amp;type=watch&amp;count=true" allowtransparency="true" frameborder="0" height="20px" scrolling="0" width="100px"></iframe></li>
								<li><iframe class="github-btn" src="github-btn.html?user=golchha21&amp;repo=<?php echo _CODENAME; ?>&amp;type=fork&amp;count=true" allowtransparency="true" frameborder="0" height="20px" scrolling="0" width="102px"></iframe></li>
								<li><iframe class="github-btn" src="github-btn.html?user=golchha21&type=follow&count=true" allowtransparency="true" frameborder="0" scrolling="0" width="165" height="20"></iframe></li>
								<li><a href="https://twitter.com/golchha21" class="twitter-follow-button" data-link-color="#0069D6" data-show-count="true">Follow @golchha21</a></li>
								<li><a href="https://twitter.com/share" class="twitter-share-button" data-url="<?php echo _URL; ?>" data-text="Unofficial BoxPHPAPI v2 based on the API documentations." data-via="golchha21">Tweet</a></iframe></li>
								<li><span class="g-plusone" data-size="tall" data-annotation="inline" data-width="120" data-href="<?php echo _URL; ?>"></span></li>
								<li><span class="fb-like" data-href="<?php echo _URL; ?>" data-send="true" data-layout="button_count" data-width="150" data-show-faces="false"></span></li>
							</ul>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="span6">
						<div class="page-header">
							<h4>Credits: </h4>
						</div>
						<p>Developed by <a href="http://www.ulhas.net" target="_blank" onclick="_gaq.push(['_trackEvent', '<?php echo _CODENAME; ?> Footer actions', 'Social links', 'Developed by Ulhas Vardhan Golchha']);">Ulhas Vardhan Golchha</a>.<br>Code licensed under the <a href="http://www.apache.org/licenses/LICENSE-2.0" target="_blank" onclick="_gaq.push(['_trackEvent', '<?php echo _CODENAME; ?> Footer actions', 'Social links', 'Apache License v2.0']);">Apache License v2.0</a>.</p>
					</div>
					<div class="span3">
						<div class="page-header">
							<h4>Some links: </h4>
						</div>
						<ul>
							<li><a href="<?php echo _URL; ?>/blob/master/CHANGELOG.md" target="_blank" onclick="_gaq.push(['_trackEvent', '<?php echo _CODENAME; ?> Footer actions', '<?php echo _CODENAME; ?> Footer links', '<?php echo _CODENAME; ?> Changelog']);">Changelog</a></li>
							<li><a href="<?php echo _URL; ?>/blob/master/LICENSE.md" target="_blank" onclick="_gaq.push(['_trackEvent', '<?php echo _CODENAME; ?> Footer actions', '<?php echo _CODENAME; ?> Footer links', '<?php echo _CODENAME; ?> License']);">License</a></li>
							<li><a href="<?php echo _URL; ?>/issues" target="_blank" onclick="_gaq.push(['_trackEvent', '<?php echo _CODENAME; ?> Footer actions', '<?php echo _CODENAME; ?> Footer links', '<?php echo _CODENAME; ?> Feedback/Issues']);">Feedback/Issues</a></li>
						</ul>
					</div>
					<div class="span3">
						<div class="page-header">
							<h4>Repositories: </h4>
						</div>
						<ul>
							<li><a href="https://github.com/golchha21/PhotoshelterAPI" target="_blank" onclick="_gaq.push(['_trackEvent', '<?php echo _CODENAME; ?> Footer actions', '<?php echo _CODENAME; ?> Footer links', 'PhotoshelterAPI']);">PhotoshelterAPI</a></li>
							<li><a href="https://github.com/golchha21/TheToolSet" target="_blank" onclick="_gaq.push(['_trackEvent', '<?php echo _CODENAME; ?> Footer actions', '<?php echo _CODENAME; ?> Footer links', 'TheToolSet']);">TheToolSet</a></li>
							<li><a href="https://github.com/golchha21/BootswatchDownloader" target="_blank" onclick="_gaq.push(['_trackEvent', '<?php echo _CODENAME; ?> Footer actions', '<?php echo _CODENAME; ?> Footer links', 'BootswatchDownloader']);">BootswatchDownloader</a></li>
							<li><a href="https://github.com/golchha21/<?php echo _CODENAME; ?>" target="_blank" onclick="_gaq.push(['_trackEvent', '<?php echo _CODENAME; ?> Footer actions', '<?php echo _CODENAME; ?> Footer links', '<?php echo _CODENAME; ?>']);"><?php echo _CODENAME; ?></a></li>
						</ul>
					</div>
				</div>
				<hr>
				<div class="row">
					<div class="span12">
						<p class="copyright pull-left">Copyright &copy; 2013, <a href="http://www.ulhas.net/" id="credittuamc" target="_blank" onclick="_gaq.push(['_trackEvent', '<?php echo _CODENAME; ?> Footer actions', '<?php echo _CODENAME; ?> Footer links', 'Copyright']);" title="Ulhas Vardhan Golchha">Ulhas Vardhan Golchha</a>. All rights reserved.</p>
						<p class="pull-right"><a href="#top">Back to top</a></p>
					</div>
				</div>
			</footer>

		</div>
		
		<script src='js/jquery.js'></script>
		<script src='js/bootstrap.min.js'></script>
		<script src='js/jquery.smooth-scroll.min.js'></script>
		<script src='js/social.tracker.js'></script>
		<script src='js/prettify.js'></script>
		<script src='js/scripts.js'></script>
		<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
		<script type='text/javascript'>
			var _gaq = _gaq || [];
			var pluginUrl = '//www.google-analytics.com/plugins/ga/inpage_linkid.js';
			_gaq.push(['_require', 'inpage_linkid', pluginUrl]);
			_gaq.push(['_setAccount', 'UA-5397392-2']);
			_gaq.push(['_setDomainName', 'ulhas.net']);
			_gaq.push(['_setAllowLinker', true]);
			_gaq.push(['_trackPageview']);

			(function() {
				var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
				ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
				var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
			})();

			(function(d, s, id) {
				var js, fjs = d.getElementsByTagName(s)[0];
				if (d.getElementById(id)) return;
				js = d.createElement(s); js.id = id;
				js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=220974161291591";
				fjs.parentNode.insertBefore(js, fjs);
			}(document, 'script', 'facebook-jssdk'));
	
			(function() {
				var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
				po.src = 'https://apis.google.com/js/plusone.js';
				var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
			})();
		  
		  _ga.trackTwitter();
		  _ga.trackFacebook();
		</script>
	</body>
</html>