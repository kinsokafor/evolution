<template>
  <Form @submit="handleSubmit" v-slot="{ values }" :class="appData().loginFormClass ?? ''">
    <div v-show="page == 1">
      <h2>Welcome Back!</h2>
    <Field name="email" type="email" placeholder="Email" :rules="isRequired" class="animate__animated animate__fadeInLeft" :class="appData().loginInputClass ?? ''" />
    <ErrorMessage name="email"></ErrorMessage><br>
    <Button @click.prevent="page = 2" class="btn-next animate__animated animate__fadeInLeft animate__delay-1s" :class="appData().loginButtonClass ?? ''" >Next</Button>
    </div>
    <div v-show="page == 2">
    <h2>{{values.email}}</h2>
    <Field name="password" placeholder="Password" type="password" class="animate__animated animate__fadeInLeft" :class="appData().loginInputClass ?? ''" />
    <ErrorMessage name="password"></ErrorMessage><br>
    <div class="flex">
      <Button @click.prevent="page = 1" class="btn-back animate__animated animate__fadeInLeft animate__delay-1s" :class="appData().loginButtonClass ?? ''">Back</Button>
      <Button type="submit" class="btn-submit btn-outline animate__animated animate__fadeInLeft animate__delay-1s" :processing="processing" :class="appData().loginButtonClass ?? ''">Submit</Button>
    </div>
    </div>
  </Form>
</template>

<script setup>
import { Form, Field, ErrorMessage } from 'vee-validate';
import { onMounted, ref, computed } from 'vue'
import { useAuthStore } from '@/store/auth'
import { isRequired, appData, nonce } from '@/helpers'
import Button from '@/components/Button.vue'
import { useAlertStore } from '@/store/alert'
import axios from 'axios'
import 'animate.css'

const authStore = useAuthStore()
const alertStore = useAlertStore()
const processing = ref(false)
const page = ref(1)
const referrer = document.referrer
const home = process.env.EVO_API_URL
const index = ref("/")

const redirectTo = computed(() => {
  if(referrer.toLowerCase().search(home.toLowerCase()) == -1 || referrer.toLowerCase().search('logout') != -1) {
    return index.value
  } else return referrer.replace(home, '/').replace('//', '/')
})

onMounted(() => {
  authStore.redirect = true
  authStore.loginStatus()
})

const handleSubmit = async (values) => {
  processing.value = true
  await axios.post(process.env.EVO_API_URL + '/api/login', JSON.stringify(values), {
    'Access-Control-Allow-Credentials':true,
      headers: {
          'Access-Control-Allow-Origin': '*', 
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${nonce()}` 
      }
  }).then(response => {
      index.value = response.data.index
      processing.value = false;
      if(response.data.loginStatus) {
        window.location = response.data.index;//redirectTo.value;
      } else {
          alertStore.add(response.data.msg, "danger");
      }
  }).catch(error => {
      alertStore.add(error.message, "danger");
  });
}
</script>

<style lang="scss" scoped>
  .flex{
    display: flex;
  }
  .btn-next, 
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