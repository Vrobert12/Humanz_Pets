import React, { useState } from 'react';
import { Calendar } from 'react-native-calendars';
import { Text, View } from 'react-native-web';

const CalendarComponent = ({ onDateSelect }) => {
    const [selectedDate, setSelectedDate] = useState('');

    const handleDateSelect = (date) => {
        setSelectedDate(date.dateString);
        onDateSelect(new Date(date.dateString)); // Pass selected date to the parent component
    };

    return (
        <View>
            <Text>Select a Reservation Date</Text>
            <Calendar
                onDayPress={handleDateSelect}
                minDate={new Date().toISOString().split('T')[0]} // Disable past dates
                markedDates={{
                    [selectedDate]: { selected: true, selectedColor: '#007bff' },
                }}
            />
            <Text>Selected Date: {selectedDate}</Text>
        </View>
    );
};

export default CalendarComponent;