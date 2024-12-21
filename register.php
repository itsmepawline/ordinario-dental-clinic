<?php
include 'DataBase/connection.php'; // DB connection

$error_message = ''; // Initialize error message variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $first_name = trim($_POST['firstname']);
    $last_name = trim($_POST['lastname']);
    $address = trim($_POST['address']);
    $email = trim($_POST['email']);
    $contact_number = trim($_POST['contact']);
    $password = $_POST['password']; // Get plaintext password directly

    // Check if email already exists
    $checkEmailStmt = $conn->prepare("SELECT * FROM tbl_useraccounts WHERE email = ?");
    $checkEmailStmt->bind_param("s", $email);
    $checkEmailStmt->execute();
    $result = $checkEmailStmt->get_result();

    if ($result->num_rows > 0) {
        $error_message = "Email is already in use. Please choose another one.";
    } else {
        // Prepare SQL query to insert the user data into the database
        $stmt = $conn->prepare("INSERT INTO tbl_useraccounts (first_name, last_name, address, email, contact_number, password) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $first_name, $last_name, $address, $email, $contact_number, $password); // Store plaintext password

        // Execute the query and check success
        if ($stmt->execute()) {
            header("Location: login.php");
            exit(); // Ensure no further code is executed
        } else {
            $error_message = "Error: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    }

    // Close the connection
    $checkEmailStmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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

<!-- Registration Form Section -->
<div class="container mt-5">
    <h2 class="text-center">Register</h2>
    <?php if ($error_message): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>
    <form method="POST" class="mt-4 mx-auto" style="max-width: 400px;">
        <div class="input-box mb-3">
            <span class="icon"><ion-icon name="person"></ion-icon></span>
            <input type="text" name="firstname" class="form-control" placeholder="First Name"  required oninput="convertToUppercase(this)" required>
        </div>

        <div class="input-box mb-3">
            <span class="icon"><ion-icon name="person"></ion-icon></span>
            <input type="text" name="lastname" class="form-control" placeholder="Last Name"  required oninput="convertToUppercase(this)" required>
        </div>

        <div class="input-box mb-3">
            <span class="icon"><ion-icon name="location"></ion-icon></span>
            <input type="text" name="address" class="form-control" placeholder="Address"  required oninput="convertToUppercase(this)" required>
        </div>

        <div class="input-box mb-3">
            <span class="icon"><ion-icon name="mail"></ion-icon></span>
            <input type="email" name="email" class="form-control" placeholder="Email" required>
        </div>

        <div class="input-box mb-3">
            <span class="icon"><ion-icon name="call"></ion-icon></span>
            <input type="text" name="contact" class="form-control" placeholder="Contact Number" pattern="[0-9+\- ]+" title="Only numbers, spaces, '+' or '-' are allowed" required>
        </div>

        <div class="input-box mb-3">
            <span class="icon"><ion-icon name="lock-closed"></ion-icon></span>
            <input type="password" name="password" class="form-control" placeholder="Password" required>
        </div>

        <div class="mb-3">
            <label><input type="checkbox" name="terms" required> I agree to the terms & conditions</label>
        </div>

        <button type="submit" class="btn btn-primary w-100">Register</button>

        <div class="text-center mt-3">
            <p>Already have an account? <a href="login.php" class="text-primary">Login</a></p>
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

<script>
    function validateInput(event) {
        const value = event.target.value;
        const regex = /^[0-9+\- ]*$/;
        if (!regex.test(value)) {
            alert('Only numbers, spaces, "+" or "-" are allowed in the contact number.');
        }
    }

    // Convert input text to uppercase
    function convertToUppercase(element) {
        element.value = element.value.toUpperCase();
    }
</script>
</body>
</html>
