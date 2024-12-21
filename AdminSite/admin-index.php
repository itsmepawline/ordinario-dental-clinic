<?php
    include "../DataBase/connection.php"; // Ensure the path to your connection is correct
    session_start(); // Start the session

    // Check if the user is logged in
    // if (!isset($_SESSION['user_id'])) {
    //     // Redirect to the login page if not logged in
    //     header("Location: login.php");
    //     exit();
    // }

    // Fetch the total count of appointments (PENDING and APPROVED only)
    $appointment_query = "SELECT COUNT(*) FROM tbl_appointment WHERE status IN ('PENDING', 'APPROVED')";
    $stmt_appointment = $conn->prepare($appointment_query);
    $stmt_appointment->execute();
    $stmt_appointment->bind_result($total_appointments);
    $stmt_appointment->fetch();
    $stmt_appointment->close();

    // Fetch the total count of completed appointments
    $completed_appointment_query = "SELECT COUNT(*) FROM tbl_appointment WHERE status = 'DONE'"; // Change 'status' to your actual column name
    $stmt_completed_appointments = $conn->prepare($completed_appointment_query);
    $stmt_completed_appointments->execute();
    $stmt_completed_appointments->bind_result($total_completed_appointments);
    $stmt_completed_appointments->fetch();
    $stmt_completed_appointments->close();
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

    <!-- OVERVIEW -->
    <div class="container my-5">
        <div class="d-flex align-items-center mb-4">
            <h2 class="text-left me-2">Overview</h2>
            <ion-icon name="bar-chart-outline" style="font-size: 2rem;"></ion-icon>
        </div>
        <div class="row justify-content-center text-center">
            <div class="col-md-4 mb-4">
                <div class="card p-3 shadow-sm">
                    <h5>Appointment Management</h5>
                    <h2><?php echo $total_appointments; ?></h2> <!-- Display the total count of PENDING and APPROVED appointments -->
                    <p>Manage patient appointments efficiently.</p>
                    <a class="btn btn-primary me-md-2" href="view-all-appointments.php">View All Appointments</a>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card p-3 shadow-sm">
                    <h5>Feedback Analysis</h5>
                    <h2>0</h2>
                    <p>Analyze patient feedback for service improvement.</p>
                    <button class="btn btn-primary me-md-2" type="button">View All Feedback</button>
                </div>
            </div>
        </div>
        <div class="row justify-content-center text-center">
            <div class="col-md-4 mb-4">
                <div class="card p-3 shadow-sm">
                    <h5>Patient Records</h5>
                    <h2><?php echo $total_completed_appointments; ?></h2> <!-- Display the total count of completed appointments -->
                    <p>Access and manage patient records securely.</p>
                    <a class="btn btn-primary me-md-2" href="view-all-records.php" >View All Patient Records</a>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card p-3 shadow-sm">
                    <h5>Transaction Records</h5>
                    <h2>0</h2>
                    <p>Keep track of all financial transactions.</p>
                    <button class="btn btn-primary me-md-2" type="button">View All Transactions</button>
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
        // Additional AJAX for service price and dentist fetching can be implemented here
    </script>
</body>
</html>
