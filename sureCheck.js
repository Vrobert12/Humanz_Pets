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
function confirmCheck(event) {
    event.preventDefault();
    let isConfirmed;
    console.log("confirmCheck function triggered");
    if (lang === 'en') {
        isConfirmed = confirm("Are you sure you want to check the pet?");
    } else if (lang === 'sr') {
        isConfirmed = confirm("Da li ste sigurni da želite da predate ljubimca?");
    } else if (lang === 'hu') {
        isConfirmed = confirm("Biztos, hogy le akarja adni az állatot?");
    } else {
        isConfirmed = confirm("Are you sure you want to log out?");
    }

    if (isConfirmed) {
        event.target.closest("form").submit(); // Submit the form programmatically
    }
}
