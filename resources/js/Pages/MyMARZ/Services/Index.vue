<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Table, Popconfirm, Modal, Input, Button, message, Upload, Tag, DatePicker } from 'ant-design-vue';
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
//import { debounce } from 'lodash';
import debounce from 'lodash/debounce' // ✅

import { PlusOutlined } from '@ant-design/icons-vue';

const { RangePicker } = DatePicker;

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
    title: 'Description',
    dataIndex: 'description',
    key: 'description',
    sorter: (a, b) => a.description.localeCompare(b.description),
  },
  {
    title: 'Price',
    dataIndex: 'price',
    key: 'price',
    sorter: (a, b) => a.price - b.price,
  },
  {
    title: 'Image',
    key: 'image',
  },
  {
    title: 'Status',
    key: 'status',
    filters: [
      { text: 'Active', value: true },
      { text: 'Inactive', value: false },
    ],
    onFilter: (value, record) => record.is_active === value,
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
const dateRange = ref([]); // Added for date filtering
const isModalVisible = ref(false);
const isCreateModalVisible = ref(false);
const selectedRecord = ref(null);
const previewVisible = ref(false);
const previewImage = ref('');

const formState = ref({
  title: '',
  description: '',
  price: 0,
  images: [],
  is_active: true,
});

const fileList = ref([]);

const fetchServices = async () => {
  try {
    loading.value = true;
    const params = {
      search: searchQuery.value,
    };
    
    // Add date range to params if set
    if (dateRange.value && dateRange.value.length === 2) {
      params.start_date = dateRange.value[0].format('YYYY-MM-DD');
      params.end_date = dateRange.value[1].format('YYYY-MM-DD');
    }

    const response = await axios.get('/getServices', { 
      params: params 
    });
    
    dataSource.value = response.data.data.map((item, index) => ({
      ...item,
      serialNo: index + 1,
      key: item.id,
    }));
    filteredData.value = [...dataSource.value];
  } catch (error) {
    console.error('Error fetching services:', error);
    message.error('Failed to fetch services');
  } finally {
    loading.value = false;
  }
};

const handleSearch = debounce(fetchServices, 300);

const handleDateRangeChange = (dates) => {
  dateRange.value = dates;
  fetchServices();
};

const resetFilters = () => {
  searchQuery.value = '';
  dateRange.value = [];
  fetchServices();
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
    price: 0,
    images: [],
    is_active: true,
  };
  fileList.value = [];
};

const handleDelete = async (id) => {
  try {
    await axios.delete(`/services/${id}`);
    message.success('Service deleted successfully');
    fetchServices();
  } catch (error) {
    console.error('Error deleting service:', error);
    message.error('Failed to delete service');
  }
};

const beforeUpload = (file) => {
  const isImage = file.type.startsWith('image/');
  if (!isImage) {
    message.error('You can only upload image files!');
  }
  const isLt5M = file.size / 1024 / 1024 < 5;
  if (!isLt5M) {
    message.error('Image must smaller than 5MB!');
  }
  return isImage && isLt5M;
};

const handlePreview = async (file) => {
  if (!file.url && !file.preview) {
    file.preview = await getBase64(file.originFileObj);
  }
  previewImage.value = file.url || file.preview;
  previewVisible.value = true;
};

const getBase64 = (file) => {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = () => resolve(reader.result);
    reader.onerror = (error) => reject(error);
  });
};

const handleRemove = (file) => {
  const index = fileList.value.indexOf(file);
  const newFileList = fileList.value.slice();
  newFileList.splice(index, 1);
  fileList.value = newFileList;
};

const handleCreateSubmit = async () => {
  try {
    const formData = new FormData();
    formData.append('title', formState.value.title);
    formData.append('description', formState.value.description);
    formData.append('price', formState.value.price);
    formData.append('is_active', formState.value.is_active ? '1' : '0');
    
    fileList.value.forEach(file => {
      if (file.originFileObj) {
        formData.append('images[]', file.originFileObj);
      }
    });

    await axios.post('/services', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    });
    
    message.success('Service created successfully');
    fetchServices();
    handleCreateCancel();
  } catch (error) {
    console.error('Error creating service:', error);
    if (error.response?.data?.errors) {
      Object.values(error.response.data.errors).forEach(err => {
        message.error(err[0]);
      });
    } else {
      message.error('Failed to create service');
    }
  }
};

const toggleStatus = async (id, currentStatus) => {
  try {
    await axios.patch(`/services/${id}/status`, {
      is_active: !currentStatus
    });
    message.success(`Service ${!currentStatus ? 'activated' : 'deactivated'} successfully`);
    fetchServices();
  } catch (error) {
    console.error('Error toggling service status:', error);
    message.error('Failed to update service status');
  }
};

onMounted(() => {
  fetchServices();
});

watch(searchQuery, handleSearch);
</script>

<template>
  <Head title="Services" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        Services Management
      </h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
        <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
          <a-breadcrumb>
            <a-breadcrumb-item href="/">Home</a-breadcrumb-item>
            <a-breadcrumb-item>Service Management</a-breadcrumb-item>
          </a-breadcrumb>
        <br />  
          <div class="flex flex-wrap gap-4 mb-4">
            <a-input-search
              v-model:value="searchQuery"
              placeholder="Search services..."
              style="width: 300px"
              allow-clear
            />
            
            <!-- Added RangePicker for date filtering -->
            <a-range-picker
              v-model:value="dateRange"
              @change="handleDateRangeChange"
              placeholder="Filter by date range"
              style="width: 300px"
            />


            <a-button @click="resetFilters" type="default">
              Reset Filters
            </a-button>
            
            <a-button @click="showCreateModal" type="primary" class="ml-auto">
              <template #icon><plus-outlined /></template>
              Create Service
            </a-button>
          </div>

          <a-table 
            :columns="columns" 
            :dataSource="filteredData" 
            :loading="loading"
            bordered
          >
            <template #bodyCell="{ column, record }">
              <template v-if="column.key === 'action'">
                <div class="flex gap-2">
                  <a-button @click="showDetails(record)">View</a-button>
                  <Popconfirm
                    title="Are you sure you want to delete this service?"
                    ok-text="Yes"
                    cancel-text="No"
                    @confirm="handleDelete(record.id)"
                  >
                    <a-button type="primary" danger>Delete</a-button>
                  </Popconfirm>
                </div>
              </template>

              <template v-else-if="column.key === 'image'">
                <img 
                  v-if="record.images && record.images.length" 
                  :src="record.images[0]" 
                  style="width: 50px; height: 50px; cursor: pointer"
                  @click="() => { previewImage = record.images[0]; previewVisible = true }"
                />
                <span v-else>No Image</span>
              </template>

              <template v-else-if="column.key === 'status'">
                <a-tag :color="record.is_active ? 'green' : 'red'">
                  {{ record.is_active ? 'Active' : 'Inactive' }}
                </a-tag>
              </template>

              <template v-else-if="column.key === 'price'">
                K{{ record.price }}
              </template>

              <template v-else-if="column.key === 'title'">
                <span>
                  {{ record.title && record.title.length > 20
                    ? record.title.substring(0, 20) + '...'
                    : record.title }}
                </span>
              </template>

              <template v-else-if="column.key === 'description'">
                <span>
                  {{ record.description && record.description.length > 30
                    ? record.description.substring(0, 30) + '...'
                    : record.description }}
                </span>
              </template>
            </template>
          </a-table>

          <!-- View Modal -->
       
          <a-modal
        v-model:visible="isModalVisible"
        :title="selectedRecord?.title"
        width="800px"
        @cancel="isModalVisible = false"
      > <hr/><br/>
        <div v-if="selectedRecord" class="p-6 space-y-6 text-gray-700">
          <!-- Basic Info -->
          <div class="grid grid-cols-2 gap-6">
            <div>
              <span class="block text-sm text-gray-500 font-medium">Title</span>
              <p class="text-lg font-semibold">{{ selectedRecord.title }}</p>
            </div>

            <div>
              <span class="block text-sm text-gray-500 font-medium">Price</span>
              <p class="text-lg font-semibold">ZKW {{ selectedRecord.price }}</p>
            </div>

            <div class="col-span-2">
              <span class="block text-sm text-gray-500 font-medium">Description</span>
              <p class="whitespace-pre-line bg-gray-50 border border-gray-200 rounded p-4 text-base">
                {{ selectedRecord.description }}
              </p>
            </div>

            <div>
              <span class="block text-sm text-gray-500 font-medium">Status</span>
              <a-tag :color="selectedRecord.is_active ? 'green' : 'red'" class="mt-1">
                {{ selectedRecord.is_active ? 'Active' : 'Inactive' }}
              </a-tag>
            </div>
          </div>

          <!-- Images -->
          <div>
            <span class="block text-sm text-gray-500 font-medium">Images</span>
            <div class="flex flex-wrap gap-4 mt-2">
              <img
                v-for="(image, index) in selectedRecord.images"
                :key="index"
                :src="image"
                class="w-36 h-24 object-cover rounded shadow cursor-pointer hover:opacity-90 transition"
                @click="() => { previewImage = image; previewVisible = true }"
              />
            </div>
          </div>
        </div>

        <!-- Footer -->
        <template #footer>
          <a-button
            type="primary"
            :danger="selectedRecord?.is_active"
            @click="toggleStatus(selectedRecord.id, selectedRecord.is_active)"
          >
            {{ selectedRecord?.is_active ? 'Deactivate' : 'Activate' }}
          </a-button>
          <a-button @click="isModalVisible = false">Close</a-button>
        </template>
      </a-modal>


          <!-- Create Modal -->
          <a-modal
            v-model:visible="isCreateModalVisible"
            title="Create New Service"
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
              
              <a-form-item label="Price" required>
                <a-input-number 
                  v-model:value="formState.price" 
                  :min="0" 
                  :step="0.01" 
                  style="width: 100%"
                />
              </a-form-item>
              
              <a-form-item label="Status">
                <a-switch v-model:checked="formState.is_active" />
                <span class="ml-2">{{ formState.is_active ? 'Active' : 'Inactive' }}</span>
              </a-form-item>
              
              <a-form-item label="Images">
                <a-upload
                  v-model:file-list="fileList"
                  list-type="picture-card"
                  :multiple="true"
                  :before-upload="beforeUpload"
                  @preview="handlePreview"
                  @remove="handleRemove"
                  accept="image/*"
                >
                  <div v-if="fileList.length < 5">
                    <plus-outlined />
                    <div style="margin-top: 8px">Upload</div>
                  </div>
                </a-upload>
              </a-form-item>
            </a-form>
          </a-modal>

          <!-- Image Preview Modal -->
          <a-modal :visible="previewVisible" :footer="null" @cancel="previewVisible = false">
            <img alt="preview" style="width: 100%" :src="previewImage" />
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
    <Head title="Services" />

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
