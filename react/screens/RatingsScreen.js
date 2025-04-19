import React, { useState, useEffect } from 'react';
import { View, Text, Alert, ActivityIndicator, Button } from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import axios from 'axios';
import StarRating from 'react-native-star-rating-widget';
import { useTranslation } from 'react-i18next';

const API_URL = 'http://192.168.1.8/Humanz2.0/Humanz_Pets/phpForReact/check_reviews.php';
const SUBMIT_URL = 'http://192.168.1.8/Humanz2.0/Humanz_Pets/phpForReact/submit_review.php';

const RatingsScreen = ({ fetchReviewCount }) => {
    const { t } = useTranslation();

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
                Alert.alert(t('error'), t('userNotLoggedIn'));
            } else {
                setUserId(storedUserId);
            }
        } catch (error) {
            console.error(t('error'), error);
        }
    };

    const fetchReviews = async (userId) => {
        try {
            const response = await axios.post(API_URL, { user_id: userId });
            setReviews(response.data.reviews);
        } catch (error) {
            console.error(t('error'), error);
        } finally {
            setLoading(false);
        }
    };

    const handleSubmitRating = async (reviewId) => {
        const rating = tempRatings[reviewId];
        if (rating == null) {
            Alert.alert(t('error'), t('pleaseSelectRating'));
            return;
        }

        try {
            await axios.post(SUBMIT_URL, { review_id: reviewId, rating: rating });
            Alert.alert(t('success'), t('ratingSubmitted'));
            fetchReviews(userId);
            fetchReviewCount(userId);
        } catch (error) {
            console.error(t('error'), error);
            Alert.alert(t('error'), t('submitRatingFailed'));
        }
    };

    if (loading) return <ActivityIndicator size="large" color="blue" />;

    return (
        <View style={{ flex: 1, padding: 20 }}>
            <Text style={{ fontSize: 26, fontWeight: 'bold' }}>{t('rateVeterinarians')}</Text>
            {reviews.length === 0 ? (
                <Text>{t('noPendingReviews')}</Text>
            ) : (
                reviews.map((review, index) => (
                    <View key={review.reviewId || index} style={{ marginBottom: 20 }}>
                        <Text style={{ fontSize: 24 }}>{t('veterinarian')}: {review.veterinarian_name}</Text>
                        <Text style={{ fontSize: 20 }}>{t('checkedTime')}: {review.reviewTime}</Text>
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
                            title={t('submitRating')}
                            onPress={() => handleSubmitRating(review.reviewId)}
                        />
                    </View>
                ))
            )}
        </View>
    );
};

export default RatingsScreen;
