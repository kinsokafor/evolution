<template>
  <h2>{{ welcomeText }}</h2>
  <CreateForm
    :fields="fields"
    :initial-values="{}"
    @submit="handleSubmit"
    :processing="processing"
  >
    <template #submitButton>
      <div class="flex">
        <Button
          v-if="page !== 1"
          @click.prevent="back"
          class="btn-back animate__animated animate__fadeInLeft animate__delay-1s"
          :class="appData().loginButtonClass ?? ''"
          >Back</Button
        >
        <Button
          type="submit"
          class="btn-next animate__animated animate__fadeInLeft animate__delay-1s"
          :class="appData().loginButtonClass ?? ''"
        >
          <template v-if="page == 1">Next</template>
          <template v-else>Submit</template>
        </Button>
      </div>
    </template>
  </CreateForm>
</template>

<script setup>
import { onMounted, ref, computed } from "vue";
import { useAuthStore } from "@/store/auth";
import CreateForm from "@/components/form/CreateForm.vue";
import { appData, Request } from "@/helpers";
import Button from "@/components/Button.vue";
import { useAlertStore } from "@/store/alert";
import * as yup from "yup";
import "animate.css";

const authStore = useAuthStore();
const alertStore = useAlertStore();
const processing = ref(false);
const page = ref(1);
const index = ref("/");
const temp = ref({});
const welcomeText = ref("Welcome Back!");
const req = new Request();

const fields = computed(() => [
  {
    label: "",
    placeholder: `${appData().loginUsernameLabel ?? "Email"}`,
    name: "email",
    rules: yup.mixed().required(),
    condition: page.value == 1,
    class: `animate__animated animate__fadeInLeft ${appData().loginInputClass ?? ''}`,
  },
  {
    label: "",
    placeholder: "Password",
    name: "password",
    as: "password",
    rules: yup.mixed().required(),
    condition: page.value == 2,
    class: `animate__animated animate__fadeInLeft ${appData().loginInputClass ?? ''}`,
  },
]);

onMounted(() => {
  authStore.loginStatus();
});

const back = () => {
  page.value = 1;
  welcomeText.value = "Welcome Back!";
};

const handleSubmit = async (values) => {
  if (page.value == 1) {
    temp.value = { ...temp.value, ...values };
    page.value = 2;
    welcomeText.value = temp.value.email;
  } else {
    processing.value = true;
    temp.value = { ...temp.value, ...values };
    await req
      .post(req.root + "/api/login", temp.value)
      .then((response) => {
        index.value = response.data.index;
        processing.value = false;
        if (response.data.loginStatus) {
          window.location = response.data.index;
        } else {
          alertStore.add(response.data.msg, "danger");
        }
      })
      .catch((error) => {
        alertStore.add(error.message, "danger");
      });
  }
};
</script>

<style lang="scss" scoped>
.flex {
  display: flex;
}
.btn-back {
  width: calc(40% - 5px);
  margin-right: 5px;
}
.btn-next {
  width: 60%;
}
.flex .btn-block + .btn-block {
  margin-top: 0;
}
</style>
