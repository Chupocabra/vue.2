<template>
  <div class="fs-5 col"
       v-bind:class="{'bg-light': light, 'text-danger': error}">
    <div> {{ valueName }}</div>
  </div>
  <div class="fs-5 col text-break"
       v-bind:class="{'bg-light': light, 'text-danger': error}">
    <div>{{ value }}</div>
  </div>
</template>

<script>
export default {
  name: "ResumeOut",
  props: ['valueName', 'value', 'light', 'error'],
}
</script>

<style>

</style>