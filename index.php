<?php
date_default_timezone_set('UTC');
// http://flightphp.com/
require 'flight/Flight.php';
// http://www.geoplugin.com/webservices/php
// require 'includes/geoplugin.class.php';
// http://www.phpclasses.org/package/5082-PHP-Determine-the-MIME-type-of-a-file.html
// require 'includes/phpmimetypeclass-2010-10-19/class.mime.php';

// http://firephp.org/
//require 'includes/FirePHP.class.php';
//Flight::set('isdebug', false);
//$isdebug = Flight::get('isdebug');
//if($isdebug){$firephp = FirePHP::getInstance(true);}
//if($isdebug){$firephp->log();}

// Set framework variables
// Override the base url of the request. (default: null)
Flight::set('flight.base_url', null);
// Allow Flight to handle all errors internally. (default: true)
Flight::set('flight.handle_errors', true);
// Log errors to the web server's error log file. (default: false)
Flight::set('flight.log_errors', true);
// Directory containing view template files (default: ./views)
Flight::set('flight.views.path', 'views');
// set your own variables now
Flight::set('flight.static.path', 'static');
// use js? (include maps and scripts in other words)
Flight::set('js', false);
Flight::set('title_prefix', 'WhatsMyIPSite');
Flight::set('meta_description', 'WhatsMyIPSite tells visitors what IP address they are making requests from.');
// set default maptype here (google, here, or wikimapia)
Flight::set('maptype', 'here');
// Google Maps Embed API key
// https://developers.google.com/maps/documentation/embed/guide#api_key
// Flight::set('google_embed_key', 'YOUR_GOOGLE_MAP_EMBED_KEY');
// Google Analytics - replace UA-XXXXX-X with your site id
//Flight::set('google_analytics_site_id', 'UA-XXXXX-X');

// Now do classes
// Register db class with constructor parameters
Flight::register('db', 'PDO', array('sqlite:data/flightgeoip.sqlite3'), function($db){
    // after construct callback (with new object passed in)
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // now create tables if needed
    $db->exec('CREATE TABLE IF NOT EXISTS geo (
                    id INTEGER PRIMARY KEY,
                    ip TEXT UNIQUE,
                    geoplugin_city TEXT,
                    geoplugin_region TEXT,
                    geoplugin_areaCode TEXT,
                    geoplugin_dmaCode TEXT,
                    geoplugin_countryCode TEXT,
                    geoplugin_countryName TEXT,
                    geoplugin_continentCode TEXT,
                    geoplugin_latitude TEXT,
                    geoplugin_longitude TEXT,
                    geoplugin_regionCode TEXT,
                    geoplugin_regionName TEXT)');
	
    $db->exec('CREATE TABLE IF NOT EXISTS requests (
                    id INTEGER PRIMARY KEY,
                    path TEXT,
                    ip TEXT,
                    uastring TEXT,
                    time INTEGER)');
});
//$db = Flight::db();


// Now extend Flight with your methods
/**
 * getStaticFile
 * Returns a static file in the static directory or 404.
 * @param [string] $filepath - path to the static file
 */
Flight::map('getStaticFile', function($req_path){
	$req_path = parse_url($req_path, PHP_URL_PATH);
    $filepath = Flight::get('flight.static.path') . $req_path;
	// sometimes relative to root not folder
	if(!realpath($filepath)){
		$server_path = realpath($_SERVER['DOCUMENT_ROOT']);
		$this_path = realpath(dirname(__FILE__));
		if (strlen($this_path) > strlen($server_path)) {
			$tmp = substr($this_path, strlen($server_path));
			$filepath = Flight::get('flight.static.path') . substr($req_path, strlen($tmp));
		}
	}
    if (file_exists($filepath)) {
		// we don't need MIME id until we need it
		require_once 'includes/phpmimetypeclass-2010-10-19/class.mime.php';
		$mime = new MIMETypes('includes/phpmimetypeclass-2010-10-19/class.types.php');
        $mimetype = $mime->getMimeType($filepath);
        header("Content-Type: $mimetype", true);
        readfile($filepath);
    } else {
        // 404 it
        Flight::notFound();
    }
});
/**
 * checkIP
 * Checks the ip of the request for geolocation
 * information, if none is found the information is requested.
 * @param [string] $ip - request IP
 */
Flight::map('checkIP', function($ip){
    // query db for ip first
    $db = Flight::db();
	// If localhost set a real IP
	// live hosting should erase this below!
	if($ip === '::1'){
		$ip = '24.105.250.46';
	}
    $dbquery ="SELECT * FROM geo WHERE ip IS '" . $ip . "'";
    $result = $db->query($dbquery);
	// does it exist in db?
    if($result->fetch(PDO::FETCH_NUM) > 0){
		$result = $db->query($dbquery);
		foreach ($result as $row) {
			// set geo information into globals
			if(strlen($row['ip']) > 0){Flight::set('ip', $row['ip']);}
			if(strlen($row['geoplugin_city']) > 0){Flight::set('city', $row['geoplugin_city']);}
			if(strlen($row['geoplugin_region']) > 0){Flight::set('region', $row['geoplugin_region']);}
			if(strlen($row['geoplugin_areaCode']) > 0){Flight::set('areaCode', $row['geoplugin_areaCode']);}
			if(strlen($row['geoplugin_dmaCode']) > 0){Flight::set('dmaCode', $row['geoplugin_dmaCode']);}
			if(strlen($row['geoplugin_countryCode']) > 0){Flight::set('countryCode', $row['geoplugin_countryCode']);}
			if(strlen($row['geoplugin_countryName']) > 0){Flight::set('countryName', $row['geoplugin_countryName']);}
			if(strlen($row['geoplugin_continentCode']) > 0){Flight::set('continentCode', $row['geoplugin_continentCode']);}
			if(strlen($row['geoplugin_latitude']) > 0){Flight::set('latitude', $row['geoplugin_latitude']);}
			if(strlen($row['geoplugin_longitude']) > 0){Flight::set('longitude', $row['geoplugin_longitude']);}
			if(strlen($row['geoplugin_regionCode']) > 0){Flight::set('regionCode', $row['geoplugin_regionCode']);}
			if(strlen($row['geoplugin_regionName']) > 0){Flight::set('regionName', $row['geoplugin_regionName']);}
		}
		Flight::set('geoinfo', true);
    }else{
		require 'includes/geoplugin.class.php';
        $geoplugin = new geoPlugin();
		$geoplugin->locate($ip);
		// geoplugin.com API up?
        if( strlen($geoplugin->ip ) > 0){
			$ipquery = "INSERT INTO geo (ip, geoplugin_city, geoplugin_region, geoplugin_areaCode, geoplugin_dmaCode, geoplugin_countryCode, geoplugin_countryName, geoplugin_continentCode, geoplugin_latitude, geoplugin_longitude, geoplugin_regionCode, geoplugin_regionName) VALUES(:ip, :geoplugin_city, :geoplugin_region, :geoplugin_areaCode, :geoplugin_dmaCode, :geoplugin_countryCode, :geoplugin_countryName, :geoplugin_continentCode, :geoplugin_latitude, :geoplugin_longitude, :geoplugin_regionCode, :geoplugin_regionName)";
			$stmt = $db->prepare($ipquery);
			$stmt->bindParam(':ip', $geoplugin->ip);
			$stmt->bindParam(':geoplugin_city', $geoplugin->city);
			$stmt->bindParam(':geoplugin_region', $geoplugin->region);
			$stmt->bindParam(':geoplugin_areaCode', $geoplugin->areaCode);
			$stmt->bindParam(':geoplugin_dmaCode', $geoplugin->dmaCode);
			$stmt->bindParam(':geoplugin_countryCode', $geoplugin->countryCode);
			$stmt->bindParam(':geoplugin_countryName', $geoplugin->countryName);
			$stmt->bindParam(':geoplugin_continentCode', $geoplugin->continentCode);
			$stmt->bindParam(':geoplugin_latitude', $geoplugin->latitude);
			$stmt->bindParam(':geoplugin_longitude', $geoplugin->longitude);
			$stmt->bindParam(':geoplugin_regionCode', $geoplugin->regionCode);
			$stmt->bindParam(':geoplugin_regionName', $geoplugin->regionName);
			$stmt->execute();
			// globals
			if(strlen($geoplugin->ip) > 0){Flight::set('ip', $geoplugin->ip);}
			if(strlen($geoplugin->city) > 0){Flight::set('city', $geoplugin->city);}
			if(strlen($geoplugin->region) > 0){Flight::set('region', $geoplugin->region);}
			if(strlen($geoplugin->areaCode) > 0){Flight::set('areaCode', $geoplugin->areaCode);}
			if(strlen($geoplugin->dmaCode) > 0){Flight::set('dmaCode', $geoplugin->dmaCode);}
			if(strlen($geoplugin->countryCode) > 0){Flight::set('countryCode', $geoplugin->countryCode);}
			if(strlen($geoplugin->countryName) > 0){Flight::set('countryName', $geoplugin->countryName);}
			if(strlen($geoplugin->continentCode) > 0){Flight::set('continentCode', $geoplugin->continentCode);}
			if(strlen($geoplugin->latitude) > 0){Flight::set('latitude', $geoplugin->latitude);}
			if(strlen($geoplugin->longitude) > 0){Flight::set('longitude', $geoplugin->longitude);}
			if(strlen($geoplugin->regionCode) > 0){Flight::set('regionCode', $geoplugin->regionCode);}
			if(strlen($geoplugin->regionName) > 0){Flight::set('regionName', $geoplugin->regionName);}
			Flight::set('geoinfo', true);
		}else{
			// geoplugin.com not responding
			Flight::set('geoinfo', false);
		}
    }
});

/**
 * notFound
 * This handles the 404 file not found.
 * Simply loads a custom 404 page.
 
Flight::map('notFound', function(){
	header("Status: 404", true); 
	header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true); 
    Flight::render('404.php');
});
*/
/**
 * This acts as middleware of sorts
 * If your route function returns true
 * execution is passed on to the next
 * matching route function.
 */
Flight::route('*', function(){
    $request = Flight::request();
    $db = Flight::db();
    $insert = "INSERT INTO requests (path, ip, uastring, time)
                VALUES (:path, :ip, :uastring, :time)";
    $stmt = $db->prepare($insert);
    $stmt->bindParam(':path', $request->url);
    $stmt->bindParam(':ip', $request->ip);
    $stmt->bindParam(':uastring', $request->user_agent);
    $stmt->bindParam(':time', time());
    // stuff hit into db
    $stmt->execute();
    Flight::checkIP($request->ip);
    // return true to pass execution
    return true;
});
// Now define your routes and handlers

//front page
Flight::route('GET /', function(){
	$templatedata = array();
	$request = Flight::request();
	// redundant as Flight::get is available in template too 
	$templatedata['ip'] = $request->ip;
	$templatedata['geoinfo'] = Flight::get('geoinfo');
	$templatedata['meta_description'] = Flight::get('meta_description');
	// evaluate the query string (if any)
	if(isset($request->query->js)){
		$templatedata['js'] = $request->query->js;
	}else{
		$templatedata['js'] = Flight::get('js');
	}
	if(isset($request->query->maptype)){
		$templatedata['maptype'] = $request->query->maptype;
	}else{
		$templatedata['maptype'] = Flight::get('maptype');
	}
	Flight::render('partials/iframe_insert.php', $templatedata,
										'iframe_insert');
    Flight::render('default.php', $templatedata);
});

// place any other dynamic routes here

// everything else (static folder)
Flight::route('GET /*', function(){
    $fpath = Flight::request()->url;
    Flight::getStaticFile($fpath);
});
// Now Set It Off!
Flight::start();
