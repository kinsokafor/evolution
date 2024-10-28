<template>
  <div class="k-input-group" :class="layout">
    <Field v-slot="{ field }" :name="name" v-bind="realAttrs">
      <label :for="name" class="label">{{ label }}</label>
      <v-select
        v-model="model"
        v-bind="{ ...realAttrs, ...field}"
        :reduce="(i) => i?.value ?? (i?.name ?? i)"
        label="name"
      ></v-select>
    </Field>
  </div>
</template>

<script setup>
import vSelect from "vue-select";
import { useField, Field } from "vee-validate";
import { ref, watchEffect, computed } from "vue";
import * as yup from "yup";
import "vue-select/dist/vue-select.css";
import _ from "lodash";

const props = defineProps({
  name: {
    type: String,
    default: "",
  },
  attrs: {
    type: Object,
    default: {},
  },
  layout: {
    type: String,
    default: "",
  },
  label: {
    type: String,
    default: "",
  },
  initialValues: {
    type: Object,
    default: {}
  },
});

const init = computed(() => {
  return props.initialValues[props.name] ?? []
})

const realAttrs = computed(() => {
  const attrs = props.attrs;
  if ("vlabel" in attrs) {
    attrs.label = attrs.vlabel;
    delete attrs.vlabel;
  }
  const options = Array.isArray(attrs?.options) ? attrs?.options : [];
  attrs.options = options.map((option) => {
    return typeof option == "string" ? { name: option, value: option } : option;
  });
  return attrs;
});

const { setErrors, setValue } = useField(props.name);

const validate = (value) => {
  const schema = props.attrs.rules ?? yup.mixed();
  schema
    .validate(value)
    .then((validValue) => {
      // console.log('Validation passed:', validValue);
    })
    .catch((validationError) => {
      setErrors(validationError.errors);
    });
};

const initiated = ref(false);

const model = ref(null);

watchEffect(() => {
  if (initiated.value) return;
  if (props.initialValues[props.name] != undefined) {
    model.value = props.initialValues[props.name];
    if(props.attrs?.multiple == undefined) {
      initiated.value = true;
    } else {
      if(!_.isEmpty(model.value)) {
        initiated.value = true;
      } else {
        setTimeout(() => {
          initiated.value = true
        }, 2000)
      }
    }
    
  }
});

watchEffect(() => {
  setValue(model.value);
  if (model.value != null) {
    validate(model.value);
  }
});
</script>

<style lang="scss">
:root {
  --vs-controls-size: 0.7;
  --vs-selected-bg: var(--color3);
  --vs-selected-color: var(--highlight1);
  --vs-selected-border-color: var(--color2);
  --vs-actions-padding: 0 .75rem 0 .75rem;
}
.form-control {
  .vs__dropdown-toggle {
    padding: 0 !important;
    border: none !important;
  }
  .vs__search,
  .vs__search:focus {
    margin: 0 !important;
    padding: 0 !important;
  }
  .vs__deselect {
    transition: 0.3s linear all;
  }
  .vs__selected {
    --vs-controls-color: var(--muted);
    margin: 0px 2px 2px !important;
    background-image: var(--color1);
    font-size: smaller;
  }
  .vs__selected:hover {
    --vs-controls-color: var(--highlight1);
  }
  .vs__clear {
    line-height: normal;
    display: flex;
    opacity: 0.3;
  }
}
.v-select.vs--multiple.form-control {
    height: auto;
}
.v-select.form-control {
  padding: .375rem 0rem .375rem .75rem;
}
</style>
