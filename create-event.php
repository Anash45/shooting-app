<?php
require './db_conn.php';
if (!isUserLoggedIn()) {
    header('location:index.php');
    exit;
}
$page = 'create_edit_event';
$userID = $_SESSION['user_id'];
// Check if eventID is set in the URL
if (isset($_GET['eventID'])) {
    $currentEventID = intval($_GET['eventID']);

    // If the form is submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_event'])) {
        // print_r($_POST);
        // Retrieve and sanitize form data
        $updateEventID = $currentEventID; // This is safe because it's explicitly set
        $updateType = !empty($_POST['type']) ? $_POST['type'] : NULL;
        $updateDate = date('Y-m-d', strtotime($_POST['date']));
        $updateLocation = !empty($_POST['location']) ? $_POST['location'] : NULL;
        $updateWeather = !empty($_POST['weather']) ? $_POST['weather'] : NULL;
        $updateAmmo = !empty($_POST['ammo']) ? $_POST['ammo'] : NULL;
        $updatePOI = !empty($_POST['poi']) ? $_POST['poi'] : NULL;
        $updateGlasses = !empty($_POST['glasses']) ? $_POST['glasses'] : NULL;
        $updateEars = !empty($_POST['ears']) ? $_POST['ears'] : NULL;
        $updateTotalShots = intval($_POST['totalShots']);

        // Get the current timestamp for createdAt and updatedAt
        $updateTimestamp = date('Y-m-d H:i:s');

        // Begin transaction
        mysqli_begin_transaction($conn);

        try {
            // Update the event details in the events table
            $updateEventQuery = "
                UPDATE events 
                SET type = ?, date = ?, location = ?, weather = ?, ammo = ?, poi = ?, glasses = ?, ears = ?, totalShots = ?, updatedAt = ? 
                WHERE eventID = ?";

            $stmtUpdateEvent = mysqli_prepare($conn, $updateEventQuery);
            mysqli_stmt_bind_param($stmtUpdateEvent, 'isisiiiiisi', $updateType, $updateDate, $updateLocation, $updateWeather, $updateAmmo, $updatePOI, $updateGlasses, $updateEars, $updateTotalShots, $updateTimestamp, $updateEventID);
            mysqli_stmt_execute($stmtUpdateEvent);
            mysqli_stmt_close($stmtUpdateEvent);

            // Delete existing rounds for the event
            $deleteRoundsQuery = "DELETE FROM rounds WHERE eventID = ?";
            $stmtDeleteRounds = mysqli_prepare($conn, $deleteRoundsQuery);
            mysqli_stmt_bind_param($stmtDeleteRounds, 'i', $updateEventID);
            mysqli_stmt_execute($stmtDeleteRounds);
            mysqli_stmt_close($stmtDeleteRounds);

            // Insert new rounds
            $insertRoundQuery = "INSERT INTO rounds (eventID, round_type, rounds, createdAt, updatedAt) VALUES (?, ?, ?, ?, ?)";
            $stmtInsertRound = mysqli_prepare($conn, $insertRoundQuery);
            $createdTimestamp = $updateTimestamp; // For new entries, createdAt and updatedAt are the same

            // Insert singles rounds
            if (!empty($_POST['singles'])) {
                foreach ($_POST['singles'] as $singleRound) {
                    $singleRoundsValue = intval($singleRound);
                    $roundType = 'singles';
                    mysqli_stmt_bind_param($stmtInsertRound, 'isiss', $updateEventID, $roundType, $singleRoundsValue, $createdTimestamp, $updateTimestamp);
                    mysqli_stmt_execute($stmtInsertRound);
                }
            }

            // Insert handicap rounds
            if (!empty($_POST['handicaps'])) {
                foreach ($_POST['handicaps'] as $handicapRound) {
                    $handicapRoundsValue = intval($handicapRound);
                    $roundType = 'handicap';
                    mysqli_stmt_bind_param($stmtInsertRound, 'isiss', $updateEventID, $roundType, $handicapRoundsValue, $createdTimestamp, $updateTimestamp);
                    mysqli_stmt_execute($stmtInsertRound);
                }
            }

            // Insert doubles rounds
            if (!empty($_POST['doubles'])) {
                foreach ($_POST['doubles'] as $doubleRound) {
                    $doubleRoundsValue = intval($doubleRound);
                    $roundType = 'doubles';
                    mysqli_stmt_bind_param($stmtInsertRound, 'isiss', $updateEventID, $roundType, $doubleRoundsValue, $createdTimestamp, $updateTimestamp);
                    mysqli_stmt_execute($stmtInsertRound);
                }
            }

            mysqli_stmt_close($stmtInsertRound);

            // Commit transaction
            mysqli_commit($conn);

            // Display success message
            array_push($_SESSION['notifications'], array('success' => "Event and rounds updated successfully."));
            // Optionally, you could redirect to another page
            // header('Location: success_page.php');
        } catch (Exception $e) {
            // Rollback transaction on error
            mysqli_rollback($conn);
            array_push($_SESSION['notifications'], array('error' => "Error updating event: " . $e->getMessage() . ""));
        }
    }

    // Fetch event details from the database
    $fetchEventQuery = "SELECT * FROM events WHERE eventID = ?";
    $stmtFetchEvent = mysqli_prepare($conn, $fetchEventQuery);
    mysqli_stmt_bind_param($stmtFetchEvent, 'i', $currentEventID);
    mysqli_stmt_execute($stmtFetchEvent);
    $eventResult = mysqli_stmt_get_result($stmtFetchEvent);

    $eventDetails = [];
    if ($eventResult && mysqli_num_rows($eventResult) > 0) {
        $eventDetails = mysqli_fetch_assoc($eventResult);
    } else {
        array_push($_SESSION['notifications'], array('error' => "Event not found."));
        exit;
    }
    mysqli_stmt_close($stmtFetchEvent);

    // Fetch rounds details for the event
    $fetchRoundsQuery = "SELECT * FROM rounds WHERE eventID = ?";
    $stmtFetchRounds = mysqli_prepare($conn, $fetchRoundsQuery);
    mysqli_stmt_bind_param($stmtFetchRounds, 'i', $currentEventID);
    mysqli_stmt_execute($stmtFetchRounds);
    $roundsResult = mysqli_stmt_get_result($stmtFetchRounds);

    $roundsDetails = [];
    if ($roundsResult && mysqli_num_rows($roundsResult) > 0) {
        while ($roundRow = mysqli_fetch_assoc($roundsResult)) {
            $roundsDetails[] = $roundRow;
        }
    }
    mysqli_stmt_close($stmtFetchRounds);
} elseif ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['add_event'])) {
    // Main fields from $_POST array
    $userID = $_SESSION['user_id'];
    $type = $_POST['type'];
    $date = date('Y-m-d', strtotime($_POST['date']));
    $location = $_POST['location'];
    $weather = !empty($_POST['weather']) ? $_POST['weather'] : NULL;
    $totalShots = intval($_POST['totalShots']);
    $ammo = !empty($_POST['ammo']) ? $_POST['ammo'] : NULL;
    $poi = !empty($_POST['poi']) ? $_POST['poi'] : NULL;
    $glasses = !empty($_POST['glasses']) ? $_POST['glasses'] : NULL;
    $ears = !empty($_POST['ears']) ? $_POST['ears'] : NULL;

    try {

        // Prepare the SQL statement with placeholders
        $stmt = mysqli_prepare($conn, "INSERT INTO events (type, date, location, weather, ammo, poi, glasses, ears, totalShots, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        // Bind the parameters
        mysqli_stmt_bind_param($stmt, "isisiiiiii", $type, $date, $location, $weather, $ammo, $poi, $glasses, $ears, $totalShots, $userID);
        mysqli_stmt_execute($stmt);

        // Get the last inserted eventID
        $currentEventID = mysqli_insert_id($conn);

        // Singles, handicaps, and doubles from $_POST array
        $singles = isset($_POST['singles']) ? $_POST['singles'] : [];
        $handicaps = isset($_POST['handicaps']) ? $_POST['handicaps'] : [];
        $doubles = isset($_POST['doubles']) ? $_POST['doubles'] : [];

        // Insert singles into rounds table
        foreach ($singles as $shots) {
            $stmt = mysqli_prepare($conn, "INSERT INTO rounds (eventID, round_type, rounds) VALUES (?, 'Singles', ?)");
            mysqli_stmt_bind_param($stmt, "is", $currentEventID, $shots);
            mysqli_stmt_execute($stmt);
        }

        // Insert handicaps into rounds table
        foreach ($handicaps as $shots) {
            $stmt = mysqli_prepare($conn, "INSERT INTO rounds (eventID, round_type, rounds) VALUES (?, 'Handicap', ?)");
            mysqli_stmt_bind_param($stmt, "is", $currentEventID, $shots);
            mysqli_stmt_execute($stmt);
        }

        // Insert doubles into rounds table
        foreach ($doubles as $shots) {
            $stmt = mysqli_prepare($conn, "INSERT INTO rounds (eventID, round_type, rounds) VALUES (?, 'Doubles', ?)");
            mysqli_stmt_bind_param($stmt, "is", $currentEventID, $shots);
            mysqli_stmt_execute($stmt);
        }

        if (!isset($_SESSION['notifications'])) {
            $_SESSION['notifications'] = array();
        }

        // Push a notification into $_SESSION['notifications']
        array_push($_SESSION['notifications'], array('success' => 'Event created successfully'));
    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($conn);
        array_push($_SESSION['notifications'], array('error' => "Error creating event: " . $e->getMessage() . ""));
    }
}
// print_r($_POST);
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['title']) && !empty($_POST['description'])) {

    // echo 1;
    if (isset($currentEventID)) {
        // Get other form data
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);

        // Initialize file handling variables
        $fileUploaded = false;
        $newFileName = '';

        // Check if a file is uploaded
        if (!empty($_FILES['file-upload']['name'])) {
            // File upload handling
            $uploadDir = 'uploads/';
            $file = $_FILES['file-upload'];
            $fileName = basename($file['name']);
            $fileTmpPath = $file['tmp_name'];
            $fileSize = $file['size'];
            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            $allowedExtensions = ['pdf', 'png', 'jpg', 'jpeg', 'svg', 'gif'];
            $errors = [];

            // Validate file extension
            if (!in_array($fileExt, $allowedExtensions)) {
                $errors[] = 'Invalid file type. Only PDF, PNG, JPG, JPEG, SVG, and GIF files are allowed.';
            }
            // Validate file size (5 MB max)
            if ($fileSize > 5 * 1024 * 1024) { // 5 MB
                $errors[] = 'File size should not exceed 5 MB.';
            }

            // If no errors, proceed with the file upload
            if (empty($errors)) {
                // Generate a unique file name to avoid overwriting
                $newFileName = uniqid('file_', true) . '.' . $fileExt;
                $destPath = $uploadDir . $newFileName;

                // Move the file to the desired directory
                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    $fileUploaded = true;
                } else {
                    $errors[] = 'Error uploading file.';
                }
            }
        }

        // Check for errors before proceeding
        if (empty($errors)) {
            // Prepare the query based on whether a file was uploaded
            $createdAt = date('Y-m-d H:i:s');
            if ($fileUploaded) {
                $query = "INSERT INTO notes (userID, eventID, title, description, file, created_at, updated_at)
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, 'iisssss', $userID, $currentEventID, $title, $description, $newFileName, $createdAt, $createdAt);
            } else {
                $query = "INSERT INTO notes (userID, eventID, title, description, created_at, updated_at)
                      VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, 'iissss', $userID, $currentEventID, $title, $description, $createdAt, $createdAt);
            }

            // Execute the query
            if (mysqli_stmt_execute($stmt)) {
                if (isset($_GET['eventID'])) {
                    array_push($_SESSION['notifications'], array('success' => 'Event updated & note added successfully'));
                } else {
                    array_push($_SESSION['notifications'], array('success' => 'Event created & note added successfully'));
                }
                header('location:event-details.php?eventID='.$currentEventID);
                exit;
            } else {
                $info = "<div class='px-3 py-2 mb-3 bg-red-200 border-red-800 text-red-800 border rounded'>Error: " . mysqli_error($conn) . "</div>";
            }

            mysqli_stmt_close($stmt);
        } else {
            // Display errors
            foreach ($errors as $error) {
                array_push($_SESSION['notifications'], array('error' => $error));
            }
        }
    } else {
        $info = "<div class='px-3 py-2 mb-3 bg-red-200 border-red-800 text-red-800 border rounded'>A note cannot be added without adding an event.</div>";
    }
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
        <link href="dist/custom.css?v=5" rel="stylesheet">
        <script>
            function populateForm() {
                <?php if (isset($eventDetails)): ?>
                    $('#type').val("<?php echo ($eventDetails['type']); ?>").trigger('change');
                    $('#date').val("<?php echo (date('m/d/Y', strtotime($eventDetails['date']))); ?>");
                    $('#totalShots').val("<?php echo ($eventDetails['totalShots']); ?>");
                    updateTotalShots();
                    $('#location').val("<?php echo $eventDetails['location']; ?>").trigger('change');
                    $('#weather').val("<?php echo ($eventDetails['weather']); ?>");
                    $('#ammo').val("<?php echo ($eventDetails['ammo']); ?>").trigger('change');
                    $('#poi').val("<?php echo ($eventDetails['poi']); ?>").trigger('change');
                    $('#glasses').val("<?php echo ($eventDetails['glasses']); ?>").trigger('change');
                    $('#ears').val("<?php echo ($eventDetails['ears']); ?>").trigger('change');

                    <?php foreach ($roundsDetails as $roundRow): ?>
                        addRound("<?php echo $roundRow['round_type']; ?>", <?php echo $roundRow['rounds']; ?>);
                    <?php endforeach; ?>
                <?php endif; ?>
            }

            // Call populateForm when the page loads
            window.onload = populateForm;
        </script>
    </head>

    <body class="home-bg pb-10 flex flex-col justify-center"
        style="background-image: url(./assets/images/<?php echo getBgImg(); ?>);">
        <?php
        include ('./header.php');
        ?>
        <main class="mt-10">
            <form action="" method="POST" oninput="validateFields()">
                <div class="tab-content active" id="tab1">
                    <div
                        class="bg-white shadow-md rounded border-b-orange px-8 pt-6 pb-8 mb-4 max-w-lg glass w-full mx-auto">
                        <h2 class="text-center text-2xl font-bold mb-4">
                            <?php echo $pageTitle = isset($_GET['eventID']) ? 'Update Event' : 'Create Event'; ?>
                        </h2>
                        <?php echo $info; ?>
                        <!-- Type (Drop Down) -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="type">Type <span
                                    class="text-red-600">*</span></label>
                            <select id="type" name="type" class="form-select2 w-full border p-2">
                                <option value="">Select Type</option>
                                <?php
                                foreach ($dbTypes as $dbType) {
                                    echo '<option value="' . $dbType['id'] . '">' . $dbType['name'] . '</option>';
                                }
                                ?>
                            </select>
                            <p class="inp-error">Please select a type.</p>
                        </div>
                        <!-- Date (Clickable) -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="date">Date <span
                                    class="text-red-600">*</span></label>
                            <input id="date" name="date" type="text" class="datepicker form-input border p-1 w-full">
                            <p class="inp-error">Please select a date.</p>
                        </div>
                        <!-- Location (Drop Down) -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="location">Location <span
                                    class="text-red-600">*</span></label>
                            <select id="location" name="location" class="form-select2 w-full border p-2">
                                <option value="">Select Location</option>
                                <?php
                                foreach ($dbLocations as $dbLocation) {
                                    echo '<option value="' . $dbLocation['id'] . '">' . $dbLocation['location'] . '</option>';
                                }
                                ?>
                            </select>
                            <p class="inp-error">Please select a location.</p>
                        </div>
                        <!-- Weather (Text Box) -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="weather">Weather </label>
                            <input id="weather" name="weather" type="text" class="form-input w-full border p-1 rounded">
                            <p class="inp-error">Please enter the weather.</p>
                        </div>
                        <!-- Ammo (Drop Down) -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="ammo">Ammo </label>
                            <select id="ammo" name="ammo" class="form-select2 w-full border p-2">
                                <option value="">Select Ammo</option>
                                <?php
                                foreach ($dbAmmos as $dbAmmo) {
                                    echo '<option value="' . $dbAmmo['id'] . '">' . $dbAmmo['name'] . '</option>';
                                }
                                ?>
                            </select>
                            <p class="inp-error">Please select an ammo type.</p>
                        </div>
                        <!-- POI (Drop Down) -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="poi">POI </label>
                            <select id="poi" name="poi" class="form-select2 w-full border p-2">
                                <option value="">Select POI</option>
                                <?php
                                foreach ($dbPOIs as $dbPOI) {
                                    echo '<option value="' . $dbPOI['id'] . '">' . $dbPOI['name'] . '</option>';
                                }
                                ?>
                            </select>
                            <p class="inp-error">Please select a POI.</p>
                        </div>
                        <!-- Glasses (Drop Down) -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="glasses">Glasses </label>
                            <select id="glasses" name="glasses" class="form-select2 w-full border p-2">
                                <option value="">Select Glasses</option>
                                <?php
                                foreach ($dbGlasses as $dbGlasse) {
                                    echo '<option value="' . $dbGlasse['id'] . '">' . $dbGlasse['name'] . '</option>';
                                }
                                ?>
                            </select>
                            <p class="inp-error">Please select glasses.</p>
                        </div>
                        <!-- Ears (Drop Down) -->
                        <div class="mb-6">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="ears">Ears </label>
                            <select id="ears" name="ears" class="form-select2 w-full border p-2">
                                <option value="">Select Ear Protection</option>
                                <?php
                                foreach ($dbEars as $dbEar) {
                                    echo '<option value="' . $dbEar['id'] . '">' . $dbEar['type'] . '</option>';
                                }
                                ?>
                            </select>
                            <p class="inp-error">Please select ear protection.</p>
                        </div>
                        <div class="flex items-center justify-between">
                            <button
                                class="btn-create text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline nav-tab"
                                type="button" id="firstBtn" data-target="tab2">Next <i
                                    class="fa fa-arrow-right"></i></button>
                        </div>
                    </div>
                </div>
                <div class="tab-content" id="tab2">
                    <div
                        class="bg-white shadow-md rounded-lg px-8 pt-6 pb-8 border-b-orange mb-4 max-w-lg glass w-full mx-auto">
                        <h2 class="text-center text-2xl font-bold mb-4">Rounds</h2>
                        <fieldset class="py-4">
                            <div class="relative">
                                <legend class="font-bold text-center block mb-3 text-white">Total Shots Fired</legend>
                            </div>
                            <div class="mb-4">
                                <label for="totalShots" class="block text-gray-700 text-sm font-bold mb-2">Total
                                    Shot</label>
                                <input type="number" id="totalShots" value="0" name="totalShots" min="0" max="600"
                                    class="form-input w-full border p-1 rounded">
                                <div id="totalShotsValue" class="text-center mt-2 text-blue-600 text-xl font-bold">0
                                </div>
                                <p class="inp-error"></p>
                            </div>
                        </fieldset>
                        <fieldset class="py-4">
                            <div class="relative">
                                <legend class="font-bold text-center block mb-3 text-white">Singles</legend>
                            </div>
                            <div id="singles" class="rounds-cont">
                            </div>
                            <button type="button"
                                class="text-white bg-blue-600 px-2 py-1 rounded-md hover:bg-blue-700 text-xs"
                                onclick="addRound('singles')"><i class="fa fa-plus"></i> Add Round</button>
                        </fieldset>
                        <fieldset class="py-4">
                            <div class="relative">
                                <legend class="font-bold text-center block mb-3 text-white">Handicap</legend>
                            </div>
                            <div id="handicap" class="rounds-cont">
                            </div>
                            <button type="button"
                                class="text-white bg-blue-600 px-2 py-1 rounded-md hover:bg-blue-700 text-xs"
                                onclick="addRound('handicap')"><i class="fa fa-plus"></i> Add Round</button>
                        </fieldset>
                        <fieldset class="py-4">
                            <div class="relative">
                                <legend class="font-bold text-center block mb-3 text-white">Doubles</legend>
                            </div>
                            <div id="doubles" class="rounds-cont">
                            </div>
                            <button type="button"
                                class="text-white bg-blue-600 px-2 py-1 rounded-md hover:bg-blue-700 text-xs"
                                onclick="addRound('doubles')"><i class="fa fa-plus"></i> Add Round</button>
                        </fieldset>
                        <div class="flex items-center justify-between">
                            <button
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline nav-tab"
                                type="button" data-target="tab1"><i class="fa fa-arrow-left"></i> Back</button>
                            <button
                                class="btn-create text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                                type="submit" id="submitBtn"
                                name="<?php echo $btnName = isset($_GET['eventID']) ? 'update_event' : 'add_event'; ?>">Submit
                                <i class="fa fa-arrow-right"></i></button>
                        </div>
                    </div>
                </div>
            </form>
        </main>
        <?php
        if (isset($_GET['eventID'])) {
            include './footer.php';
        }
        ?>
        <script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"
            integrity="sha512-2ImtlRlf2VVmiGZsjm9bEyhjGW4dU7B6TNwh/hx/iSByxNENtj3WVE6o/9Lj4TJeVXPi4bnOIMXFIJJAeufa0A=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="assets/js/script.js?v=5"></script>
        <script>
            // Initialize Select2 for dropdowns
            $('.form-select2').select2();

            // Initialize Datepicker for date input
            $('.datepicker').datepicker();


            // Function to show the tab by ID
            function showTab(tabId) {
                tabId = "#" + tabId;
                // Hide all tab contents
                $('.tab-content').removeClass('active').hide();
                // Remove active class from all tab links
                $('#nav-tabs a').removeClass('active-tab');

                // Show the selected tab content and add active class to the corresponding tab link
                $(tabId).addClass('active').show();
                $('#nav-tabs a[href="' + tabId + '"]').addClass('active-tab');

                // Log the active tab's ID
                console.log('Active Tab ID:', tabId);
            }
            // Handle tab click
            $('.nav-tab').on('click', function (e) {
                e.preventDefault(); // Prevent default link behavior

                // Check if the clicked tab is the first tab
                if ($(this).attr('id') == 'firstBtn') {
                    // Validate fields
                    if (!validateFields()) {
                        // If validation fails, stop the function here
                        return;
                    }
                }

                // Proceed with showing the tab if validation passes or if it's not 'firstBtn'
                var tabId = $(this).attr('data-target'); // Get the ID of the tab to show
                showTab(tabId); // Show the tab content
            });
            function updateTotalShots() {
                $('#totalShotsValue').text($('#totalShots').val());
            }
            $(document).ready(function () {
                // Update the displayed value as the slider is moved
                $('#totalShots').on('input', function () {
                    updateTotalShots();
                });
                checkRoundCounts();
                updateTotalShots();
            });

            function addRound(roundType, shots = '') {
                if (roundType.toLowerCase() === 'singles') {
                    // Count the number of existing singles rounds
                    const singleCount = $('#singles .single').length;

                    if (singleCount < 8) {
                        // Append new singles round HTML with updated round number
                        $('#singles').append(`
                    <div class="mb-4 single round-type">
                        <label for="singlesR${singleCount + 1}" class="text-gray-700 text-sm font-bold mb-2 flex items-center justify-between"><span>Singles Round <span class="round-count-num">${singleCount + 1}</span></span>
                        <button class="text-xs bg-red-700 text-white px-1 rounded-sm remove-btn" type="button"
                                            onclick="removeRound(event)"><i class="fa fa-minus"></i></button>
                        </label>
                        <input type="number" required id="singlesR${singleCount + 1}" name="singles[]" value="${shots}" min="0" max="25"
                            class="form-input w-full border p-1 rounded">
                        <p class="text-xs text-gray-900 p-1">0-25 singles per round.</p>
                        <p class="inp-error"></p>
                    </div>
                `);
                    }
                } else if (roundType.toLowerCase() === 'doubles') {
                    // Count the number of existing doubles rounds
                    const doubleCount = $('#doubles .double').length;

                    if (doubleCount < 4) {
                        // Append new doubles round HTML with updated round number (Placeholder example)
                        $('#doubles').append(`
                    <div class="mb-4 double round-type">
                        <label for="doublesR${doubleCount + 1}" class="text-gray-700 text-sm font-bold mb-2 flex items-center justify-between"><span>Doubles Round <span class="round-count-num">${doubleCount + 1}</span></span>
                        <button class="text-xs bg-red-700 text-white px-1 rounded-sm remove-btn" type="button"
                                            onclick="removeRound(event)"><i class="fa fa-minus"></i></button>
                        </label>
                        <input type="number" required id="doublesR${doubleCount + 1}" name="doubles[]" value="${shots}" min="0" max="50"
                            class="form-input w-full border p-1 rounded">
                        <p class="text-xs text-gray-900 p-1">0-50 doubles per round.</p>
                        <p class="inp-error"></p>
                    </div>
                `);
                    }

                } else if (roundType.toLowerCase() === 'handicap') {
                    // Count the number of existing handicap rounds
                    const handicapCount = $('#handicap .handicap').length;

                    if (handicapCount < 8) {
                        // Append new handicap round HTML with updated round number (Placeholder example)
                        $('#handicap').append(`
                    <div class="mb-4 handicap round-type">
                        <label for="handicapR${handicapCount + 1}" class="text-gray-700 text-sm font-bold mb-2 flex items-center justify-between"><span>Handicap Round <span class="round-count-num">${handicapCount + 1}</span></span>
                        <button class="text-xs bg-red-700 text-white px-1 rounded-sm remove-btn" type="button"
                                            onclick="removeRound(event)"><i class="fa fa-minus"></i></button>
                        </label>
                        <input type="number" required id="handicapR${handicapCount + 1}" name="handicaps[]" value="${shots}" min="0" max="25"
                            class="form-input w-full border p-1 rounded">
                        <p class="text-xs text-gray-900 p-1">0-25 handicap per round.</p>
                        <p class="inp-error"></p>
                    </div>
                `);
                    }
                } else {
                    console.log('Invalid round type specified.');
                }
                checkRoundCounts();
            }
            function checkRoundCounts() {
                const singlesCount = $('#singles .single').length;
                const doublesCount = $('#doubles .double').length;
                const handicapCount = $('#handicap .handicap').length;

                $('.rounds-cont .round-type').each(function () {
                    let roundNum = $(this).index();
                    $(this).find('.round-count-num').html(roundNum + 1);
                    console.log($(this), roundNum + 1);
                })

                if (singlesCount <= 8 && singlesCount > 0) {
                    $('#singles .remove-btn').show();
                } else {
                    $('#singles .remove-btn').hide();
                }
                if (handicapCount <= 8 && handicapCount > 0) {
                    $('#handicap .remove-btn').show();
                } else {
                    $('#handicap .remove-btn').hide();
                }
                if (doublesCount <= 4 && doublesCount > 0) {
                    $('#doubles .remove-btn').show();
                } else {
                    $('#doubles .remove-btn').hide();
                }
            }

            function removeRound(e) {

                let target = e.currentTarget;
                let parentRoundsCount = $(target).closest('.rounds-cont').find('.round-type').length;
                console.log(parentRoundsCount);
                if (parentRoundsCount > 0) {
                    $(target).closest('.round-type').remove();
                }
                checkRoundCounts();
            }
            function validateFields() {
                // IDs of the input fields
                var fieldIds = ['#type', '#date', '#location'];

                // Flag to track if all fields are valid
                var allValid = true;

                // Loop through each field and check if it's empty
                fieldIds.forEach(function (id) {
                    if ($(id).val().trim() === '') {
                        allValid = false;
                        $(id).siblings('.inp-error').show();
                    } else {
                        $(id).siblings('.inp-error').hide();
                    }
                });
                return allValid; // Return whether all fields are valid
            }


        </script>
    </body>

</html>