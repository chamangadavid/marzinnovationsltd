<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    canLogin: { type: Boolean, required: true },
    canRegister: { type: Boolean, required: true },
    authUser: { type: Object, default: null },
});

// State management
const servicesDropdownOpen = ref(false);
const mobileMenuOpen = ref(false);
const isScrolled = ref(false);

// Dropdown functions
const toggleServicesDropdown = () => servicesDropdownOpen.value = !servicesDropdownOpen.value;
const openServicesDropdown = () => servicesDropdownOpen.value = true;
const closeServicesDropdown = () => servicesDropdownOpen.value = false;

// Handle scroll effect
const handleScroll = () => {
    isScrolled.value = window.scrollY > 10;
};

// Outside click handler
const handleClickOutside = (event) => {
    if (!mobileMenuOpen.value && !event.target.closest('.nav-dropdown') && !event.target.closest('.mobile-dropdown-container')) {
        closeServicesDropdown();
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
    window.addEventListener('scroll', handleScroll);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside);
    window.removeEventListener('scroll', handleScroll);
});
</script>

<template>
    <header 
        class="fixed w-full z-50 transition-all duration-300 ease-in-out"
        :class="{
            'bg-white/95 backdrop-blur-md shadow-lg py-2': isScrolled,
            'bg-white/90 backdrop-blur-sm py-4': !isScrolled
        }"
    >
        <div class="container mx-auto px-5 flex justify-between items-center">
            <!-- Logo with animation -->
            <Link href="/" class="flex items-center group">
                <img 
                    src="/assets/logo.svg" 
                    alt="Marz Innovations Logo" 
                    class="h-10 mr-3 transition-transform duration-300 group-hover:scale-110"
                >
                <span class="font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-emerald-500 text-xl leading-tight">
                    Marz Innovations
                </span>
            </Link>

            <!-- Desktop Navigation -->
            <nav class="hidden md:flex items-center space-x-1">
                <Link 
                    href="/" 
                    class="nav-link px-4 py-2 rounded-lg"
                    :class="{ 'active-link': $page.url === '/' }"
                >
                    Home
                </Link>

                <Link 
                    :href="route('aboutUs')" 
                    class="nav-link px-4 py-2 rounded-lg"
                    :class="{ 'active-link': $page.url.startsWith('/about') }"
                >
                    About
                </Link>

                <!-- Services Dropdown -->
                <div 
                    class="nav-dropdown relative" 
                    @mouseenter="openServicesDropdown" 
                    @mouseleave="closeServicesDropdown"
                >
                    <button class="nav-link px-4 py-2 rounded-lg inline-flex items-center">
                        Services
                        <svg 
                            class="ml-2 h-4 w-4 transition-transform duration-200" 
                            :class="{ 'rotate-180': servicesDropdownOpen }"
                            xmlns="http://www.w3.org/2000/svg" 
                            viewBox="0 0 20 20" 
                            fill="currentColor"
                        >
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    
                    <transition
                        enter-active-class="transition duration-200 ease-out"
                        enter-from-class="transform opacity-0 scale-95"
                        enter-to-class="transform opacity-100 scale-100"
                        leave-active-class="transition duration-150 ease-in"
                        leave-from-class="transform opacity-100 scale-100"
                        leave-to-class="transform opacity-0 scale-95"
                    >
                        <div 
                            v-if="servicesDropdownOpen" 
                            class="absolute left-0 mt-2 w-56 origin-top-left rounded-xl bg-white shadow-xl ring-1 ring-black/5 focus:outline-none z-50"
                        >
                            <div class="p-1">
                                <Link 
                                    :href="route('servicesList')" 
                                    class="dropdown-item group flex w-full items-center rounded-lg px-4 py-3 text-sm"
                                    @click="closeServicesDropdown"
                                >
                                    <span class="text-gray-700 group-hover:text-blue-600 transition-colors">Our Services</span>
                                </Link>
                            </div>
                        </div>
                    </transition>
                </div>

                <Link 
                    :href="route('contactDetails')" 
                    class="nav-link px-4 py-2 rounded-lg"
                    :class="{ 'active-link': $page.url.startsWith('/contact') }"
                >
                    Contact
                </Link>

                <!-- Auth Buttons -->
                <div class="ml-4 flex items-center space-x-2">
                    <template v-if="canLogin">
                        <Link
                            v-if="authUser"
                            :href="route('dashboard')"
                            class="px-4 py-2 rounded-lg bg-gradient-to-r from-blue-500 to-blue-600 text-white font-medium hover:shadow-lg transition-all duration-300"
                        >
                            Dashboard
                        </Link>
                        <template v-else>
                            <Link
                                :href="route('login')"
                                class="px-4 py-2 rounded-lg border border-blue-500 text-blue-600 font-medium hover:bg-blue-50 transition-colors"
                            >
                                Log in
                            </Link>
                            <Link
                                v-if="canRegister"
                                :href="route('register')"
                                class="px-4 py-2 rounded-lg bg-gradient-to-r from-blue-500 to-blue-600 text-white font-medium hover:shadow-lg transition-all duration-300"
                            >
                                Register
                            </Link>
                        </template>
                    </template>
                </div>
            </nav>

            <!-- Mobile Menu Button -->
            <button 
                class="md:hidden p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                @click="mobileMenuOpen = !mobileMenuOpen"
            >
                <svg 
                    class="w-6 h-6 text-gray-700" 
                    :class="{ 'hidden': mobileMenuOpen }" 
                    fill="none" 
                    viewBox="0 0 24 24" 
                    stroke="currentColor"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg 
                    class="w-6 h-6 text-gray-700" 
                    :class="{ 'hidden': !mobileMenuOpen }" 
                    fill="none" 
                    viewBox="0 0 24 24" 
                    stroke="currentColor"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Mobile Navigation Menu -->
        <transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95"
        >
            <div 
                v-if="mobileMenuOpen" 
                class="md:hidden px-5 pb-5 pt-2 bg-white/95 backdrop-blur-md shadow-lg z-50"
            >
                <div class="space-y-2">
                    <Link 
                        href="/" 
                        class="mobile-nav-link block px-4 py-3 rounded-lg"
                        :class="{ 'mobile-active-link': $page.url === '/' }"
                        @click="mobileMenuOpen = false"
                    >
                        Home
                    </Link>

                    <Link 
                        :href="route('aboutUs')" 
                        class="mobile-nav-link block px-4 py-3 rounded-lg"
                        :class="{ 'mobile-active-link': $page.url.startsWith('/about') }"
                        @click="mobileMenuOpen = false"
                    >
                        About
                    </Link>

                    <!-- Mobile Dropdown -->
                    <div class="px-4 py-3 rounded-lg mobile-dropdown-container" @click.stop>
                        <button 
                            @click="toggleServicesDropdown" 
                            class="mobile-nav-link w-full text-left flex justify-between items-center"
                        >
                            <span>Services</span>
                            <svg 
                                class="h-4 w-4 transition-transform duration-200" 
                                :class="{ 'rotate-180': servicesDropdownOpen }"
                                xmlns="http://www.w3.org/2000/svg" 
                                viewBox="0 0 20 20" 
                                fill="currentColor"
                            >
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        
                        <transition
                            enter-active-class="transition duration-200 ease-out"
                            enter-from-class="transform opacity-0 -translate-y-2"
                            enter-to-class="transform opacity-100 translate-y-0"
                            leave-active-class="transition duration-200 ease-in"
                            leave-from-class="transform opacity-100 translate-y-0"
                            leave-to-class="transform opacity-0 -translate-y-2"
                        >
                            <div 
                                v-if="servicesDropdownOpen" 
                                class="pl-4 mt-2 space-y-2 mobile-dropdown-content"
                                @click.stop
                            >
                                <Link 
                                    :href="route('servicesList')" 
                                    class="block px-4 py-2 text-gray-700 hover:text-blue-600 rounded-lg transition-colors"
                                    @click="mobileMenuOpen = false; servicesDropdownOpen = false"
                                >
                                    Our Services
                                </Link>
                            </div>
                        </transition>
                    </div>

                    <Link 
                        :href="route('contactDetails')" 
                        class="mobile-nav-link block px-4 py-3 rounded-lg"
                        :class="{ 'mobile-active-link': $page.url.startsWith('/contact') }"
                        @click="mobileMenuOpen = false"
                    >
                        Contact
                    </Link>

                    <!-- Mobile Auth Buttons -->
                    <div class="pt-2 space-y-2">
                        <template v-if="canLogin">
                            <Link
                                v-if="authUser"
                                :href="route('dashboard')"
                                class="block w-full px-4 py-3 text-center rounded-lg bg-gradient-to-r from-blue-500 to-blue-600 text-white font-medium"
                                @click="mobileMenuOpen = false"
                            >
                                Dashboard
                            </Link>
                            <template v-else>
                                <Link
                                    :href="route('login')"
                                    class="block w-full px-4 py-3 text-center rounded-lg border border-blue-500 text-blue-600 font-medium"
                                    @click="mobileMenuOpen = false"
                                >
                                    Log in
                                </Link>
                                <Link
                                    v-if="canRegister"
                                    :href="route('register')"
                                    class="block w-full px-4 py-3 text-center rounded-lg bg-gradient-to-r from-blue-500 to-blue-600 text-white font-medium"
                                    @click="mobileMenuOpen = false"
                                >
                                    Register
                                </Link>
                            </template>
                        </template>
                    </div>
                </div>
            </div>
        </transition>
    </header>
</template>

<style scoped>
/* Navigation Link Styles */
.nav-link {
    color: #374151; /* Tailwind text-gray-700 */
    font-weight: 500; /* Tailwind font-medium */
    transition-property: all;
    transition-duration: 300ms;
}
.nav-link:hover {
    color: #2563eb; /* Tailwind text-blue-600 */
    background-color: #eff6ff; /* Tailwind bg-blue-50 */
}

.active-link {
    color: #2563eb; /* Tailwind text-blue-600 */
    background-color: rgba(239, 246, 255, 0.5); /* Tailwind bg-blue-50/50 */
    font-weight: 600; /* Tailwind font-semibold */
}

.mobile-nav-link {
    color: #374151; /* Tailwind text-gray-700 */
    font-weight: 500; /* Tailwind font-medium */
    transition-property: color;
    transition-duration: 200ms;
}
.mobile-nav-link:hover {
    color: #2563eb; /* Tailwind text-blue-600 */
}

.mobile-active-link {
    color: #2563eb; /* Tailwind text-blue-600 */
    background-color: #eff6ff; /* Tailwind bg-blue-50 */
    font-weight: 600; /* Tailwind font-semibold */
}

/* Dropdown Item Styles */
.dropdown-item {
    transition: all 200ms ease;
}
.dropdown-item:hover {
    background-color: #eff6ff; /* Tailwind bg-blue-50 */
}

/* Mobile Dropdown Styles */
.mobile-dropdown-container {
    position: relative;
    z-index: 10;
}

.mobile-dropdown-content {
    z-index: 20;
    position: relative;
}

/* Smooth transitions */
header {
    will-change: transform, background-color, box-shadow;
}

/* Better focus states */
button:focus, a:focus {
    outline: none;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5); /* blue-500 with 50% opacity */
    border-radius: 0.375rem; /* matches Tailwind's rounded-md */
}
</style>