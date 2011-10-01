<?php

header("content-type: text/xml");
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
$template = sprintf("<Response><Sms>%s</Sms></Response>", $message);
echo $template;