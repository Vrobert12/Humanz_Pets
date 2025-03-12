import React, { useState, useEffect } from 'react';
import { View, Text, Alert, ActivityIndicator, Button } from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import axios from 'axios';
import StarRating from 'react-native-star-rating-widget';

//const API_URL = 'http://192.168.43.125/Humanz_Pets/phpForReact/check_reviews.php';
//const SUBMIT_URL = 'http://192.168.43.125/Humanz_Pets/phpForReact/submit_review.php';
const API_URL = 'http://192.168.1.8/Humanz2.0/Humanz_Pets/phpForReact/check_reviews.php';
const SUBMIT_URL = 'http://192.168.1.8/Humanz2.0/Humanz_Pets/phpForReact/submit_review.php';

const RatingsScreen = ({ fetchReviewCount }) => { // Receive fetchReviewCount as a prop
    const [reviews, setReviews] = useState([]);
    const [loading, setLoading] = useState(true);
    const [tempRatings, setTempRatings] = useState({});
    const [userId, setUserId] = useState(null);

    useEffect(() => {
        fetchUserId();
    }, []);

    useEffect(() => {
        if (userId) {
            fetchReviews(userId);
        }
    }, [userId]);

    const fetchUserId = async () => {
        try {
            const storedUserId = await AsyncStorage.getItem('user_id');
            if (!storedUserId) {
                Alert.alert("Error", "User not logged in.");
            } else {
                setUserId(storedUserId);
            }
        } catch (error) {
            console.error("Error fetching user ID:", error);
        }
    };

    const fetchReviews = async (userId) => {
        try {
            const response = await axios.post(API_URL, { user_id: userId });
            setReviews(response.data.reviews);
        } catch (error) {
            console.error("Error fetching reviews:", error);
        } finally {
            setLoading(false);
        }
    };

    const handleSubmitRating = async (reviewId) => {
        const rating = tempRatings[reviewId];
        if (rating == null) {
            Alert.alert("Error", "Please select a rating.");
            return;
        }

        try {
            await axios.post(SUBMIT_URL, { review_id: reviewId, rating: rating });
            Alert.alert("Success", "Rating submitted!");
            fetchReviews(userId); // Re-fetch reviews after submission
            fetchReviewCount(userId); // Update the review count after submission
        } catch (error) {
            console.error("Error submitting rating:", error);
            Alert.alert("Error", "Could not submit rating.");
        }
    };

    if (loading) return <ActivityIndicator size="large" color="blue" />;

    return (
        <View style={{ flex: 1, padding: 20 }}>
            <Text style={{ fontSize: 26, fontWeight: 'bold' }}>Rate Veterinarians</Text>
            {reviews.length === 0 ? (
                <Text>No pending reviews.</Text>
            ) : (
                reviews.map((review, index) => (
                    <View key={review.reviewId || index} style={{ marginBottom: 20 }}>
                        <Text style={{ fontSize: 24 }}>Veterinarian: {review.veterinarian_name}</Text>
                        <Text style={{ fontSize: 20 }}>Checked time: {review.reviewTime}</Text>
                        <StarRating
                            rating={tempRatings[review.reviewId] || review.review || 0}
                            onChange={(newRating) => {
                                setTempRatings((prevRatings) => ({
                                    ...prevRatings,
                                    [review.reviewId]: newRating,
                                }));
                            }}
                            starSize={60}
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

