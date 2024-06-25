function handleCredentialResponse(response) {
    const data = jwt_decode(response.credential);
    console.log(data);
    // fetch('login.php', {
    //     method: 'POST',
    //     headers: {
    //         'Content-Type': 'application/json'
    //     },
    //     body: JSON.stringify({
    //         google_id: data.sub,
    //         name: data.name,
    //         email: data.email,
    //         profile_picture: data.picture
    //     })
    // })
    //     .then(response => response.json())
    //     .then(data => {
    //         if (data.success) {
    //             // Handle successful login
    //             console.log('Login successful');
    //         } else {
    //             // Handle login failure
    //             console.log('Login failed');
    //         }
    //     })
    //     .catch(error => {
    //         console.error('Error:', error);
    //     });
}

// document.getElementById('g_id_onload').addEventListener('click', () => {
//     google.accounts.id.prompt();
// });

function hideNotesPopup() {
    $('#popup-backdrop').addClass('hidden');
    $('body').css({ "overflow-y": "auto" });
}

function showNotesPopup() {
    $('#popup-backdrop').removeClass('hidden');
    $('body').css({ "overflow-y": "hidden" });
}
$(document).ready(function () {
    $('.menu-btn').on('click', function () {
        $('header nav').toggleClass('expanded');
    })
    // Open the popup
    $('#open-popup').click(function (e) {
        e.preventDefault();
        showNotesPopup();
    });

    // Close the popup
    $('#close-popup').click(function () {
        hideNotesPopup();
    });

    // Close the popup when clicking outside of the popup (on the backdrop)
    $('#popup-backdrop').on('click', function (e) {
        // Check if the click is directly on the backdrop, not inside the popup
        if (!$(e.target).closest('#popup').length) {
            hideNotesPopup();
        }
    })

    // Form submission
    // $('#popup-form').submit(function (event) {
    //     event.preventDefault();
    //     // Handle form data here
    //     $('#popup-backdrop').addClass('hidden');
    // });
});
document.addEventListener('DOMContentLoaded', function () {
    const readMoreElements = document.querySelectorAll('.read-more');
    readMoreElements.forEach(element => {
        element.addEventListener('click', function () {
            const parentParagraph = this.parentElement;
            const fullText = parentParagraph.getAttribute('data-full-text');
            // parentParagraph.innerHTML = ;
            alert(fullText);
        });
    });
});