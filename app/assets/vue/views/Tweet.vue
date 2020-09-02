<template>
  <div class="col-md-8 mt-5" style="margin: auto">
    <form>
      <div class="row">
        <div class="col-md-10">
          <input
            v-model="tweetID"
            placeholder="Put here link or tweet ID"
            type="text"
            class="form-control"
          >
        </div>
        <div>
          <button
            @click="getTweet()"
            :disabled="tweetID.length === 0 || isLoading"
            type="button"
            class="btn btn-primary"
          >Get</button>
        </div>
      </div>
    </form>

    <div v-if="isLoading" class="row col">
      <p>Loading...</p>
    </div>

    <div v-else-if="hasError" class="row col">
      <error-message :error="error"></error-message>
    </div>

    <div v-else-if="hasRootTweet" class="row col">
      <root-tweet :tweet="rootTweet"></root-tweet>
    </div>

    <replies :hasReplies="hasReplies" :replies="replies" :tweetID="this.$data.tweetID"></replies>
  </div>
</template>

<script>
import RootTweet from "../components/RootTweet";
import Replies from "../components/Replies";
import ErrorMessage from "../components/ErrorMessage";

export default {
  name: "tweet",
  components: {
    RootTweet,
    Replies,
    ErrorMessage
  },
  data() {
    return {
      tweetID: ""
    };
  },
  computed: {
    isLoading() {
      return this.$store.getters["tweet/isLoading"];
    },
    hasError() {
      return this.$store.getters["tweet/hasError"];
    },
    error() {
      return this.$store.getters["tweet/error"];
    },
    hasRootTweet() {
      return this.$store.getters["tweet/hasRootTweet"];
    },
    rootTweet() {
      return this.$store.getters["tweet/rootTweet"];
    },
    hasReplies() {
      return this.$store.getters["tweet/hasReplies"];
    },
    replies() {
      return this.$store.getters["tweet/replies"];
    }
  },
  methods: {
    getTweet() {
      this.$store
        .dispatch("tweet/getTweet", {tweetID: this.$data.tweetID, sortBy: 'likes'});
    }
  }
};
</script>