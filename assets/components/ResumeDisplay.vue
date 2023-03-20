<!-- Компонент отображения, сохранения и редактирования резюме -->
<!-- Данные, введенные пользователем сохраняет в бд-->
<template>
  <div class="container px-2 mb-2">
    <div class="row g-0 vh-100" v-if="!loading">
      <div class="col-md-6">
        <form id="resume_Form" v-on:submit.prevent>
          <h1 style="text-align: center">Ваше резюме</h1>
          <select-val v-model="resume.status" value-name="Статус" :options="options.statusOpt"/>
          <resume-input v-model="resume.prof" value-name="Профессия"/>
          <city-inp v-model="resume.city"/>
          <resume-input v-model="resume.photo" value-name="Фото"/>
          <resume-input v-model="resume.fio" value-name="Фамилия Имя Отчество"/>
          <resume-input v-model="resume.phone" value-name="Телефон" small-info="Только цифры!"/>
          <resume-input v-model="resume.email" value-name="Электронная почта" small-info="example@mail.ru"/>
          <resume-input tip="date" v-model="resume.birthday" value-name="Дата рождения"/>

          <educ-inp v-model="resume.education" :city="resume.city" :educationFromDb="resume.education"/>

          <select-val v-model="resume.salary" value-name="Желаемая зарплата" :options="options.salaryOpt"/>
          <key-skills v-model="resume.key_skills"/>
          <div class="form-group">
            <label for="description">О себе</label>
            <textarea maxlength="240" v-model="resume.description" class="form-control" id="description" rows="5"
                      style="resize: none"
                      placeholder="Напишите пару слов о себе"/>
          </div>
          <div class="text-center">
            <button @click="save" type="submit" class="btn btn-success text-center">Сохранить резюме</button>
          </div>
        </form>
      </div>
      <div class="col-md-6">
        <h1 style="text-align: center">Итоговое резюме</h1>
        <div class="border border-2">
          <div class="text-center fs-5" :class="statusClass">{{ resume.status }}</div>
          <photo-and-name :city="r_city" :fio="r_fio" :photo="r_photo"></photo-and-name>
          <div class="row row-cols-2  ms-1">
            <resume-out value-name="Профессия" :value="resume.prof"/>
            <resume-out value-name="Телефон" :value="r_phone" :error="errors.phoneError" :light="true"/>
            <resume-out value-name="Email" :value="r_email" :error="errors.emailError"/>
            <resume-out value-name="Дата рождения" :value="r_birthday" :error="errors.birthdayError" :light="true"/>
            <resume-out :light="true" value-name="Возраст" :error="errors.birthdayError" :value="age"/>
          </div>
          <educ-out :education="resume.education"/>
          <div class="row row-cols-2 ms-1">
            <resume-out :light="true" value-name="Желаемая зарплата" :value="resume.salary"/>
            <resume-out value-name="Ключевые навыки"/>
            <resume-out v-for="(keySkill) in resume.key_skills" v-bind:key="keySkill.id" :value="keySkill.title"/>
          </div>
          <div class="fs-5 col bg-light  ms-1" style="padding-bottom: 0px">
            <div>Описание</div>
            <textarea v-model="resume.description" class="form-control" disabled id="r_description" rows="5"
                      style="resize: none"/>
          </div>
        </div>
      </div>
    </div>
    <div v-else>Загрузка...</div>
  </div>
</template>

<script>
import ResumeOut from "./ResumeOutput";
import ResumeInput from "./ResumeInput";
import PhotoAndName from "./PhotoAndName";
import SelectVal from "./SelectVal";
import KeySkills from "./KeySkills";
import EducInp from "./EducInput";
import EducOut from "./EducOutput";
import CityInp from "./CityInput";
import {Api} from "../api/Api";

export default {
  name: "ResumeDisplay",
  components: {
    CityInp,
    EducOut,
    EducInp,
    KeySkills,
    SelectVal,
    ResumeOut,
    ResumeInput,
    PhotoAndName,
  },
  created() {
    if (this.id) {
      this.loadResume(this.id);
    }
  },
  props: {
    id: {
      type: String,
      required: false,
      default: null,
    },
  },
  data() {
    return {
      resume: {
        status: 'Новый',
        prof: '',
        city: '',
        photo: '',
        fio: '',
        phone: '',
        email: '',
        birthday: '',
        education: {
          type: 'Среднее',
          university: '',
          faculty: '',
          specialization: '',
          endYear: '',
          secondEducation: [],
        },
        salary: 'до 50 тыс',
        key_skills: [
          {
            id: 1,
            title: 'Обучаемость'
          },
        ],
        description: '',
      },
      errors: {
        birthdayError: false,
        emailError: false,
        phoneError: false,
      },
      options: {
        salaryOpt: [
          'до 50 тыс',
          '50-100 тыс',
          '100-200 тыс',
          'от 200 тыс и выше',
        ],
        statusOpt: [
          'Новый',
          'Назначено собеседование',
          'Принят',
          'Отказ',
        ],
      },
      loading: false,
    }
  },
  computed: {
    r_city: function () {
      if (this.resume.city === '') return "г. Город";
      return "г. " + this.resume.city;
    },
    r_fio: function () {
      if (this.resume.fio === '') return "Фамилия Имя Отчество";
      return this.resume.fio;
    },
    r_photo: function () {
      return this.validPicture(this.resume.photo);
    },
    r_phone: function () {
      if (this.resume.phone === '') return '';
      else if (this.validPhone(this.resume.phone)) {
        this.errors.phoneError = false;
        return this.resume.phone;
      } else {
        this.errors.phoneError = true;
        return "Телефон может состоять только из цифр и иметь длину от 6 до 10 символов.";
      }
    },
    r_email: function () {
      if (this.resume.email === '') return '';
      else if (this.validEmail(this.resume.email)) {
        this.errors.emailError = false;
        return this.resume.email;
      } else {
        this.errors.emailError = true;
        return "Формат почты неверный";
      }
    },
    r_birthday: function () {
      if (this.resume.birthday === '') return '';
      let db = this.resume.birthday.split('-');
      if (!this.resume.birthday || (this.resume.age > 120) || (this.resume.age < 0)) {
        this.errors.birthdayError = true;
        return "Какая?";
      } else if(db[2]!== undefined) {
        this.errors.birthdayError = false;
        return db[2] + '.' + db[1] + '.' + db[0];
      } else {
        this.errors.birthdayError = true;
        return "Какая?";
      }
    },
    age: function () {
      if (this.resume.birthday === '') return '';
      let db = this.resume.birthday.split('-');
      let now = new Date();
      let today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
      let dob = new Date(db[0], db[1] - 1, db[2]); //Дата рождения
      dob.setFullYear(db[0].padStart(4, '0'));
      let dobnow = new Date(today.getFullYear(), dob.getMonth(), dob.getDate());
      let age = today.getFullYear() - dob.getFullYear();
      //Если ДР в этом году ещё предстоит, то вычитаем из age один год
      if (today < dobnow) {
        age = age - 1;
      }
      if (!this.resume.birthday || (age > 120) || (age < 0)) {
        return "?";
      } else {
        return this.age_postscript(age);
      }
    },
    statusClass: function () {
      switch (this.resume.status) {
        case this.options.statusOpt[0]:
          return 'text-primary';
        case this.options.statusOpt[1]:
          return 'text-info';
        case this.options.statusOpt[2]:
          return 'text-success';
        case this.options.statusOpt[3]:
          return 'text-danger';
        default:
          return '';
      }
    },
    resumeEdit: function () {
      return !!this.id;
    }
  },
  watch: {
    education() {
      if (this.education === "Среднее") {
        this.showMore = 'none';
        this.showMoreResume = 'none';
      } else {
        this.showMore = 'block';
        this.showMoreResume = 'flex';
      }
    },
  },
  methods: {
    validPhone: function (phone) {
      let reg = /^[0-9]{6,10}$/;
      return reg.test(phone);
    },
    validPicture: function (str) {
      console.log(str);
      let img = new Image();
      img.src = str;
      img.onload = function () {
        document.getElementById("r_photo").src = str;
        return str;
      };
      img.onerror = function () {
        document.getElementById("r_photo").src = "https://i.pinimg.com/originals/31/ec/2c/31ec2ce212492e600b8de27f38846ed7.jpg";
        return "https://i.pinimg.com/originals/31/ec/2c/31ec2ce212492e600b8de27f38846ed7.jpg";
      };
    },
    validEmail: function (email) {
      let regex = /^([a-zA-Z0-9_.+-])+@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
      return regex.test(email);
    },
    age_postscript: function (age) {
      if (age % 100 > 9 && age % 100 < 20) {
        return age + " лет";
      }
      if (age % 10 < 5 && age % 10 > 0) {
        if (age % 10 == 1) return age + " год";
        else return age + " года";
      } else return age + " лет";
    },
    async save() {
      this.errors.emailError = false; this.errors.birthdayError = false; this.errors.phoneError = false;
      if (!(this.errors.emailError || this.errors.birthdayError || this.errors.phoneError)) {
        let data = {
          status: this.resume.status,
          profession: this.resume.prof,
          city: this.resume.city,
          photo: this.resume.photo,
          fio: this.resume.fio,
          phone: this.resume.phone,
          email: this.resume.email,
          birth_date: this.resume.birthday,
          salary: this.resume.salary,
          key_skills: JSON.stringify(this.resume.key_skills),
          about: this.resume.description,
          education: {
            type: this.resume.education.type,
            university: this.resume.education.university,
            faculty: this.resume.education.faculty,
            specialization: this.resume.education.specialization,
            endYear: this.resume.education.endYear,
            secondEducation: this.resume.education.secondEducation,
          },
        };
        console.log(data);
        if (!this.resumeEdit) {
          let result = await Api.post('/api/cv/add', data);
          if (result) alert('Резюме добавлено');
          else alert('Произошла ошибка');
        } else {
          let result = await Api.post(`/api/cv/${this.id}/edit`, data);
          if (result) alert('Резюме изменено');
          else alert('Произошла ошибка');
        }
      } else alert('В резюме ошибка!');
      this.$router.push({name: 'ResumeList'});
    },
    async loadResume(id) {
      this.loading = true;
      let res = await Api.get(`/api/cv/${id}`);
      let result = res.result;
      if (result) {
        this.resume.prof = result.Profession;
        this.resume.status = result.Status;
        this.resume.city = result.City;
        this.resume.photo = result.Photo;
        this.resume.fio = result.FIO;
        this.resume.phone = result.Phone;
        this.resume.email = result.Email;
        this.resume.birthday = result.BirthDate;
        let array = [];
        res.education.forEach((element) => {
          array.push(element);
        })
        if(array.length != 0){
          this.resume.education = array[0];
          array.shift();
          this.resume.education.secondEducation = array;
        }
        this.resume.salary = result.Salary;
        this.resume.key_skills = JSON.parse(result.KeySkills);
        this.resume.description = result.About;
      }
      else {
        alert('Произошла ошибка ' + res.error);
        this.$router.push({name: 'ResumeList'});
      }
      this.loading = false;
    }
  },
}
</script>

<style>

</style>