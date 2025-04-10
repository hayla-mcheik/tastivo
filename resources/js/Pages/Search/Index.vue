<script setup>
import { computed, onMounted } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    query: String,
    products: Array,
    suggestions: Array,
    categories: Array,
    filters: Object,
    matchedCategory: Object,
});

const hasResults = computed(() => props.products && props.products.length > 0);
const selectedCategory = computed(() => {
    return props.categories.find(c => c.id == props.filters.category)?.name;
});

onMounted(() => {
    // Only redirect if we haven't already processed this category
    if (props.matchedCategory && props.filters.category !== props.matchedCategory.id) {
        router.get('/search', {
            q: props.query,
            category: props.matchedCategory.id,
        }, {
            preserveScroll: true,
            replace: true  // Use replace instead of push to avoid adding to history
        });
    }
});
const applyFilter = () => {
    router.get('/search', {
        q: props.query,
        category: props.filters.category,
        min_price: props.filters.min_price,
        max_price: props.filters.max_price,
    }, {
        preserveScroll: true // Preserve scroll position on navigation
    });
};

const clearCategoryFilter = () => {
    router.get('/search', {
        q: props.query,
        min_price: props.filters.min_price,
        max_price: props.filters.max_price,
    }, {
        preserveScroll: true
    });
};
</script>

<template>
    <Head title="Search Results" />
  
    <div class="container mx-auto px-4 py-8 min-h-screen">
        <!-- Search Header -->
        <div class="mb-8 bg-gradient-to-r from-red-50 to-red-100 rounded-xl p-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Search Results</h1>
            <div class="flex items-center">
                <p class="text-lg text-gray-600">
                    Showing results for: 
                    <span class="font-semibold text-red-600">{{ query || 'All items' }}</span>
                </p>
                <span class="mx-4 text-gray-400">|</span>
                <span class="text-gray-600">{{ products.length }} items found</span>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="mb-8 bg-white rounded-lg shadow p-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select 
                        v-model="filters.category"
                        @change="applyFilter"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                    >
                        <option 
                            v-for="category in categories" 
                            :value="category.id"
                            :key="category.id"
                        >
                            {{ category.name }}
                        </option>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Results -->
        <div v-if="hasResults" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 pb-8">
            <div 
                v-for="product in products" 
                :key="product.id" 
                class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300"
            >
                <Link :href="product.route" class="block">
                    <div class="relative h-48 overflow-hidden">
                        <img 
                            :src="'/storage/' + product.image" 
                            :alt="product.name" 
                            class="w-full h-full object-cover hover:scale-105 transition-transform duration-500"
                        >
                        <div class="absolute bottom-2 left-2 bg-white/90 px-2 py-1 rounded-full text-xs font-bold">
                            {{ product.category }}
                        </div>
                        <div v-if="product.rating" class="absolute top-2 left-2 bg-white/90 px-2 py-1 rounded-full flex items-center">
                            <i class="fas fa-star text-yellow-400 mr-1"></i>
                            <span class="text-xs font-bold">{{ product.rating.toFixed(1) }}</span>
                        </div>
                    </div>
                    <div class="p-4">
                        <h3 class="font-semibold text-lg mb-1 truncate">{{ product.name }}</h3>
                        <p class="font-bold text-red-600 text-lg">${{ product.price }}</p>
                        <div v-if="product.review_count" class="flex items-center mt-1 text-gray-500 text-sm">
                            <i class="fas fa-comment-alt mr-1"></i>
                            <span>{{ product.review_count }} reviews</span>
                        </div>
                    </div>
                </Link>
            </div>
        </div>
        
        <!-- Empty State -->
        <div v-else class="text-center py-16">
            <div class="max-w-md mx-auto">
                <i class="fas fa-search text-5xl text-gray-300 mb-6"></i>
                <h3 class="text-xl font-medium text-gray-700 mb-2">
                    {{ query ? 'No results found' : 'Search our menu' }}
                </h3>
                <p class="text-gray-500 mb-6">
                    {{ query 
                        ? 'Try different keywords or filters' 
                        : 'Enter a dish name or category to find delicious options' 
                    }}
                </p>
            </div>
        </div>
    </div>
</template>

<style scoped>
.container {
    overflow-y: auto;
    height: 100vh;
}

.grid {
    min-height: 0;
    min-width: 0;
    overflow-anchor: none; /* Prevent scroll jump */
}

/* Smooth scrolling */
html {
    scroll-behavior: smooth;
}
</style>