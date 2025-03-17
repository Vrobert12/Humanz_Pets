import React, { useState, useEffect } from 'react';
import { NavigationContainer } from '@react-navigation/native';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { createDrawerNavigator } from '@react-navigation/drawer';
import { createStackNavigator } from '@react-navigation/stack';
import AsyncStorage from '@react-native-async-storage/async-storage';
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
import ProductDetails from './screens/ProductDetails';
import RegisterScreen from "./screens/RegisterScreen";
import { TouchableOpacity, View, Text } from "react-native";

// Navigation Stacks
const Tab = createBottomTabNavigator();
const Drawer = createDrawerNavigator();
const Stack = createStackNavigator();
const AuthStack = createStackNavigator();

// Profile Stack with Drawer Navigation
const ProfileStack = ({ onLogout }) => {
    const [reviewCount, setReviewCount] = useState(0); // Define reviewCount here
    const [loading, setLoading] = useState(true); // Track loading state

    // Function to fetch review count
    const fetchReviewCount = async (userId) => {
        setLoading(true); // Start loading
        const count = await getReviewCountFromAPI(userId);  // Replace with actual logic
        setReviewCount(count);  // Set review count
        setLoading(false); // Stop loading
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
                options={{
                    drawerLabel: () => (
                        <Text style={{ color: 'red',  fontSize: 20 }}>Log Out</Text>
                    ),
                }}>
                {(props) => <LogoutScreen {...props} onLogout={onLogout} />}
            </Drawer.Screen>
        </Drawer.Navigator>
    );
};

// Logout Screen
const LogoutScreen = ({ navigation, onLogout }) => {
    const handleLogoutPress = async () => {
        await AsyncStorage.multiRemove(['session_token', 'user_id']);
        onLogout();  // Update App state to re-render
    };

    return (
        <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }}>
            <Text style={{ fontSize: 18, marginBottom: 20 }}>Are you sure you want to log out?</Text>
            <TouchableOpacity
                onPress={handleLogoutPress}
                style={{ backgroundColor: 'red', padding: 10, borderRadius: 5 }}
            >
                <Text style={{ color: 'white', fontWeight: 'bold' }}>Log Out</Text>
            </TouchableOpacity>
        </View>
    );
};

// Shop Stack (Home & Product Details)
const ShopStack = () => (
    <Stack.Navigator>
        <Stack.Screen name="Home" component={Home} options={{ headerShown: false }} />
        <Stack.Screen name="ProductDetails" component={ProductDetails} />
    </Stack.Navigator>
);

// Main App with Bottom Tab Navigation
const MainApp = ({ onLogout }) => (
    <Tab.Navigator
        screenOptions={({ route }) => ({
            tabBarIcon: ({ color, size }) => {
                let iconName;
                if (route.name === 'Shop') iconName = 'shopping-cart';
                else if (route.name === 'BookAppointment') iconName = 'book';
                else if (route.name === 'Reservations') iconName = 'clipboard';
                else if (route.name === 'ProfileMenu') iconName = 'user';
                return <Icon name={iconName} size={size} color={color} />;
            },
            tabBarActiveTintColor: '#007bff',
            tabBarInactiveTintColor: 'gray',
            tabBarStyle: { backgroundColor: '#fff', paddingBottom: 5 },
        })}
    >
        <Tab.Screen name="Shop" component={ShopStack} options={{ headerShown: false }} />
        <Tab.Screen name="BookAppointment" component={BookAppointment} />
        <Tab.Screen name="Reservations" component={ReservationScreen} />
        <Tab.Screen name="ProfileMenu">
            {(props) => <ProfileStack {...props} onLogout={onLogout} />}
        </Tab.Screen>
    </Tab.Navigator>
);

// Authentication Stack (Login Screen)
const AuthStackScreen = ({ onLogin }) => (
    <AuthStack.Navigator>
        <AuthStack.Screen name="Login">
            {(props) => <LoginScreen {...props} onLogin={onLogin} />}
        </AuthStack.Screen>
        <AuthStack.Screen name="Register" component={RegisterScreen} />
    </AuthStack.Navigator>
);

// Main App Component
export default function App() {
    const [isLoggedIn, setIsLoggedIn] = useState(null);

    useEffect(() => {
        const checkLoginStatus = async () => {
            const token = await AsyncStorage.getItem('session_token');
            setIsLoggedIn(!!token);
        };
        checkLoginStatus();
    }, []);

    const handleLogin = () => setIsLoggedIn(true);
    const handleLogout = () => setIsLoggedIn(false);

    return (
        <NavigationContainer>
            {isLoggedIn ? (
                <MainApp onLogout={handleLogout} />
            ) : (
                <AuthStackScreen onLogin={handleLogin} />
            )}
        </NavigationContainer>
    );
}
