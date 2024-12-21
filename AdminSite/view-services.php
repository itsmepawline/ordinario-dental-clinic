<?php
    include "../DataBase/connection.php";

    // Fetch data from tbl_services
    $query = "SELECT * FROM tbl_services";
    $result = mysqli_query($conn, $query);

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

    <!-- Services Table -->
    <div class="container my-5">
        <div class="table-container">
            <div class="d-flex align-items-center mb-4">
                <h2 class="text-left me-2">Services</h2>
                <ion-icon name="bar-chart-outline" style="font-size: 2rem;"></ion-icon>
            </div>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Price</th>
                        <th>Description</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Check if any rows are returned
                    if (mysqli_num_rows($result) > 0) {
                        // Fetch each row of data
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . $row['type'] . "</td>";
                            echo "<td>" . $row['price'] . "</td>";
                            echo "<td>" . $row['description'] . "</td>";
                            echo '<td>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#updateServiceModal" onclick="populateUpdateModal(\'' . $row['id'] . '\', \'' . $row['type'] . '\', \'' . $row['price'] . '\', \'' . $row['description'] . '\')">Update</button>
                                            <button class="btn btn-danger btn-sm" onclick="confirmDelete(' . $row['id'] . ')">Delete</button>
                                        </div>
                                    </td>';
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No services available.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            <button class="btn btn-primary me-md-2" type="button" data-bs-toggle="modal" data-bs-target="#addServiceModal">ADD A NEW SERVICE</button>
        </div>
    </div>

    <!-- Add Service Modal -->
    <div class="modal fade" id="addServiceModal" tabindex="-1" aria-labelledby="addServiceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addServiceModalLabel">Add New Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="../DataBase/add-service.php" method="POST">
                        <div class="input-box mb-3">
                            <span class="icon"><ion-icon name="medkit-outline"></ion-icon></span>
                            <input type="text" name="service-type" class="form-control" placeholder="Type of Service" required>
                        </div>

                        <div class="input-box mb-3">
                            <span class="icon"><ion-icon name="cash-outline"></ion-icon></span>
                            <input type="text" name="service-price" class="form-control" placeholder="Price of Service" required>
                        </div>

                        <div class="input-box mb-3">
                            <span class="icon"><ion-icon name="document-text-outline"></ion-icon></span>
                            <input type="text" name="service-description" class="form-control" placeholder="Description" required>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Add Service</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Service Modal -->
    <div class="modal fade" id="updateServiceModal" tabindex="-1" aria-labelledby="updateServiceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateServiceModalLabel">Update Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="../DataBase/update-service.php" method="POST">
                        <!-- Hidden input to store service id -->
                        <input type="hidden" id="update-service-id" name="service-id">
                        
                        <div class="input-box mb-3">
                            <span class="icon"><ion-icon name="medkit-outline"></ion-icon></span>
                            <input type="text" id="update-service-type" name="service-type" class="form-control" placeholder="Type of Service" required>
                        </div>

                        <div class="input-box mb-3">
                            <span class="icon"><ion-icon name="cash-outline"></ion-icon></span>
                            <input type="text" id="update-service-price" name="service-price" class="form-control" placeholder="Price of Service" required>
                        </div>

                        <div class="input-box mb-3">
                            <span class="icon"><ion-icon name="document-text-outline"></ion-icon></span>
                            <input type="text" id="update-service-description" name="service-description" class="form-control" placeholder="Description" required>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update Service</button>
                        </div>
                    </form>
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
    <script>
        function populateUpdateModal(id, type, price, description) {
            // Set the values in the modal form
            document.getElementById('update-service-id').value = id;
            document.getElementById('update-service-type').value = type;
            document.getElementById('update-service-price').value = price;
            document.getElementById('update-service-description').value = description;
        }
    </script>
    <script>
        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this service?')) {
                // Redirect to delete-service.php with the service ID
                window.location.href = '../DataBase/delete-service.php?id=' + id;
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <script>
        // Additional AJAX for service price and dentist fetching can be implemented here
    </script>
</body>
</html>
