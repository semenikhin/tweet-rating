<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TwitterService
{
    /** @var SessionInterface */
    private $session;

    /** @var HttpClientInterface */
    private $httpClient;

    /** @var string */
    private $bearer;

    /** @var string */
    private $guestToken = null;

    private const TWEET_URL = 'https://api.twitter.com/2/timeline/conversation/%s.json?tweet_mode=extended&count=2055';
    private const AUTH_URL = 'https://api.twitter.com/1.1/guest/activate.json';


    /**
     * PostService constructor.
     * @param string $bearer
     * @param SessionInterface $session
     * @param HttpClientInterface $httpClient
     */
    public function __construct(
        string $bearer,
        SessionInterface $session,
        HttpClientInterface $httpClient
    ) {
        $this->bearer = $bearer;
        $this->session = $session;
        $this->httpClient = $httpClient;
    }

    private function isAuthorized()
    {
        if ($this->guestToken) {
            return true;
        }

        $guestToken = $this->session->get('guest_token');
        if ($guestToken) {
            $this->guestToken = $guestToken;
            return true;
        }

        return false;
    }

    private function deauth()
    {
        $this->guestToken = null;
        $this->session->remove('guest_token');
    }

    /**
     * Twitter authorization as guest
     *
     * @return bool
     */
    private function auth()
    {
        $headers = [
            "Referer: mobile.twitter.com",
            "Authorization: " . $this->bearer,
            "Content-Type: multipart/form-data"
        ];

        $options = [
            'headers' => $headers,
            'http_version' => 1.1
        ];

        $response = $this->httpClient->request('POST', self::AUTH_URL, $options);

        $response = json_decode($response->getContent(), true);

        if (isset($response['guest_token'])) {
            $this->guestToken = $response['guest_token'];
            $this->session->set('guest_token', $response['guest_token']);

            return true;
        }

        return false;
    }

    private function getConversation($tweetId)
    {
        if (!$this->isAuthorized()) {
            $this->auth();
        }

        $headers = [
            "authorization: " . $this->bearer,
            "x-guest-token: " . $this->guestToken
        ];

        $options = [
            'headers' => $headers,
            'http_version' => 1.1
        ];

        $url = sprintf(self::TWEET_URL, $tweetId);

        $httpResponse = $this->httpClient->request('GET', $url, $options);

        if ($httpResponse->getStatusCode() === 403) {
            $this->deauth();
            $httpResponse = $this->httpClient->request('GET', $url, $options);
        }

        $response = json_decode($httpResponse->getContent(false), true);

        return $response;
    }

    public function getTweets($tweetId, $sortBy)
    {
        $data = $this->getConversation($tweetId);

        $tweets = $data['globalObjects']['tweets'] ?? [];

        $rootTweet = null;
        $firstLevelTweets = [];
        $allReplies = [];

        foreach ($tweets as $tweet) {
            if ($tweet['in_reply_to_status_id'] === null) {
                $rootTweet = $tweet;
            } elseif ($tweet['in_reply_to_status_id'] == $tweetId) {
                $firstLevelTweets[] = $tweet;
                $allReplies[] = $tweet;
            } else {
                $allReplies[] = $tweet;
            }
        }

        if ($sortBy === 'likes') {
            $sortedReplies = $this->sortRepliesByLikes($allReplies);
        } elseif ($sortBy === 'replies') {
            $sortedReplies = $this->sortRepliesByLikes($allReplies);
        } elseif ($sortBy === 'retweets') {
            $sortedReplies = $this->sortRepliesByRetweets($allReplies);
        } else {
            $sortedReplies = $this->sortRepliesByTotal($allReplies);
        }

        return [
            'root' => $rootTweet,
            'first_level_tweets' => $firstLevelTweets,
            'all_replies' => $sortedReplies
        ];
    }

    private function sortRepliesByLikes($replies)
    {
        usort($replies, function ($a, $b) {
            return $b['favorite_count'] <=> $a['favorite_count'];
        });

        return $replies;
    }

    private function sortRepliesByRetweets($replies)
    {
        usort($replies, function ($a, $b) {
            return $b['retweet_count'] <=> $a['retweet_count'];
        });

        return $replies;
    }

    private function sortRepliesByTotal($replies)
    {
        usort($replies, function ($a, $b) {
            $sumA = $a['retweet_count'] + $a['favorite_count'];
            $sumB = $b['retweet_count'] + $b['favorite_count'];

            return $sumB <=> $sumA;
        });

        return $replies;
    }
}
