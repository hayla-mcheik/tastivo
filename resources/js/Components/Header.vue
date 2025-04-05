<template>
    <div class="mobile-menu-container">
      <!-- Hamburger Button -->
      <button 
        @click="toggleMenu"
        class="hamburger-button"
        :class="{ 'is-active': isOpen }"
        aria-label="Toggle menu"
      >
        <span class="hamburger-box">
          <span class="hamburger-inner"></span>
        </span>
      </button>
  
      <!-- Menu Overlay -->
      <transition name="menu">
        <div v-if="isOpen" class="menu-overlay" @click="closeMenu">
          <!-- Navigation Links -->
          <nav class="menu-content" @click.stop>
            <transition-group name="list" tag="ul">
              <li v-for="(item, index) in menuItems" :key="item.text" :style="`transition-delay: ${index * 0.1}s`">
                <a 
                  :href="item.link" 
                  class="menu-link"
                  @click="closeMenu"
                >
                  {{ item.text }}
                  <span class="link-underline"></span>
                </a>
              </li>
            </transition-group>
          </nav>
        </div>
      </transition>
    </div>
  </template>
  
  <script>
  export default {
    name: 'MobileMenu',
    data() {
      return {
        isOpen: false,
        menuItems: [
          { text: 'Home', link: '/' },
          { text: 'About', link: '/about' },
          { text: 'Services', link: '/services' },
          { text: 'Portfolio', link: '/portfolio' },
          { text: 'Contact', link: '/contact' }
        ]
      }
    },
    methods: {
      toggleMenu() {
        this.isOpen = !this.isOpen
        document.body.style.overflow = this.isOpen ? 'hidden' : ''
      },
      closeMenu() {
        this.isOpen = false
        document.body.style.overflow = ''
      }
    }
  }
  </script>
  
  <style scoped>
  /* Base Styles */
  .mobile-menu-container {
    position: relative;
    z-index: 1000;
  }
  
  /* Hamburger Button Styles */
  .hamburger-button {
    padding: 15px;
    display: inline-block;
    cursor: pointer;
    background-color: transparent;
    border: 0;
    margin: 0;
    outline: none;
    transition: transform 0.3s ease;
    z-index: 1001;
  }
  
  .hamburger-button:hover {
    transform: scale(1.1);
  }
  
  .hamburger-box {
    width: 30px;
    height: 24px;
    display: inline-block;
    position: relative;
  }
  
  .hamburger-inner {
    width: 100%;
    height: 2px;
    background-color: #fff;
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    transition: background-color 0.2s 0.1s ease;
  }
  
  .hamburger-inner::before,
  .hamburger-inner::after {
    content: "";
    width: 100%;
    height: 2px;
    background-color: #fff;
    position: absolute;
    left: 0;
    transition: transform 0.2s 0.2s ease;
  }
  
  .hamburger-inner::before {
    top: -8px;
  }
  
  .hamburger-inner::after {
    top: 8px;
  }
  
  /* Active state (X shape) */
  .hamburger-button.is-active .hamburger-inner {
    background-color: transparent;
  }
  
  .hamburger-button.is-active .hamburger-inner::before {
    transform: translateY(8px) rotate(45deg);
  }
  
  .hamburger-button.is-active .hamburger-inner::after {
    transform: translateY(-8px) rotate(-45deg);
  }
  
  /* Menu Overlay */
  .menu-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(5px);
    display: flex;
    justify-content: center;
    align-items: center;
  }
  
  .menu-content {
    width: 80%;
    max-width: 400px;
  }
  
  .menu-content ul {
    list-style: none;
    padding: 0;
    margin: 0;
  }
  
  .menu-content li {
    margin-bottom: 1.5rem;
    opacity: 0;
    transform: translateY(20px);
  }
  
  .menu-link {
    color: white;
    font-size: 1.5rem;
    text-decoration: none;
    position: relative;
    padding: 0.5rem 0;
    display: inline-block;
    font-weight: 300;
    letter-spacing: 1px;
  }
  
  .link-underline {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 0;
    height: 1px;
    background-color: white;
    transition: width 0.3s ease;
  }
  
  .menu-link:hover .link-underline {
    width: 100%;
  }
  
  /* Animations */
  .menu-enter-active, .menu-leave-active {
    transition: opacity 0.5s ease;
  }
  
  .menu-enter-from, .menu-leave-to {
    opacity: 0;
  }
  
  .list-enter-active {
    transition: all 0.4s ease;
  }
  
  .list-enter-from {
    opacity: 0;
    transform: translateY(20px);
  }
  
  .list-enter-to {
    opacity: 1;
    transform: translateY(0);
  }
  
  /* Responsive */
  @media (min-width: 768px) {
    .mobile-menu-container {
      display: none;
    }
  }
  </style>