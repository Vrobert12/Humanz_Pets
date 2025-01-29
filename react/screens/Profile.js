import React, { useEffect, useState } from 'react';
import { View, Text, Image, ActivityIndicator, StyleSheet } from 'react-native';

export default function Profile() {
    const [userData, setUserData] = useState(null);
    const [loading, setLoading] = useState(true);
    const userId = 19; // Change this if needed

    useEffect(() => {
        fetch(`http://192.168.43.125/Humanz_Pets/getPets/user/19`)
            .then((response) => response.json())
            .then((data) => {
                if (data.status === 200) {
                    setUserData(data.data[0]); // Assuming only one user is returned
                }
                setLoading(false);
            })
            .catch((error) => {
                console.error('Error fetching data:', error);
                setLoading(false);
            });
    }, []);

    if (loading) {
        return (
            <View style={styles.container}>
                <ActivityIndicator size="large" color="#007bff" />
                <Text>Loading user data...</Text>
            </View>
        );
    }

    if (!userData) {
        return (
            <View style={styles.container}>
                <Text style={styles.errorText}>Failed to load user data.</Text>
            </View>
        );
    }

    return (
        <View style={styles.container}>
            <Image
                source={{ uri: `http://192.168.43.125/Humanz_Pets/pictures/${userData.profilePic}` }} // Update path if needed
                style={styles.profileImage}
            />
            <Text style={styles.userName}>{userData.firstName} {userData.lastName}</Text>
            <Text style={styles.info}>📧 {userData.userMail}</Text>
            <Text style={styles.info}>📞 {userData.phoneNumber}</Text>
            <Text style={styles.info}>🌐 Language: {userData.usedLanguage}</Text>
            <Text style={styles.info}>🔒 Privilege: {userData.privilage}</Text>
        </View>
    );
}

const styles = StyleSheet.create({
    container: {
        flex: 1,
        justifyContent: 'center',
        alignItems: 'center',
        backgroundColor: '#f8f8f8',
        padding: 20,
    },
    profileImage: {
        width: 120,
        height: 120,
        borderRadius: 60,
        marginBottom: 10,
        borderWidth: 2,
        borderColor: '#007bff',
    },
    userName: {
        fontSize: 22,
        fontWeight: 'bold',
        color: '#333',
        marginBottom: 5,
    },
    info: {
        fontSize: 16,
        color: '#555',
        marginBottom: 5,
    },
    errorText: {
        fontSize: 18,
        color: 'red',
    },
});
