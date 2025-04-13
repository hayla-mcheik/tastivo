<template>
  <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
      <!-- Header -->
      <div class="text-center mb-12">
        <h1 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
          Our Restaurant Information
        </h1>
        <p class="mt-3 max-w-2xl mx-auto text-xl text-gray-500 sm:mt-4">
          Find all our locations, opening hours and contact details
        </p>
      </div>

      <!-- Location Cards Grid -->
      <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
        <!-- Location Card (repeat for each location) -->
        <div 
          v-for="(location, index) in locations" 
          :key="index"
          class="bg-white rounded-xl shadow-md overflow-hidden transition-all duration-300 hover:shadow-lg"
        >
          <!-- Location Image -->
          <div class="h-48 overflow-hidden">
            <img 
              :src="location.image" 
              :alt="`${location.name} restaurant`"
              class="w-full h-full object-cover"
            >
          </div>
          
          <!-- Location Info -->
          <div class="p-6">
            <!-- Name and Status -->
            <div class="flex items-start justify-between">
              <h3 class="text-xl font-bold text-gray-900">{{ location.name }}</h3>
              <span 
                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                :class="location.isOpen ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
              >
                {{ location.isOpen ? 'Open Now' : 'Closed' }}
              </span>
            </div>

            <!-- Address -->
            <div class="mt-4 flex items-start">
              <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-base text-gray-500">{{ location.address }}</p>
              </div>
            </div>

            <!-- Hours -->
            <div class="mt-4 flex items-start">
              <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-base font-medium text-gray-900">Opening Hours</p>
                <div class="mt-1 text-sm text-gray-500">
                  <p v-for="(hour, day) in location.hours" :key="day" class="flex justify-between">
                    <span class="font-medium">{{ day }}:</span>
                    <span>{{ hour }}</span>
                  </p>
                </div>
              </div>
            </div>

            <!-- Contact and Delivery -->
            <div class="mt-6 pt-6 border-t border-gray-200">
              <!-- Phone -->
              <div class="flex items-center">
                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
                <a :href="`tel:${location.phone}`" class="ml-3 text-base text-gray-500 hover:text-gray-900">
                  {{ location.phone }}
                </a>
              </div>

              <!-- Email (if exists) -->
              <div v-if="location.email" class="mt-3 flex items-center">
                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <a :href="`mailto:${location.email}`" class="ml-3 text-base text-gray-500 hover:text-gray-900">
                  {{ location.email }}
                </a>
              </div>

              <!-- Delivery Info -->
              <div class="mt-4">
                <div class="flex items-center">
                  <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                  </svg>
                  <span class="ml-3 text-base font-medium">
                    {{ location.delivery ? 'Delivery Available' : 'Pickup Only' }}
                  </span>
                </div>
                <p v-if="location.deliveryAreas" class="mt-1 ml-8 text-sm text-gray-500">
                  Serving: {{ location.deliveryAreas }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps } from 'vue';

const props = defineProps({
  locations: {
    type: Array,
    required: true,
    default: () => []
  }
});
</script>

<style scoped>
/* Add any custom styles here if needed */
</style>