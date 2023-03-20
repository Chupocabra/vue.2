<!-- Компонент для указания образований -->
<!-- Связан с resume.education -->
<!-- Использует city для поиска образовательных учреждений -->
<!-- Образования после первого хранятся в массиве -->
<template>
  <select-val v-model="educationType" value-name="Образование" :options="educationOpt"/>
  <div class="form-group">
    <div v-bind:style="{ display: showMore }">
      <div style="display: flex; justify-content: space-between">
        <p>Расскажите подробнее</p>
      </div>
      <resume-input v-model="education.university" value-name="Учебное заведение"/>
      <span v-show="showHint" class="mt-2 form-group">Выберите из списка</span>
      <div class="form-group list-group-flush" v-show="showHint">
        <button @click="insertSearch" type="button" class="list-group-item list-group-item-action"
                v-for="hint in hints"
                v-bind:key="hint">
          {{ hint }}
        </button>
      </div>
      <resume-input v-model="education.faculty" value-name="Факультет"/>
      <resume-input v-model="education.specialization" value-name="Специализация"/>
      <resume-input v-on:keypress="isYear($event)" v-model="education.endYear" value-name="Год окончания"/>
    </div>
  </div>

  <div v-for="(secondEd, index) in education.secondEducation" v-bind:key="secondEd">
    <select-val v-if="haveSecondEd" v-model="secondEd.type" value-name="Образование" :options="educationOpt"/>
    <div v-if="haveSecondEd" class="form-group">
      <div v-if="secondEd.type != 'Среднее'">
        <div style="display: flex; justify-content: space-between">
          <p>Расскажите подробнее</p>
          <p v-if="haveSecondEd" class="d-md-flex justify-content-md-end">
            <span @click="deleteEd(index)" class="btn btn-outline-danger btn-sm pull-right">Удалить</span>
          </p>
        </div>
        <resume-input v-model="secondEd.university" value-name="Учебное заведение"/>
        <resume-input v-model="secondEd.faculty" value-name="Факультет"/>
        <resume-input v-model="secondEd.specialization" value-name="Специализация"/>
        <resume-input v-on:keypress="isYear($event)" v-model="secondEd.endYear" value-name="Год окончания"/>
      </div>
    </div>
  </div>

  <div v-bind:style="{ display: showMore }">
    <button @click="addNewEducation" class="btn btn-outline-success btn-sm">Указать еще одно место обучения</button>
  </div>
</template>

<script>
import SelectVal from "./SelectVal";
import ResumeInput from "./ResumeInput";
import jsonp from "jsonp";
import token from '../token.json';

export default {
  name: "EducInp",
  props: ['modelValue', 'city', 'educationFromDb'],
  components: {
    SelectVal,
    ResumeInput,
  },
  emits: ['update:modelValue'],
  data() {
    return {
      education: {
        type: '',
        university: '',
        faculty: '',
        specialization: '',
        endYear: '',
        secondEducation: [],
      },
      haveSecondEd: false,
      cityId: 0,
      showHint: false,
      hints: [],
      showMore: 'none',
      educationType: 'Среднее',
      educationOpt: [
        'Среднее',
        'Среднее специальное',
        'Неоконченное высшее',
        'Высшее',
      ],
    }
  },
  computed: {
    inputValue: {
      get() {
        return this.modelValue;
      },
      set(value) {
        this.$emit('update:modelValue', value);
      }
    },
  },
  watch: {
    education: {
      handler(val) {
        jsonp(`https://api.vk.com/method/database.getCities?country_id=1&q=${this.city}&count=1&v=5.131&access_token=${token.vkToken}`,
            null, (error, data) => {
              if (error) {
                console.log(error);
              } else {
                this.cityId = data.response.items[0].id;
                jsonp(`https://api.vk.com/method/database.getUniversities?country_id=1&city_id=${this.cityId}&q=${this.education.university}&count=4&v=5.131&access_token=${token.vkToken}`,
                    null, (error, data) => {
                      if (error) {
                        console.log(error);
                      } else {
                        console.log(data);
                        this.showHint = true;
                        this.hints = [];
                        data.response.items.forEach((element) => {
                          this.hints.push(element.title);
                        });
                      }
                    });
              }
            });
        this.education.type = this.educationType;
        this.education.university = val.university;
        this.education.faculty = val.faculty;
        this.education.specialization = val.specialization;
        this.education.endYear = val.endYear;
        this.$emit('update:modelValue', this.education);
      },
      deep: true
    },
    educationType: function () {
      this.education.type = this.educationType;
      if (this.educationType === "Среднее") {
        this.showMore = 'none';
        this.showMoreResume = 'none';
      } else {
        this.showMore = 'block';
        this.showMoreResume = 'flex';
      }
    }
  },
  methods: {
    update() {
      this.$emit('update:modelValue', this.education);
    },
    isYear(e) {
      let char = String.fromCharCode(e.keyCode);
      if (/^[0-9]/.test(char)) return true;
      else e.preventDefault();
    },
    insertSearch(event) {
      this.showHint = false;
      this.education.university = event.target.innerHTML;
    },
    addNewEducation() {
      this.haveSecondEd = true;
      this.education.secondEducation.push({
        city: '',
        type: 'Высшее',
        university: '',
        faculty: '',
        specialization: '',
        endYear: '',
      });
      this.$emit('update:modelValue', this.education);
    },
    deleteEd(index) {
      this.education.secondEducation.splice(index, 1);
    }
  },
  created() {
    if(this?.educationFromDb?.type){
      this.haveSecondEd = true;
      this.educationType = this.educationFromDb.type;
      this.education.type = this.educationFromDb.type;
      this.education.faculty = this.educationFromDb.faculty;
      this.education.university = this.educationFromDb.university;
      this.education.specialization = this.educationFromDb.specialization;
      this.education.endYear = this.educationFromDb.endYear;
      let array = [];
      this.educationFromDb.secondEducation.forEach((element) => {
        this.education.secondEducation = array.push(element);
      })
      this.education.secondEducation = array;
      console.log(this.education.secondEducation);
    }

  }
}
</script>

<style scoped>

</style>