<?php
include "connection.php"; // Ensure the path to your connection is correct
session_start(); // Start the session

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize the inputs
    $day = isset($_POST['day']) ? (int)$_POST['day'] : null;
    $month = isset($_POST['month']) ? (int)$_POST['month'] : null;
    $year = isset($_POST['year']) ? (int)$_POST['year'] : null;
    $time = isset($_POST['time']) ? $_POST['time'] : null;

    // Create a date object
    $selectedDate = new DateTime("$year-$month-$day");
    $formattedDate = $selectedDate->format('Y-m-d'); // Format as YYYY-MM-DD

    // Prepare the SQL query
    $query = "INSERT INTO tbl_dateavailability (selectedDate, time) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    
    // Bind parameters
    $stmt->bind_param("ss", $formattedDate, $time); // "ss" for string types
    
    // Execute the query
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>
