<template>
  <div v-if="hasReplies">
    <div class="text-center my-3">
      <button
        @click="getTweet('likes', tweetID)"
        type="button" class="btn mr-3">By Likes</button>
      <button
        @click="getTweet('replies', tweetID)"
        type="button" class="btn btn-secondary mr-3">By Replies</button>
      <button 
        @click="getTweet('retweets', tweetID)"
        type="button" class="btn btn-light">By Retweets</button>
    </div>

    <div v-for="reply in replies" v-bind:key="reply.id" class="row col">
      <replies-list :reply="reply"></replies-list>
    </div>
  </div>
</template>

<script>
import RepliesList from "./RepliesList";

export default {
  name: "replies",
  props: ["hasReplies", "replies", "tweetID"],
  components: {
    RepliesList
  },
  methods: {
    getTweet(sortBy, tweetID) {
      this.$store
        .dispatch("tweet/getTweet", {tweetID: tweetID, sortBy: sortBy});
    }
  }

};
</script>