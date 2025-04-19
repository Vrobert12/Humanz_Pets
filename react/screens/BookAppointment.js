import React, { useEffect, useState } from 'react';
import { View, Text, TouchableOpacity, Alert, ScrollView } from 'react-native';
import { Picker } from '@react-native-picker/picker';
import { Calendar } from "react-native-calendars";
import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { useTranslation } from 'react-i18next';

const API_URL = 'http://192.168.1.8/Humanz2.0/Humanz_Pets/phpForReact/bookReact.php';
const API_URL_CHECK_AVAILABILITY = 'http://192.168.1.8/Humanz2.0/Humanz_Pets/phpForReact/checkAvailability.php';

const ReservationForm = ({ navigation }) => {
    const { t } = useTranslation();

    const [petId, setPetId] = useState('');
    const [vetId, setVetId] = useState('');
    const [reservationDate, setReservationDate] = useState('');
    const [reservationStart, setReservationStart] = useState('');
    const [reservationEnd, setReservationEnd] = useState('');
    const [loading, setLoading] = useState(false);
    const [availableStartTimes, setAvailableStartTimes] = useState([]);
    const [petOptions, setPetOptions] = useState([]);
    const [selectedDate, setSelectedDate] = useState('');
    const [isLoadingPets, setIsLoadingPets] = useState(true);

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
            console.error(t('error'), error);
        } finally {
            setIsLoadingPets(false);
        }
    };

    useEffect(() => {
        loadPets();
    }, []);

    if (isLoadingPets) {
        return <Text>{t('loadingPets')}</Text>;
    }

    const loadAvailableStartTimes = async (selectedDate) => {
        try {
            const selectedDay = new Date(selectedDate).getDay();
            if (selectedDay === 0 || selectedDay === 6) {
                setAvailableStartTimes(['08:00', '09:00', '10:00', '11:00', '12:00']);
            } else {
                const response = await axios.post(API_URL_CHECK_AVAILABILITY, {
                    date: selectedDate,
                });

                if (response.data.availableStartTimes) {
                    setAvailableStartTimes(response.data.availableStartTimes);
                } else {
                    Alert.alert(t('error'), t('noAvailableTimes'));
                }
            }
        } catch (error) {
            console.error('Error checking availability:', error);
            Alert.alert(t('error'), t('errorCheckingAvailability'));
        }
    };

    const HandleDateSelect = (date) => {
        setSelectedDate(date.dateString);
        setReservationDate(date.dateString);
        loadAvailableStartTimes(date.dateString);
    };

    const HandleSubmit = async () => {
        if (petId && vetId && reservationDate && reservationStart && reservationEnd) {
            setLoading(true);

            try {
                let formData = new FormData();
                formData.append('pet_id', petId);
                formData.append('date', reservationDate);
                formData.append('start', reservationStart);
                formData.append('end', reservationEnd);
                formData.append('veterinarianId', vetId);

                const response = await axios.post(API_URL, formData, {
                    headers: { 'Content-Type': 'multipart/form-data' },
                });

                setLoading(false);

                if (response.data.message === 'Reservation successful!') {
                    Alert.alert(t('success'), t('reservationSuccess'));
                    navigation.goBack();
                } else {
                    Alert.alert(t('reservationFailed'), response.data.message);
                    navigation.goBack();
                }
            } catch (error) {
                setLoading(false);
                Alert.alert(t('error'), t('errorTryAgain'));
            }
        } else {
            Alert.alert(t('error'), t('fillAllFields'));
        }
    };

    const HandlePetSelection = (petId) => {
        setPetId(petId);
        const selectedPet = petOptions.find((pet) => pet.value === petId);
        setVetId(selectedPet ? selectedPet.vetId : null);
    };

    return (
        <ScrollView style={{ padding: 20 }}>
            <View>
                <Text>{t('selectDate')}</Text>
                <Calendar
                    onDayPress={HandleDateSelect}
                    minDate={new Date().toISOString().split('T')[0]}
                    markedDates={{
                        [selectedDate]: {
                            selected: true,
                            selectedColor: '#007bff',
                            selectedTextColor: '#fff',
                        },
                    }}
                />
                <Text>{t('selectedDate')} {reservationDate}</Text>

                <Text>{t('choosePet')}</Text>
                <Picker selectedValue={petId} onValueChange={HandlePetSelection}>
                    <Picker.Item label={t('selectPet')} value="" />
                    {petOptions.length > 0 ? (
                        petOptions.map((pet) => (
                            <Picker.Item key={pet.value} label={pet.label} value={pet.value} />
                        ))
                    ) : (
                        <Picker.Item label={t('noPets')} value="" />
                    )}
                </Picker>

                <Text>{t('chooseStartTime')}</Text>
                <Picker
                    selectedValue={reservationStart}
                    onValueChange={(itemValue) => {
                        setReservationStart(itemValue);
                        setReservationEnd(`${parseInt(itemValue.split(':')[0]) + 1}:00`);
                    }}
                >
                    <Picker.Item label={t('selectStartTime')} value="" />
                    {availableStartTimes.map((time) => (
                        <Picker.Item key={time} label={time} value={time} />
                    ))}
                </Picker>

                <Text>{t('endTime')} {reservationEnd}</Text>

                <TouchableOpacity
                    onPress={HandleSubmit}
                    style={{
                        backgroundColor: '#007bff',
                        paddingVertical: 10,
                        paddingHorizontal: 20,
                        borderRadius: 5,
                        alignItems: 'center',
                        marginBottom: 30,
                    }}
                    disabled={loading}
                >
                    <Text style={{ color: '#fff', fontSize: 16 }}>
                        {loading ? t('submitting') : t('bookReservation')}
                    </Text>
                </TouchableOpacity>
            </View>
        </ScrollView>
    );
};

export default ReservationForm;
