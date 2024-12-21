<?php
    $host = 'localhost';       // Database host, usually 'localhost'
    $dbname = 'db_dentalappointment'; // Database name
    $username = 'root';        // Your MySQL username
    $password = '';            // Your MySQL password (leave blank if not set)

    // Create connection
    $conn = new mysqli($host, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
?>
