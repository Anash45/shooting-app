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
