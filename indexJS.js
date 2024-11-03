function activateProfilePicture() {
    // Trigger click event on the file input element
    document.getElementById('pictureInput').click();
}


function activateSubmit() {
    // Activate the submit button when a file is selected
    document.getElementById('submitButton').click();
}

function logoutAndRedirect() {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'functions.php', true);
    xhr.onload = function () {
        if (xhr.status === 200) {
            // Redirect to MainPage.php after successful logout
            window.location.href = 'index.php';
        } else {
            // Handle logout error
            console.error('Logout failed with status ' + xhr.status);
        }
    };
    xhr.send();
}
