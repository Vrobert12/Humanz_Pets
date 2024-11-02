function confirmLogout(event) {
    event.preventDefault();
    const isConfirmed = confirm("Are you sure you want to log out?");
    if (isConfirmed) {
        window.location.href = event.currentTarget.href;
    }
}