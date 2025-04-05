<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    products: Object,
    categories: Object,
});
</script>

<template>
    <section class="ul-foods-shop ul-section-spacing relative mb-5">
        <div class="ul-shop-container">
            <div class="text-center mb-4 relative">
                <!-- Floating decorative elements -->
                <!-- Main title with layered effect -->
                <h2 class="text-2xl sm:text-6xl font-bold italic relative z-10">
                    <span class="absolute -z-10 -inset-2 bg-gradient-to-r from-[#fddfdf] to-transparent opacity-60 rounded-full blur-sm"></span>
                    <span class="relative bg-clip-text text-transparent bg-gradient-to-r from-[#9d0606] to-[#346607]">
                        Categories Menu
                    </span>
                </h2>

                <!-- Decorative divider -->
                <div class="flex justify-center items-center">
                    <span class="block w-16 h-0.5 bg-[#9d0606]"></span>
                    <span class="mx-4 text-[#9d0606] text-xl">
                        <img src="/public/assets/img/sub-banner-3-img.png" class="w-[40px]" />
                    </span>
                    <span class="block w-16 h-0.5 bg-[#9d0606]"></span>
                </div>
            </div>
            
            <div class="row ul-bs-row row-cols-lg-4 row-cols-md-3 row-cols-2 row-cols-xxs-2">
                <!-- single menu item -->
                <div v-for="category in categories" :key="category.id" class="col">
                    <div class="ul-food p-0 m-0">
                        <div class="ul-food-image w-full">
                            <Link :href="`/categories/${category.slug}`" class="block">
                                <img class="w-full" :src="'/storage/' + category.image" :alt="category.name + ' Image'">
                            </Link>
                        </div>
                        <div class="ul-food-txt">
                            <Link :href="`/categories/${category.slug}`" class="ul-food-title pt-1 text-uppercase">
                                {{ category.name }}
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <div class="ul-banner-vectors">
                <img src="/public//assets/img/banner-vector-1.png" alt="vector" class="vector-1 wow animate__fadeInLeft">
                <img src="/public//assets/img/banner-vector-2.png" alt="vector" class="vector-2 wow animate__fadeInLeft">
                <img src="/public//assets/img/banner-vector-3.png" alt="vector" class="vector-3 wow animate__fadeInRight">
            </div>
        </div>
    </section>
</template>

<style scoped>
.ul-menus-tab-navs {
    flex-wrap: nowrap !important;
    overflow-x: scroll;
}
</style>