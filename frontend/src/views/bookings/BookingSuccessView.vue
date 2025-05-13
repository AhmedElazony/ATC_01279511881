<template>
  <div
    class="min-h-screen bg-gray-50 dark:bg-gray-900 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8"
  >
    <div
      class="max-w-md w-full bg-white dark:bg-gray-800 shadow-xl rounded-lg p-8"
    >
      <!-- Success icon -->
      <div class="text-center">
        <div
          class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-green-100 dark:bg-green-900 mb-6"
        >
          <svg
            class="h-12 w-12 text-green-600 dark:text-green-300"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M5 13l4 4L19 7"
            />
          </svg>
        </div>

        <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-4">
          Congratulations!
        </h2>

        <p class="text-lg text-gray-600 dark:text-gray-300 mb-8">
          You have successfully booked your spot for this event.
        </p>
      </div>

      <!-- Event details -->
      <div
        class="border-t border-b border-gray-200 dark:border-gray-700 py-4 mb-6"
      >
        <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-3">
          {{ eventDetails.eventName || "Event" }}
        </h3>

        <div class="space-y-2 text-gray-600 dark:text-gray-300">
          <div class="flex items-center">
            <svg
              class="h-5 w-5 mr-2 text-gray-400 dark:text-gray-500"
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
            <span>{{ formatDate(eventDetails.eventDate) }}</span>
          </div>

          <div class="flex items-center">
            <svg
              class="h-5 w-5 mr-2 text-gray-400 dark:text-gray-500"
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
            <span>{{ eventDetails.eventVenue }}</span>
          </div>
        </div>
      </div>

      <!-- Buttons -->
      <div class="flex flex-col sm:flex-row gap-4">
        <router-link
          :to="`/events/${eventDetails.eventId}`"
          class="flex-1 inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-indigo-900 dark:text-indigo-200 dark:hover:bg-indigo-800"
        >
          Event Details
        </router-link>

        <router-link
          to="/bookings"
          class="flex-1 inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          My Bookings
        </router-link>
      </div>

      <div class="mt-6 text-center">
        <router-link
          to="/events"
          class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500"
        >
          Browse other events
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { formatDate } from "@/utils/formatters";

const route = useRoute();
const router = useRouter();
// Get event details from route params and query
console.log(route);
const eventDetails = computed(() => ({
  eventId: route.query.eventId,
  eventName: route.query.eventName || "Event",
  eventDate: route.query.eventDate,
  eventVenue: route.query.eventVenue || "Virtual Event",
}));

// If needed data is missing, redirect back to events
onMounted(() => {
  // if (!eventId.value) {
  //   router.replace("/events");
  // }
});
</script>
