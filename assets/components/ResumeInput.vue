<!-- Input для текстовых данных резюме -->
<template>
  <div class="form-group">
    <label for="inp">{{ valueName }}</label>
    <input
        maxlength="80"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        :type=tip
        class="form-control" id="inp">
    <small class="text-muted form-text">{{ smallInfo }}</small>
  </div>
</template>

<script>
export default {
  name: "ResumeInput",
  props: ['valueName', 'modelValue', 'smallInfo', 'tip'],
  emits: ['update:modelValue'],
  data() {
    return {
      value: '',
    }
  },
}
</script>

<style scoped>

</style>