import React, { useEffect, useState } from 'react';
import { View, Text, FlatList, Image, TextInput, StyleSheet } from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';

const Pets = () => {
    const [pets, setPets] = useState([]);
    const [filteredPets, setFilteredPets] = useState([]);
    const [searchQuery, setSearchQuery] = useState('');

    useEffect(() => {
        // Function to fetch pets from AsyncStorage
        const fetchPets = async () => {
            try {
                const petsData = await AsyncStorage.getItem('pets');
                if (petsData !== null) {
                    const petsList = JSON.parse(petsData);
                    setPets(petsList);
                    setFilteredPets(petsList); // Initially show all pets
                }
            } catch (error) {
                console.error('Error fetching pets:', error);
            }
        };

        fetchPets();
    }, []);

    // Function to filter pets based on search query
    const handleSearch = (query) => {
        setSearchQuery(query);
        if (query === '') {
            setFilteredPets(pets); // If search is empty, show all pets
        } else {
            const filtered = pets.filter((pet) =>
                pet.petName.toLowerCase().includes(query.toLowerCase())
            );
            setFilteredPets(filtered); // Filter pets by name
        }
    };

    const renderPet = ({ item }) => (
        <View style={styles.petItem}>
            <View style={styles.petInfo}>
                <Text style={styles.petText}>Name: {item.petName}</Text>
                <Text style={styles.petText}>Species: {item.petSpecies}</Text>
                <Text style={styles.petText}>Breed: {item.bred}</Text>
            </View>
            <Image
                source={{ uri: 'http://192.168.1.8/Humanz2.0/Humanz_Pets/pictures/' + item.profilePic }} // Assuming petImage is a URL or a local path
                style={styles.petImage}
            />
        </View>
    );

    return (
        <View style={styles.container}>
            <TextInput
                style={styles.searchBar}
                placeholder="Search pets..."
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
        alignItems: 'center', // Align text and image vertically in the center
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
        marginLeft: 10, // Space between text and image
    },
});

export default Pets;
