import React, { useEffect, useState, useCallback } from 'react';
import {View, Text, FlatList, Image, StyleSheet, TouchableOpacity, TextInput} from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { useFocusEffect } from '@react-navigation/native';
import { useTranslation } from 'react-i18next';

export default function Home({ navigation }) {
    const [searchQuery, setSearchQuery] = useState('');
    const [products, setProducts] = useState([]);
    const [cart, setCart] = useState([]);
    const API_URL = 'https://humanz.stud.vts.su.ac.rs/phpForReact';
    const API_URL2 = 'https://humanz.stud.vts.su.ac.rs';
    const { t } = useTranslation();

    useEffect(() => {
        fetch(`${API_URL}/get_products.php`)
            .then(response => response.json())
            .then(data => setProducts(data))
            .catch(error => console.error(t('ERROR'), error));
    }, []);

    const filteredProducts = products.filter(product =>
        product.productName.toLowerCase().includes(searchQuery.toLowerCase())
    );

    const fetchCartItems = async () => {
        const userId = await AsyncStorage.getItem('user_id');
        fetch(`${API_URL}/get_cart.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ userId })
        })
            .then(response => response.json())
            .then(data => setCart(data))
            .catch(error => console.error(t('ERROR'), error));
    };

    useFocusEffect(
        useCallback(() => {
            fetchCartItems();
        }, [])
    );

    const deleteCartItem = async (userProductRelationId) => {
        try {
            const response = await fetch(`${API_URL}/delete_cart_item.php`, {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: userProductRelationId })
            });

            const data = await response.json();
            if (data.success) {
                fetchCartItems();
            } else {
                console.error(t('ERROR'), data.message);
            }
        } catch (error) {
            console.error(t('ERROR'), error);
        }
    };

    const totalPrice = cart.reduce((total, item) => total + (item.price * item.sum), 0).toFixed(2);

    return (
        <View style={styles.container}>
            <Text style={styles.header}>🛍️ {t('PRODUCT')}</Text>
            <TextInput
                style={styles.searchBar}
                placeholder={t('SEARCH')}
                placeholderTextColor="#888"
                value={searchQuery}
                onChangeText={setSearchQuery}
            />
            <FlatList
                data={filteredProducts}
                renderItem={({item}) => (
                    <View style={styles.productContainer}>
                        <Image source={{uri: `${API_URL2}/pictures/products/${item.productPicture}`}}
                               style={styles.image}/>
                        <Text style={styles.name}>{item.productName}</Text>
                        <Text style={styles.price}>${item.productCost}</Text>
                        <TouchableOpacity
                            style={styles.detailsButton}
                            onPress={() => navigation.navigate('ProductDetails', {productId: item.productId})}
                        >
                            <Text style={styles.detailsButtonText}>{t('DETAILS')}</Text>
                        </TouchableOpacity>
                    </View>
                )}
                keyExtractor={item => item.productId.toString()}
                horizontal
                showsHorizontalScrollIndicator={false}
                style={styles.listStyle}
            />

            <View style={styles.cartHeaderContainer}>
                <Text style={styles.header}>{t('CART')}</Text>
                <Text style={styles.totalPrice}>{t('TOTAL_PRICE')}: ${totalPrice}</Text>
            </View>

            <FlatList
                data={cart}
                renderItem={({item}) => (
                    <View style={styles.cartItem}>
                        <Image source={{uri: `${API_URL2}/pictures/products/${item.productPicture}`}}
                               style={styles.image}/>
                        <View style={styles.cartDetails}>
                            <Text style={styles.name}>{item.productName}</Text>
                            <Text style={styles.price}>${item.price} x {item.sum}</Text>
                        </View>
                        <TouchableOpacity style={styles.deleteButton}
                                          onPress={() => deleteCartItem(item.userProductRelationId)}>
                            <Text style={styles.deleteButtonText}>{t('DELETE_PRODUCT')}</Text>
                        </TouchableOpacity>
                    </View>
                )}
                keyExtractor={item => item.userProductRelationId.toString()}
                style={styles.cartContainer}
                ListFooterComponent={<View style={{height: 10}}/>}
            />
        </View>
    );
}

const styles = StyleSheet.create({
    listStyle: {
        height: 20,
    },
    searchBar: {
        height: 40,
        backgroundColor: '#fff',
        borderRadius: 10,
        paddingHorizontal: 10,
        fontSize: 14,
        marginBottom: 10,
        borderWidth: 1,
        borderColor: '#ccc',
    },
    container: {
        flex: 1,
        paddingTop: 30,
        backgroundColor: '#f8f8f8',
        paddingHorizontal: 10,
    },
    header: {
        fontSize: 22,
        fontWeight: 'bold',
        textAlign: 'center',
        marginVertical: 10,
    },
    cartHeaderContainer: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center',
        marginVertical: 10,
    },
    totalPrice: {
        fontSize: 18,
        fontWeight: 'bold',
        color: 'green',
    },
    productContainer: {
        width: 150,
        marginHorizontal: 10,
        alignItems: 'center',
        backgroundColor: '#fff',
        padding: 10,
        borderRadius: 10,
        shadowColor: '#000',
        shadowOpacity: 0.1,
        shadowOffset: { width: 0, height: 2 },
        shadowRadius: 4,
        elevation: 3,
        height: 250,
    },
    image: {
        width: 100,
        height: 100,
        borderRadius: 10,
    },
    name: {
        marginTop: 5,
        fontSize: 16,
        fontWeight: 'bold',
    },
    price: {
        fontSize: 14,
        color: 'green',
    },
    detailsButton: {
        marginTop: 10,
        backgroundColor: '#007bff',
        paddingVertical: 5,
        paddingHorizontal: 10,
        borderRadius: 5,
    },
    detailsButtonText: {
        color: '#fff',
        fontWeight: 'bold',
    },
    cartContainer: {
        flex: 1,
        marginTop: 10,
    },
    cartItem: {
        flexDirection: 'row',
        alignItems: 'center',
        backgroundColor: '#fff',
        padding: 10,
        borderRadius: 10,
        marginVertical: 5,
        shadowColor: '#000',
        shadowOpacity: 0.1,
        shadowOffset: { width: 0, height: 2 },
        shadowRadius: 4,
        elevation: 3,
    },
    cartDetails: {
        flex: 1,
        marginLeft: 10,
    },
    deleteButton: {
        backgroundColor: '#dc3545',
        padding: 5,
        borderRadius: 5,
    },
    deleteButtonText: {
        color: '#fff',
        fontWeight: 'bold',
    },
});