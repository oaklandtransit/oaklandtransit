<?php
error_reporting(0);
//include "lib/twilio/twilio.php";
include "../../lib/RealTimeData.php";

$body = $_REQUEST['Body'];

$busstop = "";

$busline = preg_match($_REQUEST['Body']);

$action = 'realtime';

switch($action) {

    case "realtime":
        $RT     = new RealtimeData();
        $data   = $RT->getRealtimeArrival('actransit','55554'));
        $msg    = formatData($data);
        break;

    case "":
        break;

    default:
        $msg = "Welcome to Oakland Transit !";
}


formatData($data) {
    //foreach ($data as $bus => $minutes) {
        $msg = "Line 54 in (" implode(",", $minutes) .')';
    //}
    return $msg;
}



// now greet the sender
    header("content-type: text/xml");
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<Response>
    <Sms><?= $msg ?></Sms>
</Response>