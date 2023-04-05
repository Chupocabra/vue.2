<!-- Компонент для вывода ообразований -->
<template>
  <div class="row row-cols-2  ms-1">
    <div class="fs-5 col">
      <div>Образование</div>
    </div>
    <div class="fs-5 col text-break">
      <div>{{ education.type }}</div>
    </div>
  </div>
  <div v-bind:style="{ display: showMoreResume }" class="row  row-cols-2 form-control"
       v-bind:key="education.id">
    <resume-out value-name="Учебное заведение" :value="education.university"/>
    <resume-out value-name="Факультет" :value="education.faculty"/>
    <resume-out value-name="Специализация" :value="education.specialization"/>
    <resume-out value-name="Год окончания" :value="education.endYear"/>
  </div>
  <div v-for="ed in education.secondEducation"
        v-bind:key="ed.id">
    <div class="row row-cols-2  ms-1">
      <div class="fs-5 col">
        <div>Дополнительное образование</div>
      </div>
      <div class="fs-5 col text-break">
        <div>{{ ed.type }}</div>
      </div>
    </div>
    <div v-if="ed.type != 'Среднее'" class="row row-cols-2 form-control"
         v-bind:key="ed.id" style="display: flex">
      <resume-out value-name="Учебное заведение" :value="ed.university"/>
      <resume-out value-name="Факультет" :value="ed.faculty"/>
      <resume-out value-name="Специализация" :value="ed.specialization"/>
      <resume-out value-name="Год окончания" :value="ed.endYear"/>
    </div>
  </div>
</template>

<script>
import ResumeOut from "./ResumeOutput";

export default {
  name: "EducOut",
  props: ['modelValue', 'education'],
  components: {
    ResumeOut,
  },
  computed: {
    // дополнительные поля образования
    showMoreResume: function () {
      if (this.education.type === 'Среднее') {
        return 'none';
      } else return 'flex';
    }
  }
}
</script>

<style scoped>

</style>