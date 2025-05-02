<script setup>
import { onMounted } from 'vue'
import { initFlowbite } from 'flowbite'
import AdminNavbar from './AdminNavbar.vue';
import AdminSidebar from './AdminSidebar.vue';

// initialize components based on data attribute selectors
onMounted(() => {
    initFlowbite();
})
</script>
<template>
<div class="antialiased bg-gray-50 dark:bg-gray-900">
<AdminNavbar />
    <!-- Sidebar -->
<AdminSidebar />


    <main class="md:ml-40 h-auto pt-10 pl-5">
<slot />
    </main>
  </div>
</template>



