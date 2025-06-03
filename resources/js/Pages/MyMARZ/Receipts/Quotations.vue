<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import dayjs from 'dayjs'; 
import { ref, onMounted, h } from 'vue';
import { PlusOutlined, EyeOutlined, EditOutlined, DeleteOutlined, DownloadOutlined, UserAddOutlined } from '@ant-design/icons-vue';
import Invoice from '@/Pages/MyMARZ/Receipts/Invoice.vue';
import Receipt from '@/Pages/MyMARZ/Receipts/Receipt.vue';
import PurchaseOrder from '@/Pages/MyMARZ/Receipts/PurchaseOrder.vue';
import Supplier from '@/Pages/MyMARZ/Receipts/Supplier.vue';
import DeliveryNote from '@/Pages/MyMARZ/Receipts/DeliveryNote.vue';
import { Tooltip } from 'ant-design-vue';
import { Modal, message } from 'ant-design-vue';
import axios from 'axios';

// Tab management
const activeKey = ref('quotation');
const tabItems = [
    { key: 'quotation', label: 'Quotation' },
    { key: 'customer', label: 'Customers' },
    { key: 'supplier', label: 'Suppliers' }, // Added Supplier tab
    { key: 'invoice', label: 'Invoice' },
    { key: 'receipt', label: 'Receipt' },
    { key: 'purchase-order', label: 'Purchase Order' },
    { key: 'delivery-note', label: 'Delivery Note' } // Added Delivery Note tab
];

// Data
const quotations = ref([]);
const customers = ref([]);
const loading = ref(false);
const customerLoading = ref(false);

// Modal visibility
const isCreateModalVisible = ref(false);
const isViewModalVisible = ref(false);
const isCustomerModalVisible = ref(false);
const currentDocument = ref(null);

// Form states
const formState = ref({
    date: null,
    expiry_date: null,
    customer_id: null,
    terms: '',
    notes: '',
    items: [{ description: '', quantity: 1, unit_price: 0, total: 0 }],
    subtotal: 0,
    tax: 0,
    total: 0
});

const customerFormState = ref({
    name: '',
    email: '',
    phone: '',
    address: '',
    tax_id: ''
});

// Table columns
const quotationColumns = [
    { 
        title: 'Quotation #', 
        dataIndex: 'quotation_number', 
        key: 'quotation_number',
        sorter: (a, b) => a.quotation_number.localeCompare(b.quotation_number)
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
        sorter: (a, b) => a.customer.name.localeCompare(b.customer.name)
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
      { title: 'View Document' },
      {
        default: () =>
          h(EyeOutlined, {
            onClick: () => viewDocument(record),
            style: 'color: #1890ff; margin-right: 12px; cursor: pointer;',
          }),
      }
    ),
    h(
      Tooltip,
      { title: 'Edit Document' },
      {
        default: () =>
          h(EditOutlined, {
            onClick: () => editDocument(record),
            style: 'color: #52c41a; margin-right: 12px; cursor: pointer;',
          }),
      }
    ),
    h(
      Tooltip,
      { title: 'Delete Document' },
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

];

const customerColumns = [
    { 
        title: 'Name', 
        dataIndex: 'name', 
        key: 'name',
        sorter: (a, b) => a.name.localeCompare(b.name)
    },
    { 
        title: 'Email', 
        dataIndex: 'email', 
        key: 'email',
        sorter: (a, b) => (a.email || '').localeCompare(b.email || '')
    },
    { 
        title: 'Phone', 
        dataIndex: 'phone', 
        key: 'phone' 
    },
    { 
        title: 'Tax ID', 
        dataIndex: 'tax_id', 
        key: 'tax_id' 
    },
    {
  title: 'Actions',
  key: 'actions',
  width: 120,
  customRender: ({ record }) => [
    h(
      Tooltip,
      { title: 'Edit Customer' },
      {
        default: () =>
          h(EditOutlined, {
            onClick: () => editCustomer(record),
            style: 'color: #52c41a; margin-right: 12px; cursor: pointer;',
          }),
      }
    ),
    h(
      Tooltip,
      { title: 'Delete Customer' },
      {
        default: () =>
          h(DeleteOutlined, {
            onClick: () => confirmCustomerDelete(record),
            style: 'color: #ff4d4f; cursor: pointer;',
          }),
      }
    ),
  ],
}

];

const viewItemColumns = [
    { title: '#', key: 'index', width: '5%', customRender: ({ index }) => index + 1 },
    { title: 'Description', dataIndex: 'description', key: 'description', width: '45%' },
    { title: 'Quantity', dataIndex: 'quantity', key: 'quantity', width: '15%' },
    { title: 'Unit Price', dataIndex: 'unit_price', key: 'unit_price', width: '15%', 
      customRender: ({ text }) => `K${text}` },
    { title: 'Total', dataIndex: 'total', key: 'total', width: '20%',
      customRender: ({ text }) => `K${text}` }
];

const fetchData = async () => {
    try {
        loading.value = true;
        const [quotationsResponse, customersResponse] = await Promise.all([
            axios.get('/receipts/quotations'), // Updated endpoint
            axios.get('/customers')
        ]);
        quotations.value = quotationsResponse.data;
        customers.value = customersResponse.data;
    } catch (error) {
        message.error('Failed to fetch data');
    } finally {
        loading.value = false;
    }
};


// Customer functions
const showCustomerModal = () => {
    customerFormState.value = {
        name: '',
        email: '',
        phone: '',
        address: '',
        tax_id: ''
    };
    isCustomerModalVisible.value = true;
};

const editCustomer = (customer) => {
    customerFormState.value = { ...customer };
    isCustomerModalVisible.value = true;
};

const handleCustomerSubmit = async () => {
    try {
        customerLoading.value = true;
        const method = customerFormState.value.id ? 'put' : 'post';
        const url = customerFormState.value.id 
            ? `/customers/${customerFormState.value.id}`
            : '/customers';

        await axios[method](url, customerFormState.value);
        message.success(`Customer ${customerFormState.value.id ? 'updated' : 'added'} successfully`);
        isCustomerModalVisible.value = false;
        fetchData();
    } catch (error) {
        message.error(`Failed to ${customerFormState.value.id ? 'update' : 'add'} customer`);
    } finally {
        customerLoading.value = false;
    }
};

const confirmCustomerDelete = (customer) => {
    Modal.confirm({
        title: 'Confirm Delete',
        content: 'Are you sure you want to delete this customer?',
        okText: 'Yes',
        okType: 'danger',
        cancelText: 'No',
        onOk: async () => {
            try {
                await axios.delete(`/customers/${customer.id}`);
                message.success('Customer deleted successfully');
                fetchData();
            } catch (error) {
                message.error('Failed to delete customer');
            }
        }
    });
};

const viewDocument = async (document) => {
    try {
        loading.value = true;
        // Fetch the full document with relationships
        const response = await axios.get(`/receipts/quotations/${document.id}`);
        currentDocument.value = response.data;
        isViewModalVisible.value = true;
    } catch (error) {
        message.error('Failed to load quotation details');
    } finally {
        loading.value = false;
    }
};


const showCreateModal = () => {
    currentDocument.value = null;
    formState.value = {
        date: null,
        expiry_date: null,
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

const editDocument = async (document) => {
    try {
        loading.value = true;

        // Fetch the full document with items
        const response = await axios.get(`/receipts/quotations/${document.id}`);
        currentDocument.value = response.data;

        // Build form state
        formState.value = {
            date: currentDocument.value.date ? dayjs(currentDocument.value.date) : null,
            expiry_date: currentDocument.value.expiry_date ? dayjs(currentDocument.value.expiry_date) : null,
            customer_id: currentDocument.value.customer_id,
            terms: currentDocument.value.terms || '',
            notes: currentDocument.value.notes || '',
            items: currentDocument.value.items?.map(item => ({
                description: item.description,
                quantity: item.quantity,
                unit_price: item.unit_price,
                total: item.total
            })) || [{ description: '', quantity: 1, unit_price: 0, total: 0 }],
            subtotal: currentDocument.value.subtotal,
            tax: currentDocument.value.tax,
            total: currentDocument.value.total
        };

        isCreateModalVisible.value = true;

    } catch (error) {
        console.error(error);
        message.error('Failed to load quotation for editing');
    } finally {
        loading.value = false;
    }
};

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


const handleSubmit = async () => {
    try {
        calculateTotals();
        
        // Format dates to ISO string without timezone
        const payload = {
            ...formState.value,
            date: formState.value.date?.format('YYYY-MM-DD'),
            expiry_date: formState.value.expiry_date?.format('YYYY-MM-DD')
        };
        
        const url = currentDocument.value 
            ? `/receipts/quotations/${currentDocument.value.id}`
            : '/receipts/quotations';
            
        const method = currentDocument.value ? 'put' : 'post';
        
        const response = await axios[method](url, payload);
        
        message.success(`Quotation ${currentDocument.value ? 'updated' : 'created'} successfully`);
        isCreateModalVisible.value = false;
        fetchData();
    } catch (error) {
        message.error(`Failed to ${currentDocument.value ? 'update' : 'create'} quotation`);
    }
};

const confirmDelete = (document) => {
    Modal.confirm({
        title: 'Confirm Delete',
        content: 'Are you sure you want to delete this quotation?',
        okText: 'Yes',
        okType: 'danger',
        cancelText: 'No',
        onOk: async () => {
            try {
                await axios.delete(`/receipts/quotations/${document.id}`);
                message.success('Quotation deleted successfully');
                fetchData();
            } catch (error) {
                message.error('Failed to delete quotation');
            }
        }
    });
};

const generatePDF = async (documentId) => {
    try {
        const response = await axios.get(`/receipts/quotations/${documentId}/pdf`, {
            responseType: 'blob'
        });
        
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `quotation_${documentId}.pdf`);
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
    <Head title="Document Management" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Document Management
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white p-4 shadow sm:rounded-lg">
                    <a-tabs v-model:activeKey="activeKey" @change="fetchData">
                        <!-- Quotation Tab -->
                        <a-tab-pane key="quotation" tab="Quotation">
                            <div class="mb-4 flex justify-between items-center">
                                <h3 class="text-lg font-medium">Quotations</h3>
                                <a-button type="primary" @click="showCreateModal">
                                    <template #icon><PlusOutlined /></template>
                                    Create Quotation
                                </a-button>
                            </div>
                            
                            <a-table 
                            :columns="quotationColumns" 
                            :dataSource="quotations"
                            :loading="loading"
                            rowKey="id"
                            bordered
                            size="middle"
                            />
                        </a-tab-pane>

                        <!-- Customer Tab -->
                        <a-tab-pane key="customer" tab="Customers">
                            <div class="mb-4 flex justify-between items-center">
                                <h3 class="text-lg font-medium">Customer Management</h3>
                                <a-button type="primary" @click="showCustomerModal">
                                    <template #icon><UserAddOutlined /></template>
                                    Add Customer
                                </a-button>
                            </div>
                            
                            <a-table 
                            :columns="customerColumns" 
                            :dataSource="customers"
                            :loading="loading"
                            rowKey="id"
                            bordered
                            size="middle"
                            />
                        </a-tab-pane>

                         <!-- Supplier Tab -->
                        <a-tab-pane key="supplier" tab="Suppliers">
                            <Supplier />
                        </a-tab-pane>

                         <!-- Invoice Tab -->
                        <a-tab-pane key="invoice" tab="Invoice">
                            <Invoice />
                        </a-tab-pane>

                         <!-- Receipt Tab -->
                         <a-tab-pane key="receipt" tab="Receipt">
                            <Receipt />
                        </a-tab-pane>

                         <!-- Purchase Order Tab -->
                         <a-tab-pane key="purchase-order" tab="Purchase Order">
                            <PurchaseOrder />
                        </a-tab-pane>

                        <!-- Delivery Note Tab -->
                        <a-tab-pane key="delivery-note" tab="Delivery Note">
                            <DeliveryNote />
                        </a-tab-pane>

                        <!-- Other tabs -->
                        <a-tab-pane 
                            v-for="tab in tabItems.filter(t => !['quotation', 'customer', 'supplier', 'invoice', 'receipt', 'purchase-order', 'delivery-note'].includes(t.key))" 
                            :key="tab.key" 
                            :tab="tab.label"
                        >
                            <div class="text-center py-8">
                                <p class="text-gray-500">{{ tab.label }} functionality coming soon</p>
                            </div>
                        </a-tab-pane>
                    </a-tabs>
                </div>
            </div>
        </div>

        <!-- Customer Modal -->
        <a-modal 
            v-model:visible="isCustomerModalVisible" 
            :title="customerFormState.id ? 'Edit Customer' : 'Add Customer'"
            :confirm-loading="customerLoading"
            :maskClosable="false"
            @ok="handleCustomerSubmit"
        >
            <a-form layout="vertical">
                <a-form-item label="Name" required>
                    <a-input v-model:value="customerFormState.name" />
                </a-form-item>
                <a-form-item label="Email">
                    <a-input v-model:value="customerFormState.email" type="email" />
                </a-form-item>
                <a-form-item label="Phone">
                    <a-input v-model:value="customerFormState.phone" />
                </a-form-item>
                <a-form-item label="Address">
                    <a-textarea v-model:value="customerFormState.address" rows="2" />
                </a-form-item>
                <a-form-item label="Tax ID">
                    <a-input v-model:value="customerFormState.tax_id" />
                </a-form-item>
            </a-form>
        </a-modal>

        <!-- Quotation Create/Edit Modal -->
        <a-modal 
            v-model:visible="isCreateModalVisible" 
            :title="currentDocument ? 'Edit Quotation' : 'Create Quotation'"
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
                        <a-form-item label="Expiry Date" required>
                            <a-date-picker 
                                v-model:value="formState.expiry_date" 
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
                                <a-input 
                                    v-model:value="record.description" 
                                    placeholder="Item description"
                                    style="border: 1px solid #e9e9e9; border-radius: 8px; height: 32px; line-height: 32px;"
                                />
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
                                K{{ record.total }}
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
                                K{{ formState.subtotal }}
                            </p>
                            <p class="mb-2">
                                <span class="font-medium">Tax:</span> 
                                <a-input-number 
                                    v-model:value="formState.tax" 
                                    :min="0" 
                                    :precision="2"
                                    style="width: 120px"
                                    @change="calculateTotals"
                                    addon-before="K"
                                />
                            </p>
                            <p class="text-lg font-medium">
                                <span>Total:</span> 
                                K{{ formState.total }}
                            </p>
                        </div>
                    </a-col>
                </a-row>
            </a-form>
        </a-modal>

        <!-- Quotation View Modal -->
        <a-modal 
            v-model:visible="isViewModalVisible" 
            :title="currentDocument ? `Quotation #${currentDocument.quotation_number}` : 'Quotation'"
            width="80%"
            :footer="null"
            :destroyOnClose="true"
        >
        <hr><br/>
            <div v-if="currentDocument">
                <!-- Document Header -->
                <div class="flex justify-between mb-6">
                    <div>
                        <p><strong>Date:</strong> {{ currentDocument.date }}</p>
                        <p><strong>Expiry Date:</strong> {{ currentDocument.expiry_date }}</p>
                    </div>
                  

                    <!-- In your view modal template -->
                    <div class="text-right">
                        <p><strong>Customer:</strong> {{ currentDocument.customer?.name || 'N/A' }}</p>
                        <p v-if="currentDocument.customer?.email">
                            <strong>Email:</strong> {{ currentDocument.customer.email }}
                        </p>
                        <p v-if="currentDocument.customer?.phone">
                            <strong>Phone:</strong> {{ currentDocument.customer.phone }}
                        </p>
                    </div>

                </div>

                <!-- Items Table -->
                <a-table 
                    :columns="viewItemColumns"
                    :dataSource="currentDocument.items"
                    size="small"
                    bordered
                    :pagination="false"
                />

                <!-- Totals -->
                <div class="text-right mt-4">
                    <p><strong>Sub Total:</strong> K{{ currentDocument.subtotal }}</p>
                    <p><strong>Tax:</strong> K{{ currentDocument.tax }}</p>
                    <p class="text-lg font-medium">
                        <strong>Total:</strong> K{{ currentDocument.total }}
                    </p>
                </div>

                <!-- Notes and Terms -->
                <div class="mt-6" v-if="currentDocument.notes">
                    <p><strong>Notes:</strong></p>
                    <p class="whitespace-pre-line">{{ currentDocument.notes }}</p>
                </div>

                <div class="mt-4" v-if="currentDocument.terms">
                    <p><strong>Terms & Conditions:</strong></p>
                    <p class="whitespace-pre-line">{{ currentDocument.terms }}</p>
                </div>

                <div class="mt-6 text-center">
                    <a-button 
                        type="primary" 
                        @click="generatePDF(currentDocument.id)"
                        icon="download"
                    >
                        Download PDF
                    </a-button>
                </div>
            </div>
        </a-modal>
    </AuthenticatedLayout>
</template>