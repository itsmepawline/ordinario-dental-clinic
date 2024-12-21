<?php
include "connection.php";

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $service_id = mysqli_real_escape_string($conn, $_POST['service-id']);
    $service_type = mysqli_real_escape_string($conn, $_POST['service-type']);
    $service_price = mysqli_real_escape_string($conn, $_POST['service-price']);
    $service_description = mysqli_real_escape_string($conn, $_POST['service-description']);

    // Update the service in the database
    $sql = "UPDATE tbl_services SET type='$service_type', price='$service_price', description='$service_description' WHERE id='$service_id'";

    if (mysqli_query($conn, $sql)) {
        // Redirect back to the update-services page after success
        header("Location: ../AdminSite/view-services.php");
        exit();
    } else {
        echo "Error updating service: " . mysqli_error($conn);
    }

    // Close the connection
    mysqli_close($conn);
}
?>
