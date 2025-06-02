<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "rebat";

$conn = new mysqli($servername, $username, $password, $database);
$conn->query("SET time_zone = '+08:00'");
if($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
} else {
    echo("<p class='conSucc'>Connection succesful</p>");
}
?>