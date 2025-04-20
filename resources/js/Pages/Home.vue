<script setup>

import Menusliders from '../Components/menusliders.vue';
import PopularProducts from '../Components/PopularProducts.vue';
import DesktopLayout from '../Components/DesktopLayout.vue';

defineProps({
  sliders: Object,
  products: Object,
  categories: Object,
});
</script>

<template>
  <!-- Mobile layout -->
  <div class="block md:hidden">
    <Menusliders :categories="categories"/>
    <PopularProducts :products="products" :categories="categories"/>
  </div>
<div class="hidden md:flex">
  <!-- Desktop layout -->
  <DesktopLayout 
    :categories="categories"
  />
  <div class="flex md:hidden">
  <!-- Popular products shown below on both layouts -->
  <PopularProducts 
    class="mt-8"
    :products="products" 
    :categories="categories"
  />
  </div>
  </div>
</template>