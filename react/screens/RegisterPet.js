import React, { useState } from 'react';
import { View, Text, TextInput, Button, Image, TouchableOpacity, Alert, ActivityIndicator } from 'react-native';
import * as ImagePicker from 'expo-image-picker';
import AsyncStorage from '@react-native-async-storage/async-storage';
import axios from 'axios';

const API_URL = 'http://192.168.1.8/Humanz2.0/Humanz_Pets/register_pet.php';
//const API_URL = 'http://192.168.43.125/Humanz2.0/Humanz_Pets/register_pet.php';

const RegisterPet = ({ navigation }) => {
    const [name, setName] = useState('');
    const [breed, setBreed] = useState('');
    const [species, setSpecies] = useState('');
    const [image, setImage] = useState(null);
    const [loading, setLoading] = useState(false);

    const pickImage = async () => {
        let result = await ImagePicker.launchImageLibraryAsync({
            mediaTypes: ImagePicker.MediaTypeOptions.Images,
            allowsEditing: true,
            aspect: [4, 3],
            quality: 1,
        });
        if (!result.canceled && result.assets.length > 0) {
            setImage(result.assets[0].uri);
            console.log("Image:", result.assets[0].uri);
        }
    };

    const handleRegister = async () => {
        const userId = await AsyncStorage.getItem('user_id');
        console.log(userId);
        if (!userId) {
            Alert.alert('Error', 'User not logged in');
            return;
        }
        if (!name || !breed || !species) {
            Alert.alert('Error', 'Please fill in all fields');
            return;
        }

        setLoading(true);

        let formData = new FormData();
        formData.append('user_id', userId);
        formData.append('name', name);
        formData.append('breed', breed);
        formData.append('species', species);
        console.log(image);
        if (image) {
            formData.append('image', {
                uri: image,
                name: 'pet.jpg',
                type: 'image/jpeg',
            });
        }

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
    };

    return (
        <View style={{ padding: 20 }}>
            <Text>Pet Name:</Text>
            <TextInput value={name} onChangeText={setName} style={{ borderWidth: 1, marginBottom: 10 }} />
            <Text>Breed:</Text>
            <TextInput value={breed} onChangeText={setBreed} style={{ borderWidth: 1, marginBottom: 10 }} />
            <Text>Species:</Text>
            <TextInput value={species} onChangeText={setSpecies} style={{ borderWidth: 1, marginBottom: 10 }} />
            <TouchableOpacity onPress={pickImage}>
                <Text>Select Image</Text>
            </TouchableOpacity>
            {image && <Image source={{ uri: image }} style={{ width: 100, height: 100 }} />}
            <TouchableOpacity onPress={handleRegister} style={{ backgroundColor: 'blue', padding: 10, borderRadius: 5, marginTop: 10 }}>
                <Text style={{ color: 'white' }}>Register Pet</Text>
            </TouchableOpacity>
            {loading && <ActivityIndicator size="large" color="blue" />}
        </View>
    );
};

export default RegisterPet;
