<template>
  <div class="k-input-group" :class="layout">
    <label :for="name" class="label">{{ label }}</label
    ><br />
    <date-range-picker
      v-bind="attrs"
      :date-range="dateRange"
      v-model="dateRange"
      @update="updateValues"
      :singleDatePicker="false"
      :auto-apply="true"
      :control-container-class="appData().inputFieldClass ?? ''"
    >
    </date-range-picker>
    <small>{{ attrs.hint ?? "" }}</small>
  </div>
  <ErrorMessage :name="`start_${name}`"></ErrorMessage>
  <!-- <ErrorMessage :name="`end_${name}`"></ErrorMessage> -->
  <!-- :opens="opens"
            :locale-data="{ firstDay: 1, format: 'dd-mm-yyyy HH:mm:ss' }"
            :minDate="minDate" :maxDate="maxDate"
            singleDatePicker="range"
            :timePicker="false"
            :timePicker24Hour="true"
            :showWeekNumbers="true"
            :showDropdowns="true"
            :autoApply="true"
            :linkedCalendars="linkedCalendars"
            :dateFormat="dateFormat" -->
</template>

<script setup>
import { useField } from "vee-validate";
import DateRangePicker from "vue3-daterange-picker";
import { ref, watch, watchEffect } from "vue";
import { appData } from "@/helpers";
import { ErrorMessage } from "vee-validate";

const dateRange = ref({
  startDate: null,
  endDate: null,
});

const props = defineProps({
  label: {
    type: String,
    default: "Date range",
  },
  name: {
    type: String,
  },
  attrs: {
    type: Object,
    default: {}, //used to configure filepond
  },
  column: {
    type: String,
  },
  layout: {
    type: String,
    default: "",
  },
  as: {
    type: String,
    default: "input",
  },
  initialValues: Object,
});

const start = useField(`start_${props.name}`, props.attrs.rules ?? "");

const end = useField(`end_${props.name}`, props.attrs.rules ?? "");

const initiated = ref(false);

watch(dateRange, (newVal) => {
  if (newVal.startDate) start.setValue(newVal.startDate);
  if (newVal.endDate) end.setValue(newVal.endDate);
});

watch(
  () => start.meta.dirty,
  (newVal) => {
    if (newVal === false) {
      //reset
      dateRange.value = {
        startDate: null,
        endDate: null,
      };
    }
  }
);

watchEffect(() => {
  if (initiated.value) return;
  if (props.initialValues[`start_${props.name}`] != undefined) {
    dateRange.value.startDate = props.initialValues[`start_${props.name}`];
    start.setValue(dateRange.value.startDate)
    initiated.value = true;
  }
  if (props.initialValues[`end_${props.name}`] != undefined) {
    dateRange.value.endDate = props.initialValues[`end_${props.name}`];
    end.setValue(dateRange.value.endDate)
    initiated.value = true;
  }
});

start.handleReset = function () {
  dateRange.value.startDate = null;
};
</script>

<style lang="scss" scoped></style>
