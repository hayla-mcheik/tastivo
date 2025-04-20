<script setup>
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const { t, locale } = useI18n(); 
defineProps({
  categories: Array
});

const currentLang = computed(() => locale.value.split('-')[0]);


const translatedName = (category) => {
    if (category.translations && category.translations.length) {
        const translation = category.translations.find(t => t.locale === currentLang.value);
        return translation?.name || category.name;
    }
    return category.name;
};
</script>

<template>
  <div class="bg-white rounded-lg shadow p-4 sticky top-4">
    <h3 class="font-bold text-lg mb-4"> 
        {{ t('categoriesmenu') }}</h3>
    <div class="space-y-2">
      <div v-for="category in categories" :key="category.id" class="accordion-item border-b pb-2">
        <Link 
          :href="`/categories/${category.slug}`"
          class="flex items-center p-2 hover:bg-gray-50 rounded transition"
        >
          <img 
            :src="'storage/' + category.image" 
            class="w-8 h-8 object-cover rounded-full mr-3"
           :alt="translatedName(category) + ' Image'"
          >
          <span>{{ translatedName(category) }}</span>
        </Link>
      </div>
    </div>
  </div>
</template>

<style scoped>
.accordion-item:last-child {
  border-bottom: none;
  padding-bottom: 0;
}
</style>