<?php
//This handles the SMS conversation for BettaStop
//Things left to implement
// - Getting routeID
// - Comment code

session_start();

error_reporting(0);

include "../../lib/RealtimeData.php";

//$_REQUEST['Body'] = ' #51099 hello ';

// get the session varible if it exists
$counter = $_SESSION['counter'];
// if it doesnt, set the default
if(!strlen($counter)) {
$counter = 0;
}
// save it

switch($counter) {

	case -1:
		$body = trim($_REQUEST['Body']);
		$msg = formatFeedback($body);
		break;

	case 0:
		$msg = predictArrival();
		break;
	
	case 1:
		$msg = onOrOff();
		break;
	
	case 2:
		$msg = "Thanks for your feedback - text 1 when you arrive";
		$counter++;
		$_SESSION['counter'] = $counter;
		$_SESSION['feedback'] = trim($_REQUEST['Body']);
		break;
	
	case 3:
		$msg = "You earned at total of +10 pts for this trip. Please use us again!";
		$counter = 0;
		$_SESSION['counter'] = $counter;
		saveFeedback();
		break;
	
	default:
		$counter = 0;
		$_SESSION['counter'] = $counter;
		$msg = "There was an error. Please enter your bus stop number.";
}

function formatFeedback($feedback){
	if($feedback == '1'){
		$feedback = "There was no wheelchair space.";
	} elseif($feedback == '2'){
		$feedback = "There was no bike space.";
	} elseif($feedback == '3'){
		$feedback = "There were no empty seats.";
	} else {
		$msg = "That was not a valid entry, please text 1 for no wheel chair space, 2 for no bike space, or 3 for no seats.";
		return $msg;
	}
	
	$_SESSION['feedback'] = $feedback;
	$msg = "Want another prediction? Text your stop number again.";
	saveFeedback();
	$counter = 0;
	$_SESSION['counter'] = $counter;
	return $msg;
}

function saveFeedback(){
	$dbhost = "oaklandtransitsqldb.badgerbag.com";
	$username="blaktivist";
	$password="ridethebus!";
	$database="oaklandtransit";
	$connect = mysql_connect($dbhost,$username,$password);
	@mysql_select_db($database, $connect) or die( "Unable to select database");
	//5 Digit stop number
	$stopID = $_SESSION['stopID'];
	//"test" for now but will be the route number
	$routeID = $_SESSION['routeID'];
	//A simple boolean for if they got on or not
	$gotOn = $_SESSION['gotOn'];
	//Feedback comment that was either auto-generated or user inputed.
	$feedback = $_SESSION['feedback'];
	
	$sql = "INSERT INTO feedback VALUES ('$stopID', '$routeID', '$gotOn', '$feedback', NOW())";
	$result = mysql_query($sql, $connect);
	mysql_close();
}

function onOrOff(){
	$body = trim($_REQUEST['Body']);
	$body = strtolower($body);
	
	switch($body) {

		case "on":
			$msg = "+3pts Text your feedback on the ride.";
			$counter = 2;
			$_SESSION['counter'] = $counter;
			$_SESSION['gotOn'] = TRUE;
			break;

		case "off":
			$msg = "+3pts You couldn't get on because:\r[1]wheel\r[2]bike\r[3]seat";
			$counter = -1;
			$_SESSION['counter'] = $counter;
			$_SESSION['gotOn'] = FALSE;
			break;

		default:
		$msg = "There was an error. Please text 'on' if you got on the bus or 'off' if you can't get on.";
	
	}
	
	return $msg;
}

function predictArrival(){
	$body = trim($_REQUEST['Body']);
	preg_match("/[0-9]{5}/", $body, $matches);

	if (!empty( $matches[0] )) {
		$action = 'realtime';
	}

	switch($action) {

		case "realtime":
			$RT     = new RealtimeData();
			$msg    = formatMsg( $RT->getRealtimeArrival('actransit', $matches[0] ) );
			$_SESSION['stopID'] = $matches[0];
			break;

		default:
			$msg = "Please enter your bus stop number #";
	}
	
	return $msg;
}

function formatMsg($data) {
	if (!empty($realtimeData)){
		foreach($data as $route => $mins) {
			$msg .=  'Line ' . $route . " in ";
			foreach($mins as $min) { $msg .= $min."mins "; }
			$msg .= " / ";
		}
	
		//We'll grab the routeID another way later
		$counter++;
		$_SESSION['counter'] = $counter;
		$_SESSION['routeID'] = "test";
	
		$msg = substr( trim($msg), 0, -1);
		$msg .= "+2pts text 'on' when you get on the bus - text 'off' if you can't get on";

	} else {
		$msg = "No buses found within the next hour.";
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