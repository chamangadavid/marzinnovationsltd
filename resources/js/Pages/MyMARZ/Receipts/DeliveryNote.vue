<template>
    <div class="delivery-note-container">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold">Delivery Notes</h2>
        <a-button type="primary" @click="showCreateModal">
          <template #icon><plus-outlined /></template>
          Create Delivery Note
        </a-button>
      </div>
  
      <a-table 
        :columns="columns" 
        :data-source="deliveryNotes" 
        :loading="loading"
        :pagination="pagination"
        @change="handleTableChange"
      >
        <template #bodyCell="{ column, record }">
          <template v-if="column.key === 'actions'">
            <a-space>
              <a-tooltip title="View">
              <a-button size="small" @click="viewDeliveryNote(record)">
                <eye-outlined />
              </a-button>
            </a-tooltip>

            <a-tooltip title="Download">
              <a-button size="small" @click="downloadDeliveryNote(record)">
                <download-outlined />
              </a-button>
            </a-tooltip>

            <a-tooltip title="Delete">
              <a-button size="small" danger @click="confirmDelete(record)">
                <delete-outlined />
              </a-button>
            </a-tooltip>
            </a-space>
          </template>
          
          <template v-else-if="column.key === 'amount'">
            {{ formatCurrency(record.total) }}
          </template>
          
          <template v-else-if="column.key === 'date'">
            {{ formatDate(record.date) }}
          </template>
        </template>
      </a-table>
  
      <!-- Create/Edit Modal -->
      <a-modal
        v-model:visible="modalVisible"
        :title="currentDeliveryNote.id ? 'Edit Delivery Note' : 'Create Delivery Note'"
        width="800px"
        @ok="handleSubmit"
      >
        <a-form layout="vertical" :model="formState" ref="formRef">
          <a-row :gutter="16">
            <a-col :span="8">
              <a-form-item label="Date" name="date" :rules="[{ required: true, message: 'Please select date' }]">
                <a-date-picker v-model:value="formState.date" style="width: 100%" />
              </a-form-item>
            </a-col>
            <a-col :span="8">
              <a-form-item label="Customer" name="customer_id" :rules="[{ required: true, message: 'Please select customer' }]">
                <a-select
                v-model:value="formState.customer_id"
                show-search
                option-filter-prop="label"
                :options="customers"
                :field-names="{ label: 'name', value: 'id' }"
                :loading="customersLoading"
              ></a-select>
              </a-form-item>
            </a-col>
            <a-col :span="8">
              <a-form-item label="Delivery Address" name="delivery_address" :rules="[{ required: true, message: 'Please enter delivery address' }]">
                <a-input v-model:value="formState.delivery_address" />
              </a-form-item>
            </a-col>
          </a-row>
  
          <a-row :gutter="16">
            <a-col :span="12">
              <a-form-item label="Reference (PO/Invoice #)" name="reference_number">
                <a-input v-model:value="formState.reference_number" />
              </a-form-item>
            </a-col>
            <a-col :span="12">
              <a-form-item label="Vehicle Number" name="vehicle_number">
                <a-input v-model:value="formState.vehicle_number" />
              </a-form-item>
            </a-col>
          </a-row>
  
          <div class="mb-4">
            <h4 class="text-base font-medium mb-2">Items</h4>
            <a-table 
              :columns="itemColumns" 
              :data-source="formState.items"
              :pagination="false"
            >
              <template #bodyCell="{ column, record, index }">
                <template v-if="column.key === 'actions'">
                  <a-button type="link" danger @click="removeItem(index)">
                    <delete-outlined />
                  </a-button>
                </template>
                <template v-else>
                  <a-form-item 
                    :name="['items', index, column.dataIndex]"
                    :rules="column.rules"
                    style="margin-bottom: 0"
                  >
                    <component
                      :is="column.component || 'a-input'"
                      v-model:value="record[column.dataIndex]"
                      v-bind="column.props || {}"
                    />
                  </a-form-item>
                </template>
              </template>
            </a-table>
            <a-button class="mt-2" @click="addItem">
              <plus-outlined /> Add Item
            </a-button>
          </div>
  
          <a-form-item label="Notes" name="notes">
            <a-textarea v-model:value="formState.notes" :rows="3" />
          </a-form-item>
  
          <a-form-item label="Received By" name="received_by">
            <a-input v-model:value="formState.received_by" />
          </a-form-item>
        </a-form>
      </a-modal>
    </div>
  </template>
  
  <script setup>
  import { ref, reactive, onMounted } from 'vue';
  import { message, Modal } from 'ant-design-vue';
  import { 
    PlusOutlined, 
    EyeOutlined, 
    DownloadOutlined, 
    DeleteOutlined 
  } from '@ant-design/icons-vue';
  import dayjs from 'dayjs';
  
  const columns = [
    { title: 'DN #', dataIndex: 'delivery_note_number', key: 'delivery_note_number' },
    { title: 'Date', dataIndex: 'date', key: 'date' },
    { title: 'Customer', dataIndex: ['customer', 'name'], key: 'customer' },
    { title: 'Reference', dataIndex: 'reference_number', key: 'reference' },
    { title: 'Actions', key: 'actions' }
  ];
  
  const itemColumns = [
  { 
    title: 'Description', 
    dataIndex: 'description', 
    key: 'description',
    rules: [{ required: true, message: 'Description is required' }]
  },
  { 
    title: 'Quantity', 
    dataIndex: 'quantity', 
    key: 'quantity',
    width: '120px',
    component: 'a-input-number',
    props: { min: 0.01, step: 0.01, style: 'width: 100%' },
    rules: [{ required: true, message: 'Quantity is required' }]
  },
  { 
    title: 'Unit', 
    dataIndex: 'unit', 
    key: 'unit',
    width: '100px',
    component: 'a-input',
    props: { placeholder: 'pcs, kg, etc' }
  },
  { 
    title: 'Unit Price', 
    dataIndex: 'unit_price', 
    key: 'unit_price',
    width: '150px',
    component: 'a-input-number',
    props: { min: 0, step: 0.01, style: 'width: 100%' },
    rules: [{ required: true, message: 'Unit price is required' }]
  },
  { 
    title: 'Actions', 
    key: 'actions',
    width: '50px'
  }
];


  
  const deliveryNotes = ref([]);
  const loading = ref(false);
  const modalVisible = ref(false);
  const formRef = ref();
  const currentDeliveryNote = ref({});
  const customers = ref([]);
  const pagination = reactive({
    current: 1,
    pageSize: 10,
    total: 0,
  });
  
  const formState = reactive({
    date: dayjs(),
    customer_id: undefined,
    delivery_address: '',
    reference_number: '',
    vehicle_number: '',
    items: [],
    notes: '',
    received_by: ''
  });
  
  // Fetch delivery notes
  const fetchDeliveryNotes = async (params = {}) => {
  try {
    loading.value = true;
    const response = await axios.get('/receipts/delivery-notes', {
      params: {
        page: pagination.current,
        pageSize: pagination.pageSize,
        ...params
      }
    });

    if (response.data?.data) {
      deliveryNotes.value = response.data.data;
      pagination.total = response.data.meta?.total || 0;
    } else {
      throw new Error('Invalid response structure');
    }
  } catch (error) {
    console.error('Fetch delivery notes error:', error);
    message.error(error.response?.data?.message || 'Failed to fetch delivery notes');
  } finally {
    loading.value = false;
  }
};

const customersLoading = ref(false);

const fetchCustomers = async () => {
  try {
    customersLoading.value = true;
    const response = await axios.get('/customers');
    customers.value = Array.isArray(response.data) ? response.data : [];
  } catch (error) {
    console.error('Fetch customers error:', error);
    message.error('Failed to fetch customers. Please try again later.');
  } finally {
    customersLoading.value = false;
  }
};
  
  // Handle table pagination/sort changes
  const handleTableChange = (pag) => {
    pagination.current = pag.current;
    fetchDeliveryNotes();
  };
  
  // Show create modal
  const showCreateModal = () => {
    currentDeliveryNote.value = {};
    Object.assign(formState, {
      date: dayjs(),
      customer_id: undefined,
      delivery_address: '',
      reference_number: '',
      vehicle_number: '',
      items: [],
      notes: '',
      received_by: ''
    });
    modalVisible.value = true;
  };
  
  // View delivery note
  const viewDeliveryNote = (record) => {
  currentDeliveryNote.value = record;
  Object.assign(formState, {
    date: dayjs(record.date),
    customer_id: record.customer_id,
    delivery_address: record.delivery_address,
    reference_number: record.reference_number,
    vehicle_number: record.vehicle_number,
    items: record.items.map(item => ({
      description: item.description,
      quantity: item.quantity,
      unit: item.unit,
      unit_price: item.unit_price
    })),
    notes: record.notes,
    received_by: record.received_by
  });
  modalVisible.value = true;
};


  // Download delivery note PDF
  const downloadDeliveryNote = async (record) => {
    try {
      const response = await axios.get(`/receipts/delivery-notes/${record.id}/pdf`, {
        responseType: 'blob'
      });
      const url = window.URL.createObjectURL(new Blob([response.data]));
      const link = document.createElement('a');
      link.href = url;
      link.setAttribute('download', `delivery-note-${record.delivery_note_number}.pdf`);
      document.body.appendChild(link);
      link.click();
    } catch (error) {
      message.error('Failed to download delivery note');
    }
  };
  
  // Confirm delete
  const confirmDelete = (record) => {
    Modal.confirm({
      title: 'Confirm Delete',
      content: `Are you sure you want to delete delivery note ${record.delivery_note_number}?`,
      okText: 'Delete',
      okType: 'danger',
      cancelText: 'Cancel',
      onOk: () => deleteDeliveryNote(record.id)
    });
  };
  
  // Delete delivery note
  const deleteDeliveryNote = async (id) => {
    try {
      await axios.delete(`/receipts/delivery-notes/${id}`);
      message.success('Delivery note deleted successfully');
      fetchDeliveryNotes();
    } catch (error) {
      message.error('Failed to delete delivery note');
    }
  };
  
  // Add item to form
  const addItem = () => {
  formState.items.push({
    description: '',
    quantity: 1,
    unit: 'pcs',
    unit_price: 0
  });
};

  
  // Remove item from form
  const removeItem = (index) => {
    formState.items.splice(index, 1);
  };
  
  // Handle form submission
  const handleSubmit = async () => {
  try {
    await formRef.value.validateFields();
    
    if (formState.items.length === 0) {
      message.error('Please add at least one item');
      return;
    }
    
    // Prepare items with proper numeric values
    const items = formState.items.map(item => ({
      ...item,
      quantity: Number(item.quantity),
      unit_price: Number(item.unit_price)
    }));
    
    const payload = {
      ...formState,
      date: formState.date.format('YYYY-MM-DD'),
      items: items
    };
    
    const url = currentDeliveryNote.value.id 
      ? `/receipts/delivery-notes/${currentDeliveryNote.value.id}`
      : '/receipts/delivery-notes';
      
    const method = currentDeliveryNote.value.id ? 'put' : 'post';
    
    await axios[method](url, payload);
    message.success(`Delivery note ${currentDeliveryNote.value.id ? 'updated' : 'created'} successfully`);
    modalVisible.value = false;
    fetchDeliveryNotes();
  } catch (error) {
    if (error.response?.data?.errors) {
      message.error(Object.values(error.response.data.errors).join('\n'));
    } else {
      message.error(error.message || 'An error occurred');
    }
  }
};
  
  // Format currency
  const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: 'ZKW'
    }).format(amount);
  };
  
  // Format date
  const formatDate = (date) => {
    return dayjs(date).format('DD/MM/YYYY');
  };
  
  onMounted(() => {
    fetchDeliveryNotes();
    fetchCustomers();
  });
  </script>
  
  <style scoped>
  .delivery-note-container {
    padding: 20px;
    background: #fff;
    border-radius: 8px;
  }
  </style>