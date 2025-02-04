<template>
  <div class="k-input-group" :class="layout">
    <label :for="name" class="label">{{ label }}</label>
    <input
      ref="inputRef"
      type="text"
      class="flatpickr-input"
      :class="appData().inputFieldClass ?? ''"
    />
    <small>{{ attrs.hint ?? "" }}</small>
  </div>
  <ErrorMessage :name="`start_${name}`"></ErrorMessage>
</template>

<script setup>
import { useField } from "vee-validate";
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.min.css";
import { ref, watch, onMounted, onBeforeUnmount } from "vue";
import { appData, formatDateShort } from "@/helpers";
import { ErrorMessage } from "vee-validate";

const emit = defineEmits(["update:modelValue"]);

const props = defineProps({
  label: { type: String, default: "Date range" },
  name: { type: String },
  attrs: { type: Object, default: () => ({}) }, // Ensure it's a function to avoid shared state
  column: { type: String },
  layout: { type: String, default: "" },
  as: { type: String, default: "input" },
  initialValues: { type: Object, default: () => ({}) },
  modelValue: String,
});

const inputRef = ref(null);
let picker = null;

const e = useField(`${props.name}`, props.attrs.rules ?? "");
const start = useField(`start_${props.name}`, props.attrs.rules ?? "");
const end = useField(`end_${props.name}`, props.attrs.rules ?? "");

// Initialize Flatpickr
const initFlatpickr = (defaultDate = "today") => {
  if (picker) picker.destroy(); // Destroy previous instance if exists
  picker = flatpickr(inputRef.value, {
    ...props.attrs,
    defaultDate,
    onChange: (selectedDates, dateStr) => {
      emit("update:modelValue", dateStr);
      e.setValue(dateStr);
      if (props.attrs.mode == "range") {
        start.setValue(
          formatDateShort(new Date(selectedDates[0]), props.attrs.dateFormat)
        );
        end.setValue(
          formatDateShort(
            new Date(selectedDates[selectedDates.length - 1]),
            props.attrs.dateFormat
          )
        );
      }
    },
  });
};

// Run Flatpickr on component mount
onMounted(() => {
  let defaultDate = props.initialValues[props.name] || "today";
  if (props.attrs.mode == "range") {
    if (
      props.initialValues[`start_${props.name}`] !== undefined &&
      props.initialValues[`end_${props.name}`] !== undefined
    ) {
      defaultDate = `${props.initialValues[`start_${props.name}`]} to ${
        props.initialValues[`end_${props.name}`]
      }`;
    }
  }
  initFlatpickr(defaultDate);
});

// Watch for changes in options and reinitialize Flatpickr
watch(
  () => props.attrs,
  () => {
    initFlatpickr(props.modelValue); // Reinitialize with updated options
  },
  { deep: true }
);

// Ensure the date updates if `initialValues` change dynamically
watch(
  () => props.initialValues[props.name],
  (newVal) => {
    if (newVal !== undefined) {
      initFlatpickr(newVal);
    }
  }
);

// Cleanup on unmount
onBeforeUnmount(() => {
  if (picker) picker.destroy();
});
</script>

<style lang="scss" scoped></style>
