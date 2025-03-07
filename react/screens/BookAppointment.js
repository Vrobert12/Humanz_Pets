import React, { useEffect, useState } from 'react';
import { View, Text, TouchableOpacity, Alert, ScrollView } from 'react-native';
import { Picker } from '@react-native-picker/picker';
import { Calendar } from "react-native-calendars";
import axios from 'axios'; // Install this package
import AsyncStorage from '@react-native-async-storage/async-storage';

const API_URL = 'http://192.168.1.8/Humanz2.0/Humanz_Pets/bookReact.php';
const API_URL2 = 'http://192.168.1.8/Humanz2.0/Humanz_Pets/getPets2.php';
const API_URL_CHECK_AVAILABILITY = 'http://192.168.1.8/Humanz2.0/Humanz_Pets/checkAvailability.php'; // Add this URL

const ReservationForm = ({ navigation }) => {
    const [petId, setPetId] = useState('');
    const [vetId, setVetId] = useState('');
    const [reservationDate, setReservationDate] = useState('');
    const [reservationStart, setReservationStart] = useState('');
    const [reservationEnd, setReservationEnd] = useState('');
    const [loading, setLoading] = useState(false);
    const [availableStartTimes, setAvailableStartTimes] = useState([]);
    const [selectedValue, setSelectedValue] = useState('');
    const [petOptions, setPetOptions] = useState([]);
    const [selectedDate, setSelectedDate] = useState('');
    let selectedDate2 = new Date();

    useEffect(() => {
        const loadPets = async () => {
            try {
                const storedPets = await AsyncStorage.getItem('pets');
                if (storedPets) {
                    const parsedPets = JSON.parse(storedPets);

                    const options = parsedPets.map((pet) => ({
                        value: pet.petId.toString(),
                        label: pet.petName,
                        vetId: pet.veterinarId
                    }));

                    setPetOptions(options);
                }
            } catch (error) {
                console.error('Error loading pets:', error);
            }
        };

        loadPets();
    }, []);

    // Function to load available start times
    const loadAvailableStartTimes = async (selectedDate) => {
        try {
            const response = await axios.post(API_URL_CHECK_AVAILABILITY, {
                date: selectedDate,
            });

            if (response.data.availableStartTimes) {
                setAvailableStartTimes(response.data.availableStartTimes);
            } else {
                Alert.alert('Error', 'No available time slots for this date.');
            }
        } catch (error) {
            console.error('Error checking availability:', error);
            Alert.alert('Error', 'There was an error checking availability.');
        }
    };

    const HandleDateSelect = (date) => {
        setSelectedDate(date.dateString);
        setReservationDate(date.dateString);
        loadAvailableStartTimes(date.dateString); // Load available start times when a date is selected
    };

    const HandleSubmit = async () => {
        if (petId && vetId && reservationDate && reservationStart && reservationEnd) {
            setLoading(true);

            let formData = new FormData();
            formData.append('pet_id', petId);
            formData.append('vet_id', vetId);
            formData.append('date', reservationDate);
            formData.append('start', reservationStart);
            formData.append('end', reservationEnd);

            try {
                const response = await axios.post(API_URL, formData, {
                    headers: { 'Content-Type': 'multipart/form-data' },
                });
                setLoading(false);
                Alert.alert('Success', response.data.message);
                navigation.goBack();
            } catch (error) {
                setLoading(false);
                Alert.alert('Error', 'An error occurred. Please try again later.');
            }
        } else {
            Alert.alert('Error', 'Please fill all the fields.');
        }
    };

    const HandlePetSelection = (petId) => {
        setPetId(petId);
        // Find the selected pet and set the vetId
        const selectedPet = petOptions.find((pet) => pet.value === petId);
        console.log(selectedPet);
        setVetId(selectedPet ? selectedPet.vetId : null);
    };

    return (
        <ScrollView style={{ padding: 20 }}>
            <View>
                <Text>Select a Date:</Text>
                <Calendar
                    onDayPress={HandleDateSelect}
                    minDate={new Date().toISOString().split('T')[0]} // Disable past dates
                    markedDates={{
                        [selectedDate]: {
                            selected: true,
                            selectedColor: '#007bff', // Highlight color
                            selectedTextColor: '#fff', // Text color when selected
                        },
                    }}
                />
                <Text>You've selected: {reservationDate}</Text>

                {/* Pet Selection ComboBox */}
                <Text>Choose your pet:</Text>
                <Picker
                    selectedValue={petId}
                    onValueChange={HandlePetSelection}
                >
                    <Picker.Item label="Select Pet..." value="" />
                    {petOptions.map((pet) => (
                        <Picker.Item key={pet.value} label={pet.label} value={pet.value} />
                    ))}
                </Picker>

                {/* Reservation Start Time ComboBox */}
                <Text>Choose start time:</Text>
                <Picker
                    selectedValue={reservationStart}
                    onValueChange={(itemValue) => {
                        setReservationStart(itemValue);
                        setReservationEnd(`${parseInt(itemValue.split(':')[0]) + 1}:00`); // Auto-set end time by adding 1 hour
                    }}
                >
                    <Picker.Item label="Select Start Time..." value="" />
                    {availableStartTimes.map((time) => (
                        <Picker.Item key={time} label={time} value={time} />
                    ))}
                </Picker>

                {/* Reservation End Time will be auto-set based on Start */}
                <Text>End Time: {reservationEnd}</Text>

                <TouchableOpacity
                    onPress={HandleSubmit}
                    style={{
                        backgroundColor: '#007bff',
                        paddingVertical: 10,
                        paddingHorizontal: 20,
                        borderRadius: 5,
                        alignItems: 'center',
                        marginBottom: 30
                    }}
                    disabled={loading}
                >
                    <Text style={{ color: '#fff', fontSize: 16 }}>
                        {loading ? 'Submitting...' : 'Book Reservation'}
                    </Text>
                </TouchableOpacity>
            </View>
        </ScrollView>
    );
};

export default ReservationForm;
