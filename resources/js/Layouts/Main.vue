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
import Navbar from "./Navbar.vue";
import FooterDesktop from "../Components/FooterDesktop.vue";
import VerticalCategories from '@/Components/VerticalCategories.vue';

const { t, locale } = useI18n();

defineProps({
  categories: Array
});
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
  <Navbar />
    <!-- HEADER SECTION END -->

    <main class="mt-20">
        <slot />
    </main>
    <div class="flex md:hidden">
        <!-- FOOTER SECTION START -->
<Footer :cart-count="cartCount" />
    <!-- FOOTER SECTION END -->
     </div>
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