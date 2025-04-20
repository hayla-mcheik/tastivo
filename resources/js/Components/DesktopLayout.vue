<script setup>
import VerticalCategories from './VerticalCategories.vue';
import CartSummary from './CartSummary.vue';
import Menusliders from './menusliders.vue';

defineProps({
  sliders: Array,
  categories: Array
});
</script>

<template>
  <div class="hidden md:flex container mx-auto gap-6 mt-6">
    <!-- Left Column - Categories (col-3) -->
    <div class="w-1/4">
      <VerticalCategories :categories="categories" />
    </div>
    
    <!-- Center Column - Slider (col-6) -->
    <div class="w-3/4">
        <Menusliders :categories="categories"/>
    </div>
    
    <!-- Right Column - Cart (col-3) -->
    <div class="w-1/4">
      <CartSummary />
    </div>
  </div>
</template>