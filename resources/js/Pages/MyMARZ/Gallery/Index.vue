<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Table, Popconfirm, Modal, Input, DatePicker, Select, Button, Upload, message } from 'ant-design-vue';
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import { debounce } from 'lodash';

const { RangePicker } = DatePicker;
const { Option } = Select;
const { Dragger } = Upload;

const categoryLoading = ref(false); // Add this line with your other refs

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
    title: 'Category',
    dataIndex: 'category',
    key: 'category',
    customRender: ({ text }) => text?.name || 'N/A',
  },
  {
    title: 'Images',
    dataIndex: 'images',
    key: 'images',
  },
  {
    title: 'Description',
    dataIndex: 'description',
    key: 'description',
  },
  {
    title: 'Created At',
    dataIndex: 'createdAt',
    key: 'createdAt',
    sorter: (a, b) => new Date(a.createdAt) - new Date(b.createdAt),
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
const isModalVisible = ref(false);
const selectedRecord = ref(null);
const isAddModalVisible = ref(false);
const isCategoryModalVisible = ref(false);
const categories = ref([]);
const formState = ref({
  title: '',
  description: '',
  images: [],
  category_id: null,
});
const categoryFormState = ref({
  name: '',
});
const fileList = ref([]);

const fetchGalleries = async () => {
  try {
    loading.value = true;
    const response = await axios.get('/galleries');
    dataSource.value = response.data.map((item, index) => ({
      ...item,
      serialNo: index + 1,
      key: item.id,
      createdAt: new Date(item.created_at).toLocaleDateString(),
    }));
    filteredData.value = [...dataSource.value];
  } catch (error) {
    console.error('Error fetching galleries:', error);
    message.error('Failed to fetch galleries');
  } finally {
    loading.value = false;
  }
};

const fetchCategories = async () => {
  try {
    const response = await axios.get('/gallery-category');
    categories.value = response.data;
  } catch (error) {
    console.error('Error fetching categories:', error);
    message.error('Failed to fetch categories');
  }
};

const handleSearch = debounce(() => {
  if (!searchQuery.value) {
    filteredData.value = [...dataSource.value];
    return;
  }

  const query = searchQuery.value.toLowerCase();
  filteredData.value = dataSource.value.filter(item =>
    item.title.toLowerCase().includes(query) ||
    (item.description && item.description.toLowerCase().includes(query)) ||
    (item.category?.name && item.category.name.toLowerCase().includes(query))
  );
}, 300);

const handleDateRangeChange = (dates) => {
  dateRange.value = dates;
  applyFilters();
};

const applyFilters = () => {
  let result = [...dataSource.value];

  if (dateRange.value && dateRange.value.length === 2) {
    const [start, end] = dateRange.value;
    result = result.filter(item => {
      const itemDate = new Date(item.created_at);
      return itemDate >= start && itemDate <= end;
    });
  }

  filteredData.value = result;
};

const resetFilters = () => {
  searchQuery.value = '';
  dateRange.value = [];
  filteredData.value = [...dataSource.value];
};

const showDetails = (record) => {
  selectedRecord.value = record;
  isModalVisible.value = true;
};

const handleDelete = async (id) => {
  try {
    await axios.delete(`/galleries/${id}`);
    message.success('Gallery deleted successfully');
    fetchGalleries();
  } catch (error) {
    console.error('Error deleting gallery:', error);
    message.error('Failed to delete gallery');
  }
};

const showAddModal = () => {
  isAddModalVisible.value = true;
};

const showCategoryModal = () => {
  isCategoryModalVisible.value = true;
};

const handleAddCancel = () => {
  isAddModalVisible.value = false;
  formState.value = {
    title: '',
    description: '',
    images: [],
    category_id: null,
  };
  fileList.value = [];
};

const handleCategoryCancel = () => {
  isCategoryModalVisible.value = false;
  categoryFormState.value = {
    name: '',
  };
};

const beforeUpload = (file) => {
  const isImage = file.type.startsWith('image/');
  if (!isImage) {
    message.error('You can only upload image files!');
  }
  return isImage;
};

const handleChange = ({ fileList: newFileList }) => {
  fileList.value = newFileList;
};

const handleSubmit = async () => {
  try {
    const formData = new FormData();
    formData.append('title', formState.value.title);
    formData.append('description', formState.value.description);
    formData.append('category_id', formState.value.category_id);
    
    fileList.value.forEach(file => {
      formData.append('images[]', file.originFileObj);
    });

    await axios.post('/galleries', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    });

    message.success('Gallery added successfully');
    fetchGalleries();
    handleAddCancel();
  } catch (error) {
    console.error('Error adding gallery:', error);
    message.error('Failed to add gallery');
  }
};

const handleCategorySubmit = async () => {
  categoryLoading.value = true; // Start loading
  try {
    await axios.post('/gallery-categories', categoryFormState.value);
    message.success('Category added successfully');
    fetchCategories();
    handleCategoryCancel();
  } catch (error) {
    console.error('Error adding category:', error);
    if (error.response?.data?.message) {
      message.error(error.response.data.message);
    } else {
      message.error('Failed to add category');
    }
  } finally {
    categoryLoading.value = false; // End loading
  }
};

onMounted(() => {
  fetchGalleries();
  fetchCategories();
});

watch(searchQuery, handleSearch);
</script>

<template>
  <Head title="Gallery" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        Gallery Management
      </h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
        <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
          <a-breadcrumb>
            <a-breadcrumb-item href="/">Home</a-breadcrumb-item>
            <a-breadcrumb-item>Gallery Management</a-breadcrumb-item>
          </a-breadcrumb>
        <br />  
          <div class="flex flex-wrap gap-4 mb-4">
            <a-input-search
              v-model:value="searchQuery"
              placeholder="Search galleries..."
              style="width: 300px"
              allow-clear
            />
            
            <a-range-picker
              v-model:value="dateRange"
              @change="handleDateRangeChange"
              placeholder="Filter by date range"
              style="width: 300px"
            />
            
            <a-button @click="resetFilters" type="default">
              Reset Filters
            </a-button>
            
            <div class="ml-auto flex gap-2">
              <a-button @click="showCategoryModal" type="primary" ghost>
                Create Category
              </a-button>
              <a-button @click="showAddModal" type="primary">
                Add New Photos
              </a-button>
            </div>
          </div>

            <a-table 
            :columns="columns" 
            :dataSource="filteredData" 
            :loading="loading"
            bordered
            >
            <template #bodyCell="{ column, record }">
                <template v-if="column.key === 'images'">
                <div class="flex flex-wrap gap-2 items-center">
                    <!-- Show first 3 images -->
                    <template v-for="(image, index) in record.images.slice(0, 3)" :key="index">
                    <img 
                        :src="image" 
                        class="object-cover rounded"
                        style="width: 30px; height: 30px;"
                    />
                    </template>
                    <!-- Show "+X more" if there are more than 3 images -->
                    <span 
                    v-if="record.images.length > 3" 
                    class="text-xs text-gray-500 ml-1"
                    >
                    +{{ record.images.length - 3 }} more
                    </span>
                </div>
                </template>
                <template v-else-if="column.key === 'description'">
                <span>
                    {{ record.description && record.description.length > 30 ? 
                    `${record.description.substring(0, 30)}...` : 
                    record.description }}
                </span>
                </template>
                <template v-else-if="column.key === 'action'">
                <div class="flex gap-2">
                    <a-button @click="showDetails(record)">View</a-button>
                    <Popconfirm
                    title="Are you sure you want to delete this gallery?"
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
          
          <!-- Gallery View Modal -->
          <a-modal
            v-model:visible="isModalVisible"
            :title="selectedRecord?.title"
            width="800px"
            :footer="null"
            @cancel="isModalVisible = false"
          >
          <hr><br />

            <div v-if="selectedRecord" class="space-y-4">
              <div class="grid grid-cols-1 gap-4">
                <div>
                  <p class="font-semibold">Title: {{ selectedRecord.title }}</p>
                </div>
                <div>
                  <p class="font-semibold">Category: {{ selectedRecord.category?.name || 'N/A' }}</p>
                </div>
                <div>
                  <p class="font-semibold">Description:</p>
                  <p class="whitespace-pre-line">{{ selectedRecord.description }}</p>
                </div>
                <div>
                  <p class="font-semibold mb-2">Images:</p>
                  <div class="grid grid-cols-3 gap-4">
                    <img 
                      v-for="(image, index) in selectedRecord.images" 
                      :key="index"
                      :src="image"
                      class="w-full h-48 object-cover rounded"
                    />
                  </div>
                </div>
                <div>
                  <p class="font-semibold">Created At: {{ selectedRecord.createdAt }}</p>
                </div>
              </div>
            </div>
          </a-modal>

          <!-- Add Gallery Modal -->
          <a-modal
            v-model:visible="isAddModalVisible"
            title="Add New Gallery"
            width="800px"
            :confirm-loading="loading"
            @ok="handleSubmit"
            @cancel="handleAddCancel"
          >
            <a-form layout="vertical">
              <a-form-item label="Title" required>
                <a-input v-model:value="formState.title" />
              </a-form-item>
              
              <a-form-item label="Category" required>
                <a-select
                  v-model:value="formState.category_id"
                  placeholder="Select category"
                >
                  <a-select-option 
                    v-for="category in categories" 
                    :key="category.id" 
                    :value="category.id"
                  >
                    {{ category.name }}
                  </a-select-option>
                </a-select>
              </a-form-item>
              
              <a-form-item label="Description">
                <a-textarea v-model:value="formState.description" :rows="4" />
              </a-form-item>
              
              <a-form-item label="Images" required>
                <a-upload
                  v-model:file-list="fileList"
                  list-type="picture-card"
                  multiple
                  :before-upload="beforeUpload"
                  @change="handleChange"
                >
                  <div v-if="fileList.length < 10">
                    <plus-outlined />
                    <div class="mt-2">Upload</div>
                  </div>
                </a-upload>
              </a-form-item>
            </a-form>
          </a-modal>

          <!-- Add Category Modal -->
          <a-modal
          v-model:visible="isCategoryModalVisible"
          title="Create New Category"
          width="500px"
          :confirm-loading="categoryLoading"
          @ok="handleCategorySubmit"
          @cancel="handleCategoryCancel"
          :ok-button-props="{ loading: categoryLoading }"
        >
          <a-form layout="vertical">
            <a-form-item label="Category Name" required>
              <a-input 
                v-model:value="categoryFormState.name" 
                :disabled="categoryLoading"
              />
            </a-form-item>
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
    <Head title="Gallery" />

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
