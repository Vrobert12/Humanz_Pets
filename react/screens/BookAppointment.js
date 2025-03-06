import React, { useState } from 'react';
import {View, Text, TextInput, TouchableOpacity, ScrollView, Alert} from 'react-native';
import { Calendar } from 'react-native-calendars';
import { Picker } from "@react-native-picker/picker";
import axios from "axios"; // Install this package
//import CalendarComponent from '../CalendarComponent'; // Updated component

const API_URL = 'http://192.168.1.8/Humanz2.0/Humanz_Pets/bookReact.php';
const API_URL2 = 'http://192.168.1.8/Humanz2.0/Humanz_Pets/getPets2.php';


const ReservationForm = ({navigation}) => {
    const [petId, setPetId] = useState('');
    const [userId, setUserId] = useState('');
    const [reservationDate, setReservationDate] = useState('');
    const [reservationStart, setReservationStart] = useState('');
    const [reservationEnd, setReservationEnd] = useState('');
    const [loading, setLoading] = useState(false);
    const [selected, setSelected] = useState('');
    const [selectedValue, setSelectedValue] = useState("");
    let selectedDate = new Date();

    const ComboBox = ({ options, selectedValue, onChange }) => {
        return (
            <View>
                <Text>Choose an option:</Text>
                <Picker selectedValue={selectedValue} onValueChange={onChange}>
                    <Picker.Item label="Select..." value="" />
                    {options.map((option) => (
                        <Picker.Item key={option.value} label={option.label} value={option.value} />
                    ))}
                </Picker>
            </View>
        );
    };

    const HandleDateSelect = (date) => {
        console.log(date.dateString);
        selectedDate = date.dateString;
        console.log('selected: ', selectedDate);
        setReservationDate(date);

    };

    const options = [
        { value: "12:00", label: "12:00" },
        { value: "option2", label: "Option 2" },
        { value: "option3", label: "Option 3" },
    ];

    const HandleSubmit = async () => {
        if (1===1) {
            console.log(petId);
            console.log(selected);
            console.log(reservationStart);
            console.log(reservationEnd);
            setLoading(true);

            let formData = new FormData();
            formData.append('pet_id', petId);
            formData.append('date', selected);
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
            window.alert('Error: Please fill all the fields.');
        }
    };

    return (
        <ScrollView style={{ padding: 20 }}>
            <Calendar
                onDayPress={day => {setSelected(day.dateString), HandleDateSelect}}
                minDate={new Date().toISOString().split('T')[0]} // Disable past dates
                markedDates={{
                    [selected]: { selected: true, selectedColor: '#007bff' },
                }}
            />
            <Text>You've selected: {selected.toString()}</Text>

            <View>
                <TextInput
                    style={{height: 40, borderColor: 'gray', borderWidth: 1, marginBottom: 10, paddingLeft: 8}}
                    placeholder="Pet ID"
                    value={petId}
                    onChangeText={(text) => setPetId(text)}
                />

                <TextInput
                    style={{ height: 40, borderColor: 'gray', borderWidth: 1, marginBottom: 10, paddingLeft: 8 }}
                    placeholder="Start Time"
                    value={reservationStart}
                    onChangeText={(text) => setReservationStart(text)}
                />
                {/*<ComboBox options={options} selectedValue={selectedValue} onChange={setSelectedValue} />*/}

                <TextInput
                    style={{height: 40, borderColor: 'gray', borderWidth: 1, marginBottom: 10, paddingLeft: 8}}
                    placeholder="End Time"
                    value={reservationEnd}
                    onChangeText={(text) => setReservationEnd(text)}
                />

                <TouchableOpacity
                    onPress={HandleSubmit}
                    style={{
                        backgroundColor: '#007bff',
                        paddingVertical: 10,
                        paddingHorizontal: 20,
                        borderRadius: 5,
                        alignItems: 'center',
                    }}
                    disabled={loading}
                >
                    <Text style={{color: '#fff', fontSize: 16}}>
                        {loading ? 'Submitting...' : 'Book Reservation'}
                    </Text>
                </TouchableOpacity>
            </View>
        </ScrollView>

    );
};

export default ReservationForm;