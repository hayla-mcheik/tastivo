<script setup>
import { Head, router, useForm } from '@inertiajs/vue3';
import PaginationLinks from '@/Components/PaginationLinks.vue';
import InputField from '@/Components/InputField.vue'; // Make sure this path is correct

const props = defineProps({ 
  users: Object,
  products: Object,
  searchTerm: String
});

const form = useForm({
  search: props.searchTerm || '',
});

const search = () => {
  router.get(route('products.index'), { // Changed from "home" to your products route
    search: form.search,
  });
};

const deleteProduct = (id) => {
if(confirm("Are you sure you want to delete this product?")){
    router.delete(route('products.destroy',id));
}
}
</script>
<template>
  <Head title="Admin Products" />

  <div>Products</div>
  <section class="bg-gray-50 dark:bg-gray-900 p-3 sm:p-5">
    <div class="mx-auto max-w-screen-xl px-4 lg:px-12">
      <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
        <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
          <div class="w-full md:w-1/2">
            <form @submit.prevent="search">
                <InputField
                    type="search"
                    label=""
                    icon="magnifying-glass"
                    placeholder="Search..."
                    v-model="form.search"
                />
            </form>
          </div>
          <div class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0">
            <button type="button" class="flex items-center justify-center text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800">
              <svg class="h-3.5 w-3.5 mr-2" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path clip-rule="evenodd" fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
              </svg>
              <Link href="/admin/products/create">
              Add product
              </Link>
            </button>
       
          </div>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
              <tr>
                <th scope="col" class="px-4 py-3">Product name</th>
                <th scope="col" class="px-4 py-3">Category</th>
                <th scope="col" class="px-4 py-3">Price</th>
                <th scope="col" class="px-4 py-3">Quantity</th>
                <th scope="col" class="px-4 py-3">Rate</th>
                <th scope="col" class="px-4 py-3">
                  <span class="sr-only">Actions</span>
                </th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="product in products.data" :key="product.id" class="border-b dark:border-gray-700">
                <td class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ product.name }}</td>
                <td class="px-4 py-3">{{ product.category.name }}</td>
                <td class="px-4 py-3">${{ product.price }}</td>
                <td class="px-4 py-3">{{ product.quantity }}</td>
                <td class="px-4 py-3">{{ product.rate }}</td>
                <td class="px-4 py-3 flex items-center justify-end">
                  <button :id="`${product.id}-dropdown-button`" :data-dropdown-toggle="`${product.id}-dropdown`" class="inline-flex items-center p-0.5 text-sm font-medium text-center text-gray-500 hover:text-gray-800 rounded-lg focus:outline-none dark:text-gray-400 dark:hover:text-gray-100" type="button">
                    <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                      <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                    </svg>
                  </button>
                  <div :id="`${product.id}-dropdown`" class="hidden z-10 w-44 bg-white rounded divide-y divide-gray-100 shadow dark:bg-gray-700 dark:divide-gray-600">
                    <ul class="py-1 text-sm text-gray-700 dark:text-gray-200" :aria-labelledby="`${product.id}-dropdown-button`">
                      <li>
                        <a href="#" class="block py-2 px-4 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Show</a>
                      </li>
                      <li>
                        <a :href="route('products.edit' , product.id)" class="block py-2 px-4 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Edit</a>
                      </li>
                    </ul>
                    <div class="py-1">
  <button 
    @click.prevent="deleteProduct(product.id)" 
    class="block w-full text-left py-2 px-4 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white"
  >
    Delete
  </button>
</div>

                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="mt-8">
          <PaginationLinks :paginator="products" />
        </div>
      </div>
    </div>
  </section>
</template>
<style scope>
.bg-primary-700{
  background-color: blue;
}
</style>