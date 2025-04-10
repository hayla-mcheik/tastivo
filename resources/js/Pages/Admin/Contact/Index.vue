<template>

      <div class="px-4 sm:px-6 lg:px-8 py-8">
        <div class="sm:flex sm:items-center">
          <div class="sm:flex-auto">
            <h1 class="text-xl font-semibold text-gray-900">Restaurant Locations</h1>
            <p class="mt-2 text-sm text-gray-700">
              A list of all your restaurant locations including their contact information and opening hours.
            </p>
          </div>
          <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <button 
              @click="$inertia.visit(route('admin.contact.create'))"
              type="button"
              class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto"
            >
              Add location
            </button>
          </div>
        </div>
  
        <!-- Success Message -->
        <!-- <div v-if="$page.props.flash.success" class="rounded-md bg-green-50 p-4 mt-4">
          <div class="flex">
            <div class="flex-shrink-0">
              <CheckCircleIcon class="h-5 w-5 text-green-400" aria-hidden="true" />
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-green-800">{{ $page.props.flash.success }}</p>
            </div>
          </div>
        </div> -->
  
        <div class="mt-8 flex flex-col">
          <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
              <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                <table class="min-w-full divide-y divide-gray-300">
                  <thead class="bg-gray-50">
                    <tr>
                      <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Name</th>
                      <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Address</th>
                      <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Phone</th>
                      <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                      <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Delivery</th>
                      <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                        <span class="sr-only">Edit</span>
                      </th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-200 bg-white">
                    <tr v-for="location in locations" :key="location.id">
                      <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                        {{ location.name }}
                      </td>
                      <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ location.address }}</td>
                      <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ location.phone }}</td>
                      <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                        <span :class="[location.is_open ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800', 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium']">
                          {{ location.is_open ? 'Open' : 'Closed' }}
                        </span>
                      </td>
                      <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                        <span :class="[location.delivery_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800', 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium']">
                          {{ location.delivery_available ? 'Available' : 'Not Available' }}
                        </span>
                      </td>
                      <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                        <button 
                          @click="$inertia.visit(route('admin.contact.edit', location.id))"
                          class="text-indigo-600 hover:text-indigo-900 mr-4"
                        >
                          Edit
                        </button>
                        <button 
                          @click="confirmDelete(location)"
                          class="text-red-600 hover:text-red-900"
                        >
                          Delete
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
  
      <!-- Delete Confirmation Modal -->

  </template>
  
  <script setup>
  import AdminLayout from '@/Layouts/AdminLayout.vue';
  import { ref } from 'vue';
  import { router } from '@inertiajs/vue3';
  
  const props = defineProps({
    locations: Array,
  });
  
  const showDeleteModal = ref(false);
  const locationToDelete = ref(null);
  
  const confirmDelete = (location) => {
    locationToDelete.value = location;
    showDeleteModal.value = true;
  };
  
  const deleteLocation = () => {
    router.delete(route('admin.contact.destroy', locationToDelete.value.id), {
      onSuccess: () => {
        showDeleteModal.value = false;
      },
    });
  };
  </script>