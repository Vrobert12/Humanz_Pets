import React, { useState, useEffect } from 'react';
import {View, Text, Image, Alert} from 'react-native'; // Add the necessary imports
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

            const response = await fetch(`https://humanz.stud.vts.su.ac.rs/phpForReact/getQrCode.php?user=${userId}`);

            // Check if the response is okay
            if (!response.ok || response.status === 404) {
                throw new Error(` ${t('error') + response.statusText}`);
            }

            const text = await response.text(); // Log raw response

            const data = JSON.parse(text);
            //console.log('QR Code Path:', data.data.path);

            if (data.status === 200) {
                // Update the QR code path with the full URL
                setQrCodePath(`https://humanz.stud.vts.su.ac.rs/${data.data.path}`);
            } else {
                setQrCodePath(" "); // No QR code found
                Alert.alert(data.message);
            }
        } catch (error) {
            console.error(t('error'), error);
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
    console.log(qrCodePath);
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
