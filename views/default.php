<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo Flight::get('title_prefix'); ?> - Your IP is: <?php echo Flight::get('ip'); ?></title>
    <meta name="description" content="<?php echo $meta_description; ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" type="image/x-icon" href="favicon.ico" />
    <style>
    </style>
	<?php if($geoinfo && $js){	?>
    <script type="text/javascript">
		window.onload = function() {resizemap();}
		window.onresize = function() {resizemap();}
		function resizemap(){
			var map = document.getElementById("map");
			var container = document.getElementById("container");
			map.height = container.clientHeight - 75;
			map.width = container.clientWidth;
		}
	</script>
	<?php } ?>
	<link rel="stylesheet" href="css/main.css">
    
</head>
<body>
<noscript>Either enable JavaScript or click <a href="?js=0">here</a> </noscript>
<div id="container">
            <div id="toppane">
                <div class="box30">
					<div id="lefttop"><div id="brand"><a href="<?php echo Flight::get('base_url'); ?>"><?php echo Flight::get( "title_prefix"); ?></a></div></div>
					
				</div>
                <div class="box40">
					<div id="centertop"><span id="iplbl">Your IP is: </span><br/><?php echo Flight::get('ip'); ?></div>
				</div>
                <div class="box30">
					<div id="righttop">
						<?php if($geoinfo && $js){	?>
						<div class="tmbutt"><a href="?maptype=here">Here</a></div>
						<div class="tmbutt"><a href="?maptype=wikimapia">Wikimapia</a></div>
						<?php if(Flight::has('google_embed_key')){ ?>
						<div class="tmbutt"><a href="?maptype=google">Google</a></div>
						<?php } ?>
						<?php }else{ ?>
						<div class="tmbutt">
						<?php echo date('M d Y'); ?>
						</div>
						<?php } ?>
					</div>
					
				</div>
				<!--<div style="clear:both;"></div>-->
            </div>
			<?php echo $iframe_insert; ?>
	<div id="bottompane">
		IP <?php echo Flight::get('ip'); ?> =
		<?php echo Flight::get('latitude'); ?> ,
		<?php echo Flight::get('longitude'); ?> - 
		<?php echo Flight::get('city'); ?>, 
		<?php echo Flight::get('region'); ?>, 
		<?php echo Flight::get('countryName'); ?> - 
		Telephone Area Code: <?php echo Flight::get('areaCode'); ?>
		<a class="right" target="_blank" href="http://geoplugin.com/">geoplugin.com</a>
	</div>
</div>
<?php if($js && Flight::has('google_analytics_site_id')){	?>
<script>
    (function(b,o,i,l,e,r){b.GoogleAnalyticsObject=l;b[l]||(b[l]=
        function(){(b[l].q=b[l].q||[]).push(arguments)});b[l].l=+new Date;
        e=o.createElement(i);r=o.getElementsByTagName(i)[0];
        e.src='//www.google-analytics.com/analytics.js';
        r.parentNode.insertBefore(e,r)}(window,document,'script','ga'));
    ga('create','<?php echo Flight::get('google_analytics_site_id'); ?>');ga('send','pageview');
</script>

<?php } ?>
</body>
</html>
