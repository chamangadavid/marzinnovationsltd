<script setup>
import { ref, onMounted, h } from 'vue';
import { 
    PlusOutlined, 
    EyeOutlined,
    EditOutlined,
    DeleteOutlined,
    DownloadOutlined
} from '@ant-design/icons-vue';
import { Modal, message } from 'ant-design-vue';
import axios from 'axios';
import dayjs from 'dayjs';
import { Tooltip } from 'ant-design-vue';

// Data
const invoices = ref([]);
const customers = ref([]);
const loading = ref(false);

// Modal visibility
const isCreateModalVisible = ref(false);
const isViewModalVisible = ref(false);
const currentInvoice = ref(null);

// Form state
const formState = ref({
    date: null,
    due_date: null,

    customer_id: null,
    terms: '',
    notes: '',
    items: [{ description: '', quantity: 1, unit_price: 0, total: 0 }],
    subtotal: 0,
    tax: 0,
    total: 0
});

// Table columns
const columns = [
    { 
        title: 'Invoice #', 
        dataIndex: 'invoice_number', 
        key: 'invoice_number',
        sorter: (a, b) => a.invoice_number.localeCompare(b.invoice_number)
    },
    { 
        title: 'Date', 
        dataIndex: 'date', 
        key: 'date',
        sorter: (a, b) => new Date(a.date) - new Date(b.date)
    },
    { 
        title: 'Customer', 
        dataIndex: ['customer', 'name'], 
        key: 'customer',
        sorter: (a, b) => a.customer?.name?.localeCompare(b.customer?.name || '')
    },
    { 
        title: 'Total', 
        dataIndex: 'total', 
        key: 'total',
        sorter: (a, b) => a.total - b.total,
        customRender: ({ text }) => `ZKW${text}`
    },
    {
  title: 'Actions',
  key: 'actions',
  width: 150,
  customRender: ({ record }) => [
    h(
      Tooltip,
      { title: 'View Invoice' },
      {
        default: () =>
          h(EyeOutlined, {
            onClick: () => viewInvoice(record),
            style: 'color: #1890ff; margin-right: 12px; cursor: pointer;',
          }),
      }
    ),
    h(
      Tooltip,
      { title: 'Edit Invoice' },
      {
        default: () =>
          h(EditOutlined, {
            onClick: () => editInvoice(record),
            style: 'color: #52c41a; margin-right: 12px; cursor: pointer;',
          }),
      }
    ),
    h(
      Tooltip,
      { title: 'Delete Invoice' },
      {
        default: () =>
          h(DeleteOutlined, {
            onClick: () => confirmDelete(record),
            style: 'color: #ff4d4f; cursor: pointer;',
          }),
      }
    ),
  ],
}
    // { 
    //     title: 'Actions', 
    //     key: 'actions',
    //     width: 150,
    //     customRender: ({ record }) => [
    //         h(EyeOutlined, {
    //             onClick: () => viewInvoice(record),
    //             style: 'color: #1890ff; margin-right: 12px; cursor: pointer;'
    //         }),
    //         h(EditOutlined, {
    //             onClick: () => editInvoice(record),
    //             style: 'color: #52c41a; margin-right: 12px; cursor: pointer;'
    //         }),
    //         h(DeleteOutlined, {
    //             onClick: () => confirmDelete(record),
    //             style: 'color: #ff4d4f; cursor: pointer;'
    //         }),
    //     ]
    // }
];

const viewItemColumns = [
    { title: '#', key: 'index', width: '5%', customRender: ({ index }) => index + 1 },
    { title: 'Description', dataIndex: 'description', key: 'description', width: '45%' },
    { title: 'Quantity', dataIndex: 'quantity', key: 'quantity', width: '15%' },
    { title: 'Unit Price', dataIndex: 'unit_price', key: 'unit_price', width: '15%', 
      customRender: ({ text }) => `ZKW${text}` },
    { title: 'Total', dataIndex: 'total', key: 'total', width: '20%',
      customRender: ({ text }) => `ZKW${text}` }
];

// Fetch data
const fetchData = async () => {
    try {
        loading.value = true;
        const [invoicesResponse, customersResponse] = await Promise.all([
            axios.get('/receipts/invoices'),
            axios.get('/customers')
        ]);
        invoices.value = invoicesResponse.data;
        customers.value = customersResponse.data;
    } catch (error) {
        message.error('Failed to fetch data');
    } finally {
        loading.value = false;
    }
};

// View invoice
const viewInvoice = async (invoice) => {
    try {
        loading.value = true;
        const response = await axios.get(`/receipts/invoices/${invoice.id}`);
        currentInvoice.value = response.data;
        isViewModalVisible.value = true;
    } catch (error) {
        message.error('Failed to load invoice details');
    } finally {
        loading.value = false;
    }
};

// Create new invoice
const showCreateModal = () => {
    currentInvoice.value = null;
    formState.value = {
        date: null,
        due_date: null,
        customer_id: null,
        terms: '',
        notes: '',
        items: [{ description: '', quantity: 1, unit_price: 0, total: 0 }],
        subtotal: 0,
        tax: 0,
        total: 0
    };
    isCreateModalVisible.value = true;
};

// Edit invoice
const editInvoice = async (invoice) => {
    try {
        loading.value = true;
        const response = await axios.get(`/receipts/invoices/${invoice.id}`);
        currentInvoice.value = response.data;

        formState.value = {
            date: currentInvoice.value.date ? dayjs(currentInvoice.value.date) : null,
            due_date: currentInvoice.value.due_date ? dayjs(currentInvoice.value.due_date) : null,
            customer_id: currentInvoice.value.customer_id,
            terms: currentInvoice.value.terms || '',
            notes: currentInvoice.value.notes || '',
            items: currentInvoice.value.items?.map(item => ({
                description: item.description,
                quantity: item.quantity,
                unit_price: item.unit_price,
                total: item.total
            })) || [{ description: '', quantity: 1, unit_price: 0, total: 0 }],
            subtotal: currentInvoice.value.subtotal,
            tax: currentInvoice.value.tax,
            total: currentInvoice.value.total
        };

        isCreateModalVisible.value = true;
    } catch (error) {
        message.error('Failed to load invoice for editing');
    } finally {
        loading.value = false;
    }
};

// Item management
const addItem = () => {
    formState.value.items.push({
        description: '',
        quantity: 1,
        unit_price: 0,
        total: 0
    });
};

const removeItem = (index) => {
    formState.value.items.splice(index, 1);
    calculateTotals();
};

const calculateItemTotal = (index) => {
    const item = formState.value.items[index];
    item.total = item.quantity * item.unit_price;
    calculateTotals();
};

const calculateTotals = () => {
    formState.value.subtotal = formState.value.items.reduce((sum, item) => sum + item.total, 0);
    formState.value.total = formState.value.subtotal + (formState.value.tax || 0);
};

// Submit form
const handleSubmit = async () => {
    try {
        calculateTotals();
        
        const payload = {
            ...formState.value,
            date: formState.value.date?.format('YYYY-MM-DD'),
            due_date: formState.value.due_date?.format('YYYY-MM-DD')
        };
        
        const url = currentInvoice.value 
            ? `/receipts/invoices/${currentInvoice.value.id}`
            : '/receipts/invoices';
            
        const method = currentInvoice.value ? 'put' : 'post';
        
        const response = await axios[method](url, payload);
        
        message.success(`Invoice ${currentInvoice.value ? 'updated' : 'created'} successfully`);
        isCreateModalVisible.value = false;
        fetchData();
    } catch (error) {
        message.error(`Failed to ${currentInvoice.value ? 'update' : 'create'} invoice`);
    }
};

// Delete invoice
const confirmDelete = (invoice) => {
    Modal.confirm({
        title: 'Confirm Delete',
        content: 'Are you sure you want to delete this invoice?',
        okText: 'Yes',
        okType: 'danger',
        cancelText: 'No',
        onOk: async () => {
            try {
                await axios.delete(`/receipts/invoices/${invoice.id}`);
                message.success('Invoice deleted successfully');
                fetchData();
            } catch (error) {
                message.error('Failed to delete invoice');
            }
        }
    });
};

// Generate PDF
const generatePDF = async (invoiceId) => {
    try {
        const response = await axios.get(`/receipts/invoices/${invoiceId}/invoicePdf`, {
            responseType: 'blob'
        });
        
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `invoice_${invoiceId}.pdf`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    } catch (error) {
        message.error('Failed to generate PDF');
    }
};

// Initialize
onMounted(() => {
    fetchData();
});
</script>

<template>
    <div class="mb-4 flex justify-between items-center">
        <h3 class="text-lg font-medium">Invoices</h3>
        <a-button type="primary" @click="showCreateModal">
            <template #icon><PlusOutlined /></template>
            Create Invoice
        </a-button>
    </div>
    
    <a-table 
        :columns="columns" 
        :dataSource="invoices"
        :loading="loading"
        rowKey="id"
        bordered
        size="middle"
    />

    <!-- Create/Edit Modal -->
    <a-modal 
        v-model:visible="isCreateModalVisible" 
        :title="currentInvoice ? 'Edit Invoice' : 'Create Invoice'"
        width="80%"
        :maskClosable="false"
        :destroyOnClose="true"
        @ok="handleSubmit"
    >
        <a-form layout="vertical" :model="formState">
            <a-row :gutter="16">
                <a-col :span="8">
                    <a-form-item label="Date" required>
                        <a-date-picker 
                            v-model:value="formState.date" 
                            style="width: 100%" 
                            format="YYYY-MM-DD"
                        />
                    </a-form-item>
                </a-col>
                <a-col :span="8">
                    <a-form-item label="Due Date" required>
                        <a-date-picker 
                            v-model:value="formState.due_date" 
                            style="width: 100%" 
                            format="YYYY-MM-DD"
                        />
                    </a-form-item>
                </a-col>
                <a-col :span="8">
                    <a-form-item label="Customer" required>
                        <a-select 
                            v-model:value="formState.customer_id"
                            placeholder="Select customer"
                            show-search
                            option-filter-prop="label"
                            :filter-option="(input, option) => 
                                option.label.toLowerCase().indexOf(input.toLowerCase()) >= 0"
                        >
                            <a-select-option 
                                v-for="customer in customers" 
                                :key="customer.id"
                                :value="customer.id"
                                :label="customer.name"
                            >
                                {{ customer.name }}
                            </a-select-option>
                        </a-select>
                    </a-form-item>
                </a-col>
            </a-row>

            <!-- Items Table -->
            <div class="mb-4">
                <h4 class="text-md font-medium mb-2">Items</h4>
                <a-table 
                    :columns="[
                        { title: 'Description', key: 'description' },
                        { title: 'Quantity', key: 'quantity', width: '15%' },
                        { title: 'Unit Price', key: 'unit_price', width: '20%' },
                        { title: 'Total', key: 'total', width: '20%' },
                        { title: 'Actions', key: 'actions', width: '10%' }
                    ]"
                    :dataSource="formState.items"
                    size="small"
                    bordered
                    :pagination="false"
                >
                    <template #bodyCell="{ column, record, index }">
                        <template v-if="column.key === 'description'">
                            <a-input v-model:value="record.description" />
                        </template>
                        <template v-else-if="column.key === 'quantity'">
                            <a-input-number 
                                v-model:value="record.quantity" 
                                :min="1" 
                                style="width: 100%"
                                @change="calculateItemTotal(index)"
                            />
                        </template>
                        <template v-else-if="column.key === 'unit_price'">
                            <a-input-number 
                                v-model:value="record.unit_price" 
                                :min="0" 
                                :precision="2"
                                style="width: 100%"
                                @change="calculateItemTotal(index)"
                            />
                        </template>
                        <template v-else-if="column.key === 'total'">
                            ZKW{{ record.total }}
                        </template>
                        <template v-else-if="column.key === 'actions'">
                            <a-button 
                                type="link" 
                                danger
                                @click="removeItem(index)"
                                v-if="formState.items.length > 1"
                            >
                                Remove
                            </a-button>
                        </template>
                    </template>
                </a-table>
                <a-button 
                    type="dashed" 
                    block
                    @click="addItem"
                    class="mt-2"
                >
                    Add Item
                </a-button>
            </div>

            <!-- Totals -->
            <a-row :gutter="16" class="mt-4">
                <a-col :span="12">
                    <a-form-item label="Notes">
                        <a-textarea v-model:value="formState.notes" rows="2" />
                    </a-form-item>
                    <a-form-item label="Terms & Conditions">
                        <a-textarea v-model:value="formState.terms" rows="3" />
                    </a-form-item>
                </a-col>
                <a-col :span="12">
                    <div class="text-right">
                        <p class="mb-2">
                            <span class="font-medium">Sub Total:</span> 
                            ZKW{{ formState.subtotal }}
                        </p>
                        <p class="mb-2">
                            <span class="font-medium">Tax:</span> 
                            <a-input-number 
                                v-model:value="formState.tax" 
                                :min="0" 
                                :precision="2"
                                style="width: 120px"
                                @change="calculateTotals"
                                addon-before="ZKW"
                            />
                        </p>
                        <p class="text-lg font-medium">
                            <span>Total:</span> 
                            ZKW{{ formState.total }}
                        </p>
                    </div>
                </a-col>
            </a-row>
        </a-form>
    </a-modal>

    <!-- View Modal -->
    <a-modal 
        v-model:visible="isViewModalVisible" 
        :title="currentInvoice ? `Invoice #${currentInvoice.invoice_number}` : 'Invoice'"
        width="80%"
        :footer="null"
        :destroyOnClose="true"
    >
        <div v-if="currentInvoice">
            <!-- Document Header -->
            <div class="flex justify-between mb-6">
                <div>
                    <p><strong>Date:</strong> {{ currentInvoice.date }}</p>
                    <p><strong>Due Date:</strong> {{ currentInvoice.due_date }}</p>
                </div>
                <div class="text-right">
                    <p><strong>Customer:</strong> {{ currentInvoice.customer?.name || 'N/A' }}</p>
                    <p v-if="currentInvoice.customer?.email">
                        <strong>Email:</strong> {{ currentInvoice.customer.email }}
                    </p>
                    <p v-if="currentInvoice.customer?.phone">
                        <strong>Phone:</strong> {{ currentInvoice.customer.phone }}
                    </p>
                </div>
            </div>

            <!-- Items Table -->
            <a-table 
                :columns="viewItemColumns"
                :dataSource="currentInvoice.items"
                size="small"
                bordered
                :pagination="false"
            />

            <!-- Totals -->
            <div class="text-right mt-4">
                <p><strong>Sub Total:</strong> ZKW{{ currentInvoice.subtotal }}</p>
                <p><strong>Tax:</strong> ZKW{{ currentInvoice.tax }}</p>
                <p class="text-lg font-medium">
                    <strong>Total:</strong> ZKW{{ currentInvoice.total }}
                </p>
            </div>

            <!-- Notes and Terms -->
            <div class="mt-6" v-if="currentInvoice.notes">
                <p><strong>Notes:</strong></p>
                <p class="whitespace-pre-line">{{ currentInvoice.notes }}</p>
            </div>

            <div class="mt-4" v-if="currentInvoice.terms">
                <p><strong>Terms & Conditions:</strong></p>
                <p class="whitespace-pre-line">{{ currentInvoice.terms }}</p>
            </div>

            <div class="mt-6 text-center">
                <a-button 
                    type="primary" 
                    @click="generatePDF(currentInvoice.id)"
                    icon="download"
                >
                    Download PDF
                </a-button>
            </div>
        </div>
    </a-modal>
</template>