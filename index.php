<?php

// include paginator file
include 'paginator.php';

echo 'Welcome to pagination example';
echo "<br>";

// database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "abims";
$table = 'invoice_line_items';

$conn = mysqli_connect($servername, $username, $password, $dbname);

// return error message if connection fails
if ($conn->connect_error) { 
    die("Connection failed: " . $conn->connect_error);
}

// get data count 
$get_users_count = "SELECT COUNT(*) as total FROM $table";
$total_records = $conn->query($get_users_count);
$total_records_count = $total_records->fetch_assoc();

// use paginator file referenced included in line 3
$paginator = new Paginator();
$paginator->link = filter_var($_SERVER['PHP_SELF'], FILTER_UNSAFE_RAW); // replace with the right url for this page
$paginator->total = $total_records_count['total'];
$paginator->paginate();

// define limit & offset
$limit = ($paginator->currentPage-1) * $paginator->itemsPerPage;
$offset = $paginator->itemsPerPage;
$search = $paginator->search;

//get record from database and take search into consideration
if(!$search) {
    $records = $conn->query("SELECT * FROM $table LIMIT $limit, $offset");
} else {
    $records = $conn->query("SELECT * FROM $table WHERE name LIKE '%$search%' LIMIT $limit, $offset");
}

// this is for items per page, can be styled in paginator.php
echo $paginator->itemsPerPage();
echo "<br>";

echo $paginator->itemsSearch();
echo "<br>";

// set serial number
$offset;
$pageNumber = isset($_GET['current']) ? (int)$_GET['current'] : 1;
$currentNumber = ($pageNumber - 1) * $offset + 1;

// display data
while ($row = $records->fetch_assoc()) {
    echo $currentNumber++ . ' - ' . $row['name']."<br>";
}

//print
echo $paginator->pageNumbers();

?>