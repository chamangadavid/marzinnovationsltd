<template>
    <div class="supplier-container">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold">Suppliers</h2>
        <a-button type="primary" @click="showCreateModal">
          <template #icon><plus-outlined /></template>
          Add Supplier
        </a-button>
      </div>
  
      <a-table 
        :columns="columns" 
        :data-source="suppliers" 
        :loading="loading"
        :pagination="pagination"
        @change="handleTableChange"
      >
        <template #bodyCell="{ column, record }">
          <template v-if="column.key === 'actions'">
            <a-space>
              <a-tooltip title="Edit Supplier">
              <a-button size="small" @click="editSupplier(record)">
                <edit-outlined />
              </a-button>
            </a-tooltip>

            <a-tooltip title="Delete Supplier">
              <a-button size="small" danger @click="confirmDelete(record)">
                <delete-outlined />
              </a-button>
            </a-tooltip>
            </a-space>
          </template>
        </template>
      </a-table>
  
      <!-- Create/Edit Modal -->
      <a-modal
        v-model:visible="modalVisible"
        :title="currentSupplier.id ? 'Edit Supplier' : 'Add Supplier'"
        @ok="handleSubmit"
      >
        <a-form layout="vertical" :model="formState" ref="formRef">
          <a-form-item label="Name" name="name" :rules="[{ required: true, message: 'Please enter name' }]">
            <a-input v-model:value="formState.name"   style="border: 1px solid #e9e9e9; border-radius: 8px; height: 32px; line-height: 32px;"/>
          </a-form-item>
          <a-form-item label="Email" name="email">
            <a-input v-model:value="formState.email" type="email"   style="border: 1px solid #e9e9e9; border-radius: 8px; height: 32px; line-height: 32px;"/>
          </a-form-item>
          <a-form-item label="Phone" name="phone">
            <a-input v-model:value="formState.phone"   style="border: 1px solid #e9e9e9; border-radius: 8px; height: 32px; line-height: 32px;"/>
          </a-form-item>
          <a-form-item label="Address" name="address">
            <a-textarea v-model:value="formState.address" :rows="2" />
          </a-form-item>
          <a-form-item label="Tax ID" name="tax_id">
            <a-input v-model:value="formState.tax_id"   style="border: 1px solid #e9e9e9; border-radius: 8px; height: 32px; line-height: 32px;"/>
          </a-form-item>
          <a-form-item label="Notes" name="notes">
            <a-textarea v-model:value="formState.notes" :rows="3" />
          </a-form-item>
        </a-form>
      </a-modal>
    </div>
  </template>
  
  <script setup>
  import { ref, reactive, onMounted } from 'vue';
  import { message, Modal } from 'ant-design-vue';
  import { PlusOutlined, EditOutlined, DeleteOutlined } from '@ant-design/icons-vue';
  
  const columns = [
    { title: 'Name', dataIndex: 'name', key: 'name' },
    { title: 'Email', dataIndex: 'email', key: 'email' },
    { title: 'Phone', dataIndex: 'phone', key: 'phone' },
    { title: 'Tax ID', dataIndex: 'tax_id', key: 'tax_id' },
    { title: 'Actions', key: 'actions' }
  ];
  
  const suppliers = ref([]);
  const loading = ref(false);
  const modalVisible = ref(false);
  const formRef = ref();
  const currentSupplier = ref({});
  const pagination = reactive({
    current: 1,
    pageSize: 10,
    total: 0,
  });
  
  const formState = reactive({
    name: '',
    email: '',
    phone: '',
    address: '',
    tax_id: '',
    notes: ''
  });
  
  // Fetch suppliers
  const fetchSuppliers = async (params = {}) => {
  try {
    loading.value = true;
    const response = await axios.get('/receipts/getSuppliers', {
      params: {
        page: pagination.current,
        pageSize: pagination.pageSize,
        ...params
      }
    });
    
    // Check if response has the expected structure
    if (response.data && Array.isArray(response.data.data)) {
      suppliers.value = response.data.data;
      pagination.total = response.data.meta?.total || 0;
    } else {
      // Handle unexpected response structure
      suppliers.value = response.data || [];
      pagination.total = response.data?.length || 0;
    }
  } catch (error) {
    console.error('Fetch suppliers error:', error);
    // Only show error if there are no suppliers loaded
    if (!suppliers.value.length) {
      message.error('Failed to fetch suppliers');
    }
  } finally {
    loading.value = false;
  }
};
  // const fetchSuppliers = async (params = {}) => {
  //   try {
  //     loading.value = true;
  //     const response = await axios.get('/receipts/getSuppliers', {
  //       params: {
  //         page: pagination.current,
  //         pageSize: pagination.pageSize,
  //         ...params
  //       }
  //     });
  //     suppliers.value = response.data.data;
  //     pagination.total = response.data.meta.total;
  //   } catch (error) {
  //     message.error('Failed to fetch suppliers');
  //   } finally {
  //     loading.value = false;
  //   }
  // };
  
  // Handle table pagination/sort changes
  const handleTableChange = (pag) => {
    pagination.current = pag.current;
    fetchSuppliers();
  };
  
  // Show create modal
  const showCreateModal = () => {
    currentSupplier.value = {};
    Object.assign(formState, {
      name: '',
      email: '',
      phone: '',
      address: '',
      tax_id: '',
      notes: ''
    });
    modalVisible.value = true;
  };
  
  // Edit supplier
  const editSupplier = (record) => {
    currentSupplier.value = record;
    Object.assign(formState, {
      name: record.name,
      email: record.email,
      phone: record.phone,
      address: record.address,
      tax_id: record.tax_id,
      notes: record.notes
    });
    modalVisible.value = true;
  };
  
  // Confirm delete
  const confirmDelete = (record) => {
    Modal.confirm({
      title: 'Confirm Delete',
      content: `Are you sure you want to delete ${record.name}?`,
      okText: 'Delete',
      okType: 'danger',
      cancelText: 'Cancel',
      onOk: () => deleteSupplier(record.id)
    });
  };
  
  // Delete supplier
  const deleteSupplier = async (id) => {
    try {
      await axios.delete(`/receipts/suppliers/${id}`);
      message.success('Supplier deleted successfully');
      fetchSuppliers();
    } catch (error) {
      message.error('Failed to delete supplier');
    }
  };
  
  // Handle form submission
  const handleSubmit = async () => {
    try {
      await formRef.value.validateFields();
      
      if (currentSupplier.value.id) {
        await axios.put(`/receipts/suppliers/${currentSupplier.value.id}`, formState);
        message.success('Supplier updated successfully');
      } else {
        await axios.post('/receipts/suppliers', formState);
        message.success('Supplier created successfully');
      }
      
      modalVisible.value = false;
      fetchSuppliers();
    } catch (error) {
      if (error.response?.data?.errors) {
        message.error(Object.values(error.response.data.errors).join('\n'));
      } else {
        message.error(error.message || 'An error occurred');
      }
    }
  };
  
  onMounted(() => {
    fetchSuppliers();
  });
  </script>
  
  <style scoped>
  .supplier-container {
    padding: 20px;
    background: #fff;
    border-radius: 8px;
  }
  </style>