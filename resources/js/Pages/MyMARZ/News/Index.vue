<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Table, Popconfirm, Modal, Input, DatePicker, Button, message, Upload } from 'ant-design-vue';
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import { debounce } from 'lodash';
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
    title: 'Image',
    key: 'image',
  },
  {
    title: 'Status',
    key: 'status',
    filters: [
      { text: 'Published', value: true },
      { text: 'Draft', value: false },
    ],
    onFilter: (value, record) => record.is_published === value,
  },
  {
    title: 'Created At',
    dataIndex: 'created_at',
    key: 'created_at',
    sorter: (a, b) => a.created_at.localeCompare(b.created_at),
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
const isCreateModalVisible = ref(false);
const selectedRecord = ref(null);
const previewVisible = ref(false);
const previewImage = ref('');

const formState = ref({
  title: '',
  description: '',
  images: [],
});

const fileList = ref([]);

const fetchNews = async () => {
  try {
    loading.value = true;
    const params = {
      search: searchQuery.value,
    };
    
    if (dateRange.value && dateRange.value.length === 2) {
      params.start_date = dateRange.value[0].format('YYYY-MM-DD');
      params.end_date = dateRange.value[1].format('YYYY-MM-DD');
    }

    const response = await axios.get('/getNews', { params });
    dataSource.value = response.data.data.map((item, index) => ({
      ...item,
      serialNo: index + 1,
      key: item.id,
    }));
    filteredData.value = [...dataSource.value];
  } catch (error) {
    console.error('Error fetching news:', error);
    message.error('Failed to fetch news');
  } finally {
    loading.value = false;
  }
};

const publishNews = async (id) => {
  try {
    await axios.patch(`/news/${id}/publish`);
    message.success('News published successfully');
    fetchNews();
    isModalVisible.value = false;
  } catch (error) {
    console.error('Error publishing news:', error);
    message.error('Failed to publish news');
  }
};

const unpublishNews = async (id) => {
  try {
    await axios.patch(`/news/${id}/unpublish`);
    message.success('News unpublished successfully');
    fetchNews();
    isModalVisible.value = false;
  } catch (error) {
    console.error('Error unpublishing news:', error);
    message.error('Failed to unpublish news');
  }
};

const handleSearch = debounce(fetchNews, 300);

const handleDateRangeChange = (dates) => {
  dateRange.value = dates;
  fetchNews();
};

const resetFilters = () => {
  searchQuery.value = '';
  dateRange.value = [];
  fetchNews();
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
    images: [],
  };
  fileList.value = [];
};

const handleDelete = async (id) => {
  try {
    await axios.delete(`/news/${id}`);
    message.success('News deleted successfully');
    fetchNews();
  } catch (error) {
    console.error('Error deleting news:', error);
    message.error('Failed to delete news');
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
    formData.append('is_published', '0'); // Default to false (draft)
    
    fileList.value.forEach(file => {
      if (file.originFileObj) {
        formData.append('images[]', file.originFileObj);
      }
    });

    await axios.post('/news', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    });
    
    message.success('News created successfully (saved as draft)');
    fetchNews();
    handleCreateCancel();
  } catch (error) {
    console.error('Error creating news:', error);
    if (error.response?.data?.errors) {
      Object.values(error.response.data.errors).forEach(err => {
        message.error(err[0]);
      });
    } else {
      message.error('Failed to create news');
    }
  }
};

onMounted(() => {
  fetchNews();
});

watch(searchQuery, handleSearch);
</script>

<template>
  <Head title="News" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        News Management
      </h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
        <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
            <a-breadcrumb>
                <a-breadcrumb-item href="/">Home</a-breadcrumb-item>
                <a-breadcrumb-item>News Management</a-breadcrumb-item>
              </a-breadcrumb>
            <br />  
            <div class="flex flex-wrap gap-4 mb-4">
            <a-input-search
              v-model:value="searchQuery"
              placeholder="Search news..."
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
            
            <a-button @click="showCreateModal" 
            type="primary" class="ml-auto">
              <PlusOutlined/> Create News
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
                    title="Are you sure you want to delete this news?"
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
                  style="width: 30px; height: 30px; cursor: pointer"
                  @click="() => { previewImage = record.images[0]; previewVisible = true }"
                />
                <span v-else>No Image</span>
              </template>

              <template v-else-if="column.key === 'created_at'">
                <span>
                  {{ new Date(record.created_at).toLocaleDateString() }}
                </span>
              </template>

              <template v-else-if="column.key === 'title'">
                <span>
                  {{ record.title && record.title.length > 20
                    ? record.title.substring(0, 20) + '...'
                    : record.title }}
                </span>
              </template>

              <template v-else-if="column.key === 'status'">
                <a-tag :color="record.is_published ? 'green' : 'orange'">
                  {{ record.is_published ? 'Published' : 'Draft' }}
                </a-tag>
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
            :footer="null"
            @cancel="isModalVisible = false"
          >
          <hr/><br/>
            <div v-if="selectedRecord" class="space-y-4">
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <p class="font-semibold">Title: {{ selectedRecord.title }}</p>
                </div>
                 <div>
                    <p class="font-semibold">Status:
                    <a-tag :color="selectedRecord.is_published ? 'green' : 'orange'">
                      {{ selectedRecord.is_published ? 'Published' : 'Draft' }}
                    </a-tag>
                </p>
                </div>
                <div>
                  <p class="font-semibold">Description:</p>
                  <p class="whitespace-pre-line">{{ selectedRecord.description }}</p>
                </div>
                <div>
                  <p class="font-semibold">Images:</p>
                  <div class="flex flex-wrap gap-2 mt-2">
                    <img 
                      v-for="(image, index) in selectedRecord.images" 
                      :key="index" 
                      :src="image" 
                      style="width: 150px; height: auto; cursor: pointer"
                      @click="() => { previewImage = image; previewVisible = true }"
                    />
                  </div>
                </div>
                <div>
                  <p class="font-semibold">Created At:</p>
                  <p>{{ new Date(selectedRecord.created_at).toLocaleString() }}</p>
                </div>
              </div>
            </div>
            <hr><br/>
              <div>
                <a-button 
                v-if="!selectedRecord.is_published"
                type="primary" 
                @click="publishNews(selectedRecord.id)"
              >
                Publish News
              </a-button>
              <a-button 
                v-else
                type="primary" 
                @click="unpublishNews(selectedRecord.id)"
                danger>
                Unpublish News
              </a-button>
              <a-button @click="isModalVisible = false">Close</a-button>
           
              </div>
          </a-modal>

          <!-- Create Modal -->
          <a-modal
            v-model:visible="isCreateModalVisible"
            title="Create New News"
            width="750px"
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
    <Head title="News" />

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
