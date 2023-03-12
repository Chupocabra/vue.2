<template>
  <div class="container px-2 mb-2">
    <div class="row g-0 vh-100">
      <div class="col-md-6">
        <form id="resume_Form" v-on:submit.prevent>
          <h1 style="text-align: center">Ваше резюме</h1>
          <select-val v-model="status" value-name="Статус" :options="options.statusOpt"/>
          <resume-input v-model="prof" value-name="Профессия"/>
          <city-inp v-model="city"/>
          <resume-input v-model="photo" value-name="Фото"/>
          <resume-input v-model="fio" value-name="Фамилия Имя Отчество"/>
          <resume-input v-model="phone" value-name="Телефон" small-info="Только цифры!"/>
          <resume-input v-model="email" value-name="Электронная почта" small-info="example@mail.ru"/>
          <resume-input tip="date" v-model="birthday" value-name="Дата рождения"/>
          <educ-inp v-model="education" :city="city"/>
          <select-val v-model="salary" value-name="Желаемая зарплата" :options="options.salaryOpt"/>
          <key-skills v-model="key_skills"/>
          <div class="form-group">
            <label for="description">О себе</label>
            <textarea maxlength="240" v-model="description" class="form-control" id="description" rows="5"
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
          <div class="text-center fs-5" :class="statusClass">{{ status }}</div>
          <photo-and-name :city="r_city" :fio="r_fio" :photo="r_photo"></photo-and-name>
          <div class="row row-cols-2  ms-1">
            <resume-out value-name="Профессия" :value="prof"/>
            <resume-out value-name="Телефон" :value="r_phone" :error="errors.phoneError" :light="true"/>
            <resume-out value-name="Email" :value="r_email" :error="errors.emailError"/>
            <resume-out value-name="Дата рождения" :value="r_birthday" :error="errors.birthdayError" :light="true"/>
            <resume-out :light="true" value-name="Возраст" :error="errors.birthdayError" :value="age"/>
          </div>
          <educ-out :education="education"/>
          <div class="row row-cols-2 ms-1">
            <resume-out :light="true" value-name="Желаемая зарплата" :value="salary"/>
            <resume-out value-name="Ключевые навыки"/>
            <resume-out v-for="(keySkill) in key_skills" v-bind:key="keySkill.id" :value="keySkill.title"/>
          </div>
          <div class="fs-5 col bg-light  ms-1" style="padding-bottom: 0px">
            <div>Описание</div>
            <textarea v-model="description" class="form-control" disabled id="r_description" rows="5" style="resize: none"/>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import ResumeOut from "./ResumeOut";
import ResumeInput from "./ResumeInput";
import PhotoAndName from "./PhotoAndName";
import SelectVal from "./SelectVal";
import KeySkills from "./KeySkills";
import EducInp from "./EducInp";
import EducOut from "./EducOut";
import CityInp from "./CityInp";
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
  data() {
    return {
      status: 'Новый',
      prof: '',
      city: '',
      photo: '',
      fio: '',
      phone: '',
      email: '',
      birthday: '',
      education: {
        type: 'Среднее'
      },
      salary: 'до 50 тыс',
      key_skills: [
        {
          title: 'Обучаемость'
        },
      ],
      description: '',
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
    }
  },
  computed: {
    r_city: function () {
      if (this.city === '') return "г. Город";
      return "г. " + this.city;
    },
    r_fio: function () {
      if (this.fio === '') return "Фамилия Имя Отчество";
      return this.fio;
    },
    r_photo: function () {
      return this.validPicture(this.photo);
    },
    r_phone: function () {
      if (this.phone === '') return '';
      else if (this.validPhone(this.phone)) {
        return this.phone;
      } else {
        return "Телефон может состоять только из цифр и иметь длину от 6 до 10 символов.";
      }
    },
    r_email: function () {
      if (this.email === '') return '';
      else if (this.validEmail(this.email)) {
        return this.email;
      } else {
        return "Формат почты неверный";
      }
    },
    r_birthday: function () {
      if (this.birthday === '') return '';
      let db = this.birthday.split('-');
      if (!this.birthday || (this.age > 120) || (this.age < 0)) {
        return "Какая?";
      } else {
        return db[2] + '.' + db[1] + '.' + db[0];
      }
    },
    age: function () {
      if (this.birthday === '') return '';
      let db = this.birthday.split('-');
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
      if (!this.birthday || (age > 120) || (age < 0)) {
        return "?";
      } else {
        return this.age_postscript(age);
      }
    },
    statusClass: function() {
      switch (this.status){
        case this.options.statusOpt[0]: return 'text-primary';
        case this.options.statusOpt[1]: return 'text-info';
        case this.options.statusOpt[2]: return 'text-success';
        case this.options.statusOpt[3]: return 'text-danger';
        default: return '';
      }
    }
  },
  watch: {
    phone() {
      this.errors.phoneError = !this.validPhone(this.phone);
    },
    email() {
      this.errors.emailError = !this.validEmail(this.email);
    },
    birthday() {
      this.errors.birthdayError = this.age === '?';
    },
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
    async save(){
      if(!(this.errors.emailError || this.errors.birthdayError || this.errors.phoneError)){
        //отредактировано или добавлено
        let data = {
          status: this.status,
          profession: this.prof,
          city: this.city,
          photo: this.photo,
          fio: this.fio,
          phone: this.phone,
          email: this.email,
          birth_date: this.birthday,
          salary: this.salary,
          key_skills: JSON.stringify(this.key_skills),
          about: this.description,
          education: {
            type: this.education.type,
            university: this.education.university,
            faculty: this.education.faculty,
            specialization: this.education.specialization,
            end_year: this.education.endYear,
          },
        };
        console.log(data);
        let result = await Api.post('/api/cv/add', data);
        if (result) alert('Резюме добавлено');
        else alert('Произошла ошибка');
      }
      else alert('В резюме ошибка!');
    }
  },
}
</script>

<style>

</style>