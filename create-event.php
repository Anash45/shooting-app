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
    $totalShots = $_POST['totalShots'];
    $ammo = $_POST['ammo'];
    $poi = $_POST['poi'];
    $glasses = $_POST['glasses'];
    $ears = $_POST['ears'];
    // Insert main fields into events table
    $stmt = mysqli_prepare($conn, "INSERT INTO events (type, date, location, weather, ammo, poi, glasses, ears, totalShots, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssssssssii", $type, $date, $location, $weather, $ammo, $poi, $glasses, $ears, $totalShots, $userid);
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
        <link href="dist/custom.css?v=3" rel="stylesheet">
    </head>

    <body class="home-bg pb-10 flex flex-col justify-center">
        <?php
        include ('./header.php');
        ?>
        <main class="mt-10">
            <form action="" method="POST" oninput="validateFields()">
                <div class="tab-content active" id="tab1">
                    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4 max-w-lg glass w-full mx-auto">
                        <h2 class="text-center text-2xl font-bold mb-4">Create Event</h2>
                        <!-- Type (Drop Down) -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="type">Type <span
                                    class="text-red-600">*</span></label>
                            <select id="type" name="type" class="form-select2 w-full border p-2">
                                <option value="">Select Type</option>
                                <option value="Practice">Practice</option>
                                <option value="Registered">Registered</option>
                                <option value="Misc.">Misc.</option>
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
                                <option
                                    value="Watervliet Fish and Game Protective Association - 70 Rifle Range Rd, Colonie, NY 12205">
                                    Watervliet Fish and Game Protective Association - 70 Rifle Range Rd, Colonie, NY
                                    12205 </option>
                                <option value="West Albany Rod & Gun Club Inc - 100 Willoughby Dr, Albany, NY 12205">
                                    West Albany Rod & Gun Club Inc - 100 Willoughby Dr, Albany, NY 12205</option>
                                <option value="Voorheesville Rod & Gun Club - 52 Foundry Rd, Voorheesville, NY 12186">
                                    Voorheesville Rod & Gun Club - 52 Foundry Rd, Voorheesville, NY 12186</option>
                                <option value="A R Sportsman's Club. - 79 UDELL RD Reidsville, NY 12193">A R Sportsman's
                                    Club. - 79 UDELL RD Reidsville, NY 12193</option>
                                <option value="Iroquois Rod & Gun Club - 590 Feuz Rd, Schenectady, NY 12306">Iroquois
                                    Rod & Gun Club - 590 Feuz Rd, Schenectady, NY 12306</option>
                                <option value="Guan Ho Ha Fish and Game Club - 1451 Rector Rd, Scotia, NY 12302">Guan Ho
                                    Ha Fish and Game Club - 1451 Rector Rd, Scotia, NY 12302</option>
                                <option value="Elsmere Rod & Gun Club - 3131 Delaware TurnpikeVoorheesville, NY 12186">
                                    Elsmere Rod & Gun Club - 3131 Delaware TurnpikeVoorheesville, NY 12186</option>
                                <option value="NYS ATA Shooting Grounds - 7400 Bullt Street, Bridgeport NY 13030">NYS
                                    ATA Shooting Grounds - 7400 Bullt Street, Bridgeport NY 13030</option>
                                <option
                                    value="Pennsylvania State Shotgunning Association - 405 Monastery Rd, Elysburg, PA 17824">
                                    Pennsylvania State Shotgunning Association - 405 Monastery Rd, Elysburg, PA 17824
                                </option>
                                <option
                                    value="Sportsman Club Of Clifton Park - 644 Englemore Rd, Clifton Park, NY 12065">
                                    Sportsman Club Of Clifton Park - 644 Englemore Rd, Clifton Park, NY 12065</option>
                                <option value="Pine Belt Sportsman's Club - 377 Stokes Rd, Shamong, NJ 08088">Pine Belt
                                    Sportsman's Club - 377 Stokes Rd, Shamong, NJ 08088</option>
                                <option value="Hartford Gun Club - 157 S Main St, East Granby, CT 06026">Hartford Gun
                                    Club - 157 S Main St, East Granby, CT 06026</option>
                                <option
                                    value="World Shooting and Recreational Complex - 1 Main Event Ln, Sparta, IL 62286">
                                    World Shooting and Recreational Complex - 1 Main Event Ln, Sparta, IL 62286</option>
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
                                <option value="Federal Top Gun 1145fps 1 1/8 oz. Size 8">Federal Top Gun 1145fps 1 1/8
                                    oz. Size 8</option>
                                <option value="Federal Top Gun 1145fps 1 1/8 oz. Size 7.5">Federal Top Gun 1145fps 1 1/8
                                    oz. Size 7.5</option>
                                <option value="Federal Top Gun 1200ps 1 1/8 oz. Size 8">Federal Top Gun 1200ps 1 1/8 oz.
                                    Size 8</option>
                                <option value="Federal Top Gun 1200fps 1 1/8 oz. Size 7.5">Federal Top Gun 1200fps 1 1/8
                                    oz. Size 7.5</option>
                                <option value="Winchester AA 1200fps 1 1/8 oz. Size 8">Winchester AA 1200fps 1 1/8 oz.
                                    Size 8</option>
                                <option value="Winchester AA 1200fps 1 1/8 oz. Size 7.5">Winchester AA 1200fps 1 1/8 oz.
                                    Size 7.5</option>
                                <option value="Federal HOA 1200fps 1 1/8 oz. Size 7.5">Federal HOA 1200fps 1 1/8 oz.
                                    Size 7.5</option>
                                <option value="Estate 1200fps 1 1/8 oz. Size 7.5">Estate 1200fps 1 1/8 oz. Size 7.5
                                </option>
                                <option value="Remington Gun Club 1145fps 1 1/8 oz. Size 8">Remington Gun Club 1145fps 1
                                    1/8 oz. Size 8</option>
                                <option value="Remington Gun Club 1145fps 1 1/8 oz. Size 7.5">Remington Gun Club 1145fps
                                    1 1/8 oz. Size 7.5</option>
                                <option value="Fiochi Shooting Dynamics 1200fps 1 1/8 oz. Size 8">Fiochi Shooting
                                    Dynamics 1200fps 1 1/8 oz. Size 8</option>
                                <option value="Fiochi Shooting Dynamics 1200fps 1 1/8 oz. Size 7.5">Fiochi Shooting
                                    Dynamics 1200fps 1 1/8 oz. Size 7.5</option>
                                <option value="Fiochi Shooting Dynamics 1145fps 1 1/8 oz. Size 8">Fiochi Shooting
                                    Dynamics 1145fps 1 1/8 oz. Size 8</option>
                                <option value="Fiochi Shooting Dynamics 1145fps 1 1/8 oz. Size 7.5">Fiochi Shooting
                                    Dynamics 1145fps 1 1/8 oz. Size 7.5</option>
                            </select>
                            <p class="inp-error">Please select an ammo type.</p>
                        </div>
                        <!-- POI (Drop Down) -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="poi">POI </label>
                            <select id="poi" name="poi" class="form-select2 w-full border p-2">
                                <option value="">Select POI</option>
                                <option value="50/50">50/50</option>
                                <option value="60/40">60/40</option>
                                <option value="70/30">70/30</option>
                                <option value="80/20">80/20</option>
                                <option value="90/10">90/10</option>
                                <option value="110/110">110/110</option>
                                <option value="120/120">120/120</option>
                            </select>
                            <p class="inp-error">Please select a POI.</p>
                        </div>
                        <!-- Glasses (Drop Down) -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="glasses">Glasses </label>
                            <select id="glasses" name="glasses" class="form-select2 w-full border p-2">
                                <option value="">Select Glasses</option>
                                <option value="Ranger">Ranger</option>
                                <option value="Pilla 53CIK">Pilla 53CIK</option>
                                <option value="Pilla 78CIHC">Pilla 78CIHC</option>
                                <option value="Pilla15CIHC">Pilla15CIHC</option>
                            </select>
                            <p class="inp-error">Please select glasses.</p>
                        </div>
                        <!-- Ears (Drop Down) -->
                        <div class="mb-6">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="ears">Ears </label>
                            <select id="ears" name="ears" class="form-select2 w-full border p-2">
                                <option value="">Select Ear Protection</option>
                                <option value="Standard">Standard</option>
                                <option value="Passive">Passive</option>
                                <option value="Foam">Foam</option>
                                <option value="Music">Music</option>
                                <option value="Foam/Music">Foam/Music</option>
                            </select>
                            <p class="inp-error">Please select ear protection.</p>
                        </div>
                        <div class="flex items-center justify-between">
                            <button
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline nav-tab"
                                type="button" id="firstBtn" data-target="tab2">Next <i
                                    class="fa fa-arrow-right"></i></button>
                        </div>
                    </div>
                </div>
                <div class="tab-content" id="tab2">
                    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4 max-w-lg glass w-full mx-auto">
                        <h2 class="text-center text-2xl font-bold mb-4">Rounds</h2>
                        <fieldset class="py-4">
                            <div class="relative">
                                <legend class="font-bold text-center block mb-3 text-white">Total Shots Fired</legend>
                            </div>
                            <div class="mb-4">
                                <label for="totalShots" class="block text-gray-700 text-sm font-bold mb-2">Total
                                    Shots</label>
                                <input type="range" id="totalShots" value="0" name="totalShots" min="1" max="600"
                                    class="range-input mt-2 w-full">
                                <div id="totalShotsValue" class="text-center mt-2 text-blue-600 text-xl font-bold">300
                                </div>
                                <p class="inp-error"></p>
                            </div>
                        </fieldset>
                        <fieldset class="py-4">
                            <div class="relative">
                                <legend class="font-bold text-center block mb-3 text-white">Singles</legend>
                            </div>
                            <div id="singles" class="rounds-cont">
                                <div class="mb-4 single round-type">
                                    <label for="signlesR1"
                                        class="text-gray-700 text-sm font-bold mb-2 flex items-center justify-between"><span>Singles
                                            <span class="round-count-num">1</span>/8</span>
                                        <button class="text-xs bg-red-700 text-white px-1 rounded-sm remove-btn"
                                            type="button" onclick="removeRound(event)"><i
                                                class="fa fa-minus"></i></button>
                                    </label>
                                    <input type="number" required id="signlesR1" name="singles[]" min="0" max="25"
                                        class="form-input w-full border p-1 rounded">
                                    <p class="text-xs text-gray-900 p-1">0-25 singles per round.</p>
                                    <p class="inp-error"></p>
                                </div>
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
                                <div class="mb-4 handicap round-type">
                                    <label for="signlesR1"
                                        class="text-gray-700 text-sm font-bold mb-2 flex items-center justify-between"><span>Handicap
                                            Round <span class="round-count-num">1</span>/8</span>
                                        <button class="text-xs bg-red-700 text-white px-1 rounded-sm remove-btn"
                                            type="button" onclick="removeRound(event)"><i
                                                class="fa fa-minus"></i></button>
                                    </label>
                                    <input type="number" required id="signlesR1" name="handicaps[]" min="0" max="25"
                                        class="form-input w-full border p-1 rounded">
                                    <p class="text-xs text-gray-900 p-1">0-25 handicaps per round.</p>
                                    <p class="inp-error"></p>
                                </div>
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
                                <div class="mb-4 double round-type">
                                    <label for="doubleR1"
                                        class="text-gray-700 text-sm font-bold mb-2 flex items-center justify-between"><span>Doubles
                                            Round <span class="round-count-num">1</span>/4</span>
                                        <button class="text-xs bg-red-700 text-white px-1 rounded-sm remove-btn"
                                            type="button" onclick="removeRound(event)"><i
                                                class="fa fa-minus"></i></button>
                                    </label>
                                    <input type="number" required id="doubleR1" name="doubles[]" min="0" max="50"
                                        class="form-input w-full border p-1 rounded">
                                    <p class="text-xs text-gray-900 p-1">0-50 doubles per round.</p>
                                    <p class="inp-error"></p>
                                </div>
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
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                                type="submit" id="submitBtn">Submit <i class="fa fa-arrow-right"></i></button>
                        </div>
                    </div>
                </div>
            </form>
        </main>
        <script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"
            integrity="sha512-2ImtlRlf2VVmiGZsjm9bEyhjGW4dU7B6TNwh/hx/iSByxNENtj3WVE6o/9Lj4TJeVXPi4bnOIMXFIJJAeufa0A=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="assets/js/script.js?v=3"></script>
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
            $(document).ready(function () {
                // Update the displayed value as the slider is moved
                $('#totalShots').on('input', function () {
                    $('#totalShotsValue').text($(this).val());
                });
                checkRoundCounts();
            });

            function addRound(roundType) {
                if (roundType === 'singles') {
                    // Count the number of existing singles rounds
                    const singleCount = $('#singles .single').length;

                    if (singleCount < 8) {
                        // Append new singles round HTML with updated round number
                        $('#singles').append(`
                    <div class="mb-4 single round-type">
                        <label for="singlesR${singleCount + 1}" class="text-gray-700 text-sm font-bold mb-2 flex items-center justify-between"><span>Singles Round <span class="round-count-num">${singleCount + 1}</span>/8</span>
                        <button class="text-xs bg-red-700 text-white px-1 rounded-sm remove-btn" type="button"
                                            onclick="removeRound(event)"><i class="fa fa-minus"></i></button>
                        </label>
                        <input type="number" required id="singlesR${singleCount + 1}" name="singles[]" min="0" max="25"
                            class="form-input w-full border p-1 rounded">
                        <p class="text-xs text-gray-900 p-1">0-25 singles per round.</p>
                        <p class="inp-error"></p>
                    </div>
                `);
                    }
                } else if (roundType === 'doubles') {
                    // Count the number of existing doubles rounds
                    const doubleCount = $('#doubles .double').length;

                    if (doubleCount < 4) {
                        // Append new doubles round HTML with updated round number (Placeholder example)
                        $('#doubles').append(`
                    <div class="mb-4 double round-type">
                        <label for="doublesR${doubleCount + 1}" class="text-gray-700 text-sm font-bold mb-2 flex items-center justify-between"><span>Doubles Round <span class="round-count-num">${doubleCount + 1}</span>/4</span>
                        <button class="text-xs bg-red-700 text-white px-1 rounded-sm remove-btn" type="button"
                                            onclick="removeRound(event)"><i class="fa fa-minus"></i></button>
                        </label>
                        <input type="number" required id="doublesR${doubleCount + 1}" name="doubles[]" min="0" max="50"
                            class="form-input w-full border p-1 rounded">
                        <p class="text-xs text-gray-900 p-1">0-50 doubles per round.</p>
                        <p class="inp-error"></p>
                    </div>
                `);
                    }

                } else if (roundType === 'handicap') {
                    // Count the number of existing handicap rounds
                    const handicapCount = $('#handicap .handicap').length;

                    if (handicapCount < 8) {
                        // Append new handicap round HTML with updated round number (Placeholder example)
                        $('#handicap').append(`
                    <div class="mb-4 handicap round-type">
                        <label for="handicapR${handicapCount + 1}" class="text-gray-700 text-sm font-bold mb-2 flex items-center justify-between"><span>Handicap Round <span class="round-count-num">${handicapCount + 1}</span>/8</span>
                        <button class="text-xs bg-red-700 text-white px-1 rounded-sm remove-btn" type="button"
                                            onclick="removeRound(event)"><i class="fa fa-minus"></i></button>
                        </label>
                        <input type="number" required id="handicapR${handicapCount + 1}" name="handicaps[]" min="0" max="30"
                            class="form-input w-full border p-1 rounded">
                        <p class="text-xs text-gray-900 p-1">0-30 handicap per round.</p>
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