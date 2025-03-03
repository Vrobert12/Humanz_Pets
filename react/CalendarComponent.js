// CalendarComponent.js
import React, { useState } from 'react';
import Calendar from 'react-calendar';
import 'react-calendar/dist/Calendar.css';
import {Text, View} from "react-native"; // Import the default styles

const CalendarComponent = ({ onDateSelect }) => {
    const [date, setDate] = useState(new Date()); // Default to current date

    const handleDateChange = (selectedDate) => {
        setDate(selectedDate);
        onDateSelect(selectedDate); // Pass selected date to the parent component
    };

    return (
        <View>
            <Text>Select a Reservation Date</Text>
            <Calendar
                onChange={handleDateChange}
                value={date}
                minDate={new Date()} // Disable past dates
            />
            <Text>Selected Date: {date.toDateString()}</Text>
        </View>
    );
};

export default CalendarComponent;
