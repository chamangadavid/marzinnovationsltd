<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppNavbar from '@/Components/AppNavbar.vue';
import HeroSection from '@/Components/HeroSection.vue';
import FeaturesSection from '@/Components/FeaturesSection.vue';
import ServicesList from '@/Components/ServicesList.vue';
import GallerySection from '@/Components/GallerySection.vue';
import AppFooter from '@/Components/AppFooter.vue';
import LatestNewsEventsSection from '@/Components/LatestNewsEventsSection.vue';

defineProps({
    canLogin: { type: Boolean },
    canRegister: { type: Boolean },
    laravelVersion: { type: String, required: true },
    phpVersion: { type: String, required: true },
});

// This function is no longer strictly necessary if you remove the elements it interacts with,
// but keeping it doesn't hurt.
function handleImageError() {
    document.getElementById('screenshot-container')?.classList.add('!hidden');
    document.getElementById('docs-card')?.classList.add('!row-span-1');
    document.getElementById('docs-card-content')?.classList.add('!flex-row');
    document.getElementById('background')?.classList.add('!hidden');
}
</script>

<template>
    <Head title="Blue Spring Cleaning" />

    <div class="text-black/50 dark:text-white/50 min-h-screen">
        <div class="relative flex flex-col min-h-screen">
            <AppNavbar :can-login="canLogin" :can-register="canRegister" :auth-user="$page.props.auth.user" />

            <main class="flex-grow"> <div class="w-full">
                    <HeroSection />
                    <LatestNewsEventsSection />
                    <!-- <FeaturesSection /> -->
                    <ServicesList /> 
                    <GallerySection />
                    </div>
            </main>

            <AppFooter />
            <!-- <footer class="py-16 text-center text-sm text-black dark:text-white/70">
                Laravel v{{ laravelVersion }} (PHP v{{ phpVersion }})
            </footer> -->
        </div>
    </div>
</template>