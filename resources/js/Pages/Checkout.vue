<script setup>
import { ref, computed, onMounted } from 'vue';
import { useCartStore } from '../store/cartStore';
import { storeToRefs } from 'pinia';

const cartStore = useCartStore();
const { items, total } = storeToRefs(cartStore);

// Checkout steps
const currentStep = ref(1);
const steps = [
  { id: 1, name: 'Address', completed: false },
  { id: 2, name: 'Location', completed: false },
  { id: 3, name: 'Summary', completed: false }
];

// Form data
const formData = ref({
  name: '',
  phone: '',
  address: '',
  location: { lat: null, lng: null },
  notes: ''
});

// Map variables
const map = ref(null);
const marker = ref(null);
const mapCenter = ref({ lat: 33.8938, lng: 35.5018 }); // Default to Beirut coordinates
const mapZoom = ref(15);
const mapLoaded = ref(false);

// WhatsApp integration
const whatsappNumber = '96178913139'; // Replace with your WhatsApp number

// Calculate delivery charge (2.99 or free over $50)
const deliveryCharge = computed(() => total.value > 50 ? 0 : 2.99);
const grandTotal = computed(() => total.value + deliveryCharge.value);

// Initialize Google Maps
const initMap = async () => {
  try {
    const { Map } = await google.maps.importLibrary("maps");
    const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");

    const mapElement = document.getElementById('map');
    if (!mapElement) return;

    map.value = new Map(mapElement, {
      center: mapCenter.value,
      zoom: mapZoom.value,
      mapTypeControl: false,
      streetViewControl: false,
      fullscreenControl: false,
      mapId: 'DEMO_MAP_ID' // Replace with your actual Map ID
    });

    // Add click listener to place marker
    map.value.addListener('click', (e) => {
      updateLocation(e.latLng.lat(), e.latLng.lng());
    });

    mapLoaded.value = true;
    
    // Initialize with default marker
    updateLocation(mapCenter.value.lat, mapCenter.value.lng);
  } catch (error) {
    console.error("Error initializing Google Maps:", error);
  }
};

const updateLocation = async (lat, lng) => {
  if (lat === null || lng === null) {
    formData.value.location = { lat: null, lng: null };
    if (marker.value) {
      marker.value.map = null;
      marker.value = null;
    }
    return;
  }

  formData.value.location = { lat, lng };
  mapCenter.value = { lat, lng };
  
  if (!map.value) return;

  try {
    const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");

    if (marker.value) {
      marker.value.position = { lat, lng };
    } else {
      marker.value = new AdvancedMarkerElement({
        position: { lat, lng },
        map: map.value,
        gmpDraggable: true
      });
      
      marker.value.addListener('dragend', (e) => {
        formData.value.location = {
          lat: e.latLng.lat(),
          lng: e.latLng.lng()
        };
      });
    }
    
    map.value.setCenter({ lat, lng });
  } catch (error) {
    console.error("Error updating location:", error);
  }
};

const confirmOrder = () => {
  let message = `*New Order Received!*\n\n`;
  message += `*Name:* ${formData.value.name}\n`;
  message += `*Mobile Number:* ${formData.value.phone}\n`;
  message += `*Address:* ${formData.value.address}\n`;
  
  if (formData.value.location.lat && formData.value.location.lng) {
    message += `*Map Location:* https://www.google.com/maps?q=${formData.value.location.lat},${formData.value.location.lng}\n\n`;
  }
  
  message += `*Order Items:*\n`;
  
  items.value.forEach((item, index) => {
    message += `${index + 1}. *${item.product.name}* - $${item.price} x ${item.quantity} = $${(item.price * item.quantity).toFixed(2)}\n`;
  });
  
  message += `\n*Sub Total:* $${total.value.toFixed(2)}\n`;
  message += `*Delivery Charge:* $${deliveryCharge.value.toFixed(2)}\n`;
  message += `*Total:* $${grandTotal.value.toFixed(2)}`;
  
  const encodedMessage = encodeURIComponent(message)
    .replace(/'/g, "%27")
    .replace(/"/g, "%22");
  
  window.open(`https://wa.me/${whatsappNumber}?text=${encodedMessage}`, '_blank');
  
  steps[2].completed = true;
  currentStep.value = 3;
};

const nextStep = () => {
  if (currentStep.value < steps.length) {
    steps[currentStep.value - 1].completed = true;
    currentStep.value++;
    
    if (currentStep.value === 2) {
      loadGoogleMaps();
    }
  }
};

const prevStep = () => {
  if (currentStep.value > 1) {
    currentStep.value--;
  }
};

// Load Google Maps script
const loadGoogleMaps = () => {
  if (window.google?.maps?.Map) {
    initMap();
    return;
  }

  const existingScript = document.querySelector('script[src*="maps.googleapis.com"]');
  if (existingScript) {
    existingScript.remove();
  }

  const script = document.createElement('script');
  script.src = `https://maps.googleapis.com/maps/api/js?key=AIzaSyDgOAfku4rbOW-Rk0HeS6PJrDMIGERmHsU&loading=async&libraries=marker&callback=initMap`;
  script.async = true;
  script.defer = true;
  script.onerror = () => {
    console.error("Failed to load Google Maps script");
  };
  document.head.appendChild(script);
  
  window.initMap = initMap;
};

const updateQuantity = async (itemId, newQuantity) => {
  if (newQuantity < 1 || newQuantity > 10) return;
  await cartStore.updateQuantity(itemId, newQuantity);
};

const removeItem = async (itemId) => {
  await cartStore.removeItem(itemId);
};

const isLoadingLocation = ref(false);

// Get user's current location
const getCurrentLocation = () => {
  if (!mapLoaded.value) {
    console.warn("Map not loaded yet");
    return;
  }

  isLoadingLocation.value = true;
  
  if (!navigator.geolocation) {
    console.warn("Geolocation not supported");
    isLoadingLocation.value = false;
    return;
  }

  navigator.geolocation.getCurrentPosition(
    (position) => {
      const pos = {
        lat: position.coords.latitude,
        lng: position.coords.longitude
      };
      updateLocation(pos.lat, pos.lng);
      isLoadingLocation.value = false;
    },
    (error) => {
      console.error("Geolocation error:", error);
      isLoadingLocation.value = false;
      alert("Could not get your location. Please select manually on the map.");
    },
    { 
      enableHighAccuracy: true,
      timeout: 10000,
      maximumAge: 0
    }
  );
};

onMounted(() => {
  if (currentStep.value === 2) {
    loadGoogleMaps();
  }
});
</script>

<template>
  <Head title="- Checkout" />
  <div class="max-w-2xl mx-auto p-4 mb-5 md:p-6">
    <!-- Progress Steps -->
    <ol class="flex items-center w-full mb-8">
      <li v-for="(step, index) in steps" :key="step.id" 
          class="flex items-center relative"
          :class="{
            'w-full': index < steps.length - 1,
          }">
        <div class="flex flex-col items-center">
          <span class="flex items-center justify-center w-8 h-8 rounded-full border-2 shrink-0"
                :class="{
                  'border-red-500 bg-red-600 text-white': currentStep === step.id,
                  'border-green-500 bg-green-500 text-white': currentStep > step.id,
                  'border-gray-300': currentStep < step.id
                }">
            <template v-if="currentStep > step.id">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
              </svg>
            </template>
            <template v-else>
              {{ step.id }}
            </template>
          </span>
          <span class="mt-2 text-sm font-medium"
                :class="{
                  'text-red-600': currentStep === step.id,
                  'text-green-500': currentStep > step.id,
                  'text-gray-500': currentStep < step.id
                }">
            {{ step.name }}
          </span>
        </div>
        <span v-if="index < steps.length - 1" 
              class="absolute top-4 left-8 w-full h-0.5 bg-gray-200 -z-10"></span>
      </li>
    </ol>

    <!-- Step 1: Address Information -->
    <div v-if="currentStep === 1" class="bg-white rounded-lg shadow-md p-6">
      <h2 class="text-2xl font-bold text-gray-800 mb-6">Delivery Address</h2>
      
      <form @submit.prevent="nextStep" class="space-y-4">
        <div>
          <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
          <input type="text" id="name" v-model="formData.name" required
                 class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">
        </div>
        
        <div>
  <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
  <div class="relative">
    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
      <span class="fi fi-lb rounded-sm"></span>
    </div>
    <input 
      type="tel" 
      id="phone" 
      v-model="formData.phone" 
      required
      class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500"
      placeholder="XX XXX XXX"
      pattern="[0-9]{7,8}"
    >
  </div>
</div>
        
        <div>
          <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Full Address *</label>
          <textarea id="address" v-model="formData.address" required rows="3"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500"
                    placeholder="Building, Street, Area, City"></textarea>
        </div>
        
        <div>
          <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Delivery Notes (Optional)</label>
          <textarea id="notes" v-model="formData.notes" rows="2"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500"
                    placeholder="Any special instructions for delivery"></textarea>
        </div>
        
        <div class="flex justify-between pt-4">
          <button type="button" @click="$router.back()" 
                  class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
            Cancel
          </button>
          <button type="submit" 
                  class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg transition-colors">
            Continue to Location
          </button>
        </div>
      </form>
    </div>

    <!-- Step 2: Location Selection -->
    <div v-else-if="currentStep === 2" class="bg-white rounded-lg shadow-md p-6">
      <h2 class="text-2xl font-bold text-gray-800 mb-6">Pin Your Location</h2>
      
      <div class="mb-6">
        <div id="map" class="h-96 w-full rounded-lg border border-gray-300"></div>
        <div class="mt-4 flex flex-wrap gap-2">
          <button @click="getCurrentLocation" 
                  class="px-4 py-2 bg-red-600 text-white rounded-lg flex items-center justify-center"
                  :disabled="isLoadingLocation || !mapLoaded">
            <svg v-if="isLoadingLocation" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>{{ isLoadingLocation ? 'Locating...' : 'Use Current Location' }}</span>
          </button>
          <button @click="updateLocation(null, null)" 
                  class="px-4 py-2 border border-gray-300 rounded-lg">
            Skip Location
          </button>
        </div>
      </div>
      
      <div class="bg-gray-50 p-4 rounded-lg mb-6">
        <h3 class="font-medium text-gray-800 mb-2">Address Confirmation</h3>
        <p class="text-gray-700">{{ formData.address }}</p>
        <p v-if="formData.location.lat" class="text-sm text-gray-500 mt-1">
          Location pinned: {{ formData.location.lat.toFixed(6) }}, {{ formData.location.lng.toFixed(6) }}
        </p>
        <p v-else class="text-sm text-gray-500 mt-1">
          No location selected
        </p>
      </div>
      
      <div class="flex justify-between">
        <button @click="prevStep" 
                class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
          Back
        </button>
        <button @click="nextStep" 
                class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg transition-colors">
          Continue to Summary
        </button>
      </div>
    </div>

    <!-- Step 3: Order Summary -->
    <div v-else class="bg-white rounded-lg shadow-md p-6">
      <h2 class="text-2xl font-bold text-gray-800 mb-6">Order Summary</h2>
      
      <div class="bg-gray-50 rounded-lg p-4 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-3">Delivery Information</h3>
        <div class="grid grid-cols-2 gap-y-2 mb-4">
          <div class="text-gray-600">Name:</div>
          <div class="font-medium">{{ formData.name }}</div>
          <div class="text-gray-600">Phone:</div>
          <div class="font-medium">{{ formData.phone }}</div>
          <div class="text-gray-600">Address:</div>
          <div class="font-medium">{{ formData.address }}</div>
          <div v-if="formData.location.lat" class="text-gray-600">Map Location:</div>
          <div v-if="formData.location.lat" class="font-medium">
            <a :href="`https://www.google.com/maps?q=${formData.location.lat},${formData.location.lng}`" 
               target="_blank" class="text-red-600 hover:underline">
              View on Map
            </a>
          </div>
          <div v-if="formData.notes" class="text-gray-600">Notes:</div>
          <div v-if="formData.notes" class="font-medium">{{ formData.notes }}</div>
        </div>
        
        <div class="border-t border-gray-200 pt-4">
          <h3 class="text-lg font-semibold text-gray-800 mb-3">Order Items</h3>
          <div v-for="item in items" :key="'confirm-'+item.id" class="flex justify-between py-2">
            <span>{{ item.quantity }}x {{ item.product.name }}</span>
            <span class="font-medium">${{ (item.price * item.quantity).toFixed(2) }}</span>
          </div>
        </div>
        
        <div class="border-t border-gray-200 pt-4 space-y-2">
          <div class="flex justify-between">
            <span>Subtotal:</span>
            <span class="font-medium">${{ total.toFixed(2) }}</span>
          </div>
          <div class="flex justify-between">
            <span>Delivery Fee:</span>
            <span class="font-medium">${{ deliveryCharge.toFixed(2) }}</span>
          </div>
          <div class="flex justify-between font-bold text-lg pt-2">
            <span>Total:</span>
            <span>${{ grandTotal.toFixed(2) }}</span>
          </div>
        </div>
      </div>
      
      <div class="flex justify-between">
        <button @click="prevStep" 
                class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
          Back
        </button>
        <button @click="confirmOrder" 
                class="flex items-center bg-green-600 hover:bg-green-700 text-white px-6 mx-2 py-2 rounded-lg transition-colors">
          Place Order via WhatsApp
          <svg class="w-5 h-5 ml-2" fill="currentColor" viewBox="0 0 24 24">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-6.29-3.588c.545 1.422 1.666 2.595 1.666 2.595-.406-.216-2.002-1.153-2.45-1.511l-.008.01zm7.147-5.273c0 .323.039.647.118.953.078.307.198.6.347.871a4.27 4.27 0 0 1-.347-.871 4.381 4.381 0 0 0-.118-.953c0-2.385-1.947-4.333-4.333-4.333a4.32 4.32 0 0 0-3.135 1.3 4.32 4.32 0 0 0-1.3 3.135c0 .779.198 1.518.545 2.19L4.6 16.357l3.917-1.3a4.276 4.276 0 0 0 2.19.545 4.36 4.36 0 0 0 3.135-1.3 4.36 4.36 0 0 0 1.3-3.135zm-1.3 0a3.04 3.04 0 0 1-.892 2.157 3.04 3.04 0 0 1-2.156.892 3.098 3.098 0 0 1-1.559-.413l-.112-.066-1.122.371.371-1.107-.07-.112a3.1 3.1 0 0 1-.413-1.559c0-.823.322-1.605.892-2.156a3.04 3.04 0 0 1 2.156-.892c.823 0 1.605.322 2.157.892.569.551.891 1.333.891 2.156zM12 2.167c5.338 0 9.667 4.329 9.667 9.667 0 5.338-4.329 9.667-9.667 9.667a9.632 9.632 0 0 1-4.917-1.35l-5.583 1.85 1.85-5.583A9.617 9.617 0 0 1 2.333 11.834c0-5.338 4.329-9.667 9.667-9.667zm0 1c-4.774 0-8.667 3.893-8.667 8.667 0 1.748.521 3.395 1.413 4.789l-1.038 3.136 3.136-1.038a8.66 8.66 0 0 0 4.789 1.413c4.774 0 8.667-3.893 8.667-8.667 0-4.774-3.893-8.667-8.667-8.667z"/>
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Remove number input arrows */
input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
input[type="number"] {
  -moz-appearance: textfield;
}

/* Map styling */
#map {
  min-height: 300px;
}

/* Progress step styling */
ol li {
  flex: 1;
  position: relative;
}

ol li:not(:last-child):after {
  content: '';
  position: absolute;
  width: calc(100% - 2rem);
  height: 2px;
  background: #e5e7eb;
  top: 1rem;
  left: 2rem;
  z-index: 0;
}
</style>