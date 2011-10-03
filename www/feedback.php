<?php
$dbhost = "oaklandtransitsqldb.badgerbag.com";
$username="blaktivist";
$password="ridethebus!";
$database="oaklandtransit";
$connect = mysql_connect($dbhost,$username,$password);
@mysql_select_db($database, $connect) or die( "Unable to select database");
$sql = "SELECT * FROM feedback ORDER BY stopID ASC";
$result = mysql_query($sql, $connect);
$numrows=mysql_numrows($result);
$i=0;

while ($i < $numrows) {
	$stopID[] = mysql_result($result,$i,"stopID");
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
<title>BettaSTOP Feedback</title>
</head>

<body>

<p>
<? if($numrows != 0): ?>
<table>
	<? while($i < $numrows):?>
	<tr>
		<td><?= $stopID[$i] ?></td>
		<td><?= $gotOn[$i] ?></td>
		<td><?= $comment[$i] ?></td>
		<td><?= $date_time[$i] ?></td>
	</tr>
	<? $i++; ?>
	<? endwhile; ?>
</table>
<? else: ?>
<i>No feedback found.</i>
<? endif; ?>
</p>

</body>
</html>