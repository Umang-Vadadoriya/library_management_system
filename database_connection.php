<?php

//database_connection.php

$connect = new PDO("mysql:host=localhost; dbname=lms", "root", "");
$conn = mysqli_connect("localhost", "root", "", "lms");

session_start();

?>