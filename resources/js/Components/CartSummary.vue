<script setup>
import { computed } from 'vue';
import { useCartStore } from '../store/cartStore';
import { storeToRefs } from 'pinia';
import { Link } from '@inertiajs/vue3';

const cartStore = useCartStore();
const { items, total } = storeToRefs(cartStore);
</script>

<template>
  <div class="bg-white rounded-lg shadow p-4 sticky top-4">
    <h3 class="font-bold text-lg mb-4">Your Order</h3>
    
    <!-- Empty state -->
    <div v-if="items.length === 0" class="text-center py-8">
      <svg class="w-50 h-80 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
      </svg>
      <p class="mt-2 text-gray-600">Your cart is empty</p>
 
    </div>

    <!-- With items -->
    <div v-else>
      <div class="space-y-4">
        <div v-for="item in items" :key="item.id" class="flex justify-between border-b pb-2">
          <div>
            <p class="font-medium">{{ item.product.name }}</p>
            <p class="text-sm text-gray-500">Qty: {{ item.quantity }}</p>
          </div>
          <div class="text-right">
            <p>${{ (item.price * item.quantity).toFixed(2) }}</p>
          </div>
        </div>
      </div>

      <div class="mt-4 pt-4 border-t">
        <div class="flex justify-between font-bold">
          <span>Total:</span>
          <span>${{ total.toFixed(2) }}</span>
        </div>
        <Link href="/checkout" class="mt-4 block w-full bg-red-600 text-white text-center py-2 rounded hover:bg-red-700">
          Checkout
        </Link>
      </div>
    </div>
  </div>
</template>

<style scoped>
.accordion-item {
  border-bottom: 1px solid #eee;
}
.accordion-header {
  transition: all 0.2s;
}
</style>