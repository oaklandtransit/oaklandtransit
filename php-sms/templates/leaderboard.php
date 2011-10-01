<?php

echo "<pre>"; var_dump($leaders); echo "</pre>";

?>

<h1>Leaders</h1>

<h2>People</h2>
<ul>
<?php foreach ( $leaders->users as $u) :  ?>
    <li><?= $u['_id'] ?> : <?= $u['value'] ?> pts</li>
<?php endforeach; ?>
</ul>

<hl>
<h2>Stops</h2>
<ul>
<?php foreach ( $leaders->stops as $s ) :  ?>
    <li><?= $s['_id'] ?> : <?= $s['value'] ?> checkins</li>
<?php endforeach; ?>
</ul>


<hl>
<h2>Lines</h2>
<ul>
<?php foreach ( $leaders->lines as $l => $line ) :  ?>
    <li><?= $l ?></li>
<?php endforeach; ?>
</ul>