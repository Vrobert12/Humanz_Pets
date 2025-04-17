import React, { useState, useEffect } from "react";
import {
    View,
    Text,
    TextInput,
    Button,
    ActivityIndicator,
    Alert
} from "react-native";
import AsyncStorage from "@react-native-async-storage/async-storage";
import { setLanguage } from "../i18n";
import { useTranslation } from 'react-i18next';

export default function Settings() {
    const [userData, setUserData] = useState(null);
    const [loading, setLoading] = useState(true);
    const [updating, setUpdating] = useState(false);
    const [userId, setUserId] = useState(null);
    const { t } = useTranslation();

    useEffect(() => {
        const fetchUserId = async () => {
            try {
                const storedUserId = await AsyncStorage.getItem("user_id");
                if (storedUserId) {
                    setUserId(storedUserId);
                } else {
                    Alert.alert(t('ERROR'), t('USER_NOT_LOGGED_IN'));
                }
            } catch (error) {
                console.error(t('ERROR_FETCHING_USER_ID'), error);
            }
        };

        fetchUserId();
    }, []);

    useEffect(() => {
        if (!userId) return;

        const apiUrl = `http://192.168.1.8/Humanz2.0/Humanz_Pets/phpForReact/getPets/user/${userId}`;

        fetch(apiUrl)
            .then((response) => response.json())
            .then((data) => {
                if (data.status === 200) {
                    setUserData(data.data[0]);
                } else {
                    Alert.alert(t('ERROR'), t('LOAD_USER_DATA_FAILED'));
                }
            })
            .catch((error) => {
                console.error(t('ERROR_FETCHING_DATA'), error);
                Alert.alert(t('ERROR'), `${t('FETCH_DATA_FAILED')}: ${error.message}`);
            })
            .finally(() => setLoading(false));
    }, [userId]);

    const handleUpdate = () => {
        if (!userId || !userData) return;

        setUpdating(true);
        const updatedUserData = { ...userData, id: userId };

        const apiUrl = `http://192.168.1.8/Humanz2.0/Humanz_Pets/phpForReact/updateUser.php`;

        fetch(apiUrl, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(updatedUserData)
        })
            .then(async (response) => {
                const text = await response.text();
                console.log(t('RAW_RESPONSE'), text);

                try {
                    const data = JSON.parse(text);
                    console.log(t('PARSED_RESPONSE_DATA'), data);
                    if (data.status === 200) {
                        Alert.alert(t('success'), t('USER_DATA_UPDATED'));
                    } else {
                        Alert.alert(t('ERROR'), `${t('UPDATE_FAILED')}: ${data.message}`);
                    }
                } catch (e) {
                    console.error(t('JSON_PARSING_ERROR'), e);
                    Alert.alert(t('ERROR'), t('INVALID_SERVER_RESPONSE'));
                }
            })
            .catch((error) => {
                console.error(t('ERROR_UPDATING_DATA'), error);
                Alert.alert(t('ERROR'), `${t('UPDATE_REQUEST_FAILED')}: ${error.message}`);
            })
            .finally(() => setUpdating(false));
    };

    const handleLanguageChange = async (langCode) => {
        setUserData((prevData) => ({
            ...prevData,
            usedLanguage: langCode
        }));
        await setLanguage(langCode);
    };

    return (
        <View style={{ flex: 1, padding: 20 }}>
            {loading ? (
                <ActivityIndicator size="large" />
            ) : userData ? (
                <>
                    <Text>{t('NAME')}:</Text>
                    <TextInput
                        value={userData.firstName}
                        onChangeText={(text) =>
                            setUserData({ ...userData, firstName: text })
                        }
                        style={{ borderWidth: 1, padding: 10, marginBottom: 10 }}
                    />

                    <Text>{t('LASTNAME')}:</Text>
                    <TextInput
                        value={userData.lastName}
                        onChangeText={(text) =>
                            setUserData({ ...userData, lastName: text })
                        }
                        style={{ borderWidth: 1, padding: 10, marginBottom: 10 }}
                    />

                    <Text>{t('PHONE')}:</Text>
                    <TextInput
                        value={userData.phoneNumber}
                        onChangeText={(text) =>
                            setUserData({ ...userData, phoneNumber: text })
                        }
                        style={{ borderWidth: 1, padding: 10, marginBottom: 10 }}
                    />

                    <Text>{t('LG')}:</Text>
                    <TextInput
                        value={userData.usedLanguage}
                        editable={false}
                        style={{
                            borderWidth: 1,
                            padding: 10,
                            marginBottom: 10,
                            backgroundColor: "#eee"
                        }}
                    />

                    <View style={{ marginBottom: 20 }}>
                        <Text>{t('SWITCH_LANGUAGE')}:</Text>
                        <View style={{ flexDirection: "row", gap: 10, marginTop: 10 }}>
                            <Button title={t('LANGUAGE_en')} onPress={() => handleLanguageChange("en")} />
                            <Button title={t('LANGUAGE_hu')} onPress={() => handleLanguageChange("hu")} />
                            <Button title={t('LANGUAGE_sr')} onPress={() => handleLanguageChange("sr")} />
                        </View>
                    </View>

                    <Button
                        title={t('UPDATE')}
                        onPress={handleUpdate}
                        disabled={updating}
                    />
                </>
            ) : (
                <Text>{t('NO_USER_DATA')}</Text>
            )}
        </View>
    );
}