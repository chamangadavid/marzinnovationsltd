<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Table, Popconfirm, message, Modal, Input, DatePicker, Select } from 'ant-design-vue';
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import { debounce } from 'lodash';

const { RangePicker } = DatePicker;
const { Option } = Select;

const columns = [
  {
    title: 'Serial No',
    dataIndex: 'serialNo',
    key: 'serialNo',
    sorter: (a, b) => a.serialNo - b.serialNo,
  },
  {
    title: 'Full Name',
    dataIndex: 'fullName',
    key: 'fullName',
    sorter: (a, b) => a.fullName.localeCompare(b.fullName),
  },
  {
    title: 'Email',
    dataIndex: 'email',
    key: 'email',
  },
  {
    title: 'Subject',
    dataIndex: 'subject',
    key: 'subject',
  },
  {
    title: 'Message',
    dataIndex: 'message',
    key: 'message',
  },
  {
    title: 'Created At',
    dataIndex: 'createdAt',
    key: 'createdAt',
    sorter: (a, b) => new Date(a.createdAt) - new Date(b.createdAt),
  },
  {
    title: 'Action',
    dataIndex: 'operation',
    key: 'operation',
    width: 150,
  },
];

const dataSource = ref([]);
const filteredData = ref([]);
const loading = ref(false);
const searchQuery = ref('');
const selectedRecord = ref(null);
const isModalVisible = ref(false);
const dateRange = ref([]);
const monthFilter = ref(null);

// Months for filter
const months = [
  { value: 1, label: 'January' },
  { value: 2, label: 'February' },
  { value: 3, label: 'March' },
  { value: 4, label: 'April' },
  { value: 5, label: 'May' },
  { value: 6, label: 'June' },
  { value: 7, label: 'July' },
  { value: 8, label: 'August' },
  { value: 9, label: 'September' },
  { value: 10, label: 'October' },
  { value: 11, label: 'November' },
  { value: 12, label: 'December' },
];

const fetchContacts = async () => {
  try {
    loading.value = true;
    const response = await axios.get('/getcontacts');
    dataSource.value = response.data.map((item, index) => ({
      ...item,
      serialNo: index + 1,
      key: item.id,
      createdAt: new Date(item.created_at).toLocaleDateString(),
    }));
    filteredData.value = [...dataSource.value];
  } catch (error) {
    console.error('Error fetching contacts:', error);
    message.error('Failed to fetch contacts');
  } finally {
    loading.value = false;
  }
};

const handleDelete = async (id) => {
  try {
    await axios.delete(`/contacts/${id}`);
    message.success('Contact deleted successfully');
    fetchContacts();
  } catch (error) {
    console.error('Error deleting contact:', error);
    message.error('Failed to delete contact');
  }
};

const showModal = (record) => {
  selectedRecord.value = record;
  isModalVisible.value = true;
};

const handleSearch = debounce(() => {
  if (!searchQuery.value) {
    filteredData.value = [...dataSource.value];
    return;
  }

  const query = searchQuery.value.toLowerCase();
  filteredData.value = dataSource.value.filter(item =>
    item.fullName.toLowerCase().includes(query) ||
    item.email.toLowerCase().includes(query) ||
    item.subject.toLowerCase().includes(query) ||
    item.message.toLowerCase().includes(query)
  );
}, 300);

const handleDateRangeChange = (dates) => {
  dateRange.value = dates;
  applyFilters();
};

const handleMonthChange = (month) => {
  monthFilter.value = month;
  applyFilters();
};

const applyFilters = () => {
  let result = [...dataSource.value];

  // Apply date range filter
  if (dateRange.value && dateRange.value.length === 2) {
    const [start, end] = dateRange.value;
    result = result.filter(item => {
      const itemDate = new Date(item.created_at);
      return itemDate >= start && itemDate <= end;
    });
  }

  // Apply month filter
  if (monthFilter.value) {
    result = result.filter(item => {
      const itemDate = new Date(item.created_at);
      return itemDate.getMonth() + 1 === monthFilter.value;
    });
  }

  filteredData.value = result;
};

const resetFilters = () => {
  dateRange.value = [];
  monthFilter.value = null;
  filteredData.value = [...dataSource.value];
};

watch(searchQuery, handleSearch);

onMounted(() => {
  fetchContacts();
});
</script>

<template>
  <Head title="Contact" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        Profile
      </h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
        <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
            <a-breadcrumb>
                <a-breadcrumb-item href="/">Home</a-breadcrumb-item>
                <a-breadcrumb-item>Contact Management</a-breadcrumb-item>
              </a-breadcrumb>
            <br />  
          <div class="flex flex-wrap gap-4 mb-4">
            <a-input-search
              v-model:value="searchQuery"
              placeholder="Search contacts..."
              style="width: 300px"
              allow-clear
            />
            
            <a-range-picker
              v-model:value="dateRange"
              @change="handleDateRangeChange"
              placeholder="Filter by date range"
              style="width: 300px"
            />
            
            <a-select
              v-model:value="monthFilter"
              placeholder="Filter by month"
              style="width: 200px"
              allow-clear
              @change="handleMonthChange"
            >
              <a-select-option v-for="month in months" :key="month.value" :value="month.value">
                {{ month.label }}
              </a-select-option>
            </a-select>
            
            <a-button @click="resetFilters" type="default">
              Reset Filters
            </a-button>
          </div>

          <a-table 
            :columns="columns" 
            :dataSource="filteredData" 
            :loading="loading"
            bordered
            class="mt-4"
          >
            <template #bodyCell="{ column, record }">
              <template v-if="column.key === 'message'">
                <span>
                  {{ record.message && record.message.length > 20 
                    ? record.message.slice(0, 20) + '...' 
                    : record.message }}
                </span>
              </template>
              <template v-else-if="column.key === 'operation'">
                <div class="flex gap-2">
                  <a-button type="primary" @click="showModal(record)">View</a-button>
                  <Popconfirm
                    title="Are you sure you want to delete this contact?"
                    ok-text="Yes"
                    cancel-text="No"
                    @confirm="handleDelete(record.id)"
                  >
                    <a-button type="primary" danger>Delete</a-button>
                  </Popconfirm>
                </div>
              </template>
            </template>
          </a-table>
          <!-- <a-modal
          v-model:visible="isModalVisible"
          title="Contact Details"
          width="800px"
          :footer="null"
          @cancel="isModalVisible = false"
        >
          <div v-if="selectedRecord" class="p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
              <div>
                <p class="text-sm text-gray-500">Serial No:</p>
                <p class="font-medium text-gray-900">{{ selectedRecord.serialNo }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-500">Full Name:</p>
                <p class="font-medium text-gray-900">{{ selectedRecord.fullName }}</p>
              </div>
        
              <div>
                <p class="text-sm text-gray-500">Email:</p>
                <p class="font-medium text-gray-900">{{ selectedRecord.email }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-500">Subject:</p>
                <p class="font-medium text-gray-900">{{ selectedRecord.subject }}</p>
              </div>
        
              <div class="md:col-span-2">
                <p class="text-sm text-gray-500">Message:</p>
                <p class="font-medium text-gray-900 whitespace-pre-line">{{ selectedRecord.message }}</p>
              </div>
        
              <div>
                <p class="text-sm text-gray-500">Created At:</p>
                <p class="font-medium text-gray-900">{{ selectedRecord.createdAt }}</p>
              </div>
            </div>
          </div>
          <div v-else class="p-4 text-center text-gray-500">
            No contact details available.
          </div>
        </a-modal> -->

        <a-modal
        v-model:visible="isModalVisible"
        title="Contact Details"
        width="800px"
        :footer="null"
        @cancel="isModalVisible = false"
      >
      <hr><br/>
        <div v-if="selectedRecord" class="p-4 space-y-6">
          <div class="grid grid-cols-2 gap-6 text-base text-gray-700">
            <div>
              <span class="font-medium text-gray-500">Serial No:</span>
              <p class="mt-1 font-semibold">{{ selectedRecord.serialNo }}</p>
            </div>

            <div>
              <span class="font-medium text-gray-500">Full Name:</span>
              <p class="mt-1 font-semibold">{{ selectedRecord.fullName }}</p>
            </div>

            <div>
              <span class="font-medium text-gray-500">Email:</span>
              <p class="mt-1 font-semibold">{{ selectedRecord.email }}</p>
            </div>

            <div>
              <span class="font-medium text-gray-500">Subject:</span>
              <p class="mt-1 font-semibold">{{ selectedRecord.subject }}</p>
            </div>

            <div class="col-span-2">
              <span class="font-medium text-gray-500">Message:</span>
              <p class="mt-1 font-normal whitespace-pre-line border border-gray-200 rounded p-3 bg-gray-50">
                {{ selectedRecord.message }}
              </p>
            </div>

            <div>
              <span class="font-medium text-gray-500">Created At:</span>
              <p class="mt-1 font-semibold">{{ selectedRecord.createdAt }}</p>
            </div>
          </div>
        </div>
      </a-modal>

          <!-- <a-modal
            v-model:visible="isModalVisible"
            title="Contact Details"
            width="800px"
            :footer="null"
            @cancel="isModalVisible = false"
          >
          <hr><br/>
            <div v-if="selectedRecord" class="space-y-4">
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <p class="font-semibold">Serial No: {{ selectedRecord.serialNo }}</p>
                </div>
                <div>
                  <p class="font-semibold">First Name: {{ selectedRecord.fullName }}</p>
                </div>
                <div>
                  <p class="font-semibold">Email: {{ selectedRecord.email }}</p>
                </div>
                <div>
                  <p class="font-semibold">Subject: {{ selectedRecord.subject }}</p>
                </div>
                <div class="col-span-2">
                  <p class="font-semibold">Message:</p>
                  <p class="whitespace-pre-line">{{ selectedRecord.message }}</p>
                </div>
                <div>
                  <p class="font-semibold">Created At: {{ selectedRecord.createdAt }}</p>
                </div>
              </div>
            </div>
          </a-modal> -->
        </div>

        <!-- <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
          <p>test code two</p>
        </div>

        <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
          <p>test code three</p>
        </div> -->
      </div>
    </div>
  </AuthenticatedLayout>
</template>






<!-- <script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Table } from 'ant-design-vue';
import { ref, onMounted } from 'vue';
import axios from 'axios';

const columns = [
  {
    title: 'Serial No',
    dataIndex: 'serialNo',
    key: 'serialNo',
  },
  {
    title: 'First Name',
    dataIndex: 'fullName',
    key: 'fullName',
  },
  {
    title: 'Email',
    dataIndex: 'email',
    key: 'email',
  },
  {
    title: 'Subject',
    dataIndex: 'subject',
    key: 'subject',
  },
  {
    title: 'Message',
    dataIndex: 'message',
    key: 'message',
  },
];

const dataSource = ref([]);
const loading = ref(false);

const fetchContacts = async () => {
  try {
    loading.value = true;
    const response = await axios.get('/getcontacts');
    dataSource.value = response.data.map((item, index) => ({
      ...item,
      serialNo: index + 1,
    }));
  } catch (error) {
    console.error('Error fetching contacts:', error);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchContacts();
});
</script>

<template>
    <Head title="Contact" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Profile
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                    <p>test code</p>
                    <a-table 
                        :columns="columns" 
                        :dataSource="dataSource" 
                        :loading="loading"
                        bordered
                        class="mt-4"
                    />
                </div>

                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                    <p>test code two</p>
                </div>

                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                    <p>test code three</p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template> -->








<!-- <script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';


</script>

<template>
    <Head title="Contact" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Profile
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div
                    class="bg-white p-4 shadow sm:rounded-lg sm:p-8"
                >
                <p>test code</p>
                    
                </div>

                <div
                    class="bg-white p-4 shadow sm:rounded-lg sm:p-8"
                >
                    
                    <p>test code two</p>
                </div>

                <div
                    class="bg-white p-4 shadow sm:rounded-lg sm:p-8"
                >
                <p>test code three</p>
                   
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template> -->
