<?php
$servername = "localhost";
$username = "frames_admin";
$password = "Optik@2015";
$db = "i3583958_wp1";


try {
    $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e){
    die ("Connection failed: " . $e->getMessage());
}

$rows = $conn->query('SELECT * FROM frames');
?>

<html>
<head>
</head>
<body>
<table>
<thead>
<tr>
	<th>Vendor ID</th>
	<th>Line ID</th>
	<th>Size</th>
	<th>Style</th>
	<th>Model</th>
	<th>Bridge</th>
	<th>Temple</th>
	<th>Frame Picture</th>
</tr>
</thead>
<?php foreach ($rows as $row){ 
	$url = "frames/img/" . strtolower(trim($row[1], '0')) . '/' . $row[13];
?>
<tr>
	<td><?php echo $row[1]?></td>
	<td><?php echo $row[2]?></td>
	<td><?php echo $row[3]?></td>
	<td><?php echo $row[6]?></td>
	<td><?php echo $row[7]?></td>
	<td><?php echo $row[11]?></td>
	<td><?php echo $row[12]?></td>
	<td><a href="#" title="Picture" onclick="window.open('<?php echo $url?>')"><img src="<?php echo $url?>" width="100px" height="50px"/></a></td>
</tr>
<?php } ?>
</table>
</body>
</html>
