import React, { useState, useEffect } from "react";
import { View, Text, TextInput, Button, ActivityIndicator, Alert } from "react-native";

export default function Settings() {
    const [userData, setUserData] = useState(null);
    const [loading, setLoading] = useState(true);
    const [updating, setUpdating] = useState(false);

    const userId = 25; // Hardcoded for now
    const apiUrl = `http://192.168.1.7/Humanz_Pets/getPets/user/${userId}`; // Correct API URL to fetch user data

    // Fetch user data
    useEffect(() => {
        console.log("Fetching user data from API:", apiUrl); // Log the URL for debugging

        fetch(apiUrl)
            .then((response) => response.json())
            .then((data) => {
                console.log("User data response:", data); // Log the response data
                if (data.status === 200) {
                    setUserData(data.data[0]); // Assuming data array has one user
                } else {
                    Alert.alert("Error", "Failed to load user data.");
                }
            })
            .catch((error) => {
                console.error("Error fetching data:", error); // Log the error message
                Alert.alert("Error", `Failed to fetch data: ${error.message}`);
            })
            .finally(() => setLoading(false));
    }, []); // Empty dependency array means this effect runs once when the component mounts

    // Handle update
    const handleUpdate = () => {
        setUpdating(true);

        // Optimistic UI update: update the UI before making the network request
        const updatedUserData = { ...userData, id: userId }; // Ensure the id is included

        console.log("Sending PUT request to:", apiUrl); // Log the URL being used for PUT
        console.log("Request Body:", JSON.stringify(updatedUserData)); // Log the request body

        fetch(apiUrl, {
            method: "PATCH", // Use PUT method to update
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(updatedUserData), // Send updated user data, including userId
        })
            .then((response) => response.json())
            .then((data) => {
                console.log("Response Data:", data); // Log the response body
                if (data.status === 200) {
                    Alert.alert("Success", "User data updated successfully!");
                } else {
                    Alert.alert("Error", `Failed to update data: ${data.message}`);
                }
            })
            .catch((error) => {
                console.error("Error updating data:", error); // Log the error message
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
