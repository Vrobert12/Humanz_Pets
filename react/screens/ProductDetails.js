import React, { useEffect, useState } from 'react';
import { View, Text, Image, StyleSheet, TouchableOpacity } from 'react-native';

export default function ProductDetails({ route }) {
    const { productId } = route.params;
    const [product, setProduct] = useState(null);

    useEffect(() => {
        fetch(`http://192.168.1.8/Humanz2.0/Humanz_Pets/phpForReact/get_product_details.php?id=${productId}`)
        //fetch(`http://192.168.43.125/Humanz_Pets/phpForReact/get_product_details.php?id=${productId}`)
            .then(response => response.json())
            .then(data => setProduct(data))
            .catch(error => console.error('Error fetching product details:', error));
    }, [productId]);

    console.log('products', product);

    if (!product) {
        return <Text style={styles.loading}>Loading...</Text>;
    }

    return (
        <View style={styles.container}>
            {/*<Image source={{ uri: 'http://192.168.43.125/Humanz_Pets/pictures/products/' + product.productPicture }} style={styles.image} />*/}
            <Image source={{ uri: 'http://192.168.1.8/Humanz2.0/Humanz_Pets/pictures/products/' + product.productPicture }} style={styles.image} />
            <Text style={styles.name}>{product.productName}</Text>
            <Text style={styles.price}>${product.productCost}</Text>
            <Text style={styles.description}>{product.description}</Text>
            <Text style={styles.releaseDate}>Released: {product.productRelease}</Text>
            <TouchableOpacity style={styles.addToCartButton}>
                <Text style={styles.addToCartText}>Add to Cart</Text>
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