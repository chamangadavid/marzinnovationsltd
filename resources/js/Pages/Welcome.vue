<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppNavbar from '@/Components/AppNavbar.vue';
import HeroSection from '@/Components/HeroSection.vue';
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

</script>

<template>
    <Head title="Blue Spring Cleaning" />

    <div class="text-black/50 dark:text-white/50 min-h-screen">
        <div class="relative flex flex-col min-h-screen">
            <AppNavbar :can-login="canLogin" :can-register="canRegister" :auth-user="$page.props.auth.user" />

            <main class="flex-grow"> <div class="w-full">
                    <HeroSection />
                    <LatestNewsEventsSection />
                    <ServicesList /> 
                    <GallerySection />
             </div>
            </main>

            <AppFooter />
        </div>
    </div>
</template>