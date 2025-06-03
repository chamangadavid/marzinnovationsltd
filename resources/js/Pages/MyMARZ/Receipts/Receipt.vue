<template>
    <div class="receipt-container">
      <a-tabs v-model:activeKey="activeTab">
        <a-tab-pane key="list" tab="Receipts">
          <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold">Receipt Management</h2>
            <a-button type="primary" @click="showCreateModal">
              <template #icon><plus-outlined /></template>
              Create Receipt
            </a-button>
          </div>
  
          <a-table 
            :columns="columns" 
            :data-source="receipts" 
            :loading="loading"
            :pagination="pagination"
            @change="handleTableChange"
          >
          <template #bodyCell="{ column, record }">
            <template v-if="column.key === 'actions'">
              <a-space>
                <a-tooltip title="Edit">
                  <a-button size="small" @click="viewReceipt(record)">
                    <eye-outlined />
                  </a-button>
                </a-tooltip>
          
                <a-tooltip title="Download">
                  <a-button size="small" @click="downloadReceipt(record)">
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
              {{ formatCurrency(record.amount) }}
            </template>
          
            <template v-else-if="column.key === 'date'">
              {{ formatDate(record.date) }}
            </template>
          </template>
          
          </a-table>
        </a-tab-pane>
      </a-tabs>
  
      <!-- Create/Edit Receipt Modal -->
      <a-modal
        v-model:visible="modalVisible"
        :title="currentReceipt.id ? 'Edit Receipt' : 'Create Receipt'"
        width="800px"
        @ok="handleSubmit"
      >
        <a-form layout="vertical" :model="formState" ref="formRef">
          <a-row :gutter="16">
            <a-col :span="12">
              <a-form-item label="Customer" name="customer_id" :rules="[{ required: true, message: 'Please select customer' }]">
                <a-select
                  v-model:value="formState.customer_id"
                  show-search
                  option-filter-prop="label"
                  :options="customers"
                  :field-names="{ label: 'name', value: 'id' }"
                />
              </a-form-item>
            </a-col>
            <a-col :span="12">
              <a-form-item label="Invoice (Optional)" name="invoice_id">
                <a-select
                  v-model:value="formState.invoice_id"
                  show-search
                  option-filter-prop="label"
                  :options="invoices"
                  :field-names="{ label: 'invoice_number', value: 'id' }"
                />
              </a-form-item>
            </a-col>
          </a-row>
  
          <a-row :gutter="16">
            <a-col :span="8">
              <a-form-item label="Date" name="date" :rules="[{ required: true, message: 'Please select date' }]">
                <a-date-picker v-model:value="formState.date" style="width: 100%" />
              </a-form-item>
            </a-col>
            <a-col :span="8">
              <a-form-item label="Payment Method" name="payment_method" :rules="[{ required: true, message: 'Please select payment method' }]">
                <a-select v-model:value="formState.payment_method">
                  <a-select-option value="Cash">Cash</a-select-option>
                  <a-select-option value="Credit Card">Credit Card</a-select-option>
                  <a-select-option value="Bank Transfer">Bank Transfer</a-select-option>
                  <a-select-option value="Check">Check</a-select-option>
                  <a-select-option value="Airtel Money">Airtel Money</a-select-option>
                  <a-select-option value="MTN Money">MTN Money</a-select-option>
                  <a-select-option value="Zamtel Money">Zamtel Money</a-select-option>
                  <a-select-option value="Other">Other</a-select-option>
                </a-select>
              </a-form-item>
            </a-col>
            <a-col :span="8">
              <a-form-item label="Amount" name="amount" :rules="[{ required: true, message: 'Please enter amount' }]">
                <a-input-number v-model:value="formState.amount" :min="0" :step="0.01" style="width: 100%" />
              </a-form-item>
            </a-col>
          </a-row>
  
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
  import { 
    PlusOutlined, 
    EyeOutlined, 
    DownloadOutlined, 
    DeleteOutlined 
  } from '@ant-design/icons-vue';
  import dayjs from 'dayjs';
  import axios from 'axios';
  
  const activeTab = ref('list');
  const columns = [
    { title: 'Receipt #', dataIndex: 'receipt_number', key: 'receipt_number' },
    { title: 'Date', dataIndex: 'date', key: 'date' },
    { title: 'Customer', dataIndex: ['customer', 'name'], key: 'customer' },
    { title: 'Amount', key: 'amount' },
    { title: 'Payment Method', dataIndex: 'payment_method', key: 'payment_method' },
    { title: 'Actions', key: 'actions' }
  ];
  
  const receipts = ref([]);
  const loading = ref(false);
  const modalVisible = ref(false);
  const formRef = ref();
  const currentReceipt = ref({});
  const customers = ref([]);
  const invoices = ref([]);
  const pagination = reactive({
    current: 1,
    pageSize: 10,
    total: 0,
  });
  
  const formState = reactive({
    customer_id: undefined,
    invoice_id: undefined,
    date: dayjs(),
    payment_method: 'Cash',
    amount: undefined,
    notes: ''
  });
  
  // Fetch receipts
  const fetchReceipts = async (params = {}) => {
  try {
    loading.value = true;
    const response = await axios.get('/receipts/getReceipts', {
      params: {
        page: pagination.current,
        pageSize: pagination.pageSize,
        ...params
      }
    });

    // ✅ Safe check for expected response structure
    if (response.data && Array.isArray(response.data.data)) {
      receipts.value = response.data.data;
      pagination.total = response.data.meta?.total ?? response.data.data.length;
    } else {
      message.warning('Unexpected data format received');
    }
  } catch (error) {
    console.error('Fetch receipts error:', error); // Optional: helpful for debugging
    message.error('Failed to fetch receipts');
  } finally {
    loading.value = false;
  }
};

  // const fetchReceipts = async (params = {}) => {
  //   try {
  //     loading.value = true;
  //     const response = await axios.get('/receipts/getReceipts', {
  //       params: {
  //         page: pagination.current,
  //         pageSize: pagination.pageSize,
  //         ...params
  //       }
  //     });
  //     receipts.value = response.data.data;
  //     pagination.total = response.data.meta.total;
  //   } catch (error) {
  //     message.error('Failed to fetch receipts');
  //   } finally {
  //     loading.value = false;
  //   }
  // };
  
  // Fetch customers and invoices for dropdowns
  const fetchDropdownData = async () => {
    try {
      const [custRes, invRes] = await Promise.all([
        axios.get('/customers'),
        axios.get('/receipts/invoices')
      ]);
      customers.value = custRes.data;
      invoices.value = invRes.data;
    } catch (error) {
      message.error('Failed to fetch dropdown data');
    }
  };
  
  // Handle table pagination/sort changes
  const handleTableChange = (pag) => {
    pagination.current = pag.current;
    fetchReceipts();
  };
  
  // Show create modal
  const showCreateModal = () => {
    currentReceipt.value = {};
    Object.assign(formState, {
      customer_id: undefined,
      invoice_id: undefined,
      date: dayjs(),
      payment_method: 'Cash',
      amount: undefined,
      notes: ''
    });
    modalVisible.value = true;
  };
  
  // View receipt details
  const viewReceipt = (record) => {
    currentReceipt.value = record;
    Object.assign(formState, {
      customer_id: record.customer_id,
      invoice_id: record.invoice_id,
      date: dayjs(record.date),
      payment_method: record.payment_method,
      amount: record.amount,
      notes: record.notes
    });
    modalVisible.value = true;
  };
  
  // Download receipt PDF
  const downloadReceipt = async (record) => {
    try {
      const response = await axios.get(`/receipts/receipts/${record.id}/pdf`, {
        responseType: 'blob'
      });
      const url = window.URL.createObjectURL(new Blob([response.data]));
      const link = document.createElement('a');
      link.href = url;
      link.setAttribute('download', `receipt-${record.receipt_number}.pdf`);
      document.body.appendChild(link);
      link.click();
    } catch (error) {
      message.error('Failed to download receipt');
    }
  };
  
  // Confirm delete
  const confirmDelete = (record) => {
    Modal.confirm({
      title: 'Confirm Delete',
      content: `Are you sure you want to delete receipt ${record.receipt_number}?`,
      okText: 'Delete',
      okType: 'danger',
      cancelText: 'Cancel',
      onOk: () => deleteReceipt(record.id)
    });
  };
  
  // Delete receipt
  const deleteReceipt = async (id) => {
    try {
      await axios.delete(`/receipts/receipts/${id}`);
      message.success('Receipt deleted successfully');
      fetchReceipts();
    } catch (error) {
      message.error('Failed to delete receipt');
    }
  };
  
  // Handle form submission
  const handleSubmit = async () => {
    try {
      await formRef.value.validateFields();
      
      const payload = {
        ...formState,
        date: formState.date.format('YYYY-MM-DD')
      };
      
      if (currentReceipt.value.id) {
        await axios.put(`/receipts/receipts/${currentReceipt.value.id}`, payload);
        message.success('Receipt updated successfully');
      } else {
        await axios.post('/receipts/receipts', payload);
        message.success('Receipt created successfully');
      }
      
      modalVisible.value = false;
      fetchReceipts();
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
    fetchReceipts();
    fetchDropdownData();
  });
  </script>
  
  <style scoped>
  .receipt-container {
    padding: 20px;
    background: #fff;
    border-radius: 8px;
  }
  </style>