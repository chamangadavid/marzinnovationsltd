<script setup>
import { Head, Link } from '@inertiajs/vue3';

</script>


<template>
    <Head title="Contact" />

    <footer class="bg-gray-200 text-gray-700 pt-16 pb-8">
        <div class="container mx-auto px-5">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 pb-10 border-b border-gray-300">
                <div>
                    <h4 class="font-semibold text-lg mb-4 relative pb-2 text-gray-800 footer-heading">About Company</h4>
                    <p class="text-sm leading-relaxed" style="text-align:justify">
                        We are a Digital abd Branding Compnay that strives to grow the industry by providing quality productsn& services.
                    </p>
                </div>

                <div>
                    <h4 class="font-semibold text-lg mb-4 relative pb-2 text-gray-800 footer-heading">Quick Links</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-hero-purple transition-colors duration-200">Our History</a></li>
                        <li><a href="#" class="hover:text-hero-purple transition-colors duration-200">Services</a></li>
                        <li><a href="#" class="hover:text-hero-purple transition-colors duration-200">Features</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-semibold text-lg mb-4 relative pb-2 text-gray-800 footer-heading">Features</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-hero-purple transition-colors duration-200">Home</a></li>
                        <li><a href="#" class="hover:text-hero-purple transition-colors duration-200">About Us</a></li>
                        <li><a href="#" class="hover:text-hero-purple transition-colors duration-200">Features</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-semibold text-lg mb-4 relative pb-2 text-gray-800 footer-heading">Contact Info</h4>
                    <ul class="space-y-3 text-sm">
                        <li class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-hero-purple" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                            </svg>
                            Permanent House Room 253A Cairo
                        </li>
                        <li class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-hero-purple" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0018 4H2a2 2 0 00-.003 1.884z" />
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                            </svg>
                            marzinnov8@gmail.com
                        </li>
                        <li class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-hero-purple" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.774a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                            </svg>
                            +260 966 390 807
                        </li>
                    </ul>
                </div>
            </div>

            <div class="flex flex-col md:flex-row justify-between items-center py-8">
                <h3 class="text-xl font-semibold mb-4 md:mb-0 text-gray-800">Subscribe to the latest Newsletters</h3>
                <div class="flex w-full md:w-auto">
                    <input type="email" placeholder="Your email" class="flex-grow py-2 px-4 rounded-l-md border border-gray-300 focus:ring-hero-purple focus:border-hero-purple outline-none">
                    <button class="bg-blue-600 text-white px-6 py-2 rounded-r-md font-semibold hover:bg-blue-700 transition-colors duration-300">Subscribe</button>
                </div>
            </div>
        </div>

        <div class="bg-gray-300 py-4">
            <div class="container mx-auto px-5 flex flex-col md:flex-row justify-between items-center text-sm text-gray-600">
                <p class="mb-2 md:mb-0">© Copyright 2025. All Rights Reserved</p>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-600 hover:text-hero-purple transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.847c-4.013 0-4.433 2.379-4.433 4.267v2.733z"/>
                        </svg>
                    </a>
                    <a href="#" class="text-gray-600 hover:text-hero-purple transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.103 0-5.597 2.838-4.993 5.787-4.143-.207-7.822-2.185-10.285-5.181-.426.72-.666 1.56-.666 2.47 0 1.637.833 3.067 2.091 3.905-.77-.024-1.49-.234-2.11-.583v.03c0 3.067 2.202 5.617 5.102 6.182-.537.146-1.106.223-1.69.223-.412 0-.81-.04-1.197-.115.811 2.544 3.167 4.385 5.955 4.436-2.198 1.727-4.974 2.766-8 2.766-.525 0-1.04-.03-1.547-.09 2.858 1.834 6.237 2.909 9.893 2.909 11.854 0 18.332-9.884 18.332-18.514 0-.281-.006-.562-.019-.842.8-.577 1.493-1.299 2.046-2.124z"/>
                        </svg>
                    </a>
                    <a href="#" class="text-gray-600 hover:text-hero-purple transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm-.01 17c-2.761 0-5-2.239-5-5s2.239-5 5-5 5 2.239 5 5-2.239 5-5 5z"/>
                        </svg>
                    </a>
                    <a href="#" class="text-gray-600 hover:text-hero-purple transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.529-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>
</template>


<style scoped>
.footer-heading::after {
    content: '';
    display: block;
    width: 50px; /* Adjust as needed */
    height: 3px;
    background-color: #6A0DAD; /* Use your hero-purple color */
    margin-top: 8px; /* Space between heading and line */
}
</style>