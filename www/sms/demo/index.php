<?php
error_reporting(0);
//include "lib/twilio/twilio.php";
include "../../lib/RealtimeData.php";

$body = $_REQUEST['Body'];

$busstop = "";

$busline = preg_match($body);

$action = 'realtime';

switch($action) {

    case "realtime":
        $RT     = new RealtimeData();
        $msg    = formatMsg( $RT->getRealtimeArrival('actransit','55554') );
        break;

    case "":
        break;

    default:
        $msg = "Welcome to Oakland Transit !";
}

function formatMsg($data) {
    var_dump( $data );

    //foreach($data as $line)
    //return $msg;

}



// now greet the sender
    header("content-type: text/xml");
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<Response>
    <Sms><?= $msg ?></Sms>
</Response>