import React, { useState, useEffect } from 'react';
import { NavigationContainer } from '@react-navigation/native';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { createDrawerNavigator } from '@react-navigation/drawer';
import { createStackNavigator } from '@react-navigation/stack';
import AsyncStorage from '@react-native-async-storage/async-storage';
import Icon from 'react-native-vector-icons/FontAwesome';
import { I18nextProvider, useTranslation } from 'react-i18next';
import i18n, { loadLanguage } from './i18n';

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
import PurchasedProducts from "./screens/PurchasedProducts";
import Pets from "./screens/Pets";
import { TouchableOpacity, View, Text } from "react-native";

const Tab = createBottomTabNavigator();
const Drawer = createDrawerNavigator();
const Stack = createStackNavigator();
const AuthStack = createStackNavigator();

const ProfileStack = ({ onLogout }) => {
    const { t } = useTranslation();
    const [reviewCount, setReviewCount] = useState(0);
    const [loading, setLoading] = useState(true);

    const fetchReviewCount = async (userId) => {
        setLoading(true);
        const count = await getReviewCountFromAPI(userId);
        setReviewCount(count);
        setLoading(false);
    };

    return (
        <Drawer.Navigator>
            <Drawer.Screen
                name="Profile"
                component={Profile}
                options={{
                    drawerLabel: () => (
                        <Text style={{ fontSize: 20 }}>{t('drawer.profile')}</Text>
                    )
                }}
            />
            <Drawer.Screen
                name="QrCode"
                component={QrCode}
                options={{
                    drawerLabel: () => (
                        <Text style={{ fontSize: 20 }}>{t('drawer.qrcode')}</Text>
                    )
                }}
            />
            <Drawer.Screen
                name="Settings"
                component={Settings}
                options={{
                    drawerLabel: () => (
                        <Text style={{ fontSize: 20 }}>{t('drawer.settings')}</Text>
                    )
                }}
            />
            <Drawer.Screen
                name="Pets"
                component={Pets}
                options={{
                    drawerLabel: () => (
                        <Text style={{ fontSize: 20 }}>{t('drawer.pets')}</Text>
                    )
                }}
            />
            <Drawer.Screen
                name="RegisterPet"
                component={RegisterPet}
                options={{
                    drawerLabel: () => (
                        <Text style={{ fontSize: 20 }}>{t('drawer.registerPet')}</Text>
                    )
                }}
            />
            <Drawer.Screen
                name="PurchaseHistory"
                component={PurchasedProducts}
                options={{
                    drawerLabel: () => (
                        <Text style={{ fontSize: 20 }}>{t('drawer.purchaseHistory')}</Text>
                    )
                }}
            />
            <Drawer.Screen
                name="RatingsScreen"
                component={() => <RatingsScreen fetchReviewCount={fetchReviewCount} />}
                options={{
                    drawerLabel: () => (
                        <View style={{ flexDirection: 'row', alignItems: 'center' }}>
                            <Text style={{ fontSize: 20 }}>{t('drawer.ratings')}</Text>
                            {reviewCount > 0 && (
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
                                    <Text style={{ color: 'white', fontSize: 20 }}>
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
                        <Text style={{ color: 'red', fontSize: 20 }}>{t('drawer.logout')}</Text>
                    ),
                }}
            >
                {(props) => <LogoutScreen {...props} onLogout={onLogout} />}
            </Drawer.Screen>
        </Drawer.Navigator>
    );
};

const LogoutScreen = ({ navigation, onLogout }) => {
    const { t } = useTranslation();

    const handleLogoutPress = async () => {
        await AsyncStorage.multiRemove(['session_token', 'user_id']);
        onLogout();
    };

    return (
        <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }}>
            <Text style={{ fontSize: 18, marginBottom: 20 }}>{t('logout.confirm')}</Text>
            <TouchableOpacity
                onPress={handleLogoutPress}
                style={{ backgroundColor: 'red', padding: 10, borderRadius: 5 }}
            >
                <Text style={{ color: 'white', fontWeight: 'bold' }}>{t('logout.button')}</Text>
            </TouchableOpacity>
        </View>
    );
};

const ShopStack = () => (
    <Stack.Navigator>
        <Stack.Screen name="Home" component={Home} options={{ headerShown: false }} />
        <Stack.Screen name="ProductDetails" component={ProductDetails} />
    </Stack.Navigator>
);

const MainApp = ({ onLogout }) => {
    const { t } = useTranslation();

    return (
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
            <Tab.Screen
                name="Shop"
                component={ShopStack}
                options={{ headerShown: false, title: t('tabs.shop') }}
            />
            <Tab.Screen
                name="BookAppointment"
                component={BookAppointment}
                options={{ title: t('tabs.bookAppointment') }}
            />
            <Tab.Screen
                name="Reservations"
                component={ReservationScreen}
                options={{ title: t('tabs.reservations') }}
            />
            <Tab.Screen
                name="ProfileMenu"
                options={{ title: t('tabs.profile') }}
            >
                {(props) => <ProfileStack {...props} onLogout={onLogout} />}
            </Tab.Screen>
        </Tab.Navigator>
    );
};

const AuthStackScreen = ({ onLogin }) => (
    <AuthStack.Navigator>
        <AuthStack.Screen name="Login">
            {(props) => <LoginScreen {...props} onLogin={onLogin} />}
        </AuthStack.Screen>
        <AuthStack.Screen name="Register" component={RegisterScreen} />
    </AuthStack.Navigator>
);

export default function App() {
    const [isLoggedIn, setIsLoggedIn] = useState(null);

    useEffect(() => {
        const checkLoginStatus = async () => {
            const token = await AsyncStorage.getItem('session_token');
            setIsLoggedIn(!!token);
        };
        checkLoginStatus();
        loadLanguage();
    }, []);

    const handleLogin = () => setIsLoggedIn(true);
    const handleLogout = () => setIsLoggedIn(false);

    return (
        <I18nextProvider i18n={i18n}>
            <NavigationContainer>
                {isLoggedIn ? (
                    <MainApp onLogout={handleLogout} />
                ) : (
                    <AuthStackScreen onLogin={handleLogin} />
                )}
            </NavigationContainer>
        </I18nextProvider>
    );
}
