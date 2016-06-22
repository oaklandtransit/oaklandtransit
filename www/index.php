
<!DOCTYPE html>
<html>
  <head>
    <title>BettaSTOP - Text "stopID" to 5108170800 - BettaSTOP</title>
    <link href='http://twitter.github.com/bootstrap/assets/css/bootstrap.css' rel='stylesheet' type='text/css' />
    <link href='http://twitter.github.com/bootstrap/assets/css/bootstrap-responsive.css' rel='stylesheet' type='text/css' />
    <!--<script type='text/javascript'>
      var _gaq = _gaq || [];_gaq.push(['_setAccount', 'UA-8063472-4']);_gaq.push(['_trackPageview']);(function() {var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);})();
    </script>-->
  </head>
  <body>
    <div class='container'>
      <div class='hero-unit'>
        <div class='row'>
          <div class='span4'>
            <strong>Re-introducing customer service...</strong>
            <h1>BettaSTOP</h1>
        <br />

            <p>
              BettaSTOP is a commuter driven public transit intelligence platform, powered by text messages (SMS).  
              
              <br><br>
              Text your 5-digit AC Transit stopID to <strong>(510) 817-0800:</strong></p>
              <blockquote><strong>14th St. & Broadway:</strong> 52111
			  <br><strong>Coliseum BART ></strong> 56777
			  <br><strong>Oakland Airport > </strong> 50275
			  <br><strong>8th St. & Broadway ></strong> 54222	</blockquote>
              <p>Get your bus arrival prediction, 
              <br>then <a href="feedback.php"><strong>tell us about your experience on the bus</strong></a> <strong>all via text.</strong>
            </p>
            <br />
            <a class='btn btn-large btn-primary' href='#use'>Learn more</a>
          </div>
          <div class='span6'>
            <img class='thumbnail' src='http://localhost/img/sample-prediction.png' width="375px" />
          </div>
        </div>
      </div>
      <br />
    <div class='row'>
        <div class='span12'>
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

$dbhost = "localhost";
$username="root";
$password="root";
$database="bettastop";
/*$dbhost = "oaklandtransitsqldb.badgerbag.com";
$username="blaktivist";
$password="ridethebus!";
$database="oaklandtransit";*/


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

<div id="outerBorder">
	
	<div id="innerContent">
		<h2>Review Feedback</h2>
		<p>
			<form action="/">
			<table>
			<tr><td>Agency:</td><td><input type="hidden" value="actransit">
			<select name="agency" id="agency">
		    <option value="actransit">AC Transit </option>
		    <option disabled value="sf-muni">SF Muni (Not yet!)</option>
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

        </div>
    </div>
<hr>      
      <a name='use'></a>
      <div class='row'>
        <div class='span12'>
          <h1>How to Use</h1>
          <ul>
          <li>Send a text message w/ the stop ID of the AC Transit stop you are standing at.</li>
          <li>When the bus arrives, send "on" if you were able to successfully get on.</li>
          </ul>
          
          <p>*Standard telephone and SMS rates apply.</p>
        </div>
      </div>
      <br />
      <h1>Transit Agencies</h1>
      <div class='row'>
            <div class='span5'>
          <h3>
            <a href='http://www.actransit.org/' target='_blank'>AC Transit</a>
            &mdash; Oakland, CA
          </h3>
          <br />
          <p>Text your stop number to <strong>(510) 817-0800</strong> (example 52111)</p>
          <ul class='thumbnails'>
            <li class='span4'>
              <a class='thumbnail' href='va_english.png'>
                <img class='thumbnail' src='http://localhost/img/sample-prediction.png' />
              </a>
            </li>
          </ul>
        </div>
        <div class='span6'>
          <h3>
            <em><!--<a href='http://www.sfmta.com/' target='_blank'>-->SF Muni <span style="color:red;">Want it?</em></span></a>
            &mdash; San Fran, CA
          </h3>
          <br />
          <a class='btn btn-mini btn-primary' href='' style="margin-bottom:10px;">Vote for it</a>
          <ul class='thumbnails'>
            <li class='span4'>
              <a class='thumbnail' href='va_english.png'>
                <img class='thumbnail' src='http://localhost/img/sample-prediction.png' />
              </a>
            </li>
          </ul>
      </div>
        <!--<div class='span12'>
          <h3>
            <a href='http://www.arlingtontransit.com/' target='_blank'>AC Transit</a>
            &mdash; Oakland, CA
          </h3>
          <br />
          <p>Text your stop number to <strong>(510) 817-0800</strong> (example 52111)</p>
          <ul class='thumbnails'>
            <li class='span4'>
              <a class='thumbnail' href='va_english.png'>
                <img class='thumbnail' src='http://localhost/img/sample-prediction.png' />
              </a>
            </li>
          </ul>
        </div>
      </div>
      <div class='row'>
        <div class='span12'>
          <h3>
            <a href='http://www.arlingtontransit.com/' target='_blank'>SF Muni <span style="color:red;"><em>Coming Soon!</em></span></a>
            &mdash; Oakland, CA
          </h3>
          <br />
          <p>Text your stop number to <strong>(510) 817-0800</strong> (example 52111)</p>
          <ul class='thumbnails'>
            <li class='span4'>
              <a class='thumbnail' href='va_english.png'>
                <img class='thumbnail' src='http://localhost/img/sample-prediction.png' />
              </a>
            </li>
          </ul>
        </div>-->
      </div>
      
           <br />
      <br />
      
      <div class='row'>
        <div class='span12'>
          <h3>Powered by:</h3>
          <a href='http://www.twilio.com/gallery/projects/BettaSTOP' target='_blank'> <img src='https://twimg0-a.akamaihd.net/profile_images/126034111/logo-circle-only_reasonably_small.png' height="27px" style="float:left; padding: 0 5px;"/> <h3>Twilio</h3>
          </a>
        </div>
      </div>
      <footer>
        <div id="copyright">&copy; 2011-2012 Oakland Transit Group | Team Lead by <a href="http://krysfreeman.com">Krys Freeman</a></div>
      </footer>
      <br />
    </div>
  
  </body>
  
</html>

# test line
