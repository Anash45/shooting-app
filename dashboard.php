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

if (isset($_REQUEST['delete'])) {
    $eventId = $_REQUEST['delete'];
    $sql1 = "DELETE FROM events WHERE `eventID` = '$eventId' AND `user_id` = '$userid'";
    if (mysqli_query($conn, $sql1)) {
        array_push($_SESSION['notifications'], array('success' => 'Event created successfully'));
    }
}

$info = '';
if (isset($_SESSION['notifications'])) {
    foreach ($_SESSION['notifications'] as $type => $message) {
        $classes = ($type == 'error') ? 'bg-red-200 border-red-800 text-red-800 ' : 'bg-green-200 border-green-800 text-green-800 ';
        $info .= '<div class="px-3 py-2 mb-3 ' . $classes . ' border rounded">Event is created successfully!</div>';
    }
    $_SESSION['notifications'] = array();
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
        <link href="dist/custom.css" rel="stylesheet">
    </head>

    <body class="home-bg flex flex-col">
        <div class="h-100vh glass">
            <header class="px-4 py-3 border-b-2 border-blue-900">
                <div class="container flex justify-between">
                    <div class="flex items-center gap-2">
                        <img src="<?php echo $_SESSION['user_picture']; ?>" class="w-14 h-14 rounded-full"
                            alt="Profile Picture">
                        <div class="flex flex-col">
                            <h2 class="font-bold text-lg text-blue-700"><?php echo $_SESSION['user_name']; ?></h2>
                            <h5 class="font-medium text-blue-900"><?php echo $_SESSION['user_email']; ?></h5>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="./create-event.php"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"><i
                                class="fa-regular fa-plus"></i> Create Event</a>
                        <a href="./logout.php"
                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"><i
                                class="fa fa-sign-out"></i> Logout</a>
                    </div>
                </div>
            </header>
            <main class="py-5">
                <div class="container">
                    <h3 class="text-3xl font-bold text-center text-blue-600">My Events</h3>
                    <div class="events py-10">
                        <?php echo $info; ?>
                        <?php
                        // Query to fetch event data
                        $sql = "SELECT eventID, location, date FROM events WHERE user_id = '$userid'";
                        $result = mysqli_query($conn, $sql);

                        // Check if there are any results
                        if (mysqli_num_rows($result) > 0) {
                            // Loop through each row
                            while ($row = mysqli_fetch_assoc($result)) {
                                // Extract data from the current row
                                $eventID = $row['eventID'];
                                $location = $row['location'];
                                $date = $row['date'];

                                // Generate HTML structure for each event
                                echo '<div class="bg-white event shadow-md rounded-md p-3 mb-4">';
                                echo '<div class="flex justify-between gap-3 mb-2 items-center">';
                                echo '<h4 class="font-bold text-2xl text-blue-900">' . $location . '</h4>';
                                echo '<div>';
                                echo '<a onclick="return confirm(\'Do you really want to delete this event?\');" href="?delete=' . $eventID . '" class="px-2 text-xs py-1 rounded bg-red-700 hover:bg-red-800 text-white"><i class="fa fa-trash"></i></a>';
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
        <script src="assets/js/script.js"></script>
    </body>

</html>