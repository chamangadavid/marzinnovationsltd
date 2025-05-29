<template>
    <section class="py-16 bg-gray-100">
        <div class="container mx-auto px-5 grid grid-cols-1 lg:grid-cols-3 gap-8">

            <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Latest Works</h2>

                <div class="flex flex-col lg:flex-row gap-6">

                    <div class="lg:w-2/3">
                        <transition name="fade" mode="out-in">
                            <div :key="smallNews[currentSmallNewsIndex].id" class="mb-4 lg:mb-0">
                                <img :src="smallNews[currentSmallNewsIndex].mainImageSrc"
                                     :alt="smallNews[currentSmallNewsIndex].title"
                                     class="w-full h-64 object-cover rounded-lg mb-4">
                                <p class="text-sm text-gray-500 mb-2">{{ smallNews[currentSmallNewsIndex].date }}</p>
                                <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ smallNews[currentSmallNewsIndex].title }}</h3>
                                <p class="text-gray-600 text-base leading-snug">{{ smallNews[currentSmallNewsIndex].description }}</p>
                            </div>
                        </transition>
                    </div>

                    <div class="lg:w-1/3 flex flex-col justify-center">
                        <div class="relative h-64 overflow-hidden">
                            <transition-group name="slide-fade" tag="div" class="absolute inset-0">
                                <div
                                    v-for="(newsItem, index) in smallNews"
                                    :key="newsItem.id"
                                    v-show="index === currentSmallNewsIndex"
                                    class="absolute inset-0 flex items-start gap-4 p-2 flex-row-reverse"
                                >
                                    <img v-if="newsItem.smallImageSrc" :src="newsItem.smallImageSrc" :alt="newsItem.title" class="w-16 h-16 object-cover rounded-lg flex-shrink-0">
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">{{ newsItem.date }}</p>
                                        <h3 class="text-base font-semibold text-gray-800 mb-1">{{ newsItem.title }}</h3>
                                        <p class="text-sm text-gray-600 leading-tight">{{ newsItem.description }}</p>
                                    </div>
                                </div>
                            </transition-group>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1 bg-white text-gray-800 p-6 rounded-lg shadow-md">
                <h2 class="text-2xl font-bold mb-6">Upcoming Promotion</h2>
                
                <div class="h-80 overflow-y-auto border border-gray-200 rounded-lg">
                    <table class="min-w-full bg-white">
                        <thead>
                            <tr class="sticky top-0 bg-gray-100 text-gray-700 uppercase text-sm leading-normal z-10">
                                <th class="py-3 px-6 text-left">Date</th>
                                <th class="py-3 px-6 text-left">Promotion</th> </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm font-light">
                            <tr v-if="events.length === 0">
                                <td colspan="2" class="py-4 px-6 text-center text-gray-500">No Promotions Scheduled.</td> </tr>
                            <tr v-for="event in events" :key="event.id" class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="py-3 px-6 text-left whitespace-nowrap">
                                    <span class="font-medium">{{ event.date }}</span>
                                </td>
                                <td class="py-3 px-6 text-left">
                                    <p class="font-semibold">{{ event.title }}</p>
                                    <p class="text-xs text-gray-500">{{ event.location }}</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>

        </div>
    </section>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const smallNews = ref([
    {
        id: 1,
        date: 'MAY 24, 2025', // This was previously the static main news, now it's the first in the slider
        title: 'CAA DIRECTOR GENERAL, CAPTAIN DERRICK LUJEMBE TAKES OVER CHAIRMANSHIP OF THE AFPP STEERING COMMITTEE',
        description: 'The Zambia Civil Aviation Authority Drecto...',
        mainImageSrc: '/assets/news/news-main-1.jpeg', // Main image for this item
        smallImageSrc: '/assets/news/news-main-1.jpeg' // Small thumbnail for this item (optional)
    },
    {
        id: 2,
        date: 'APR 29, 2025',
        title: 'ICAO LAUNCHES SAFETY MANAGEMENT AND OVERSIGHT SUB-GROUP IN NAIROBI',
        description: 'The International Civil Aviation Organiza...',
        mainImageSrc: '/assets/news/news-main-2.jpeg', // Main image for this item
        smallImageSrc: '/assets/news/news-main-2.jpeg' // Small thumbnail for this item (optional)
    },
    {
        id: 3,
        date: 'FEB 24, 2025',
        title: 'ZAMBIAN GOVERNMENT AWARDS CONTRACTS FOR THE CONSTRUCTION OF NAKONDE AND REHABILITATION OF MFULWE AIRPORT',
        description: 'The Zambia Government, through the Ministry of Transport and Logistics today signed contr...',
        mainImageSrc: '/assets/news/news-main-3.jpeg', // Main image for this item
        smallImageSrc: '/assets/news/news-main-3.jpeg' // Small thumbnail for this item (optional)
    },
    {
        id: 4, // Added a fourth item to ensure the slider cycles more effectively
        date: 'FEB 24, 2025',
        title: 'ZAMBIAN GOVERNMENT AWARDS CONTRACTS FOR THE CONSTRUCTION OF NAKONDE AND REHABILITATION OF MFULWE AIRPORT (CONT.)',
        description: 'The Zambia Government, through the Mini...',
        mainImageSrc: '/assets/news/news-main-4.jpeg', // Main image for this item
        smallImageSrc: '/assets/news/news-main-4.jpeg' // Small thumbnail for this item (optional)
    }

   
]);

// Event data (now representing promotions)
const events = ref([
    { id: 1, date: '2025-06-15', title: 'Early Bird Flight Discounts', location: 'All Destinations' },
    { id: 2, date: '2025-07-20', title: 'Holiday Package Deals', location: 'Coastal Routes' },
    { id: 3, date: '2025-08-01', title: 'Business Class Upgrade Offer', location: 'Select International Flights' },
    { id: 4, date: '2025-09-10', title: 'Student Travel Savings', location: 'Domestic Flights' },
    { id: 5, date: '2025-10-05', title: 'Loyalty Program Bonus Points', location: 'All Flights' },
    { id: 6, date: '2025-11-12', title: 'Family Fare Reductions', location: 'Regional Flights' },
    { id: 7, date: '2025-12-03', title: 'New Year Getaway Special', location: 'Popular Holiday Spots' },
    { id: 8, date: '2026-01-20', title: 'Winter Escape Packages', location: 'Warm Destinations' },
    { id: 9, date: '2026-02-28', title: 'Group Travel Discounts', location: 'Custom Itineraries' },
    { id: 10, date: '2026-03-17', title: 'First-time Flyer Welcome Bonus', location: 'Any Flight' },
]);

const currentSmallNewsIndex = ref(0);
let smallNewsIntervalId = null;

const nextSmallNews = () => {
    currentSmallNewsIndex.value = (currentSmallNewsIndex.value + 1) % smallNews.value.length;
};

onMounted(() => {
    smallNewsIntervalId = setInterval(nextSmallNews, 7000);
});

onUnmounted(() => {
    if (smallNewsIntervalId) {
        clearInterval(smallNewsIntervalId);
    }
});
</script>

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
</style>


<!-- <template>
    <section class="py-16 bg-gray-100">
        <div class="container mx-auto px-5 grid grid-cols-1 lg:grid-cols-3 gap-8">

            <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Latest News</h2>

                <div class="mb-8">
                    <img src="/assets/news/news-main-1.jpeg" alt="Latest News Image" class="w-full h-auto object-cover rounded-lg mb-4">
                    <p class="text-sm text-gray-500 mb-2">MAY 24, 2025</p>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">CAA DIRECTOR GENERAL, CAPTAIN DERRICK LUJEMBE TAKES OVER CHAIRMANSHIP OF THE AFPP STEERING COMMITTEE</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">The Zambia Civil Aviation Authority Drecto...</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">APR 29, 2025</p>
                        <h3 class="text-lg font-semibold text-gray-800 mb-1">ICAO LAUNCHES SAFETY MANAGEMENT AND OVERSIGHT SUB-GROUP IN NAIROBI</h3>
                        <p class="text-gray-600 text-sm leading-relaxed">The International Civil Aviation Organiza...</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">FEB 24, 2025</p>
                        <h3 class="text-lg font-semibold text-gray-800 mb-1">ZAMBIAN GOVERNMENT AWARDS CONTRACTS FOR THE CONSTRUCTION OF NAKONDE AND REHABILITATION OF MFULWE AIRPORT</h3>
                        <p class="text-gray-600 text-sm leading-relaxed">The Zambia Government, through the Ministry of Transport and Logistics today signed contr...</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">FEB 24, 2025</p>
                        <h3 class="text-lg font-semibold text-gray-800 mb-1">ZAMBIAN GOVERNMENT AWARDS CONTRACTS FOR THE CONSTRUCTION OF NAKONDE AND REHABILITATION OF MFULWE AIRPORT</h3>
                        <p class="text-gray-600 text-sm leading-relaxed">The Zambia Government, through the Mini...</p>
                    </div>
                    </div>
            </div>

            <div class="lg:col-span-1 bg-blue-700 text-white p-6 rounded-lg shadow-md h-fit">
                <h2 class="text-2xl font-bold mb-6">Events</h2>
                <div class="text-center py-4">
                    <p class="text-gray-200">No Events Available</p>
                    </div>
            </div>

        </div>
    </section>
</template>

<script setup>
</script>

<style scoped>
</style> -->