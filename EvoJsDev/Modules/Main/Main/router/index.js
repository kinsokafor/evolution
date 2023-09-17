import {createRouter, createWebHashHistory} from 'vue-router'
import dashboard from '../views/dashboard.vue'
import myprofile from '../views/myprofile.vue'
import changePassword from '../views/change-password.vue'

const router = createRouter({
    history: createWebHashHistory(),
    routes : [
        {
            path: '/', 
            name: 'Dashboard', 
            component: dashboard, 
            meta: {title: "Dashboard | Home"}
        },   
        {
            path: '/profile', 
            name: 'Profile', 
            component: myprofile, 
            meta: {title: "Profile | Home"}
        },
        {
            path: '/change-password', 
            name: 'Change Password', 
            component: changePassword, 
            meta: {title: "Change Password | Home"}
        }
    ]
});

router.beforeEach((to, from, next) => {
    document.title = `${to.meta.title}`;
    next();
});

export default router;