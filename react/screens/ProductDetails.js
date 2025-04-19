import React, { useEffect, useState } from 'react';
import { View, Text, Image, StyleSheet, TouchableOpacity, TextInput, Alert } from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { useTranslation } from 'react-i18next';

export default function ProductDetails({ route }) {
    const { productId } = route.params;
    const [product, setProduct] = useState(null);
    const [quantity, setQuantity] = useState(1);
    const { t } = useTranslation();

    useEffect(() => {
        fetch(`http://192.168.1.8/Humanz2.0/Humanz_Pets/phpForReact/get_product_details.php?id=${productId}`)
            .then(response => response.json())
            .then(data => setProduct(data))
            .catch(error => console.error('Error fetching product details:', error));
    }, [productId]);

    const handleAddToCart = async () => {
        const userId = await AsyncStorage.getItem('user_id');
        if (!userId || !product) return;

        const totalPrice = quantity * product.productCost;
        const currentDate = new Date().toISOString().split('T')[0];

        fetch('http://192.168.1.8/Humanz2.0/Humanz_Pets/phpForReact/add_to_cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                userId,
                productName: product.productName,
                productPicture: product.productPicture,
                productId,
                sum: quantity,
                price: totalPrice,
                productPayed: 0,
                boughtDay: currentDate
            })
        })
            .then(response => response.json())
            .then(data => Alert.alert(t('success'), t('productAddedToCart')))
            .catch(error => console.error(t('error'), error));
    };

    if (!product) {
        return <Text style={styles.loading}>Loading...</Text>;
    }

    return (
        <View style={styles.container}>
            <Image source={{ uri: 'http://192.168.1.8/Humanz2.0/Humanz_Pets/pictures/products/' + product.productPicture }} style={styles.image} />
            <Text style={styles.name}>{product.productName}</Text>
            <Text style={styles.price}>${product.productCost}</Text>
            <Text style={styles.description}>{product.description}</Text>
            <Text style={styles.releaseDate}>{t('released')}: {product.productRelease}</Text>
            <TextInput
                style={styles.quantityInput}
                keyboardType='numeric'
                value={quantity.toString()}
                onChangeText={(text) => setQuantity(Math.max(1, parseInt(text) || 1))}
            />
            <TouchableOpacity style={styles.addToCartButton} onPress={handleAddToCart}>
                <Text style={styles.addToCartText}>{t('ADDCART')}</Text>
            </TouchableOpacity>
        </View>
    );
}

const styles = StyleSheet.create({
    container: {
        flex: 1,
        padding: 20,
        alignItems: 'center',
        backgroundColor: '#f8f8f8',
    },
    image: {
        width: 200,
        height: 200,
        borderRadius: 10,
    },
    name: {
        marginTop: 10,
        fontSize: 22,
        fontWeight: 'bold',
    },
    price: {
        fontSize: 18,
        color: 'green',
        marginBottom: 10,
    },
    description: {
        fontSize: 16,
        textAlign: 'center',
        marginBottom: 10,
    },
    releaseDate: {
        fontSize: 14,
        color: 'gray',
    },
    quantityInput: {
        marginTop: 10,
        borderWidth: 1,
        borderColor: '#ccc',
        padding: 10,
        width: 100,
        textAlign: 'center',
        borderRadius: 5,
    },
    addToCartButton: {
        marginTop: 20,
        backgroundColor: '#28a745',
        paddingVertical: 10,
        paddingHorizontal: 20,
        borderRadius: 5,
    },
    addToCartText: {
        color: '#fff',
        fontWeight: 'bold',
    },
    loading: {
        textAlign: 'center',
        marginTop: 50,
        fontSize: 18,
    },
});
