<?php
require './db_conn.php';

if (!isUserLoggedIn()) {
    header('location:index.php');
    exit;
}

$userid = $_SESSION['user_id'];

if (!isset($_SESSION['notifications'])) {
    $_SESSION['notifications'] = array();
}
$time_period = isset($_POST['time_period']) ? $_POST['time_period'] : 'past_month';
$start_date = '';
$end_date = '';

switch ($time_period) {
    case 'past_week':
        $start_date = date('Y-m-d', strtotime('-1 week'));
        $end_date = date('Y-m-d');
        break;
    case 'past_month':
        $start_date = date('Y-m-d', strtotime('-1 month'));
        $end_date = date('Y-m-d');
        break;
    case 'past_6_months':
        $start_date = date('Y-m-d', strtotime('-6 months'));
        $end_date = date('Y-m-d');
        break;
    case 'custom':
        $start_date = $_POST['start_date'];
        $end_date = $_POST['start_date'];
        break;
    default:
        $time_period = 'past_month';
        $start_date = date('Y-m-d', strtotime('-1 month'));
        $end_date = date('Y-m-d');
}

// echo $start_date;
// echo "<br>";
// echo $end_date;
// echo "<br>";
// echo $userid;
// echo "<br>";
$query = "SELECT SUM(totalShots) as total_shots FROM events 
          WHERE user_id = ? AND `date` BETWEEN ? AND ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('iss', $userid, $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// print_r($row);
$totalShots = $row['total_shots'] ?? 0;

$stmt->close();

if (isset($_REQUEST['delete'])) {
    $eventId = $_REQUEST['delete'];
    $sql1 = "DELETE FROM events WHERE `eventID` = '$eventId' AND `user_id` = '$userid'";
    if (mysqli_query($conn, $sql1)) {
        array_push($_SESSION['notifications'], array('success' => 'Event created successfully'));
    }
}


?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Clay Shooting</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
            integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
            crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link href="dist/styles.css" rel="stylesheet">
        <link href="dist/custom.css?v=7" rel="stylesheet">
    </head>

    <body class="home-bg flex flex-col" style="background-image: url(./assets/images/<?php echo getBgImg(); ?>);">
        <div class="h-100vh">
            <?php
            include ('./header.php');
            ?>
            <main class="py-5 h-100vh">
                <div class="container">
                    <div class="flex mb-6 justify-between items-center gap-4 sm:flex-row flex-col">
                        <form action="" class="flex items-center gap-4" id="shotsForm" method="POST">
                            <select name="time_period" id="time_period" class="form-input w-full border p-1 rounded">
                                <option value="">Select Date</option>
                                <option value="past_week" <?php echo $selected = ($time_period == 'past_week') ? 'selected' : ''; ?>>Last Week</option>
                                <option value="past_month" <?php echo $selected = ($time_period == 'past_month') ? 'selected' : ''; ?>>Last Month</option>
                                <option value="past_6_months" <?php echo $selected = ($time_period == 'past_6_months') ? 'selected' : ''; ?>>Last 6 Months</option>
                                <option value="custom" <?php echo $selected = ($time_period == 'custom') ? 'selected' : ''; ?>>Custom Date</option>
                            </select>
                            <div id="custom_date" <?php echo $custom_date = ($time_period == 'custom') ? 'style="display: block;"' : 'style="display: none;"'; ?> class="form-input w-full border p-1 rounded">
                                <input type="date" id="start_date" name="start_date" value="<?php echo $custom_date = ($time_period == 'custom') ? $start_date : ''; ?>">
                            </div>
                        </form>
                        <h4 class="font-bold text-xl text-white p-3 bg-black border-white border">Total Shots:
                            <?php echo $totalShots; ?></h4>
                    </div>
                    <h3 class="text-3xl font-bold text-center text-white">My Events</h3>
                    <div class="events py-10">
                        <?php echo $info; ?>
                        <?php
                        // Query to fetch event data with joins
                        $sql = "SELECT e.eventID, l.location AS location_name, e.date, a.name AS ammo_name, p.name AS poi_name, g.name AS glasses_name, ea.type AS ears_name, t.name AS type_name
        FROM events e
        LEFT JOIN locations l ON e.location = l.id
        LEFT JOIN ammo a ON e.ammo = a.id
        LEFT JOIN poi p ON e.poi = p.id
        LEFT JOIN glasses g ON e.glasses = g.id
        LEFT JOIN ears ea ON e.ears = ea.id
        LEFT JOIN type t ON e.type = t.id
        WHERE e.user_id = '$userid'
        ORDER BY e.date DESC";

                        $result = mysqli_query($conn, $sql);

                        // Check if there are any results
                        if (mysqli_num_rows($result) > 0) {
                            // Loop through each row
                            while ($row = mysqli_fetch_assoc($result)) {
                                // Extract data from the current row
                                $eventID = $row['eventID'];
                                $location = $row['location_name'];
                                $date = $row['date'];

                                // Generate HTML structure for each event
                                echo '<div class="bg-white event shadow-md rounded-md aa p-3 mb-4 event border-b-orange">';
                                echo '<div class="flex justify-between gap-3 mb-2">';
                                echo '<h4 class="font-bold sm:text-lg text-md text-orange">' . $location . '</h4>';
                                echo '<div>';
                                echo '<div class="flex items-center gap-1"><a href="create-event.php?eventID=' . $eventID . '" class="px-2 text-xs py-1 rounded bg-blue-700 hover:bg-blue-800 text-white"><i class="fa fa-edit"></i></a> <a onclick="return confirm(\'Do you really want to delete this event?\');" href="?delete=' . $eventID . '" class="px-2 text-xs py-1 rounded bg-red-700 hover:bg-red-800 text-white"><i class="fa fa-trash"></i></a></div>';
                                echo '</div>';
                                echo '</div>';
                                echo '<div class="flex items-center gap-3 justify-between">';
                                echo '<div class="flex items-center gap-2 text-xs text-gray-700"><span><i class="fa fa-calendar"></i></span><span>' . $date . '</span></div>';
                                echo '<div>';
                                echo '<a href="event-details.php?eventID=' . $eventID . '" class="px-2 text-xs py-1 rounded bg-blue-700 hover:bg-blue-800 text-white font-medium">Details <i class="fa fa-arrow-right"></i></a>';
                                echo '</div>';
                                echo '</div>';
                                echo '</div>';
                            }
                        } else {
                            // No events found
                            echo '<div class="px-3 py-2 mb-4 bg-red-200 border-red-800 border text-red-800 rounded">No events found!</div>';
                        }

                        ?>
                    </div>
                </div>
            </main>
        </div>
        <script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>
        <script src="assets/js/script.js?v=7"></script>
        <script>
            $(document).ready(function () {
                $('#time_period').change(function () {
                    if ($(this).val() === 'custom') {
                        $('#custom_date').show();
                    } else {
                        $('#custom_date').hide();
                        $('#shotsForm').submit();
                    }
                });

                $('#start_date').change(function () {
                    if ($('#start_date').val()) {
                        $('#shotsForm').submit();
                    }
                });
            });
        </script>
    </body>

</html>