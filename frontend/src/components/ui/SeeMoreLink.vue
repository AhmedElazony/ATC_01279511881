<template>
  <div class="mt-12 text-center">
    <router-link
      :to="to"
      class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-medium"
    >
      <slot></slot>
      <svg
        class="ml-2 w-4 h-4"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
        xmlns="http://www.w3.org/2000/svg"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M9 5l7 7-7 7"
        ></path>
      </svg>
    </router-link>
  </div>
</template>

<script setup>
defineProps({
  to: {
    type: String,
    required: true,
  },
});
</script>
