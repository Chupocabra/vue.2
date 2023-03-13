<template>
  <div class="form-group">
    <label for="city">Город</label>
    <input id="city" class="form-control" type="text" v-model="inputValue"/>
    <p v-show="showHint" class="mt-2">Выберите из списка</p>
    <div class="form-group list-group-flush" v-show="showHint">
      <button @click="insertSearch" type="button" class="list-group-item list-group-item-action"
              v-for="hint in hints"
              v-bind:key="hint">
        {{ hint }}
      </button>
    </div>
  </div>
</template>

<script>
import jsonp from "jsonp";
import token from '../token.json';

export default {
  name: "CityInp",
  props: ['modelValue'],
  emits: ['update:modelValue'],
  data() {
    return {
      showHint: false,
      hints: [],
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
    }
  },
  watch: {
    inputValue: function () {
      jsonp(`https://api.vk.com/method/database.getCities?country_id=1&q=${this.inputValue}&count=4&v=5.131&access_token=${token.vkToken}`,
          null, (error, data) => {
            if (error) {
              console.log(error);
            } else {
              console.log(data);
              this.showHint = true;
              this.hints = [];
              data.response.items.forEach(element => {
                this.hints.push(element.title);
              });
            }
          });
    }
  },
  methods: {
    insertSearch(event) {
      this.showHint = false;
      this.$emit('update:modelValue', event.target.innerHTML);
    }
  }
}
</script>

<style scoped>

</style>