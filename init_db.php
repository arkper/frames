<?php
// Create (connect to) SQLite database in file
$db = new PDO('sqlite:frames.db') or die ('Could not open the database frames.db');

// Set errormode to exceptions
$db->setAttribute(PDO::ATTR_ERRMODE,
                            PDO::ERRMODE_EXCEPTION);

//$db->query("drop table frames");

$sql =
	"create table IF NOT EXISTS frames(sku varchar(20), model varchar(15), color_name varchar(48), color_code varchar(10), " .
	"size int, lens_color varchar(48), suggested_retail_price float, wholesale_price float, lens_material varchar(20), " .
	"photochromic char(1), polarized char(1), standard char(1), temple_length float, bridge_size float,	lens_witdth float, " .
	"lens_height float,	rx_able char(1), front_color varchar(30), shape varchar(20), type varchar(10), temple_material varchar(32),	" .
	"front_material varchar(32), gender varchar(6),	collection varchar(15),	new char(1), theme varchar(15),	brand_name varchar(15),	brand_code varchar(5))";


if ($db->query($sql)) {
	echo "Created table frames\n";
} else {
	echo "Failed to create table frames\n";
}


$sql =
	"create table IF NOT EXISTS products(id decimal(10,0), model varchar(15), lens_color varchar(48), lens_material varchar(20), " .
	"photochromic char(1), polarized char(1), standard char(1), temple_length float, bridge_size float,	lens_witdth float, " .
	"lens_height float,	rx_able char(1), front_color varchar(30), shape varchar(20), type varchar(10), temple_material varchar(32),	" .
	"front_material varchar(32), gender varchar(6),	collection varchar(15),	new char(1), theme varchar(15),	brand_name varchar(15),	brand_code varchar(5), tags varchar(48))";

if ($db->query($sql)) {
	echo "Created table products\n";
} else {
	echo "Failed to create table products\n";
}

$sql =
	"create table IF NOT EXISTS variations(id decimal(10,0), sku varchar(20), model varchar(15), color_name varchar(48), color_code varchar(10), " .
	"size int, suggested_retail_price float, wholesale_price float, photo_url varchar(128))";

if ($db->query($sql)) {
	echo "Created table variations\n";
} else {
	echo "Failed to create table variations\n";
}

$sql =
	"create table IF NOT EXISTS tags(id decimal(10,0), name varchar(32))";

if ($db->query($sql)) {
	echo "Created table tags\n";
} else {
	echo "Failed to create table tags\n";
}

$sql =
	"create table IF NOT EXISTS categories(id decimal(10,0), name varchar(32))";

if ($db->query($sql)) {
	echo "Created table categories\n";
} else {
	echo "Failed to create table categories\n";
}



?>