import React, { useState, useEffect } from 'react';
import { View, Text, TouchableOpacity } from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage'; // Ensure AsyncStorage is imported
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { createDrawerNavigator } from '@react-navigation/drawer';
import { NavigationContainer } from '@react-navigation/native';
import Icon from 'react-native-vector-icons/FontAwesome';
import Profile from './screens/Profile';
import QrCode from './screens/QrCode';
import Settings from './screens/Settings';
import RegisterPet from './screens/RegisterPet';
import RatingsScreen from './screens/RatingsScreen';
import LoginScreen from './screens/LoginScreen';
import Home from './screens/Home';
import BookAppointment from './screens/BookAppointment';
import ReservationScreen from './screens/ReservationsScreen';

// Your API URL
const API_URL = 'http://192.168.43.125/Humanz_Pets/check_reviews.php';

const Tab = createBottomTabNavigator();
const Drawer = createDrawerNavigator();

// Profile Stack with dynamic user_id and review count fetch
// In ProfileStack Component
const ProfileStack = ({ onLogout }) => {
    const [reviewCount, setReviewCount] = useState(0);
    const [loading, setLoading] = useState(true);
    const [userId, setUserId] = useState(null);

    useEffect(() => {
        const getUserId = async () => {
            try {
                const storedUserId = await AsyncStorage.getItem('user_id');
                if (storedUserId) {
                    setUserId(storedUserId);
                    fetchReviewCount(storedUserId); // Fetch reviews for specific user
                } else {
                    console.error('User ID is not available');
                }
            } catch (error) {
                console.error('Error fetching user ID:', error);
            }
        };

        getUserId();
    }, []);

    const fetchReviewCount = async (userId) => {
        try {
            const response = await fetch(API_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ user_id: userId })
            });

            const data = await response.json();
            if (data.ReviewCount !== undefined) {
                setReviewCount(data.ReviewCount);
            } else {
                console.error('ReviewCount is undefined or not returned');
            }
        } catch (error) {
            console.error('Error fetching review count:', error);
        } finally {
            setLoading(false);
        }
    };

    return (
        <Drawer.Navigator>
            <Drawer.Screen
                name="Profile"
                component={Profile}
                options={{
                    drawerLabel: () => (
                        <Text style={{ fontSize: 20,  }}>Profile</Text>
                    )
                }}
            />
            <Drawer.Screen name="QrCode" component={QrCode}
                           options={{
                               drawerLabel: () => (
                                   <Text style={{ fontSize: 20,  }}>QrCode</Text>
                               )
                           }}
            />
            <Drawer.Screen name="Settings" component={Settings}
                           options={{
                               drawerLabel: () => (
                                   <Text style={{ fontSize: 20,  }}>Settings</Text>
                               )
                           }}
            />

            <Drawer.Screen name="RegisterPet" component={RegisterPet}
                           options={{
                               drawerLabel: () => (
                                   <Text style={{ fontSize: 20,  }}>RegisterPet</Text>
                               )
                           }}
            />

            <Drawer.Screen
                name="RatingsScreen"
                component={() => <RatingsScreen fetchReviewCount={fetchReviewCount} />}
                options={{
                    drawerLabel: () => (
                        <View style={{ flexDirection: 'row', alignItems: 'center' }}>
                            <Text style={{ fontSize: 20,  }}>Ratings</Text>
                            {reviewCount > 0 && (  // Only show rating number and circle if reviewCount > 0
                                <View
                                    style={{
                                        backgroundColor: 'red',
                                        borderRadius: 30,
                                        width: 30,
                                        height: 30,
                                        justifyContent: 'center',
                                        alignItems: 'center',
                                        marginLeft: 10,
                                    }}
                                >
                                    <Text style={{ color: 'white',  fontSize: 20 }}>
                                        {loading ? '...' : reviewCount}
                                    </Text>
                                </View>
                            )}
                        </View>
                    ),
                }}
            />

            <Drawer.Screen
                name="Logout"
                component={LogoutScreen}
                options={{
                    drawerLabel: () => (
                        <Text style={{ color: 'red',  fontSize: 20 }}>Log Out</Text>
                    ),
                }}
            />
        </Drawer.Navigator>
    );
};



// Logout Screen
const LogoutScreen = ({ navigation, onLogout }) => {
    const handleLogoutPress = async () => {
        await AsyncStorage.removeItem('session_token');
        await AsyncStorage.removeItem('user_id'); // Remove user_id on logout
        onLogout();
        navigation.replace('Login'); // Navigate to login screen after logout
    };

    return (
        <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }}>
            <Text style={{ fontSize: 18, marginBottom: 20 }}>Biztosan ki szeretnél jelentkezni?</Text>
            <TouchableOpacity
                onPress={handleLogoutPress}
                style={{ backgroundColor: 'red', padding: 10, borderRadius: 5 }}
            >
                <Text style={{ color: 'white', fontWeight: 'bold' }}>Log Out</Text>
            </TouchableOpacity>
        </View>
    );
};

// MainApp with Bottom Tab Navigation
const MainApp = ({ onLogout }) => (
    <Tab.Navigator
        screenOptions={({ route }) => ({
            tabBarIcon: ({ color, size }) => {
                let iconName;
                if (route.name === 'Shop') iconName = 'shopping-cart';
                else if (route.name === 'ProfileMenu') iconName = 'user';
                else if (route.name === 'BookAppointment') iconName = 'book';
                else if (route.name === 'Reservations') iconName = 'clipboard';
                return <Icon name={iconName} size={size} color={color} />;
            },
            tabBarActiveTintColor: '#007bff',
            tabBarInactiveTintColor: 'gray',
            tabBarStyle: { backgroundColor: '#fff', paddingBottom: 5 },
        })}
    >
        <Tab.Screen name="Shop" component={Home} />
        <Tab.Screen name="BookAppointment" component={BookAppointment} />
        <Tab.Screen name="Reservations" component={ReservationScreen} />
        <Tab.Screen name="ProfileMenu">
            {(props) => <ProfileStack {...props} onLogout={onLogout} />}
        </Tab.Screen>
    </Tab.Navigator>
);

// App Component (Main Entry Point)
export default function App() {
    const [isLoggedIn, setIsLoggedIn] = useState(null);

    useEffect(() => {
        const checkLoginStatus = async () => {
            const token = await AsyncStorage.getItem('session_token');
            setIsLoggedIn(!!token);
        };
        checkLoginStatus();
    }, []);

    const handleLogout = async () => {
        await AsyncStorage.removeItem('session_token');
        setIsLoggedIn(false); // Update login state after logout
    };

    return (
        <NavigationContainer>
            {isLoggedIn ? (
                <MainApp onLogout={handleLogout} />
            ) : (
                <LoginScreen navigation={{ replace: setIsLoggedIn }} />  // Make sure LoginScreen has a way to set the login state
            )}
        </NavigationContainer>
    );
}
