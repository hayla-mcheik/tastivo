<script setup>
import { onMounted, ref } from 'vue';

const props = defineProps({
  image: String,
  startX: Number,
  startY: Number,
  endX: Number,
  endY: Number
});

const isVisible = ref(true);

onMounted(() => {
  // Auto-remove after animation completes
  setTimeout(() => {
    isVisible.value = false;
  }, 1000);
});
</script>

<template>
  <div 
    v-if="isVisible"
    class="flying-item"
    :style="{
      '--start-x': `${props.startX}px`,
      '--start-y': `${props.startY}px`,
      '--end-x': `${props.endX}px`,
      '--end-y': `${props.endY}px`
    }"
  >
    <img 
      :src="image" 
      class="item-image"
    />
  </div>
</template>

<style scoped>
.flying-item {
  position: fixed;
  top: var(--start-y);
  left: var(--start-x);
  z-index: 9999;
  pointer-events: none;
  animation: fly 0.4s cubic-bezier(0.42, 0, 0.58, 1) forwards;
  transform-origin: center;
}

.item-image {
  width: 40px;
  height: 40px;
  object-fit: cover;
  border-radius: 50%;
  border: 2px solid white;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
  transform: scale(1);
  animation: pulse 0.8s ease-in-out;
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

@keyframes pulse {
  0%, 100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.2);
  }
}
</style>