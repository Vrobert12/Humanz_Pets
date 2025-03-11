import React, { useState, useEffect } from 'react';
import { View, Text, TouchableOpacity } from 'react-native';
import { NavigationContainer } from '@react-navigation/native';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { createDrawerNavigator } from '@react-navigation/drawer';
import AsyncStorage from '@react-native-async-storage/async-storage';
import LoginScreen from './screens/LoginScreen';
import Home from './screens/Home';
import Profile from './screens/Profile';
import Settings from './screens/Settings';
import RegisterPet from './screens/RegisterPet';
import BookAppointment from './screens/BookAppointment';
import ReservationScreen from './screens/ReservationsScreen';
import QrCode from './screens/QrCode.js';
import RatingsScreen from "./screens/RatingsScreen";
import Icon from 'react-native-vector-icons/FontAwesome';

const Tab = createBottomTabNavigator();
const Drawer = createDrawerNavigator();

// Logout Screen
const LogoutScreen = ({ navigation, onLogout }) => {
    const handleLogoutPress = async () => {
        await AsyncStorage.removeItem('session_token');
        onLogout(); // Call logout function
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

// Profile Stack
const ProfileStack = ({ onLogout }) => (
    <Drawer.Navigator>
        <Drawer.Screen name="Profile" component={Profile} />
        <Drawer.Screen name="QrCode" component={QrCode} />
        <Drawer.Screen name="Settings" component={Settings} />
        <Drawer.Screen name="RegisterPet" component={RegisterPet} />
        <Drawer.Screen name="RatingsScreen" component={RatingsScreen} />
        {/* Custom Logout Screen with Red Button */}
        <Drawer.Screen
            name="Logout"
            component={() => (
                <LogoutScreen onLogout={onLogout} />
            )}
            options={{
                drawerLabel: () => (
                    <Text style={{ color: 'red', fontWeight: 'bold' }}>Log Out</Text>
                ),
            }}
        />
    </Drawer.Navigator>
);

// MainApp with Bottom Tab Navigation
const MainApp = ({ onLogout }) => (
    <Tab.Navigator
        screenOptions={({ route }) => ({
            tabBarIcon: ({ color, size }) => {
                let iconName;
                if (route.name === 'Home') iconName = 'home';
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
        <Tab.Screen name="Home" component={Home} />
        <Tab.Screen name="ProfileMenu">
            {(props) => <ProfileStack {...props} onLogout={onLogout} />}
        </Tab.Screen>
        <Tab.Screen name="BookAppointment" component={BookAppointment} />
        <Tab.Screen name="Reservations" component={ReservationScreen} />
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
