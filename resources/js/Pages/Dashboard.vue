<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { FileTextOutlined, SwapOutlined, FileDoneOutlined, IdcardOutlined, GiftOutlined, PhoneOutlined, UserOutlined, ReadOutlined, AppstoreOutlined, PictureOutlined, CalendarOutlined, SettingOutlined } from '@ant-design/icons-vue'
import { Head } from '@inertiajs/vue3';


// Props from Inertia
defineProps({
  auth: Object
});


const activeKey = ref('1');

// Function to check if user has a permission
const can = (permission, auth) => {
  return auth?.permissions?.includes(permission);
};

</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Dashboard
            </h2>
        </template>

        <div class="py-4">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900">

                        <div>
                            <a-tabs v-model:activeKey="activeKey">
                              <a-tab-pane key="1" tab="Quick Links">

                                <div class="flex flex-wrap gap-4">
                                                  
                                  <Card v-if="can('manage transactions', auth)"
                                  title="Transactions" 
                                  subTitle="Create, edit, update and delete of News" 
                                  routeName="transactions.index">
                                  <template #icon>
                                  <SwapOutlined style="font-size: 24px;" />
                                  </template>
                                  </Card>

                                  <Card v-if="can('manage receipts', auth)"
                                  title="Receipts" 
                                  subTitle="Create, edit, update and delete of Receipts" 
                                  routeName="receipts.quotations">
                                  <template #icon>
                                  <FileDoneOutlined style="font-size: 24px;" />
                                  </template>
                                  </Card>

                                  <Card  v-if="can('manage news management', auth)"
                                  title="News" 
                                  subTitle="Create, edit, update and delete of News" 
                                  routeName="news.index">
                                  <template #icon>
                                  <ReadOutlined style="font-size: 24px;" />
                                  </template>
                                  </Card>

                                  <Card v-if="can('manage promotions', auth)"
                                  title="Promotions" 
                                  subTitle="Create, edit, update and delete of News" 
                                  routeName="promotions.index">
                                  <template #icon>
                                  <GiftOutlined style="font-size: 24px;" />
                                  </template>
                                  </Card>

                                  <Card v-if="can('manage services', auth)"
                                  title="Services" 
                                  subTitle="Create, edit, update and delete of News" 
                                  routeName="services.index">
                                  <template #icon>
                                  <AppstoreOutlined style="font-size: 24px;" />
                                  </template>
                                  </Card>

                                  <Card v-if="can('manage gallery', auth)"
                                  title="Gallary" 
                                  subTitle="Create, edit, update and delete of News" 
                                  routeName="gallery.index">
                                  <template #icon>
                                  <FileTextOutlined style="font-size: 24px;" />
                                  </template>
                                  </Card>

                                  <Card v-if="can('manage contact', auth)"
                                  title="Contact" 
                                  subTitle="Create, edit, update and delete of News" 
                                  routeName="contacts.index">
                                  <template #icon>
                                  <PhoneOutlined style="font-size: 24px;" />
                                  </template>
                                  </Card>

                                  <Card v-if="can('manage access control', auth)"
                                  title="Access Control" 
                                  subTitle="Create, edit, update and delete of News" 
                                  routeName="admin.rolesAndPermission">
                                  <template #icon>
                                  <SettingOutlined style="font-size: 24px;" />
                                  </template>
                                  </Card>

                                  
                                  <Card title="My Account" 
                                  subTitle="Create, edit, update and delete of News" 
                                  routeName="profile.edit">
                                  <template #icon>
                                  <IdcardOutlined style="font-size: 24px;" />
                                  </template>
                                  </Card>

                                </div> 
                              </a-tab-pane>
                            </a-tabs>
                          </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
<script>
import Card from "@/Components/Auth/Card.vue";
import { ref } from "vue";

export default {
  components: {
    Card,
  },
  data() {
    return {
      value: "",
      activeKey: ref("1"),
      loading: false,
    };
  },
  mounted() {
  },

  methods: {

  },
};

</script>
<style>
.ant-breadcrumb-link {
  font-weight: bold;
  font-size: 20px !important;
  color: black;
}

.complete-your-profile-banner {
  border: 0 solid #fff;
  background: #fff;
  box-shadow: 0 11px 30px 0 rgba(0, 0, 0, 0.07);
  padding: 0px;
  margin-top: 30px;
  margin-bottom: 30px;
}

.complete-your-profile-banner h4 {
  padding-right: 10px;
  padding-bottom: 10px;
  color: #000;
  font-size: 28px !important;
  font-style: normal;
  font-weight: 700;
}

.complete-your-profile-banner .ant-btn span {
  font-size: 18px !important;
  font-weight: bold !important;
}

.external-system-status {
  height: auto;
  color: #fff;
  background-color: #15396c;
  padding: 20px;
  border-radius: 10px;
}

.external-system-status h1 {
  color: #fff;
  font-size: 22px !important;
}

.external-system-status p {
  color: #fff !important;
}

.status-card {
  background: #ffffff;
  border-radius: 10px;
  /* box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); */
  /* background-image: url("/assets/header-default-desktop.png"); */
  background-image: url("/assets/bg-grey.png");
  box-shadow: 0 11px 30px 0 rgba(0, 0, 0, 0.07);
  background-repeat: no-repeat;
  background-size: cover;
  padding: 20px;
  margin: 20px auto;
}

.status-header {
  margin-bottom: 15px;
}

.status-header h2 {
  color: #333 !important;
  font-size: 18px !important;
  font-weight: 700;
  margin-top: 5px;
  margin-bottom: 5px !important;
}

.status-body {
  font-size: 1.2em;
}

.status-label {
  font-weight: 600;
  color: #333333;
}

.status-value {
  color: #4caf50;
}
</style>
