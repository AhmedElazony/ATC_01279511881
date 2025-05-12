<template>
  <div
    :key="event.id"
    class="bg-white dark:bg-gray-700 rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow"
  >
    <div class="aspect-video bg-gray-200 dark:bg-gray-600 relative">
      <img
        v-if="event.image_url"
        :src="event.image_url"
        :alt="event.name"
        class="w-full h-full object-cover"
      />
      <div
        v-else
        class="w-full h-full flex items-center justify-center text-gray-400 dark:text-gray-300"
      >
        <svg
          class="h-12 w-12"
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
          />
        </svg>
      </div>

      <!-- Category badge -->
      <div class="absolute top-4 right-4">
        <span
          class="bg-indigo-100 text-indigo-800 text-xs px-3 py-1 rounded-full font-medium dark:bg-indigo-900 dark:text-indigo-200"
        >
          {{ event.category.name }}
        </span>
      </div>
    </div>

    <div class="p-6">
      <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
        {{ event.name }}
      </h3>

      <!-- Date and time -->
      <div class="flex items-center mb-2 text-gray-600 dark:text-gray-300">
        <svg
          class="h-5 w-5 mr-2"
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
          />
        </svg>
        <span>{{ formatDate(event.date) }}</span>
      </div>

      <!-- Location -->
      <div class="flex items-center mb-3 text-gray-600 dark:text-gray-300">
        <svg
          class="h-5 w-5 mr-2"
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
          />
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
          />
        </svg>
        <span>{{ event.venue || "Virtual Event" }}</span>
      </div>
      <!-- Description -->
      <p class="text-gray-600 dark:text-gray-300 mb-4 line-clamp-3">
        {{ event.description }}
      </p>

      <div class="flex justify-between items-center">
        <!-- Price -->
        <div class="text-indigo-600 font-bold dark:text-indigo-400">
          {{ event.price > 0 ? formatPrice(event.price) : "Free" }}
        </div>

        <!-- View details button -->
        <router-link
          :to="`/events/${event.id}`"
          class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
        >
          View Details
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";
defineProps({
  event: {
    type: Object,
    required: true,
  },
});
import { formatDate, formatPrice } from "@/utils/formatters";
</script>
