function confirmLogout(event) {
    event.preventDefault();
    let isConfirmed;

    if (lang === 'en') {
        isConfirmed = confirm("Are you sure you want to log out?");
    } else if (lang === 'sr') {
        isConfirmed = confirm("Da li ste sigurni da želite da se odjavite?");
    } else if (lang === 'hu') {
        isConfirmed = confirm("Biztos, hogy ki akar jelentkezni?");
    } else {
        isConfirmed = confirm("Are you sure you want to log out?");
    }

    if (isConfirmed) {
        window.location.href = event.currentTarget.href;
    }
}
