<?php
session_start(); // Start a session
include "DataBase/connection.php";

$error_message = ''; // Initialize error message variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $email = trim($_POST['email']);
    $password = $_POST['password']; // Get plaintext password directly

    // Prepare SQL query to fetch user data
    $stmt = $conn->prepare("SELECT * FROM tbl_useraccounts WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the email exists in user accounts
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Check the password directly
        if ($password === $user['password']) {
            // Store user information in session
            $_SESSION['user_id'] = $user['id']; // Assuming 'id' is the primary key
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_first_name'] = $user['first_name']; // Add first_name to session
            $_SESSION['user_last_name'] = $user['last_name']; // Add last_name to session


            // Redirect to appointment page
            header("Location: patient-index.php");
            exit();
        } else {
            $error_message = "Invalid password. Please try again.";
        }
    } else {
        // No user found, check if it's an admin login
        $stmt->close(); // Close the previous statement

        // Prepare SQL query to fetch admin data
        $stmt = $conn->prepare("SELECT * FROM tbl_admin WHERE username = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if the username exists in admin accounts
        if ($result->num_rows > 0) {
            $admin = $result->fetch_assoc();
            
            // Check the password directly for admin
            if ($password === $admin['password']) {
                // Store admin information in session
                $_SESSION['admin_id'] = $admin['id']; // Assuming 'id' is the primary key

                // Redirect to admin site
                header("Location: AdminSite/admin-index.php");
                exit();
            } else {
                $error_message = "Invalid password for admin. Please try again.";
            }
        } else {
            $error_message = "No account found with that email or username.";
        }
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="design/index.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/ionicons@5.5.3/dist/css/ionicons.min.css">
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-fluid">
        <a class="navbar-brand text-white" href="#">
            <img src="images/odc-logo.png" alt="MySite Logo" width="30" height="30" class="d-inline-block align-text-top">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link text-white active" aria-current="page" href="#">Home</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Services
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="dental-services/dental-check-up.php">Dental Check up</a></li>
                        <li><a class="dropdown-item" href="dental-services/restoration.php">Restoration</a></li>
                        <li><a class="dropdown-item" href="dental-services/oral-prophylaxis.php">Oral Prophylaxis</a></li>
                        <li><a class="dropdown-item" href="dental-services/extraction.php">Extraction</a></li>
                        <li><a class="dropdown-item" href="dental-services/teeth-whitening.php">Teeth Whitening</a></li>
                        <li><a class="dropdown-item" href="dental-services/orthodontics.php">Orthodontics</a></li>
                        <li><a class="dropdown-item" href="dental-services/dentures.php">Dentures</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link btn btn-primary text-white" href="login.php">Login</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Login Form Section -->
<div class="container mt-5">
    <h2 class="text-center">Login</h2>
    <?php if ($error_message): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>
    <form method="POST" action="login.php" class="mt-4 mx-auto" style="max-width: 400px;">
        <div class="input-box mb-3">
            <span class="icon"><ion-icon name="mail"></ion-icon></span>
            <input type="name" name="email" class="form-control" placeholder="Email" required>
        </div>

        <div class="input-box mb-3">
            <span class="icon"><ion-icon name="lock-closed"></ion-icon></span>
            <input type="password" name="password" class="form-control" placeholder="Password" required>
        </div>

        <div class="d-flex justify-content-between align-items-center">
            <label><input type="checkbox" name="remember"> Remember me</label>
            <a href="#" class="text-primary">Forgot password?</a>
        </div>

        <button type="submit" class="btn btn-primary w-100 mt-3">Login</button>

        <div class="text-center mt-3">
            <p>Don't have an account? <a href="register.php" class="text-primary">Register</a></p>
        </div>
    </form>
</div>

<!-- Footer -->
<footer class="bg-light text-center text-lg-start mt-5">
    <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">
        © <?php echo date("Y"); ?> Ordinario Dental Clinic - All Rights Reserved.
    </div>
</footer>

<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
