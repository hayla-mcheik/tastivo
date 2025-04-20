<script setup>
import { useCartStore } from '../store/cartStore';
import { onMounted } from 'vue';

const cartStore = useCartStore();

onMounted(async () => {
    await cartStore.initialize();
});
</script>

<template>
    <!-- This component doesn't render anything -->
    <!-- It just initializes the cart when mounted -->
</template>