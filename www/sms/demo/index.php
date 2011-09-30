<?php

error_reporting(0);

//Cookie code
//session_start();
//$numberarray = $_SESSION['numberarray'];

//if(!strlen($numberarray)) {
//	$numberarray = array();
//}

include "../../lib/RealtimeData.php";

//$_REQUEST['Body'] = ' #51099 hello ';

$body = trim($_REQUEST['Body']);
$from = trim($_REQUEST['From']);
preg_match("/[0-9]{5}/", $body, $matches);

if (!empty( $matches[0] )) {
    $action = 'realtime';
}

switch($action) {

    case "realtime":
        $RT     = new RealtimeData();
        $msg    = formatMsg( $RT->getRealtimeArrival('actransit', $matches[0] ) );
        break;

    default:
        $msg = "Please enter your bus stop number #";
}

function formatMsg($data) {
    foreach($data as $route => $mins) {
	    $msg .=  'Line ' . $route . " in ";
	    foreach($mins as $min) { $msg .= $min."mins "; }
	    $msg .= " / ";
    }

    $msg = substr( trim($msg), 0, -1);
	//$msg .= $from;

    return $msg;
}

// now greet the sender
header("content-type: text/xml");
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<Response>
    <Sms><?= $msg ?></Sms>
</Response>