import React, { useState, useEffect } from 'react';
import { View, Text, FlatList, Image, StyleSheet } from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { useTranslation } from 'react-i18next';

export default function PurchasedProducts() {
    const { t } = useTranslation();
    const [purchasedProducts, setPurchasedProducts] = useState([]);
    const API_URL = 'https://humanz.stud.vts.su.ac.rs/phpForReact';
    const API_URL2 = 'https://humanz.stud.vts.su.ac.rs';

    useEffect(() => {
        const fetchPurchasedProducts = async () => {
            const userId = await AsyncStorage.getItem('user_id');

            fetch(`${API_URL}/get_purchased_products.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ userId: userId }),
            })
                .then(response => response.json())
                .then(data => setPurchasedProducts(data))
                .catch(error => console.error(t('error'), error));
        };

        fetchPurchasedProducts();
    }, []);


    return (
        <View style={styles.container}>
            <Text style={styles.header}>{t('purchaseHistory')}</Text>
            <FlatList
                data={purchasedProducts}
                renderItem={({ item }) => (
                    <View style={styles.productContainer}>
                        <Image source={{ uri: `${API_URL2}/pictures/products/${item.productPicture}` }} style={styles.image} />
                        <View style={styles.detailsContainer}>
                            <Text style={styles.name}>{item.productName}</Text>
                            <Text style={styles.price}>${item.productCost * item.sum}</Text>
                            <Text style={styles.quantity}>{t('SUM_PROD')}: {item.sum}</Text>
                            <Text style={styles.quantity}>{t('date')}: {item.payedDay}</Text>
                        </View>
                    </View>
                )}
                keyExtractor={item => item.userProductRelationId.toString()}
            />
        </View>
    );
}

const styles = StyleSheet.create({
    container: {
        flex: 1,
        padding: 20,
        backgroundColor: '#f8f8f8',
    },
    header: {
        fontSize: 22,
        fontWeight: 'bold',
        textAlign: 'center',
        marginVertical: 10,
    },
    productContainer: {
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
    image: {
        width: 80,
        height: 80,
        borderRadius: 10,
    },
    detailsContainer: {
        flex: 1,
        marginLeft: 10,
    },
    name: {
        fontSize: 16,
        fontWeight: 'bold',
    },
    price: {
        fontSize: 14,
        color: 'green',
    },
    quantity: {
        fontSize: 14,
        fontWeight: 'bold',
        color: '#555',
    },
});
