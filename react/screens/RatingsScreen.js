import React, { useState, useEffect } from 'react';
import { View, Text, Alert, ActivityIndicator, Button } from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import axios from 'axios';
import StarRating from 'react-native-star-rating-widget';

const API_URL = 'http://192.168.1.8/Humanz2.0/Humanz_Pets/check_reviews.php';
const SUBMIT_URL = 'http://192.168.1.8/Humanz2.0/Humanz_Pets/submit_review.php';

const RatingsScreen = () => {
    const [reviews, setReviews] = useState([]);
    const [loading, setLoading] = useState(true);

    // State to hold temporarily selected rating for submission
    const [tempRatings, setTempRatings] = useState({});

    useEffect(() => {
        fetchReviews();
    }, []);

    const fetchReviews = async () => {
        const storedUserId = await AsyncStorage.getItem('user_id');
        if (!storedUserId) {
            Alert.alert("Error", "User not logged in.");
            return;
        }

        try {
            const response = await axios.post(API_URL, { user_id: storedUserId });
            console.log(response.data.reviews);
            setReviews(response.data.reviews);
        } catch (error) {
            console.error("Error fetching reviews:", error);
        } finally {
            setLoading(false);
        }
    };

    const handleSubmitRating = async (reviewId) => {
        console.log(reviewId);
        const rating = tempRatings[reviewId];
        if (rating == null) {
            Alert.alert("Error", "Please select a rating.");
            return;
        }

        try {
            await axios.post(SUBMIT_URL, { review_id: reviewId, rating: rating });
            Alert.alert("Success", "Rating submitted!");
            fetchReviews(); // Re-fetch reviews after submission
        } catch (error) {
            console.error("Error submitting rating:", error);
            Alert.alert("Error", "Could not submit rating.");
        }
    };

    if (loading) return <ActivityIndicator size="large" color="blue" />;

    return (
        <View style={{ flex: 1, padding: 20 }}>
            <Text style={{ fontSize: 20, fontWeight: 'bold' }}>Rate Veterinarians</Text>
            {reviews.length === 0 ? (
                <Text>No pending reviews.</Text>
            ) : (
                reviews.map((review, index) => (
                    <View key={review.reviewId || index} style={{ marginBottom: 20 }}>
                        <Text>Veterinarian: {review.veterinarian_name}</Text>
                        <StarRating
                            rating={tempRatings[review.reviewId] || review.review || 0} // Show temporary rating or stored rating
                            onChange={(newRating) => {
                                setTempRatings((prevRatings) => ({
                                    ...prevRatings,
                                    [review.reviewId]: newRating, // Save temporarily selected rating
                                }));
                            }}
                        />
                        <Button
                            title="Submit Rating"
                            onPress={() => handleSubmitRating(review.reviewId)}
                        />
                    </View>
                ))
            )}
        </View>
    );
};

export default RatingsScreen;
