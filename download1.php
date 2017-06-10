<?php

$data_dir = 'DATA/';
$img_dir = 'IMG/';
$records = 2000;

$data_files = array_diff(scandir($data_dir), array('..', '.'));

$file_name = $data_files[2];

$count = 0;

if ($file = fopen($data_dir . $file_name, "r")) {
	fgets($file);
	fgets($file);
} else {
	die ("Failed to open file $file_name");
}

//print_r ($argv);

if ($argv[1] === "update") {
	$update_only = 1;
} else {
	$update_only = 0;
}

//echo $update_only;

$file_counter = 0;

while (!feof($file)) {

	$target_file = "$data_dir/import/import_data_" . $file_counter++ . '.csv';

	echo "Opening output file $target_file\n";

	$target = fopen($target_file, "w");

	$count = 0;

	while(!feof($file) && $count < $records) {
		$line = fgets($file);

		$data = explode(";", $line);

		if (!$data[0]) {
			break;
		}

		$upc = $data[1];
		$model = $data[2];
		$color = $data[3];
		$color_code = $data[4];
		$size = $data[5];
		$category = $data[34];
		$model_desc = $data[8];
		$s_price = number_format($data[10] * 2 * 0.9, 2, '.', '');
		$w_price = $data[10] * 2;
		$temple = $data[16];
		$bridge = $data[17];
		$gender = $data[33];
		$brand_name = $data[40];
		$brand = $data[41];
		$lenses_color = $data[7];
		$shape = $data[27];
		$temple_mat = $data[29];
		$front_mat = $data[30];
		$style = $data[38];
		$temple_length = $data[16];
		$bridge_size = $data[17];
		$lens_width = $data[18];
		$lens_height = $data[19];
		$rx_able = $data[23];

		if ($rx_able === "Y") {
			$category .= '|RX-able';
		}

		$title = "$brand_name $model $size $color $gender";
		$desc = "<table><tr><td>Model: $brand_name $model</td><td>Size: $size</td></tr>";
		$desc .= "<tr><td>Gender: $gender</td><td>Color: $color</td></tr>";
		$desc .= "<tr><td>Lenses color: $lenses_color</td><td>Shape: $shape</td></tr>";
		$desc .= "<tr><td>Temple material: $temple_mat</td><td>Front material: $front_mat</td></tr>";
		$desc .= "<tr><td>Stype: $style</td><td>Collection: $category</td></tr>";
		$desc .= "<tr><td>Temple length: $temple_length</td><td>Bridge size: $bridge_size</td></tr>";
		$desc .= "<tr><td>Lens width: $lens_width</td><td>Lens height: $lens_height</td></tr></table>";

		//$img_file = str_replace(' ', '_', $model) . '__' . $color_code . '_222x111.jpg';
		//$img_file = str_replace('/', '_', $img_file);
       	//$url = "https://my.luxottica.com/myLuxotticaImages/" . strtoupper($brand) . "/" . $img_file;
		$img_file1 = str_replace(' ', '_', $model) . '__' . $color_code . '_890x445.jpg';
		$img_file1 = str_replace('/', '_', $img_file1);

		if ($update_only == 0) {
       		$url1 = "https://my.luxottica.com/myLuxotticaImages/" . strtoupper($brand) . "/" . $img_file1;
		} else {
			$url1 = "";
		}

		//$data = file_get_contents ($url);
		//file_put_contents($img_dir . $img_file, $data);

		$row = "$upc,$model,$color,$color_code,$size,$category,$desc,$title,$s_price,$w_price,$temple,$bridge,$gender,$brand_name,$brand_name,$url1,no,instock,$rx_able\n";

		//echo "Writing record $count\n";

		fwrite ($target, $row);

		fflush($target);

		$count++;

	}
	fclose($target);
}

fclose($file);
