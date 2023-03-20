<!-- Один ключевой навык и его кнопка -->
<template>
  <li class="text-break mt-2">
    {{ title }}
    <button class="btn btn-outline-danger btn-sm" v-on:click="$emit('remove')">Удалить</button>
  </li>
</template>

<script>
export default {
  name: "KeySkillItem",
  props: ['title'],
  emits: ['remove'],
}
</script>

<style scoped>

</style>