<template>
  <div class="container" v-if="!loading">
    <div class="row">
      <div class="col">
        <h3>Новый ({{ newResume.length }})</h3>
      </div>
      <div class="col">
        <h3>Назначено собеседование ({{ waitResume.length }})</h3>
      </div>
      <div class="col">
        <h3>Принят ({{ acceptedResume.length }})</h3>
      </div>
      <div class="col">
        <h3>Отказ ({{ refusedResume.length }})</h3>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-3 col-6 mt-3">
        <transition-group type="transition" name="flip-list">
          <li class="list-group-item" v-for="item in newResume" :key="item.id">
            <resume-card
                :fio="item.FIO"
                :profession="item.Profession"
                :bday="item.BirthDate"
                :photo="item.Photo"
                @click="clickCard(item)"
            ></resume-card>
          </li>
        </transition-group>
      </div>
      <div class="col-lg-3 col-6 mt-3">
        <transition-group type="transition" name="flip-list">
          <li class="list-group-item" v-for="item in waitResume" :key="item.id">
            <resume-card
                :fio="item.FIO"
                :profession="item.Profession"
                :bday="nitem.BirthDate"
                :photo="item.Photo"
                @click="clickCard(item)"
            ></resume-card>
          </li>
        </transition-group>
      </div>
      <div class="col-lg-3 col-6 mt-3">
        <transition-group type="transition" name="flip-list">
          <li class="list-group-item" v-for="item in acceptedResume" :key="item.id">
            <resume-card
                :fio="item.FIO"
                :profession="item.Profession"
                :bday="item.BirthDate"
                :photo="item.Photo"
                @click="clickCard(item)"
            ></resume-card>
          </li>
        </transition-group>
      </div>
      <div class="col-lg-3 col-6 mt-3">
        <transition-group type="transition" name="flip-list">
          <li class="list-group-item" v-for="item in refusedResume" :key="item.id">
            <resume-card
                :fio="item.FIO"
                :profession="item.Profession"
                :bday="item.BirthDate"
                :photo="item.Photo"
                @click="clickCard(item)"
            ></resume-card>
          </li>
        </transition-group>
      </div>
    </div>
  </div>
  <div class="container" v-else>
    <div>Загрузка..</div>
  </div>
</template>

<script>
import ResumeCard from "./ResumeCard";
import draggable from 'vuedraggable';
import axios from "axios";
import {Api} from "../api/Api";

export default {
  name: "ResumeList",
  components: {ResumeCard, draggable},
  data: () => ({
    loading: true,
    newResume: [],
    waitResume: [],
    acceptedResume: [],
    refusedResume: [],
  }),
  methods: {
    clickCard(item) {
      this.$router.push({ name: 'edit', params: { id: item.id } });
    },
    async onMove({relatedContext, draggedContext}) {
      // const relatedElement = relatedContext.element;
      // const draggedElement = draggedContext.element;
      // console.log(draggedElement.id + relatedElement.Status);
      // await Api.post(`/api/cv/${draggedElement.id}/status/update`, { status: relatedElement.Status });
      // return (!relatedElement || !relatedElement.fixed) && !draggedElement.fixed;
    }
  },
  computed: {
    drag() {
      return {
        animation: 0,
        group: 'description',
        disabled: !this.editable,
        ghostClass: 'ghost',
      }
    }
  },
  mounted() {
    axios
        .get("http://127.0.0.1:8000/api/cv")
        .then((response) => {
          console.log(response);
          response.data.forEach((item) =>
          {
            if (item.Status == 'Новый') {
              this.newResume.push(item);
            }
            if (item.Status == 'Назначено собеседование') {
              this.waitResume.push(item);
            }
            if (item.Status == 'Принят') {
              this.acceptedResume.push(item);
            }
            if (item.Status == 'Отказ') {
              this.refusedResume.push(item);
            }
          });
        })
    .catch((error) =>{
      console.log(error);
    })
    .finally(() => (this.loading = false));
  }
}
</script>

<style scoped>

</style>