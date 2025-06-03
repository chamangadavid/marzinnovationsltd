<script setup>
import { ref } from 'vue'
import { useForm, Head, Link } from '@inertiajs/vue3';
import { notification } from 'ant-design-vue';
import axios from 'axios'


const loading = ref(false)
// Using Inertia's form helper
const form = useForm({
    fullName: '',
    email: '',
    subject: '',
    message: '',
});

const submitForm = () => {
    loading.value = true

    axios.post('/contact', {
        fullName: form.fullName,
        email: form.email,
        subject: form.subject,
        message: form.message,
    })
    .then(response => {
        notification.success({
            message: 'Success',
            description: response.data.message || 'Thank you for your message! We will get back to you shortly.',
            duration: 4.5,
        });

        form.reset()
    })
    .catch(error => {
        if (error.response && error.response.status === 422) {
            const errors = error.response.data.errors
            form.setErrors(errors) // assuming form is from useForm()

            notification.error({
                message: 'Form Validation Error',
                description: 'Please fix the following errors: ' +
                            Object.values(errors).flat().join(', '),
                duration: 5,
            });
        } else {
            notification.error({
                message: 'Submission Error',
                description: 'There was an error submitting the form. Please try again later.',
                duration: 4.5,
            });
        }
    })
    .finally(() => {
        loading.value = false
    });
};

</script>

<template>
    <Head title="Contact" />

    <div class="font-sans antialiased text-gray-800">
      <section class="relative bg-cover bg-center h-64 flex items-center justify-center text-white"
        style="background-image: url('/assets/contact.jpg');">
        <div class="absolute inset-0 bg-black opacity-50"></div>
        <div class="relative z-10 text-center">
            <h1 class="text-5xl font-bold mb-2">Contact Us</h1>
            <p class="text-lg">Home / Contact Us</p>
        </div>
    </section>
    <div class="px-5 mt-4">
        <Link href="/" class="inline-flex items-center text-blue-600 hover:underline text-base font-medium">
          <span class="mr-2 text-xl">←</span>
          Back to Home Page
        </Link>
      </div>

  
      <section class="container mx-auto px-5 py-16">
        <div class="flex flex-col md:flex-row gap-12">
          <div class="w-full md:w-1/2 bg-white p-8 rounded-lg shadow-lg">
            <h2 class="text-3xl font-bold mb-6 text-gray-900">Write</h2>
            <form @submit.prevent="submitForm">
              <div class="mb-6">
                <label for="fullName" class="block text-gray-700 text-sm font-semibold mb-2">Your Name</label>
                <input type="text" id="fullName" v-model="form.fullName"
                  class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="Full Name" required>
              </div>
              <div class="mb-6">
                <label for="email" class="block text-gray-700 text-sm font-semibold mb-2">Your Email</label>
                <input type="email" id="email" v-model="form.email"
                  class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="Email Address" required>
              </div>
              <div class="mb-6">
                <label for="subject" class="block text-gray-700 text-sm font-semibold mb-2">Subject</label>
                <input type="text" id="subject" v-model="form.subject"
                  class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="Subject" required>
              </div>
              <div class="mb-6">
                <label for="message" class="block text-gray-700 text-sm font-semibold mb-2">Your Message</label>
                <textarea id="message" v-model="form.message" rows="6"
                  class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="Message" required></textarea>
              </div>
              <a-button type="primary" :loading="loading" @click="submitForm">
                Submit
              </a-button>
            </form>
          </div>
  
          <div class="w-full md:w-1/2 bg-white p-8 rounded-lg shadow-lg">
            <h2 class="text-3xl font-bold mb-6 text-gray-900">Get In Touch</h2>
            <p class="text-gray-600 mb-8 leading-relaxed" style="text-align: justify;">
                At Marz Innovations Limited we specialize in creating bold and memorable brand identities through expert branding and graphic design services. 
                Whether you're launching a new business or refreshing your current image, our team is here to craft visual solutions that capture attention 
                and communicate your message clearly. Let’s bring your brand to life—get in touch with us today!
            </p>
  
            <div class="space-y-6 mb-8">
              <div class="flex items-center">
                <div class="flex-shrink-0 w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mr-4">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.63A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                </div>
                <div>
                  <h3 class="font-semibold text-gray-900">Call Us</h3>
                  <p class="text-gray-600">+260 966 390 807</p>
                </div>
              </div>
  
              <div class="flex items-center">
                <div class="flex-shrink-0 w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mr-4">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-mail"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                </div>
                <div>
                  <h3 class="font-semibold text-gray-900">Email Us</h3>
                  <p class="text-gray-600">marzinnov8s@gmail.com</p>
                </div>
              </div>
  
              <div class="flex items-center">
                <div class="flex-shrink-0 w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mr-4">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-globe"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/></svg>
                </div>
                <div>
                  <h3 class="font-semibold text-gray-900">Website</h3>
                  <p class="text-gray-600">www.marzinnovationsltd.com</p>
                </div>
              </div>
  
              <div class="flex items-center">
                <div class="flex-shrink-0 w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mr-4">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin"><path d="M12 12.72a1 1 0 1 0 0-1.44 1 1 0 0 0 0 1.44Z"/><path d="M19.4 15c-.5 9.9-1.9 11.2-3.4 12.7-2.1 2.1-4.6 2.1-6.7 0-1.5-1.5-2.9-2.8-3.4-12.7C5.3 5 8.7 2 12 2s6.7 3 7.4 13Z"/></svg>
                </div>
                <div>
                  <h3 class="font-semibold text-gray-900">Address</h3>
                  <p class="text-gray-600">Permanent House, 2nd floor Block T Room 253A Cairo Road Lusaka</p>
                </div>
              </div>
            </div>
  
            <div>
              <h3 class="font-semibold text-gray-900 mb-4">Follow Us On</h3>
              <div class="flex space-x-4">
                <a href="#" class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center hover:bg-blue-700 transition-colors duration-300">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M14 13.5h2.5l1-4H14v-2c0-1.03 0-2 2-2h3V2h-3c-3.87 0-5 3.13-5 7v4.5zM22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.84 3.44 8.87 8 9.8V15h-2v-3h2V9.5C10 7.57 11.57 6 13.5 6H17v3h-2c-.55 0-1 .45-1 1v2h3.5l-1 4H14V22c-4.56-.93-8-4.96-8-9.8 0-4.42 3.58-8 8-8s8 3.58 8 8"/></svg>
                </a>
                <a href="#" class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center hover:bg-blue-700 transition-colors duration-300">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.37-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.25 7.47 3.2 4.75c-.37.63-.58 1.37-.58 2.17 0 1.49.75 2.81 1.91 3.56-.7-.02-1.36-.2-1.93-.5v.05c0 2.08 1.48 3.82 3.44 4.2-.36.1-.73.15-1.1.15-.27 0-.53-.03-.79-.08.55 1.7 2.14 2.93 4.02 2.97-1.47 1.15-3.33 1.84-5.35 1.84-.35 0-.69-.02-1.03-.06C3.17 20.37 5.4 21 7.8 21c8.45 0 13.07-6.98 13.07-13.07 0-.2-.01-.4-.02-.6.9-.65 1.67-1.47 2.29-2.4z"/></svg>
                </a>
                <a href="#" class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center hover:bg-blue-700 transition-colors duration-300">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M7.8 2h8.4C19.4 2 22 4.6 22 7.8v8.4c0 3.2-2.6 5.8-5.8 5.8H7.8C4.6 22 2 19.4 2 16.2V7.8C2 4.6 4.6 2 7.8 2zm-.2 2A2.8 2.8 0 0 0 5 7.8v8.4c0 1.54 1.26 2.8 2.8 2.8h8.4a2.8 2.8 0 0 0 2.8-2.8V7.8A2.8 2.8 0 0 0 16.2 5H7.6z"/><path d="M12 7.5C9.5 7.5 7.5 9.5 7.5 12s2 4.5 4.5 4.5 4.5-2 4.5-4.5-2-4.5-4.5-4.5zm0 2a2.5 2.5 0 0 1 2.5 2.5c0 1.38-1.12 2.5-2.5 2.5S9.5 13.38 9.5 12s1.12-2.5 2.5-2.5zM17.25 6.25c-.69 0-1.25.56-1.25 1.25s.56 1.25 1.25 1.25 1.25-.56 1.25-1.25-.56-1.25-1.25-1.25z"/></svg>
                </a>
                <a href="#" class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center hover:bg-blue-700 transition-colors duration-300">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                </a>
              </div>
            </div>
          </div>
        </div>
      </section>
  
        <section class="w-full">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3846.2669301014835!2d28.27688937078971!3d-15.416137880960099!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1940f30037258aa7%3A0x2840383e00a328ba!2sPermanent%20house!5e0!3m2!1sen!2szm!4v1748444539131!5m2!1sen!2szm"
                width="100%"
                height="450"
                style="border:0;"
                allowfullscreen=""
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
            ></iframe>
        </section>
        <!-- footer -->
        <AppFooter />
    
      <!-- <section class="w-full">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3846.2669301014835!2d28.27688937078971!3d-15.416137880960099!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1940f30037258aa7%3A0x2840383e00a328ba!2sPermanent%20house!5e0!3m2!1sen!2szm!4v1748444539131!5m2!1sen!2szm" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        <img src="https://placehold.co/1200x400/cccccc/333333?text=Map+Placeholder" alt="Location Map" class="w-full h-auto object-cover">
      </section> -->
    </div>
  </template>
  
  <style>
  /* You can add custom styles here if needed, but Tailwind CSS should handle most of it. */
  /* For example, if you want a specific font: */
  /* body {
    font-family: 'Inter', sans-serif;
  } */
  </style>