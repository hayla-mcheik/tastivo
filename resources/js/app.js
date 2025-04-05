import "./bootstrap";
import "../css/app.css";

import { createApp, h, ref } from "vue";
import { createInertiaApp, Head, Link } from "@inertiajs/vue3";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";
import Main from "./Layouts/Main.vue";
import AdminLayout from "./Layouts/AdminLayout.vue";
import { createI18n } from "vue-i18n";
import english from "./langs/english";

const i18n = createI18n({
    legacy: false,
    locale: "ar-AR",
    fallbackLocale: "en",
    messages: {
        "en-US": english.messages,
        "fr-FR": english.messages,
        "ar-AR": english.messages,
    },
});

// Create reactive cart state
const cartState = ref({
    count: 0,
    isAnimating: false
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
            setup() {
                // Provide cart state management functions
                const triggerCartAnimation = () => {
                    cartState.value.isAnimating = true;
                    setTimeout(() => {
                        cartState.value.isAnimating = false;
                    }, 1000);
                };

                const updateCartCount = (count) => {
                    cartState.value.count = count;
                };

                return {
                    cartState,
                    triggerCartAnimation,
                    updateCartCount
                };
            }
        })
        .use(plugin)
        .use(ZiggyVue)
        .component("Head", Head)
        .component("Link", Link)
        .use(i18n);

        // Add global mixin for cart functionality
        vueApp.mixin({
            mounted() {
                if (!this.$page.props.auth?.user?.is_admin) {
                    this.fetchCartCount();
                }
            },
            methods: {
                async fetchCartCount() {
                    try {
                        const response = await axios.get(route('cart.count'));
                        this.updateCartCount(response.data.count);
                    } catch (error) {
                        console.error('Error fetching cart count:', error);
                    }
                },
                async addToCart(productId, quantity = 1) {
                    try {
                        await axios.post(route('cart.add'), {
                            product_id: productId,
                            quantity: quantity
                        });
                        this.triggerCartAnimation();
                        this.updateCartCount(this.cartState.count + quantity);
                    } catch (error) {
                        if (error.response?.data?.message === 'Please login to add items to cart') {
                            window.location.href = route('login');
                        }
                        console.error('Error adding to cart:', error);
                    }
                }
            }
        });

        vueApp.mount(el);
    },
    progress: {
        color: "#4f46e5",
        showSpinner: true,
    },
});