<?php
    include "DataBase/connection.php";
    session_start(); // Start the session

    // Check if the user is logged in
    if (!isset($_SESSION['user_id'])) {
        // Redirect to the login page if not logged in
        header("Location: login.php");
        exit();
    }

    // Fetch user details if needed
    $user_id = $_SESSION['user_id'];
    // You can also fetch user details from the database if necessary

    // Fetch the count of upcoming appointments
    $upcoming_query = "SELECT COUNT(*) FROM tbl_appointment WHERE user_id = ? AND (status = 'PENDING' OR status = 'APPROVED')";
    $stmt_upcoming = $conn->prepare($upcoming_query);
    $stmt_upcoming->bind_param("i", $user_id);
    $stmt_upcoming->execute();
    $stmt_upcoming->bind_result($upcoming_count);
    $stmt_upcoming->fetch();
    $stmt_upcoming->close();

    // Fetch the count of past appointments (if needed)
    $past_query = "SELECT COUNT(*) FROM tbl_appointment WHERE user_id = ? AND status = 'COMPLETED'";
    $stmt_past = $conn->prepare($past_query);
    $stmt_past->bind_param("i", $user_id);
    $stmt_past->execute();
    $stmt_past->bind_result($past_count);
    $stmt_past->fetch();
    $stmt_past->close();


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="design/index.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/ionicons@5.5.3/dist/css/ionicons.min.css">
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

    <!-- Custom Css -->
    <style>
        .fixed-height-card {
            height: 200px; /* Set your desired fixed height */
        }
    </style>
</head>
<body>

    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand text-white" href="#">
                <img src="images/odc-logo.png" alt="MySite Logo" width="30" height="30" class="d-inline-block align-text-top">
                <span class="ms-2">PATIENT DASHBOARD</span>
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
                            <?php echo htmlspecialchars($_SESSION['user_first_name'] . ' ' . $_SESSION['user_last_name']); // Display full name ?>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="Functionality/logout.php">Log Out</a></li>
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
                <div class="card fixed-height-card d-flex align-items-center justify-content-center p-3 shadow-sm">
                    <div>
                        <h5>Upcoming Appointments</h5>
                        <h2><?php echo $upcoming_count; ?></h2> <!-- Display the count of upcoming appointments -->
                        <a href="appointment.php" class="btn btn-primary me-md-2">View / Make Appointment</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card fixed-height-card d-flex align-items-center justify-content-center p-3 shadow-sm">
                    <div>
                        <h5>Past Appointment and Payment History</h5>
                        <h2>0</h2>
                        <button class="btn btn-primary me-md-2" type="button">View History</button>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card fixed-height-card d-flex align-items-center justify-content-center p-3 shadow-sm">
                    <div>
                        <h5>My Feedbacks</h5>
                        <h2>0</h2>
                        <button class="btn btn-primary me-md-2" type="button">Write a feedback</button>
                    </div>
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
