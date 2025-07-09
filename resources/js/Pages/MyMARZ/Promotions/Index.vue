<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Table, Popconfirm, Modal, Input, DatePicker, Select, Button, message } from 'ant-design-vue';
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
//import { debounce } from 'lodash';
import debounce from 'lodash/debounce' // ✅


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
    title: 'Title',
    dataIndex: 'title',
    key: 'title',
    sorter: (a, b) => a.title.localeCompare(b.title),
  },
  {
    title: 'description',
    dataIndex: 'description',
    key: 'description',
    sorter: (a, b) => a.description.localeCompare(b.description),
  },
  {
    title: 'start_date',
    dataIndex: 'start_date',
    key: 'start_date',
    sorter: (a, b) => a.start_date.localeCompare(b.start_date),
  },{
    title: 'end_date',
    dataIndex: 'end_date',
    key: 'end_date',
    sorter: (a, b) => a.end_date.localeCompare(b.end_date),
  },


  
  {
    title: 'Action',
    key: 'action',
    width: 200,
  },
];

const dataSource = ref([]);
const filteredData = ref([]);
const loading = ref(false);
const searchQuery = ref('');
const dateRange = ref([]);
const statusFilter = ref(null);
const isModalVisible = ref(false);
const isCreateModalVisible = ref(false);
const selectedRecord = ref(null);
const formState = ref({
  title: '',
  description: '',
  start_date: null,
  end_date: null,
});

const fetchPromotions = async () => {
  try {
    loading.value = true;
    const params = {
      search: searchQuery.value,
      is_active: statusFilter.value,
    };
    
    if (dateRange.value && dateRange.value.length === 2) {
      params.start_date = dateRange.value[0].format('YYYY-MM-DD');
      params.end_date = dateRange.value[1].format('YYYY-MM-DD');
    }

    const response = await axios.get('/getPromotions', { params });
    dataSource.value = response.data.map((item, index) => ({
      ...item,
      serialNo: index + 1,
      key: item.id,
    }));
    filteredData.value = [...dataSource.value];
  } catch (error) {
    console.error('Error fetching promotions:', error);
    message.error('Failed to fetch promotions');
  } finally {
    loading.value = false;
  }
};

const handleSearch = debounce(fetchPromotions, 300);

const handleDateRangeChange = (dates) => {
  dateRange.value = dates;
  fetchPromotions();
};

const handleStatusChange = (value) => {
  statusFilter.value = value;
  fetchPromotions();
};

const resetFilters = () => {
  searchQuery.value = '';
  dateRange.value = [];
  statusFilter.value = null;
  fetchPromotions();
};

const showDetails = (record) => {
  selectedRecord.value = record;
  isModalVisible.value = true;
};

const showCreateModal = () => {
  isCreateModalVisible.value = true;
};

const handleCreateCancel = () => {
  isCreateModalVisible.value = false;
  formState.value = {
    title: '',
    description: '',
    start_date: null,
    end_date: null,
  };
};

const handleDelete = async (id) => {
  try {
    await axios.delete(`/promotions/${id}`);
    message.success('Promotion deleteds successfully');
    fetchPromotions();
  } catch (error) {
    console.error('Error deleting promotion:', error);
    message.error('Failed to delete promotion');
  }
};

const handleCreateSubmit = async () => {
  try {
    await axios.post('/promotions', {
      ...formState.value,
      start_date: formState.value.start_date?.format('YYYY-MM-DD'),
      end_date: formState.value.end_date?.format('YYYY-MM-DD'),
    });
    message.success('Promotion created successfully');
    fetchPromotions();
    handleCreateCancel();
  } catch (error) {
    console.error('Error creating promotion:', error);
    if (error.response?.data?.errors) {
      Object.values(error.response.data.errors).forEach(err => {
        message.error(err[0]);
      });
    } else {
      message.error('Failed to create promotion');
    }
  }
};

onMounted(() => {
  fetchPromotions();
});

watch(searchQuery, handleSearch);
</script>

<template>
  <Head title="Promotions" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        Promotions Management
      </h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
        <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
          <a-breadcrumb>
            <a-breadcrumb-item href="/">Home</a-breadcrumb-item>
            <a-breadcrumb-item>Promotoions</a-breadcrumb-item>
          </a-breadcrumb>
        <br />  
          <div class="flex flex-wrap gap-4 mb-4">
            <a-input-search
              v-model:value="searchQuery"
              placeholder="Search promotions..."
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
              v-model:value="statusFilter"
              placeholder="Filter by status"
              style="width: 150px"
              allow-clear
              @change="handleStatusChange"
            >
              <a-select-option :value="true">Active</a-select-option>
              <a-select-option :value="false">Inactive</a-select-option>
            </a-select>
            
            <a-button @click="resetFilters" type="default">
              Reset Filters
            </a-button>
            
            <a-button @click="showCreateModal" type="primary" class="ml-auto">
              Create Promotion
            </a-button>
          </div>

          <a-table 
            :columns="columns" 
            :dataSource="filteredData" 
            :loading="loading"
            bordered
            >
            <template #bodyCell="{ column, record }">
                <!--  Keep only one action block -->
                <template v-if="column.key === 'action'">
                <div class="flex gap-2">
                    <a-button @click="showDetails(record)">View</a-button>
                    <Popconfirm
                    title="Are you sure you want to delete this promotion?"
                    ok-text="Yes"
                    cancel-text="No"
                    @confirm="handleDelete(record.id)"
                    >
                    <a-button type="primary" danger>Delete</a-button>
                    </Popconfirm>
                </div>
                </template>

                <!-- Start Date -->
                <template v-else-if="column.key === 'start_date'">
                <span>
                    {{ record.start_date ? new Date(record.start_date).toISOString().slice(0, 10) : '' }}
                </span>
                </template>

                <!-- End Date -->
                <template v-else-if="column.key === 'end_date'">
                <span>
                    {{ record.end_date ? new Date(record.end_date).toISOString().slice(0, 10) : '' }}
                </span>
                </template>

                <!-- Title -->
                <template v-else-if="column.key === 'title'">
                <span>
                    {{ record.title && record.title.length > 10
                    ? record.title.substring(0, 10) + '...'
                    : record.title }}
                </span>
                </template>

                <!-- Description -->
                <template v-else-if="column.key === 'description'">
                <span>
                    {{ record.description && record.description.length > 20
                    ? record.description.substring(0, 20) + '...'
                    : record.description }}
                </span>
                </template>

                <!-- Status -->
                <template v-else-if="column.key === 'is_active'">
                <span :class="`px-2 py-1 rounded-full text-xs ${
                    record.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                }`">
                    {{ record.is_active ? 'Active' : 'Inactive' }}
                </span>
                </template>
            </template>
            </a-table>


          <!-- View Modal -->
          <a-modal
          v-model:visible="isModalVisible"
          :title="selectedRecord?.title"
          width="800px"
          :footer="null"
          @cancel="isModalVisible = false"
        > <hr/><br/>
          <div v-if="selectedRecord" class="p-6 space-y-6 text-gray-700">
            <!-- Title -->
            <div>
              <h3 class="text-sm text-gray-500 font-medium mb-1">Title</h3>
              <p class="text-lg font-semibold">{{ selectedRecord.title }}</p>
            </div>
        
            <!-- Description -->
            <div>
              <h3 class="text-sm text-gray-500 font-medium mb-1">Description</h3>
              <p class="whitespace-pre-line bg-gray-50 border border-gray-200 rounded-md p-4 text-base leading-relaxed">
                {{ selectedRecord.description }}
              </p>
            </div>
        
            <!-- Dates -->
            <div class="grid grid-cols-2 gap-6">
              <div>
                <h3 class="text-sm text-gray-500 font-medium mb-1">Start Date</h3>
                <p class="text-base font-semibold">{{ selectedRecord.start_date }}</p>
              </div>
              <div>
                <h3 class="text-sm text-gray-500 font-medium mb-1">End Date</h3>
                <p class="text-base font-semibold">{{ selectedRecord.end_date }}</p>
              </div>
            </div>
        
            <!-- Status -->
            <div>
              <h3 class="text-sm text-gray-500 font-medium mb-1">Status</h3>
              <span
                class="inline-block px-3 py-1 rounded-full text-sm font-medium"
                :class="selectedRecord.is_active 
                  ? 'bg-green-100 text-green-800' 
                  : 'bg-red-100 text-red-800'"
              >
                {{ selectedRecord.is_active ? 'Active' : 'Inactive' }}
              </span>
            </div>
          </div>
        </a-modal>

          <!-- Create Modal -->
          <a-modal
            v-model:visible="isCreateModalVisible"
            title="Create New Promotion"
            width="600px"
            :confirm-loading="loading"
            @ok="handleCreateSubmit"
            @cancel="handleCreateCancel"
          >
            <a-form layout="vertical">
              <a-form-item label="Title" required>
                <a-input v-model:value="formState.title"   style="border: 1px solid #e9e9e9; border-radius: 8px; height: 32px; line-height: 32px;"/>
              </a-form-item>
              
              <a-form-item label="Description">
                <a-textarea v-model:value="formState.description" :rows="4" />
              </a-form-item>
              
              <div class="grid grid-cols-2 gap-4">
                <a-form-item label="Start Date" required>
                  <a-date-picker
                    v-model:value="formState.start_date"
                    style="width: 100%"
                  />
                </a-form-item>
                
                <a-form-item label="End Date" required>
                  <a-date-picker
                    v-model:value="formState.end_date"
                    style="width: 100%"
                    :disabled-date="(current) => {
                      return formState.start_date && current < formState.start_date.startOf('day');
                    }"
                  />
                </a-form-item>
              </div>
            </a-form>
          </a-modal>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>




<!-- <script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';


</script>

<template>
    <Head title="Promotions" />

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
