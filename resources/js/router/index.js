import { createRouter, createWebHistory } from "vue-router";

import notFound from '../components/notFound.vue';

const routes = [
    {
        path: '/:catchAll(.*)', // Wildcard route for 404 Not Found
        name: 'NotFound',
        component: notFound,
        meta: {
            requiresAuth: false
        },
    }
];

const router = createRouter({
    history: createWebHistory(), 
    routes,
});

// Navigation Guard
// router.beforeEach((to) => {
//     const isAuthenticated = !!localStorage.getItem('token');

//     if (to.meta.requiresAuth && !isAuthenticated) {
//         return { name: 'Login' };
//     }

//     if (!to.meta.requiresAuth && isAuthenticated && (to.name === 'Login' || to.name === 'Register')) {
//         return { name: 'adminHome' };
//     }
// });

export default router;
