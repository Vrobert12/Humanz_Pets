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
function confirmDeletingProduct(event) {
    event.preventDefault();
    let isConfirmed;
    console.log("confirmCheck function triggered");
    if (lang === 'en') {
        isConfirmed = confirm("Are you sure you want to delete the product?");
    } else if (lang === 'sr') {
        isConfirmed = confirm("Da li ste sigurni da želite da izbrišete proizvod?");
    } else if (lang === 'hu') {
        isConfirmed = confirm("Biztos, hogy ki akarja törölni a terméket?");
    } else {
        isConfirmed = confirm("Are you sure you want to log out?");
    }

    if (isConfirmed) {
        event.target.closest("form").submit(); // Submit the form programmatically
    }
}
function confirmDeletingCart(event) {
    event.preventDefault();
    let isConfirmed;
    console.log("confirmCheck function triggered");
    if (lang === 'en') {
        isConfirmed = confirm("Are you sure you want to remove the product from the cart?");
    } else if (lang === 'sr') {
        isConfirmed = confirm("Da li ste sigurni da želite da izbrišete proizvod iz vaše korpe?");
    } else if (lang === 'hu') {
        isConfirmed = confirm("Biztos, hogy el akarja távolítani a terméket a kosarából?");
    } else {
        isConfirmed = confirm("Are you sure you want to log out?");
    }

    if (isConfirmed) {
        event.target.closest("form").submit(); // Submit the form programmatically
    }
}
function confirmDeletingApointment(event) {
    event.preventDefault();
    let isConfirmed;
    console.log("confirmCheck function triggered");
    if (lang === 'en') {
        isConfirmed = confirm("Are you sure you want to delete the booked time?");
    } else if (lang === 'sr') {
        isConfirmed = confirm("Da li ste sigurni da želite da izbrišete rezervaciju?");
    } else if (lang === 'hu') {
        isConfirmed = confirm("Biztos, hogy ki akarja törölni az időpontot?");
    } else {
        isConfirmed = confirm("Are you sure you want to log out?");
    }

    if (isConfirmed) {
        event.target.closest("form").submit(); // Submit the form programmatically
    }
}

