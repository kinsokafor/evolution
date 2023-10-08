import {createApp} from 'vue'
import admin from './admin.vue'
import router from './router'
import { createPinia } from 'pinia'
import restricted from '@/plugins/restricted'
import VueFroala from 'vue-froala-wysiwyg';

app = createApp(admin)
        .use(createPinia())
        .use(restricted)
        .use(VueFroala)
        .use(router)
        .mount("#app")