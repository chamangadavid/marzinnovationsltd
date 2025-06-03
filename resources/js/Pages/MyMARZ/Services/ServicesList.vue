<script setup>
import { ref, onMounted, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import AppFooter from '@/Components/AppFooter.vue';
import { WhatsAppOutlined } from '@ant-design/icons-vue';
import axios from 'axios';

const services = ref([]);
const showGallery = ref(false);
const currentServiceImages = ref([]);
const currentImageIndex = ref(0);
const currentServiceIndex = ref(0);

const currentImage = computed(() => {
  return currentServiceImages.value[currentImageIndex.value] || '';
});

const canNavigatePrev = computed(() => {
  return currentImageIndex.value > 0;
});

const canNavigateNext = computed(() => {
  return currentImageIndex.value < currentServiceImages.value.length - 1;
});

onMounted(async () => {
  try {
    const response = await axios.get('/getAllServices');
    services.value = response.data;
  } catch (error) {
    console.error('Error fetching services:', error);
  }
});

function openGallery(serviceIndex) {
  currentServiceIndex.value = serviceIndex;
  currentServiceImages.value = services.value[serviceIndex].images;
  currentImageIndex.value = 0;
  showGallery.value = true;
  document.body.style.overflow = 'hidden';
}

function closeGallery() {
  showGallery.value = false;
  document.body.style.overflow = '';
}

function nextImage() {
  if (canNavigateNext.value) {
    currentImageIndex.value++;
  }
}

function prevImage() {
  if (canNavigatePrev.value) {
    currentImageIndex.value--;
  }
}

function inquireAboutService(service) {
  // Implement your inquiry logic here
  console.log('Inquiring about:', service.title);
  // You might want to open a contact form or redirect
  // window.location.href = `/contact?service=${service.id}`;
}

function inquireOnWhatsApp(service) {
  // Format the phone number (remove any non-digit characters)
  const phoneNumber = '+260966390807'.replace(/\D/g, '');
  
  // Create the WhatsApp message
  const message = `Hi, I'm interested in your ${service.title} service (Price: ZKW${service.price}). Could you please provide more information?`;
  
  // Encode the message for URL
  const encodedMessage = encodeURIComponent(message);
  
  // Create the WhatsApp URL
  const whatsappUrl = `https://wa.me/${phoneNumber}?text=${encodedMessage}`;
  
  // Open in a new tab
  window.open(whatsappUrl, '_blank');
}

</script>
<template>
    <Head title="Services" />
    <header  
      class="h-64 mb-6 bg-cover bg-center relative" 
      :style="{ backgroundImage: `url('/assets/contact.jpg')` }"
    >
      <div class="max-w-4xl mx-auto px-4 flex flex-col justify-center h-full relative z-10 space-y-4 text-center">
        <h2 class="text-4xl font-bold text-white drop-shadow-lg mb-0">Our Services</h2>
        
        <nav class="text-lg font-semibold text-white drop-shadow-lg space-x-2">
          <a href="/" class="hover:underline">Home</a> 
          <span class="mx-2">/</span>
          <a href="/" class="hover:underline">Services</a> 
        </nav>
      </div>
   
      <!-- Back button bottom left -->
      <a 
        href="/" 
        class="absolute bottom-4 left-4 bg-white bg-opacity-80 text-gray-800 font-semibold px-4 py-2 rounded hover:bg-opacity-100 transition z-20"
      >
        &larr; Back to Home
      </a>
   
      <div class="absolute inset-0 bg-black opacity-40"></div>
    </header>
  
    <div class="container mx-auto px-4 py-8">
      <!-- Services Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div 
          v-for="(service, index) in services" 
          :key="service.id" 
          class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300"
        >
          <!-- Service Image -->
          <div class="relative h-48 overflow-hidden cursor-pointer" @click="openGallery(index)">
            <img 
              :src="service.images[0] || '/placeholder-service.jpg'" 
              :alt="service.title" 
              class="w-full h-full object-cover transition-transform duration-300 hover:scale-105"
            >
            <div v-if="service.images.length > 1" class="absolute bottom-2 right-2 bg-black bg-opacity-50 text-white px-2 py-1 rounded text-xs">
              +{{ service.images.length - 1 }} more
            </div>
          </div>
          
          <!-- Service Content -->
          <div class="p-4">
            <div class="flex justify-between items-start mb-2">
              <h3 class="text-lg font-semibold text-gray-800">{{ service.title }}</h3>
              <span 
                class="text-sm font-medium px-2 py-1 rounded-full"
                :class="service.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
              >
                {{ service.is_active ? 'Active' : 'Inactive' }}
              </span>
            </div>
            
            <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ service.description }}</p>
            
            <div class="flex justify-between items-center">
              <span class="text-lg font-bold text-blue-600">ZKW{{ service.price }}</span>
              <button 
                class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700 transition flex items-center gap-1"
                @click.stop="inquireOnWhatsApp(service)"
                >
                <WhatsAppOutlined class="text-white" />
                WhatsApp
                </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  
    <!-- Image Gallery Modal -->
    <div 
      v-if="showGallery" 
      class="fixed inset-0 bg-black bg-opacity-90 z-50 flex items-center justify-center p-4"
      @click.self="closeGallery"
    >
      <div class="relative max-w-4xl w-full">
        <!-- Close Button -->
        <button 
          @click="closeGallery"
          class="absolute top-4 right-4 text-white text-2xl z-10 hover:text-gray-300"
        >
          &times;
        </button>
        
        <!-- Main Image -->
        <img 
          :src="currentImage" 
          class="w-full max-h-[80vh] object-contain mx-auto"
          alt="Service image"
        >
        
        <!-- Navigation Arrows -->
        <button 
          v-if="canNavigatePrev"
          @click.stop="prevImage"
          class="absolute left-4 top-1/2 -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-70"
        >
          &larr;
        </button>
        <button 
          v-if="canNavigateNext"
          @click.stop="nextImage"
          class="absolute right-4 top-1/2 -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-70"
        >
          &rarr;
        </button>
        
        <!-- Image Counter -->
        <div class="text-white text-center mt-4">
          {{ currentImageIndex + 1 }} / {{ currentServiceImages.length }}
        </div>
      </div>
    </div>
  
    <AppFooter />
  </template>

  <style scoped>
  .line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }
  </style>