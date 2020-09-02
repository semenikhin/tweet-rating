<template>
  <div class="my-1 p-3 bg-white rounded box-shadow">
    <p>
      <b>({{reply.favorite_count}})</b>
      {{reply.full_text}}
    </p>
    <div style="width: 100%">
      <img v-if="hasImg" v-bind:src="imgSrc" style="width: inherit">
    </div>
  </div>
</template>

<script>
export default {
  name: "replies-list",
  props: ["reply"],
  computed: {
    hasImg() {
      if (typeof this.reply.entities.media === "undefined") {
        return false;
      }

      if (this.reply.entities.media.length === 0) {
        return false;
      }

      if (typeof this.reply.entities.media[0].media_url_https === "undefined") {
        return false;
      }

      return true;
    },
    imgSrc() {
      return this.reply.entities.media[0].media_url_https;
    }
  }
};
</script>