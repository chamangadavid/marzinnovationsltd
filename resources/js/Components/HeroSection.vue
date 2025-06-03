

<template>
    <section class="bg-hero-purple text-white py-20 relative overflow-hidden">
        <div class="container mx-auto px-5 flex flex-col md:flex-row items-center justify-between gap-10 text-center md:text-left">
            <div class="flex-1 max-w-xl z-10">
                <p class="bg-white bg-opacity-20 inline-block px-4 py-1 rounded-full text-sm mb-5">Motto : Creating Value Together!</p>
                <h1 class="text-5xl md:text-6xl font-bold leading-tight mb-5">Marz Innovations <span class="text-[#92e623]">Limited,</span></h1>
                <p class="text-lg leading-relaxed mb-8">
                    We are a Digital and Branding company that strives to grow the industry by providing quality products and services.</p>
                <div class="flex flex-col sm:flex-row justify-center md:justify-start gap-5">
                    <button class="btn-primary">Get Instant Price</button>
                    <a href="tel:3033094226" class="btn-secondary">(+260) 966 390 807</a>
                </div>
            </div>

            <div class="flex-1 text-right relative md:static -right-24 -top-5 md:right-0 md:top-0 md:max-w-md">
                <div class="relative w-full h-96 overflow-hidden rounded-lg"> <transition-group name="fade" tag="div" class="absolute inset-0">
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
    '/assets/hero/hero-image-0.png', 
    '/assets/hero/hero-image-1.png', 
    '/assets/hero/hero-image-2.png',
    '/assets/hero/hero-image-3.png',
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
.fade-enter-active, .fade-leave-active {
  transition: opacity 1s ease; 
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
.fade-enter-to, .fade-leave-from {
  opacity: 1;
}


.fade-leave-active {
    position: absolute;
}
</style>





<!-- <template>
    <section class="bg-hero-purple text-white py-20 relative overflow-hidden">
        <div class="container mx-auto px-5 flex flex-col md:flex-row items-center justify-between gap-10 text-center md:text-left">
            <div class="flex-1 max-w-xl z-10">
                <p class="bg-white bg-opacity-20 inline-block px-4 py-1 rounded-full text-sm mb-5">Trusted Cleaning Experts in Denver!</p>
                <h1 class="text-5xl md:text-6xl font-bold leading-tight mb-5">Reliable Cleaning <br>Services In <span class="text-[#92e623]">Denver, CO</span></h1>
                <p class="text-lg leading-relaxed mb-8">We proudly serve Denver, CO, and the surrounding areas, ensuring top-quality service through our two dedicated lines.</p>
                <div class="flex flex-col sm:flex-row justify-center md:justify-start gap-5">
                    <button class="btn-primary">Get Instant Price</button>
                    <a href="tel:3033094226" class="btn-secondary">(303) 309 4226</a>
                </div>
            </div>
          
            <div class="flex-1 text-right relative md:static -mt-74 md:-mt-52 md:max-w-md">
                <div class="relative w-full h-full">
                    <transition-group name="fade" tag="div" class="relative w-full">
                        <img 
                            v-for="(image, index) in images" 
                            :key="image.src"
                            :src="image.src" 
                            :alt="image.alt" 
                            class="max-w-full h-auto block rounded-lg absolute top-0 left-0"
                            v-show="currentIndex === index"
                        >
                    </transition-group>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';

const images = [
    { src: "/assets/hero/hero-image-0.png", alt: "Smiling cleaning professional" },
    { src: "/assets/hero/hero-image-1.png", alt: "Cleaning team at work" },
    { src: "/assets/hero/hero-image-2.png", alt: "Professional cleaning service" },
    { src: "/assets/hero/hero-image-3.png", alt: "Professional cleaning service" },
    { src: "/assets/hero/hero-image-4.png", alt: "Professional cleaning service" },
  
];

const currentIndex = ref(0);
let interval;

const nextSlide = () => {
    currentIndex.value = (currentIndex.value + 1) % images.length;
};

onMounted(() => {
    interval = setInterval(nextSlide, 5000);
});

onBeforeUnmount(() => {
    clearInterval(interval);
});
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 1s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style> -->






<!-- 
<template>
    <section class="bg-hero-purple text-white py-20 relative overflow-hidden">
        <div class="container mx-auto px-5 flex flex-col md:flex-row items-center justify-between gap-10 text-center md:text-left">
            <div class="flex-1 max-w-xl z-10">
                <p class="bg-white bg-opacity-20 inline-block px-4 py-1 rounded-full text-sm mb-5">Trusted Cleaning Experts in Denver!</p>
                <h1 class="text-5xl md:text-6xl font-bold leading-tight mb-5">Reliable Cleaning <br>Services In <span class="text-[#92e623]">Denver, CO</span></h1>
                <p class="text-lg leading-relaxed mb-8">We proudly serve Denver, CO, and the surrounding areas, ensuring top-quality service through our two dedicated lines.</p>
                <div class="flex flex-col sm:flex-row justify-center md:justify-start gap-5">
                    <button class="btn-primary">Get Instant Price</button>
                    <a href="tel:3033094226" class="btn-secondary">(303) 309 4226</a>
                </div>
            </div>
            <div class="flex-1 text-right relative md:static -right-24 -top-5 md:right-0 md:top-0 md:max-w-md">
                <img src="/assets/hero-image.png" alt="Smiling cleaning professional" class="max-w-full h-auto block rounded-lg">
            </div>
        </div>
    </section>
</template>

<script setup>
</script> -->
