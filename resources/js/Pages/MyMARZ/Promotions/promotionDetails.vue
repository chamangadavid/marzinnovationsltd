<script setup>
import { defineProps } from 'vue';
import { Head } from '@inertiajs/vue3';
import AppFooter from '@/Components/AppFooter.vue';

const props = defineProps({
  promotion: Object
});

const formatDate = (dateStr) => {
  const date = new Date(dateStr);
  return date.toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  });
};

const formatDateRange = (startStr, endStr) => {
  const start = new Date(startStr);
  const end = new Date(endStr);
  
  if (start.getMonth() === end.getMonth() && start.getFullYear() === end.getFullYear()) {
    return `${start.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })} - ${end.getDate()}, ${end.getFullYear()}`;
  }
  else if (start.getFullYear() === end.getFullYear()) {
    return `${start.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })} - ${end.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })}, ${end.getFullYear()}`;
  }
  else {
    return `${start.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })} - ${end.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}`;
  }
};
</script>


<template>
    <Head title="Promotion" />
   <header  
    class="h-64 mb-6 bg-cover bg-center relative" 
    :style="{ backgroundImage: `url('/assets/contact.jpg')` }"
    >
    <div class="max-w-4xl mx-auto px-4 flex flex-col justify-center h-full relative z-10 space-y-4 text-center">
        <h2 class="text-4xl font-bold text-white drop-shadow-lg mb-0">Promotion Details</h2>
        
        <nav class="text-lg font-semibold text-white drop-shadow-lg space-x-2">
        <a href="/" class="hover:underline">Home</a> 
        <span class="mx-2">/</span>
        <a href="/" class="hover:underline">Promotions</a> 
        <span class="mx-2">/</span>
        <span>{{ promotion.title }}</span>
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
 
    <div class="max-w-4xl mx-auto py-8 px-4">
      <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <!-- Image removed -->
  
        <div class="p-6">
          <div class="flex justify-between items-center mb-4">
            <span class="text-sm font-medium px-3 py-1 bg-blue-100 text-blue-800 rounded-full">
              {{ formatDateRange(promotion.start_date, promotion.end_date) }}
            </span>
          </div>
          
          <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ promotion.title }}</h1>
          
          <div class="prose max-w-none text-gray-700 mb-6" v-html="promotion.description"></div>
          
          <div class="border-t border-gray-200 pt-4">
            <p class="text-sm text-gray-500">
              <span class="font-medium">Valid:</span> 
              {{ formatDate(promotion.start_date) }} to {{ formatDate(promotion.end_date) }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <AppFooter />
  </template>

