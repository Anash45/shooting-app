<?php
require './db_conn.php';
if (!isUserLoggedIn()) {
    header('location:index.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Main fields from $_POST array
    $userid = $_SESSION['user_id'];
    $type = $_POST['type'];
    $date = date('Y-m-d', strtotime($_POST['date']));
    $location = $_POST['location'];
    $weather = $_POST['weather'];
    $ammo = $_POST['ammo'];
    $poi = $_POST['poi'];
    $glasses = $_POST['glasses'];
    $ears = $_POST['ears'];
    // Insert main fields into events table
    $stmt = mysqli_prepare($conn, "INSERT INTO events (type, date, location, weather, ammo, poi, glasses, ears, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssssssssi", $type, $date, $location, $weather, $ammo, $poi, $glasses, $ears, $userid);
    mysqli_stmt_execute($stmt);

    // Get the last inserted eventID
    $eventID = mysqli_insert_id($conn);

    // Singles, handicaps, and doubles from $_POST array
    $singles = $_POST['singles'];
    $handicaps = $_POST['handicaps'];
    $doubles = $_POST['doubles'];

    // Insert singles into rounds table
    foreach ($singles as $shots) {
        $stmt = mysqli_prepare($conn, "INSERT INTO rounds (eventID, round_type, rounds) VALUES (?, 'Singles', ?)");
        mysqli_stmt_bind_param($stmt, "is", $eventID, $shots);
        mysqli_stmt_execute($stmt);
    }

    // Insert handicaps into rounds table
    foreach ($handicaps as $shots) {
        $stmt = mysqli_prepare($conn, "INSERT INTO rounds (eventID, round_type, rounds) VALUES (?, 'Handicap', ?)");
        mysqli_stmt_bind_param($stmt, "is", $eventID, $shots);
        mysqli_stmt_execute($stmt);
    }

    // Insert doubles into rounds table
    foreach ($doubles as $shots) {
        $stmt = mysqli_prepare($conn, "INSERT INTO rounds (eventID, round_type, rounds) VALUES (?, 'Doubles', ?)");
        mysqli_stmt_bind_param($stmt, "is", $eventID, $shots);
        mysqli_stmt_execute($stmt);
    }

    if (!isset($_SESSION['notifications'])) {
        $_SESSION['notifications'] = array();
    }

    // Push a notification into $_SESSION['notifications']
    array_push($_SESSION['notifications'], array('success' => 'Event created successfully'));
    header('location:dashboard.php?event=success');
}
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Clay Shooting</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css"
            integrity="sha512-nMNlpuaDPrqlEls3IX/Q56H36qvBASwb3ipuo3MxeWbsQB1881ox0cRv7UPTgBlriqoynt35KjEwgGUeUXIPnw=="
            crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
            integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
            crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link href="dist/styles.css" rel="stylesheet">
        <link href="dist/custom.css" rel="stylesheet">
    </head>

    <body class="home-bg py-10 flex flex-col justify-center">
        <main>
            <form action="" method="POST" oninput="validateFields()">
                <div class="tab-content active" id="tab1">
                    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4 max-w-lg glass w-full mx-auto">
                        <h2 class="text-center text-2xl font-bold mb-4">Event Details</h2>
                        <?php
                        // Fetch eventID from GET parameter
                        if (isset($_GET['eventID'])) {
                            $eventID = $_GET['eventID'];

                            // Query to fetch event details
                            $eventQuery = "SELECT * FROM events WHERE eventID = $eventID";
                            $eventResult = mysqli_query($conn, $eventQuery);

                            // Check if event exists
                            if (mysqli_num_rows($eventResult) > 0) {
                                $eventRow = mysqli_fetch_assoc($eventResult);

                                // Display event details in specified format
                                echo '<table class="w-full">';
                                foreach ($eventRow as $field => $value) {
                                    if ($field == 'eventID' || $field == 'user_id' || $field == 'createdAt' || $field == 'updatedAt') {

                                    } else {

                                        echo '<tr>';
                                        echo '<th class="p-2 text-left">' . ucwords(str_replace("_", " ", $field)) . '</th>';
                                        echo '<td class="p-2">' . $value . '</td>';
                                        echo '</tr>';
                                    }
                                }
                                echo '</table>';

                                // Query to fetch rounds for the event
                                $roundsQuery = "SELECT * FROM rounds WHERE eventID = $eventID";
                                $roundsResult = mysqli_query($conn, $roundsQuery);

                                // Check if rounds exist
                                if (mysqli_num_rows($roundsResult) > 0) {
                                    echo '<h2 class="font-bold text-xl text-center my-3">Rounds</h2>';
                                    $rounds = array();
                                    while ($roundsRow = mysqli_fetch_assoc($roundsResult)) {
                                        $roundType = $roundsRow['round_type'];
                                        $rounds[$roundType][] = $roundsRow['rounds'];
                                    }
                                    echo '<table class="w-full mt-4">';
                                    foreach ($rounds as $roundType => $roundValues) {
                                        if(strtolower($roundType) == 'singles'){
                                            $totalRounds = 25;
                                        }elseif(strtolower($roundType) == 'handicaps'){
                                            $totalRounds = 25;
                                        }elseif(strtolower($roundType) == 'doubles'){
                                            $totalRounds = 50;
                                        }
                                        echo '<tr>';
                                        echo '<th>' . $roundType . ' ('.$totalRounds.')</th>' . '<td>' . implode(', ', $roundValues) . '</td>';
                                        echo '</tr>';
                                    }
                                    echo '</table>';
                                }
                            }
                        }
                        ?>
                    </div>
                </div>
            </form>
        </main>
        <script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"
            integrity="sha512-2ImtlRlf2VVmiGZsjm9bEyhjGW4dU7B6TNwh/hx/iSByxNENtj3WVE6o/9Lj4TJeVXPi4bnOIMXFIJJAeufa0A=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="assets/js/script.js"></script>
    </body>

</html>