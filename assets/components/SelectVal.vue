<template>
  <div class="form-group">
    <label for="sel">{{ valueName }}</label>
    <select
        v-model="sel"
        @input="$emit('update:selected', $event.target.value)"
        id="sel" class="form-control">
        <option v-for="option in options" v-bind:key="option">{{ option }}</option>
    </select>
  </div>

</template>

<script>
export default {
  name: "SelectVal",
  props: ['valueName', 'options', 'modelValue'],
  emits: ['update:selected'],
  data () {
    return {
      sel: this.modelValue,
    }
  },
  watch: {
    sel () {
      this.$emit('update:modelValue', this.sel);
    },
  }
}
</script>

<style scoped>

</style>