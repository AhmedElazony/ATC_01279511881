<template>
  <router-link
    :to="to"
    class="bg-white dark:bg-gray-500 dark:text-white dark:hover:bg-gray-400 text-indigo-700 hover:bg-indigo-50 px-8 py-4 rounded-md font-bold text-lg inline-block transition-colors"
  >
    <slot></slot>
  </router-link>
</template>

<script setup>
defineProps({
  to: {
    type: String,
    required: true,
  },
});
</script>
