import React, { useEffect, useState } from "react";
import { View, Text, Image, ActivityIndicator, StyleSheet, TouchableOpacity } from "react-native";
import AsyncStorage from "@react-native-async-storage/async-storage";

export default function Profile({ route }) {
    const [userData, setUserData] = useState(null);
    const [loading, setLoading] = useState(true);
    const [qrCodePath, setQrCodePath] = useState(""); // State for QR Code
    const { onLogout } = route.params || {};

    useEffect(() => {
        const fetchUserData = async () => {
            try {
                const userId = await AsyncStorage.getItem("user_id");
                console.log(userId);
                if (!userId) throw new Error("User ID not found");

                const response = await fetch(`http://192.168.1.8/Humanz2.0/Humanz_Pets/getPets/user/${userId}`);
                //const response = await fetch(`http://192.168.43.125/Humanz_Pets/getPets/user/${userId}`);
                const data = await response.json();

                if (data.status === 200) {
                    setUserData(data.data[0]);
                }
            } catch (error) {
                console.error("Error fetching user data:", error);
            } finally {
                setLoading(false);
            }
        };

        fetchUserData();
    }, []);

    // ✅ Define fetchQRCode inside useEffect
    const fetchQRCode = async () => {
        try {
            const userId = await AsyncStorage.getItem("user_id");
            if (!userId) throw new Error("User ID not found");

            const response = await fetch(`http://192.168.1.8/Humanz2.0/Humanz_Pets/getQrCode.php?user=${userId}`);
            //const response = await fetch(`http://192.168.43.125/Humanz_Pets/getQrCode.php?user=${userId}`);
            const text = await response.text(); // ✅ Log raw response
            console.log("Raw Response:", text);

            const data = JSON.parse(text);
            if (data.status === 200) {
                setQrCodePath(`http://192.168.1.8/Humanz_Pets/${data.data.path}`); // ✅ Ensure full path
                //setQrCodePath(`http://192.168.43.125/Humanz_Pets/${data.data.path}`); // ✅ Ensure full path
            } else {
                setQrCodePath(""); // No QR code found
            }
        } catch (error) {
            console.error("Error fetching QR code:", error);
        }
    };

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
                source={{ uri: `http://192.168.1.8/Humanz2.0/Humanz_Pets/pictures/${String(userData.profilePic)}` }}
                //source={{ uri: `http://192.168.43.125/Humanz_Pets/pictures/${String(userData.profilePic)}` }}
                style={styles.profileImage}
            />
            <Text style={styles.userName}>{String(userData.firstName)} {String(userData.lastName)}</Text>
            <Text style={styles.info}>📧 {String(userData.userMail)}</Text>
            <Text style={styles.info}>📞 {String(userData.phoneNumber)}</Text>
            <Text style={styles.info}>🌐 Language: {String(userData.usedLanguage)}</Text>
            <Text style={styles.info}>🔒 Privilege: {String(userData.privilage)}</Text>

            {/* ✅ QR Code Button */}
            <TouchableOpacity onPress={fetchQRCode} style={styles.qrButton}>
                <Text style={styles.qrText}>Show QR Code</Text>
            </TouchableOpacity>

            {/* ✅ Show QR Code if available */}
            {qrCodePath ? (
                <Image source={{ uri: qrCodePath }} style={styles.qrImage} />
            ) : null}

            {/* Logout Button */}
            {onLogout && (
                <TouchableOpacity onPress={onLogout} style={styles.logoutButton}>
                    <Text style={styles.logoutText}>Log Out</Text>
                </TouchableOpacity>
            )}
        </View>
    );
}

const styles = StyleSheet.create({
    container: {
        flex: 1,
        justifyContent: "center",
        alignItems: "center",
        backgroundColor: "#f8f8f8",
        padding: 20,
    },
    profileImage: {
        width: 120,
        height: 120,
        borderRadius: 60,
        marginBottom: 10,
        borderWidth: 2,
        borderColor: "#007bff",
    },
    userName: {
        fontSize: 22,
        fontWeight: "bold",
        color: "#333",
        marginBottom: 5,
    },
    info: {
        fontSize: 16,
        color: "#555",
        marginBottom: 5,
    },
    qrButton: {
        backgroundColor: "#28a745",
        padding: 10,
        borderRadius: 5,
        marginTop: 20,
    },
    qrText: {
        color: "white",
        fontWeight: "bold",
    },
    qrImage: {
        width: 150,
        height: 150,
        marginTop: 10,
        borderWidth: 1,
        borderColor: "#ddd",
    },
    logoutButton: {
        backgroundColor: "red",
        padding: 10,
        borderRadius: 5,
        marginTop: 20,
    },
    logoutText: {
        color: "white",
        fontWeight: "bold",
    },
});

