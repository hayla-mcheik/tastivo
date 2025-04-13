<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const { t, locale } = useI18n(); // Make sure to destructure locale here

defineProps({
    products: Object,
    categories: Object,
});

// Extract language code (en-US → en)
const currentLang = computed(() => locale.value.split('-')[0]);

// Get translated name
const translatedName = (category) => {
    if (category.translations && category.translations.length) {
        const translation = category.translations.find(t => t.locale === currentLang.value);
        return translation?.name || category.name;
    }
    return category.name;
};
</script>

<template>

    <section class="ul-foods-shop ul-section-spacing relative mb-5 pb-2">
        <div class="ul-shop-container">
            <div class="text-center mb-4 relative">
                <!-- Floating decorative elements -->
                <!-- Main title with layered effect -->
                <h2 class="text-2xl sm:text-6xl font-bold italic relative z-10">
                    <span class="absolute -z-10 -inset-2 bg-gradient-to-r from-[#fddfdf] to-transparent opacity-60 rounded-full blur-sm"></span>
                    <span class="relative bg-clip-text text-black">
                       
                        {{ t('categoriesmenu') }}
                    </span>
                </h2>

                <!-- Decorative divider -->
                <div class="flex justify-center items-center">
                    <span class="block w-16 h-0.5 bg-red-600"></span>
                    <span class="mx-4 text-red-600 text-xl">
                        <img src="/public/assets/img/sub-banner-3-img.png" class="w-[40px]" />
                    </span>
                    <span class="block w-16 h-0.5 bg-red-600"></span>
                </div>
            </div>
            
            <div class="row ul-bs-row row-cols-lg-4 row-cols-md-3 row-cols-2 row-cols-xxs-2">
            <div v-for="category in categories" :key="category.id" class="col">
                <div class="ul-food p-0 m-0 rounded-2xl">
                    <div class="ul-food-image w-full rounded-t-2xl" >
                        <Link :href="`/categories/${category.slug}`" class="block">
                            <img class="w-full" :src="'/storage/' + category.image" 
                                 :alt="translatedName(category) + ' Image'">
                        </Link>
                    </div>
                    <div class="ul-food-txt">
                        <Link :href="`/categories/${category.slug}`" class="ul-food-title py-2 text-uppercase">
                            {{ translatedName(category) }}
                        </Link>
                    </div>
                </div>
            </div>
        </div>
        


        </div>
    </section>

</template>

<style scoped>

</style>