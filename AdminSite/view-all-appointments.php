<?php
    include "../DataBase/connection.php"; // Ensure the path to your connection is correct

    if (isset($_POST['appointment_id'])) {
        $appointment_id = $_POST['appointment_id'];
    
        $sql = "UPDATE tbl_appointment SET status = 'DONE' WHERE appointment_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $appointment_id);
    
        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "error";
        }
        $stmt->close();
        $conn->close();
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../design/index.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/ionicons@5.5.3/dist/css/ionicons.min.css">
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</head>


<body>
    <?php if (isset($_GET['message'])): ?>
        <script>
            alert("<?= htmlspecialchars($_GET['message']) ?>");
        </script>
    <?php endif; ?>

    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand text-white" href="admin-index.php">
                <img src="../images/odc-logo.png" alt="MySite Logo" width="30" height="30" class="d-inline-block align-text-top">
                <span class="ms-2">ADMIN DASHBOARD</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="input-box mt-1 mb-1 d-flex ms-auto">
                    <span class="icon"><ion-icon name="search-outline"></ion-icon></span>
                    <input type="text" class="form-control" placeholder="Search..." aria-label="Search">
                </div>

                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            ADMINISTRATION
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="view-services.php">Edit Services</a></li>
                            <li><a class="dropdown-item" href="schedule-planner.php">Schedule Availability</a></li>
                            <li><a class="nav-link text-white w-70" href="../Functionality/logout.php">Log Out</a></li>
                            <!-- Add more services here -->
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- TABLE -->
    <div class="container my-5">
        <div class="table-container">
            <div class="d-flex align-items-center mb-4">
                <h2 class="text-left me-2">Your Appointments</h2>
                <ion-icon name="bar-chart-outline" style="font-size: 2rem;"></ion-icon>
            </div>
            <table class="table table-hover table-striped table-bordered text-center">
                <thead>
                    <tr>
                        <th>Appointment ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Service</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    // Query to fetch only "PENDING" or "APPROVED" appointments
                    $sql = "SELECT `user_id`, `appointment_id`, `first_name`, `last_name`, `date`, `time`, `service`, `price`, `status` FROM `tbl_appointment` WHERE `status` IN ('PENDING', 'APPROVED')";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $statusColor = ($row['status'] == "APPROVED") ? "green" : "blue";

                            $approveButton = $row['status'] == "APPROVED"
                                ? "<button class='btn btn-success btn-sm w-50 mb-1' disabled>Approved</button>"
                                : "<a class='btn btn-success btn-sm w-50 mb-1' href='../DataBase/update-appointment-status.php?id={$row['appointment_id']}'>Approve</a>";

                            echo "<tr>
                                <td style='display: none;'>{$row['user_id']}</td>
                                <td>{$row['appointment_id']}</td>
                                <td>{$row['first_name']}</td>
                                <td>{$row['last_name']}</td>
                                <td>{$row['date']}</td>
                                <td>{$row['time']}</td>
                                <td>{$row['service']}</td>
                                <td>{$row['price']}</td>
                                <td style='color: {$statusColor}; font-weight: bold;'>{$row['status']}</td>
                                <td>
                                    <button class='btn btn-primary btn-sm w-50 mb-1 view-btn' 
                                            data-bs-toggle='modal' 
                                            data-bs-target='#viewModal'
                                            data-user-id='{$row['user_id']}'
                                            data-appointment-id='{$row['appointment_id']}'
                                            data-first-name='{$row['first_name']}'
                                            data-last-name='{$row['last_name']}'
                                            data-date='{$row['date']}'
                                            data-time='{$row['time']}'
                                            data-service='{$row['service']}'
                                            data-price='{$row['price']}'
                                            data-status='{$row['status']}'>
                                        View
                                    </button>
                                    <button class='btn btn-warning btn-sm w-50 mb-1'>Reschedule</button>
                                    {$approveButton}
                                </td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='9'>No appointments found</td></tr>";
                    }

                    $conn->close();
                ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL FOR VIEWING DETAILS -->
    <div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewModalLabel">View Appointment Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <label for="viewAppointmentId" class="form-label">Appointment ID</label>
                            <input type="text" class="form-control" id="viewAppointmentId" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="viewUserId" class="form-label">User ID</label>
                            <input type="text" class="form-control" id="viewUserId" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="viewFirstName" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="viewFirstName" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="viewLastName" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="viewLastName" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="viewDate" class="form-label">Date</label>
                            <input type="text" class="form-control" id="viewDate" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="viewTime" class="form-label">Time</label>
                            <input type="text" class="form-control" id="viewTime" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="viewService" class="form-label">Service</label>
                            <input type="text" class="form-control" id="viewService" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="viewPrice" class="form-label">Price</label>
                            <input type="text" class="form-control" id="viewPrice" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="viewStatus" class="form-label">Status</label>
                            <input type="text" class="form-control" id="viewStatus" readonly>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success">Make it Done</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-light text-center text-lg-start mt-5">
        <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">
            © <?php echo date("Y"); ?> Ordinario Dental Clinic - All Rights Reserved.
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const viewModal = new bootstrap.Modal(document.getElementById("viewModal"));

            document.querySelectorAll(".view-btn").forEach(button => {
                button.addEventListener("click", function () {
                    document.getElementById("viewAppointmentId").value = button.getAttribute("data-appointment-id");
                    document.getElementById("viewUserId").value = button.getAttribute("data-user-id");
                    document.getElementById("viewFirstName").value = button.getAttribute("data-first-name");
                    document.getElementById("viewLastName").value = button.getAttribute("data-last-name");
                    document.getElementById("viewDate").value = button.getAttribute("data-date");
                    document.getElementById("viewTime").value = button.getAttribute("data-time");
                    document.getElementById("viewService").value = button.getAttribute("data-service");
                    document.getElementById("viewPrice").value = button.getAttribute("data-price");
                    document.getElementById("viewStatus").value = button.getAttribute("data-status");
                });
            });

             // Add event listener for "Make it Done" button
             document.querySelector(".modal-footer .btn-success").addEventListener("click", function () {
                const appointmentId = document.getElementById("viewAppointmentId").value;

                $.ajax({
                    url: "../DataBase/update-appointment-status.php",
                    type: "POST",
                    data: { appointment_id: appointmentId, status: "DONE" },
                    success: function (response) {
                        if (response === "success") {
                            alert("Appointment status updated to DONE.");
                            location.reload(); // Reload the page to update the status
                        } else {
                            alert("Failed to update appointment status.");
                        }
                    },
                    error: function () {
                        alert("Error occurred while updating appointment status.");
                    }
                });
            });
        });
    </script>

    <script>
        // Additional AJAX for service price and dentist fetching can be implemented here
    </script>


</body>
</html>
