<script setup>
import { defineProps, onMounted, ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
import { inject } from 'vue'
import { useCartStore } from '../store/cartStore';
import FlyingItem from '../Components/FlyingItem.vue';

const footer = inject('footer') 
const props = defineProps({
    category: Object,
    categories: Object,
    products: Array, 
    additions: Array,
    allProducts: Array ,
    initialCategory: Number
});


const activeCategory = ref(null);
const currentProducts = ref(props.products);
const flyingItems = ref([]);
const showAdditionModal = ref(false);
const selectedProduct = ref(null);
const selectedAdditions = ref([]);


const showImageModal = ref(false);
const modalImageUrl = ref('');


const fullStars = Math.floor(props.rating);
const halfStar = props.rating % 1 >= 0.5 ? fullStars + 1 : 0;

const openImageModal = (product) => {
  modalImageUrl.value = '/storage/' + product.image;
  showImageModal.value = true;
};

const cartItemQuantities = computed(() => {
  if (!cartStore.items || !Array.isArray(cartStore.items)) {
    return {};
  }
  
  return cartStore.items.reduce((quantities, item) => {
    if (item?.product?.id) {
      quantities[item.product.id] = item.quantity || 0;
    }
    return quantities;
  }, {});
});


const openAdditionModal = (product, event) => {
  selectedProduct.value = product;
  selectedAdditions.value = [];
  
  // Check if product has any additions
  const hasAdditions = (product.additions && product.additions.length > 0) || 
                      (props.additions && props.additions.some(a => a.product_id === product.id));
  
  if (hasAdditions) {
    showAdditionModal.value = true;
  } else {
    addToCartWithAnimation(product.id, event);
  }
};

const addToCartWithAnimation = async (productId, event) => {
  console.log(productId);
  const product = filteredProducts.value.find(p => p.id === productId) || 
                 props.products.find(p => p.id === productId);
  
  if (!product) {
    console.error('Product not found:', productId);
    return;
  }
  console.log(product);
  // Get cart icon position (footer cart)
  const cartIcon = document.querySelector('.cart-icon');
  const cartRect = cartIcon?.getBoundingClientRect();
  
  // Create flying item
  if (event && cartRect) {
    flyingItems.value.push({
      id: Date.now(),
      image: '/storage/' + product.image,
      startX: event.clientX,
      startY: event.clientY,
      endX: cartRect.left + cartRect.width / 2,
      endY: cartRect.top + cartRect.height / 2
    });
    
    // Remove after animation completes
    setTimeout(() => {
      flyingItems.value.shift();
    }, 1000);
  }
  
  await addToCart(productId);
};

const cartStore = useCartStore();
onMounted(() => {
    if (props.category?.id) {
    activeCategory.value = props.category.id;
  }
    cartStore.initialize();
});

const addToCart = async (productId) => {
    try {
        await cartStore.addToCart(productId, 1, selectedAdditions.value);
        
        if (footer?.triggerAnimation) {
            footer.triggerAnimation();
        }
        
        showAdditionModal.value = false;

    } catch (error) {
        toast.error(error.message || 'Failed to add to cart', {
            position: 'bottom-right',
            autoClose: 3000,
        });
    }
};

const confirmAdditions = (event) => {
  if (selectedProduct.value) {
    addToCartWithAnimation(selectedProduct.value.id, event);
  }
};


// Filter products based on active category
const filteredProducts = computed(() => {
  if (!activeCategory.value) return currentProducts.value;
  return currentProducts.value.filter(product => 
    product.category_id == activeCategory.value
  );
});

// Filter by category
const filterByCategory = (categoryId) => {
  if (categoryId === props.initialCategory) {

    activeCategory.value = props.initialCategory;
    currentProducts.value = props.products;
  } else {
    activeCategory.value = categoryId;
    currentProducts.value = props.allProducts;
  }
};

const clearFilter = () => {
  activeCategory.value = props.initialCategory;
  currentProducts.value = props.products;
};

const currentProductAdditions = computed(() => {
  if (!selectedProduct.value) return [];
  
  
  if (selectedProduct.value.additions && selectedProduct.value.additions.length > 0) {
    return selectedProduct.value.additions;
  }

  return props.additions?.filter(a => a.product_id === selectedProduct.value.id) || [];
});

const totalWithAdditions = computed(() => {
  if (!selectedProduct.value) return '0.00';
  

  const basePrice = Number(selectedProduct.value.price) || 0;
  
  const additionsTotal = selectedAdditions.value.reduce((total, additionId) => {
    const addition = currentProductAdditions.value.find(a => a.id === additionId);

    const price = Number(addition?.price) || 0;
    return total + price;
  }, 0);

  return (basePrice + additionsTotal).toLocaleString('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  });
});
const formatPrice = (price) => {
  // Convert to number first
  const num = Number(price);
  // Check if valid number
  if (isNaN(num)) return '0.00';
  // Format with 2 decimal places
  return num.toLocaleString('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  });
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
                            @click="filterByCategory(cat.id)"
                            class="px-4 py-1 text-capitalize text-sm font-bold transition-all duration-300                     
                                   text-black hover:text-primary-600 flex items-center gap-2"
                            :class="{
                                'text-primary-600 border-b-2 border-primary-600': activeCategory === cat.id
                            }"
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
        <!-- Active category indicator and clear filter -->
<!-- Active category indicator and clear filter -->
<div v-if="activeCategory && props.category?.id && activeCategory !== props.category.id" class="flex items-center justify-between mb-4 px-2">
    <div class="flex items-center">
        <span class="text-sm text-gray-500">Showing: </span>
        <span class="ml-2 font-medium">
            {{ categories.find(c => c.id === activeCategory)?.name }}
        </span>
    </div>
    <button 
        @click="clearFilter"
        class="text-xs text-gray-500 hover:text-primary-800 flex items-center"
    >
        <span>Show {{ categories.find(c => c.id === activeCategory)?.name ??  category.name }}</span>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>
</div>
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5">
            <!-- Product Card with Smaller Image -->
            <div v-for="product in filteredProducts" :key="product.id" 
                 class="group relative overflow-hidden rounded-2xl bg-white transition-all duration-500
                        hover:-translate-y-1 hover:shadow-xl border border-gray-100 hover:border-primary-100">
                
                <!-- Smaller Product Image Container -->
                <div class="relative h-40 overflow-hidden cursor-pointer product-image " @click="openImageModal(product)">
                    <img :src="'/storage/' + product.image" 
                         :alt="product.name"
                         class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110">
                    
                         <div v-if="cartItemQuantities && product.id in cartItemQuantities" 
     class="absolute bottom-2 right-2 bg-red-600 text-white text-xs font-bold 
            rounded-full h-10 w-10 flex items-center justify-center animate-bounce">
  {{ cartItemQuantities[product.id] }}
</div>

                    <!-- Floating Add to Cart -->
                    <button @click="openAdditionModal(product, $event)"
                            class="absolute bottom-3 right-3 flex h-10 w-10 items-center justify-center rounded-full 
                                bg-white/90 backdrop-blur-md  shadow-lg text-primary-600 transition-all 
                                duration-300 opacity-0 group-hover:opacity-100 hover:bg-primary-600 hover:text-white
                                transform translate-y-2 group-hover:translate-y-0">
                        <i class="text-red-600 fa-solid fa-cart-plus text-base"></i>
                    </button>
                </div>


                <!-- Product Info -->
                <div class="p-4 pt-3">
                    <div class="flex justify-between items-start mb-1">
                        <h3 class="text-capitalize text-lg font-bold text-gray-800 line-clamp-1 pr-2">
                            {{ product.name }}
                        </h3>
                        <span class="text-sm font-bold text-red-600 
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
    <i class="fa-solid fa-star text-yellow-400"></i>
    <span>{{ product.rate.toFixed(1) }}</span>
    <span>({{ product.review_count || 0 }})</span>
</div>
                        <button 
                            @click="openAdditionModal(product, $event)"
                        >
                            <i class="text-black fa-solid fa-cart-plus text-base"></i>
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

        <transition name="fade">
  <div v-if="showImageModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-90 p-4">
    <div class="relative max-w-4xl w-full max-h-[90vh]">
    <!-- Close button (X) -->
    <button 
      @click="showImageModal = false"
      class="absolute -top-10 right-0 text-white hover:text-gray-300 text-2xl z-10"
    >
      <i class="fas fa-times"></i>
    </button>
    
    <!-- The enlarged image -->
    <img 
  :src="modalImageUrl" 
  class="w-full h-full object-contain max-h-[80vh] rounded-2xl"
  alt="Product image preview"
>

  </div>
  </div>
</transition>

        <!-- Additions Modal -->
        <div v-if="showAdditionModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50  p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                      <div class="">
                        <p class="text-gray-500 text-sm mb-2">{{ props.category?.name }}</p>
                        <h3 class="text-lg font-bold">{{ selectedProduct?.name }}</h3>
                     
                    </div>
                 
                   
                        <button @click="showAdditionModal = false" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <div class="mb-6">
                        <h4 class="font-medium mb-3">Addons:</h4>
                        <div v-if="currentProductAdditions.length > 0" class="space-y-2">
                            <div v-for="addition in currentProductAdditions" :key="addition.id" 
                                 class="flex items-center justify-between p-3 border rounded-lg hover:bg-gray-50 transition-colors"
                                 :class="{ 'border-primary-300 bg-primary-50': selectedAdditions.includes(addition.id) }">
                                <div class="flex items-center">
                                    <input 
                                        type="checkbox" 
                                        :id="`addition-${addition.id}`"
                                        v-model="selectedAdditions"
                                        :value="addition.id"
                                        class="h-4 w-4 text-black rounded border-gray-300 focus:ring-black"
                                    >
                                    <label :for="`addition-${addition.id}`" class="ml-3 text-sm font-medium text-gray-700">
                                        {{ addition.name }}
                                    </label>
                                </div>
                                <span class="text-sm font-medium text-gray-900">+${{ formatPrice(addition.price) }}</span>
                            </div>
                        </div>
                        <div v-else class="text-gray-500 text-center py-4">
                            No additions available for this item
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center mb-6">
                        <span class="font-medium">Total:</span>
                        <span class="text-lg font-bold">${{ totalWithAdditions }}</span>
                    </div>
                    
      <!-- Updated Modal Footer -->
      <div class="flex justify-end space-x-3">
        <button 
            @click="showAdditionModal = false"
            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors"
        >
            Cancel
        </button>
        <button 
            @click="confirmAdditions($event)"
            class="px-4 py-2 bg-black rounded-md text-sm font-medium text-white hover:bg-primary-700 transition-colors"
        >
            Add to Cart (${{ totalWithAdditions }})
        </button>
    </div>

                </div>
            </div>
        </div>

        <!-- No products message -->
        <div v-if="filteredProducts.length === 0" class="text-center py-12">
            <div class="text-gray-400 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-700 mb-2">No products found</h3>
            <p class="text-gray-500 mb-4">Try selecting a different category</p>
            <button 
                @click="activeCategory = null"
                class="px-4 py-2 bg-black text-white rounded-lg hover:bg-black transition-colors"
            >
                Show all products
            </button>
        </div>
        
        <!-- Flying items container -->
        <div class="flying-items-container">
            <FlyingItem
                v-for="item in flyingItems"
                :key="item.id"
                :image="item.image"
                :start-x="item.startX"
                :start-y="item.startY"
                :end-x="item.endX"
                :end-y="item.endY"
            />
        </div>
    </section>
</template>

<style scoped>
/* Animation for category buttons */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(5px); }
  to { opacity: 1; transform: translateY(0); }
}

.hide-scrollbar {
  scrollbar-width: none; /* Firefox */
  -ms-overflow-style: none; /* IE and Edge */
}
.hide-scrollbar::-webkit-scrollbar {
  display: none; /* Chrome, Safari, Opera */
}

/* Active category button style */
.text-primary-600 {
  color: #2563eb;
}
.border-primary-600 {
  border-color: #2563eb;
}
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

/* Make the modal image zoom on appear */
img {
  transition: transform 0.3s ease;
}
img:hover {
  transform: scale(1.02);
}
.product-image::before{
  content: '';
  position: absolute;
  background-color: rgba(0, 0, 0, 0.273);
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
}
</style>