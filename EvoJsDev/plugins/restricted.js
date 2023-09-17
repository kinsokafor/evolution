import Restricted from '@/components/Restricted.vue'
export default {
    install: (app, options) => {
      app.component('Restricted', Restricted)
    }
}