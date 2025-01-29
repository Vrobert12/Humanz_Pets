import React from 'react';
import { View, Text, TouchableOpacity, StyleSheet } from 'react-native';
import { NavigationContainer } from '@react-navigation/native';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import Icon from 'react-native-vector-icons/FontAwesome';

// Import screens
import Home from './screens/Home';
import Profile from './screens/Profile';
import Settings from './screens/Settings';

const Tab = createBottomTabNavigator();

export default function App() {
    return (
        <NavigationContainer>
            <Tab.Navigator
                screenOptions={({ route }) => ({
                    tabBarIcon: ({ color, size }) => {
                        let iconName;
                        if (route.name === 'Home') {
                            iconName = 'home';
                        } else if (route.name === 'Profile') {
                            iconName = 'user';
                        } else if (route.name === 'Settings') {
                            iconName = 'cog';
                        }
                        return <Icon name={iconName} size={size} color={color} />;
                    },
                    tabBarActiveTintColor: '#007bff',
                    tabBarInactiveTintColor: 'gray',
                    tabBarStyle: { backgroundColor: '#fff', paddingBottom: 5 },
                })}
            >
                <Tab.Screen name="Home" component={Home} />
                <Tab.Screen name="Profile" component={Profile} />
                <Tab.Screen name="Settings" component={Settings} />
            </Tab.Navigator>
        </NavigationContainer>
    );
}

const Footer = ({ navigation }) => (
    <View style={styles.footer}>
        <TouchableOpacity style={styles.button} onPress={() => navigation.navigate('Home')}>
            <Icon name="home" size={24} color="white" style={styles.icon} />
            <Text style={styles.buttonText}>Home</Text>
        </TouchableOpacity>
        <TouchableOpacity style={styles.button} onPress={() => navigation.navigate('Profile')}>
            <Icon name="user" size={24} color="white" style={styles.icon} />
            <Text style={styles.buttonText}>Profile</Text>
        </TouchableOpacity>
        <TouchableOpacity style={styles.button} onPress={() => navigation.navigate('Settings')}>
            <Icon name="cog" size={24} color="white" style={styles.icon} />
            <Text style={styles.buttonText}>Settings</Text>
        </TouchableOpacity>
    </View>
);

const styles = StyleSheet.create({
    screen: {
        flex: 1,
        justifyContent: 'center',
        alignItems: 'center',
        backgroundColor: '#f8f8f8',
    },
    text: {
        fontSize: 20,
        fontWeight: 'bold',
        color: '#333',
    },
    footer: {
        flexDirection: 'row',
        justifyContent: 'space-around',
        backgroundColor: '#007bff',
        paddingVertical: 15,
        position: 'absolute',
        bottom: 0,
        width: '100%',
    },
    button: {
        alignItems: 'center',
    },
    icon: {
        marginBottom: 5,
    },
    buttonText: {
        color: 'white',
        fontSize: 14,
        fontWeight: 'bold',
    },
});

