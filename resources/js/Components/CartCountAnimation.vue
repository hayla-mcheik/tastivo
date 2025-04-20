<script setup>
import { onMounted, onUnmounted, ref } from 'vue';

const props = defineProps({
    count: Number
});

const isAnimating = ref(false);
const animationType = ref('');

const handleAnimation = (event) => {
    if (event.detail.type === 'add') {
        animationType.value = 'add';
        isAnimating.value = true;
        setTimeout(() => {
            isAnimating.value = false;
        }, 1000);
    }
};

onMounted(() => {
    window.addEventListener('cart-animation', handleAnimation);
});

onUnmounted(() => {
    window.removeEventListener('cart-animation', handleAnimation);
});
</script>

<template>
    <span 
        v-if="count > 0"
        class="cart-count-badge  text-xs md:text-sm"
        :class="{
            'animate-pop': isAnimating && animationType === 'add',
            'animate-pulse': !isAnimating && count > 0
        }"
    >
        {{ count }}
        <span v-if="isAnimating && animationType === 'add'" class="particle"></span>
    </span>
</template>

<style scoped>
.cart-count-badge {
    position: absolute;
    top: -8px;
    right: -30px;
    background-color: #ef4444;
    color: white;
    font-size: 0.75rem;
    font-weight: bold;
    padding: 0.25rem 0.5rem;
    border-radius: 9999px;
    min-width: 1.5rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    z-index: 10;
    line-height: 1;
}

/* Pop animation */
.animate-pop {
    animation: pop 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

@keyframes pop {
    0% { transform: scale(1); }
    50% { transform: scale(1.5); }
    100% { transform: scale(1); }
}

/* Particle effect */
.particle {
    position: absolute;
    display: block;
    width: 4px;
    height: 4px;
    background-color: #ef4444;
    border-radius: 50%;
    animation: particle 0.8s ease-out forwards;
}

@keyframes particle {
    0% {
        opacity: 1;
        transform: translate(0, 0) scale(1);
    }
    100% {
        opacity: 0;
        transform: translate(
            calc(var(--x) * 20px),
            calc(var(--y) * 20px)
        ) scale(0);
    }
}

.particle:nth-child(1) { --x: 0.5; --y: -0.7; }
.particle:nth-child(2) { --x: -0.5; --y: -0.7; }
.particle:nth-child(3) { --x: 0; --y: -1; }
.particle:nth-child(4) { --x: 0.7; --y: -0.5; }
.particle:nth-child(5) { --x: -0.7; --y: -0.5; }

/* Pulse animation */
.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}
</style>