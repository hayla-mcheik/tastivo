<template>
    <footer class="hidden md:block p-4 w-[40%] mx-auto z-9999 relative">
      <div class="container mx-auto px-4 flex justify-between items-center ">
        <div class="flex space-x-6">
          <button 
            @click="showTerms = true"
            class="text-gray-600 text-sm hover:text-gray-800 transition-colors"
          >
            Terms & Conditions
          </button>

          
          <!-- Language Toggle -->
          <div class="toggle-language flex items-center gap-2">
                            <a 
                                v-for="lang in availableLocales" 
                                :key="lang.code"
                                href="#" 
                                @click.prevent="changeLanguage(lang.code)" 
                                class="language-option"
                                :class="{ 'active': locale === lang.code }">
                                <span :class="`fi fi-${lang.flag} text-sm`"></span>
                            </a>
                        </div>
        </div>
        <div class="text-gray-500 text-sm">
          Powered by Tastivo
        </div>
      </div>
  
      <!-- Terms Modal -->
      <div v-if="showTerms" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white  p-6 max-w-2xl w-full mx-4">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold">Terms & Conditions</h3>
            <button @click="showTerms = false" class="text-gray-500 hover:text-gray-700">
              ✕
            </button>
          </div>
          <div class="prose">
            <!-- Add your terms content here -->
            <p>Your terms and conditions content goes here...</p>
          </div>
        </div>
      </div>
  
    </footer>
  </template>
  
  <script setup>
  import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';
  
  const showTerms = ref(false);
  const showPrivacy = ref(false);
  
const { t, locale } = useI18n();
const currentRoute = computed(() => usePage().url);
// Available languages
const availableLocales = [
    { code: 'en-US', name: 'English', flag: 'gb' },
    { code: 'fr-FR', name: 'Français', flag: 'fr' },
    { code: 'ar-AR', name: 'العربية', flag: 'ae' }
];

// Change language function
const changeLanguage = (langCode) => {
    locale.value = langCode;
    localStorage.setItem('locale', langCode);
    document.documentElement.lang = langCode;
    
    // Set direction for RTL languages (like Arabic)
    if (langCode === 'ar-AR') {
        document.documentElement.dir = 'rtl';
    } else {
        document.documentElement.dir = 'ltr';
    }
};

// Initialize on component mount
const initializeLanguage = () => {
    const savedLocale = localStorage.getItem('locale') || 'en-US';
    locale.value = savedLocale;
    document.documentElement.lang = savedLocale;
    
    if (savedLocale === 'ar-AR') {
        document.documentElement.dir = 'rtl';
    }
};

  </script>
  
  <style scoped>
 
  .modal-enter-active,
  .modal-leave-active {
    transition: opacity 0.3s;
  }
  .modal-enter-from,
  .modal-leave-to {
    opacity: 0;
  }
  </style>
