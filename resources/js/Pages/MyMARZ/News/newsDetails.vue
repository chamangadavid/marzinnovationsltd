<script setup>
import { defineProps } from 'vue';
import { Head } from '@inertiajs/vue3';
import AppFooter from '@/Components/AppFooter.vue';

const props = defineProps({
  news: Object
});

const formatDate = (dateStr) => {
  const date = new Date(dateStr);
  return date.toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  });
};

// Get first image or fallback
const mainImage = props.news.images?.length ? props.news.images[0] : '/assets/thumbnail.jpg';
</script>

<template>
  <Head title="News" />
  
  <header  
    class="h-64 mb-6 bg-cover bg-center relative" 
    :style="{ backgroundImage: `url('${mainImage}')` }"
  >
    <div class="max-w-4xl mx-auto px-4 flex flex-col justify-center h-full relative z-10 space-y-4 text-center">
      <h2 class="text-4xl font-bold text-white drop-shadow-lg mb-0">News Details</h2>
      
      <nav class="text-lg font-semibold text-white drop-shadow-lg space-x-2">
        <a href="/" class="hover:underline">Home</a> 
        <span class="mx-2">/</span>
        <a href="/news" class="hover:underline">News</a> 
        <span class="mx-2">/</span>
        <span>{{ news.title }}</span>
      </nav>
    </div>

    <a 
      href="/" 
      class="absolute bottom-4 left-4 bg-white bg-opacity-80 text-gray-800 font-semibold px-4 py-2 rounded hover:bg-opacity-100 transition z-20"
    >
      &larr; Back to Home
    </a>

    <div class="absolute inset-0 bg-black opacity-40"></div>
  </header>

  <div class="max-w-4xl mx-auto py-8 px-4">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
      <!-- Show image(s) -->
      <div v-if="news.images?.length" class="w-full">
        <img 
          v-for="(img, index) in news.images" 
          :key="index"
          :src="img" 
          alt="News Image"
          class="w-full h-64 object-cover mb-4"
        />
      </div>
      <div v-else class="w-full">
        <img 
          src="/assets/thumbnail.jpg" 
          alt="Default Thumbnail" 
          class="w-full h-64 object-cover mb-4"
        />
      </div>

      <div class="p-6">
        <div class="flex justify-between items-center mb-4">
          <span class="text-sm font-medium px-3 py-1 bg-blue-100 text-blue-800 rounded-full">
            {{ formatDate(news.created_at) }}
          </span>
        </div>
        
        <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ news.title }}</h1>
        
        <div class="prose max-w-none text-gray-700 mb-6" v-html="news.description"></div>
        
        <div class="border-t border-gray-200 pt-4">
          <p class="text-sm text-gray-500">
            <span class="font-medium">Created at:</span> 
            {{ formatDate(news.created_at) }} 
          </p>
        </div>
      </div>
    </div>
  </div>
  <AppFooter />
</template>
