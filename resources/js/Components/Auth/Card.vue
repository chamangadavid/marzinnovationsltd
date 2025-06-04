<script setup>
import { Link } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
  title: {
    type: String,
    required: true,
  },
  subTitle: {
    type: String,
    required: true,
  },
  routeName: {
    type: String,
    required: false,
  },
  externalLink: {
    type: String,
    required: false,
  },
  routeVariable: {
    type: [String, Number],
    required: false,
  },
  isOnlineSystem: {
    type: Boolean,
    required: false,
    default: false,
  },
});

const dynamicRoute = computed(() => {
  if (props.routeName && props.routeVariable) {
    return route(props.routeName, { id: props.routeVariable });
  } else if (props.routeName) {
    return route(props.routeName);
  } else {
    return props.externalLink;
  }
});
</script>

<template>
  <div class="w-full sm:w-1/2 md:w-1/3 lg:w-1/6 px-2 mb-4">
    <Link v-if="routeName" :href="dynamicRoute">
      <div class="certificate-card">
        <div class="icon-slot">
          <slot name="icon" />
        </div>
        <div class="content">
          <a-badge v-if="props.isOnlineSystem" status="success" />
          <h2 class="certificate-title">{{ title }}</h2>
          <p class="certificate-number">{{ subTitle }}</p>
        </div>
      </div>
    </Link>

    <a
      v-else
      :href="externalLink"
      target="_blank"
      rel="noopener noreferrer"
    >
      <div class="certificate-card">
        <div class="icon-slot">
          <slot name="icon" />
        </div>
        <div class="content">
          <a-badge v-if="props.isOnlineSystem" status="success" />
          <h2 class="certificate-title">{{ title }}</h2>
          <p class="certificate-number">{{ subTitle }}</p>
        </div>
      </div>
    </a>
  </div>
</template>

<style scoped>
.certificate-card {
  background-image: url("/assets/bg-grey.png");
  background-repeat: no-repeat;
  background-color: #fff;
  background-size: cover;
  width: auto; /* full width on small screens */
  max-width: auto; /* limit max width */
  min-height: 230px;
  border-radius: 20px;
  padding: 20px;
  cursor: pointer;
  box-shadow: 0 11px 30px 0 rgba(0, 0, 0, 0.07);
  margin: 0 auto; /* center the card */
}

@media (min-width: 640px) {
  .certificate-card {
    width: 100%; /* full width on small screens */
  }
}


/*
.certificate-card {
  background-image: url("/assets/bg-grey.png");
  background-repeat: no-repeat;
  background-color: #fff;
  background-size: cover;
  width: 200px !important;
  border: 0px solid #e0e0e0;
  min-height: 230px;
  border-radius: 20px;
  padding: 20px;
  cursor: pointer;
  box-shadow: 0 11px 30px 0 rgba(0, 0, 0, 0.07);
}
  */

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.logo {
  height: 30px;
}

.status {
  font-size: 12px;
  color: #fff;
  padding: 5px 10px;
  border-radius: 4px;
}

.status.active {
  background-color: #4caf50;
}

.content {
  text-align: left;
}

.certificate-number,
.certificate-id,
.issue-date {
  margin: 5px 0;
  font-size: 14px;
  color: #666 !important;
  text-align: left;
}

.certificate-title {
  color: #333 !important;
  font-size: 18px !important;
  font-weight: 700;
  margin-top: 5px;
  margin-bottom: 5px !important;
}

.certificate-title sup {
  font-size: 12px;
}

.view-certificate,
.get-hard-copy {
  display: block;
  font-size: 14px;
  color: rgba(0, 0, 0);
  text-decoration: none;
  margin: 10px 0;
}

.view-certificate:hover,
.get-hard-copy:hover {
  text-decoration: underline;
}
</style>
