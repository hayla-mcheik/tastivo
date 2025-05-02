<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { useCartStore } from '../store/cartStore';
import { storeToRefs } from 'pinia';
import { Link } from '@inertiajs/vue3';

const cartStore = useCartStore();
const { items, total, isLoading, isGuest } = storeToRefs(cartStore);

// Exchange rate for LBP
const exchangeRate = 90000;

// Animation values
const animatedTotal = ref(0);
const animatedSubtotal = ref(0);
const animationDuration = 500; // ms
const allAdditions = ref([])

const fetchAdditions = async () => {
  try {
    const response = await axios.get('/api/additions')
    allAdditions.value = response.data
  } catch (error) {
    console.error('Error fetching additions:', error)
  }
}

// Initialize cart and animated values
onMounted(() => {
  cartStore.initialize();
  animatedTotal.value = total.value;
  animatedSubtotal.value = total.value;
});

// Watch for changes in total and animate
watch(total, (newVal, oldVal) => {
  animateValue(oldVal, newVal, animatedSubtotal);
  animateValue(oldVal, newVal, animatedTotal);
});

// Calculate shipping (free over $50)
const shippingFee = computed(() => total.value > 50 ? 0 : 2.99);
const grandTotal = computed(() => total.value + shippingFee.value);
const lbpTotal = computed(() => (grandTotal.value * exchangeRate).toLocaleString('en-US'));

const animateValue = (start, end, target) => {
  const startTime = performance.now();
  const step = (timestamp) => {
    const progress = Math.min((timestamp - startTime) / animationDuration, 1);
    const value = start + (end - start) * progress;
    target.value = parseFloat(value.toFixed(2));
    if (progress < 1) {
      requestAnimationFrame(step);
    }
  };
  requestAnimationFrame(step);
};

const updateQuantity = async (itemId, newQuantity) => {
  if (newQuantity < 1 || newQuantity > 10) return;
  
  // Find the current item to get its additions
  const currentItem = items.value.find(item => item.id === itemId);
  const additions = currentItem?.additions || currentItem?.product?.additions || [];
  
  await cartStore.updateQuantity(itemId, newQuantity, additions);
};

const removeItem = async (itemId) => {
  await cartStore.removeItem(itemId);
};

const getItemTotalWithAdditions = (item) => {
  const base = item.price * item.quantity;
  
  // Check both item.additions and item.product.additions
  const additions = item.additions || item.product?.additions || [];
  
  const additionsTotal = additions.reduce((sum, addition) => {
    // Handle both full addition objects and just IDs
    const price = addition.price || 
                 (typeof addition === 'number' 
                  ? allAdditions.value.find(a => a.id === addition)?.price 
                  : 0);
    return sum + (parseFloat(price) * item.quantity);
  }, 0);
  
  return base + additionsTotal;
};


// In your component
const clearCart = async () => {
  if (!items.value.length) {
    toast.info('Your cart is already empty', {
      position: 'bottom-right',
      autoClose: 3000,
    });
    return;
  }

  if (confirm('Are you sure you want to clear your cart?')) {
    const result = await cartStore.clearCart();
    
    if (result.success) {

    } else {
console.log(result.message);
    }
  }
};

const applyPromoCode = () => {
  alert('Promo code functionality would be implemented here');
};
const getFullAdditions = (item) => {
  if (!item.additions) return []
  return allAdditions.value.filter(add => item.product.additions.includes(add.id))
}

</script>

<template>
  <div class="cart-container">
    <!-- Loading State -->
    <div v-if="isLoading" class="loading-overlay">
      <div class="loading-spinner"></div>
    </div>

    <!-- Empty State -->
    <div v-else-if="items.length === 0" class="empty-cart">
      <div class="empty-icon">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
      </div>
      <p class="empty-text">Your cart is empty</p>
      <Link href="/" class="continue-shopping">
        Continue Shopping
      </Link>
    </div>

    <!-- Cart Content -->
    <div v-else>
      <div class="cart-header">
        <h1>Your Order</h1>
        <!-- <button @click="clearCart" class="clear-cart-btn">Clear Cart</button> -->
      </div>

      <!-- Cart Items -->
      <div class="cart-items">
        <div v-for="item in items" :key="item.id" class="cart-item">
          <div class="item-image">
            <img :src="'/storage/' + item.product.image" :alt="item.product.name">
          </div>
          <div class="item-details">
            <h3 class="item-name">{{ item.product.name }}</h3>
            
            <!-- Display additions if any -->
            <div v-if="item.product.additions && item.product.additions.length > 0" class="item-additions">
  <div
    v-for="addition in item.product.additions"
    :key="addition.id"
    class="addition-item"
  >
    <span class="addition-name">+ {{ addition.name }}</span>
    <span class="addition-price">
      +${{ (parseFloat(addition.price) * item.quantity).toFixed(2) }}
    </span>
  </div>
</div>

            
            <div class="item-controls">
              <div class="quantity-controls">
                <button @click="updateQuantity(item.id, item.quantity - 1)" 
                        :disabled="item.quantity <= 1"
                        class="quantity-btn">
                  -
                </button>
                <input type="number" 
                       v-model.number="item.quantity"
                       @change="updateQuantity(item.id, item.quantity)"
                       min="1" max="10"
                       class="quantity-input">
                <button @click="updateQuantity(item.id, item.quantity + 1)"
                        :disabled="item.quantity >= 10"
                        class="quantity-btn">
                  +
                </button>
              </div>
              <button @click="removeItem(item.id)" class="remove-btn">
                Remove
              </button>
            </div>
          </div>
          <div class="item-price">
  <span class="usd-price">
    ${{ getItemTotalWithAdditions(item).toFixed(2) }}
  </span>
  <span class="lbp-price">
    LBP {{ (getItemTotalWithAdditions(item) * exchangeRate).toLocaleString('en-US') }}
  </span>
</div>

        </div>
      </div>

      <!-- Promo Code Section -->
      <div class="promo-section">
        <button @click="applyPromoCode" class="promo-btn">
          <span>+</span> Add Promo Code
        </button>
      </div>

      <!-- Order Summary -->
      <div class="order-summary">
        <div class="summary-row">
          <span>Subtotal</span>
          <span>${{ animatedSubtotal.toFixed(2) }}</span>
        </div>
        <div class="summary-row">
          <span>Shipping</span>
          <span>{{ shippingFee === 0 ? 'FREE' : `$${shippingFee.toFixed(2)}` }}</span>
        </div>
        <div class="divider"></div>
        <div class="total-row">
          <span>TOTAL</span>
          <div class="total-amount">
            <span class="usd-total">USD {{ (animatedTotal + shippingFee).toFixed(2) }}</span>
            <span class="lbp-total">LBP {{ ((animatedTotal + shippingFee) * exchangeRate).toLocaleString('en-US') }}</span>
          </div>
        </div>
      </div>

      <!-- Checkout Button -->
      <button class="checkout-btn">
        <Link href="/checkout">
          PROCEED TO CHECKOUT
        </Link>
      </button>

      <!-- Guest Notice -->
      <div v-if="isGuest" class="guest-notice">
        <p>Your cart will be saved for this session. <Link href="/login">Log in</Link> to save your cart permanently.</p>
      </div>

      <!-- Continue Shopping -->
      <Link href="/" class="continue-shopping-link">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Continue Shopping
      </Link>
    </div>
  </div>
</template>

<style scoped>
.cart-container {
  margin: 0 auto;
  padding: 20px;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.loading-overlay {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 200px;
}

.loading-spinner {
  border: 4px solid rgba(0, 0, 0, 0.1);
  border-radius: 50%;
  border-top: 4px solid #3498db;
  width: 40px;
  height: 40px;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.empty-cart {
  text-align: center;
  padding: 40px 0;
}

.empty-icon {
  font-size: 60px;
  color: #ccc;
  margin-bottom: 20px;
}

.empty-text {
  font-size: 18px;
  color: #666;
  margin-bottom: 20px;
}

.continue-shopping {
  display: inline-block;
  padding: 10px 20px;
  background-color: #000;
  color: white;
  border-radius: 4px;
  text-decoration: none;
  transition: background-color 0.3s;
}

.continue-shopping:hover {
  background-color: rgb(220 38 38);
}

.cart-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.cart-header h1 {
  font-size: 24px;
  font-weight: 600;
  color: #333;
}

.clear-cart-btn {
  background: none;
  border: none;
  color: rgb(220 38 38);
  cursor: pointer;
  font-size: 14px;
}

.clear-cart-btn:hover {
  text-decoration: underline;
}

.cart-items {
  margin-bottom: 20px;
}

.cart-item {
  display: flex;
  padding: 15px 0;
  border-bottom: 1px solid #eee;
  gap: 15px;
}

.item-image {
  width: 80px;
  height: 80px;
  flex-shrink: 0;
}

.item-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 4px;
}

.item-details {
  flex-grow: 1;
}

.item-name {
  font-weight: 500;
  margin-bottom: 10px;
}

.item-additions {
  margin: 8px 0;
}

.addition-item {
  display: flex;
  justify-content: space-between;
  font-size: 14px;
  color: #666;
  margin-bottom: 4px;
}

.item-controls {
  display: flex;
  gap: 15px;
  margin-top: 10px;
}

.quantity-controls {
  display: flex;
  align-items: center;
}

.quantity-btn {
  width: 30px;
  height: 30px;
  background-color: #f5f5f5;
  border: none;
  border-radius: 4px;
  font-size: 16px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}

.quantity-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.quantity-input {
  width: 40px;
  height: 30px;
  text-align: center;
  margin: 0 5px;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.remove-btn {
  background: none;
  border: none;
  color: rgb(220 38 38);
  cursor: pointer;
  font-size: 14px;
}

.remove-btn:hover {
  text-decoration: underline;
}

.item-price {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  min-width: 100px;
}

.usd-price {
  font-weight: 600;
}

.lbp-price {
  font-size: 12px;
  color: #777;
}

.promo-section {
  margin: 20px 0;
}

.promo-btn {
  width: 100%;
  padding: 12px;
  background: none;
  border: 1px dashed #aaa;
  border-radius: 4px;
  color: #666;
  font-size: 14px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}

.promo-btn:hover {
  border-color: #666;
  color: #333;
}

.promo-btn span {
  margin-right: 5px;
  font-size: 18px;
}

.order-summary {
  background-color: #f9f9f9;
  padding: 20px;
  border-radius: 8px;
  margin-bottom: 20px;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 10px;
  font-size: 15px;
}

.divider {
  height: 1px;
  background-color: #ddd;
  margin: 15px 0;
}

.total-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.total-amount {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
}

.usd-total {
  font-weight: 700;
  font-size: 18px;
}

.lbp-total {
  font-size: 14px;
  color: #666;
}

.checkout-btn {
  width: 100%;
  padding: 15px;
  background-color: #000;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 16px;
  font-weight: bold;
  cursor: pointer;
  margin-bottom: 20px;
  transition: background-color 0.3s;
}

.checkout-btn:hover {
  background-color: #45a049;
}

.checkout-btn a {
  color: white;
  text-decoration: none;
  display: block;
}

.guest-notice {
  text-align: center;
  color: #666;
  margin-bottom: 20px;
  font-size: 14px;
}

.guest-notice a {
  color: #3498db;
  text-decoration: none;
}

.guest-notice a:hover {
  text-decoration: underline;
}

.continue-shopping-link {
  display: flex;
  align-items: center;
  justify-content: center;
  color: #3498db;
  text-decoration: none;
  gap: 5px;
  font-size: 14px;
}

.continue-shopping-link:hover {
  text-decoration: underline;
}

input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

input[type="number"] {
  -moz-appearance: textfield;
}
</style>