<script setup>
import { ref, watch, onMounted, onUnmounted } from 'vue';
import { debounce } from 'lodash';
import { router, usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';
import axios from 'axios'; // Make sure axios is imported

const page = usePage();
const auth = page.props.auth;


const can = (permission) => {
  if (auth?.user?.roles?.some(role => role.name === 'Super Admin')) {
    return true;
  }
  return auth?.permissions?.includes(permission);
};

// Function to check if user has a permission requires to pass auth like  v-if="can('manage access control', auth)"
// const can = (permission, auth) => {
//   return auth?.permissions?.includes(permission);
// };



const showingNavigationDropdown = ref(false);
const searchValue = ref('');
const searchResults = ref([]);
const showSuggestions = ref(false);
const isLoading = ref(false);

// Debounced search function
const debouncedSearch = debounce(async (value) => {
  if (value.length < 2) {
    searchResults.value = [];
    showSuggestions.value = false;
    return;
  }

  isLoading.value = true;
  try {
    const response = await axios.get(route('users.search'), {
      params: { query: value }
    });
    searchResults.value = response.data;
    showSuggestions.value = true;
  } catch (error) {
    console.error('Search error:', error);
    searchResults.value = [];
    showSuggestions.value = false;
  } finally {
    isLoading.value = false;
  }
}, 500);

// Watch for changes in searchValue
watch(searchValue, (newVal) => {
  if (newVal) {
    debouncedSearch(newVal);
  } else {
    searchResults.value = [];
    showSuggestions.value = false;
  }
});

// Select user handler
const selectUser = (user) => {
  router.visit(route('users.show', { user: user.id }));
  searchValue.value = '';
  searchResults.value = [];
  showSuggestions.value = false;
};

// Click outside handler to close suggestions
const handleClickOutside = (event) => {
  const searchContainer = document.querySelector('.search-container');
  if (searchContainer && !searchContainer.contains(event.target)) {
    showSuggestions.value = false;
  }
};

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
  <div>
    <div class="min-h-screen bg-gray-100">
      <nav class="bg-purple-900 text-white shadow rounded-lg px-6 mx-4 mt-3">
        <!-- Primary Navigation Menu -->
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <div class="flex h-16 justify-between">
            <div class="flex">
              <!-- Logo -->
              <div class="flex shrink-0 items-center">
                <Link :href="route('dashboard')">
                  <ApplicationLogo class="block h-9 w-auto fill-current text-white" />
                </Link>
              </div>

              <!-- Navigation Links -->
              <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                <NavLink
                  :href="route('dashboard')"
                  :active="route().current('dashboard')"
                  class="text-white"
                >
                  Dashboard
                </NavLink>
               
                  <NavLink
                    v-if="can('manage access control')"
                    :href="route('admin.rolesAndPermission')"
                    :active="route().current('admin.rolesAndPermission')"
                    class="text-white"
                  >
                    Roles & Permissions
                  </NavLink>
                
                  <!-- <span
                    v-if="auth.user.roles && auth.user.roles.length"
                    class="ms-2 text-xs bg-purple-100 text-purple-800 px-2 py-1 rounded-full"
                  >
                    {{ auth.user.roles[0].name }}
                  </span> -->


                <!-- <NavLink  
                  :href="route('admin.rolesAndPermission')"
                  :active="route().current('admin.rolesAndPermission')"
                  class="text-white"
                >
                  Roles & Permissions
                </NavLink> -->
              </div>
            </div>

            <div class="hidden sm:ms-6 sm:flex sm:items-center">
              <!-- Search Field with Suggestions -->
              <div class="ms-4 flex items-center search-container relative">
                <a-input
                  v-model:value="searchValue"
                  placeholder="Search user..."
                  allowClear
                  style="width: 350px; border-radius: 12px"
                  @focus="showSuggestions = searchResults.length > 0"
                />
                
                <!-- Suggestions Dropdown -->
                <div 
                  v-if="showSuggestions"
                  class="absolute top-full left-0 w-full mt-1 bg-white rounded-md shadow-lg z-50 max-h-60 overflow-auto border border-gray-200"
                >
                  <div 
                    v-for="user in searchResults" 
                    :key="user.id"
                    class="px-4 py-2 hover:bg-gray-100 cursor-pointer flex items-center search-suggestion-item"
                    @click="selectUser(user)"
                  >
                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
                      {{ user.name.charAt(0).toUpperCase() }}
                    </div>
                    <div class="ml-3">
                      <div class="font-medium text-gray-900">{{ user.name }}</div>
                      <div class="text-sm text-gray-500">{{ user.email }}</div>
                    </div>
                  </div>
                  <div v-if="isLoading" class="px-4 py-2 text-center text-gray-500">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-500 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Searching...
                  </div>
                  <div 
                    v-if="!isLoading && searchResults.length === 0 && searchValue.length >= 2" 
                    class="px-4 py-2 text-center text-gray-500"
                  >
                    No users found for "{{ searchValue }}"
                  </div>
                </div>
              </div>

              <!-- Administration Role Display -->
              <div class="relative ms-3">
                <span v-if="auth.user && auth.user.roles && auth.user.roles.length"
                      class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-medium text-gray-700 border border-gray-300">
                  {{ auth.user.roles[0] }}
                </span>
              </div>
              
              <!-- Settings Dropdown -->
              <div class="relative ms-3">
                <Dropdown align="right" width="48">
                  <template #trigger>
                    <span class="inline-flex rounded-md">
                      <button
                        type="button"
                        class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none"
                      >
                        <!-- {{ $page.props.auth.user.name }} -->
                        {{ auth.user.name }}


                        <svg
                          class="-me-0.5 ms-2 h-4 w-4"
                          xmlns="http://www.w3.org/2000/svg"
                          viewBox="0 0 20 20"
                          fill="currentColor"
                        >
                          <path
                            fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd"
                          />
                        </svg>
                      </button>
                    </span>
                  </template>

                  <template #content>
                    <DropdownLink :href="route('profile.edit')">
                      Profile
                    </DropdownLink>
                    <DropdownLink :href="route('logout')" method="post" as="button">
                      Log Out
                    </DropdownLink>
                  </template>
                </Dropdown>
              </div>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
              <button
                @click="showingNavigationDropdown = !showingNavigationDropdown"
                class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none"
              >
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                  <path
                    :class="{ hidden: showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16"
                  />
                  <path
                    :class="{ hidden: !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M6 18L18 6M6 6l12 12"
                  />
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Page Heading slot below navigation -->
        <div class="mt-6 pt-6">
            <div class="mx-auto max-w-7xl px-4 py-2 sm:px-6 lg:px-8">
              <h1 class="text-xl  font-semibold text-white">Manage Sales Progress</h1>
              <p class="mt-1 text-sm text-white/80">Keep an eye on how well your branding and printing services are selling.</p>
            </div>
          </div>
      </nav>

      <!-- Responsive Navigation Menu -->
      <div
        :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
        class="sm:hidden"
      >
        <div class="space-y-1 pb-3 pt-2">
          <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
            Dashboard
          </ResponsiveNavLink>
          <ResponsiveNavLink v-if="can('manage access control')"
            :href="route('admin.rolesAndPermission')" 
            :active="route().current('admin.rolesAndPermission')">
            Roles & Permissions
          </ResponsiveNavLink>
        </div>

        <!-- Responsive Settings Options -->
        <div class="border-t border-gray-200 pb-1 pt-4">
          <div class="px-4">
            <div class="text-base font-medium text-gray-800">{{ auth.user.name }}
              <!-- {{ $page.props.auth.user.name }} -->
            </div>
            <div class="text-sm font-medium text-gray-500">{{ auth.user.email }}
              <!-- {{ $page.props.auth.user.email }} -->
            </div>
          </div>

          <div class="mt-3 space-y-1">
            <ResponsiveNavLink :href="route('profile.edit')">Profile</ResponsiveNavLink>
            <ResponsiveNavLink :href="route('logout')" method="post" as="button">Log Out</ResponsiveNavLink>
          </div>
        </div>
      </div>

      <!-- Page Content -->
      <main>
        <slot />
      </main>
    </div>
  </div>
</template>