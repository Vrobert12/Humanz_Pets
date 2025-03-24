import React, { useState, useEffect } from 'react';
import { ScrollView, Text, TextInput, Image, TouchableOpacity, Alert, ActivityIndicator } from 'react-native';
import * as ImagePicker from 'expo-image-picker';
import { useRoute } from '@react-navigation/native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { Picker } from '@react-native-picker/picker';
import axios from 'axios';

const API_URL = 'http://192.168.1.8/Humanz2.0/Humanz_Pets/phpForReact/register_pet.php';
const VETS_API_URL = 'http://192.168.1.8/Humanz2.0/Humanz_Pets/phpForReact/veterinariansReact.php';
const PETS_API_URL = 'http://192.168.1.8/Humanz2.0/Humanz_Pets/phpForReact/applogIn.php';

const RegisterPet = ({ navigation }) => {
    const [name, setName] = useState('');
    const [breed, setBreed] = useState('');
    const [species, setSpecies] = useState('Dog');
    const [veterinarian, setVeterinarian] = useState('');
    const [veterinarians, setVeterinarians] = useState([]);
    const [image, setImage] = useState(null);
    const [loading, setLoading] = useState(false);
    const [isLoadingVets, setIsLoadingVets] = useState(true);
    const [isPictureSet, PictureSet] = useState(false);
    const [imageButtonText, setImageButtonText] = useState('Select Image');

    // Safely extract setRefresh from route.params or provide a no-op default

    const loadVets = async () => {
        try {
            const response = await axios.get(VETS_API_URL);
            if (response.data) {
                console.log("Fetched Veterinarians:", response.data);
                const options = response.data.map((vet) => ({
                    value: vet.veterinarianId.toString(),
                    label: `${vet.firstName} ${vet.lastName}`,
                }));
                setVeterinarians(response.data);
                // Set default veterinarian if available
                if (response.data.length > 0) {
                    setVeterinarian(response.data[0].veterinarianId.toString());
                }
            }
        } catch (error) {
            console.error("Error loading veterinarians:", error);
        } finally {
            setIsLoadingVets(false);
        }
    };

    useEffect(() => {
        loadVets();
    }, []);

    const pickImage = async () => {
        let result = await ImagePicker.launchImageLibraryAsync({
            mediaTypes: ImagePicker.MediaTypeOptions.Images,
            allowsEditing: true,
            aspect: [4, 3],
            quality: 1,
        });
        if (!result.canceled && result.assets.length > 0) {
            setImage(result.assets[0].uri);
            PictureSet(true);
            setImageButtonText('Change Image');
        }
    };

    const handleRegister = async () => {
        if (isPictureSet && breed && name) {
            const userId = await AsyncStorage.getItem('user_id');
            if (!userId) {
                Alert.alert('Error', 'User not logged in');
                return;
            }
            if (!name || !breed || !species || !veterinarian) {
                Alert.alert('Error', 'Please fill in all fields');
                return;
            }

            setLoading(true);
            let formData = new FormData();
            formData.append('user_id', userId);
            formData.append('name', name);
            formData.append('breed', breed);
            formData.append('species', species);
            formData.append('veterinarian_id', veterinarian);

            const currentDate = new Date();
            const newFileName = `${currentDate.getFullYear()}${(currentDate.getMonth() + 1)
                .toString().padStart(2, '0')}${currentDate.getDate().toString().padStart(2, '0')}${currentDate.getHours().toString().padStart(2, '0')}${currentDate.getMinutes().toString().padStart(2, '0')}${currentDate.getSeconds().toString().padStart(2, '0')}.png`;

            if (image) {
                const uriParts = image.split('.');
                const fileType = uriParts[uriParts.length - 1];
                formData.append('image', {
                    uri: image,
                    name: newFileName,
                    type: `image/${fileType}`,
                });
            }

            try {
                const response = await axios.post(API_URL, formData, {
                    headers: {'Content-Type': 'multipart/form-data'},
                });
                setLoading(false);
                if (response.data.success) {
                    Alert.alert('Success', response.data.message);
                    navigation.goBack();
                } else {
                    Alert.alert('Error', response.data.message);
                }
            } catch (error) {
                setLoading(false);
                Alert.alert('Error', 'An error occurred. Please try again later.');
            }
        } else {
            Alert.alert('Error: missing data', 'Please fill in all the fields and have an image selected!');
        }
        const token = await AsyncStorage.getItem('session_token');
        const userId = await AsyncStorage.getItem('user_id');

        if (token && userId) {
            setLoading(true);
            try {
                // Fetch updated user data
                const response = await axios.post(PETS_API_URL, {userId, token});
                if (response.data.success) {
                    await AsyncStorage.removeItem('pets');
                    await AsyncStorage.setItem('pets', JSON.stringify(response.data.pets));
                }
            } catch (error) {
                console.error("Error refreshing user data:", error);
            }
            setLoading(false);
        }
    };

    return (
        <ScrollView style={{ padding: 20 }}>
            <Text>Pet Name:</Text>
            <TextInput value={name} onChangeText={setName} style={{ borderWidth: 1, marginBottom: 10 }} />
            <Text>Breed:</Text>
            <TextInput value={breed} onChangeText={setBreed} style={{ borderWidth: 1, marginBottom: 10 }} />
            <Text>Species:</Text>
            <Picker selectedValue={species} onValueChange={(itemValue) => setSpecies(itemValue)} style={{ borderWidth: 1, marginBottom: 10 }}>
                <Picker.Item label="Dog" value="Dog" />
                <Picker.Item label="Cat" value="Cat" />
                <Picker.Item label="Parrot" value="Parrot" />
                <Picker.Item label="Rabbit" value="Rabbit" />
                <Picker.Item label="Pig" value="Pig" />
            </Picker>
            <Text>Choose Veterinarian:</Text>
            <Picker
                selectedValue={veterinarian}
                onValueChange={(itemValue) => setVeterinarian(itemValue)}
                style={{ borderWidth: 1, marginBottom: 10 }}
            >
                {veterinarians.map(vet => (
                    <Picker.Item
                        key={vet.veterinarianId}
                        label={`${vet.firstName} ${vet.lastName}`}
                        value={vet.veterinarianId}
                    />
                ))}
            </Picker>
            <TouchableOpacity
                onPress={pickImage}
                style={{
                    backgroundColor: '#007bff',
                    padding: 12,
                    borderRadius: 8,
                    alignItems: 'center',
                    marginVertical: 10
                }}
            >
                <Text style={{ color: 'white', fontWeight: 'bold', fontSize: 16 }}>{imageButtonText}</Text>
            </TouchableOpacity>
            {image && <Image source={{ uri: image }} style={{ width: 100, height: 100 }} />}
            <TouchableOpacity onPress={handleRegister} style={{ backgroundColor: '#007bff', padding: 10, borderRadius: 5, marginTop: 10, marginBottom: 30 }}>
                <Text style={{ color: 'white', fontWeight: 'bold', fontSize: 16, textAlign: 'center' }}>Register Pet</Text>
            </TouchableOpacity>
            {loading && <ActivityIndicator size="large" style={{ backgroundColor: '#007bf' }} />}
        </ScrollView>
    );
};

export default RegisterPet;
