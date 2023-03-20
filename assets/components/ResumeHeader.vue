<!-- Header с кнопками главная страница и добавить резюме -->
<template>
  <div class="container myHeader">
    <ul class="d-flex align-items-center flex-wrap nav justify-content-between">
      <li class="nav-link px-3 text-black">
        <h2 class="text-wrap fs-3">
          <router-link :to="{name: 'ResumeList'}" class="fs-3 btn btn-outline-light text-black">Главная страница</router-link>
        </h2>
      </li>
      <li class="nav-link px-3 text-black">
        <h2 class="text-wrap fs-3">
          <router-link :to="{name: 'ResumeDisplay'}" class="fs-3 btn btn-outline-light text-black">Новое резюме</router-link>
        </h2>
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  name: 'App',
  components: {}
}
</script>

<style scoped>
.myHeader {
  color: black;
  background: #DBEFF7;
  border: 2px solid #507ED8;
}
</style>