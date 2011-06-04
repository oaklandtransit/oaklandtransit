<?php

// query the realtime arrival API and display data

include_once('lib/RealtimeData.php');

$agency = $_REQUEST['agency'];
$stop = $_REQUEST['stop'];
$line = $_REQUEST['line'];
if(empty($line))
	$line = null;

$RT = new RealtimeData();
$realtimeData = $RT->getRealtimeArrival('actransit',$stop,$line);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>BettaSTOP</title>
<link rel="stylesheet" type="text/css" media="screen" href="/css/screen.css" />
</head>
<body>
    

<div id="outerBorder">
	<h1 id="mainHeader">BettaSTOP</h1>
	<div id="innerContent">
		<p>
			<form action="<?= $_SERVER['PHP_SELF'] ?>">
			Agency: <input type="hidden" value="actransit">
			<select name="agency" id="agency">
		    <option value="actransit">AC Transit</option>
		    <option value="sf-muni">SF Muni</option>
			</select>
			Stop: <input type="text" name="stop" id="stop" value="<?= $_REQUEST['stop'] ?>">
			Line: <input type="text" name="line" id="line" value="<?= $_REQUEST['line'] ?>">
			<input type="submit" value="Go">
			</form>
		</p>
		<hr>
		<p>
		Next Bus:
		<table>
			<? foreach($realtimeData as $route => $minutes): ?>
			<tr>
				<td><?= $route ?>: </td>
				<td> <? foreach($minutes as $min) { echo $min."mins "; } ?></td>
			</tr>
			<? endforeach; ?>
		</table>
		</p>
	</div>
</div>
<div id="copyright">&copy; 2011 Oakland Transit</div>
</body>
</html>
