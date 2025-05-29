<template>
    <header class="bg-white py-4 shadow-md w-full">
        <div class="container mx-auto px-5 flex justify-between items-center">
            <div class="flex items-center">
                <img src="/assets/logo.svg" alt="Blue Spring Cleaning Logo" class="h-10 mr-2">
                <span class="font-bold text-blue-500 leading-tight text-lg">Marz <br>Innovation Ltd</span>
            </div>
            <nav class="hidden md:flex space-x-6 items-center">
                <a href="#" class="text-gray-600 font-semibold hover:text-blue-500 transition-colors duration-300">Home</a>

                <div 
                class="relative"
                @mouseenter="openServicesDropdown"
                @mouseleave="closeServicesDropdown"
              >
                <!-- Services Button -->
                <button
                  class="text-gray-600 font-semibold hover:text-blue-500 transition-colors duration-300 inline-flex items-center px-3 py-2 focus:outline-none"
                >
                  Services
                  <svg class="ml-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                  </svg>
                </button>
              
                <!-- Dropdown Menu -->
                <div
                  v-if="servicesDropdownOpen"
                
                  class="absolute bg-white shadow-lg rounded-md -mt-1 w-48 z-10"

                >
                  <a href="#" class="block px-4 py-2 text-gray-800 hover:bg-blue-100">Branding</a>
                  <a href="#" class="block px-4 py-2 text-gray-800 hover:bg-blue-100">Embroidery</a>
                  <a href="#" class="block px-4 py-2 text-gray-800 hover:bg-blue-100">Printing</a>
                  <a href="#" class="block px-4 py-2 text-gray-800 hover:bg-blue-100">Videography</a>
                </div>
              </div>

              <Link
                    :href="route('contactDetails')"
                    class="text-gray-600 font-semibold hover:text-blue-500 transition-colors duration-300"
                >
                    Contact Us
                </Link>

                <!-- <a href="#" class="text-gray-600 font-semibold hover:text-blue-500 transition-colors duration-300">Contact Us</a> -->

                <template v-if="canLogin">
                    <Link
                        v-if="authUser"
                        :href="route('dashboard')"
                        class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white ml-4"
                    >
                        Dashboard
                    </Link>

                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white ml-4"
                        >
                            Log in
                        </Link>

                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                        >
                            Register
                        </Link>
                    </template>
                </template>
            </nav>

            <button class="md:hidden text-gray-600 text-2xl">
                ☰
            </button>
        </div>
    </header>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'; // Import ref and lifecycle hooks
import { Link } from '@inertiajs/vue3';

// Define props
const props = defineProps({
    canLogin: { type: Boolean, required: true },
    canRegister: { type: Boolean, required: true },
    authUser: { type: Object, default: null },
});

// Reactive state for dropdown visibility
const servicesDropdownOpen = ref(false);

// Functions to control dropdown
const toggleServicesDropdown = () => {
    servicesDropdownOpen.value = !servicesDropdownOpen.value;
};

const openServicesDropdown = () => {
    servicesDropdownOpen.value = true;
};

const closeServicesDropdown = () => {
    servicesDropdownOpen.value = false;
};

// Handle clicks outside the dropdown to close it
const handleClickOutside = (event) => {
    // Ensure the click did not originate from within the dropdown container
    // or its trigger button.
    const dropdownContainer = event.target.closest('.relative');
    if (!dropdownContainer || !dropdownContainer.contains(event.target)) {
        closeServicesDropdown();
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>

<!-- <template>
    <header class="bg-white py-4 shadow-md w-full"> <div class="container mx-auto px-5 flex justify-between items-center">
            <div class="flex items-center">
                <img src="/assets/logo.svg" alt="Blue Spring Cleaning Logo" class="h-10 mr-2">
                <span class="font-bold text-blue-500 leading-tight text-lg">Marz <br>Innovation Ltd</span>
            </div>
            <nav class="hidden md:flex space-x-6 items-center"> <a href="#" class="text-gray-600 font-semibold hover:text-blue-500 transition-colors duration-300">Home</a>
                <a href="#" class="text-gray-600 font-semibold hover:text-blue-500 transition-colors duration-300">Services</a>
                <a href="#" class="text-gray-600 font-semibold hover:text-blue-500 transition-colors duration-300">Contact Us</a>

                <template v-if="canLogin">
                    <Link
                        v-if="authUser"
                        :href="route('dashboard')"
                        class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white ml-4"
                    >
                        Dashboard
                    </Link>

                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white ml-4"
                        >
                            Log in
                        </Link>

                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                        >
                            Register
                        </Link>
                    </template>
                </template>
            </nav>


            <button class="md:hidden text-gray-600 text-2xl">
                ☰
            </button>
        </div>
    </header>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'; 


const props = defineProps({
    canLogin: { type: Boolean, required: true },
    canRegister: { type: Boolean, required: true },
    authUser: { type: Object, default: null }, 
});
</script> -->