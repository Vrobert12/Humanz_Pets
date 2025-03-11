import React, { useState, useEffect } from 'react';
import { View, Text, Image } from 'react-native'; // Add the necessary imports
import AsyncStorage from "@react-native-async-storage/async-storage";

const QrCode = () => {
    const [qrCodePath, setQrCodePath] = useState(""); // Ensure the QR code path state is defined
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null); // State to store error messages if needed

    const fetchQRCode = async () => {
        setLoading(true); // Set loading to true while fetching
        try {
            const userId = await AsyncStorage.getItem("user_id");
            if (!userId) {
                throw new Error("User ID not found");
            }

            //const response = await fetch(`http://192.168.43.125/Humanz_Pets/getQrCode.php?user=${userId}`);

            const response = await fetch(`http://192.168.1.8/Humanz2.0/Humanz_Pets/getQrCode.php?user=${userId}`);

            // Check if the response is okay
            if (!response.ok) {
                throw new Error(`Failed to fetch QR code: ${response.statusText}`);
            }

            const text = await response.text(); // Log raw response
            console.log("Raw Response:", text);

            const data = JSON.parse(text);
            console.log('QR Code Path:', data.data.path);

            if (data.status === 200) {
                // Update the QR code path with the full URL
                //setQrCodePath(`http://192.168.43.125/Humanz_Pets/${data.data.path}`);
                setQrCodePath(`http://192.168.1.8/Humanz2.0/Humanz_Pets/${data.data.path}`);
            } else {
                setQrCodePath(""); // No QR code found
            }
        } catch (error) {
            console.error("Error fetching QR code:", error);
            setError(error.message); // Set the error state if there's an issue
        } finally {
            setLoading(false); // Set loading to false after the request
        }
    };

    useEffect(() => {
        fetchQRCode(); // Fetch the QR code when the component is mounted
    }, []);

    if (loading) {
        return <Text>Loading...</Text>; // Optionally display a loading indicator
    }

    return (
        <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }}>
            {error ? (
                <Text style={{ color: 'red' }}>Error: {error}</Text> // Display error if any
            ) : (
                qrCodePath ? (
                    <Image source={{ uri: qrCodePath }} style={{ width: 400, height: 400 }} /> // Display QR code if found
                ) : (
                    <Text>No QR Code available</Text> // If no QR code is available
                )
            )}
        </View>
    );
};

export default QrCode;
