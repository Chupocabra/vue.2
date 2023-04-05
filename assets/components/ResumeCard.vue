<!-- Карточка, показывающая минимальную информацию о резюме -->
<template>
<div class="card">
  <img :src="photo" class="card-img-top"/>
  <div>
    <div class="card-header">{{ fio }}</div>
    <div>Профессия: {{ profession }}</div>
    <div>Возраст: {{ age }}</div>
  </div>
</div>
</template>

<script>
export default {
  name: "ResumeCard",
  props: ['photo', 'fio', 'profession', 'bday'],
  computed: {
    // считает возраст
    age(){
      return (new Date().getFullYear() - new Date(this.bday).getFullYear());
    }
  }
}
</script>

<style scoped>

</style>