<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Table, Popconfirm, Modal, Input, Button, message, DatePicker, Select, InputNumber } from 'ant-design-vue';
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import { debounce } from 'lodash';
import { PlusOutlined, DownloadOutlined, BarChartOutlined, SaveOutlined, DeleteOutlined, EditOutlined } from '@ant-design/icons-vue';

const { RangePicker } = DatePicker;
const { Option } = Select;

const columns = [
  {
    title: 'ID',
    dataIndex: 'id',
    key: 'id',
    sorter: (a, b) => a.id - b.id,
  },
  {
    title: 'Trans ID',
    dataIndex: 'transactionId',
    key: 'transactionId',
    sorter: (a, b) => a.transactionId.localeCompare(b.transactionId),
  },
  {
    title: 'Trans Name',
    dataIndex: 'transactionName',
    key: 'transactionName',
    sorter: (a, b) => a.transactionName.localeCompare(b.transactionName),
  },
  {
    title: 'Client Name',
    dataIndex: 'clientName',
    key: 'clientName',
    sorter: (a, b) => a.clientName.localeCompare(b.clientName),
  },
  
  {
    title: 'Qty',
    dataIndex: 'quantity',
    key: 'quantity',
    sorter: (a, b) => a.quantity - b.quantity,
  },
  {
    title: 'Unit Price',
    dataIndex: 'unitPrice',
    key: 'unitPrice',
    sorter: (a, b) => a.unitPrice - b.unitPrice,
  },
  {
    title: 'Amount',
    dataIndex: 'totalAmount',
    key: 'totalAmount',
    sorter: (a, b) => a.totalAmount - b.totalAmount,
  },
  {
    title: 'Paid',
    dataIndex: 'initial_pay',
    key: 'initial_pay',
    sorter: (a, b) => a.initial_pay - b.initial_pay,
  },
  {
    title: 'Balance',
    dataIndex: 'balance',
    key: 'balance',
    sorter: (a, b) => a.balance - b.balance,
  },
  {
    title: 'Status',
    key: 'status',
    filters: [
      { text: 'Paid', value: 'Paid' },
      { text: 'Pending', value: 'Pending' },
      { text: 'Partially_Paid', value: 'Partially_Paid' },
    ],
    onFilter: (value, record) => record.status === value,
  },
  {
    title: 'Action',
    key: 'action',
    width: 150,
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

const formState = ref({
  transactionName: '',
  clientName: '',
  clientEmail: '',
  clientMobile: '',
  clientTpin: '',
  clientAddress: '',
  status: 'Pending',
  quantity: 1,
  unitPrice: 0,
});

const fetchTransactions = async () => {
  try {
    loading.value = true;
    const params = {
      search: searchQuery.value,
    };
    
    if (dateRange.value && dateRange.value.length === 2) {
      params.start_date = dateRange.value[0].format('YYYY-MM-DD');
      params.end_date = dateRange.value[1].format('YYYY-MM-DD');
    }

    const response = await axios.get('/getTransactions', { params });
    dataSource.value = response.data.data.map((item, index) => ({
      ...item,
      key: item.id,
    }));
    filteredData.value = [...dataSource.value];
  } catch (error) {
    console.error('Error fetching transactions:', error);
    message.error('Failed to fetch transactions');
  } finally {
    loading.value = false;
  }
};

const handleSearch = debounce(fetchTransactions, 300);

const handleDateRangeChange = (dates) => {
  dateRange.value = dates;
  fetchTransactions();
};

const resetFilters = () => {
  searchQuery.value = '';
  dateRange.value = [];
  fetchTransactions();
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
    transactionId: '',
    transactionName: '',
    clientName: '',
    clientEmail: '',
    clientMobile: '',
    clientTpin: '',
    clientAddress: '',
    status: 'Pending',
    quantity: 1,
    unitPrice: 0,
  };
};

const isEditModalVisible = ref(false);
const editFormState = ref({});

const showEditModal = (record) => {
  selectedRecord.value = record;
  editFormState.value = { ...record };
  isEditModalVisible.value = true;
};

const handleEditSubmit = async () => {
  try {
    await axios.put(`/updateTransactions/${editFormState.value.id}`, editFormState.value);
    message.success('Transaction updated successfully');
    fetchTransactions();
    isEditModalVisible.value = false;
  } catch (error) {
    console.error('Error updating transaction:', error);
    message.error('Failed to update transaction');
  }
};

const updateTransaction = async () => {
  try {
    await axios.patch(`/transactions/${selectedRecord.value.id}`, {
      status: selectedRecord.value.status,
      initial_pay: selectedRecord.value.initial_pay
    });
    message.success('Transaction updated successfully');
    fetchTransactions();
  } catch (error) {
    console.error('Error updating transaction:', error);
    message.error('Failed to update transaction');
  }
};

const handleDelete = async (id) => {
  try {
    await axios.delete(`/transactions/${id}`);
    message.success('Transaction deleted successfully');
    fetchTransactions();
  } catch (error) {
    console.error('Error deleting transaction:', error);
    message.error('Failed to delete transaction');
  }
};

const handleCreateSubmit = async () => {
  try {
    await axios.post('/transactions', formState.value);
    message.success('Transaction created successfully');
    fetchTransactions();
    handleCreateCancel();
  } catch (error) {
    console.error('Error creating transaction:', error);
    if (error.response?.data?.errors) {
      Object.values(error.response.data.errors).forEach(err => {
        message.error(err[0]);
      });
    } else {
      message.error('Failed to create transaction');
    }
  }
};

onMounted(() => {
  fetchTransactions();
});

const exportTransactions = (status) => {
  const url = `/transactions/export/${status}`;
  window.open(url, '_blank');
};

const downloadPdf = (transactionId) => {
  const url = `/transactions/${transactionId}/pdf`;
  window.open(url, '_blank');
};

// Add these to your script section
const isReportsModalVisible = ref(false);
const activeReportTab = ref('summary');
const reportDateRange = ref([]);

const reportData = ref({
  totalRevenue: 0,
  totalPaid: 0,
  totalPending: 0,
  totalPartiallyPaid: 0,
  totalOutstanding: 0,
  monthlyComparison: [],
  detailedTransactions: []
});

const monthlyComparisonColumns = [
  {
    title: 'Month',
    dataIndex: 'month',
    key: 'month',
  },
  {
    title: 'Total Revenue',
    dataIndex: 'revenue',
    key: 'revenue',
  },
  {
    title: 'Total Paid',
    dataIndex: 'paid',
    key: 'paid',
  },
  {
    title: 'Outstanding',
    dataIndex: 'outstanding',
    key: 'outstanding',
  },
  {
    title: 'Profit/Loss',
    dataIndex: 'profitLoss',
    key: 'profitLoss',
    customRender: ({ text }) => {
      const isProfit = parseFloat(text) >= 0;
      return {
        children: `ZKW${Math.abs(parseFloat(text)).toFixed(2)} ${isProfit ? 'Profit' : 'Loss'}`,
        props: {
          style: { color: isProfit ? 'green' : 'red' }
        }
      };
    }
  }
];

const detailedReportColumns = [
  {
    title: 'Date',
    dataIndex: 'date',
    key: 'date',
  },
  {
    title: 'Transaction ID',
    dataIndex: 'transactionId',
    key: 'transactionId',
  },
  {
    title: 'Client',
    dataIndex: 'clientName',
    key: 'clientName',
  },
  {
    title: 'Amount',
    dataIndex: 'amount',
    key: 'amount',
  },
  {
    title: 'Paid',
    dataIndex: 'paid',
    key: 'paid',
  },
  {
    title: 'Balance',
    dataIndex: 'balance',
    key: 'balance',
  },
  {
    title: 'Status',
    dataIndex: 'status',
    key: 'status',
    customRender: ({ text }) => {
      let color = '';
      if (text === 'Paid') color = 'green';
      else if (text === 'Pending') color = 'red';
      else color = 'orange';
      return {
        children: text,
        props: { style: { color } }
      };
    }
  }
];

const showReportsModal = async () => {
  isReportsModalVisible.value = true;
  await fetchReportData();
};

const fetchReportData = async () => {
  try {
    loading.value = true;
    const params = {};
    
    if (reportDateRange.value && reportDateRange.value.length === 2) {
      params.start_date = reportDateRange.value[0].format('YYYY-MM-DD');
      params.end_date = reportDateRange.value[1].format('YYYY-MM-DD');
    }
    
    const response = await axios.get('/getTransactionReports', { params });
    reportData.value = response.data;
    
    // Calculate monthly comparison if not provided by backend
    if (!reportData.value.monthlyComparison) {
      reportData.value.monthlyComparison = calculateMonthlyComparison(dataSource.value);
    }
  } catch (error) {
    console.error('Error fetching reports:', error);
    message.error('Failed to load reports');
  } finally {
    loading.value = false;
  }
};

const calculateMonthlyComparison = (transactions) => {
  const monthlyData = {};
  
  transactions.forEach(transaction => {
    const date = new Date(transaction.created_at);
    const monthYear = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`;
    
    if (!monthlyData[monthYear]) {
      monthlyData[monthYear] = {
        revenue: 0,
        paid: 0,
        outstanding: 0
      };
    }
    
    monthlyData[monthYear].revenue += parseFloat(transaction.totalAmount);
    monthlyData[monthYear].paid += parseFloat(transaction.initial_pay);
    monthlyData[monthYear].outstanding += parseFloat(transaction.balance);
  });
  
  return Object.keys(monthlyData).map(month => ({
    month,
    revenue: `ZKW${monthlyData[month].revenue.toFixed(2)}`,
    paid: `ZKW${monthlyData[month].paid.toFixed(2)}`,
    outstanding: `ZKW${monthlyData[month].outstanding.toFixed(2)}`,
    profitLoss: (monthlyData[month].revenue - monthlyData[month].outstanding).toFixed(2)
  }));
};

const handleReportDateChange = (dates) => {
  reportDateRange.value = dates;
  fetchReportData();
};

const exportReport = async () => {
  try {
    const params = {};
    
    if (reportDateRange.value && reportDateRange.value.length === 2) {
      params.start_date = reportDateRange.value[0].format('YYYY-MM-DD');
      params.end_date = reportDateRange.value[1].format('YYYY-MM-DD');
    }
    
    const response = await axios.get('/exportReport', {
      params,
      responseType: 'blob' // Important for file downloads
    });
    
    // Create download link
    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', `report_${new Date().toISOString().slice(0,10)}.xlsx`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  } catch (error) {
    console.error('Export failed:', error);
    message.error('Failed to export report');
  }
};
// const exportReport = () => {
//   let url = '/exportReport';
//   const params = [];
  
//   if (reportDateRange.value && reportDateRange.value.length === 2) {
//     params.push(`start_date=${reportDateRange.value[0].format('YYYY-MM-DD')}`);
//     params.push(`end_date=${reportDateRange.value[1].format('YYYY-MM-DD')}`);
//   }
  
//   if (params.length) {
//     url += `?${params.join('&')}`;
//   }
  
//   window.open(url, '_blank');
// };

watch(searchQuery, handleSearch);
</script>

<template>
  <Head title="Transactions" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        Transactions Management
      </h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
        <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
            <a-breadcrumb>
                <a-breadcrumb-item href="/">Home</a-breadcrumb-item>
                <a-breadcrumb-item>Transactions </a-breadcrumb-item>
              </a-breadcrumb>
            <br />  
            <div class="flex flex-wrap gap-4 mb-4">

            <a-input-search
              v-model:value="searchQuery"
              placeholder="Search transactions..."
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

            <!-- Export Dropdown Button -->
            <a-dropdown>
                <template #overlay>
                <a-menu @click="({ key }) => exportTransactions(key)">
                    <a-menu-item key="all">
                    Export All
                    </a-menu-item>
                    <a-menu-item key="Paid">
                    Export Paid
                    </a-menu-item>
                    <a-menu-item key="Pending">
                    Export Pending
                    </a-menu-item>
                    <a-menu-item key="Partially_Paid">
                    Export Partially Paid
                    </a-menu-item>
                </a-menu>
                </template>
                <a-button type="primary">
                <template #icon><download-outlined /></template>
                Export
                </a-button>
            </a-dropdown>
            <!-- Add this to your template, perhaps near the export button -->
            <a-button @click="showReportsModal" type="primary" class="ml-2">
                <template #icon><BarChartOutlined /></template>
                Reports
            </a-button>
            
            <a-button @click="showCreateModal" type="primary" class="ml-auto">
              <template #icon><plus-outlined /></template>
              New Transaction
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
                  <a-button type="primary" @click="showEditModal(record)">Edit</a-button>
                  <Popconfirm
                    title="Are you sure you want to delete this transaction?"
                    ok-text="Yes"
                    cancel-text="No"
                    @confirm="handleDelete(record.id)"
                  >
                    <a-button type="primary" danger>Delete</a-button>
                  </Popconfirm>
                </div>
              </template>

              <template v-else-if="column.key === 'status'">
                <a-tag :color="record.status === 'Paid' ? 'green' : record.status === 'Pending' ? 'red' : 'orange'">
                  {{ record.status }}
                </a-tag>
              </template>

               <template v-else-if="column.key === 'totalAmount'">
                ZKW{{ record.totalAmount }}
            </template>
            <template v-else-if="column.key === 'initial_pay'">
                ZKW{{ record.initial_pay }}
            </template>
            <template v-else-if="column.key === 'balance'">
                <span :class="{'text-green-500': record.balance === 0, 'text-red-500': record.balance > 0}">
                ZKW{{ record.balance }}
                </span>
            </template>

              <template v-else-if="column.key === 'totalAmount'">
                K{{ record.totalAmount }}
              </template>

              <template v-else-if="column.key === 'unitPrice'">
                K{{ record.unitPrice }}
              </template>
            </template>
          </a-table>

          <!-- View Modal -->
          <a-modal
          v-model:visible="isModalVisible"
          :title="`Transaction #${selectedRecord?.transactionId}`"
          width="1200px"
          @cancel="isModalVisible = false"
        >
       
          <div class="flex justify-between items-center">
            <span></span>
            <a-button 
              type="primary" 
              @click="downloadPdf(selectedRecord.id)"
              class="ml-4"
            >
              <template #icon><download-outlined /></template>
              Download Receipt
            </a-button>
          </div>
          <br/>
          <div v-if="selectedRecord" class="space-y-4">
            <a-descriptions bordered :column="2">
              <a-descriptions-item label="Transaction ID">{{ selectedRecord.transactionId }}</a-descriptions-item>
              <a-descriptions-item label="Transaction Name">{{ selectedRecord.transactionName }}</a-descriptions-item>
              <a-descriptions-item label="Client Name">{{ selectedRecord.clientName }}</a-descriptions-item>
              <a-descriptions-item label="Client Email">{{ selectedRecord.clientEmail }}</a-descriptions-item>
              <a-descriptions-item label="Client Mobile">{{ selectedRecord.clientMobile }}</a-descriptions-item>
              <a-descriptions-item label="Client TPIN">{{ selectedRecord.clientTpin }}</a-descriptions-item>
              <a-descriptions-item label="Client Address" :span="2">{{ selectedRecord.clientAddress }}</a-descriptions-item>
              <a-descriptions-item label="Quantity">{{ selectedRecord.quantity }}</a-descriptions-item>
              <a-descriptions-item label="Unit Price">K{{ selectedRecord.unitPrice }}</a-descriptions-item>
              <a-descriptions-item label="Total Amount">K{{ selectedRecord.totalAmount }}</a-descriptions-item>
              <a-descriptions-item label="Initial Pay">
              <a-input-number 
                v-model:value="selectedRecord.initial_pay" 
                :min="0" 
                :max="selectedRecord.totalAmount"
                :step="0.01"
                style="width: 200px"
                @change="updateTransaction"
            />
            </a-descriptions-item>
            <a-descriptions-item label="Balance">K{{ selectedRecord.balance }}</a-descriptions-item>
            <a-descriptions-item label="Status">
            <a-select 
                v-model:value="selectedRecord.status" 
                style="width: 200px"
                @change="updateTransaction"
            >
                <a-select-option value="Paid">Paid</a-select-option>
                <a-select-option value="Pending">Pending</a-select-option>
                <a-select-option value="Partially_Paid">Partially Paid</a-select-option>
            </a-select>
            </a-descriptions-item>
              <a-descriptions-item label="Created At">{{ new Date(selectedRecord.created_at).toLocaleString() }}</a-descriptions-item>
            </a-descriptions>
          </div>
          <template #footer>
            <a-button @click="isModalVisible = false">Close</a-button>
          </template>
        </a-modal>

          <!-- Create Modal -->
          <a-modal
            v-model:visible="isCreateModalVisible"
            title="Create New Transaction"
            width="750px"
            :confirm-loading="loading"
            @ok="handleCreateSubmit"
            @cancel="handleCreateCancel"
          >
            <a-form layout="vertical">
              
              <a-form-item label="Transaction Name" required>
                <a-input v-model:value="formState.transactionName" 
                style="border: 1px solid #e9e9e9; border-radius: 8px; height: 32px; line-height: 32px;" />
              </a-form-item>
              
              <div class="grid grid-cols-2 gap-4">
                <a-form-item label="Client Name" required>
                  <a-input v-model:value="formState.clientName" 
                  style="border: 1px solid #e9e9e9; border-radius: 8px; height: 32px; line-height: 32px;" />
                </a-form-item>
                <a-form-item label="Client Email" required>
                  <a-input v-model:value="formState.clientEmail" 
                  style="border: 1px solid #e9e9e9; border-radius: 8px; height: 32px; line-height: 32px;" />
                </a-form-item>
              </div>
              
              <div class="grid grid-cols-2 gap-4">
                <a-form-item label="Client Mobile">
                  <a-input v-model:value="formState.clientMobile" 
                  style="border: 1px solid #e9e9e9; border-radius: 8px; height: 32px; line-height: 32px;" />
                </a-form-item>
                <a-form-item label="Client TPIN">
                  <a-input v-model:value="formState.clientTpin" 
                  style="border: 1px solid #e9e9e9; border-radius: 8px; height: 32px; line-height: 32px;" />
                </a-form-item>
              </div>
              
              <a-form-item label="Client Address">
                <a-textarea v-model:value="formState.clientAddress" :rows="2" />
              </a-form-item>
              
              <div class="grid grid-cols-3 gap-4">
                <a-form-item label="Quantity" required>
                  <a-input-number 
                    v-model:value="formState.quantity" 
                    :min="1" 
                    style="width: 100%"
                  />
                </a-form-item>
                <a-form-item label="Unit Price" required>
                  <a-input-number 
                    v-model:value="formState.unitPrice" 
                    :min="0" 
                    :step="0.01" 
                    style="width: 100%"
                  />
                </a-form-item>
                <a-form-item label="Status" required>
                  <a-select v-model:value="formState.status" style="width: 100%">
                    <a-select-option value="Paid">Paid</a-select-option>
                    <a-select-option value="Pending">Pending</a-select-option>
                    <a-select-option value="Partially Paid">Partially Paid</a-select-option>
                  </a-select>
                </a-form-item>
              </div>
            </a-form>
          </a-modal>

          <!-- Add Edit Modal -->
            <a-modal
            v-model:visible="isEditModalVisible"
            title="Edit Transaction"
            width="800px"
            :confirm-loading="loading"
            @ok="handleEditSubmit"
            @cancel="isEditModalVisible = false"
            >
            <a-form layout="vertical">
                <div class="grid grid-cols-2 gap-4">
                <a-form-item label="Transaction Name" required>
                    <a-input v-model:value="editFormState.transactionName" style="border: 1px solid #e9e9e9; border-radius: 8px; height: 32px; line-height: 32px;" />
                </a-form-item>
                <a-form-item label="Client Name" required>
                    <a-input v-model:value="editFormState.clientName" style="border: 1px solid #e9e9e9; border-radius: 8px; height: 32px; line-height: 32px;" />
                </a-form-item>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                <a-form-item label="Client Email" required>
                    <a-input v-model:value="editFormState.clientEmail" style="border: 1px solid #e9e9e9; border-radius: 8px; height: 32px; line-height: 32px;" />
                </a-form-item>
                <a-form-item label="Client Mobile">
                    <a-input v-model:value="editFormState.clientMobile" style="border: 1px solid #e9e9e9; border-radius: 8px; height: 32px; line-height: 32px;" />
                </a-form-item>
                </div>
                
                <div class="grid grid-cols-3 gap-4">
                <a-form-item label="Quantity" required>
                    <a-input-number 
                    v-model:value="editFormState.quantity" 
                    :min="1" 
                    style="width: 100%"
                    />
                </a-form-item>
                <a-form-item label="Unit Price" required>
                    <a-input-number 
                    v-model:value="editFormState.unitPrice" 
                    :min="0" 
                    :step="0.01" 
                    style="width: 100%"
                    />
                </a-form-item>
                <a-form-item label="Initial Pay">
                    <a-input-number 
                    v-model:value="editFormState.initial_pay" 
                    :min="0" 
                    :max="editFormState.totalAmount"
                    :step="0.01" 
                    style="width: 100%"
                    />
                </a-form-item>
                </div>
                
                <a-form-item label="Status" required>
                <a-select v-model:value="editFormState.status" style="width: 100%">
                    <a-select-option value="Paid">Paid</a-select-option>
                    <a-select-option value="Pending">Pending</a-select-option>
                    <a-select-option value="Partially_Paid">Partially Paid</a-select-option>
                </a-select>
                </a-form-item>
                
                <a-form-item label="Client Address">
                <a-textarea v-model:value="editFormState.clientAddress" :rows="2" />
                </a-form-item>
            </a-form>
            </a-modal>

            <!-- Add this modal to your template -->
            <a-modal
            v-model:visible="isReportsModalVisible"
            title="Financial Reports"
            width="1200px"
            @cancel="isReportsModalVisible = false"
            >
            <a-tabs v-model:activeKey="activeReportTab">
            <a-tab-pane key="summary" tab="Summary Report">
                <a-descriptions bordered :column="2">
                <a-descriptions-item label="Total Revenue">
                    ZKW{{ reportData.totalRevenue.toFixed(2) }}
                </a-descriptions-item>
                <a-descriptions-item label="Total Paid">
                    ZKW{{ reportData.totalPaid.toFixed(2) }}
                </a-descriptions-item>
                <a-descriptions-item label="Total Pending">
                    ZKW{{ reportData.totalPending.toFixed(2) }}
                </a-descriptions-item>
                <a-descriptions-item label="Total Partially Paid">
                    ZKW{{ reportData.totalPartiallyPaid.toFixed(2) }}
                </a-descriptions-item>
                <a-descriptions-item label="Total Outstanding Balances">
                    ZKW{{ reportData.totalOutstanding.toFixed(2) }}
                </a-descriptions-item>
                </a-descriptions>
                
                <div class="mt-6">
                <h3 class="text-lg font-semibold">Monthly Comparison</h3>
                <a-table 
                    :columns="monthlyComparisonColumns" 
                    :dataSource="reportData.monthlyComparison" 
                    bordered
                    :pagination="false"
                />
                </div>
            </a-tab-pane>
            
            <a-tab-pane key="detailed" tab="Detailed Report" force-render>
                <a-range-picker
                v-model:value="reportDateRange"
                @change="handleReportDateChange"
                picker="month"
                style="width: 300px; margin-bottom: 20px"
                />
                
                <a-table 
                :columns="detailedReportColumns" 
                :dataSource="reportData.detailedTransactions" 
                bordered
                />
            </a-tab-pane>
            </a-tabs>

            <template #footer>
            <a-button @click="exportReport" type="primary">
                <template #icon><download-outlined /></template>
                Export Report
            </a-button>
            <a-button @click="isReportsModalVisible = false">Close</a-button>
            </template>
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
    <Head title="Transactions" />

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
