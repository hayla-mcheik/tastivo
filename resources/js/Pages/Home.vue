<script setup>

import HeroSlider from '../Components/HeroSlider.vue'
import Menusliders from '../Components/menusliders.vue';
import PopularProducts from '../Components/PopularProducts.vue';
defineProps({
sliders:Object,
products:Object,
categories:Object,
});

</script>

<template>
<Menusliders :categories="categories"/>
<PopularProducts :products="products" :categories="categories"/>
</template>