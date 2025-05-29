<template>
    <section class="py-16 bg-white">
        <div class="container mx-auto px-5 text-center">
            <h2 class="text-3xl font-bold text-gray-800 mb-12 relative pb-4 services-heading">Our Services</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-10"> <div v-for="service in services" :key="service.name"
                     class="flex flex-col items-center p-6 rounded-lg shadow-md bg-white transition-transform duration-300 hover:scale-105">
                    <div class="bg-gold-light p-4 rounded-lg mb-4 text-gold">
                        <component :is="service.icon" class="h-12 w-12" />
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ service.name }}</h3>
                    <p class="text-gray-600 text-center text-sm">{{ service.description }}</p>
                    </div>
            </div>

            <a href="#" class="text-hero-purple font-semibold flex items-center justify-center group mt-8">
                Read More
                <ArrowRightOutlined class="ml-2 w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" />
            </a>
        </div>
    </section>
</template>

<script setup>
import {
    TrademarkOutlined,
    SketchOutlined,
    PrinterOutlined,
    VideoCameraOutlined,
    ArrowRightOutlined // Still needed for the single link
} from '@ant-design/icons-vue';
import { ref } from 'vue';

const services = ref([
    {
        name: 'Branding',
        icon: TrademarkOutlined,
        description: 'Crafting unique identities that resonate with your audience and leave a lasting impression.'
    },
    {
        name: 'Embroidery',
        icon: SketchOutlined,
        description: 'High-quality custom embroidery for apparel, uniforms, and promotional items.'
    },
    {
        name: 'Printing',
        icon: PrinterOutlined,
        description: 'Professional printing services for brochures, flyers, business cards, and large formats.'
    },
    {
        name: 'Videography',
        icon: VideoCameraOutlined,
        description: 'Capturing your stories and events with stunning visuals and compelling narratives.'
    }
]);
</script>

<style scoped>
/* Custom CSS for the heading underline */
.services-heading::after {
    content: '';
    display: block;
    width: 60px; /* Adjust width as needed */
    height: 3px;
    background-color: #D4AF37; /* Use the hex code for gold.DEFAULT */
    margin: 8px auto 0; /* Center the line */
}
</style>