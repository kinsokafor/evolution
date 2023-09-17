import {createRouter, createWebHashHistory} from 'vue-router'
import dashboard from '../views/dashboard.vue'
import users from '../views/users.vue'
import editProfile from '../views/editProfile.vue'
import profile from '../views/editProfile.vue'
import changeUserPassword from '../views/change-user-password.vue'
import options from '../views/options.vue'

const router = createRouter({
    history: createWebHashHistory(),
    routes : [
        {
            path: '/', 
            name: 'Dashboard', 
            component: dashboard,
            meta: {title: "Dashboard | Admin"}
        },
        {
            path: '/change-user-password/:id', 
            name: 'Change User Password', 
            component: changeUserPassword,
            meta: {title: "Change Password | Admin"}
        },
        {
            path: '/edit-profile/:id', 
            name: 'Edit Profile', 
            component: editProfile,
            meta: {title: "Edit Profile | Admin"}
        },
        {
            path: '/profile/:id', 
            name: 'Profile', 
            component: profile,
            meta: {title: "Profile | Admin"}
        },
        {
            path: '/users', 
            name: 'Users', 
            component: users,
            meta: {title: "Users | Admin"}
        },
        {
            path: '/options', 
            name: 'Options', 
            component: options,
            meta: {title: "Options | Admin"}
        }
    ]
});

router.beforeEach((to, from, next) => {
    document.title = `${to.meta.title}`;
    next();
});

export default router;