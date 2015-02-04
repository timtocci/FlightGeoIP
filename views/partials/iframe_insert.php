<?php if($geoinfo && $js){
		switch ($maptype)
		{
		case 'here':
			$mapurl = "https://www.here.com/?map=" . Flight::get('latitude') . "," . Flight::get('longitude') . ",12,satellite";
			break;
		case 'google':
            if(Flight::has('google_embed_key')){
				$mapurl = "https://www.google.com/maps/embed/v1/view?key=" . Flight::get('google_embed_key') . "&center=" . Flight::get('latitude') . "," . Flight::get('longitude') . "&zoom=12&maptype=satellite";
			} else{
				$mapurl = "http://wikimapia.org/#lang=en&lat=" . Flight::get('latitude') . "&lon=" . Flight::get('longitude') . "&z=12&m=b";
			}
			break;
		case 'wikimapia':
			$mapurl = "http://wikimapia.org/#lang=en&lat=" . Flight::get('latitude') . "&lon=" . Flight::get('longitude') . "&z=12&m=b";
			break;
		default:
			$mapurl = "https://www.google.com/maps/embed/v1/view?key=" . Flight::get('google_embed_key') . "&center=" . Flight::get('latitude') . "," . Flight::get('longitude') . "&zoom=12&maptype=satellite";
		}
?>

			<div id="mappane">
			<iframe id="map" src="<?php echo $mapurl; ?>" frameborder="0"></iframe>	
			</div>
<?php }elseif($geoinfo){  ?>
	<?php
	$heremapurl = "https://www.here.com/?map=" . Flight::get('latitude') . "," . Flight::get('longitude') . ",12,satellite";
	$googlemapurl = "https://www.google.com/maps/@" . Flight::get('latitude') . "," . Flight::get('longitude') . ",12z";
	$wikimapiamapurl = "http://wikimapia.org/#lang=en&lat=" . Flight::get('latitude') . "&lon=" . Flight::get('longitude') . "&z=12&m=b";
	$openstreetmapmapurl = "http://www.openstreetmap.org/#map=12/" . Flight::get('latitude') . "/" . Flight::get('longitude');
	$weathermapurl = "http://www.wunderground.com/wundermap/?lat=" . Flight::get('latitude') . "&lon=" . Flight::get('longitude') . "&zoom=8&pin=" . Flight::get('city') . "%2c%20" . Flight::get('region');
	?>
	<div id="mappane">
		<div class="box50">
			<h3><?php echo Flight::get('ip'); ?></h3>
			<ul class="nsl">
				<li class="nsli"><span class="nsinfo">City: <strong><?php echo Flight::get('city'); ?></strong></span> </li>
				<li class="nsli"><span class="nsinfo">Region: <strong><?php echo Flight::get('region'); ?></strong></span> </li>
				<li class="nsli"><span class="nsinfo">Area Code: <strong><?php echo Flight::get('areaCode'); ?></strong></span> </li>
				<li class="nsli"><span class="nsinfo">DMA Code: <strong><?php echo Flight::get('dmaCode'); ?></strong></span> </li>
				<li class="nsli"><span class="nsinfo">Country Code: <strong><?php echo Flight::get('countryCode'); ?></strong></span> </li>
				<li class="nsli"><span class="nsinfo">Country Name: <strong><?php echo Flight::get('countryName'); ?></strong></span> </li>
				<li class="nsli"><span class="nsinfo">Continent Code: <strong><?php echo Flight::get('continentCode'); ?></strong></span> </li>
				<li class="nsli"><span class="nsinfo">Latitude: <strong><?php echo Flight::get('latitude'); ?></strong></span> </li>
				<li class="nsli"><span class="nsinfo">Longitude: <strong><?php echo Flight::get('longitude'); ?></strong></span> </li>
			</ul>
		</div>
		<div class="box50">
			<h4>View location on one of these map sites:</h4>
			<ul class="nsl">
				<li class="nsli"><a class="nsinfo" href="<?php echo $googlemapurl; ?>" target="_blank">Google Map</a> </li>
				<li class="nsli"><a class="nsinfo" href="<?php echo $heremapurl; ?>" target="_blank">Here Map</a> </li>
				<li class="nsli"><a class="nsinfo" href="<?php echo $wikimapiamapurl; ?>" target="_blank">Wikimapia Map</a> </li>
				<li class="nsli"><a class="nsinfo" href="<?php echo $openstreetmapmapurl; ?>" target="_blank">Open Street Map</a> </li>
			</ul>
			<h4>Weather Map:</h4>
			<ul class="nsl">
				<li class="nsli"><a class="nsinfo" href="<?php echo $weathermapurl; ?>" target="_blank">
					Weather Underground Wundermap
					</a> </li>
			</ul>
		</div>
		<!--<div style="clear:both;"></div>-->
	</div>
<?php }else{  ?>
			<div id="mappane">
				<h2>Unable to retrieve Geographical information at this time. Please try again later.</h2>
				<h3>Our Geographical Information Provider May Not Be Responding</h3>
				<p>If you are connected to the Internet you can try one of the links below to view your IP address location.
				 These Internet map application providers provide a map based on your IP address by default.</p>
				 <ul>
					<li><a href="https://www.google.com/maps/" >google.com</a></li>
					<li><a href="https://www.here.com/" >here.com</a></li>
					<li><a href="https://maps.yahoo.com/" >maps.yahoo.com</a></li>
				 </ul>
			</div>
<?php } ?>	
	
