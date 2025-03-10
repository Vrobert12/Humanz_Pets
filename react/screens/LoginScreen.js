import React, { useState, useEffect } from 'react';
import { View, Text, TextInput, TouchableOpacity, Alert, ActivityIndicator } from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import axios from 'axios';

//const API_URL = 'http://192.168.1.8/Humanz2.0/Humanz_Pets/applogIn.php';
const API_URL = 'http://192.168.43.125/Humanz_Pets/applogIn.php';
const LoginScreen = ({ navigation }) => {
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [loading, setLoading] = useState(false);

    useEffect(() => {
        checkLoginStatus();
    }, []);

    const checkLoginStatus = async () => {
        const token = await AsyncStorage.getItem('session_token');
        if (token) {
            navigation.replace('Main'); // Redirect to main app if token exists
        }
    };

    const handleLogin = async () => {
        if (!email || !password) {
            Alert.alert('Error', 'Please fill in both fields');
            return;
        }

        setLoading(true);
        try {
            const response = await axios.post(API_URL, { email, password });
            setLoading(false);
            console.log(response.data);
            if (response.data.success) {
                await AsyncStorage.setItem('session_token', response.data.token);
                await AsyncStorage.setItem('user_id', response.data.userid.toString());
                await AsyncStorage.setItem("pets", JSON.stringify(response.data.pets));
                // console.log('this!!!!!', response.data.petId.toArray());
                Alert.alert('Success', 'Login successful');
                navigation.replace('Main');
            } else {
                Alert.alert('Error', response.data.message || 'Invalid credentials');
            }
        } catch (error) {
            setLoading(false);
            Alert.alert('Error', 'An error occurred. Please try again later.');
        }
    };

    return (
        <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center', padding: 20 }}>
            <Text style={{ fontSize: 24, marginBottom: 20 }}>Login</Text>
            <TextInput
                style={{ width: '100%', padding: 10, borderWidth: 1, marginBottom: 10 }}
                placeholder="Email"
                keyboardType="email-address"
                value={email}
                onChangeText={setEmail}
            />
            <TextInput
                style={{ width: '100%', padding: 10, borderWidth: 1, marginBottom: 10 }}
                placeholder="Password"
                secureTextEntry
                value={password}
                onChangeText={setPassword}
            />
            <TouchableOpacity onPress={handleLogin} style={{ backgroundColor: 'blue', padding: 10, borderRadius: 5 }}>
                <Text style={{ color: 'white' }}>Log In</Text>
            </TouchableOpacity>

            {loading && <ActivityIndicator size="large" color="blue" />}
        </View>
    );
};

export default LoginScreen;
