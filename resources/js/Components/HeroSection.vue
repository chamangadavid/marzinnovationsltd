<template>
    <section class="relative text-white py-20 overflow-hidden h-[90vh] flex items-center">
        <!-- Background slider with dark purple overlay from left -->
        <div class="absolute inset-0 z-0">
            <transition-group name="slide" tag="div" class="absolute inset-0 w-full h-full">
                <img
                    v-for="(image, index) in images"
                    :key="image"
                    :src="image"
                    v-show="index === currentIndex"
                    class="absolute inset-0 w-full h-full object-cover transition-opacity duration-1000"
                />
            </transition-group>
            <!-- Dark purple overlay from left -->
            <div class="absolute inset-0 bg-gradient-to-r from-[#3b0a54]/80 to-transparent z-10"></div>
        </div>

        <!-- Foreground content -->
        <div class="container mx-auto px-5 relative z-20">
            <div class="flex flex-col md:flex-row items-center justify-between gap-10 text-center md:text-left">
                <div class="flex-1 max-w-xl">
                    <p class="bg-white bg-opacity-20 inline-block px-4 py-1 rounded-full text-sm mb-5">Creating Value Together!</p>
                    <h1 class="text-5xl md:text-6xl font-bold leading-tight mb-5">
                        Marz Innovations <span class="text-blue-400">Limited,</span>
                    </h1>
                    <p class="text-lg leading-relaxed mb-8">
                        We are a Digital and Branding company that strives to grow the industry by providing quality products and services.
                    </p>
                    <div class="flex flex-col sm:flex-row justify-center md:justify-start gap-5">
                        <button class="bg-blue-500 hover:bg-blue-600 text-white font-semibold px-6 py-3 rounded shadow transition-all">
                            Get Instant Price
                        </button>
                        <a href="tel:3033094226" class="btn-secondary">(+260) 966 390 807</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>


<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const images = ref([
    'https://images.unsplash.com/photo-1519389950473-47ba0277781c?auto=format&fit=crop&w=1600&q=80',
    'https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&w=1600&q=80',
    'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=1600&q=80',
]);




const currentIndex = ref(0);
let intervalId = null;

const nextImage = () => {
    currentIndex.value = (currentIndex.value + 1) % images.value.length;
};

onMounted(() => {
    intervalId = setInterval(nextImage, 5000);
});

onUnmounted(() => {
    if (intervalId) clearInterval(intervalId);
});
</script>

<style scoped>
.slide-enter-active,
.slide-leave-active {
    transition: opacity 1s ease, transform 1s ease;
}

.slide-enter-from {
    opacity: 0;
    transform: translateX(100%);
}
.slide-enter-to {
    opacity: 1;
    transform: translateX(0);
}

.slide-leave-from {
    opacity: 1;
    transform: translateX(0);
}
.slide-leave-to {
    opacity: 0;
    transform: translateX(-100%);
}

.slide-leave-active {
    position: absolute;
}
</style>
















<!-- <template>
    <section class="bg-hero-purple text-white py-20 relative overflow-hidden">
        <div class="container mx-auto px-5 flex flex-col md:flex-row items-center justify-between gap-10 text-center md:text-left">
            <div class="flex-1 max-w-xl z-10">
                <p class="bg-white bg-opacity-20 inline-block px-4 py-1 rounded-full text-sm mb-5">Creating Value Together!</p>
                <h1 class="text-5xl md:text-6xl font-bold leading-tight mb-5">Marz Innovations <span class="text-[#92e623]">Limited,</span></h1>
                <p class="text-lg leading-relaxed mb-8">
                    We are a Digital and Branding company that strives to grow the industry by providing quality products and services.</p>
                <div class="flex flex-col sm:flex-row justify-center md:justify-start gap-5">
                    <button class="btn-primary">Get Instant Price</button>
                    <a href="tel:3033094226" class="btn-secondary">(+260) 966 390 807</a>
                </div>
            </div>

            <div class="flex-1 text-right relative md:static -right-24 -top-5 md:right-0 md:top-0 md:max-w-md">
                <div class="relative w-full h-96 overflow-hidden rounded-lg">
                    <transition-group name="slide" tag="div" class="absolute inset-0">
                        <img
                            v-for="(image, index) in images"
                            :key="image"
                            :src="image"
                            :alt="`Cleaning professional ${index + 1}`"
                            v-show="index === currentIndex"
                            class="absolute inset-0 w-full h-full object-cover rounded-lg"
                        >
                    </transition-group>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const images = ref([
    '/assets/hero/five.png',
    '/assets/hero/two.png', 
    '/assets/hero/three.png', 
]);

const currentIndex = ref(0);
let intervalId = null; 

const nextImage = () => {
    currentIndex.value = (currentIndex.value + 1) % images.value.length;
};

onMounted(() => {
    intervalId = setInterval(nextImage, 5000);
});

onUnmounted(() => {
    if (intervalId) {
        clearInterval(intervalId);
    }
});
</script>

<style scoped>
/* Slide transition */
.slide-enter-active,
.slide-leave-active {
  transition: all 1s ease;
}

.slide-enter-from {
  transform: translateX(100%);
  opacity: 0;
}

.slide-enter-to {
  transform: translateX(0);
  opacity: 1;
}

.slide-leave-from {
  transform: translateX(0);
  opacity: 1;
}

.slide-leave-to {
  transform: translateX(-100%);
  opacity: 0;
}

.slide-leave-active {
  position: absolute;
}
</style> -->