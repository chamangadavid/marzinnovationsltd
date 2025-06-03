<template>
    <div class="purchase-order-container">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold">Purchase Orders</h2>
        <a-button type="primary" @click="showCreateModal">
          <template #icon><plus-outlined /></template>
          Create PO
        </a-button>
      </div>
  
      <a-table 
        :columns="columns" 
        :data-source="purchaseOrders" 
        :loading="loading"
        :pagination="pagination"
        @change="handleTableChange"
      >
        <template #bodyCell="{ column, record }">
          <template v-if="column.key === 'actions'">
            <a-space>
              <a-tooltip title="View">
              <a-button size="small" @click="viewPO(record)">
                <eye-outlined />
              </a-button>
            </a-tooltip>

            <a-tooltip title="Download">
              <a-button size="small" @click="downloadPO(record)">
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
          
          <template v-else-if="column.key === 'status'">
            <a-tag :color="getStatusColor(record.status)">
              {{ record.status.toUpperCase() }}
            </a-tag>
          </template>
        </template>
      </a-table>
  
      <!-- Create/Edit PO Modal -->
      <a-modal
        v-model:visible="modalVisible"
        :title="currentPO.id ? 'Edit Purchase Order' : 'Create Purchase Order'"
        width="900px"
        @ok="handleSubmit"
      >
        <a-form layout="vertical" :model="formState" ref="formRef">
          <a-row :gutter="16">
            <a-col :span="8">
              <a-form-item label="PO Date" name="date" :rules="[{ required: true, message: 'Please select date' }]">
                <a-date-picker v-model:value="formState.date" style="width: 100%" />
              </a-form-item>
            </a-col>
            <a-col :span="8">
              <a-form-item label="Expected Delivery" name="expected_delivery_date">
                <a-date-picker v-model:value="formState.expected_delivery_date" style="width: 100%" />
              </a-form-item>
            </a-col>
            <a-col :span="8">
              <a-form-item label="Status" name="status">
                <a-select v-model:value="formState.status">
                  <a-select-option value="draft">Draft</a-select-option>
                  <a-select-option value="sent">Sent</a-select-option>
                  <a-select-option value="approved">Approved</a-select-option>
                  <a-select-option value="received">Received</a-select-option>
                  <a-select-option value="cancelled">Cancelled</a-select-option>
                </a-select>
              </a-form-item>
            </a-col>
          </a-row>
  
          <a-row :gutter="16">
            <a-col :span="12">
              <a-form-item label="Supplier" name="supplier_id" :rules="[{ required: true, message: 'Please select supplier' }]">
                <a-select
                  v-model:value="formState.supplier_id"
                  show-search
                  option-filter-prop="label"
                  :options="suppliers"
                  :field-names="{ label: 'name', value: 'id' }"
                />
              </a-form-item>
            </a-col>
            <a-col :span="12">
              <a-form-item label="Delivery Address" name="delivery_address" :rules="[{ required: true, message: 'Please enter delivery address' }]">
                <a-textarea v-model:value="formState.delivery_address" :rows="2" />
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
                <template v-else-if="column.key === 'total'">
                  {{ formatCurrency(record.quantity * record.unit_price * (1 + (record.tax_rate || 0) / 100)) }}
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
                      @change="calculateTotals"
                    />
                  </a-form-item>
                </template>
              </template>
            </a-table>
            <a-button class="mt-2" @click="addItem">
              <plus-outlined /> Add Item
            </a-button>
          </div>
  
          <a-row :gutter="16">
            <a-col :span="12">
              <a-form-item label="Terms & Conditions" name="terms">
                <a-textarea v-model:value="formState.terms" :rows="3" />
              </a-form-item>
            </a-col>
            <a-col :span="12">
              <a-form-item label="Notes" name="notes">
                <a-textarea v-model:value="formState.notes" :rows="3" />
              </a-form-item>
            </a-col>
          </a-row>
  
          <div class="totals-section">
            <div class="total-row">
              <span>Subtotal:</span>
              <span>{{ formatCurrency(formState.subtotal) }}</span>
            </div>
            <div class="total-row">
              <span>Tax:</span>
              <span>{{ formatCurrency(formState.tax) }}</span>
            </div>
            <div class="total-row grand-total">
              <span>Total:</span>
              <span>{{ formatCurrency(formState.total) }}</span>
            </div>
          </div>
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
    { title: 'PO #', dataIndex: 'po_number', key: 'po_number' },
    { title: 'Date', dataIndex: 'date', key: 'date' },
    { title: 'Supplier', dataIndex: ['supplier', 'name'], key: 'supplier' },
    { title: 'Status', key: 'status' },
    { title: 'Amount', key: 'amount' },
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
      title: 'Qty', 
      dataIndex: 'quantity', 
      key: 'quantity',
      width: '120px',
      component: 'a-input-number',
      props: { min: 0.001, step: 0.001, style: 'width: 100%' },
      rules: [{ required: true, message: 'Quantity is required' }]
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
      title: 'Tax Rate %', 
      dataIndex: 'tax_rate', 
      key: 'tax_rate',
      width: '120px',
      component: 'a-input-number',
      props: { min: 0, max: 100, step: 0.1, style: 'width: 100%' }
    },
    { 
      title: 'Total', 
      key: 'total',
      width: '120px'
    },
    { 
      title: '', 
      key: 'actions',
      width: '50px'
    }
  ];
  
  const purchaseOrders = ref([]);
  const loading = ref(false);
  const modalVisible = ref(false);
  const formRef = ref();
  const currentPO = ref({});
  const suppliers = ref([]);
  const pagination = reactive({
    current: 1,
    pageSize: 10,
    total: 0,
  });
  
  const formState = reactive({
    date: dayjs(),
    expected_delivery_date: null,
    supplier_id: undefined,
    delivery_address: '',
    terms: '',
    notes: '',
    status: 'draft',
    items: [],
    subtotal: 0,
    tax: 0,
    total: 0
  });
  
  // Fetch purchase orders
  const fetchPurchaseOrders = async (params = {}) => {
  try {
    loading.value = true;
    const response = await axios.get('/receipts/purchase-orders', {
      params: {
        page: pagination.current,
        pageSize: pagination.pageSize,
        ...params
      }
    });

    // Validate response structure
    if (response.data && Array.isArray(response.data.data)) {
      purchaseOrders.value = response.data.data;
      pagination.total = response.data.meta?.total || 0;
    } else {
      console.error('Invalid API response structure:', response.data);
      throw new Error('Invalid data structure received from server');
    }
  } catch (error) {
    console.error('Error fetching purchase orders:', {
      message: error.message,
      response: error.response?.data,
      stack: error.stack
    });

    // Different error messages based on status
    if (error.response) {
      if (error.response.status === 404) {
        purchaseOrders.value = [];
        message.warning('No purchase orders found');
      } else {
        message.error(error.response.data?.message || 'Failed to fetch purchase orders');
      }
    } else if (error.request) {
      message.error('Network error - please check your connection');
    } else {
      message.error(error.message || 'An error occurred');
    }
  } finally {
    loading.value = false;
  }
};
  // const fetchPurchaseOrders = async (params = {}) => {
  //   try {
  //     loading.value = true;
  //     const response = await axios.get('/receipts/purchase-orders', {
  //       params: {
  //         page: pagination.current,
  //         pageSize: pagination.pageSize,
  //         ...params
  //       }
  //     });
  //     purchaseOrders.value = response.data.data;
  //     pagination.total = response.data.meta.total;
  //   } catch (error) {
  //     message.error('Failed to fetch purchase orders');
  //   } finally {
  //     loading.value = false;
  //   }
  // };
  
  // Fetch suppliers for dropdown
  const fetchSuppliers = async () => {
  try {
    loading.value = true;
    const response = await axios.get('/receipts/getSuppliers');
    
    // Ensure we always have an array
    suppliers.value = Array.isArray(response.data) ? response.data : [];
    
    if (suppliers.value.length === 0) {
      console.warn('Suppliers list is empty');
    }
  } catch (error) {
    console.error('Error fetching suppliers:', {
      message: error.message,
      response: error.response?.data,
      stack: error.stack
    });
    
    // Only show error if it's not a 404 (might be first run)
    if (error.response?.status !== 404) {
      message.error('Failed to fetch suppliers. Please try again later.');
    }
  } finally {
    loading.value = false;
  }
};

//   const fetchSuppliers = async () => {
//   try {
//     const response = await axios.get('/receipts/getSuppliers');
//     suppliers.value = response.data;
//   } catch (error) {
 
//     message.error('Failed to fetch suppliers');
//   }
// };

  
  // Handle table pagination/sort changes
  const handleTableChange = (pag) => {
    pagination.current = pag.current;
    fetchPurchaseOrders();
  };
  
  // Show create modal
  const showCreateModal = () => {
    currentPO.value = {};
    Object.assign(formState, {
      date: dayjs(),
      expected_delivery_date: null,
      supplier_id: undefined,
      delivery_address: '',
      terms: '',
      notes: '',
      status: 'draft',
      items: [],
      subtotal: 0,
      tax: 0,
      total: 0
    });
    modalVisible.value = true;
  };
  
  // View PO details
  const viewPO = (record) => {
    currentPO.value = record;
    Object.assign(formState, {
      date: dayjs(record.date),
      expected_delivery_date: record.expected_delivery_date ? dayjs(record.expected_delivery_date) : null,
      supplier_id: record.supplier_id,
      delivery_address: record.delivery_address,
      terms: record.terms,
      notes: record.notes,
      status: record.status,
      items: record.items.map(item => ({
        description: item.description,
        quantity: item.quantity,
        unit_price: item.unit_price,
        tax_rate: item.tax_rate
      })),
      subtotal: record.subtotal,
      tax: record.tax,
      total: record.total
    });
    modalVisible.value = true;
  };
  
  // Download PO PDF
  const downloadPO = async (record) => {
    try {
      const response = await axios.get(`/receipts/purchase-orders/${record.id}/pdf`, {
        responseType: 'blob'
      });
      const url = window.URL.createObjectURL(new Blob([response.data]));
      const link = document.createElement('a');
      link.href = url;
      link.setAttribute('download', `purchase-order-${record.po_number}.pdf`);
      document.body.appendChild(link);
      link.click();
    } catch (error) {
      message.error('Failed to download purchase order');
    }
  };
  
  // Confirm delete
  const confirmDelete = (record) => {
    Modal.confirm({
      title: 'Confirm Delete',
      content: `Are you sure you want to delete PO ${record.po_number}?`,
      okText: 'Delete',
      okType: 'danger',
      cancelText: 'Cancel',
      onOk: () => deletePO(record.id)
    });
  };
  
  // Delete PO
  const deletePO = async (id) => {
    try {
      await axios.delete(`/receipts/purchase-orders/${id}`);
      message.success('Purchase order deleted successfully');
      fetchPurchaseOrders();
    } catch (error) {
      message.error('Failed to delete purchase order');
    }
  };
  
  // Add item to form
  const addItem = () => {
    formState.items.push({
      description: '',
      quantity: 1,
      unit_price: 0,
      tax_rate: 0
    });
  };
  
  // Remove item from form
  const removeItem = (index) => {
    formState.items.splice(index, 1);
    calculateTotals();
  };
  
  // Calculate totals
  const calculateTotals = () => {
    const subtotal = formState.items.reduce((sum, item) => {
      return sum + (item.quantity || 0) * (item.unit_price || 0);
    }, 0);
    
    const tax = formState.items.reduce((sum, item) => {
      const itemTotal = (item.quantity || 0) * (item.unit_price || 0);
      const taxRate = item.tax_rate || 0;
      return sum + (itemTotal * taxRate / 100);
    }, 0);
    
    formState.subtotal = subtotal;
    formState.tax = tax;
    formState.total = subtotal + tax;
  };
  
  // Handle form submission
  const handleSubmit = async () => {
    try {
      await formRef.value.validateFields();
      
      if (formState.items.length === 0) {
        message.error('Please add at least one item');
        return;
      }
      
      const payload = {
        ...formState,
        date: formState.date.format('YYYY-MM-DD'),
        expected_delivery_date: formState.expected_delivery_date?.format('YYYY-MM-DD'),
        items: formState.items.map(item => ({
          description: item.description,
          quantity: item.quantity,
          unit_price: item.unit_price,
          tax_rate: item.tax_rate
        }))
      };
      
      if (currentPO.value.id) {
        await axios.put(`/receipts/purchase-orders/${currentPO.value.id}`, payload);
        message.success('Purchase order updated successfully');
      } else {
        await axios.post('/receipts/purchase-orders', payload);
        message.success('Purchase order created successfully');
      }
      
      modalVisible.value = false;
      fetchPurchaseOrders();
    } catch (error) {
      if (error.response?.data?.errors) {
        message.error(Object.values(error.response.data.errors).join('\n'));
      } else {
        message.error(error.message || 'An error occurred');
      }
    }
  };
  
  // Get status color for tag
  const getStatusColor = (status) => {
    const colors = {
      draft: 'default',
      sent: 'blue',
      approved: 'green',
      received: 'cyan',
      cancelled: 'red'
    };
    return colors[status] || 'default';
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
    fetchPurchaseOrders();
    fetchSuppliers();
  });
  </script>
  
  <style scoped>
  .purchase-order-container {
    padding: 20px;
    background: #fff;
    border-radius: 8px;
  }
  
  .totals-section {
    margin-top: 20px;
    padding: 10px;
    background: #f9f9f9;
    border-radius: 4px;
  }
  
  .total-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 5px;
  }
  
  .grand-total {
    font-weight: bold;
    font-size: 16px;
    margin-top: 5px;
    padding-top: 5px;
    border-top: 1px solid #eee;
  }
  </style>