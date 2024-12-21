<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="design/index.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
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

    <!-- Main Content Section -->
    <div class="container-fluid bg-image text-white d-flex align-items-center" style="background-image: url('images/index-background.jpg'); height: 100vh;">
        <div class="container">
            <div class="row">
                <!-- Left Content (Clinic Info) -->
                <div class="col-md-8 position-relative">
                    <div class="blur-overlay">
                        <h1 class="display-4">Ordinario Dental Clinic</h1>
                        <p class="lead">
                            Your Trusted Dental Companion for Comprehensive Care
                        </p>
                        <p>
                            We offer top-quality dental services to ensure your smile stays healthy and beautiful. From regular check-ups to advanced dental procedures, we are here to help you maintain optimal oral health.
                        </p>
                    </div>
                </div>

                <!-- Right Content (Button) -->
                <div class="col-md-4 d-flex align-items-center justify-content-center">
                    <a href="register.php" class="btn btn-primary btn-lg px-4 py-2 mt-3">Book an Appointment</a>
                </div>
            </div>
        </div>
    </div>



    <!-- Footer -->
    <footer class="bg-light text-center text-lg-start mt-4">
        <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">
            © <?php echo date("Y"); ?> Ordinario Dental Clinic - All Rights Reserved.
        </div>
    </footer>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
