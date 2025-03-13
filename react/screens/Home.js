import React, { useEffect, useState } from 'react';
import { View, Text, FlatList, Image, StyleSheet, TouchableOpacity } from 'react-native';

export default function Home({ navigation }) {
    const [products, setProducts] = useState([]);
    const API_URL = 'http://192.168.43.125/Humanz_Pets/phpForReact/get_products.php';
    //const API_URL = 'http://192.168.1.8/Humanz2.0/Humanz_Pets/phpForReact/get_products.php';

    useEffect(() => {
        fetch(API_URL) // Replace with your actual API URL
            .then(response => response.json())
            .then(data => setProducts(data))
            .catch(error => console.error('Error fetching products:', error));
    }, []);


    const renderItem = ({ item }) => (
        <View style={styles.productContainer}>
            <Image source={{ uri: 'http://192.168.43.125/Humanz_Pets/pictures/products/' + item.productPicture }} style={styles.image} />

            {/*<Image source={{ uri: 'http://192.168.1.8/Humanz2.0/Humanz_Pets/pictures/products/' + item.productPicture }} style={styles.image} />*/}
            <Text style={styles.name}>{item.productName}</Text>
            <Text style={styles.price}>${item.productCost}</Text>
            <TouchableOpacity
                style={styles.detailsButton}
                onPress={() => navigation.navigate('ProductDetails', { productId: item.productId })}
            >
                <Text style={styles.detailsButtonText}>Details</Text>
            </TouchableOpacity>
        </View>
    );

    return (
        <View style={styles.container}>
            <Text style={styles.header}>🛒 Products</Text>
            <FlatList
                data={products}
                renderItem={renderItem}
                keyExtractor={item => item.productId.toString()}
                horizontal
                showsHorizontalScrollIndicator={false}
            />
        </View>
    );
}

const styles = StyleSheet.create({
    container: {
        flex: 1,
        paddingTop: 20,
        backgroundColor: '#f8f8f8',
        marginTop: 50
    },
    header: {
        fontSize: 22,
        fontWeight: 'bold',
        textAlign: 'center',
        marginBottom: 10,
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
        height: 250
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
});
