
<?php
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


<!DOCTYPE html>
<html>
  <head>
    <title>Text "stopID" to 5108170800 - BettaSTOP</title>
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
          <div class='span12'>
          <p>Re-introducing customer service...
            <h1>BettaSTOP</h1>
      <p>
<? if($numrows != 0): ?>
<table>
	<? while($i < $numrows):?>
	<tr>
		<td style="padding:0 5px;"><?= $stopID[$i] ?></td>
		<td style="padding:0 5px;"><?= $gotOn[$i] ?></td>
		<td width="400px"><?= $comment[$i] ?></td>
		<td><?= $date_time[$i] ?></td>
	</tr>
	<? $i++; ?>
	<? endwhile; ?>
</table>
<? else: ?>
<i>No feedback found.</i>
<? endif; ?>
</p>

          </div>
        </div>
      </div>
      <br />
    
      <a name='use'></a>
      <div class='row'>
        <div class='span12'>
          <h1>How to Use</h1>
          <p>Send a text message to the number below for your Transit Agency.</p>
          <p>For arrival times enter the 5 digit bus stop number that is displayed at the stop.</p>
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
            <em><!--<a href='http://www.sfmta.com/' target='_blank'>-->SF Muni <span style="color:red;">Coming Soon!</em></span></a>
            &mdash; San Francisco, CA
          </h3>
          <br />
          <br />
          <br />
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
          <a href='http://www.twilio.com' target='_blank'> <img src='https://twimg0-a.akamaihd.net/profile_images/126034111/logo-circle-only_reasonably_small.png' height="27px" style="float:left; padding: 0 5px;"/> <h3>Twilio</h3>
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

