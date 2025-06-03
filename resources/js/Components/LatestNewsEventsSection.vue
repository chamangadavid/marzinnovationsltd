<script setup>
import { Link } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import axios from 'axios';

const newsList = ref([]);
const events = ref([]);
const currentIndex = ref(0);
const currentDate = ref(new Date());
let interval = null;
let dateInterval = null;

const fetchNews = async () => {
  try {
    const response = await axios.get('/getLatestNews', {
      params: { is_published: 1 },
    });

    newsList.value = response.data.data.map(item => ({
      id: item.id,
      title: item.title,
      description: item.description,
      created_at: item.created_at,
      image: item.images && item.images.length ? item.images[0] : '/placeholder.jpg',
    }));
  } catch (error) {
    console.error('Failed to fetch news', error);
  }
};

const fetchPromotions = async () => {
  try {
    const response = await axios.get('/getPromotions');
    events.value = response.data || [];
  } catch (error) {
    console.error('Failed to fetch promotions', error);
  }
};

const activeEvents = computed(() => {
  return events.value.filter(event => {
    const endDate = new Date(event.end_date);
    return endDate >= currentDate.value;
  });
});

const startAutoSlide = () => {
  interval = setInterval(() => {
    if (newsList.value.length) {
      currentIndex.value = (currentIndex.value + 1) % newsList.value.length;
    }
  }, 5000);
};

const stopAutoSlide = () => {
  if (interval) clearInterval(interval);
};

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

onMounted(() => {
  fetchNews();
  fetchPromotions();
  startAutoSlide();
  
  // Update current time every minute to re-evaluate active events
  dateInterval = setInterval(() => {
    currentDate.value = new Date();
  }, 60000);
});

onUnmounted(() => {
  stopAutoSlide();
  clearInterval(dateInterval);
});



</script>

<template>
    <section class="py-16 bg-gray-50">
      <div class="container mx-auto px-5 grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- News Section -->
        <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-sm border border-gray-100">
          <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-2">Latest Works</h2>
  
          <div class="flex flex-col lg:flex-row gap-6">
            <!-- Main News -->
            <div class="lg:w-2/3">
              <transition name="fade" mode="out-in">
                <div v-if="newsList.length" :key="newsList[currentIndex].id" class="mb-4">
                    <Link :href="route('news.showNews', { id: newsList[currentIndex].id })">
                      <img
                        :src="newsList[currentIndex].image"
                        :alt="newsList[currentIndex].title"
                        class="w-full h-64 object-cover rounded-lg mb-4 shadow-sm"
                      />
                      <p class="text-sm text-gray-500 mb-2">
                        {{ formatDate(newsList[currentIndex].created_at) }}
                      </p>
                      
                      <h3 class="text-xl font-semibold text-gray-800 mb-2">
                        {{
                          newsList[currentIndex].title
                            ? newsList[currentIndex].title.slice(0, 30) +
                              (newsList[currentIndex].title.length > 30 ? '...' : '')
                            : ''
                        }}
                      </h3>
                      
                      <p class="text-gray-600 text-base leading-snug">
                        {{
                          newsList[currentIndex].description
                            ? newsList[currentIndex].description.slice(0, 60) +
                              (newsList[currentIndex].description.length > 60 ? '...' : '')
                            : ''
                        }}
                      </p>
                      
                      <!-- <p class="text-sm text-gray-500 mb-2">{{ formatDate(newsList[currentIndex].created_at) }}</p>
                      <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ newsList[currentIndex].title }}</h3>
                      <p class="text-gray-600 text-base leading-snug">{{ newsList[currentIndex].description }}</p> -->
                    </Link>
                  </div>
              </transition>
            </div>
  
            <!-- Thumbnail List -->
            <div class="lg:w-1/3 flex flex-col justify-center">
              <div class="relative h-64 overflow-hidden">
                <transition-group name="slide-fade" tag="div" class="absolute inset-0">
                  <div
                    v-for="(item, index) in newsList"
                    :key="item.id"
                    v-show="index === currentIndex"
                    class="absolute inset-0 flex items-start gap-4 p-2 flex-row-reverse"
                  >
                    <img
                      v-if="item.image"
                      :src="item.image"
                      :alt="item.title"
                      class="w-16 h-16 object-cover rounded-lg flex-shrink-0 shadow-sm"
                    />
                    <div>
                        <p class="text-xs text-gray-500 mb-1">{{ formatDate(item.created_at) }}</p>
                        <h3 class="text-base font-semibold text-gray-800 mb-1">
                          {{ item.title ? item.title.substr(0, 15) + (item.title.length > 15 ? '...' : '') : '' }}
                        </h3>
                        <p class="text-sm text-gray-600 leading-tight line-clamp-3">
                          {{ item.description ? item.description.substr(0, 20) + (item.description.length > 20 ? '...' : '') : '' }}
                        </p>
                      </div>
                  </div>
                </transition-group>
              </div>
            </div>
          </div>
        </div>
  
        <!-- Promotion Section -->
        <div class="lg:col-span-1 bg-white p-6 rounded-lg shadow-sm border border-gray-100">
          <div class="flex justify-between items-center mb-4 border-b pb-2">
            <h2 class="text-2xl font-bold text-gray-800">Current Promotions</h2>
          </div>
          
          <div class="h-80 overflow-y-auto pr-2 promo-scrollbar">
            <div v-if="activeEvents.length === 0" class="flex items-center justify-center h-full">
              <p class="text-gray-500 text-center">No active promotions at this time.<br>Check back soon for updates!</p>
            </div>
            
            <div v-else class="space-y-4">
                <div v-for="event in activeEvents" :key="event.id" class="bg-gray-50 rounded-lg p-4 border border-gray-200 hover:border-blue-300 transition-colors">
                        <Link :href="route('promotions.showPromotion', { id: event.id })" class="block">
                      <div class="flex justify-between items-start mb-2">
                        <span class="text-xs font-semibold px-2 py-1 bg-blue-100 text-blue-800 rounded-full">
                          {{ formatDateRange(event.start_date, event.end_date) }}
                        </span>
                      </div>
                      <h3 class="font-bold text-gray-800 mb-1">
                        {{ event.title ? event.title.substr(0, 30) + (event.title.length > 30 ? '...' : '') : '' }}
                      </h3>
                      <p class="text-sm text-gray-600">
                        {{ event.description ? event.description.substr(0, 60) + (event.description.length > 60 ? '...' : '') : '' }}
                      </p>
                      
                      <!-- <h3 class="font-bold text-gray-800 mb-1">{{ event.title }}</h3>
                      <p class="text-sm text-gray-600">{{ event.description }}</p> -->
                      <div class="mt-3 flex justify-between items-center text-xs text-gray-500">
                        <span>Valid: {{ formatDate(event.start_date) }}</span>
                        <span>to {{ formatDate(event.end_date) }}</span>
                      </div>
                    </Link>
                  </div>
              <!-- <div v-for="event in activeEvents" :key="event.id" class="bg-gray-50 rounded-lg p-4 border border-gray-200 hover:border-blue-300 transition-colors">
                <div class="flex justify-between items-start mb-2">
                  <span class="text-xs font-semibold px-2 py-1 bg-blue-100 text-blue-800 rounded-full">
                    {{ formatDateRange(event.start_date, event.end_date) }}
                  </span>
                </div>
                <h3 class="font-bold text-gray-800 mb-1">{{ event.title }}</h3>
                <p class="text-sm text-gray-600">{{ event.description }}</p>
                <div class="mt-3 flex justify-between items-center text-xs text-gray-500">
                  <span>Valid: {{ formatDate(event.start_date) }}</span>
                  <span>to {{ formatDate(event.end_date) }}</span>
                </div>
              </div> -->

            </div>
          </div>
        </div>
      </div>
    </section>
</template>

<style scoped>
/* CSS for the slide-fade transition (for small news items) */
.slide-fade-enter-active,
.slide-fade-leave-active {
    transition: all 0.8s ease;
}

.slide-fade-enter-from {
    opacity: 0;
    transform: translateY(100%);
}
.slide-fade-leave-to {
    opacity: 0;
    transform: translateY(-100%);
}

.slide-fade-leave-active {
    position: absolute;
}

/* CSS for the main news image/content fade transition */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.5s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

/* Promotion card hover effect */
.promo-card {
  transition: all 0.2s ease;
}
.promo-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
}

/* Custom scrollbar styling */
.promo-scrollbar::-webkit-scrollbar {
  width: 6px;
}
.promo-scrollbar::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}
.promo-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e0;
  border-radius: 10px;
}
.promo-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #a0aec0;
}

.bg-gray-50:hover {
    background-color: #f8fafc;
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    transition: all 0.2s ease;
  }
</style>