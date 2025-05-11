<template>
  <router-link
    :to="to"
    class="hover:text-indigo-600 dark:text-white dark:hover:text-indigo-600 inline-flex text-gray-500 items-center px-1 py-2 text-sm font-medium border-b-2 border-transparent transition-colors duration-200"
    active-class="border-indigo-600 text-indigo-600"
  >
    <slot>{{ name }}</slot>
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
