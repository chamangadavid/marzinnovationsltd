<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AppFooter from '@/Components/AppFooter.vue'
</script>

<template>
  <Head title="About Us" />

  <!-- Hero Section -->
  <section class="relative bg-cover bg-center h-72" style="background-image: url('/assets/branding/one.jpg')">
    <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
      <div class="text-center text-white">
        <h1 class="text-4xl font-bold">About Us</h1>
        <div class="mt-2 text-sm">
          <Link href="/" class="hover:underline text-white">Home</Link>
          <span class="mx-2">•</span>
          <span>About</span>
        </div>
      </div>
    </div>
  </section>

  <div class="px-5 mt-4">
    <Link href="/" class="inline-flex items-center text-purple-500 hover:underline text-base font-medium">
      <span class="mr-2 text-xl">←</span>
      Back to Home Page
    </Link>
  </div>

  <!-- Content Section -->
  <section class="py-16 px-4 md:px-20 bg-white">
    <div class="grid md:grid-cols-2 gap-10 items-center">
      <!-- Left Text -->
      <div>
        <h2 class="text-purple-700 font-semibold mb-2">About Marz Innovation Ltd</h2>
        <h3 class="text-2xl md:text-3xl font-bold mb-4">WHO WE ARE?</h3>
        <p class="text-gray-700 leading-relaxed mb-6" style="text-align: justify;">
          We are a Digital and Branding Company that strives to grow the industry by providing quality products and services. 
          We are attentive to the needs of clients and provide them solutions that are suitable for their brands. help them 
          grow & reach their full potential. We also help you discover your target audience and give you strategies that 
          will help reach them.

          <br /><br />
          We are passionate about working with difference companies to create a meaningful customer relation. 
          The client's vision, goals and profits are always our first priority.
          <br /><br />
          results by bringing an entrepreneurial edge to your business. consistently delivering quality services to our clients is a key factor to building and growing our client's business
        </p>
        <div class="flex gap-8 mt-8">
          <div>
            <h4 class="text-xl font-bold text-blue-600">2k+</h4>
            <p class="text-sm text-gray-600">Satisfied clients</p>
          </div>
          <div>
            <h4 class="text-xl font-bold text-blue-600">5k+</h4>
            <p class="text-sm text-gray-600">Projects Delivered</p>
          </div>
          <div>
            <h4 class="text-xl font-bold text-blue-600">10+</h4>
            <p class="text-sm text-gray-600">Years of Experience</p>
          </div>
        </div>
      </div>

      <!-- Right Image -->
      <div>
        <img
          src="/assets/branding/four.jpg"
          alt="Team brainstorming with sticky notes"
          class="rounded-lg shadow-lg"
        />
      </div>
    </div>
  </section>
  <AppFooter />
  
</template>

<style>
/* No additional CSS needed if Tailwind is available */
</style>
