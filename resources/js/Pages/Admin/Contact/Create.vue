<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useForm } from '@inertiajs/vue3';
import ErrorMessages from '../../../Components/ErrorMessages.vue';

const days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

const form = useForm({
    name: '',
    image: null,
    address: '',
    opening_hours: days.reduce((acc, day) => ({ ...acc, [day]: '' }), {}),
    phone: '',
    email: '',
    delivery_available: false,
    delivery_areas: '',
    is_open: true,
});

const props = defineProps({
    errors: Object,
});

const submit = () => {
    // Convert form data to FormData for file upload
    const formData = new FormData();
    formData.append('name', form.name);
    formData.append('image', form.image);
    formData.append('address', form.address);
    formData.append('opening_hours', JSON.stringify(form.opening_hours));
    formData.append('phone', form.phone);
    formData.append('email', form.email);
    formData.append('delivery_available', form.delivery_available);
    formData.append('delivery_areas', form.delivery_areas);
    formData.append('is_open', form.is_open);

    // Send as multipart form data
    form.post(route('admin.contact.store'), {
        data: formData,
        forceFormData: true,
        preserveScroll: true,
    });
};
</script>
<template>
      <div class="px-4 sm:px-6 lg:px-8 py-8">
        <div class="max-w-3xl mx-auto">
          <div class="mb-8">
            <h1 class="text-xl font-semibold text-gray-900">Add New Location</h1>
            <p class="mt-2 text-sm text-gray-700">
              Fill in the details below to add a new restaurant location.
            </p>
          </div>
  
          <ErrorMessages :errors="form.errors" />
  
          <form @submit.prevent="submit">
            <div class="shadow sm:rounded-md sm:overflow-hidden">
              <div class="bg-white py-6 px-4 space-y-6 sm:p-6">
                <div class="grid grid-cols-6 gap-6">
                  <div class="col-span-6">
                    <label for="name" class="block text-sm font-medium text-gray-700">Location Name</label>
                    <input
                      type="text"
                      v-model="form.name"
                      id="name"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    />
                    <p v-if="errors.name" class="mt-2 text-sm text-red-600">{{ errors.name }}</p>
                  </div>
  
                  <div class="w-full">
            <label for="image" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Restaurant Image</label>
            <input type="file" @input="form.image = $event.target.files[0]" id="image" accept="image/*" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
          </div>
  
                  <div class="col-span-6">
                    <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                    <textarea
                      v-model="form.address"
                      id="address"
                      rows="3"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    ></textarea>
                    <p v-if="errors.address" class="mt-2 text-sm text-red-600">{{ errors.address }}</p>
                  </div>
  
                  <div class="col-span-6">
                    <label class="block text-sm font-medium text-gray-700">Opening Hours</label>
                    <div class="mt-1 grid grid-cols-2 gap-4">
                      <div v-for="(day, index) in days" :key="index" class="flex items-center">
                        <label class="w-24 text-sm text-gray-700">{{ day }}:</label>
                        <input
                          type="text"
                          v-model="form.opening_hours[day]"
                          class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                          placeholder="9:00 AM - 10:00 PM"
                        />
                      </div>
                    </div>
                    <p v-if="errors.opening_hours" class="mt-2 text-sm text-red-600">{{ errors.opening_hours }}</p>
                  </div>
  
                  <div class="col-span-6 sm:col-span-3">
                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                    <input
                      type="text"
                      v-model="form.phone"
                      id="phone"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    />
                    <p v-if="errors.phone" class="mt-2 text-sm text-red-600">{{ errors.phone }}</p>
                  </div>
  
                  <div class="col-span-6 sm:col-span-3">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input
                      type="email"
                      v-model="form.email"
                      id="email"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    />
                    <p v-if="errors.email" class="mt-2 text-sm text-red-600">{{ errors.email }}</p>
                  </div>
  
                  <div class="col-span-6 sm:col-span-3">
                    <div class="flex items-center">
                      <input
                        type="checkbox"
                        v-model="form.delivery_available"
                        id="delivery_available"
                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                      />
                      <label for="delivery_available" class="ml-2 block text-sm text-gray-700">Delivery Available</label>
                    </div>
                  </div>
  
                  <div class="col-span-6 sm:col-span-3">
                    <div class="flex items-center">
                      <input
                        type="checkbox"
                        v-model="form.is_open"
                        id="is_open"
                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                      />
                      <label for="is_open" class="ml-2 block text-sm text-gray-700">Currently Open</label>
                    </div>
                  </div>
  
                  <div v-if="form.delivery_available" class="col-span-6">
                    <label for="delivery_areas" class="block text-sm font-medium text-gray-700">Delivery Areas</label>
                    <input
                      type="text"
                      v-model="form.delivery_areas"
                      id="delivery_areas"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                      placeholder="Comma separated areas"
                    />
                    <p v-if="errors.delivery_areas" class="mt-2 text-sm text-red-600">{{ errors.delivery_areas }}</p>
                  </div>
                </div>
              </div>
              <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                <button
                  type="button"
                  @click="$inertia.visit(route('admin.contact.index'))"
                  class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  class="ml-3 inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                  Save
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    
  </template>
  
