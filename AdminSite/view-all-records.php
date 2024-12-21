<?php
    include "../DataBase/connection.php"; // Ensure the path to your connection is correct
    session_start(); // Start the session

    // Check if the user is logged in
    // if (!isset($_SESSION['user_id'])) {
    //     // Redirect to the login page if not logged in
    //     header("Location: login.php");
    //     exit();
    // }

    // Fetch the total count of appointments
    $appointment_query = "SELECT COUNT(*) FROM tbl_appointment";
    $stmt_appointment = $conn->prepare($appointment_query);
    $stmt_appointment->execute();
    $stmt_appointment->bind_result($total_appointments);
    $stmt_appointment->fetch();
    $stmt_appointment->close();

    // Fetch appointments with status "DONE" and join with user accounts
    $done_appointments_query = "
        SELECT a.`user_id`, a.`appointment_id`, a.`first_name`, a.`last_name`, a.`date`, a.`service`, 
            u.`email`, u.`address` 
        FROM `tbl_appointment` a
        JOIN `tbl_useraccounts` u ON a.`user_id` = u.`id`
        WHERE a.`status` = 'DONE'";
    $stmt_done = $conn->prepare($done_appointments_query);
    $stmt_done->execute();
    $stmt_done->bind_result($user_id, $appointment_id, $first_name, $last_name, $date, $service, $email, $address);

    // Store appointment data in an array
    $appointments = [];
    while ($stmt_done->fetch()) {
        $appointments[] = [
            'user_id' => $user_id,
            'appointment_id' => $appointment_id,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'address' => $address,
            'date' => $date,
            'service' => $service
        ];
    }
    $stmt_done->close();
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
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</head>
<body>

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
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- DATATABLES -->
    <div class="container mt-5">
        <h2 class="text-center">Completed Appointments</h2>
        <table id="appointmentsTable" class="table table-striped">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Appointment ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Date</th>
                    <th>Service</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($appointments as $appointment): ?>
                    <tr>
                        <td><?php echo $appointment['user_id']; ?></td>
                        <td><?php echo $appointment['appointment_id']; ?></td>
                        <td><?php echo $appointment['first_name']; ?></td>
                        <td><?php echo $appointment['last_name']; ?></td>
                        <td><?php echo $appointment['email']; ?></td>
                        <td><?php echo $appointment['address']; ?></td>
                        <td><?php echo $appointment['date']; ?></td>
                        <td><?php echo $appointment['service']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
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
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#appointmentsTable').DataTable();
        });
    </script>
</body>
</html>
