  document.addEventListener("DOMContentLoaded", function () {
        const searchInput = document.getElementById('searchVetEmail');
        const vetList = document.getElementById('vetList');

        function loadVets(query = '') {
            fetch('selectVeterinarian.php?ajax=1&searchVetEmail=' + encodeURIComponent(query))
                .then(response => response.text())
                .then(data => {
                    vetList.innerHTML = data;
                })
                .catch(error => {
                    vetList.innerHTML = '<div class="alert alert-danger">Hiba történt a keresés során.</div>';
                    console.error('Error:', error);
                });
        }

        loadVets();

        let debounceTimer;
        searchInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                loadVets(searchInput.value.trim());
            }, 300); // 300ms stabilabb
        });
    });