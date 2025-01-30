<template>
  <!-- reference https://github.com/joshuaeasy/vue3-daterange-picker#readme -->
  <div class="k-input-group" :class="layout">
    <label :for="name" class="label">{{ label }}</label
    ><br />
    <date-range-picker
      v-bind="attrs"
      :date-range="dateRange"
      v-model="dateRange"
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
import { ref, watch, watchEffect, computed } from "vue";
import { appData, formatDate } from "@/helpers";
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
    default: {},
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

const dateFormat = computed(() => {
  if(props.attrs?.format === undefined) return "YYYY-MM-DD";
  return props.attrs?.format;
})

const start = useField(`start_${props.name}`, props.attrs.rules ?? "");

const end = useField(`end_${props.name}`, props.attrs.rules ?? "");

const initiated = ref(false);

watch(dateRange, (newVal) => {
  if (newVal.startDate) {
    const startVal = formatDate(new Date(newVal.startDate), dateFormat.value)
    start.setValue(startVal);
  }
  if (newVal.endDate) {
    const endVal = formatDate(new Date(newVal.endDate), dateFormat.value)
    end.setValue(endVal);
  }
});

watch(
  () => start.meta.dirty,
  (newVal) => {
    if (newVal === false && meta.value !== meta?.initialValue) {
      //reset
      if (props.initialValues[`start_${props.name}`] != undefined) {
        dateRange.value = {
          startDate: props.initialValues[`start_${props.name}`],
          endDate: props.initialValues[`end_${props.name}`]
        };
      } else {
        dateRange.value = {
          startDate: null,
          endDate: null,
        };
      }
      
    }
  }
);

watchEffect(() => {
  if (initiated.value) return;
  if (props.initialValues[`start_${props.name}`] != undefined) {
    dateRange.value = {
      startDate: props.initialValues[`start_${props.name}`],
      endDate: props.initialValues[`end_${props.name}`]
    };
    initiated.value = true;
  }
  // if (props.initialValues[`end_${props.name}`] != undefined) {
  //   alert(2)
  //   dateRange.value.endDate = props.initialValues[`end_${props.name}`];
  //   end.setValue(dateRange.value.endDate)
  //   initiated.value = true;
  // }
});

start.handleReset = function () {
  dateRange.value.startDate = null;
};
</script>

<style lang="scss" scoped></style>
