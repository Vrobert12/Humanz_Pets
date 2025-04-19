import React, { useEffect, useState, useCallback } from 'react';
import { useFocusEffect } from '@react-navigation/native';
import { View, Text, FlatList, Image, TextInput, StyleSheet } from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { useTranslation } from 'react-i18next';

const Pets = () => {
    const { t } = useTranslation();
    const [pets, setPets] = useState([]);
    const [filteredPets, setFilteredPets] = useState([]);
    const [searchQuery, setSearchQuery] = useState('');

    useFocusEffect(
        useCallback(() => {
            fetchPets();
        }, [])
    );

    const fetchPets = async () => {
        try {
            const petsData = await AsyncStorage.getItem('pets');
            if (petsData !== null) {
                const petsList = JSON.parse(petsData);
                setPets(petsList);
                setFilteredPets(petsList);
            }
        } catch (error) {
            console.error(t('error'), error);
        }
    };

    const handleSearch = (query) => {
        setSearchQuery(query);
        if (query === '') {
            setFilteredPets(pets);
        } else {
            const filtered = pets.filter((pet) =>
                pet.petName.toLowerCase().includes(query.toLowerCase())
            );
            setFilteredPets(filtered);
        }
    };

    const renderPet = ({ item }) => (
        <View style={styles.petItem}>
            <View style={styles.petInfo}>
                <Text style={styles.petText}>{t('name')}: {item.petName}</Text>
                <Text style={styles.petText}>{t('species')}: {item.petSpecies}</Text>
                <Text style={styles.petText}>{t('breed')}: {item.bred}</Text>
            </View>
            <Image
                source={{ uri: 'http://192.168.1.8/Humanz2.0/Humanz_Pets/pictures/' + item.profilePic }}
                style={styles.petImage}
            />
        </View>
    );

    return (
        <View style={styles.container}>
            <TextInput
                style={styles.searchBar}
                placeholder={t('searchPets')}
                value={searchQuery}
                onChangeText={handleSearch}
            />
            <FlatList
                data={filteredPets}
                keyExtractor={(item) => item.petId.toString()}
                renderItem={renderPet}
            />
        </View>
    );
};

const styles = StyleSheet.create({
    container: {
        flex: 1,
        padding: 20,
        backgroundColor: '#fff',
    },
    searchBar: {
        height: 40,
        borderColor: '#ccc',
        borderWidth: 1,
        borderRadius: 8,
        marginBottom: 20,
        paddingHorizontal: 10,
        fontSize: 16,
    },
    petItem: {
        flexDirection: 'row',
        padding: 10,
        backgroundColor: '#f9f9f9',
        marginBottom: 10,
        borderRadius: 8,
        alignItems: 'center',
    },
    petInfo: {
        flex: 1,
    },
    petText: {
        fontSize: 16,
        marginBottom: 5,
    },
    petImage: {
        width: 60,
        height: 60,
        borderRadius: 8,
        marginLeft: 10,
    },
});

export default Pets;
