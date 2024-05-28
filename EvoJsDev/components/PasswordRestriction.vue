<template>
    <div v-if="grantAccess">
        <slot></slot>
    </div>
    <div v-else class="block-access">
        <div class="input-group">
            <input type="password" v-model="password" class="form-control" placeholder="Enter your password to continue" />
            <button class="btn btn-success" @click.prevent="testAccess">
                <div class="animate__animated animate__infinite animate__headShake">
                    <FontAwesomeIcon icon="fa-solid fa-arrow-right-long"/>
                </div>
                <loading :active="processing" 
                    :can-cancel="true" 
                    :is-full-page=false></loading>
            </button>
        </div>
        
    </div>
</template>

<script setup>
    import {ref} from 'vue'
    import { library } from '@fortawesome/fontawesome-svg-core'
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
    import {fas} from '@fortawesome/free-solid-svg-icons'
    import 'animate.css'
    import {Request} from '@/helpers'
    import {useSessionStorage} from '@vueuse/core'
    import {useAlertStore} from '@/store/alert'

    library.add(fas)
    const grantAccess = ref(false)
    const password = ref(null)
    const processing = ref(false)
    const count = ref(useSessionStorage('badAttempts', 0))
    const alertStore = useAlertStore()

    const req = new Request()

    const testAccess = () => {
        processing.value = true
        req.post(req.root+"/api/test-access", {password: password.value}).then(r => {
            processing.value = false
            grantAccess.value = r.data
            if(r.data == false) {
                if(count.value >= 3) {
                    window.location = req.root+"/logout";
                }
                alertStore.add(`Failed: Remaining ${3 - count.value} attempts`, "danger")
                count.value++
            }
        }).catch(e => {
            processing.value = false
            alertStore.add(e.response.data, "danger")
        }) 
    }
</script>

<style lang="scss" scoped>
    .block-access {
        padding: 20px 10px;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 200px;
    }
    input::placeholder {
        font-style: italic;
        // color: #c6c6c6;
        font-size: 14px;
    }
</style>