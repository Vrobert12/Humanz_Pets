import React, { useState, useEffect } from 'react';
import {
    View, Text, TextInput, TouchableOpacity, Alert, ActivityIndicator
} from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import axios from 'axios';
import {setLanguage} from "../i18n";
import { useTranslation } from 'react-i18next';

const API_URL = 'https://humanz.stud.vts.su.ac.rs/phpForReact/applogIn.php';

const LoginScreen = ({ navigation, onLogin }) => {
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [loading, setLoading] = useState(false);
    const { t } = useTranslation();

    useEffect(() => {
        checkLoginStatus();
    }, []);

    const checkLoginStatus = async () => {
        const token = await AsyncStorage.getItem('session_token');
        const userId = await AsyncStorage.getItem('user_id');

        if (token && userId) {
            setLoading(true);
            try {
                // Fetch updated user data
                const response = await axios.post(API_URL, { userId, token });

                if (response.data.success) {
                    await AsyncStorage.setItem('pets', JSON.stringify(response.data.pets));
                    await AsyncStorage.setItem('lang', response.data.language);

                    if (response.data.language) {
                        setLanguage(response.data.language); // e.g., "en", "hu", "sr"
                    }
                    onLogin(); // Navigate to the main app
                } else {
                    console.log("Session expired, logging out.");
                    await AsyncStorage.removeItem('session_token');
                    await AsyncStorage.removeItem('user_id');
                    await AsyncStorage.removeItem('pets');
                }
            } catch (error) {
                console.error(t('error'), error);
            }
            setLoading(false);
        }
    };

    const handleLogin = async () => {
        if (!email || !password) {
            Alert.alert(t('error'), t('fillAllFields'));
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
                await AsyncStorage.setItem('pets', JSON.stringify(response.data.pets));
                await AsyncStorage.setItem('lang', response.data.language);

                setLanguage(response.data.language); // apply the language immediately

                Alert.alert(t('success'), t('LOGIN_SUCCESS'));
                onLogin();
            } else {
                Alert.alert(t('error'), t('INVALID_CREDENTIALS'));
            }
        } catch (error) {
            setLoading(false);
            Alert.alert(t('error'),error);
        }
    };

    return (
        <View style={{flex: 1, justifyContent: 'center', alignItems: 'center', padding: 20}}>
            <Text style={{fontSize: 24, marginBottom: 20}}>{t('LOGIN')}</Text>

            <TextInput
                style={{width: '100%', padding: 10, borderWidth: 1, marginBottom: 10}}
                placeholder={t('EMAIL')}
                keyboardType="email-address"
                value={email}
                onChangeText={setEmail}
            />
            <TextInput
                style={{width: '100%', padding: 10, borderWidth: 1, marginBottom: 10}}
                placeholder={t('PASSWORD')}
                secureTextEntry
                value={password}
                onChangeText={setPassword}
            />

            <TouchableOpacity
                onPress={handleLogin}
                style={{backgroundColor: 'blue', padding: 10, borderRadius: 5, width: '100%', alignItems: 'center'}}
            >
                <Text style={{color: 'white', fontSize: 16}}>{t('LOGIN')}</Text>
            </TouchableOpacity>

            {loading && <ActivityIndicator size="large" color="blue" style={{marginTop: 10}}/>}

            {/* Register Button */}
            <TouchableOpacity
                onPress={() => navigation.navigate('Register')}
                style={{ marginTop: 15 }}
            >
                <Text style={{ color: 'blue', fontSize: 16 }}>{t('NOACC') + ' ' + t('REGHERE')}</Text>
            </TouchableOpacity>
        </View>
    );
}

export default LoginScreen;
