<script setup>
import { useForm } from '@inertiajs/vue3';
import ErrorMessages from '../../../Components/ErrorMessages.vue';

const form = useForm({
  name: '',
  slug: '',
  status: false,
  image: null,
});

const submit = () => {
  // Create FormData object for file upload
  const formData = new FormData();
  formData.append('name', form.name);
  formData.append('slug', form.slug);
  formData.append('status', form.status ? 1 : 0);
  if (form.image) {
    formData.append('image', form.image);
  }

  // Use transform to send as FormData
  form.transform((data) => ({
    ...data,
    image: form.image,
  })).post(route('categories.store'), {
    preserveScroll: true,
    onSuccess: () => form.reset(),
    forceFormData: true, // This ensures proper file upload
  });
}
</script>
<template>
  <section class="bg-white dark:bg-gray-900">
    <div class="py-8 px-4 mx-auto max-w-2xl lg:py-16">
      <h2 class="mb-4 text-xl font-bold text-gray-900 dark:text-white">Add a new category</h2>

      <ErrorMessages :errors="form.errors" />
      <form @submit.prevent="submit">
        
        <div class="grid gap-4 sm:grid-cols-2 sm:gap-6">
          
          <!-- Category Name -->
          <div class="sm:col-span-2">
            <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Category Name</label>
            <input type="text" v-model="form.name" id="name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="Type product name" required>
          </div>

          <div class="w-full">
            <label for="slug" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Slug</label>
            <input type="text" v-model="form.slug" id="rate" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="5" min="1" max="5">
          </div>



          <!-- Image Upload -->
          <div class="w-full">
            <label for="image" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Category Image</label>
            <input type="file" @input="form.image = $event.target.files[0]" id="image" accept="image/*" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
          </div>

          
          <!-- Status -->
          <div class="w-full">
            <label for="status" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status</label>
            <label class="inline-flex items-center cursor-pointer">
              <input type="checkbox" v-model="form.status" id="status" class="sr-only peer">
              <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-1 after:left-1 after:bg-white after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600"></div>
              <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">Active</span>
            </label>
          </div>


          
        </div>

        <!-- Submit Button -->
        <button :disabled="form.processing" type="submit" class="inline-flex items-center px-5 py-2.5 mt-4 sm:mt-6 text-sm font-medium text-center text-white bg-slate-700 rounded-lg focus:ring-4 focus:ring-primary-200 dark:focus:ring-primary-900 hover:bg-primary-800">

          <span>Add category</span>
        </button>
      </form>
    </div>
  </section>
</template>