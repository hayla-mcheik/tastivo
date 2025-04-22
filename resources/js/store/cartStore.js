import { defineStore } from 'pinia';
import axios from 'axios';

export const useCartStore = defineStore('cart', {
    state: () => ({
        items: [],
        count: 0,
        total: 0,
        isLoading: false,
        error: null,
        isAuthenticated: false,
        isGuest: true
    }),
    actions: {
        async initialize() {
            // First check localStorage for guest cart
            const localCart = localStorage.getItem('guestCart');
            if (localCart) {
                try {
                    const parsed = JSON.parse(localCart);
                    this.updateCartState(parsed);
                } catch (e) {
                    console.error('Failed to parse local cart', e);
                }
            }

            try {
                await axios.get('/sanctum/csrf-cookie');
                
                try {
                    // Try authenticated user first
                    const response = await axios.get('/api/cart');
                    this.updateCartState(response.data);
                    this.isAuthenticated = true;
                    this.isGuest = false;
                    // Clear guest cart if now authenticated
                    localStorage.removeItem('guestCart');
                } catch (error) {
                    if (error.response?.status === 401) {
                        // Fall back to guest cart
                        const guestResponse = await axios.get('/cart/guest');
                        this.updateCartState(guestResponse.data);
                        this.isAuthenticated = false;
                        this.isGuest = true;
                    } else {
                        throw error;
                    }
                }
            } catch (error) {
                console.error('Cart init error:', error);
                this.error = error.response?.data?.message || 'Failed to load cart';
                // Maintain existing cart state if available
            } finally {
                this.isLoading = false;
                this.initialized = true;
            }
        },
        async addToCart(productId, quantity = 1, additions = []) {
            this.isLoading = true;
            try {
                const endpoint = this.isGuest ? '/cart/guest' : '/api/cart';
                const response = await axios.post(endpoint, {
                    product_id: productId,
                    quantity: quantity,
                    additions: additions
                });
                this.updateCartState(response.data);
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to add to cart';
                throw error;
            } finally {
                this.isLoading = false;
            }
        },
        

        async updateQuantity(itemId, quantity, additions = []) {
            try {
              const endpoint = this.isAuthenticated ? `/api/cart/${itemId}` : `/cart/guest/${itemId}`;
              const response = await axios.put(endpoint, { 
                quantity,
                additions: additions.map(a => a.id || a) // Send just IDs
              });
              this.updateCartState(response.data);
            } catch (error) {
              this.error = error.response?.data?.message || 'Failed to update quantity';
              throw error;
            }
          },
        async removeItem(itemId) {
            try {
                const endpoint = this.isAuthenticated ? `/api/cart/${itemId}` : `/cart/guest/${itemId}`;
                const response = await axios.delete(endpoint);
                this.updateCartState(response.data);
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to remove item';
                throw error;
            }
        },

        async clearCart() {
            try {
                const endpoint = this.isAuthenticated ? '/api/cart/clear' : '/cart/guest/clear';
                const response = await axios.delete(endpoint);
                this.updateCartState(response.data);
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to remove item';
                throw error;
            }
        },
 
     updateCartState(data) {
            this.items = data.items || [];
            this.count = data.count || 0;
            this.total = data.total || 0;
            
            // Persist guest cart to localStorage
            if (this.isGuest) {
                console.log(this.total);
                localStorage.setItem('guestCart', JSON.stringify({
                    items: this.items,
                    count: this.count,
                    total: this.total,
                    updatedAt: Date.now()
                }));
            }
        },

    },
    getters: {
        formattedTotal: (state) => `$${state.total.toFixed(2)}`,
        hasItems: (state) => state.count > 0
    }
}); 