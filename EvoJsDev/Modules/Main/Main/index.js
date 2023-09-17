import {createApp} from 'vue'
import main from './main.vue'
import router from './router'
import { createPinia } from 'pinia'
import restricted from '@/plugins/restricted'

app = createApp(main)
        .use(createPinia())
        .use(restricted)
        .use(router)
        .mount("#app")