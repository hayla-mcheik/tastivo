<script setup>
import { switchTheme } from "../Pages/theme";
import NavLink from "../Components/NavLink.vue";
import { useI18n } from 'vue-i18n';
import { computed, onMounted, ref, watch } from 'vue'
import { usePage, Link, router } from '@inertiajs/vue3'
import { useCartStore } from '../store/cartStore';
import { storeToRefs } from 'pinia';
import CartCountAnimation from '../Components/CartCountAnimation.vue';
import { debounce } from 'lodash';

const { t, locale } = useI18n();
const currentRoute = computed(() => usePage().url);
const cartStore = useCartStore();
const { count: cartCount } = storeToRefs(cartStore);
const showSearchModal = ref(false);
const searchQuery = ref('');
const searchSuggestions = ref([]);
const selectedCategory = ref('');
const priceRange = ref([0, 100]);
const showFilters = ref(false);

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
onMounted(() => {
  if (!localStorage.getItem('locale')) {
    locale.value = 'en-US';
    localStorage.setItem('locale', 'en-US');
  }
});
// Initialize on component mount
const initializeLanguage = () => {
    const savedLocale = localStorage.getItem('locale') || 'en-US';
    locale.value = savedLocale;
    document.documentElement.lang = savedLocale;
    
    if (savedLocale === 'ar-AR') {
        document.documentElement.dir = 'rtl';
    }
};

initializeLanguage();

const page = usePage();
const user = computed(() => page.props.auth.user);
const show = ref(false);

// Initialize cart
cartStore.initialize();

// Debounced search function
const fetchSuggestions = debounce(async () => {
  if (searchQuery.value.trim().length >= 2) {
    try {
      const response = await router.get('/search', { 
        q: searchQuery.value,
        suggest: true 
      }, {
        preserveState: true,
        only: ['suggestions']
      });
      
      searchSuggestions.value = response.props.suggestions || [];
      
      // Check if any suggestion matches a category exactly
      const exactCategoryMatch = $page.props.categories.find(
        cat => cat.name.toLowerCase() === searchQuery.value.toLowerCase()
      );
      
      if (exactCategoryMatch) {
        selectedCategory.value = exactCategoryMatch.id;
      }
      
    } catch (error) {
      console.error('Error fetching suggestions:', error);
      searchSuggestions.value = [];
    }
  } else {
    searchSuggestions.value = [];
    selectedCategory.value = '';
  }
}, 300);

watch(searchQuery, () => {
  fetchSuggestions();
});

const performSearch = () => {
  const filters = {};
  if (selectedCategory.value) filters.category = selectedCategory.value;
  if (priceRange.value[0] > 0) filters.min_price = priceRange.value[0];
  if (priceRange.value[1] < 100) filters.max_price = priceRange.value[1];
  
  router.get('/search', { 
    q: searchQuery.value,
    ...filters
  });
  showSearchModal.value = false;
};

const selectSuggestion = (suggestion) => {
  searchQuery.value = suggestion;
  
  // Check if suggestion matches a category
  const matchedCategory = $page.props.categories.find(
    cat => cat.name.toLowerCase() === suggestion.toLowerCase()
  );
  
  if (matchedCategory) {
    selectedCategory.value = matchedCategory.id;
  } else {
    selectedCategory.value = '';
  }
  
  searchSuggestions.value = [];
  performSearch();
};
</script>

<template>
   <header class="ul-header">
        <div class="header-bottom-bg-wrapper bg-dark">
            <div class="ul-header-bottom">
                <div class="ul-header-bottom-wrapper ul-header-container">
              
                    
                    <div class="">
                        <div class="ul-header-socials">
                            <div class="links">
                                <a class="text-white" href="#"><i class="fab fa-whatsapp fa-2x"></i></a>
                                <a class="text-white" href="#"><i class="fab fa-instagram fa-2x"></i></a>
                                <a class="text-white" href="#"><i class="fab fa-facebook-f fa-2x"></i></a>
                            </div>
                        </div>
                    </div>

                    <div class="logo-container">
                        <Link href="/" class="block">
                            <img src="/public/assets/img/logo.png" class="" />
                        </Link>
                    </div>
                    
                    <div class="ul-header-actions">
                        <div class="hidden md:flex">
                        <!-- Search Button -->
                        <button 
                            @click="showSearchModal = true"
                            class="inline-flex items-center justify-center p-2 hover:bg-gray-100 rounded-full group text-white"
                        >
                            <i class="fas fa-search fa-2x"></i>
                        </button>
                        
                        <!-- Cart Button -->
                        <Link 
                            href="/cart"
                            class="relative text-white inline-flex items-center justify-center p-2 hover:bg-gray-100 rounded-full group cart-icon"
                            :class="{ 'text-red-600': currentRoute === '/cart' }"
                        >
                            <div class="relative">
                                <i class="fas fa-shopping-cart fa-2x"></i>
                                <CartCountAnimation :count="cartCount" />
                            </div>
                        </Link>
                        </div>
                        <!-- Language Toggle -->
                        <div class="toggle-language items-center gap-2 hidden">
                            <a 
                                v-for="lang in availableLocales" 
                                :key="lang.code"
                                href="#" 
                                @click.prevent="changeLanguage(lang.code)" 
                                class="language-option"
                                :class="{ 'active': locale === lang.code }">
                                <span :class="`fi fi-${lang.flag} text-sm`"></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <div class="header-bottom-bg-wrapper bg-dark md:hidden">
            <div class="ul-header-bottom">
                <div class="ul-header-bottom-wrapper ul-header-container">
              
                    <div class="logo-container">
                        <Link href="/" class="block">
                            <img src="/public/assets/img/logo.png" class="" />
                        </Link>
                    </div>
                    
                    <div class="">
                        <div class="ul-header-socials">
                            <div class="links">
                                <a class="text-white" href="#"><i class="fab fa-whatsapp fa-2x"></i></a>
                                <a class="text-white" href="#"><i class="fab fa-instagram fa-2x"></i></a>
                                <a class="text-white" href="#"><i class="fab fa-facebook-f fa-2x"></i></a>
                            </div>
                        </div>
                    </div>

          
                    
                
                </div>
            </div>
        </div>
        <!-- Enhanced Search Modal -->
        <div v-if="showSearchModal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl w-full max-w-xl overflow-hidden shadow-2xl transform transition-all">
                <div class="relative">
                    <!-- Close Button -->
                    <button @click="showSearchModal = false" class="absolute top-4 right-4 text-white hover:text-gray-700 z-10">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                    
                    <!-- Search Header -->
                    <div class="bg-gradient-to-r from-red-500 to-red-600 p-6 text-white">
                        <h3 class="text-2xl font-bold">{{ t('search.title') }}</h3>
                        <p class="text-red-100 mt-1">{{ t('search.subtitle') }}</p>
                    </div>
                    
                    <!-- Search Input -->
                    <div class="p-4 border-b">
                        <div class="relative">
                            <input
                                type="text"
                                v-model="searchQuery"
                                @keyup.enter="performSearch"
                                :placeholder="t('search.placeholder')"
                                class="w-full rounded-full border-0 bg-gray-100 px-5 py-3 pr-12 focus:ring-2 focus:ring-red-600 focus:bg-white transition-all"
                                autofocus
                            />
                            <button
                                @click="performSearch"
                                class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-red-600 text-white rounded-full p-2 hover:bg-red-600 transition-colors"
                            >
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                        </div>
                        
                        <!-- Search Suggestions -->
                        <div v-if="searchSuggestions.length > 0" class="mt-2 bg-white rounded-lg shadow-lg overflow-hidden">
                            <div 
                                v-for="(suggestion, index) in searchSuggestions" 
                                :key="index"
                                @click="selectSuggestion(suggestion)"
                                class="px-4 py-3 hover:bg-gray-50 cursor-pointer border-b last:border-b-0 flex items-center"
                            >
                                <i class="fas fa-search text-gray-400 mr-3"></i>
                                <span>{{ suggestion }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Filters Section -->
                    <div class="p-4">
                        <button 
                            @click="showFilters = !showFilters"
                            class="flex items-center text-gray-600 hover:text-red-600 mb-2"
                        >
                            <i class="fas fa-sliders-h mr-2"></i>
                            <span>{{ t('search.filters') }}</span>
                            <i :class="['fas ml-2 transition-transform', showFilters ? 'fa-chevron-up' : 'fa-chevron-down']"></i>
                        </button>
                        
                        <div v-if="showFilters" class="bg-gray-50 rounded-lg p-4 mt-2">
                            <!-- Category Filter -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('search.category') }}</label>
                                <select 
                                    v-model="selectedCategory"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                >
                                    <option value="">{{ t('search.all_categories') }}</option>
                                    <option v-for="category in $page.props.categories" :value="category.id">
                                        {{ category.name }}
                                    </option>
                                </select>
                            </div>
                            
                            <!-- Price Range Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('search.price_range') }}</label>
                                <div class="flex items-center space-x-4">
                                    <input 
                                        type="range" 
                                        v-model="priceRange[0]" 
                                        :min="0" 
                                        :max="priceRange[1]"
                                        class="w-full"
                                    >
                                    <span class="text-sm text-gray-600 whitespace-nowrap">
                                        ${{ priceRange[0] }} - ${{ priceRange[1] }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Searches (optional) -->
                    <div class="p-4 bg-gray-50 border-t">
                        <h4 class="font-medium text-gray-700 mb-2">{{ t('search.recent_searches') }}</h4>
                        <div class="flex flex-wrap gap-2">
                            <span 
                                v-for="(search, index) in ['Pizza', 'Burger', 'Pasta']"
                                :key="index"
                                @click="searchQuery = search; performSearch()"
                                class="px-3 py-1 bg-white rounded-full text-sm shadow-sm border cursor-pointer hover:bg-red-50 hover:border-red-200"
                            >
                                {{ search }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
</template>

<style scoped>
.ul-header {
    position: relative;
    z-index: 50;
}

.header-top-bg-wrapper {
    background-color: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
}

.ul-header-top {
    padding: 0.5rem 0;
}

.ul-header-container {
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.ul-header-top-left {
    display: flex;
    align-items: center;
}

.ul-header-contact-infos {
    display: flex;
    gap: 1.5rem;
}

.ul-header-contact-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: #6c757d;
}

.ul-header-contact-info i {
    color: #dc3545;
}

.ul-header-top-right {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.ul-header-auth-options {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #6c757d;
}

.header-bottom-bg-wrapper {
    background-color: white;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    position: fixed;
  top: 0;
  z-index: 1020;
  will-change: transform; /* Optimize for animations */
  transition: all 0.2s ease-out;
  transform: translateZ(0); /* Hardware acceleration */
}

.ul-header-bottom {
    padding: 0.75rem 0;
}

.ul-header-bottom-wrapper {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.logo-container {
    flex-shrink: 0;
}

.ul-header-nav-wrapper {
    flex: 1;
    display: flex;
    justify-content: center;
}

.ul-header-socials .links {
    display: flex;
    gap: 1rem;
}

.ul-header-socials .links a {
    color: #6c757d;
    transition: color 0.2s;
}

.ul-header-socials .links a:hover {
    color: #dc3545;
}

.ul-header-actions {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.language-option {
    padding: 0.25rem;
    border-radius: 0.25rem;
    transition: all 0.3s ease;
}

.language-option:hover {
    background-color: #f0f0f0;
}

.language-option.active {
    background-color: #e0e0e0;
    border: 1px solid #ccc;
}

/* Mobile styles */
@media (max-width: 768px) {
    .ul-header-top {
        padding: 0.5rem;
    }
    
    .ul-header-contact-infos {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .ul-header-top-right {
        gap: 1rem;
    }
    
    .ul-header-bottom-wrapper {
        flex-wrap: wrap;
        gap: 1rem;
    }
    
    .ul-header-nav-wrapper {
        order: 3;
        width: 100%;
        justify-content: flex-end;
    }
}
.header-bottom-bg-wrapper {
    padding: 0;
}
</style>