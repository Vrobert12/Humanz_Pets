import React, { useState } from 'react';
import { View, Text, TextInput, TouchableOpacity, Alert } from 'react-native';
import { Picker } from '@react-native-picker/picker';
import axios from 'axios';
import { useTranslation } from 'react-i18next';

const API_URL = 'http://192.168.1.8/Humanz2.0/Humanz_Pets/phpForReact/registerUser.php';
//const API_URL = 'http://192.168.43.125/Humanz_Pets/phpForReact/registerUser.php';

const RegisterScreen = ({ navigation }) => {
    const [firstname, setFirstname] = useState('');
    const [lastname, setLastname] = useState('');
    const [phone, setPhone] = useState('');
    const [email, setEmail] = useState('');
    const [language, setLanguage] = useState('English');
    const [password, setPassword] = useState('');
    const [confirmPassword, setConfirmPassword] = useState('');
    const { t } = useTranslation();

    const handleRegister = async () => {
        if (!firstname || !lastname || !phone || !email || !password || !confirmPassword) {
            Alert.alert(t('error'), t('fillAllFields'));
            return;
        }
        if (password !== confirmPassword) {
            Alert.alert(t('error'), t('PASSMATCH'));
            return;
        }

        const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        const phoneRegex = /^[0-9]{10,15}$/;  // Simple phone number validation (adjust as needed)

        if (!emailRegex.test(email)) {
            Alert.alert(t('error'), t('NOEX'));
            return;
        }
        if (!phoneRegex.test(phone)) {
            Alert.alert(t('error'), t('invalidPhone'));
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
            const jsonResponse = JSON.parse(response.data.match(/\{.*}/s)[0]);
            console.log(jsonResponse.success); // Should be true
            if (jsonResponse.success) {
                Alert.alert(t('success'), t('registrationSuccess'));
                navigation.replace('Login');
            } else {
                Alert.alert('Error', response.data.message || 'Registration failed');
            }
        } catch (error) {
            Alert.alert(t('error'), error);
        }
    };

    return (
        <View style={{ flex: 1, justifyContent: 'center', padding: 20 }}>
            <Text style={{ fontSize: 24, textAlign: 'center', marginBottom: 20 }}>{t('REGISTER')}</Text>

            <TextInput
                style={{ borderWidth: 1, padding: 10, marginBottom: 10 }}
                placeholder={t('NAME')}
                value={firstname}
                onChangeText={setFirstname}
            />
            <TextInput
                style={{ borderWidth: 1, padding: 10, marginBottom: 10 }}
                placeholder={t('LASTNAME')}
                value={lastname}
                onChangeText={setLastname}
            />
            <TextInput
                style={{ borderWidth: 1, padding: 10, marginBottom: 10 }}
                placeholder={t('PHONE')}
                keyboardType="phone-pad"
                value={phone}
                onChangeText={setPhone}
            />
            <TextInput
                style={{ borderWidth: 1, padding: 10, marginBottom: 10 }}
                placeholder={t('EMAIL')}
                keyboardType="email-address"
                value={email}
                onChangeText={setEmail}
            />

            <View style={{ marginBottom: 10 }}>
                <Text style={{ marginBottom: 5 }}>{t('SELECT_LANGUAGE')}</Text>
                <Picker
                    selectedValue={language}
                    onValueChange={(itemValue) => setLanguage(itemValue)}
                    style={{ borderWidth: 1 }}
                >
                    <Picker.Item label={t('LANGUAGE_en')} value="English" />
                    <Picker.Item label={t('LANGUAGE_hu')} value="Hungarian" />
                    <Picker.Item label={t('LANGUAGE_sr')} value="Serbian" />
                </Picker>
            </View>

            <TextInput
                style={{ borderWidth: 1, padding: 10, marginBottom: 10 }}
                placeholder={t('PASSWORD')}
                secureTextEntry
                value={password}
                onChangeText={setPassword}
            />
            <TextInput
                style={{ borderWidth: 1, padding: 10, marginBottom: 10 }}
                placeholder={t('CONFPASS')}
                secureTextEntry
                value={confirmPassword}
                onChangeText={setConfirmPassword}
            />

            <TouchableOpacity onPress={handleRegister} style={{ backgroundColor: 'blue', padding: 10, borderRadius: 5 }}>
                <Text style={{ color: 'white', textAlign: 'center' }}>{t('REGISTER')}</Text>
            </TouchableOpacity>
        </View>
    );
};

export default RegisterScreen;
