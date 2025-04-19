import React, { useEffect, useState, useCallback } from 'react';
import { useFocusEffect } from '@react-navigation/native';
import { View, Text, FlatList, TouchableOpacity, Alert } from 'react-native';
import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { useTranslation } from 'react-i18next';

const API_URL_GET = 'http://192.168.1.8/Humanz2.0/Humanz_Pets/phpForReact/getReservations.php';
const API_URL_DELETE = 'http://192.168.1.8/Humanz2.0/Humanz_Pets/phpForReact/deleteReservation.php';

const ReservationsScreen = () => {
    const { t } = useTranslation();

    const [reservations, setReservations] = useState([]);
    const [loading, setLoading] = useState(true);

    useFocusEffect(
        useCallback(() => {
            fetchReservations();
        }, [])
    );

    const fetchReservations = async () => {
        try {
            const storedUserId = await AsyncStorage.getItem('user_id');
            if (!storedUserId) {
                Alert.alert(t('error'), t('userNotFound'));
                return;
            }

            const response = await axios.post(API_URL_GET, { user_id: storedUserId });
            setReservations(response.data.reservations || []);
        } catch (error) {
            console.error('Error fetching reservations:', error);
            Alert.alert(t('error'), t('fetchReservationsError'));
        } finally {
            setLoading(false);
        }
    };

    const deleteReservation = async (reservationId) => {
        try {
            const response = await axios.post(API_URL_DELETE, { reservationId });
            Alert.alert(t('success'), t('RESDELSUC'));

            if (response.data.message === 'Reservation deleted successfully') {
                setReservations(reservations.filter(res => res.reservationId !== reservationId));
                fetchReservations();
            }
        } catch (error) {
            console.error('Error deleting reservation:', error);
            Alert.alert(t('error'), t('deleteReservationError'));
        }
    };

    const isReservationInFuture = (reservationDay) => {
        const currentDate = new Date();
        const reservationDate = new Date(reservationDay);
        return reservationDate > currentDate;
    };

    const renderItem = ({ item }) => {
        const handleDeleteReservation = (reservationId) => {
            Alert.alert(
                t('confirmDeleteTitle'),
                t('confirmDeleteMessage'),
                [
                    { text: t('cancel'), style: 'cancel' },
                    { text: t('yes'), onPress: () => deleteReservation(reservationId) }
                ]
            );
        };

        return (
            <View style={{ padding: 15, borderBottomWidth: 1, borderColor: '#ccc' }}>
                <Text style={{ fontSize: 16, fontWeight: 'bold' }}>{t('pet')}: {item.petName}</Text>
                <Text>{t('veterinarian')}: {item.vetEmail}</Text>
                <Text>{t('date')}: {item.reservationDay}</Text>
                <Text>{t('startTime')}: {item.reservationTime}</Text>
                <Text>{t('endTime')}: {item.period}</Text>

                {isReservationInFuture(item.reservationDay) && (
                    <TouchableOpacity
                        style={{
                            backgroundColor: 'red',
                            padding: 10,
                            marginTop: 10,
                            borderRadius: 5,
                            alignItems: 'center',
                        }}
                        onPress={() => handleDeleteReservation(item.reservationId)}
                    >
                        <Text style={{ color: '#fff' }}>{t('deleteReservation')}</Text>
                    </TouchableOpacity>
                )}
            </View>
        );
    };

    return (
        <View style={{ flex: 1, padding: 20 }}>
            {loading ? (
                <Text>{t('loading')}</Text>
            ) : (
                <FlatList
                    data={reservations}
                    keyExtractor={(item, index) =>
                        item.id ? item.id.toString() : index.toString()
                    }
                    renderItem={renderItem}
                />
            )}
        </View>
    );
};

export default ReservationsScreen;
