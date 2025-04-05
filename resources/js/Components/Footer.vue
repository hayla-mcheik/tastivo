<script setup>
import { computed } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'

const currentRoute = computed(() => usePage().url)

// Define props and animation trigger
const props = defineProps({
    cartCount: {
        type: Number,
        required: false,
        default: 0
    }
})

const emit = defineEmits(['triggerAnimation'])

let isAnimating = false

const triggerAnimation = () => {
    isAnimating = true
    setTimeout(() => {
        isAnimating = false
    }, 1000)
}
</script>

<template>
    <div class="fixed bottom-0 left-0 z-50 w-full h-12 bg-white border-t border-gray-200 dark:bg-gray-700 dark:border-gray-600">
        <div class="grid h-full max-w-lg grid-cols-4 mx-auto font-medium">
            <!-- Home Button -->
            <Link 
                href="/"
                class="inline-flex flex-col items-center justify-center px-5 hover:bg-gray-50 dark:hover:bg-gray-800 group"
                :class="{ 'text-red-600 dark:text-red-400': currentRoute === '/' }"
            >
                <i class="fa-solid fa-house"></i>
            </Link>
            
            <!-- Cart Button -->
            <Link 
                href="/cart"
                class="relative inline-flex flex-col items-center justify-center px-5 py-1 transition-all duration-300 hover:bg-gray-100 dark:hover:bg-gray-800 group"
                :class="{ 'text-red-600 dark:text-red-400': currentRoute === '/cart' }"
            >
                <!-- Cart Icon -->
                <i class="fa-solid fa-cart-shopping text-xl group-hover:scale-110 transition-transform duration-200"></i>

                <!-- Badge with enhanced animation -->
                <span
                    v-if="cartCount > 0"
                    class="absolute top-0 right-4 bg-red-600 text-white text-xs font-bold px-1.5 py-0.5 rounded-full shadow-lg"
                    :class="{
                        'animate-bounce': isAnimating,
                        'animate-pulse': !isAnimating && cartCount > 0
                    }"
                >
                    {{ cartCount }}
                </span>
            </Link>
            
            <!-- Search Button -->
            <Link 
                href="/search"
                class="ul-header-search-opener inline-flex flex-col items-center justify-center px-5 hover:bg-gray-50 dark:hover:bg-gray-800 group"
                :class="{ 'text-red-600 dark:text-red-400': currentRoute === '/search' }"
            >
                <i class="fa-solid fa-magnifying-glass hover:text-gray-300"></i>
            </Link>
            
            <!-- Info Button -->
            <Link 
                href="/info"
                class="inline-flex flex-col items-center justify-center px-5 hover:bg-gray-50 dark:hover:bg-gray-800 group"
                :class="{ 'text-red-600 dark:text-red-400': currentRoute === '/info' }"
            >
                <i class="fa-solid fa-circle-info"></i>
            </Link>
        </div>
    </div>
</template>

<style scoped>
.animate-bounce {
  animation: bounce 0.5s cubic-bezier(0.5, 1.5, 0.5, 1.5) 3;
}

@keyframes bounce {
  0%, 100% { transform: translateY(0) scale(1); }
  30% { transform: translateY(-10px) scale(1.2); }
  60% { transform: translateY(0) scale(0.9); }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}
</style>