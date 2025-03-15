import React, { useState } from 'react';
import { View, Text, TextInput, TouchableOpacity, Alert } from 'react-native';
import { Picker } from '@react-native-picker/picker';
import axios from 'axios';

// const API_URL = 'http://192.168.1.8/Humanz2.0/Humanz_Pets/phpForReact/registerUser.php';
const API_URL = 'http://192.168.43.125/Humanz_Pets/phpForReact/registerUser.php';

const RegisterScreen = ({ navigation }) => {
    const [firstname, setFirstname] = useState('');
    const [lastname, setLastname] = useState('');
    const [phone, setPhone] = useState('');
    const [email, setEmail] = useState('');
    const [language, setLanguage] = useState('English');
    const [password, setPassword] = useState('');
    const [confirmPassword, setConfirmPassword] = useState('');

    const handleRegister = async () => {
        if (!firstname || !lastname || !phone || !email || !password || !confirmPassword) {
            Alert.alert('Error', 'Please fill in all fields');
            return;
        }
        if (password !== confirmPassword) {
            Alert.alert('Error', 'Passwords do not match');
            return;
        }

        try {
            const response = await axios.post(API_URL, {
                firstname,
                lastname,
                phone,
                email,
                language,
                password
            });

            if (response.data.success) {
                Alert.alert('Success', 'Registration successful!');
                navigation.replace('Login');
            } else {
                Alert.alert('Error', response.data.message || 'Registration failed');
            }
        } catch (error) {
            Alert.alert('Error', 'An error occurred. Please try again later.');
        }
    };

    return (
        <View style={{ flex: 1, justifyContent: 'center', padding: 20 }}>
            <Text style={{ fontSize: 24, textAlign: 'center', marginBottom: 20 }}>Register</Text>

            <TextInput
                style={{ borderWidth: 1, padding: 10, marginBottom: 10 }}
                placeholder="First Name"
                value={firstname}
                onChangeText={setFirstname}
            />
            <TextInput
                style={{ borderWidth: 1, padding: 10, marginBottom: 10 }}
                placeholder="Last Name"
                value={lastname}
                onChangeText={setLastname}
            />
            <TextInput
                style={{ borderWidth: 1, padding: 10, marginBottom: 10 }}
                placeholder="Phone Number"
                keyboardType="phone-pad"
                value={phone}
                onChangeText={setPhone}
            />
            <TextInput
                style={{ borderWidth: 1, padding: 10, marginBottom: 10 }}
                placeholder="Email Address"
                keyboardType="email-address"
                value={email}
                onChangeText={setEmail}
            />

            <Picker
                selectedValue={language}
                onValueChange={(itemValue) => setLanguage(itemValue)}
                style={{ marginBottom: 10, borderWidth: 1 }}
            >
                <Picker.Item label="English" value="English" />
                <Picker.Item label="Hungarian" value="Hungarian" />
                <Picker.Item label="Serbian" value="Serbian" />
            </Picker>

            <TextInput
                style={{ borderWidth: 1, padding: 10, marginBottom: 10 }}
                placeholder="Password"
                secureTextEntry
                value={password}
                onChangeText={setPassword}
            />
            <TextInput
                style={{ borderWidth: 1, padding: 10, marginBottom: 10 }}
                placeholder="Confirm Password"
                secureTextEntry
                value={confirmPassword}
                onChangeText={setConfirmPassword}
            />

            <TouchableOpacity onPress={handleRegister} style={{ backgroundColor: 'blue', padding: 10, borderRadius: 5 }}>
                <Text style={{ color: 'white', textAlign: 'center' }}>Register</Text>
            </TouchableOpacity>
        </View>
    );
};

export default RegisterScreen;
