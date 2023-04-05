<!-- Ключевые навыки, хранятся в массиве, связаны с resume.key_skills-->

<template>
  <div class="form-group">
    <div id="todo-list-example" class="demo">
      <form v-on:submit.prevent="addNewKeySkill">
        <label>Ключевые навыки</label>
        <input
            class="text-break ms-2 btn btn-sm keySkillInput"
            maxlength="40"
            v-model="newKeySkillText"
            id="new-todo"
        />
        <button class="btn btn-outline-success btn-sm" @click="updateKeySkills">Добавить</button>
      </form>
      <ul class="mt-2">
        <key-skill-item
            v-for="(keySkill, index) in keySkills"
            v-bind:key="keySkill.id"
            v-bind:title="keySkill.title"
            @click="updateKeySkills"
            v-on:remove="keySkills.splice(index, 1)">{{ keySkill.title }}
        </key-skill-item>
      </ul>
    </div>
  </div>
</template>

<script>

import KeySkillItem from "./KeySkillItem";
export default {
  name: "KeySkills",
  props: ['modelValue'],
  emits: ['update:list'],
  components: {KeySkillItem},
  data() {
    return {
      newKeySkillText: '',
      keySkills: [
        {
          id: 1,
          title: 'Обучаемость'
        },
      ],
      nextKeySkillId: 2,
    }
  },
  created() {
    this.keySkills = this.modelValue;
  },
  methods: {
    // добавить навык
    addNewKeySkill() {
      if (this.newKeySkillText!=''){
        this.keySkills.push({
          id: this.nextKeySkillId++,
          title: this.newKeySkillText,
        })
        this.newKeySkillText='';
      }
    },
    // обновить данные
    updateKeySkills() {
      this.$emit('update:modelValue', this.keySkills);
    }
  },
}
</script>

<style scoped>
.keySkillInput{
  text-align: left;
  border: 1px solid #ced4da;
}
</style>