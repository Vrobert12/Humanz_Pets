import React, { useState, useEffect } from 'react';
import { NavigationContainer } from '@react-navigation/native';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import AsyncStorage from '@react-native-async-storage/async-storage';
import LoginScreen from './screens/LoginScreen';
import Home from './screens/Home';
import Profile from './screens/Profile';
import Settings from './screens/Settings';
import Icon from 'react-native-vector-icons/FontAwesome';

const Tab = createBottomTabNavigator();

const MainApp = ({ onLogout }) => (
    <Tab.Navigator
        screenOptions={({ route }) => ({
            tabBarIcon: ({ color, size }) => {
                let iconName;
                if (route.name === 'Home') iconName = 'home';
                else if (route.name === 'Profile') iconName = 'user';
                else if (route.name === 'Settings') iconName = 'cog';
                return <Icon name={iconName} size={size} color={color} />;
            },
            tabBarActiveTintColor: '#007bff',
            tabBarInactiveTintColor: 'gray',
            tabBarStyle: { backgroundColor: '#fff', paddingBottom: 5 },
        })}
    >
        <Tab.Screen name="Home" component={Home} />
        <Tab.Screen name="Profile">
            {(props) => <Profile {...props} onLogout={onLogout} />}  {/* Pass handleLogout to Profile */}
        </Tab.Screen>
        <Tab.Screen name="Settings" component={Settings} />
    </Tab.Navigator>
);

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
        setIsLoggedIn(false); // Redirects to login screen
    };

    return (
        <NavigationContainer>
            {isLoggedIn ? (
                <Tab.Navigator
                    screenOptions={({ route }) => ({
                        tabBarIcon: ({ color, size }) => {
                            let iconName;
                            if (route.name === 'Home') iconName = 'home';
                            else if (route.name === 'Profile') iconName = 'user';
                            else if (route.name === 'Settings') iconName = 'cog';
                            return <Icon name={iconName} size={size} color={color} />;
                        },
                        tabBarActiveTintColor: '#007bff',
                        tabBarInactiveTintColor: 'gray',
                        tabBarStyle: { backgroundColor: '#fff', paddingBottom: 5 },
                    })}
                >
                    <Tab.Screen name="Home" component={Home} />
                    <Tab.Screen
                        name="Profile"
                        component={Profile}
                        initialParams={{ onLogout: handleLogout }} // ✅ Pass the function as an initialParam
                    />
                    <Tab.Screen name="Settings" component={Settings} />
                </Tab.Navigator>
            ) : (
                <LoginScreen navigation={{ replace: setIsLoggedIn }} />
            )}
        </NavigationContainer>
    );
}


