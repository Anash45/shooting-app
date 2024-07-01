<?php
require './db_conn.php';

if(isUserLoggedIn()){
    header('location:dashboard.php');
}
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Clay Shooting</title>
        <link href="dist/styles.css?v=6" rel="stylesheet">
        <link href="dist/custom.css?v=6" rel="stylesheet">
        <script src="https://accounts.google.com/gsi/client" async defer></script>
        <!-- 816473627869-i3cqai8sstv6e0ppiq5cbq86k4rbp06l -->
         <!-- 816473627869-0v2j3calunt3mged7uvi5pk2p8b3e113 -->
        <div id="g_id_onload" data-client_id="816473627869-0v2j3calunt3mged7uvi5pk2p8b3e113"
            data-callback="handleCredentialResponse">
        </div>
    </head>

    <body class="home-bg py-10 flex flex-col justify-center" style="./assets/images/<?php echo getBgImg(); ?>">
        <div class="bg-white shadow-md rounded px-4 pt-4 border-b-orange pb-4 mb-4 flex flex-col w-fit mx-auto glass">
            <div class="flex flex-col justify-center items-center">
                <img src="./assets/images/TheClayground-01.png" alt="Logo" class="h-20 mb-3">
                <div class="g_id_signin" data-type="standard"></div>
                <div id="response">
                    
                </div>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>
        <script src="assets/js/script.js?v=6"></script>
        <script>
            // Function to handle the credential response
            function handleCredentialResponse(response) {
                const jwtToken = response.credential;

                // Log the token or send it to the backend for verification
                console.log("JWT ID Token: " + jwtToken);

                // Send the JWT token to the server via AJAX for verification
                var xhr = new XMLHttpRequest();
                xhr.open('POST', './signin.php');
                xhr.setRequestHeader('Content-Type', 'application/json');
                xhr.onload = function () {
                    console.log(xhr.responseText);
                    let response = JSON.parse(xhr.responseText);

                    if(response.status == "success"){
                        $('#response').html(`<p class="p-3 mt-3 rounded-md border-green-900 bg-green-200 text-green-900 text-sm">${response.message}</p>`);
                        window.location = response.redirect;
                    }else{
                        $('#response').html(`<p class="p-3 mt-3 rounded-md border-red-900 bg-red-200 text-red-900 text-sm">${response.message}</p>`);
                    }
                    // Handle server response here
                    console.log(JSON.parse(xhr.responseText));
                };
                xhr.send(JSON.stringify({ token: jwtToken }));
            }
        </script>
    </body>

</html>