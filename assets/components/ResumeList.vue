<!-- Отображает все резюме из БД (минимальную информацию о резюме -->
<!-- Резюме поделены по колонкам в соответствии с их статусами -->
<template>
  <div class="container" v-if="!loading">
    <div class="row">
      <div class="col-lg-3 col-6 mt-3">
        <h3 class="text-primary">Новый ({{ newStatus.length}}):</h3>
      </div>
      <div class="col-lg-3 col-6 mt-3">
        <h3 class="text-info">Назначено собеседование ({{ interviewStatus.length }}):</h3>
      </div>
      <div class="col-lg-3 col-6 mt-3">
        <h3 class="text-success">Принят ({{ acceptedStatus.length }}):</h3>
      </div>
      <div class="col-lg-3 col-6 mt-3">
        <h3 class="text-danger">Отказ ({{ refusedStatus.length }}):</h3>
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
            <template v-if="Array.isArray(newStatus)">
              <li class="list-group-item" v-for="item in newStatus" :key="item.id">
                <resume-card
                    :fio="item.FIO"
                    :profession="item.Profession"
                    :bday="item.BirthDate"
                    :photo="item.Photo"
                    @click="clickCard(item)"/>
              </li>
            </template>
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
            <template v-if="Array.isArray(interviewStatus)">
            <li class="list-group-item" v-for="item in interviewStatus" :key="item.id">
              <resume-card
                  :fio="item.FIO"
                  :profession="item.Profession"
                  :bday="item.BirthDate"
                  :photo="item.Photo"
                  @click="clickCard(item)"
              ></resume-card>
            </li>
            </template>
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
            <template v-if="Array.isArray(acceptedStatus)">
            <li class="list-group-item" v-for="item in acceptedStatus" :key="item.id">
              <resume-card
                  :fio="item.FIO"
                  :profession="item.Profession"
                  :bday="item.BirthDate"
                  :photo="item.Photo"
                  @click="clickCard(item)"
              ></resume-card>
            </li>
            </template>
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
            <template v-if="Array.isArray(refusedStatus)">
            <li class="list-group-item" v-for="item in refusedStatus" :key="item.id">
              <resume-card
                  :fio="item.FIO"
                  :profession="item.Profession"
                  :bday="item.BirthDate"
                  :photo="item.Photo"
                  @click="clickCard(item)"
              ></resume-card>
            </li>
            </template>
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
    // изменение статуса при перемещении
    async onMove({ relatedContext, draggedContext }) {
      if (relatedContext?.element) {
        const relatedElement = relatedContext.element;
        const draggedElement = draggedContext.element;
        await Api.post(`/api/cv/${draggedElement.id}/status/update`, {Status: relatedElement.Status});
        await this.updateLists();
        return (!relatedElement || !relatedElement.fixed) && !draggedElement.fixed;
      } else {
        const draggedElement = draggedContext.element;
        await Api.post(`/api/cv/${draggedElement.id}/status/update`, {Status: relatedContext.list.element.Status});
        await this.updateLists();
        return !draggedElement.fixed;
      }
    },
    // обновление списков резюме
    async updateLists() {
      this.loading = true;
      let response = await Api.get('/api/cv');
      if (!response) alert('Ошибка');
      this.resumeCards = response;
      this.loading = false;
    },
    // открытие окна редактирования резюме
    clickCard(item) {
      item.fixed = !item.fixed;
      this.$router.push({ name: 'edit', params: { id: item.id } });
    },
  },
  computed: {
    // все резюме
    resumeList() {
      return this.resumeCards;
    },
    // новые резюме
    newStatus() {
      let list = this.resumeCards?.filter(item => item.Status === 'Новый');
      if (list.length) return list;
      else return {element: {Status: 'Новый'}};
    },
    // резюме, которым назначено собеседование
    interviewStatus() {
      let list = this.resumeCards?.filter(item => item.Status === 'Назначено собеседование');
      if (list.length) return list;
      else return {element: {Status: 'Назначено собеседование'}};
    },
    // принятые резюме
    acceptedStatus() {
      let list = this.resumeCards?.filter(item => item.Status === 'Принят');
      if (list.length) return list;
      else return {element: {Status: 'Принят'}};
    },
    // резюме, которым отказано
    refusedStatus() {
      let list = this.resumeCards?.filter(item => item.Status === 'Отказ');
      if (list.length) return list;
      else return {element: {Status: 'Отказ'}};
    },
    // для drag&drop
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
    // задержка drag&drop
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
