<?php
function parse($line, $mode) {
	if ($mode == 1) {
		return parse1($line);
	} else {
		return parse2($line);
	}			
}

function parse1($line) {
	$img_file = str_replace(' ', '_', trim(substr($line,0,9))) . '__' . trim(substr($line,13,6)) . '_890x445.jpg';
	$img_file = str_replace('/', '_', $img_file);

	return array(
		substr($line,0,3),
		substr($line,3,6),
		substr($line,9,3),
		substr($line,13,23),
		substr($line,38,5),
		substr($line,45,5),
		substr($line,52,6),
		substr($line,67,13),
		substr($line,83,16),
		substr($line,69,2),
		substr($line,74,3),
		$img_file
	);
}

function parse2($line) {
	$img_file = str_replace(' ', '_', trim(substr($line,0,9))) . '__' . trim(substr($line,13,6)) . '_890x445.jpg';
	$img_file = str_replace('/', '_', $img_file);

	return array(
		substr($line,0,3),
		substr($line,3,6),
		substr($line,9,3),
		substr($line,13,23),
		substr($line,38,5),
		substr($line,45,5),
		substr($line,52,6),
		substr($line,67,13),
		substr($line,83,16),
		substr($line,69,2),
		substr($line,74,3),
		$img_file
	);
}

$servername = "localhost";
$username = "frames_admin";
$password = "Optik@2015";
$db = "i3583958_wp1";
$data_dir = "frames/data";




try {
    $ini_array = parse_ini_file("frames.ini");
    
    $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  	$statement = $conn->prepare(
  		"INSERT INTO frames (vendor_id, line_id, size, f4, f5, style, model, f8, f9, bridge, temple, img)
    	VALUES (:f1,:f2,:f3,:f4,:f5,:f6,:f7,:f8,:f9,:f10,:f11,:f12)");
    echo "Connected successfully" . '<br>';
} catch(PDOException $e){
    die ("Connection failed: " . $e->getMessage());
}

try {
	$conn->prepare("delete from frames")->execute();
} catch(PDOException $e){
	die ("Failed to truncate table 'frames' " . $e->getMessage());
}

echo "Table 'frames' truncated successfully<br>";

$data_files = array_diff(scandir($data_dir), array('..', '.'));

$count = 0;

foreach ($data_files as $data_file) {
	$brand = substr($data_file, 0, 2);

	if ($_GET["brands"] != null) {
		$brands_to_process = $_GET["brands"];
		if (strpos($brands_to_process, $brand) === false) {
			continue;
		}

	}

	echo ("Processing data file $data_file<br>");

	echo ("Brand $brand<br>");

	$mode = $ini_array['split_method'][$brand];

	echo ("Mode $mode<br>");

	$fh = fopen($data_dir . "/" . $data_file,'r');

	if ($mode == 3) {
		$line = fgets($fh);
	}

	while ($line = fgets($fh)) {
		echo ("Processing data line $line<br>");
		$array = parse($line, $mode);

		try {
			$statement->execute(array(
				"f1" => $array[0],
				"f2" => $array[1],
				"f3" => $array[2],
				"f4" => $array[3],
				"f5" => $array[4],
				"f6" => $array[5],
				"f7" => $array[6],
				"f8" => $array[7],
				"f9" => $array[8],
				"f10" => $array[9],
				"f11" => $array[10],
				"f12" => $array[11]
			));

			$count++;

		} catch (PDOException $e) {
    			echo ("Error inserting record into 'frames' " . $e->getMessage());
		}



	}
	fclose($fh);
}


echo ("Uploaded " . $count . " records");


$conn = null;

?>