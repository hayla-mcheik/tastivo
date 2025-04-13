import "./bootstrap";
import "../css/app.css";

import { createApp, h, ref } from "vue";
import { createInertiaApp, Head, Link } from "@inertiajs/vue3";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";
import Main from "./Layouts/Main.vue";
import AdminLayout from "./Layouts/AdminLayout.vue";
import { createI18n } from "vue-i18n";
import english from "./langs/english";
import arabic from "./langs/arabic";
import french from "./langs/french";
import { createPinia } from "pinia";
import router from "./router";

// Add this right after your imports in main.js
axios.defaults.withCredentials = true;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.baseURL = import.meta.env.VITE_API_BASE_URL || '';
const pinia = createPinia();

const i18n = createI18n({
    legacy: false,
    locale: localStorage.getItem('locale') || "ar-AR", // Get saved locale or default to Arabic
    fallbackLocale: "en-US",
    messages: {
        "en-US": english.messages,
        "fr-FR": french.messages,
        "ar-AR": arabic.messages,
    },
});

createInertiaApp({
    title: (title) => `${title} - My App`,
    resolve: (name) => {
        const pages = import.meta.glob("./Pages/**/*.vue", { eager: true });
        let page = pages[`./Pages/${name}.vue`];

        if (name.startsWith("Admin/")) {
            page.default.layout = AdminLayout;
        } else {
            page.default.layout = Main;
        }
        return page;
    },
    setup({ el, App, props, plugin }) {
        const vueApp = createApp({ 
            render: () => h(App, props),
        })
        .use(plugin)
        .use(pinia)
        .use(router)
        .use(ZiggyVue)
        .component("Head", Head)
        .component("Link", Link)
        .use(i18n);
        
        vueApp.mount(el);
    },
    progress: {
        color: "#4f46e5",
        showSpinner: true,
    },
});