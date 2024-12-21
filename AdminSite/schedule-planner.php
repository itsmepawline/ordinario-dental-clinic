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

// Get the current month and year or the one provided via GET parameters
$currentMonth = isset($_GET['month']) ? (int)$_GET['month'] : date("n");
$currentYear = isset($_GET['year']) ? (int)$_GET['year'] : date("Y");

// Calculate the next and previous month/year
if ($currentMonth == 12) {
    $nextMonth = 1;
    $nextYear = $currentYear + 1;
} else {
    $nextMonth = $currentMonth + 1;
    $nextYear = $currentYear;
}

if ($currentMonth == 1) {
    $prevMonth = 12;
    $prevYear = $currentYear - 1;
} else {
    $prevMonth = $currentMonth - 1;
    $prevYear = $currentYear;
}

// Prepare to fetch availability for the month
$dateAvailability = []; // Array to hold availability

// Loop through each day of the month to check availability
$firstDay = mktime(0, 0, 0, $currentMonth, 1, $currentYear);
$numberDays = date("t", $firstDay);

for ($day = 1; $day <= $numberDays; $day++) {
    // Format the date
    $formattedDate = sprintf("%04d-%02d-%02d", $currentYear, $currentMonth, $day);
    
    // Query for the availability for the given date
    $availability_query = "SELECT `time` FROM `tbl_dateavailability` WHERE `selectedDate` = ?";
    $stmt_availability = $conn->prepare($availability_query);
    $stmt_availability->bind_param("s", $formattedDate);
    $stmt_availability->execute();
    $stmt_availability->bind_result($time);
    
    // Store the result in an array
    if ($stmt_availability->fetch()) {
        $dateAvailability[$day] = $time; // Store time for the day
    } else {
        $dateAvailability[$day] = null; // No availability
    }
    $stmt_availability->close();
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
    <style>
        .calendar {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px; /* Reduced gap between days */
            text-align: center;
            font-size: 0.9em; /* Smaller font size for the calendar */
        }
        .day {
            border: 1px solid #ccc;
            padding: 10px; /* Reduced padding */
            border-radius: 5px;
            background-color: #f9f9f9;
            position: relative;
            height: 60px; /* Fixed height for consistency */
        }
        .day-header {
            font-weight: bold;
        }
        .btn {
            padding: 5px 10px; /* Smaller button padding */
            font-size: 0.8em; /* Smaller font size for buttons */
        }
        .button-container {
            display: flex; /* Flexbox container */
            justify-content: center; /* Center the buttons */
            gap: 10px; /* Space between buttons */
            margin-top: 20px; /* Add some space above the buttons */
        }
    </style>
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

    <!-- CALENDAR OVERVIEW -->
    <div class="container my-5">
        <h2 class="text-center">Calendar for <?php echo date("F Y", mktime(0, 0, 0, $currentMonth, 1, $currentYear)); ?></h2>
        
        <div class="calendar">
            <?php
            // Calculate the first day of the month and how many days are in the month
            $firstDay = mktime(0, 0, 0, $currentMonth, 1, $currentYear);
            $numberDays = date("t", $firstDay);
            $dateComponents = getdate($firstDay);
            $dayOfWeek = $dateComponents['wday'];

            // Display the headers for the days of the week
            $dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            foreach ($dayNames as $dayName) {
                echo "<div class='day-header'>$dayName</div>";
            }

            // Blank days before the first day of the month
            for ($blank = 0; $blank < $dayOfWeek; $blank++) {
                echo "<div class='day'></div>";
            }

            // Show each day of the month
            for ($day = 1; $day <= $numberDays; $day++) {
                // Determine the background color based on availability
                $bgColor = '';
                if (isset($dateAvailability[$day])) {
                    switch ($dateAvailability[$day]) {
                        case 'AM':
                            $bgColor = 'style="background-color: yellow;"';
                            break;
                        case 'PM':
                            $bgColor = 'style="background-color: orange;"';
                            break;
                        case 'FULLDAY':
                            $bgColor = 'style="background-color: red;"';
                            break;
                        default:
                            break;
                    }
                }

                echo "<div class='day' $bgColor>$day</div>";
            }
            ?>
        </div>
        <div class="button-container">
            <a href="?month=<?php echo $prevMonth; ?>&year=<?php echo $prevYear; ?>" class="btn btn-primary">Previous</a>
            <a href="?month=<?php echo $nextMonth; ?>&year=<?php echo $nextYear; ?>" class="btn btn-primary">Next</a>
        </div>
    </div>

    <!-- MODAL FOR EDITING PER DATE -->
    <div class="modal fade" id="editAppointmentModal" tabindex="-1" aria-labelledby="editAppointmentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editAppointmentModalLabel">Edit Date Appointment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="selectedDate"></p> <!-- Placeholder for displaying selected date and day -->
                    <form id="appointmentForm">
                        <input type="hidden" id="day" name="day">
                        <input type="hidden" id="month" name="month">
                        <input type="hidden" id="year" name="year">
                        <div class="mb-3">
                            <label for="appointmentDetails" class="form-label">Details</label>
                            <div>
                                <input type="radio" id="am" name="time" value="AM" required>
                                <label for="am">AM</label>
                            </div>
                            <div>
                                <input type="radio" id="pm" name="time" value="PM" required>
                                <label for="pm">PM</label>
                            </div>
                            <div>
                                <input type="radio" id="fullday" name="time" value="FULLDAY" required>
                                <label for="fullday">FULL DAY</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-light text-center text-lg-start mt-5">
        <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">
            © <?php echo date("Y"); ?> Ordinario Dental Clinic
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.day').on('contextmenu', function(e) {
                e.preventDefault(); // Prevent the default context menu from appearing
                
                // Get the day number from the clicked element
                var dayNumber = $(this).text().trim();
                var month = <?php echo $currentMonth; ?>;
                var year = <?php echo $currentYear; ?>;

                // Set the values for hidden inputs
                $('#day').val(dayNumber);
                $('#month').val(month);
                $('#year').val(year);
                
                // Create a date object for the selected date
                var selectedDate = new Date(year, month - 1, dayNumber); // month is 0-indexed

                // Format the date in YYYY-MM-DD format
                var formattedDate = selectedDate.getFullYear() + '-' +
                String(selectedDate.getMonth() + 1).padStart(2, '0') + '-' +
                String(selectedDate.getDate()).padStart(2, '0'); // Format date

                // Display the formatted date in the modal
                $('#selectedDate').text("Selected Date: " + formattedDate);
                
                // Show the modal
                $('#editAppointmentModal').modal('show');
            });

            // Handle form submission with AJAX
            $('#appointmentForm').on('submit', function(e) {
                e.preventDefault(); // Prevent the default form submission

                // Gather form data
                var formData = {
                    day: $('#day').val(),
                    month: $('#month').val(),
                    year: $('#year').val(),
                    time: $('input[name="time"]:checked').val() // Get selected time option
                };

                // Send the data using AJAX
                $.ajax({
                    type: 'POST',
                    url: '../DataBase/edit-date-availability.php', // Update with the correct path
                    data: formData,
                    success: function(response) {
                        alert('Date availability updated successfully!'); // Alert success
                        $('#editAppointmentModal').modal('hide'); // Hide the modal
                        // Optionally refresh the calendar or update the UI accordingly
                        location.reload(); // Reload the page to refresh calendar
                    },
                    error: function(xhr, status, error) {
                        alert('Error: ' + error); // Handle any errors
                    }
                });
            });
        });
    </script>
</body>
</html>
