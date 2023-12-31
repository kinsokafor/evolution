import {createRouter, createWebHashHistory} from 'vue-router'
import login from '../views/login.vue'
import recoverPassword from '../views/recover-password.vue'
import NotFound from '@/components/NotFound.vue'

const router = createRouter({
    history: createWebHashHistory(),
    routes : [
        {path: '/', name: 'Login', component: login, meta: {title: "Login page"}},
        {path: '/recover-password', name: 'Recover Password', component: recoverPassword, meta: {title: "Recover password"}},
        {path: '/:pathMatch(.*)*', component: login, meta: {title: "Login page"}},
        {
            path: '/:pathMatch(.*)*', 
            name: 'NotFound', 
            component: NotFound, 
            meta: {title: "404 Not Found"}
        }
    ]
});

router.beforeEach((to, from, next) => {
    document.title = `${to.meta.title}`;
    next();
});

export default router;