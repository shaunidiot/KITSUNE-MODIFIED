<?php
// Zaseth#7550

if(ini_get('safe_mode')){
	fwrite(STDERR, "Safe mode is turned on, this script won't work.\n");
	exit(1);
} else {
	$getAttackerByIP = shell_exec("netstat -ntu -4 -6 |  awk '/^tcp/{ print $5 }' | sed -r 's/:[0-9]+$//' | sort | uniq -c | sort -n");
	//$oldNetstat = shell_exec("netstat -ntu |  awk '/^tcp/{ print $5 }' | sed -r 's/:[0-9]+$//' | sort | uniq -c | sort -n");
	echo "<pre>$getAttackerByIP</pre>";
}

?>