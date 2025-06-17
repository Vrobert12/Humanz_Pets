const searchPetInput = document.getElementById('searchPet');
const searchEmailInput = document.getElementById('searchEmail');
const resultsDiv = document.getElementById('results');

function fetchResults() {
    const pet = searchPetInput.value.trim();
    const email = searchEmailInput.value.trim();

    const params = new URLSearchParams({
        ajax: '1',
        searchPet: pet,
        searchEmail: email
    });

    fetch('?'+params.toString())
        .then(response => response.text())
        .then(data => {
            resultsDiv.innerHTML = data;
        })
        .catch(() => {
            resultsDiv.innerHTML = '<div class="alert alert-danger">Error fetching results</div>';
        });
}

searchPetInput.addEventListener('input', fetchResults);
searchEmailInput.addEventListener('input', fetchResults);

window.onload = fetchResults;