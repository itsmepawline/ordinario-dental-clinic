<?php
    include "connection.php";

    if (isset($_POST['service_id'])) {
        $service_id = $_POST['service_id'];

        // Fetch the selected service details from the database
        $query = "SELECT description, price FROM tbl_services WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $service_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $service = $result->fetch_assoc();
            echo json_encode($service);
        } else {
            echo json_encode([]);
        }
    }
?>
