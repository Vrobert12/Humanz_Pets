function performSearch(site) {
    const searchTerm = document.getElementById('search').value.trim();

    if (searchTerm.trim() === "") {
        location.reload(); // Refresh the page if search is empty
        return;
    }

    const formData = new FormData();
    formData.append('search', searchTerm);
    formData.append('searchAction', '1');  // Signal that the request is a search

    fetch(site, {
        method: 'POST',
        body: formData
    })
        .then(response => response.text())
        .then(data => {
            // Replace only the search results section with the divs of users
            document.getElementById('list').innerHTML = data;
        })
        .catch(error => console.error('Error:', error));
}
