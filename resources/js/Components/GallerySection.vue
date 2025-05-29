<template>
    <section class="py-16 bg-white">
        <div class="container mx-auto px-5 text-center">
            <h2 class="text-4xl font-bold text-gray-800 mb-2">OUR GALLERY</h2>
            <p class="text-lg text-gray-600 mb-12">This project is created in order to help business</p>

            <div class="flex flex-wrap justify-center gap-4 mb-10">
                <button
                    v-for="category in categories"
                    :key="category.id"
                    @click="filterGallery(category.id)"
                    :class="{
                        'px-6 py-2 rounded-full font-semibold transition-colors duration-300': true,
                        'bg-hero-purple text-white': activeCategory === category.id,
                        'bg-gray-200 text-gray-700 hover:bg-gray-300': activeCategory !== category.id
                    }"
                >
                    <span class="flex items-center">
                        <component :is="category.icon" class="mr-2 h-5 w-5" v-if="category.icon" />
                        {{ category.name }}
                    </span>
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                <div v-for="(image, index) in filteredImages" :key="image.src"
                     class="relative overflow-hidden rounded-lg shadow-md cursor-pointer aspect-w-1 aspect-h-1"
                     @click="openLightbox(image.src, index)">
                    <img :src="image.src" :alt="image.alt"
                         class="absolute inset-0 w-full h-full object-cover transform transition-transform duration-300 hover:scale-105">
                </div>
            </div>

            <button
                v-if="visibleImages < allImages.length"
                @click="loadMoreImages"
                class="mt-12 px-8 py-3 bg-hero-purple text-white rounded-full font-semibold shadow-lg hover:bg-purple-700 transition-colors duration-300 flex items-center justify-center mx-auto"
            >
                Load More
                <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                </svg>
            </button>
        </div>

        <div v-if="lightboxOpen" class="fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center z-50 p-4" @click.self="closeLightbox">
            <div class="relative max-w-full max-h-full">
                <button @click="closeLightbox" class="absolute top-4 right-4 text-white text-4xl leading-none z-10 p-2 rounded-full bg-black bg-opacity-50 hover:bg-opacity-75 transition-colors">
                    &times;
                </button>

                <img :src="currentLightboxImage" :alt="currentLightboxImage" class="max-w-[90vw] max-h-[90vh] object-contain rounded-lg shadow-xl">

                <button @click.stop="prevImage" class="absolute left-4 top-1/2 -translate-y-1/2 text-white text-5xl z-10 p-2 rounded-full bg-black bg-opacity-50 hover:bg-opacity-75 transition-colors">
                    &lt;
                </button>
                <button @click.stop="nextImage" class="absolute right-4 top-1/2 -translate-y-1/2 text-white text-5xl z-10 p-2 rounded-full bg-black bg-opacity-50 hover:bg-opacity-75 transition-colors">
                    &gt;
                </button>
            </div>
        </div>
    </section>
</template>

<script setup>
import { ref, computed } from 'vue';
import {
    SmileOutlined,    // For Corporate Apparel
    PrinterOutlined,   // For Personal/Family -> Printing
    VideoCameraOutlined, // For School -> Videography
    WomanOutlined      // For Fashion (simple female icon)
} from '@ant-design/icons-vue';

// --- Gallery Data ---
const allImages = ref([
    // All images should have a category for filtering
    // Corporate Apparel
    { src: '/assets/gallery/corporate-apparel/corporate-1.jpeg', alt: 'Corporate apparel image 1', category: 'corporate-apparel' },
    { src: '/assets/gallery/corporate-apparel/corporate-2.jpeg', alt: 'Corporate apparel image 2', category: 'corporate-apparel' },
    { src: '/assets/gallery/corporate-apparel/corporate-3.jpeg', alt: 'Corporate apparel image 3', category: 'corporate-apparel' },
    // Personal Printing
    { src: '/assets/gallery/personal-printing/printing-1.jpeg', alt: 'Personal printing image 1', category: 'personal-printing' },
    { src: '/assets/gallery/personal-printing/printing-2.jpeg', alt: 'Personal printing image 2', category: 'personal-printing' },
    { src: '/assets/gallery/personal-printing/printing-3.jpeg', alt: 'Personal printing image 3', category: 'personal-printing' },
    // Videography
    { src: '/assets/gallery/videography/video-1.jpeg', alt: 'Videography image 1', category: 'videography' },
    { src: '/assets/gallery/videography/video-2.jpeg', alt: 'Videography image 2', category: 'videography' },
    { src: '/assets/gallery/videography/video-3.jpeg', alt: 'Videography image 3', category: 'videography' },
    // Fashion
    { src: '/assets/gallery/fashion/fashion-1.jpeg', alt: 'Fashion image 1', category: 'fashion' },
    { src: '/assets/gallery/fashion/fashion-2.jpeg', alt: 'Fashion image 2', category: 'fashion' },
    { src: '/assets/gallery/fashion/fashion-3.jpeg', alt: 'Fashion image 3', category: 'fashion' },
    // Add more images as needed for each category
]);

const activeCategory = ref('all'); // 'all' by default, or 'corporate-apparel', etc.
const imagesPerPage = 8; // Number of images to show initially and load per click
const visibleImages = ref(imagesPerPage);

const categories = ref([
    { id: 'all', name: 'All', icon: null }, // No specific icon for 'All'
    { id: 'corporate-apparel', name: 'Corporate Apparel', icon: SmileOutlined },
    { id: 'personal-printing', name: 'Printing', icon: PrinterOutlined }, // Renamed from Family
    { id: 'videography', name: 'Videography', icon: VideoCameraOutlined },   // Renamed from School
    { id: 'fashion', name: 'Fashion', icon: WomanOutlined },
]);

const filteredImages = computed(() => {
    let images = activeCategory.value === 'all'
        ? allImages.value
        : allImages.value.filter(img => img.category === activeCategory.value);
    return images.slice(0, visibleImages.value);
});

const filterGallery = (categoryId) => {
    activeCategory.value = categoryId;
    visibleImages.value = imagesPerPage; // Reset visible images when category changes
};

const loadMoreImages = () => {
    visibleImages.value += imagesPerPage;
};

// --- Lightbox Logic ---
const lightboxOpen = ref(false);
const currentLightboxImage = ref('');
const currentLightboxIndex = ref(0);

const openLightbox = (imageSrc, index) => {
    currentLightboxImage.value = imageSrc;
    currentLightboxIndex.value = allImages.value.indexOf(allImages.value.find(img => img.src === imageSrc)); // Find index in all images
    lightboxOpen.value = true;
    document.body.style.overflow = 'hidden'; // Prevent scrolling when lightbox is open
};

const closeLightbox = () => {
    lightboxOpen.value = false;
    currentLightboxImage.value = '';
    currentLightboxIndex.value = 0;
    document.body.style.overflow = ''; // Re-enable scrolling
};

const navigateLightbox = (direction) => {
    const currentCategoryImages = activeCategory.value === 'all'
        ? allImages.value
        : allImages.value.filter(img => img.category === activeCategory.value);

    let newIndex = currentCategoryImages.findIndex(img => img.src === currentLightboxImage.value);

    if (direction === 'next') {
        newIndex = (newIndex + 1) % currentCategoryImages.length;
    } else { // 'prev'
        newIndex = (newIndex - 1 + currentCategoryImages.length) % currentCategoryImages.length;
    }
    currentLightboxImage.value = currentCategoryImages[newIndex].src;
};

const nextImage = () => navigateLightbox('next');
const prevImage = () => navigateLightbox('prev');

// Keyboard navigation for lightbox
window.addEventListener('keydown', (e) => {
    if (lightboxOpen.value) {
        if (e.key === 'ArrowRight') {
            nextImage();
        } else if (e.key === 'ArrowLeft') {
            prevImage();
        } else if (e.key === 'Escape') {
            closeLightbox();
        }
    }
});
</script>

<style scoped>
/* Custom style for the gallery heading underline (optional, similar to services) */
.gallery-heading::after {
    content: '';
    display: block;
    width: 80px; /* Adjust as needed */
    height: 4px;
    background-color: #D4AF37; /* Example gold color, or use a custom primary color */
    margin: 8px auto 0;
}

/* Aspect ratio for image grid items */
.aspect-w-1 {
    width: 100%;
}
.aspect-h-1 {
    padding-bottom: 100%; /* Creates a square aspect ratio */
}
</style>