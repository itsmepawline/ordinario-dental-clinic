<?php
    include "connection.php"; // Include your connection file

    // Check if the form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get the form data
        $service_type = mysqli_real_escape_string($conn, $_POST['service-type']);
        $service_price = mysqli_real_escape_string($conn, $_POST['service-price']);
        $service_description = mysqli_real_escape_string($conn, $_POST['service-description']);

        // Insert the data into the `tbl_services` table
        $sql = "INSERT INTO tbl_services (type, price, description) 
                VALUES ('$service_type', '$service_price', '$service_description')";

        // Check if the query was successful
        if (mysqli_query($conn, $sql)) {
            // Redirect to update-services.php after successful insertion
            header("Location: ../AdminSite/view-services.php");
            exit();
        } else {
            // Handle the error if the query fails
            echo "Error: " . mysqli_error($conn);
        }

        // Close the database connection
        mysqli_close($conn);
    }
?>
