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
        <draggable
            class="list-group"
            tag="ul"
            v-model="newResume"
            v-bind="dragOptions"
            group="people"
            :move="onMove"
            @start="isDragging = true"
            @end="isDragging = false">
          <li class="list-group-item" v-for="item in newResume" :key="item.id">
            <resume-card
                :fio="item.FIO"
                :profession="item.Profession"
                :bday="item.BirthDate"
                :photo="item.Photo"
                @click="clickCard(item)"
            ></resume-card>
          </li>
        </draggable>
      </div>
      <div class="col-lg-3 col-6 mt-3">
        <draggable
            class="list-group"
            tag="ul"
            v-model="waitResume"
            v-bind="dragOptions"
            group="people"
            :move="onMove"
            @start="isDragging = true"
            @end="isDragging = false">
          <li class="list-group-item" v-for="item in waitResume" :key="item.id">
            <resume-card
                :fio="item.FIO"
                :profession="item.Profession"
                :bday="item.BirthDate"
                :photo="item.Photo"
                @click="clickCard(item)"
            ></resume-card>
          </li>
        </draggable>
      </div>
      <div class="col-lg-3 col-6 mt-3">
        <draggable
            class="list-group"
            tag="ul"
            v-model="acceptedResume"
            v-bind="dragOptions"
            group="people"
            :move="onMove"
            @start="isDragging = true"
            @end="isDragging = false">
          <li class="list-group-item" v-for="item in acceptedResume" :key="item.id">
            <resume-card
                :fio="item.FIO"
                :profession="item.Profession"
                :bday="item.BirthDate"
                :photo="item.Photo"
                @click="clickCard(item)"
            ></resume-card>
          </li>
        </draggable>
      </div>
      <div class="col-lg-3 col-6 mt-3">
        <draggable
            class="list-group"
            tag="ul"
            v-model="refusedResume"
            v-bind="dragOptions"
            group="people"
            :move="onMove"
            @start="isDragging = true"
            @end="isDragging = false">
          <li class="list-group-item" v-for="item in refusedResume" :key="item.id">
            <resume-card
                :fio="item.FIO"
                :profession="item.Profession"
                :bday="item.BirthDate"
                :photo="item.Photo"
                @click="clickCard(item)"
            ></resume-card>
          </li>
        </draggable>
      </div>
    </div>
  </div>
  <div class="container" v-else>
    <div>Загрузка..</div>
  </div>
</template>

<script>
import ResumeCard from "./ResumeCard";
import {VueDraggableNext} from 'vue-draggable-next';
import {Api} from "../api/Api";
//import axios from "axios";

export default {
  name: "ResumeList",
  components: {ResumeCard, draggable: VueDraggableNext},
  data: () => ({
    loading: true,
    newResume: [],
    waitResume: [],
    acceptedResume: [],
    refusedResume: [],

    editable: true,
    isDragging: false,
    delayedDragging: false,
  }),
  methods: {
    clickCard(item) {
      this.$router.push({name: 'edit', params: {id: item.id}});
    },
    async onMove({relatedContext, draggedContext}) {
      if (relatedContext?.element) {
        const relatedElement = relatedContext.element;
        const draggedElement = draggedContext.element;
        await Api.post(`/api/cv/${draggedElement.id}/status/update`, {status: relatedElement.Status});

        await this.showResumes();

        return (!relatedElement || !relatedElement.fixed) && !draggedElement.fixed;
      }
    },
    async showResumes() {
      this.loading = true;
      this.newResume = [];
      this.waitResume = [];
      this.acceptedResume = [];
      this.refusedResume = [];
      let response = await Api.get('/api/cv');
      if (!response) alert('Ошибка');
      response.forEach((item) => {
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
      this.loading = false;
    }
  },
  computed: {
    dragOptions() {
      return {
        animation: 0,
        group: 'description',
        disabled: !this.editable,
        ghostClass: 'ghost',
      }
    }
  },
  created() {
    this.showResumes();
  },
  watch: {
    isDragging(newValue) {
      if (newValue) {
        this.delayedDragging = true;
        return;
      }
      this.$nextTick(() => {
        this.delayedDragging = false;
      });
    },
  },
}
</script>

<style scoped>

</style>