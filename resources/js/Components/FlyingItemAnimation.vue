<script setup>
import { onMounted, ref } from 'vue';

const props = defineProps({
    productImage: String,
    startPosition: Object,
    endPosition: Object
});

const isAnimating = ref(false);

onMounted(() => {
    isAnimating.value = true;
});
</script>

<template>
    <div 
        v-if="isAnimating"
        class="flying-item"
        :style="{
            '--start-x': `${startPosition.x}px`,
            '--start-y': `${startPosition.y}px`,
            '--end-x': `${endPosition.x}px`,
            '--end-y': `${endPosition.y}px`
        }"
        @animationend="isAnimating = false"
    >
        <img 
            :src="productImage" 
            class="w-8 h-8 object-cover rounded-full border-2 border-white shadow-lg"
        >
    </div>
</template>

<style scoped>
.flying-item {
    position: fixed;
    top: var(--start-y);
    left: var(--start-x);
    z-index: 100;
    animation: fly 0.8s cubic-bezier(0.42, 0, 0.58, 1) forwards;
    transform-origin: center;
    pointer-events: none;
}

@keyframes fly {
    0% {
        transform: translate(0, 0) scale(1);
        opacity: 1;
    }
    70% {
        opacity: 1;
    }
    100% {
        transform: 
            translate(
                calc(var(--end-x) - var(--start-x)),
                calc(var(--end-y) - var(--start-y))
            ) 
            scale(0.5);
        opacity: 0;
    }
}
</style>