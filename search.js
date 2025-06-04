document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById('searchForm');
    form.addEventListener('submit', function (event) {
        event.preventDefault(); // Megakadályozza a normál POST-ot
        performSearch(form.getAttribute('action'));
    });
});

function performSearch(site) {
    const searchTerm = document.getElementById('search').value.trim();

    if (searchTerm === "") {
        location.reload();
        return;
    }

    const formData = new FormData();
    formData.append('search', searchTerm);
    formData.append('searchAction', '1');

    fetch(site, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'  // Jelzi, hogy AJAX kérés
        }
    })
        .then(response => response.text())
        .then(data => {
            document.getElementById('list').innerHTML = data;
        })
        .catch(error => console.error('Error:', error));
}
