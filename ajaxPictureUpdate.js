function updateImagePreview(event) {
    // Get the selected file from the input
    var file = event.target.files[0];
    if (file) {
        var reader = new FileReader(); // Create a FileReader object
        reader.onload = function (e) {
            // Update the img source with the selected file
            document.getElementById('productImage').src = e.target.result;
        };
        reader.readAsDataURL(file); // Read the file as a Data URL
    }
}
document.addEventListener('DOMContentLoaded', function () {
    // Function to handle image preview update
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

    // Attach the updateImagePreview function to the file input's change event
    document.getElementById('pictureInput').addEventListener('change', updateImagePreview);
});
