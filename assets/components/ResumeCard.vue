<template>
<div class="card">
  <img :src="photo" class="card-img-top"/>
  <div>
    <div class="card-header">{{ fio }}</div>
    <div>Профессия: {{ profession }}</div>
    <div>Дата рождения: {{ age }}</div>
  </div>
</div>
</template>

<script>
export default {
  name: "ResumeCard",
  props: ['photo', 'fio', 'profession', 'age'],
}
</script>

<style scoped>

</style>