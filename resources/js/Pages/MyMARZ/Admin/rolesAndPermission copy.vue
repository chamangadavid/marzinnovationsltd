<script setup>
import { ref, onMounted, h } from 'vue';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Tabs, Table, Button, Modal, Input, Select, Tag, message } from 'ant-design-vue';
import axios from 'axios';

// State management
const activeTab = ref('roles');

// Roles state
const roles = ref([]);
const roleForm = ref({
    name: '',
    permissions: []
});
const showRoleModal = ref(false);

// Permissions state
const permissions = ref([]);
const permissionForm = ref({
    name: '',
    guard_name: 'web'
});
const showPermissionModal = ref(false);

// Users state
const users = ref([]);
const userForm = ref({
    userId: null,
    userName: '',
    roles: []
});
const showUserRoleModal = ref(false);

// Color mapping for guard names
const guardColors = {
    web: 'blue',
    api: 'green',
    admin: 'purple',
    default: 'gray'
};

// Fetch data
const fetchData = async () => {
    try {
        const [rolesRes, permissionsRes, usersRes] = await Promise.all([
            axios.get('/roles'),
            axios.get('/permissions'),
            axios.get('/users')
        ]);
        
        roles.value = rolesRes.data.roles || [];
        permissions.value = permissionsRes.data.permissions || [];
        users.value = usersRes.data.users || [];
    } catch (error) {
        message.error('Failed to fetch data');
            console.error(error);
        }
};

onMounted(fetchData);

// Role actions
const handleAddRole = async () => {
    try {
        await axios.post('/roles', {
            name: roleForm.value.name,
            permissions: roleForm.value.permissions
        });
        message.success('Role created successfully');
        showRoleModal.value = false;
        roleForm.value = { name: '', permissions: [] };
        fetchData();
    } catch (error) {
        message.error(error.response?.data?.message || 'Failed to create role');
    }
};

// Permission actions
const handleAddPermission = async () => {
    try {
        await axios.post('/permissions', { 
            name: permissionForm.value.name,
            guard_name: permissionForm.value.guard_name
        });
        message.success('Permission created successfully');
        showPermissionModal.value = false;
        permissionForm.value = { name: '', guard_name: 'web' };
        fetchData();
    } catch (error) {
        message.error(error.response?.data?.message || 'Failed to create permission');
    }
};

// User role assignment
const openUserRoleModal = (user) => {
    userForm.value = {
        userId: user.id,
        userName: user.name,
        roles: user.roles.map(role => role.name)
    };
    showUserRoleModal.value = true;
};

const handleAssignRoles = async () => {
    try {
        await axios.post('/users/assign-role', {
            user_id: userForm.value.userId,
            roles: userForm.value.roles
        });
        message.success('Roles assigned successfully');
        showUserRoleModal.value = false;
        fetchData();
    } catch (error) {
        message.error(error.response?.data?.message || 'Failed to assign roles');
    }
};
</script>

<template>
    <Head title="Roles & Permissions" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Roles & Permissions
            </h2>
        </template>

        <div class="p-6">
            <a-tabs v-model:activeKey="activeTab" type="card">
                <!-- Roles Tab -->
                <a-tab-pane key="roles" tab="Roles">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium">Manage Roles</h3>
                        <Button type="primary" @click="showRoleModal = true">Create Role</Button>
                    </div>
                    
                    <a-table 
                        :dataSource="roles" 
                        :columns="[
                            { 
                                title: 'Role Name', 
                                dataIndex: 'name',
                                sorter: (a, b) => a.name.localeCompare(b.name),
                                width: '20%'
                            },
                            { 
                                title: 'Permissions', 
                                dataIndex: 'permissions',
                                customRender: ({ text }) => {
                                    return h('div', { class: 'flex flex-wrap gap-1' }, 
                                        text.map(p => h(Tag, { 
                                            color: guardColors[p.guard_name] || guardColors.default 
                                        }, p.name))
                                    );
                                }
                            }
                        ]" 
                        rowKey="id"
                        :pagination="{ pageSize: 10, showSizeChanger: true }"
                        bordered
                        size="middle"
                    />
                </a-tab-pane>

                <!-- Permissions Tab -->
                <a-tab-pane key="permissions" tab="Permissions">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium">Manage Permissions</h3>
                        <Button type="primary" @click="showPermissionModal = true">Create Permission</Button>
                    </div>
                    
                    <a-table 
                        :dataSource="permissions" 
                        :columns="[
                            { 
                                title: 'Permission Name', 
                                dataIndex: 'name',
                                sorter: (a, b) => a.name.localeCompare(b.name),
                                width: '30%'
                            },
                            { 
                                title: 'Guard', 
                                dataIndex: 'guard_name',
                                customRender: ({ text }) => h(Tag, { 
                                    color: guardColors[text] || guardColors.default 
                                }, text),
                                width: '20%'
                            },
                            { 
                                title: 'Created At', 
                                dataIndex: 'created_at',
                                sorter: (a, b) => new Date(a.created_at) - new Date(b.created_at),
                                width: '25%'
                            },
                           
                        ]" 
                        rowKey="id"
                        :pagination="{ pageSize: 10, showSizeChanger: true }"
                        bordered
                        size="middle"
                    />
                </a-tab-pane>

                <!-- Users Tab -->
                <a-tab-pane key="users" tab="Users">
                    <div class="mb-4">
                        <h3 class="text-lg font-medium">Manage User Roles</h3>
                    </div>
                    
                    <a-table 
                        :dataSource="users" 
                        :columns="[
                            { 
                                title: 'Name', 
                                dataIndex: 'name',
                                sorter: (a, b) => a.name.localeCompare(b.name),
                                width: '20%'
                            },
                            { 
                                title: 'Email', 
                                dataIndex: 'email',
                                sorter: (a, b) => a.email.localeCompare(b.email),
                                width: '25%'
                            },
                            { 
                                title: 'Roles', 
                                dataIndex: 'roles',
                                customRender: ({ text }) => h('div', { class: 'flex flex-wrap gap-1' }, 
                                    text.length > 0 
                                        ? text.map(r => h(Tag, { color: 'blue' }, r.name))
                                        : h(Tag, { color: 'gray' }, 'No roles')),
                                width: '30%'
                            },
                            {
                                title: 'Actions',
                                customRender: ({ record }) => {
                                    return h(Button, {
                                        type: 'primary',
                                        size: 'small',
                                        onClick: () => openUserRoleModal(record)
                                    }, 'Assign Roles');
                                },
                                width: '25%',
                                align: 'center'
                            }
                        ]" 
                        rowKey="id"
                        :pagination="{ pageSize: 10, showSizeChanger: true }"
                        bordered
                        size="middle"
                    />
                </a-tab-pane>
            </a-tabs>
        </div>

        <!-- Create Role Modal -->
        <a-modal 
            v-model:open="showRoleModal" 
            title="Create New Role"
            @ok="handleAddRole"
            ok-text="Create"
            cancel-text="Cancel"
            :maskClosable="false"
        >
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role Name</label>
                    <a-input 
                        v-model:value="roleForm.name" 
                        placeholder="Enter role name"
                    />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Permissions</label>
                    <a-select
                        v-model:value="roleForm.permissions"
                        mode="multiple"
                        placeholder="Select permissions"
                        style="width: 100%"
                        :options="permissions.map(p => ({ 
                            label: p.name, 
                            value: p.name 
                        }))"
                    />
                </div>
            </div>
        </a-modal>

        <!-- Create Permission Modal -->
        <a-modal 
            v-model:open="showPermissionModal" 
            title="Create New Permission"
            @ok="handleAddPermission"
            ok-text="Create"
            cancel-text="Cancel"
            :maskClosable="false"
        >
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Permission Name</label>
                    <a-input 
                        v-model:value="permissionForm.name" 
                        placeholder="Enter permission name"
                    />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Guard Name</label>
                    <a-select
                        v-model:value="permissionForm.guard_name"
                        style="width: 100%"
                        :options="[
                            { label: 'Web', value: 'web' },
                            { label: 'API', value: 'api' },
                            { label: 'Admin', value: 'admin' }
                        ]"
                    />
                </div>
            </div>
        </a-modal>

        <!-- Assign Roles Modal -->
        <a-modal 
            v-model:open="showUserRoleModal" 
            :title="`Assign Roles to ${userForm.userName}`"
            @ok="handleAssignRoles"
            ok-text="Assign"
            cancel-text="Cancel"
            :maskClosable="false"
        >
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Select Roles</label>
                <a-select
                    v-model:value="userForm.roles"
                    mode="multiple"
                    placeholder="Select roles"
                    style="width: 100%"
                    :options="roles.map(r => ({ 
                        label: r.name, 
                        value: r.name 
                    }))"
                />
            </div>
        </a-modal>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Custom table styling */
:deep(.ant-table) {
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

:deep(.ant-table-thead > tr > th) {
    background-color: #f8fafc;
    font-weight: 600;
    color: #334155;
}

:deep(.ant-table-tbody > tr:hover > td) {
    background-color: #f1f5f9 !important;
}

/* Tab styling */
:deep(.ant-tabs-card > .ant-tabs-nav .ant-tabs-tab) {
    background: #f8fafc;
    border-color: #e2e8f0;
    font-weight: 500;
}

:deep(.ant-tabs-card > .ant-tabs-nav .ant-tabs-tab-active) {
    background: #ffffff;
    border-bottom-color: #ffffff;
    color: #a515dd;
}

/* Modal styling */
:deep(.ant-modal-header) {
    border-bottom: 1px solid #e2e8f0;
}

:deep(.ant-modal-footer) {
    border-top: 1px solid #e2e8f0;
    padding: 16px 24px;
}
</style>