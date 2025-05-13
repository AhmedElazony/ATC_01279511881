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
          {{ event.category?.name }}
        </span>
      </div>
    </div>

    <div class="p-6">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
          {{ event.name }}
        </h3>
        <!-- Tags -->
        <div class="flex flex-wrap gap-2 mb-4">
          <span
            v-for="tag in event.tags"
            :key="tag.id"
            class="bg-gray-200 text-gray-800 text-xs px-2 py-1 rounded-full dark:bg-gray-600 dark:text-gray-200"
          >
            {{ tag.name }}
          </span>
        </div>
      </div>

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

        <div class="flex space-x-2">
          <!-- Book now button (only shown for logged in users) -->
          <button
            v-if="isAuthenticated"
            @click="bookEvent"
            :disabled="isBooked || isBooking"
            :class="[
              'px-4 py-2 border border-transparent text-sm font-medium rounded-md transition-colors',
              isBooked
                ? 'bg-green-600 text-white cursor-not-allowed opacity-80'
                : isBooking
                ? 'bg-indigo-400 text-white cursor-wait'
                : 'bg-indigo-600 hover:bg-indigo-700 text-white',
            ]"
          >
            <span v-if="isBooking">Booking...</span>
            <span v-else-if="isBooked">Booked</span>
            <span v-else>Book Now</span>
          </button>

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
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useAuthStore } from "@/stores/auth";
import api from "@/services/api";
import { formatDate, formatPrice } from "@/utils/formatters";

const props = defineProps({
  event: {
    type: Object,
    required: true,
  },
});

const authStore = useAuthStore();
const isAuthenticated = computed(() => authStore.isLoggedIn());
const isBooked = ref(false);
const isBooking = ref(false);

// Check if user has booked this event
onMounted(async () => {
  if (isAuthenticated.value) {
    try {
      isBooked.value = props.event.booked_by_user || false;
      console.log(props.event);
    } catch (error) {
      console.error("Error checking booking status:", error);
      alert("Failed to check booking status. Please try again.");
    }
  }
});

// Book the event
const bookEvent = async () => {
  if (!isAuthenticated.value || isBooked.value || isBooking.value) return;

  try {
    isBooking.value = true;
    // You might need to adjust this endpoint based on your API
    await api.post(`/events/${props.event.id}/book`);
    isBooked.value = true;
  } catch (error) {
    console.error("Error booking event:", error);
    alert("Failed to book the event. Please try again.");
  } finally {
    isBooking.value = false;
  }
};
</script>

<style scoped>
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
