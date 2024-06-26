<?php
require './db_conn.php';
if (!isUserLoggedIn()) {
    header('location:index.php');
    exit;
}
if (isset($_GET['eventID'])) {
    $eventID = $_GET['eventID'];
    $userID = $_SESSION['user_id'];
    // Check if the form is submitted
    // Check if the form is submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Retrieve the userID from session and eventID from GET

        // Get other form data
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);

        // File upload handling
        $uploadDir = 'uploads/';
        $file = $_FILES['file-upload'];
        $fileName = basename($file['name']);
        $fileTmpPath = $file['tmp_name'];
        $fileSize = $file['size'];
        $fileType = $file['type'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $errors = [];

        // Check if file is a PDF and size is less than 5MB
        if ($fileExt != 'pdf') {
            $errors[] = 'Only PDF files are allowed.';
        }
        if ($fileSize > 5 * 1024 * 1024) { // 5 MB
            $errors[] = 'File size should not exceed 5 MB.';
        }

        // If no errors, proceed with the file upload and database insertion
        if (empty($errors)) {
            // Generate a unique file name to avoid overwriting
            $newFileName = uniqid('file_', true) . '.' . $fileExt;
            $destPath = $uploadDir . $newFileName;

            // Move the file to the desired directory
            if (move_uploaded_file($fileTmpPath, $destPath)) {
                // Prepare and execute the insert query
                $createdAt = date('Y-m-d H:i:s');
                $query = "INSERT INTO notes (userID, eventID, title, description, file, created_at, updated_at)
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, 'iisssss', $userID, $eventID, $title, $description, $newFileName, $createdAt, $createdAt);

                if (mysqli_stmt_execute($stmt)) {
                    array_push($_SESSION['notifications'], array('success' => 'Note added successfully'));
                    header('location:event-details.php?eventID=' . $eventID);
                    exit;
                } else {
                    $info .= "<div class='px-3 py-2 mb-3 bg-red-200 border-red-800 text-red-800 border rounded'>Error: " . mysqli_error($conn) . "</div>";
                }

                mysqli_stmt_close($stmt);
            } else {
                $info .= "<div class='px-3 py-2 mb-3 bg-red-200 border-red-800 text-red-800 border rounded'>Error uploading file.</div>";
            }
        }else{
            foreach ($errors as $error) {
                array_push($_SESSION['notifications'], array('error' => $error));
            }
        }
    }
    // Query to fetch notes for the logged-in user and specific event
    $query = "SELECT * FROM notes WHERE userID = ? AND eventID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'si', $userID, $eventID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $notes = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $notes[] = $row;
        }
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
        <link href="dist/custom.css?v=3" rel="stylesheet">
    </head>

    <body class="home-bg pb-10 flex flex-col justify-center">
        <?php
        include ('./header.php');
        ?>
        <main class="mt-10">
            <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4 max-w-lg glass w-full mx-auto">
                <h2 class="text-center text-2xl font-bold mb-4">Event Details</h2>
                <?php echo $info; ?>
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
                                if($field == "totalShots"){
                                    $field = '<span style="white-space:nowrap;">Total Shot</span>';
                                }
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
                                if (strtolower($roundType) == 'singles') {
                                    $totalRounds = 25;
                                } elseif (strtolower($roundType) == 'handicaps') {
                                    $totalRounds = 25;
                                } elseif (strtolower($roundType) == 'doubles') {
                                    $totalRounds = 50;
                                }
                                echo '<tr>';
                                echo '<th>' . ucwords($roundType) . ': </th>' . '<td>' . implode(', ', $roundValues) . '</td>';
                                echo '</tr>';
                                echo '<tr>';
                                echo '<th>Total Shot at: </th>' . '<td>' . $totalRounds * count($roundValues) . '</td>';
                                echo '</tr>';
                                echo '<tr class="last-tr">';
                                echo '<th>Total Hit: </th>' . '<td>' . array_sum($roundValues) . '</td>';
                                echo '</tr>';
                            }
                            echo '</table>';
                        }
                    }
                }
                ?>
            </div>
            <div class="container mx-auto">
                <div class="pt-10">
                    <h2 class="text-3xl text-center font-bold mb-4 text-black p-3 glass">Notes</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        <?php if (!empty($notes)): ?>
                            <?php foreach ($notes as $note): ?>
                                <div
                                    class="bg-white p-4 rounded-lg shadow-md border border-gray-200 flex flex-col justify-between note">
                                    <img src="./assets/images/thumb-pin.png" alt="Thumb Pin" class="thumb-pin">
                                    <div>
                                        <h3 class="text-lg font-semibold mb-2"><?php echo htmlspecialchars($note['title']); ?>
                                        </h3>
                                        <p class="text-gray-700 mb-4"
                                            data-full-text="<?php echo htmlspecialchars($note['description']); ?>">
                                            <?php echo substr(htmlspecialchars($note['description']), 0, 100); ?>
                                            <?php if (strlen($note['description']) > 100): ?>
                                                <span class="read-more text-blue-600 cursor-pointer hover:underline">...Read
                                                    more</span>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                    <?php if (!empty($note['file'])): ?>
                                        <a href="uploads/<?php echo htmlspecialchars($note['file']); ?>" target="_blank"
                                            class="mt-2 inline-flex items-center text-blue-600 hover:underline">
                                            <i class="fa fa-file mr-2"></i>
                                            <span>View PDF</span>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="px-3 py-2 w-full mx-auto mb-3 bg-red-200 border-red-800 text-red-800 border rounded">No notes available for this event.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
        <?php
        include 'footer.php';
        ?>
        <script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"
            integrity="sha512-2ImtlRlf2VVmiGZsjm9bEyhjGW4dU7B6TNwh/hx/iSByxNENtj3WVE6o/9Lj4TJeVXPi4bnOIMXFIJJAeufa0A=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="assets/js/script.js?v=3"></script>
    </body>

</html>