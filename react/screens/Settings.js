import React, { useState, useEffect } from "react";
import { View, Text, TextInput, Button, ActivityIndicator, Alert } from "react-native";
import AsyncStorage from "@react-native-async-storage/async-storage";

export default function Settings() {
    const [userData, setUserData] = useState(null);
    const [loading, setLoading] = useState(true);
    const [updating, setUpdating] = useState(false);
    const [userId, setUserId] = useState(null); // State for user ID

    useEffect(() => {
        const fetchUserId = async () => {
            try {
                const storedUserId = await AsyncStorage.getItem("user_id");
                if (storedUserId) {
                    setUserId(storedUserId);
                } else {
                    Alert.alert("Error", "User not logged in.");
                }
            } catch (error) {
                console.error("Error fetching user ID:", error);
            }
        };

        fetchUserId();
    }, []);

    useEffect(() => {
        if (!userId) return; // Only fetch data when userId is available

        const apiUrl = `http://192.168.1.8/Humanz2.0/Humanz_Pets/phpForReact/getPets/user/${userId}`;

        //const apiUrl = `http://192.168.43.125/Humanz_Pets/phpForReact/getPets/user/${userId}`;

        console.log("Fetching user data from API:", apiUrl);

        fetch(apiUrl)
            .then((response) => response.json())
            .then((data) => {
                console.log("User data response:", data);
                if (data.status === 200) {
                    setUserData(data.data[0]); // Assuming the data array has one user
                } else {
                    Alert.alert("Error", "Failed to load user data.");
                }
            })
            .catch((error) => {
                console.error("Error fetching data:", error);
                Alert.alert("Error", `Failed to fetch data: ${error.message}`);
            })
            .finally(() => setLoading(false));
    }, [userId]); // Re-run the effect when userId is set

    const handleUpdate = () => {
        if (!userId || !userData) return;

        setUpdating(true);
        const updatedUserData = { ...userData, id: userId };

        //const apiUrl = `http://192.168.1.8/Humanz2.0/Humanz_Pets/phpForReact/getPets/user/${userId}`;
        const apiUrl = `http://192.168.43.125/Humanz_Pets/phpForReact/getPets/user/${userId}`;
        console.log("Sending PATCH request to:", apiUrl);
        console.log("Request Body:", JSON.stringify(updatedUserData));

        fetch(apiUrl, {
            method: "PATCH",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(updatedUserData),
        })
            .then((response) => response.json())
            .then((data) => {
                console.log("Response Data:", data);
                if (data.status === 200) {
                    Alert.alert("Success", "User data updated successfully!");
                } else {
                    Alert.alert("Error", `Failed to update data: ${data.message}`);
                }
            })
            .catch((error) => {
                console.error("Error updating data:", error);
                Alert.alert("Error", `Update request failed: ${error.message}`);
            })
            .finally(() => setUpdating(false));
    };

    return (
        <View style={{ flex: 1, padding: 20 }}>
            {loading ? (
                <ActivityIndicator size="large" />
            ) : userData ? (
                <>
                    <Text>First Name:</Text>
                    <TextInput
                        value={userData.firstName}
                        onChangeText={(text) => setUserData({ ...userData, firstName: text })}
                        style={{ borderWidth: 1, padding: 10, marginBottom: 10 }}
                    />

                    <Text>Last Name:</Text>
                    <TextInput
                        value={userData.lastName}
                        onChangeText={(text) => setUserData({ ...userData, lastName: text })}
                        style={{ borderWidth: 1, padding: 10, marginBottom: 10 }}
                    />

                    <Text>Phone Number:</Text>
                    <TextInput
                        value={userData.phoneNumber}
                        onChangeText={(text) => setUserData({ ...userData, phoneNumber: text })}
                        style={{ borderWidth: 1, padding: 10, marginBottom: 10 }}
                    />

                    <Text>Language:</Text>
                    <TextInput
                        value={userData.usedLanguage}
                        onChangeText={(text) => setUserData({ ...userData, usedLanguage: text })}
                        style={{ borderWidth: 1, padding: 10, marginBottom: 20 }}
                    />

                    <Button title="Update" onPress={handleUpdate} disabled={updating} />
                </>
            ) : (
                <Text>No user data found.</Text>
            )}
        </View>
    );
}
