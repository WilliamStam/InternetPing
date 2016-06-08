<?php
date_default_timezone_set('Africa/Johannesburg');
setlocale(LC_ALL, 'en_ZA.UTF8');
$errorPath = dirname(ini_get('error_log'));
$errorFile = $errorPath . DIRECTORY_SEPARATOR . basename(__DIR__) . "-errors.log";
ini_set("error_log", $errorFile);

if (session_id() == "") {
	$SID = @session_start();
} else $SID = session_id();
if (!$SID) {
	session_start();
	$SID = session_id();
}
$GLOBALS["output"] = array();
$GLOBALS["models"] = array();
require_once('vendor/autoload.php');

$f3 = \base::instance();
require('inc/timer.php');
require('inc/template.php');
require('inc/functions.php');
$GLOBALS['page_execute_timer'] = new timer(true);
$cfg = array();
require_once('config.default.inc.php');
if (file_exists("config.inc.php")) {
	require_once('config.inc.php');
}

$f3->set('AUTOLOAD', './|lib/|controllers/|inc/|/modules/');
$f3->set('PLUGINS', 'vendor/bcosca/fatfree/lib/');
$f3->set('CACHE', true);

$f3->set('cfg', $cfg);
$f3->set('DEBUG',3);




//$f3->set('QUIET', TRUE);

$f3->set('UI', 'app/|media/');
$f3->set('MEDIA', './media/|'.$cfg['media']);
$f3->set('TZ', 'Africa/Johannesburg');

$f3->set('ONERRORd',
	function($f3) {
		// recursively clear existing output buffers:
		while (ob_get_level())
			ob_end_clean();
		// your fresh page here:
		echo $f3->get('ERROR.text');
		print_r($f3->get('ERROR.stack'));
	}
);

$version = date("YmdH");
if (file_exists("./.git/refs/heads/" . $cfg['git']['branch'])) {
	$version = file_get_contents("./.git/refs/heads/" . $cfg['git']['branch']);
	$version = substr(base_convert(md5($version), 16, 10), -10);
}

$minVersion = preg_replace("/[^0-9]/", "", $version);
$f3->set('_version', $version);
$f3->set('_v', $minVersion);






$f3->route('GET|POST /', 'controllers\home->page');
$f3->route('GET|POST /contact', 'controllers\contact->page');
$f3->route('GET|POST /ab', 'controllers\app_ab->page');
$f3->route('GET|POST /nf', 'controllers\app_nf->page');




$f3->route('POST /contact/send', function ($f3) {
	
	//Email information
	$admin_email = "awstam@gmail.com";
	$email = $_REQUEST['email'];
	$message = $_REQUEST['message'];
	$company = $_REQUEST['company'];
	$name = $_REQUEST['name'];
	
	
	
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	
	
	
	ini_set("sendmail_from", "website@impreshin.com");
	
	$body = "Name: ".$name . "\r\n";
	$body .= "<hr>" . "\r\n";
	$body .= "company: ".$company . "\r\n";
	$body .= "<hr>" . "\r\n";
	$body .= "message: ".$message . "\r\n";
	$body .= "<hr>" . "\r\n";
	$body .= "IP: ".$ip . "\r\n";
	
	$subject = 'Website Contact: '.$company;

	$headers = 'From: Impreshin <website@impreshin.com>' . "\r\n";
	$headers .= "Reply-To: ".$name." <".$email.">\r\n";
	$headers .= 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html;charset=iso-8859-1' . "\r\n";
	$headers .= "X-Priority: 3\r\n";
	$headers .= 'X-Mailer: PHP/' . phpversion(). "\r\n";
	$headers .= 'Delivery-Date: ' . date("r") . "\r\n";
	$headers .= "Organization: Impreshin\r\n";
			
			
			
//$headers .= 'Message-Id: <20140316055950.DA8ED58A13CE@myserver.com>' . "\r\n";
	
	
	
	
	mail($admin_email, $subject, $body, $headers, "-f website@impreshin.com");
	//mail("example@gmail.com", $subject, $body, $headers, "-f website@impreshin.com");
	
	
	
	
	
	
	
	
	//Email response
	$f3->reroute("/contact?msg=Message+submitted.+We+will+be+in+touch.");

}
);




$f3->route("GET /image/@width/@height/*", function ($f3, $params) {
	$path=$_SERVER['REQUEST_URI'];
	
	
	$crop = false;
	$enlarge = false;
	$width = $params['width'];
	$height = $params['height'];
	
	
	$img_path = str_replace("/image/{$width}/{$height}/","" ,$path );
		
	$fileexisits = false;
	$fileType = "";
	if (file_exists($img_path)){
		$fileexisits = true;
	
		$fileT = new \Web();
		$fileType = $fileT->mime($img_path);
		//test_array($fileType); 
		
		
		
		
		
		header('Content-Type: '.$fileType);
		header('Accept-Ranges: bytes');
	//	header('Content-Length: '.$size=filesize($img_path));
		header('Cache-control: max-age='.(60*60*24*365));
		header('Expires: '.gmdate(DATE_RFC1123,time()+60*60*24*365));
		header('Last-Modified: '.gmdate(DATE_RFC1123,filemtime($img_path)));
		
		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
			header('HTTP/1.1 304 Not Modified');
			die();
		}
		
		
		
		
		
		
		
		
		$img = new \Image($img_path);
		//$img->load();
		$img->resize($width, $height,$crop,$enlarge);
		
		$img->render(str_replace("image/", "", $fileType));
		exit();
		
	} else {
		
		$f3->error("404");
	}
	
			
	
	
	
});






$f3->route("GET|POST /media/@catID/@filename", function ($app, $params) {
	$cfg = $app->get("cfg");
	$path = $cfg['media'];
	$file = $path.$params['catID'].DIRECTORY_SEPARATOR.$params['filename'];
	//test_string($file); 
	
	if (file_exists($file)){
		
		
		$o = new \Web();
		header('Content-Type: '.$o->mime($file));
		header('Accept-Ranges: bytes');
		header('Content-Length: '.$size=filesize($file));
		header('Cache-control: max-age='.(60*60*24*365));
		header('Expires: '.gmdate(DATE_RFC1123,time()+60*60*24*365));
		header('Last-Modified: '.gmdate(DATE_RFC1123,filemtime($file)));
		
		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
			header('HTTP/1.1 304 Not Modified');
			die();
		}
		
		echo readfile($file);
		exit();
	} else {
		$app->error(404);
	}
});















$f3->route('GET /php', function () {
	phpinfo();
	exit();
});

$f3->run();



	

$models = $GLOBALS['models'];

///test_array($models); 
$t = array();
foreach ($models as $model) {
	$c = array();
	foreach ($model['m'] as $method) {
		$c[] = $method;
	}
	$model['m'] = $c;
	$t[] = $model;
}

//test_array($t); 

$models = $t;
$pageTime = $GLOBALS['page_execute_timer']->stop("Page Execute");

$GLOBALS["output"]['timer'] = $GLOBALS['timer'];

$GLOBALS["output"]['models'] = $models;



$GLOBALS["output"]['page'] = array(
	"page" => $_SERVER['REQUEST_URI'],
	"time" => $pageTime
);

//test_array($tt); 

if ($f3->get("ERROR")){
	exit();
}

if (($f3->get("AJAX") && ($f3->get("__runTemplate")==false) || $f3->get("__runJSON"))) {
	header("Content-Type: application/json");
	echo json_encode($GLOBALS["output"]);
} else {

	//if (strpos())
	if ($f3->get("NOTIMERS")){
		exit();
	}
	
	
}



?>
