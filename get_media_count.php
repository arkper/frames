<?php

while (true) {
	$count = file_get_contents('http://primeframes.com/get_media.php');
	echo "Media files count now: $count\n";

	sleep (20);
}


?>