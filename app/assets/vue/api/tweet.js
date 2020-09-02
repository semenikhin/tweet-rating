import axios from 'axios';

export default {
    getTweet(tweetID, sortBy) {
        return axios.get('/api/tweet?tweetID=' + tweetID + '&sortBy=' + sortBy);
    },
}