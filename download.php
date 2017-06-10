<?php

$data_dir = 'DATA/';
$img_dir = 'IMG/';

$ini_array = parse_ini_file("frames.ini");

$brands = explode(',', $argv[1]);

foreach ($brands as $brand) {
	if ($brand == 'download.php') {
		continue;
	}

	$url = $ini_array['urls'][$brand];
	$data = file_get_contents ($url);
	$file_name = $data_dir . $brand . '.dat';
	file_put_contents($file_name, $data);

	if ($file = fopen($file_name, "r")) {
	    while(!feof($file)) {
	        $line = fgets($file);
			if (strpos($line, '000000000000000000') === 0 || strlen($line) < 10) {
				continue;
			}

	        $img_file = split($line, $brand);
	       	$url = "https://my.luxottica.com/myLuxotticaImages/" . strtoupper($brand) . "/" . $img_file;
			$data = file_get_contents ($url);
			file_put_contents($img_dir . $brand . '/' . $img_file, $data);
	    }
	    fclose($file);
	}
}

function split($line, $brand) {
	global $ini_array;

	if ($ini_array['split_method'][$brand] == 1) {
		return split1($line);
	} elseif ($ini_array['split_method'][$brand] == 2) {
		return split2($line);
	} elseif ($ini_array['split_method'][$brand] == 3) {
		return split3($line);
	}
}

function split2($line) {
	$parts = preg_split('/\s+/', $line);
	$img_file = $parts[0] . '_' . $parts[1] . '__' . $parts[6] . '_890x445.jpg';
	$img_file = str_replace('/', '_', $img_file);
	return $img_file;
}

function split1($line) {
	$img_file = str_replace(' ', '_', trim(substr($line,0,9))) . '__' . trim(substr($line,13,6)) . '_890x445.jpg';
	$img_file = str_replace('/', '_', $img_file);
	return $img_file;
}

function split3($line) {
	$img_file = str_replace(' ', '_', trim(substr($line,0,9))) . '__' . trim(substr($line,13,6)) . '_890x445.jpg';
	$img_file = str_replace('/', '_', $img_file);
	return $img_file;
}


?>