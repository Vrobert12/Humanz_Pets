import React, { useEffect, useState, useCallback } from 'react';
import { useFocusEffect } from '@react-navigation/native';
import { View, Text, FlatList, TouchableOpacity, Alert } from 'react-native';
import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';

const API_URL_GET = 'http://192.168.1.8/Humanz2.0/Humanz_Pets/phpForReact/getReservations.php';
const API_URL_DELETE = 'http://192.168.1.8/Humanz2.0/Humanz_Pets/phpForReact/deleteReservation.php';
//const API_URL_GET = 'http://192.168.43.125/Humanz_Pets/phpForReact/getReservations.php';
//const API_URL_DELETE = 'http://192.168.43.125/Humanz_Pets/phpForReact/deleteReservation.php';

const ReservationsScreen = () => {
    const [reservations, setReservations] = useState([]);
    const [loading, setLoading] = useState(true);

    useFocusEffect(
        useCallback(() => {
            fetchReservations(); // Reload reservations when screen is focused
        }, [])
    );

    const fetchReservations = async () => {
        try {
            const storedUserId = await AsyncStorage.getItem('user_id');
            if (!storedUserId) {
                Alert.alert('Error', 'User not found');
                return;
            }

            const response = await axios.post(API_URL_GET, { user_id: storedUserId });
            console.log(response.data.reservations);
            setReservations(response.data.reservations || []);
        } catch (error) {
            console.error('Error fetching reservations:', error);
            Alert.alert('Error', 'Could not fetch reservations');
        } finally {
            setLoading(false);
        }
    };

    const deleteReservation = async (reservationId) => {
        try {
            const response = await axios.post(API_URL_DELETE, { reservationId });
            Alert.alert(' ', response.data.message);
            if (response.data.message === 'Reservation deleted successfully') {
                setReservations(reservations.filter(res => res.reservationId !== reservationId));
                fetchReservations(); // Refresh list after deletion
            }
        } catch (error) {
            console.error('Error deleting reservation:', error);
            Alert.alert('Error', 'Could not delete reservation');
        }
    };
    // Utility function to compare dates
    const isReservationInFuture = (reservationDay) => {
        const currentDate = new Date();
        const reservationDate = new Date(reservationDay);
        return reservationDate > currentDate; // Returns true if reservation is in the future
    };

    const renderItem = ({ item }) => {
        const handleDeleteReservation = (reservationId) => {
            // Show confirmation alert before deleting the reservation
            Alert.alert(
                "Confirm Deletion",
                "Are you sure you want to delete this reservation?",
                [
                    {
                        text: "Cancel",
                        style: "cancel" // Dismisses the alert without doing anything
                    },
                    {
                        text: "OK",
                        onPress: () => deleteReservation(reservationId), // Proceed with deletion
                    }
                ]
            );
        };

        return (
            <View style={{ padding: 15, borderBottomWidth: 1, borderColor: '#ccc' }}>
                <Text style={{ fontSize: 16, fontWeight: 'bold' }}>Pet: {item.petName}</Text>
                <Text>Veterinarian: {item.vetEmail}</Text>
                <Text>Date: {item.reservationDay}</Text>
                <Text>Start Time: {item.reservationTime}</Text>
                <Text>End Time: {item.period}</Text>

                {/* Conditionally render the delete button */}
                {isReservationInFuture(item.reservationDay) && (
                    <TouchableOpacity
                        style={{
                            backgroundColor: 'red',
                            padding: 10,
                            marginTop: 10,
                            borderRadius: 5,
                            alignItems: 'center',
                        }}
                        onPress={() => handleDeleteReservation(item.reservationId)} // Handle the deletion with confirmation
                    >
                        <Text style={{ color: '#fff' }}>Delete Reservation</Text>
                    </TouchableOpacity>
                )}
            </View>
        );
    };
    return (
        <View style={{ flex: 1, padding: 20 }}>
            {loading ? <Text>Loading...</Text> : (
                <FlatList
                    data={reservations}
                    keyExtractor={(item, index) => item.id ? item.id.toString() : index.toString()}
                    renderItem={renderItem}
                />
            )}
        </View>
    );
};

export default ReservationsScreen;
