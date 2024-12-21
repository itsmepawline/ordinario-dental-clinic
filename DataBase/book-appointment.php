<?php
    include "connection.php"; // Include your database connection file

    session_start();

    // Check if the user is logged in
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../login.php"); // Redirect to login if not logged in
        exit();
    }

    // Get user data from session
    $user_id = $_SESSION['user_id'];

    // Function to generate a random 7-digit integer
    function generateRandomInteger($length = 7) {
        // Generate a random number with 7 digits
        return rand(10**($length - 1), (10**$length) - 1);
    }

    // Check if the form was submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Retrieve form data
        $first_name = $_POST['firstname'];
        $last_name = $_POST['lastname'];
        $address = $_POST['address']; // Make sure this is passed if needed
        $contact = $_POST['contact']; // Make sure this is passed if needed
        $date = $_POST['datepicker']; // This should be the date from POST
        $time_period = $_POST['time_period']; // This should be the time from POST
        $service_type = $_POST['service']; // Change to service type
        $price = $_POST['price']; // Assume this is the price fetched from AJAX
        $status = "PENDING"; // Set status to PENDING

        // Generate a random 7-digit integer for appointment_id
        $appointment_id = generateRandomInteger(7);

        // Check if the selected date and time have already been booked more than twice
        $count_query = "SELECT COUNT(*) as total FROM tbl_appointment WHERE date = ? AND time = ?";
        $stmt_count = $conn->prepare($count_query);
        // Bind parameters for date and time from POST request
        $stmt_count->bind_param("ss", $date, $time_period);
        $stmt_count->execute();
        $result_count = $stmt_count->get_result();
        $row_count = $result_count->fetch_assoc();
        $total_count = $row_count['total'];

        // Prepare the SQL INSERT statement for tbl_appointment
        $insert_query = "INSERT INTO tbl_appointment (user_id, appointment_id, first_name, last_name, date, time, service, price, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($insert_query);

        // Check if the statement was prepared successfully
        if ($stmt_insert === false) {
            die('Prepare failed: ' . htmlspecialchars($conn->error));
        }

        // Bind parameters
        $stmt_insert->bind_param("isssssdss", $user_id, $appointment_id, $first_name, $last_name, $date, $time_period, $service_type, $price, $status);

        // Execute the insert statement for tbl_appointment
        if ($stmt_insert->execute()) {
            // Check if total_count equals 2
            if ($total_count == 2) {
                // Insert into tbl_dateavailability to track availability
                $availability_query = "INSERT INTO tbl_dateavailability (selectedDate, time) VALUES (?, ?)";
                $stmt_availability = $conn->prepare($availability_query);
                $stmt_availability->bind_param("ss", $date, $time_period);

                // Execute the insert statement for tbl_dateavailability
                if ($stmt_availability->execute()) {
                    // Redirect or show success message
                    header("Location: ../appointment.php?success=Appointment booked successfully!");
                    exit();
                } else {
                    // Handle error for tbl_dateavailability
                    echo "<script>alert('Error adding availability: " . htmlspecialchars($stmt_availability->error) . "');</script>";
                }
            } else {
                // Redirect or show success message for tbl_appointment only
                header("Location: ../appointment.php?success=Appointment booked successfully without updating availability.");
                exit();
            }
        } else {
            // Handle error for tbl_appointment
            echo "<script>alert('Error booking appointment: " . htmlspecialchars($stmt_insert->error) . "');</script>";
        }

        // Close the statements
        $stmt_insert->close();
        $stmt_availability->close();
        $stmt_count->close(); // Close count statement
    }

    // Close the database connection
    $conn->close();
?>
