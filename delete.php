<?php
include('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $residentID = $_POST['residentsID'] ?? null;

    $sql = "DELETE FROM residents WHERE residentsID = $residentID";
    if ($conn->query($sql)) {
        header("Location: index.php");
        exit();
    } else {
        die("Error deleting record: " . $conn->error);
    }
} else {
    header("Location: index.php");
    exit();
}
