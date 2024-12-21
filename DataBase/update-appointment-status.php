<?php
    include "../DataBase/connection.php"; // Ensure the path to your connection is correct

    // Check if appointment_id is set
    if (isset($_GET['id']) || isset($_POST['appointment_id'])) {
        $appointment_id = isset($_GET['id']) ? $_GET['id'] : $_POST['appointment_id'];
        $status = isset($_POST['status']) ? $_POST['status'] : 'APPROVED';

        // Prepare the SQL statement to update the status
        $sql = "UPDATE `tbl_appointment` SET `status` = ? WHERE `appointment_id` = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("si", $status, $appointment_id); // Bind the status and appointment_id parameters
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                if ($status == 'DONE') {
                    echo "success";
                } else {
                    header("Location: ../AdminSite/view-all-appointments.php?message=Appointment Approved");
                }
            } else {
                if ($status == 'DONE') {
                    echo "error";
                } else {
                    header("Location: ../AdminSite/view-all-appointments.php?message=Failed to Approve Appointment");
                }
            }

            $stmt->close();
        } else {
            echo "Error preparing statement: " . $conn->error;
        }
    } else {
        header("Location: ../AdminSite/view-all-appointments.php?message=Invalid Appointment ID");
    }

    // Close the connection
    $conn->close();
?>
