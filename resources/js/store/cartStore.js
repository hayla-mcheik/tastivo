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
            this.isLoading = true;
            try {
                await axios.get('/sanctum/csrf-cookie');
                
                try {
                    const response = await axios.get('/api/cart');
                    this.updateCartState(response.data);
                    this.isGuest = response.data.isGuest;
                } catch (error) {
                    if (error.response?.status === 401) {
                        const guestResponse = await axios.get('/cart/guest');
                        this.updateCartState(guestResponse.data);
                        this.isGuest = true;
                    } else {
                        throw error;
                    }
                }
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to initialize cart';
            } finally {
                this.isLoading = false;
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
                const endpoint = this.isAuthenticated ? `/cart/${itemId}` : `/cart/guest/${itemId}`;
                const response = await axios.put(endpoint, { 
                    quantity,
                    additions
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
        
        updateCartState(data) {
            this.items = data.items;
            this.count = data.count;
            this.total = data.total;
        }
    },
    getters: {
        formattedTotal: (state) => `$${state.total.toFixed(2)}`,
        hasItems: (state) => state.count > 0
    }
}); 