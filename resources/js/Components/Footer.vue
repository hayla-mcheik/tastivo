<script setup>
import { computed, ref, watch } from 'vue'
import { usePage, Link, router } from '@inertiajs/vue3'
import { useCartStore } from '../store/cartStore';
import { storeToRefs } from 'pinia';
import CartCountAnimation from '../Components/CartCountAnimation.vue';
import { debounce } from 'lodash';

const currentRoute = computed(() => usePage().url);
const cartStore = useCartStore();
const { count: cartCount } = storeToRefs(cartStore);
const showSearchModal = ref(false);
const searchQuery = ref('');
const searchSuggestions = ref([]);
const selectedCategory = ref('');
const priceRange = ref([0, 100]);
const showFilters = ref(false);

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
<div class="footer fixed bottom-0 left-0 z-50 w-full h-12 md:h-20 bg-white border-t border-gray-200 dark:bg-gray-700 dark:border-gray-600">
    <div class="grid h-full w-full grid-cols-4 font-medium">
        <!-- Home Button -->
        <Link 
            href="/"
            class="inline-flex flex-col items-center justify-center px-5 hover:bg-gray-50 dark:hover:bg-gray-800 group"
            :class="{ 'text-red-600 dark:text-red-600': currentRoute === '/' }"
        >
            <i class="fa-solid fa-house"></i>
        </Link>

        <!-- Cart Button -->
        <Link 
    href="/cart"
    class="cart-icon mobile-cart footer-cart relative inline-flex flex-col items-center justify-center px-5 hover:bg-gray-50 dark:hover:bg-gray-800 group "
    :class="{ 'text-red-600 dark:text-red-600': currentRoute === '/cart' }"
>
    <div class="relative">
        <i class="fa-solid fa-cart-shopping text-xl group-hover:scale-110 transition-transform duration-200"></i>
        <CartCountAnimation :count="cartCount" />
    </div>
</Link>

        <!-- Search Button -->
        <button 
            @click="showSearchModal = true"
            class="inline-flex flex-col items-center justify-center px-5 hover:bg-gray-50 dark:hover:bg-gray-800 group"
            :class="{ 'text-red-600 dark:text-red-600': currentRoute === '/search' }"
        >
            <i class="fa-solid fa-magnifying-glass hover:text-gray-300"></i>
        </button>

        <!-- Info Button -->
        <Link 
            href="/contact"
            class="inline-flex flex-col items-center justify-center px-5 hover:bg-gray-50 dark:hover:bg-gray-800 group"
            :class="{ 'text-red-600 dark:text-red-600': currentRoute === '/contact' }"
        >
            <i class="fa-solid fa-circle-info"></i>
        </Link>
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
                    <h3 class="text-2xl font-bold">What are you craving?</h3>
                    <p class="text-red-100 mt-1">Search our delicious menu</p>
                </div>
                
                <!-- Search Input -->
                <div class="p-4 border-b">
                    <div class="relative">
                        <input
                            type="text"
                            v-model="searchQuery"
                            @keyup.enter="performSearch"
                            placeholder="Search for categories..."
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
                        <span>Filters</span>
                        <i :class="['fas ml-2 transition-transform', showFilters ? 'fa-chevron-up' : 'fa-chevron-down']"></i>
                    </button>
                    
                    <div v-if="showFilters" class="bg-gray-50 rounded-lg p-4 mt-2">
                        <!-- Category Filter -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                            <select 
                                v-model="selectedCategory"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                            >
                                <option value="">All Categories</option>
                                <option v-for="category in $page.props.categories" :value="category.id">
                                    {{ category.name }}
                                </option>
                            </select>
                        </div>
                        
                        <!-- Price Range Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Price Range</label>
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
                    <h4 class="font-medium text-gray-700 mb-2">Recent Searches</h4>
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
</template>

<style scoped>
/* Add any custom styles here */
</style>