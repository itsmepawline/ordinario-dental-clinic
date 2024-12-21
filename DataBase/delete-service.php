<?php
    include "connection.php";

    // Check if 'id' is set in the URL
    if (isset($_GET['id'])) {
        $service_id = mysqli_real_escape_string($conn, $_GET['id']);

        // Prepare the SQL DELETE query
        $sql = "DELETE FROM tbl_services WHERE id='$service_id'";

        // Execute the query
        if (mysqli_query($conn, $sql)) {
            // Redirect back to the services page after deletion
            header("Location: ../AdminSite/view-services.php");
            exit();
        } else {
            echo "Error deleting service: " . mysqli_error($conn);
        }
    }

    // Close the connection
    mysqli_close($conn);
?>
