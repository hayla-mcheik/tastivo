<template>
    <AdminLayout>
      <div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
          <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
              Site Settings
            </h3>
          </div>
  
          <form @submit.prevent="submit" class="p-6 space-y-6" enctype="multipart/form-data">
            <div>
              <label for="site_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Site Name
              </label>
              <input
                v-model="form.site_name"
                type="text"
                id="site_name"
                name="site_name"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              >
            </div>
  
            <div>
              <label for="site_logo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Site Logo
              </label>
              <input
               @change="form.site_logo = $event.target.files[0]"
                type="file"
                id="site_logo"
                name="site_logo"
                accept="image/*"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              >
  
              <!-- Optional: Preview uploaded logo -->
              <div v-if="previewUrl" class="mt-4">
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-2">Logo Preview:</p>
                <img :src="previewUrl" alt="Site Logo Preview" class="h-20">
              </div>
            </div>
  
            <div class="pt-4">
              <button
                type="submit"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                Save Settings
              </button>
            </div>
          </form>
        </div>
      </div>
    </AdminLayout>
  </template>
  
  <script setup>
  import { useForm } from '@inertiajs/vue3';
  import { ref } from 'vue';
  
  const props = defineProps({
    settings: Object,
  });
  
  const previewUrl = ref(props.settings.site_logo || null);
  
  const form = useForm({
    site_name: props.settings.site_name || '',
    site_logo: null,
  });
  
  const handleFileUpload = (e) => {
    const file = e.target.files[0];
    form.site_logo_file = file;
    if (file) {
      previewUrl.value = URL.createObjectURL(file);
    }
  };
  
  const submit = () => {
    form.post(route('admin.settings.update'), {
      preserveScroll: true,
      forceFormData: true,
      onSuccess: () => {
        form.reset('site_logo');
      },
    });
  };
  </script>
  