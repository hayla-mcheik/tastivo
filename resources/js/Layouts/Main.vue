<script setup>
import { switchTheme } from "../Pages/theme";
import NavLink from "../Components/NavLink.vue";
import InputField from "../Components/InputField.vue";
import { Link, usePage } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import logoDark from '../../../public/assets/img/logo-dark.svg';
import Footer from "../Components/Footer.vue";
import { useI18n } from 'vue-i18n';
import {  watch } from 'vue';
import CartInitializer from '../Components/CartInitializer.vue';
const { t, locale } = useI18n();

// Available languages
const availableLocales = [
    { code: 'en-US', name: 'English', flag: 'gb' },
    { code: 'fr-FR', name: 'Français', flag: 'fr' },
    { code: 'ar-AR', name: 'العربية', flag: 'ae' }
];

// Change language function
const changeLanguage = (langCode) => {
    locale.value = langCode;
    localStorage.setItem('locale', langCode);
    document.documentElement.lang = langCode;
    
    // Set direction for RTL languages (like Arabic)
    if (langCode === 'ar-AR') {
        document.documentElement.dir = 'rtl';
    } else {
        document.documentElement.dir = 'ltr';
    }
};

// Initialize on component mount
const initializeLanguage = () => {
    const savedLocale = localStorage.getItem('locale') || 'ar-AR';
    locale.value = savedLocale;
    document.documentElement.lang = savedLocale;
    
    if (savedLocale === 'ar-AR') {
        document.documentElement.dir = 'rtl';
    }
};

initializeLanguage();

const page = usePage();
const user = computed(() => page.props.auth.user);

const cartCount = usePage().props.cartCount
const show = ref(false);

</script>

<template>


   <!-- SIDEBAR SECTION START -->
   <div class="ul-sidebar">
        <!-- header -->
        <div class="ul-sidebar-header">
            <div class="ul-sidebar-header-logo">
     
            </div>
            <!-- sidebar closer -->
            <button class="ul-sidebar-closer"><i class="flaticon-close"></i></button>
        </div>

        <div class="ul-sidebar-header-nav-wrapper d-block d-lg-none"></div>


        <!-- sidebar footer -->
        <div class="ul-sidebar-footer">
            <span class="ul-sidebar-footer-title">Follow us</span>

            <div class="ul-sidebar-footer-social">
                <a href="#"><i class="flaticon-facebook"></i></a>
                <a href="#"><i class="flaticon-twitter"></i></a>
                <a href="#"><i class="flaticon-instagram"></i></a>
                <a href="#"><i class="flaticon-youtube"></i></a>
            </div>
        </div>
    </div>
    <!-- SIDEBAR SECTION END -->

    <!-- SEARCH MODAL SECTION START -->
    <div class="ul-search-form-wrapper flex-grow-1 flex-shrink-0">
        <button class="ul-search-closer"><i class="flaticon-close"></i></button>

        <form action="#" class="ul-search-form">
            <div class="ul-search-form-right">
                <input type="search" name="search" id="ul-search" placeholder="Search Here">
                <button type="submit"><span class="icon"><i class="flaticon-search"></i></span></button>
            </div>
        </form>
    </div>
    <!-- SEARCH MODAL SECTION END -->

    <!-- HEADER SECTION START -->
    <header class="ul-header">
        <div class="header-top-bg-wrapper">
            <div class="ul-header-top">
                <div class="ul-header-container">
                    <div class="ul-header-top-left">
                        <div class="ul-header-contact-infos">
                            <span class="ul-header-contact-info"><i class="flaticon-location-pin colored"></i> beirut badaro</span>
                        </div>
                    </div>

                    <div class="ul-header-top-right">
                        <div v-if="user" class="relative flex items-center gap-4">
                    <div
                        @click="show = !show"
                        class="flex items-center gap-2 px-3 py-1 rounded-lg hover:bg-slate-700 cursor-pointer"
                        :class="{ 'bg-slate-700': show }"
                    >
                        <p>{{ user.name }}</p>
                        <i class="fa-solid fa-angle-down"></i>
                    </div>

                    <Link v-if="user.role === 'admin'"
                   :href="route('admin.index')"
                    class="hover:bg-slate-700 w-6 h-6 grid place-items-center rounded-full hover:outline outline-1 outline-white"
                >
                    <i class="fa-solid fa-lock"></i>
                </Link>

                    <!-------------- User dropdown menu -------------->
                    <div
                        v-show="show"
                        @click="show = false"
                        class="absolute z-50 top-16 right-0 bg-slate-800 text-white rounded-lg border-slate-300 border overflow-hidden w-40"
                    >
                    <Link
                            :href="route('listing.create')"
                            class="block w-full px-6 py-3 hover:bg-slate-700 text-left"
                            >New Listing</Link
                        >
                
                        <Link
                            :href="route('profile.edit')"
                            class="block w-full px-6 py-3 hover:bg-slate-700 text-left"
                            >Profile</Link
                        >

                        <Link
                            :href="route('dashboard')"
                            class="block w-full px-6 py-3 hover:bg-slate-700 text-left"
                            >Dashboard</Link
                        >

                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="block w-full px-6 py-3 hover:bg-slate-700 text-left"
                            >Logout</Link
                        >
                    </div>
                </div>
                        <div v-else class="ul-header-auth-options">
                            <i class="flaticon-user"></i>
                            <NavLink routeName="login" componentName="Auth/Login">Login</NavLink>
                            <span>/</span>
                            <NavLink routeName="register" componentName="Auth/Register">Register</NavLink>
                        </div>

                        <div class="ul-header-socials">
                            <span class="socials-title">Follow us: </span>
                            <div class="links">
                                <a href="#"><i class="flaticon-facebook"></i></a>
                                <a href="#"><i class="flaticon-twitter"></i></a>
                                <a href="#"><i class="flaticon-linkedin-big-logo"></i></a>
                                <a href="#"><i class="flaticon-youtube"></i></a>
                            </div>
                        </div>
                        <button
                    @click="switchTheme"
                    class="hover:bg-slate-700 w-6 h-6 grid place-items-center rounded-full hover:outline outline-1 outline-white"
                >
                    <i class="fa-solid fa-circle-half-stroke"></i>
                </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="header-bottom-bg-wrapper to-be-sticky">
            <div class="ul-header-bottom">
                <div class="ul-header-bottom-wrapper ul-header-container">
                    <div class="logo-containerr w-1/2 md:w-1/4">
                        <Link href="/" class="block w-1/2 md:w-[80px]">

                          <img src="/public/assets/img/logo.png" class="w-full" />
                        </Link>
                          <div>
                
                          </div>
                    </div>
                              <!-- header nav -->
                              <div class="ul-header-nav-wrapper">
                        <div class="to-go-to-sidebar-in-mobile">
                            <nav class="ul-header-nav">
         

          
                            </nav>
                        </div>
                    </div>
                    <div class="ul-header-actions">
        <div class="toggle-language d-flex items-center gap-2">
            <a 
                v-for="lang in availableLocales" 
                :key="lang.code"
                href="#" 
                @click.prevent="changeLanguage(lang.code)" 
                class="language-option"
                :class="{ 'active': locale === lang.code }"
            >
                <span :class="`fi fi-${lang.flag} text-sm`"></span>
            </a>
        </div>
    </div>


          

                </div>
            </div>
        </div>


    </header>
    <!-- HEADER SECTION END -->

    <main class="">
        <slot />
    </main>
        <!-- FOOTER SECTION START -->
<Footer :cart-count="cartCount" />
    <!-- FOOTER SECTION END -->
    <CartInitializer />
</template>

<style scoped>
.language-option {
    padding: 5px;
    border-radius: 3px;
    transition: all 0.3s ease;
}
.language-option:hover {
    background-color: #f0f0f0;
}
.language-option.active {
    background-color: #e0e0e0;
    border: 1px solid #ccc;
}
</style>