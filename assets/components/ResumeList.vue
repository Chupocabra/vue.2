<template>
  <div class="container" v-if="!loading">
    <div class="row">
      <div class="col-lg-3 col-6 mt-3">
        <h3>Новый ({{ newStatus.length }}):</h3>
      </div>
      <div class="col-lg-3 col-6 mt-3">
        <h3>Назначено собеседование ({{ interviewStatus.length }}):</h3>
      </div>
      <div class="col-lg-3 col-6 mt-3">
        <h3>Принят ({{ acceptedStatus.length }}):</h3>
      </div>
      <div class="col-lg-3 col-6 mt-3">
        <h3>Отказ ({{ refusedStatus.length }}):</h3>
      </div>
    </div>
    <div class="row" v-if="resumeList">
      <div class="col-lg-3 col-6 mt-3">
        <draggable
            class="list-group"
            tag="ul"
            v-model="newStatus"
            v-bind="dragOptions"
            :move="onMove"
            @start="isDragging = true"
            @end="isDragging = false">
          <transition-group type="transition" name="flip-list">
            <li class="list-group-item" v-for="item in newStatus" :key="item.id">
              <resume-card
                  :fio="item.FIO"
                  :profession="item.Profession"
                  :bday="item.BirthDate"
                  :photo="item.Photo"
                  @click="clickCard(item)"
              ></resume-card>
            </li>
          </transition-group>
        </draggable>
      </div>
      <div class="col-lg-3 col-6 mt-3">
        <draggable
            class="list-group"
            tag="ul"
            v-model="interviewStatus"
            v-bind="dragOptions"
            :move="onMove"
            @start="isDragging = true"
            @end="isDragging = false">
          <transition-group type="transition" name="flip-list">
            <li class="list-group-item" v-for="item in interviewStatus" :key="item.id">
              <resume-card
                  :fio="item.FIO"
                  :profession="item.Profession"
                  :bday="item.BirthDate"
                  :photo="item.Photo"
                  @click="clickCard(item)"
              ></resume-card>
            </li>
          </transition-group>
        </draggable>
      </div>
      <div class="col-lg-3 col-6 mt-3">
        <draggable
            class="list-group"
            tag="ul"
            v-model="acceptedStatus"
            v-bind="dragOptions"
            :move="onMove"
            @start="isDragging = true"
            @end="isDragging = false">
          <transition-group type="transition" name="flip-list">
            <li class="list-group-item" v-for="item in acceptedStatus" :key="item.id">
              <resume-card
                  :fio="item.FIO"
                  :profession="item.Profession"
                  :bday="item.BirthDate"
                  :photo="item.Photo"
                  @click="clickCard(item)"
              ></resume-card>
            </li>
          </transition-group>
        </draggable>
      </div>
      <div class="col-lg-3 col-6 mt-3">
        <draggable
            class="list-group"
            tag="ul"
            v-model="refusedStatus"
            v-bind="dragOptions"
            :move="onMove"
            @start="isDragging = true"
            @end="isDragging = false">
          <transition-group type="transition" name="flip-list">
            <li class="list-group-item" v-for="item in refusedStatus" :key="item.id">
              <resume-card
                  :fio="item.FIO"
                  :profession="item.Profession"
                  :bday="item.BirthDate"
                  :photo="item.Photo"
                  @click="clickCard(item)"
              ></resume-card>
            </li>
          </transition-group>
        </draggable>
      </div>
    </div>
  </div>
  <div class="container" v-else>
      Загрузка....
  </div>
</template>

<script>
import ResumeCard from "./ResumeCard";
import { VueDraggableNext } from 'vue-draggable-next';
import {Api} from "../api/Api";

export default {
  name: 'ResumeList',
  components: { ResumeCard, draggable: VueDraggableNext },
  data: () => ({
    editable: true,
    isDragging: false,
    delayedDragging: false,
    loading: true,
    resumeCards: [],
  }),
  async created() {
    await this.updateLists();
  },
  methods: {
    async onMove({ relatedContext, draggedContext }) {
      const relatedElement = relatedContext.element;
      const draggedElement = draggedContext.element;
      console.log(relatedElement);
      await Api.post(`/api/cv/${draggedElement.id}/status/update`, {Status: relatedElement.Status});
      await this.updateLists();
      return (!relatedElement || !relatedElement.fixed) && !draggedElement.fixed;
    },
    async updateLists() {
      this.loading = true;
      let response = await Api.get('/api/cv');
      if (!response) alert('Ошибка');
      this.resumeCards = response;
      this.loading = false;
    },
    clickCard(item) {
      item.fixed = !item.fixed;
      this.$router.push({ name: 'edit', params: { id: item.id } });
    },
  },
  computed: {
    resumeList() {
      return this.resumeCards;
    },
    newStatus() {
      return this.resumeCards?.filter(item => item.Status === 'Новый');
    },
    interviewStatus() {
      return this.resumeCards?.filter(item => item.Status === 'Назначено собеседование');
    },
    acceptedStatus() {
      return this.resumeCards?.filter(item => item.Status === 'Принят');
    },
    refusedStatus() {
      return this.resumeCards?.filter(item => item.Status === 'Отказ');
    },
    dragOptions() {
      return {
        animation: 0,
        group: 'description',
        disabled: !this.editable,
        ghostClass: 'ghost',
      };
    },
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
};
</script>

<style scoped>
.flip-list-move {
  transition: transform 0.5s;
}
.no-move {
  transition: transform 0s;
}
.ghost {
  opacity: 0.5;
  background: #c8ebfb;
}
.list-group {
  min-height: 20px;
}
.list-group-item {
  cursor: move;
}
.list-group-item i {
  cursor: pointer;
}
</style>
