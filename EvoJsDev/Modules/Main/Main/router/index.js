import {createRouter, createWebHashHistory} from 'vue-router'
import dashboard from '../views/dashboard.vue'
import myprofile from '../views/myprofile.vue'
import notifications from '../views/notifications.vue'
import changePassword from '../views/change-password.vue'
import ChangeProfilePicture from '../views/ChangeProfilePicture.vue'
import NotFound from '@/components/NotFound.vue'

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
        },
        {
            path: '/change-profile-picture', 
            name: 'ChangeProfilePicture', 
            component: ChangeProfilePicture, 
            meta: {title: "Change Profile Picture | Home"}
        },
        {
            path: '/notifications', 
            name: 'Notifications', 
            component: notifications, 
            meta: {title: "Notifications | Home"}
        },
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