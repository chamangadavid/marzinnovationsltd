<script setup>
import { ref, defineProps,onMounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { usePage } from '@inertiajs/vue3'


const props = defineProps({
  user: {
    type: Object,
    required: true,
    default: () => ({
      id: null,
      name: '',
      email: '',
      roles: [] // use roles instead of a single role
    })
  }
});

onMounted(() => {
  console.log('User Roles:', props.user.roles);
});
const activeTab = ref('Personal Details');
</script>

<template>
 <Head title="Search" />
  <AuthenticatedLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          User Details: {{ user.name }}
        </h2>
        <Link 
          :href="route('dashboard')" 
          class="text-sm text-purple-600 hover:text-purple-800"
        >
          &larr; Back to Dashboard
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <!-- Profile Header -->
            <div class="flex items-center space-x-4 mb-6">
              <div class="flex-shrink-0 h-16 w-16 rounded-full bg-purple-100 flex items-center justify-center text-purple-800 text-2xl font-bold">
                {{ user.name.charAt(0).toUpperCase() }}
              </div>
              <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ user.name }}</h1>
                <h1 class="text-sm font-normal text-gray-500">{{ user.email }}</h1>
                <p class="text-sm text-gray-500">
                  {{ user.role }}
                </p>
              </div>
            </div>

            <!-- Tab Navigation -->
            <div class="mb-6 border-b border-gray-200">
              <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <button
                  v-for="tab in ['Personal Details', 'Qualification', 'Position']"
                  :key="tab"
                  @click="activeTab = tab"
                  :class="[
                    'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm',
                    activeTab === tab
                      ? 'border-purple-500 text-purple-600'
                      : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                  ]"
                >
                  {{ tab }}
                </button>
              </nav>
            </div>

            <!-- Tab Content -->
            <div>
              <!-- Personal Details Tab -->
              <div v-show="activeTab === 'Personal Details'">
                <dl class="divide-y divide-gray-200">
                  <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                    <dt class="text-sm font-medium text-gray-500">Full name</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ user.name }}</dd>
                  </div>
                  <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                    <dt class="text-sm font-medium text-gray-500">Email address</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ user.email }}</dd>
                  </div>
                  <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                    <dt class="text-sm font-medium text-gray-500">User ID</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ user.id }}</dd>
                  </div>
                  <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                    <dt class="text-sm font-medium text-gray-500">Role</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                          {{ user.roles[0] }}
                        </span>
                      </dd>
                  </div>
                </dl>
              </div>

              <!-- Qualification Tab -->
              <div v-show="activeTab === 'Qualification'">
                <p class="text-sm text-gray-700">No qualification data available yet.</p>
              </div>

              <!-- Position Tab -->
              <div v-show="activeTab === 'Position'">
                <dl class="divide-y divide-gray-200">
                  <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                    <dt class="text-sm font-medium text-gray-500">Role</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                      <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                        <!-- {{ user.role }} --> <p>Position details comes here</p>
                      </span>
                    </dd>
                  </div>
                </dl>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>



<!-- <script setup>
import { defineProps } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
  user: {
    type: Object,
    required: true,
    default: () => ({
      id: null,
      name: '',
      email: '',
      role: ''
    })
  }
});
</script>

<template>
  <AuthenticatedLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          User Details: {{ user.name }}
        </h2>
        <Link 
          :href="route('dashboard')" 
          class="text-sm text-purple-600 hover:text-purple-800"
        >
          &larr; Back to Dashboard
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex items-center space-x-4 mb-6">
              <div class="flex-shrink-0 h-16 w-16 rounded-full bg-purple-100 flex items-center justify-center text-purple-800 text-2xl font-bold">
                {{ user.name.charAt(0).toUpperCase() }}
              </div>
              <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ user.name }}</h1>
                <p class="text-sm text-gray-500">{{ user.role }}</p>
              </div>
            </div>

            <div class="border-t border-gray-200 pt-6">
              <dl class="divide-y divide-gray-200">
                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                  <dt class="text-sm font-medium text-gray-500">
                    Full name
                  </dt>
                  <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    {{ user.name }}
                  </dd>
                </div>
                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                  <dt class="text-sm font-medium text-gray-500">
                    Email address
                  </dt>
                  <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    {{ user.email }}
                  </dd>
                </div>
                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                  <dt class="text-sm font-medium text-gray-500">
                    Role
                  </dt>
                  <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                      {{ user.role }}
                    </span>
                  </dd>
                </div>
                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                  <dt class="text-sm font-medium text-gray-500">
                    User ID
                  </dt>
                  <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    {{ user.id }}
                  </dd>
                </div>
              </dl>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template> -->