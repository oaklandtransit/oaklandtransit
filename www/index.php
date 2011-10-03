<?php

// query the realtime arrival API and display data

include_once('lib/RealtimeData.php');

$agency = isset($_REQUEST['agency']) ? $_REQUEST['agency'] : null;
$stop = isset($_REQUEST['stop']) ? $_REQUEST['stop'] : null;
$line = isset($_REQUEST['line']) ? strtoupper($_REQUEST['line']) : null;
if(empty($line))
	$line = null;

if (!is_null($agency) && is_numeric($stop)) {
	$RT = new RealtimeData();
	$realtimeData = $RT->getRealtimeArrival('actransit',$stop,$line);
} else {
	$realtimeData = array();
}

$dbhost = "oaklandtransitsqldb.badgerbag.com";
$username="blaktivist";
$password="ridethebus!";
$database="oaklandtransit";
$connect = mysql_connect($dbhost,$username,$password);
@mysql_select_db($database, $connect) or die( "Unable to select database");
$sql = "SELECT * FROM feedback WHERE stopID = '$stop' ORDER BY date_time DESC";
$result = mysql_query($sql, $connect);
$numrows=mysql_numrows($result);
$i=0;

while ($i < $numrows) {
	if(mysql_result($result,$i,"got_on"))
		$gotOn[] = "Got on!";
	else
		$gotOn[] = "Didn't get on!";
	$comment[] = mysql_result($result,$i,"comment");
	$date_time[] = mysql_result($result,$i,"date_time");
	$i++;
}

$i = 0;

mysql_close();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="viewport" content="user-scalable=yes, width=device-width" />

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>BettaSTOP</title>
<link rel="stylesheet" type="text/css" media="screen" href="/css/screen.css" />
</head>
<body>

<div id="outerBorder">
	<h1 id="mainHeader">BettaSTOP</h1>
	<div id="innerContent">
		<p>
			<form action="/">
			<table>
			<tr><td>Agency:</td><td><input type="hidden" value="actransit">
			<select name="agency" id="agency">
		    <option value="actransit">AC Transit </option>
		    <option value="sf-muni">SF Muni </option>
			</select></td>
			</tr>
			<tr><td>Stop:</td><td> <input class="datainput" type="number" name="stop" id="stop" value="<?= $stop ?>"></td></tr>
			<tr><td>Line:</td><td> <input class="datainput" type="number" name="line" id="line" value="<?= $line ?>"></td></tr>
			<tr><td colspan="2"><input type="submit" value="Go"></td></tr>
			</table>
			</form>
		</p>
		<hr>
		<p>
		<? if (!empty($realtimeData)): ?>
		<table>
			<? foreach($realtimeData as $route => $minutes): ?>
			<tr>
				<td class="route">Line <?= $route ?> </td>
				<td><? foreach($minutes as $min) { echo $min."m "; } ?></td>
			</tr>
			<? endforeach; ?>
		</table>
		<? else: ?>
		<i>No buses found within the next hour.</i>
		<? endif; ?>
		
		<? if($numrows != 0): ?>
		<table>
			<? while($i < $numrows):?>
			<tr>
				<td><?= $gotOn[$i] ?></td>
				<td><?= $comment[$i] ?></td>
				<td><?= $date_time[$i] ?></td>
			</tr>
			<? $i++; ?>
			<? endwhile; ?>
		</table>
		<? else: ?>
		<i>No feedback found for this stop.</i>
		<? endif; ?>
		</p>
	</div>
</div>
<div id="copyright">&copy; 2011 Oakland Transit Group</div>
</body>
</html>
