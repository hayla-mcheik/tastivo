<template>
    <div 
      class="relative w-full overflow-hidden hero-slider"
      @touchstart="handleTouchStart"
      @touchmove="handleTouchMove"
      @touchend="handleTouchEnd"
    >
      <!-- Carousel container -->
      <div 
        ref="slider"
        class="flex transition-transform duration-300 ease-out"
        :style="{ transform: `translateX(-${offset}px)` }"
      >
        <!-- Category slides -->
        <div 
          v-for="(category, index) in categories"
          :key="index"
          class="flex-shrink-0"
          :style="{ width: '100%' }"
        >
          <div class="bg-whiteshadow-md overflow-hidden h-full hero-slider">
            <img 
              :src="`storage/${category.image}`" 
              class="w-full h-[12rem] md:h-48 object-cover"
              :alt="category.name"
            >
   
          </div>
        </div>
      </div>
  
      <!-- Navigation buttons -->
      <button 
        v-if="showNavigation"
        @click="prev"
        class="absolute left-0 top-1/2 -translate-y-1/2 z-10 bg-white rounded-full p-2 shadow-md hover:bg-gray-100 ml-2"
        :disabled="currentIndex === 0"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
      </button>
      <button 
        v-if="showNavigation"
        @click="next"
        class="absolute right-0 top-1/2 -translate-y-1/2 z-10 bg-white rounded-full p-2 shadow-md hover:bg-gray-100 mr-2"
        :disabled="currentIndex >= maxIndex"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
      </button>
    </div>
  </template>
  
  <script setup>
  import { ref, computed, onMounted, onUnmounted } from 'vue'
  
  const props = defineProps({
    categories: {
      type: Array,
      required: true,
    },
    slidesToShow: {
      type: Number,
      default: 1
    },
    slidesToScroll: {
      type: Number,
      default: 1
    }
  })
  
  const slider = ref(null)
  const currentIndex = ref(0)
  const slideWidth = ref(0)
  const offset = ref(0)
  const containerWidth = ref(0)
  const touchStartX = ref(0)
  const touchEndX = ref(0)
  const isDragging = ref(false)
  const startOffset = ref(0)
  const dragOffset = ref(0)
  
  const showNavigation = computed(() => props.categories.length > props.slidesToShow)
  const maxIndex = computed(() => Math.max(0, props.categories.length - props.slidesToShow))
  
  const updateDimensions = () => {
    if (slider.value) {
      containerWidth.value = slider.value.offsetWidth
      slideWidth.value = (containerWidth.value - 32) / props.slidesToShow // 32px for padding
      offset.value = currentIndex.value * slideWidth.value * props.slidesToScroll
    }
  }
  
  const next = () => {
    if (currentIndex.value < maxIndex.value) {
      currentIndex.value = Math.min(currentIndex.value + 1, maxIndex.value)
      offset.value = currentIndex.value * slideWidth.value * props.slidesToScroll
    }
  }
  
  const prev = () => {
    if (currentIndex.value > 0) {
      currentIndex.value = Math.max(currentIndex.value - 1, 0)
      offset.value = currentIndex.value * slideWidth.value * props.slidesToScroll
    }
  }
  
  const handleTouchStart = (e) => {
    isDragging.value = true
    touchStartX.value = e.touches[0].clientX
    startOffset.value = offset.value
  }
  
  const handleTouchMove = (e) => {
    if (!isDragging.value) return
    touchEndX.value = e.touches[0].clientX
    dragOffset.value = touchStartX.value - touchEndX.value
    
    // Apply the drag offset with resistance
    offset.value = startOffset.value + dragOffset.value * 0.5
  }
  
  const handleTouchEnd = () => {
    if (!isDragging.value) return
    isDragging.value = false
    
    // Determine if we should change slides based on swipe distance
    const threshold = slideWidth.value / 3
    const movedBy = touchStartX.value - touchEndX.value
  
    if (movedBy > threshold && currentIndex.value < maxIndex.value) {
      next()
    } else if (movedBy < -threshold && currentIndex.value > 0) {
      prev()
    } else {
      // Return to original position
      offset.value = currentIndex.value * slideWidth.value * props.slidesToScroll
    }
  }
  
  onMounted(() => {
    updateDimensions()
    window.addEventListener('resize', updateDimensions)
  })
  
  onUnmounted(() => {
    window.removeEventListener('resize', updateDimensions)
  })
  </script>
  
  <style scoped>
  /* Custom transition for smooth sliding */
  .transition-transform {
    transition-property: transform;
    will-change: transform;
  }
  
  /* Disable text selection during drag */
  .user-select-none {
    user-select: none;
  }
  </style>