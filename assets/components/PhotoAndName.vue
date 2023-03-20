<!-- Компонент для отображения фото, имени и города -->
<template>
  <div class="row ms-1 mt-1">
    <div class="col">
      <img src="https://i.pinimg.com/originals/31/ec/2c/31ec2ce212492e600b8de27f38846ed7.jpg"
           id="r_photo" class="img-responsive rounded-circle"> {{ photo }}
    </div>
    <div class="col">
      <div id="r_fio" class="h2 text-break">{{ fio }}</div>
      <div id="r_city" class="h3 text-break">{{ city }}</div>
    </div>
  </div>
</template>

<script>
export default {
  name: "PhotoAndName",
  props: ['photo', 'fio', 'city'],
}
</script>

<style scoped>

</style>