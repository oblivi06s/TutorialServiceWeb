<?php
    $host = "localhost";
    $username = "root";
    $password = "Jabuenjm_1";
    $database = "finalproj";

    // Create a MySQLi connection
    $conn = new mysqli($host, $username, $password, $database);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
?>