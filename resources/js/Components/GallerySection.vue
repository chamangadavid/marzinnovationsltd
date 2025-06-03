<template>
    <section class="py-16 bg-white">
      <div class="container mx-auto px-5 text-center">
        <h2 class="text-4xl font-bold text-gray-800 mb-2">OUR GALLERY</h2>
        <p class="text-lg text-gray-600 mb-12">Explore our collection of work</p>
  
        <!-- Category Filters -->
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
            {{ category.name }}
          </button>
        </div>
  
        <!-- Loading Spinner -->
        <div v-if="loading" class="flex justify-center py-8">
          <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-purple-500"></div>
        </div>
  
        <!-- Gallery Grid -->
        <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
          <div
            v-for="(gallery, index) in filteredGalleries"
            :key="gallery.id"
            class="relative overflow-hidden rounded-lg shadow-md cursor-pointer aspect-w-1 aspect-h-1"
            @click="openLightbox(gallery.images[0], index)"
          >
            <img
              :src="gallery.images[0]"
              :alt="gallery.title"
              class="absolute inset-0 w-full h-full object-cover transform transition-transform duration-300 hover:scale-105"
            />
            <div
              v-if="gallery.images.length > 1"
              class="absolute bottom-2 right-2 bg-black bg-opacity-50 text-white px-2 py-1 rounded-full text-xs"
            >
              +{{ gallery.images.length - 1 }}
            </div>
          </div>
        </div>
  
        <!-- Load More Button -->
        <button
          v-if="!loading && hasMoreItems && activeCategory === 'all'"
          @click="loadMore"
          class="mt-12 px-8 py-3 bg-hero-purple text-white rounded-full font-semibold shadow-lg hover:bg-purple-700 transition-colors duration-300 flex items-center justify-center mx-auto"
        >
          Load More
          <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
          </svg>
        </button>
      </div>
  
      <!-- Lightbox Modal -->
      <div
        v-if="lightboxOpen"
        class="fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center z-50 p-4"
        @click.self="closeLightbox"
      >
        <div class="relative max-w-full max-h-full">
          <button
            @click="closeLightbox"
            class="absolute top-4 right-4 text-white text-4xl leading-none z-10 p-2 rounded-full bg-black bg-opacity-50 hover:bg-opacity-75 transition-colors"
          >
            &times;
          </button>
  
          <img
            :src="currentLightboxImage"
            :alt="currentLightboxImage"
            class="max-w-[90vw] max-h-[90vh] object-contain rounded-lg shadow-xl"
          />
  
          <button
            v-if="multipleImages"
            @click.stop="prevImage"
            class="absolute left-4 top-1/2 -translate-y-1/2 text-white text-5xl z-10 p-2 rounded-full bg-black bg-opacity-50 hover:bg-opacity-75 transition-colors"
          >
            &lt;
          </button>
          <button
            v-if="multipleImages"
            @click.stop="nextImage"
            class="absolute right-4 top-1/2 -translate-y-1/2 text-white text-5xl z-10 p-2 rounded-full bg-black bg-opacity-50 hover:bg-opacity-75 transition-colors"
          >
            &gt;
          </button>
        </div>
      </div>
    </section>
  </template>
  
  <script setup>
  import { ref, computed, onMounted } from 'vue';
  import axios from 'axios';
  
  const loading = ref(true);
  const galleries = ref([]);
  const categories = ref([]);
  const activeCategory = ref('all');
  const currentPage = ref(1);
  const lastPage = ref(1);
  const perPage = 5;
  
  const fetchCategories = async () => {
    try {
      const response = await axios.get('/fetch-gallery-category');
      categories.value = [{ id: 'all', name: 'All' }, ...response.data];
    } catch (error) {
      console.error('Error fetching categories:', error);
    }
  };
  
  const fetchGalleries = async (page = 1, categoryId = null) => {
    loading.value = true;
    try {
      let url = `/displayGalleries?page=${page}&per_page=${perPage}`;
      if (categoryId && categoryId !== 'all') {
        url += `&category_id=${categoryId}`;
      }
  
      const response = await axios.get(url);
      if (page === 1) {
        galleries.value = response.data.data;
      } else {
        galleries.value = [...galleries.value, ...response.data.data];
      }
      currentPage.value = response.data.current_page;
      lastPage.value = response.data.last_page;
    } catch (error) {
      console.error('Error fetching galleries:', error);
    } finally {
      loading.value = false;
    }
  };
  
  const filteredGalleries = computed(() => {
    if (activeCategory.value === 'all') {
      return galleries.value;
    }
    return galleries.value.filter(gallery => gallery.category_id == activeCategory.value);
  });
  
  const hasMoreItems = computed(() => {
    return currentPage.value < lastPage.value;
  });
  
  const filterGallery = (categoryId) => {
    activeCategory.value = categoryId;
    fetchGalleries(1, categoryId);
  };
  
  const loadMore = () => {
    if (hasMoreItems.value) {
      fetchGalleries(currentPage.value + 1, activeCategory.value);
    }
  };
  
  // Lightbox Logic
  const lightboxOpen = ref(false);
  const currentLightboxImage = ref('');
  const currentLightboxIndex = ref(0);
  
  const allImages = computed(() => {
    return galleries.value.flatMap(gallery =>
      gallery.images.map(img => ({
        src: img,
        category: gallery.category_id
      }))
    );
  });
  
  const openLightbox = (imageSrc, index) => {
    const imageIndex = allImages.value.findIndex(img => img.src === imageSrc);
    if (imageIndex !== -1) {
      currentLightboxIndex.value = imageIndex;
      currentLightboxImage.value = allImages.value[imageIndex].src;
      lightboxOpen.value = true;
      document.body.style.overflow = 'hidden';
    }
  };
  
  const closeLightbox = () => {
    lightboxOpen.value = false;
    currentLightboxImage.value = '';
    currentLightboxIndex.value = 0;
    document.body.style.overflow = '';
  };
  
  const navigateLightbox = (direction) => {
    const currentCategoryImages = activeCategory.value === 'all'
      ? allImages.value
      : allImages.value.filter(img => img.category == activeCategory.value);
  
    const currentIndex = currentCategoryImages.findIndex(img => img.src === currentLightboxImage.value);
  
    if (direction === 'next') {
      const nextIndex = (currentIndex + 1) % currentCategoryImages.length;
      currentLightboxImage.value = currentCategoryImages[nextIndex].src;
    } else {
      const prevIndex = (currentIndex - 1 + currentCategoryImages.length) % currentCategoryImages.length;
      currentLightboxImage.value = currentCategoryImages[prevIndex].src;
    }
  };
  
  const nextImage = () => navigateLightbox('next');
  const prevImage = () => navigateLightbox('prev');
  
  const multipleImages = computed(() => {
    const currentCategoryImages = activeCategory.value === 'all'
      ? allImages.value
      : allImages.value.filter(img => img.category == activeCategory.value);
    return currentCategoryImages.length > 1;
  });
  
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
  
  onMounted(async () => {
    await fetchCategories();
    await fetchGalleries();
  });
  </script>
  
  <style scoped>
  .gallery-heading::after {
    content: '';
    display: block;
    width: 80px;
    height: 4px;
    background-color: #D4AF37;
    margin: 8px auto 0;
  }
  
  .aspect-w-1 {
    width: 100%;
  }
  
  .aspect-h-1 {
    padding-bottom: 100%;
  }
  </style>
  