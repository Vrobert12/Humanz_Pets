import React, { useState, useEffect } from 'react';
import { ScrollView, Text, TextInput, Image, TouchableOpacity, Alert, ActivityIndicator } from 'react-native';
import * as ImagePicker from 'expo-image-picker';
import { useRoute } from '@react-navigation/native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { Picker } from '@react-native-picker/picker';
import axios from 'axios';
import { useTranslation } from 'react-i18next';
import * as FileSystem from 'expo-file-system';

const API_URL = 'https://humanz.stud.vts.su.ac.rs/phpForReact/register_pet.php';
const VETS_API_URL = 'https://humanz.stud.vts.su.ac.rs/phpForReact/veterinariansReact.php';
const PETS_API_URL = 'https://humanz.stud.vts.su.ac.rs/phpForReact/applogIn.php';

const RegisterPet = ({ navigation }) => {
    const { t } = useTranslation();

    const [name, setName] = useState('');
    const [breed, setBreed] = useState('');
    const [species, setSpecies] = useState('Dog');
    const [veterinarian, setVeterinarian] = useState('');
    const [veterinarians, setVeterinarians] = useState([]);
    const [image, setImage] = useState(null);
    const [loading, setLoading] = useState(false);
    const [isLoadingVets, setIsLoadingVets] = useState(true);
    const [isPictureSet, PictureSet] = useState(false);
    const [imageButtonText, setImageButtonText] = useState(t('selectImage'));

    useEffect(() => {
        loadVets();
    }, []);

    const loadVets = async () => {
        try {
            const response = await axios.get(VETS_API_URL);
            if (response.data) {
                setVeterinarians(response.data);
                if (response.data.length > 0) {
                    setVeterinarian(response.data[0].veterinarianId.toString());
                }
            }
        } catch (error) {
            console.error(t('errorLoadingVets'), error);
        } finally {
            setIsLoadingVets(false);
        }
    };

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
            setImageButtonText(t('changeImage'));
        }
    };

    const handleRegister = async () => {
        if (isPictureSet && breed && name && species && veterinarian) {
            const userId = await AsyncStorage.getItem('user_id');
            if (!userId) {
                Alert.alert(t('error'), t('userNotLoggedIn'));
                return;
            }

            if (!name || !breed || !species || !veterinarian) {
                Alert.alert(t('error'), t('fillAllFields'));
                return;
            }

            setLoading(true);

            let base64Image = null;
            if (image) {
                // Read image as base64 string
                base64Image = await FileSystem.readAsStringAsync(image, { encoding: 'base64' });
            } else {
                Alert.alert(t('missingData'), t('fillFieldsAndImage'));
                setLoading(false);
                return;
            }

            const payload = {
                user_id: userId,
                name,
                breed,
                species,
                veterinarian_id: veterinarian,
                image_base64: base64Image,
            };

            try {
                const response = await axios.post(API_URL, payload, {
                    headers: { 'Content-Type': 'application/json' },
                });

                setLoading(false);
                if (response.data.success) {
                    Alert.alert(t('success'), t('registerPetSuc'));
                    navigation.goBack();
                } else {
                    Alert.alert(t('error'), response.data.message);
                }
            } catch (error) {
                setLoading(false);
                Alert.alert(t('error'), t('genericError'));
            }
        } else {
            Alert.alert(t('missingData'), t('fillFieldsAndImage'));
        }
    };

    return (
        <ScrollView style={{ padding: 20 }}>
            <Text>{t('petName')}:</Text>
            <TextInput value={name} onChangeText={setName} style={{ borderWidth: 1, marginBottom: 10 }} />
            <Text>{t('breed')}:</Text>
            <TextInput value={breed} onChangeText={setBreed} style={{ borderWidth: 1, marginBottom: 10 }} />
            <Text>{t('species')}:</Text>
            <Picker selectedValue={species} onValueChange={setSpecies} style={{ borderWidth: 1, marginBottom: 10 }}>
                <Picker.Item label={t('dog')} value="Dog" />
                <Picker.Item label={t('cat')} value="Cat" />
                <Picker.Item label={t('parrot')} value="Parrot" />
                <Picker.Item label={t('rabbit')} value="Rabbit" />
                <Picker.Item label={t('pig')} value="Pig" />
            </Picker>
            <Text>{t('chooseVet')}:</Text>
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
            <TouchableOpacity
                onPress={handleRegister}
                style={{ backgroundColor: '#007bff', padding: 10, borderRadius: 5, marginTop: 10, marginBottom: 30 }}
            >
                <Text style={{ color: 'white', fontWeight: 'bold', fontSize: 16, textAlign: 'center' }}>{t('registerPet')}</Text>
            </TouchableOpacity>
            {loading && <ActivityIndicator size="large" style={{ backgroundColor: '#007bf' }} />}
        </ScrollView>
    );
};

export default RegisterPet;
