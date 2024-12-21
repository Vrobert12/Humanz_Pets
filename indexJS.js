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

function updateImagePreview(event) {
    var file = event.target.files[0]; // Get the selected file
    if (file) {
        var reader = new FileReader(); // Create a FileReader object
        reader.onload = function (e) {
            // Update the img source with the selected file
            document.getElementById('productImage').src = e.target.result;
        };
        reader.readAsDataURL(file); // Read the file as a Data URL
    }
}

function activateSubmit() {
    // Check if a file has been selected
    var fileInput = document.getElementById('pictureInput');
    var submitButton = document.getElementById('submitButton');

    // Enable the submit button only if a file is selected
    if (fileInput.files && fileInput.files[0]) {
        submitButton.disabled = false;  // Enable the submit button
    } else {
        submitButton.disabled = true;   // Keep the submit button disabled if no file is selected
    }
}

// Event listener to trigger the submit activation whenever a file is selected
document.getElementById('pictureInput').addEventListener('change', activateSubmit);

// Call activateSubmit on page load to check if the image input has already been populated
window.onload = function() {
    activateSubmit();
};
