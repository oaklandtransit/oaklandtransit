<?php
error_reporting(0);

include "../../lib/RealtimeData.php";

$_REQUEST['Body'] = ' #56329 hello ';

$body = trim($_REQUEST['Body']);
preg_match("/[0-9]{5}/", $body, $matches);

if (!empty( $matches[0] )) {
    $action = 'realtime';
}

switch($action) {

    case "realtime":
        $RT     = new RealtimeData();
        $msg    = formatMsg( $RT->getRealtimeArrival('actransit', $matches[0] ) );
        break;

    case "":
        break;

    default:
        $msg = "Welcome to Oakland Transit !";
}

function formatMsg($data) {
    foreach($data as $route=>$mins) {
	    $msg .= $route . " ";
	    foreach($mins as $min) { $msg .= $min."mins "; };
	    $msg .= " / ";
    }
    return $msg;
}

// now greet the sender
header("content-type: text/xml");
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<Response>
    <Sms><?= $msg ?></Sms>
</Response>