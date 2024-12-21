<?php
    include "DataBase/connection.php";

    session_start();

    // Check if the user is logged in
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    // Get user data from session or database
    $user_id = $_SESSION['user_id'];

    // Fetch user details from the database
    $query = "SELECT * FROM tbl_useraccounts WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    $first_name = $user['first_name'];
    $last_name = $user['last_name'];
    $email = $user['email'];

    // Fetch appointments for this user from tbl_appointment
    $appointment_query = "
    SELECT a.appointment_id, a.first_name, a.last_name, a.date, a.time, 
        a.service, s.price, s.description, s.type, a.status 
    FROM tbl_appointment a
    JOIN tbl_services s ON a.service = s.id 
    WHERE a.user_id = ?";
    $stmt_appointments = $conn->prepare($appointment_query);
    $stmt_appointments->bind_param("i", $user_id); // Use user_id directly
    $stmt_appointments->execute();
    $appointments_result = $stmt_appointments->get_result();

    // Fetch all services from the tbl_services
    $services_query = "SELECT id, type, price, description FROM tbl_services";
    $services_result = $conn->query($services_query);

    // Fetch all data from tbl_dateavailability
    $date_availability_query = "SELECT * FROM tbl_dateavailability";
    $date_availability_result = $conn->query($date_availability_query);

    // Prepare date availability array
    $date_availability = [];
    while ($row = $date_availability_result->fetch_assoc()) {
        $date = $row['selectedDate'];
        $time = $row['time'];

        if (!isset($date_availability[$date])) {
            $date_availability[$date] = $time;
        } else {
            $date_availability[$date] .= ',' . $time; // Append additional time slots
        }
    }

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

    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <style>
        .highlight-am 
        {
            background-color: #FFC300 !important; /* Yellow for AM */
            color: black; /* Change text color for better visibility */
        }
        .highlight-pm 
        {
            background-color: #FFAA33 !important; /* Orange for PM */
            color: black; /* Change text color for better visibility */
        }
        .highlight-fullday 
        {
            background-color: red !important; /* Red for FULLDAY */
            color: white; /* Change text color for better visibility */
            pointer-events: none; /* Disable click */
            cursor: not-allowed;
        }
    </style>
</head>
<body>

    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand text-white" href="patient-index.php">
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

    <!-- Appointment Table -->
    <div class="container my-5">
        <div class="table-container">
            <div class="d-flex align-items-center mb-4">
                <h2 class="text-left me-2">Your Appointments</h2>
                <ion-icon name="briefcase-outline" style="font-size: 2rem;"></ion-icon>
            </div>
            <table class="table table-hover table-striped table-bordered text-center">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Service</th>
                        <th>Price</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($appointments_result->num_rows > 0) { ?>
                        <?php while ($appointment = $appointments_result->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($appointment['date']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['time']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['service']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['price']); ?></td>
                                <td>
                                    <?php echo htmlspecialchars($appointment['status']); ?>
                                    <br>
                                    <?php if (strtoupper($appointment['status']) === 'APPROVED') { ?>
                                        <button class="btn btn-info btn-sm w-75" data-bs-toggle="modal" data-bs-target="#viewDetailsModal"
                                            data-fullname="<?php echo htmlspecialchars($appointment['first_name'] . ' ' . $appointment['last_name']); ?>"
                                            data-email="<?php echo htmlspecialchars($email); ?>"
                                            data-contact="<?php echo htmlspecialchars($user['contact_number']); ?>"
                                            data-date="<?php echo htmlspecialchars($appointment['date']); ?>"
                                            data-time="<?php echo htmlspecialchars($appointment['time']); ?>"
                                            data-service="<?php echo htmlspecialchars($appointment['service']); ?>"
                                            data-description="<?php echo htmlspecialchars($appointment['description']); ?>" 
                                            data-price="<?php echo htmlspecialchars($appointment['price']); ?>"
                                            data-service-type="<?php echo htmlspecialchars($appointment['type']); ?>"> <!-- Added service type -->
                                        View Details
                                    </button>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="8" class="text-center">NONE</td> <!-- Adjust column span based on your table -->
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- Button to trigger modal -->
        <button class="btn btn-primary me-md-2" type="button" data-bs-toggle="modal" data-bs-target="#bookAppointmentModal">Book an Appointment</button>
    </div>



    <!-- View Details Modal -->
    <div class="modal fade" id="viewDetailsModal" tabindex="-1" aria-labelledby="viewDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewDetailsModalLabel">Appointment Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Fullname:</strong> <span id="modalFullName"></span></p>
                    <p><strong>Email:</strong> <span id="modalEmail"></span></p>
                    <p><strong>Contact:</strong> <span id="modalContact"></span></p>
                    <p><strong>Date:</strong> <span id="modalDate"></span></p>
                    <p><strong>Time:</strong> <span id="modalTime"></span></p>
                    <p><strong>Service Type:</strong> <span id="modalServiceType"></span></p>
                    <p><strong>Description:</strong> <span id="modalDescription"></span></p>
                    <p><strong>Price:</strong> <span id="modalPrice"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary">Request for Reschedule</button>
                    <button type="button" class="btn btn-success proceed-payment" data-price="" data-description="">Proceed to Payment</button>
                </div>
            </div>
        </div>
    </div>




    



    <!-- Book Appointment Modal -->
    <div class="modal fade" id="bookAppointmentModal" tabindex="-1" aria-labelledby="bookAppointmentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bookAppointmentModalLabel">Book an Appointment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="DataBase/book-appointment.php" method="POST">
                        <div class="input-box mb-3">
                            <span class="icon"><ion-icon name="person"></ion-icon></span>
                            <input type="text" name="firstname" class="form-control" value="<?php echo htmlspecialchars($user['first_name']); ?>" readonly>
                        </div>
                        <div class="input-box mb-3">
                            <span class="icon"><ion-icon name="person"></ion-icon></span>
                            <input type="text" name="lastname" class="form-control" value="<?php echo htmlspecialchars($user['last_name']); ?>" readonly>
                        </div>
                        <div class="input-box mb-3">
                            <span class="icon"><ion-icon name="home"></ion-icon></span>
                            <input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars($user['address']); ?>" readonly>
                        </div>
                        <div class="input-box mb-3">
                            <span class="icon"><ion-icon name="call"></ion-icon></span>
                            <input type="text" name="contact" class="form-control" value="<?php echo htmlspecialchars($user['contact_number']); ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="datepicker">Select Date:</label>
                            <input type="text" name="datepicker" class="form-control" id="datepicker" placeholder="YYYY-MM-DD" required>
                            <div id="error-message" class="text-danger"></div>
                        </div>
                        <div class="mb-3">
                            <label>Select Time:</label><br>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="time_period" id="am" value="AM" required>
                                <label class="form-check-label" for="am">AM</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="time_period" id="pm" value="PM" required>
                                <label class="form-check-label" for="pm">PM</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="service">Select Service:</label>
                            <select name="service" id="service" class="form-control" required>
                                <option value="" disabled selected>Select Services</option>
                                <?php while ($row = $services_result->fetch_assoc()) { ?>
                                    <option value="<?php echo htmlspecialchars($row['id']); ?>"> <!-- Use id as the value -->
                                        <?php echo htmlspecialchars($row['type']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="input-box mb-3">
                            <input type="text" name="description" id="description" class="form-control" placeholder="Description" value="Description" readonly>
                        </div>
                        <div class="input-box mb-3">
                            <input type="text" name="price" id="price" class="form-control" placeholder="Price" value="Price" readonly>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Book Appointment</button>
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
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize Flatpickr
            flatpickr("#datepicker", {
                dateFormat: "Y-m-d", // Adjust the format as needed
                minDate: "today", // Prevent past dates
                onDayCreate: function(dObj, dStr, fp, dayElem) {
                    // Get the date in YYYY-MM-DD format
                    const dateString = dayElem.dateObj.toISOString().split('T')[0];
                    
                    // Check availability
                    if (dateString in <?php echo json_encode($date_availability); ?>) {
                        const timeSlots = <?php echo json_encode($date_availability); ?>[dateString];

                        // Track counts for AM and PM
                        let amCount = 0;
                        let pmCount = 0;

                        // Count AM and PM slots
                        timeSlots.split(',').forEach(slot => {
                            if (slot.trim() === "AM") {
                                amCount++;
                            } else if (slot.trim() === "PM") {
                                pmCount++;
                            }
                        });

                        // Determine class to add based on counts
                        if (amCount > 0 && pmCount > 0) {
                            dayElem.classList.add("highlight-fullday");
                            dayElem.style.pointerEvents = 'none'; // Disable clicks
                        } else if (amCount > 0) {
                            dayElem.classList.add("highlight-am");
                        } else if (pmCount > 0) {
                            dayElem.classList.add("highlight-pm");
                        }
                    }
                },
            });

            $('#service').on('change', function() {
                var serviceID = $(this).val();
                
                if(serviceID) {
                    $.ajax({
                        url: 'DataBase/fetch-service.php',
                        type: 'POST',
                        data: {service_id: serviceID},
                        dataType: 'json',
                        success: function(response) {
                            if(response) {
                                $('#description').val(response.description);
                                $('#price').val(response.price);
                            } else {
                                $('#description').val('');
                                $('#price').val('');
                            }
                        }
                    });
                } else {
                    $('#description').val('');
                    $('#price').val('');
                }
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            // Trigger modal on button click
            $('#viewDetailsModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var modal = $(this);

                // Example data from the button that triggered the modal
                const fullName = button.data('fullname');
                const email = button.data('email');
                const contact = button.data('contact');
                const date = button.data('date');
                const time = button.data('time');
                const serviceType = button.data('servicetype');
                const description = button.data('description');
                const price = button.data('price');

                // Update modal content
                modal.find('#modalFullName').text(fullName);
                modal.find('#modalEmail').text(email);
                modal.find('#modalContact').text(contact);
                modal.find('#modalDate').text(date);
                modal.find('#modalTime').text(time);
                modal.find('#modalServiceType').text(serviceType);
                modal.find('#modalDescription').text(description);
                modal.find('#modalPrice').text(price);

                // Set data attributes for the payment button
                modal.find('.proceed-payment').data('price', price).data('description', description);
            });
        });
    </script>

    <script>
        $(document).on('click', '.proceed-payment', function () {
            const price = $(this).data('price') * 100; // Convert PHP to centavos
            const description = $(this).data('description');

            $.ajax({
                url: 'PaymentController/payment-controller.php',
                type: 'POST',
                data: { amount: price, description: description },
                success: function (response) {
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    } else {
                        alert(response.error || 'Unable to process payment.');
                    }
                },
                error: function () {
                    alert('An error occurred. Please try again later.');
                }
            });
        });

    </script>
</body>
</html>
