document.addEventListener("DOMContentLoaded", function () {
    const today = new Date().toISOString().split('T')[0];
    const reservationDate = document.querySelector('[name="day"]');
    const reservationTimeStart = document.querySelector('[name="reservationTimeStart"]');
    const veterinarianId = <?= htmlspecialchars($veterinarianId) ?>;

    // Set the allowed hours (9:00 to 20:00)
    const allowedStartTime = "09:00";
    const allowedEndTime = "20:00";

    // Disable time inputs initially
    reservationTimeStart.disabled = true;

    // Populate the select options for time slots
    function populateTimeOptions() {
        const times = [];
        let currentTime = 9; // Starting from 9:00 AM
        times.push("Select time");

        // Create options for each hour between 9 and 20 (9:00 to 20:00)
        while (currentTime <= 20) {
            const timeString = (currentTime < 10 ? '0' : '') + currentTime + ":00";
            times.push(timeString);
            currentTime++;
        }

        // Populate the start time select with options
        times.forEach((time, index) => {
            const startOption = document.createElement("option");
            startOption.value = time;
            startOption.textContent = time;

            // Make the first option the "Select time" option, visible but not selectable
            if (index === 0) {
                startOption.textContent = "Select time";
                startOption.selected = true;
                startOption.disabled = true;
                startOption.hidden = true; // This makes it unselectable
            }

            reservationTimeStart.appendChild(startOption);
        });
    }

    populateTimeOptions();

    // Enable and validate inputs based on the selected date
    reservationDate.addEventListener("change", async function () {
        const selectedDate = reservationDate.value;

        if (selectedDate < today) {
            alert("You cannot select a past date.");
            reservationDate.value = '';
            reservationTimeStart.disabled = true;
            return;
        }

        // Reset time dropdown to default state
        reservationTimeStart.value = "Select time";  // Reset the time selection to "Select time"
        reservationTimeStart.disabled = true;  // Disable the time dropdown while fetching availability

        // Fetch available time slots for the selected date
        const response = await fetch(`check_availability.php?date=${selectedDate}&veterinarianId=${veterinarianId}`);
        const data = await response.json();

        // Disable unavailable time slots
        if (data.isFullyBooked) {
            alert("This date is fully booked. Please select another date.");
            reservationDate.value = '';
            reservationTimeStart.disabled = true;
        } else {
            // Enable time slots and disable the ones that are already taken
            reservationTimeStart.disabled = false;

            // Disable the reserved time slots
            const reservedTimes = data.reservedTimes; // Array of reserved times on the selected date

            // Debugging: Log reserved times
            console.log("Reserved times:", reservedTimes);

            // Iterate through the options and disable the ones that are reserved
            Array.from(reservationTimeStart.options).forEach(option => {
                // Log each option value and reserved time comparison
                console.log(`Checking option value: ${option.value}`);
                if (reservedTimes.includes(option.value)) {
                    option.disabled = true; // Disable the option if it's in reservedTimes
                    option.hidden=true;
                    console.log(`Disabled time slot: ${option.value}`);
                } else {
                    option.disabled = false; // Enable the option if it's not reserved
                    if (option.textContent !== "Select time")
                        option.hidden=false;
                }
            });
        }
    });

    // Enable and automatically calculate the end time based on start time
    reservationTimeStart.addEventListener("change", function () {
        const startTime = reservationTimeStart.value;

        if (startTime && startTime >= allowedStartTime && startTime <= allowedEndTime) {
            // Calculate the end time by adding 1 hour to the selected start time
            let endHour = parseInt(startTime.split(":")[0]) + 1;
            if (endHour > 20) endHour = 20; // Ensure end time doesn't exceed 20:00

            const endTime = (endHour < 10 ? '0' : '') + endHour + ":00"; // Format end time (e.g., 10:00)

            // Set the end time value to 1 hour later
            document.querySelector('[name="reservationTimeEnd"]').value = endTime;
        }
    });
});
