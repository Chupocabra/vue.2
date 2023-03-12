<template>
  <resume-header/>
  <router-view></router-view>
</template>

<script>
import ResumeDisplay from "./components/ResumeDisplay";
import ResumeHeader from "./components/ResumeHeader";
import MainIndex from "./components/MainIndex";

export default {
  name: "App",
  components: {
    ResumeDisplay,
    ResumeHeader,
  }
}
</script>

<style>
.form-group {
  padding: 5px 20px;
}

input {
  box-sizing: border-box
}

img {
  max-height: 300px;
  max-width: 300px;
}
@media only screen and (max-width: 1024px) {
  img{
    max-height: 200px;
    max-width: 200px;
  }
}
@media only screen and (max-width: 480px) {
  img{
    max-height: 130px;
    max-width: 130px;
  }
}

input[type="number"] {
  -moz-appearance: textfield;
  -webkit-appearance: textfield;
  appearance: textfield;
}

input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
  display: none;
}

.row {
  --bs-gutter-x: 0 !important;
}
.fs-5 {
  padding: 10px 0px 2px;
}
.dopInformation {
  display: none;
}
</style>