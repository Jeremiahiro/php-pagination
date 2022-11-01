<?php

// include paginator file
include 'paginator.php';

echo 'Welcome to pagination example';
echo "<br>"."<br>";

// database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "abims";

$conn = mysqli_connect($servername, $username, $password, $dbname);

// return error message if connection fails
if ($conn->connect_error) { 
    die("Connection failed: " . $conn->connect_error);
}

// get data count 
$get_users_count = "SELECT COUNT(*) as total FROM payments";
$total_records = $conn->query($get_users_count);
$total_records_count = $total_records->fetch_assoc();

// use paginator file referenced included in line 3
$paginator = new Paginator();
$paginator->total = $total_records_count['total'];
$paginator->paginate();

// define limit & offset
$limit = ($paginator->currentPage-1) * $paginator->itemsPerPage;
$offset = $paginator->itemsPerPage;

//get record from database
$records = $conn->query("SELECT * FROM payments LIMIT $limit,  $offset") ;

// display data
while ($row = $records->fetch_assoc()) {
    echo $row['id'] . ' - ' . $row['uuid']."<br>";
}

//print
echo $paginator->pageNumbers();
echo $paginator->itemsPerPage();

?>