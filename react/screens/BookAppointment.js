import React, { useState } from 'react';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { Alert, Text, View, TextInput, TouchableOpacity } from 'react-native'; // Use TouchableOpacity for buttons
import CalendarComponent from '../CalendarComponent'; // Assuming this component is correct

const API_URL = 'http://192.168.1.8/Humanz2.0/Humanz_Pets/bookReact.php';
// const API_URL = 'http://192.168.43.125/Humanz2.0/Humanz_Pets/bookReact.php';

const ReservationForm = () => {
    const [petId, setPetId] = useState('');
    const [userId, setUserId] = useState('');
    const [reservationDate, setReservationDate] = useState('');
    const [reservationStart, setReservationStart] = useState('');
    const [reservationEnd, setReservationEnd] = useState('');
    const [loading, setLoading] = useState(false);
    const [selectedDate, setSelectedDate] = useState(null);

    // Handle the date select in Calendar
    const handleDateSelect = (date) => {
        setSelectedDate(date);
        setReservationDate(date.toDateString()); // Update reservation date with the selected date
    };

    // Handle form submission
    const handleSubmit = async () => {
        // You can make your API call here to submit the reservation.
        if (petId && userId && reservationDate && reservationStart && reservationEnd) {
            setLoading(true);
            try {
                // Example API call logic
                const response = await fetch(API_URL, {
                    method: 'POST',
                    body: JSON.stringify({
                        petId,
                        userId,
                        reservationDate,
                        reservationStart,
                        reservationEnd,
                    }),
                });
                const data = await response.json();
                if (data.success) {
                    Alert.alert('Reservation Successful', 'Your reservation was successfully made!');
                } else {
                    Alert.alert('Error', 'There was an issue with your reservation.');
                }
            } catch (error) {
                Alert.alert('Error', 'Something went wrong, please try again later.');
            } finally {
                setLoading(false);
            }
        } else {
            Alert.alert('Error', 'Please fill all the fields.');
        }
    };

    return (
        <View style={{ padding: 20 }}>
            <Text style={{ fontSize: 24, fontWeight: 'bold', marginBottom: 20 }}>Book a Reservation</Text>

            {/* Calendar Component */}
            <CalendarComponent onDateSelect={handleDateSelect} />
            {selectedDate && <Text>You've selected: {selectedDate.toDateString()}</Text>}

            {/* Form to fill other reservation details */}
            <View>
                <TextInput
                    style={{ height: 40, borderColor: 'gray', borderWidth: 1, marginBottom: 10, paddingLeft: 8 }}
                    placeholder="Pet ID"
                    value={petId}
                    onChangeText={(text) => setPetId(text)}
                />
                <TextInput
                    style={{ height: 40, borderColor: 'gray', borderWidth: 1, marginBottom: 10, paddingLeft: 8 }}
                    placeholder="User ID"
                    value={userId}
                    onChangeText={(text) => setUserId(text)}
                />
                <TextInput
                    style={{ height: 40, borderColor: 'gray', borderWidth: 1, marginBottom: 10, paddingLeft: 8 }}
                    placeholder="Start Time"
                    value={reservationStart}
                    onChangeText={(text) => setReservationStart(text)}
                />
                <TextInput
                    style={{ height: 40, borderColor: 'gray', borderWidth: 1, marginBottom: 10, paddingLeft: 8 }}
                    placeholder="End Time"
                    value={reservationEnd}
                    onChangeText={(text) => setReservationEnd(text)}
                />

                {/* Replaced <button> with <TouchableOpacity> */}
                <TouchableOpacity
                    onPress={handleSubmit}
                    style={{
                        backgroundColor: '#007bff',
                        paddingVertical: 10,
                        paddingHorizontal: 20,
                        borderRadius: 5,
                        alignItems: 'center',
                    }}
                    disabled={loading}
                >
                    <Text style={{ color: '#fff', fontSize: 16 }}>
                        {loading ? 'Submitting...' : 'Book Reservation'}
                    </Text>
                </TouchableOpacity>
            </View>
        </View>
    );
};

export default ReservationForm;
