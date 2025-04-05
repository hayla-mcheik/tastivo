<script setup>
import { defineProps } from 'vue';
import { router } from '@inertiajs/vue3';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
import { inject } from 'vue'

const footer = inject('footer') 
const props = defineProps({
    categories: Object,
    products: Object
});

const addToCart = async (productId) => {
    try {
        await router.post(route('cart.add'), {
            product_id: productId,
            quantity: 1
        }, {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Product added to cart!', {
                    autoClose: 2000,
                    position: 'bottom-right'
                });
            },
            onError: (errors) => {
                if (errors.message === 'Please login to add items to cart') {
                    toast.info('Please login to add items to cart', {
                        autoClose: 3000,
                        position: 'bottom-right'
                    });
                } else if (errors.message === 'Admins cannot add items to cart') {
                    toast.warning('Admins cannot add items to cart', {
                        autoClose: 3000,
                        position: 'bottom-right'
                    });
                } else {
                    toast.error('Failed to add product to cart', {
                        autoClose: 2000,
                        position: 'bottom-right'
                    });
                }
            }
        });

    } catch (error) {
        toast.error('An error occurred. Please try again.', {
            autoClose: 2000,
            position: 'bottom-right'
        });
    }
};
</script>

<template>
    <!-- Animated Category Pills -->
    <div class="sticky top-0 z-20 bg-gradient-to-r from-white to-gray-50 shadow-sm backdrop-blur-sm">
        <div class="container mx-auto">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 w-6 bg-gradient-to-r from-white to-transparent z-10"></div>
                <ul class="flex space-x-3 overflow-x-auto py-2 hide-scrollbar pl-1">
                    <li v-for="(cat, index) in categories" :key="cat.id" class="flex-shrink-0">
                        <button 
                            class="px-4 py-1 text-capitalize text-sm font-bold transition-all duration-300                     
                                   text-black hover:text-primary-600 flex items-center gap-2"
                            :style="{
                                animation: `fadeIn 0.3s ease-out ${index * 0.05}s forwards`,
                                opacity: 0
                            }">
                            <span class="w-2 h-2 rounded-full" 
                                  :class="{
                                      'bg-green-400': index % 3 === 0,
                                      'bg-red-400': index % 3 === 1,
                                      'bg-rose-400': index % 3 === 2
                                  }"></span>
                            {{ cat.name }}
                        </button>
                    </li>
                </ul>
                <div class="absolute inset-y-0 right-0 w-6 bg-gradient-to-l from-white to-transparent z-10"></div>
            </div>
        </div>
    </div>

    <!-- Creative Product Grid with Smaller Images -->
    <section class="container mx-auto py-8 md:py-12 mb-5">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5">
            <!-- Product Card with Smaller Image -->
            <div v-for="product in products" :key="product.id" 
                 class="group relative overflow-hidden rounded-2xl bg-white transition-all duration-500
                        hover:-translate-y-1 hover:shadow-xl border border-gray-100 hover:border-primary-100">
                
                <!-- Smaller Product Image Container -->
                <div class="relative h-40 overflow-hidden">
                    <img :src="'/storage/' + product.image" 
                         :alt="product.name"
                         class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110">
                    
                    <!-- Floating Add to Cart -->
                    <button @click="addToCart(product.id)"
                            class="absolute bottom-3 right-3 flex h-10 w-10 items-center justify-center rounded-full 
                                bg-white/90 backdrop-blur-sm shadow-lg text-primary-600 transition-all 
                                duration-300 opacity-0 group-hover:opacity-100 hover:bg-primary-600 hover:text-white
                                transform translate-y-2 group-hover:translate-y-0">
                        <i class="text-red-500 fa-solid fa-cart-plus text-base"></i>
                    </button>
                </div>

                <!-- Product Info -->
                <div class="p-4 pt-3">
                    <div class="flex justify-between items-start mb-1">
                        <h3 class="text-capitalize text-lg font-bold text-gray-800 line-clamp-1 pr-2">
                            {{ product.name }}
                        </h3>
                        <span class="text-sm font-bold text-red-500 
                                    px-2 py-1 rounded-full whitespace-nowrap">
                            ${{ product.price }}
                        </span>
                    </div>
                    
                    <p class="text-sm text-gray-500 line-clamp-2 mb-3">
                        {{ product.desc }}
                    </p>
                    
                    <!-- Rating and Cart -->
                    <div class="flex items-center justify-between text-xs">
                        <div class="flex items-center space-x-1 text-black">
                            <i class="fa-solid fa-star text-black"></i>
                            <span>4.8</span>
                            <span>(24)</span>
                        </div>
                        <button @click="addToCart(product.id)" 
                                class="text-primary-500 hover:text-primary-600 transition-colors">
                            <i class="fa-solid fa-cart-shopping text-base"></i>
                        </button>
                    </div>
                </div>

                <!-- Creative Corner Element -->
                <div class="absolute top-0 left-0 w-16 h-16 overflow-hidden">
                    <div class="absolute -left-8 -top-8 w-16 h-16 rotate-45 bg-primary-600/10 
                                group-hover:bg-primary-600/20 transition-all duration-500"></div>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
/* Your existing styles */
</style>