<template>
  <div class="my-3 p-3 bg-white rounded box-shadow">{{tweet.full_text}}</div>
</template>

<script>
export default {
  name: "root-tweet",
  props: ["tweet"]
};
</script>