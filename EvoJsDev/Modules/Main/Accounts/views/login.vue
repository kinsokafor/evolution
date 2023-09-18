<template>
  <Form @submit="handleSubmit" v-slot="{ values }" :class="appData().loginFormClass ?? ''">
    <div v-show="loginStore.ismail">
      <h2>Welcome Back!</h2>
    <Field name="email" type="email" placeholder="Email" :rules="isRequired" class="animate__animated animate__fadeInLeft" :class="appData().loginInputClass ?? ''" />
    <ErrorMessage name="email"></ErrorMessage><br>
    <Button @click.prevent="loginStore.switchTab" class="animate__animated animate__fadeInLeft animate__delay-1s" :class="appData().loginButtonClass ?? ''" >Next</Button>
    </div>
    <div v-show="!loginStore.ismail">
    <h2>{{values.email}}</h2>
    <Field name="password" placeholder="Password" type="password" class="animate__animated animate__fadeInLeft" :class="appData().loginInputClass ?? ''" />
    <ErrorMessage name="password"></ErrorMessage><br>
    <div class="flex">
      <Button @click.prevent="loginStore.switchTab" class="btn-back animate__animated animate__fadeInLeft animate__delay-1s" :class="appData().loginButtonClass ?? ''">Back</Button>
      <Button type="submit" class="btn-submit btn-outline animate__animated animate__fadeInLeft animate__delay-2s" :processing="loginStore.submitting" :class="appData().loginButtonClass ?? ''">Submit</Button>
    </div>
    </div>
  </Form>
</template>

<script setup>
import { Form, Field, ErrorMessage } from 'vee-validate';
import { onMounted } from 'vue'
import { useLoginStore } from '@/Modules/Main/store/login'
import { useAuthStore } from '@/store/auth'
import { isRequired, appData } from '@/helpers'
import Button from '@/components/Button.vue'
import 'animate.css'

const loginStore = useLoginStore();
const authStore = useAuthStore();

onMounted(() => {
  authStore.redirect = true
  authStore.loginStatus()
})

const handleSubmit = (values) => {
  loginStore.doLogin(values)
}
</script>

<style lang="scss" scoped>
  .flex{
    display: flex;
  }
  .btn-back {
    width: calc(40% - 5px);
    margin-right: 5px;
  }
  .btn-submit {
    width: 60%;
  }
  .flex .btn-block + .btn-block {
    margin-top: 0;
  }
</style>