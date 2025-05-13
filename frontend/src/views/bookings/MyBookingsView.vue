<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Page Header -->
    <div class="bg-indigo-600 text-white py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-3xl md:text-4xl font-bold">My Bookings</h1>
        <p class="mt-3 text-lg text-indigo-100">
          Manage your upcoming events and bookings
        </p>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Loading State -->
      <div v-if="isLoading" class="flex justify-center py-12">
        <div
          class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"
        ></div>
      </div>

      <!-- No Bookings -->
      <div
        v-else-if="bookings.length === 0"
        class="bg-white dark:bg-gray-800 rounded-lg shadow p-8 text-center"
      >
        <svg
          class="mx-auto h-12 w-12 text-gray-400"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
          />
        </svg>
        <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-white">
          No bookings found
        </h3>
        <p class="mt-1 text-gray-500 dark:text-gray-400">
          You haven't booked any events yet.
        </p>
        <div class="mt-6">
          <router-link
            to="/events"
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Browse Events
          </router-link>
        </div>
      </div>

      <!-- Bookings List -->
      <div v-else class="space-y-6">
        <!-- Booking Tabs -->
        <div
          class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden"
        >
          <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="flex -mb-px">
              <button
                @click="activeTab = 'upcoming'"
                :class="[
                  'py-4 px-6 text-sm font-medium focus:outline-none',
                  activeTab === 'upcoming'
                    ? 'border-b-2 border-indigo-500 text-indigo-600 dark:text-indigo-400'
                    : 'text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300',
                ]"
              >
                Upcoming Bookings
              </button>
              <button
                @click="activeTab = 'past'"
                :class="[
                  'py-4 px-6 text-sm font-medium focus:outline-none',
                  activeTab === 'past'
                    ? 'border-b-2 border-indigo-500 text-indigo-600 dark:text-indigo-400'
                    : 'text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300',
                ]"
              >
                Past Bookings
              </button>
            </nav>
          </div>
        </div>

        <!-- Selected Bookings Display -->
        <div
          v-if="filteredBookings.length === 0"
          class="bg-white dark:bg-gray-800 rounded-lg shadow p-8 text-center"
        >
          <p class="text-gray-500 dark:text-gray-400">
            No {{ activeTab }} bookings found.
          </p>
        </div>

        <div v-else>
          <div
            v-for="booking in filteredBookings"
            :key="booking.id"
            class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden mb-4 hover:shadow-lg transition-shadow"
          >
            <div class="md:flex">
              <!-- Event Image -->
              <div class="md:w-1/3 h-48 md:h-auto relative">
                <img
                  v-if="booking.event.image_url"
                  :src="booking.event.image_url"
                  :alt="booking.event.name"
                  class="w-full h-full object-cover"
                />
                <div
                  v-else
                  class="w-full h-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-400 dark:text-gray-300"
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

                <!-- Category Badge -->
                <div class="absolute top-4 left-4">
                  <span
                    class="bg-indigo-100 text-indigo-800 text-xs px-3 py-1 rounded-full font-medium dark:bg-indigo-900 dark:text-indigo-200"
                  >
                    {{ booking.event.category?.name }}
                  </span>
                </div>
              </div>

              <!-- Event Details -->
              <div class="md:w-2/3 p-6">
                <!-- Tags -->
                <div class="flex flex-wrap gap-2 mb-4">
                  <span
                    v-for="tag in booking.event.tags"
                    :key="tag.id"
                    class="bg-gray-200 text-gray-800 text-xs px-2 py-1 rounded-full dark:bg-gray-600 dark:text-gray-200"
                  >
                    {{ tag.name }}
                  </span>
                </div>

                <div class="flex flex-wrap justify-between items-start">
                  <div>
                    <h3
                      class="text-xl font-bold text-gray-900 dark:text-white mb-2"
                    >
                      {{ booking.event.name }}
                    </h3>

                    <!-- Booking Status Badge -->
                    <div class="mb-2">
                      <span
                        :class="[
                          'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                          isPastEvent(booking.event.date)
                            ? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200'
                            : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                        ]"
                      >
                        {{
                          isPastEvent(booking.event.date)
                            ? "Completed"
                            : "Confirmed"
                        }}
                      </span>
                    </div>

                    <!-- Date and Time -->
                    <div
                      class="flex items-center mb-2 text-gray-600 dark:text-gray-300"
                    >
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
                      <span>{{ formatDate(booking.event.date) }}</span>
                    </div>

                    <!-- Location -->
                    <div
                      class="flex items-center mb-3 text-gray-600 dark:text-gray-300"
                    >
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
                      <span>{{ booking.event.venue || "Virtual Event" }}</span>
                    </div>

                    <!-- Booking details -->
                    <div
                      class="flex items-center mb-2 text-gray-600 dark:text-gray-300"
                    >
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
                          d="M15 9a2 2 0 10-4 0v5a2 2 0 01-2 2h6m-6-4h4m8 0a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                      </svg>
                      <span>{{
                        booking.event.price > 0
                          ? formatPrice(booking.event.price)
                          : "Free"
                      }}</span>
                    </div>

                    <!-- Booking date -->
                    <div
                      class="flex items-center mb-2 text-gray-600 dark:text-gray-300 text-sm"
                    >
                      <svg
                        class="h-4 w-4 mr-2"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                      </svg>
                      <span
                        >Booked on
                        {{ formatDate(booking.booked_at, true) }}</span
                      >
                    </div>
                  </div>

                  <!-- Action Buttons -->
                  <div class="mt-4 md:mt-0 space-y-2">
                    <router-link
                      :to="`/events/${booking.event.id}`"
                      class="inline-flex items-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 w-full justify-center"
                    >
                      View Event
                    </router-link>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { formatDate, formatPrice } from "../../utils/formatters";
import api from "../../services/api";

// State
const bookings = ref([]);
const isLoading = ref(true);
const activeTab = ref("upcoming");

// Computed
const upcomingBookings = computed(() => {
  return bookings.value.filter((booking) => !isPastEvent(booking.event.date));
});

const pastBookings = computed(() => {
  return bookings.value.filter((booking) => isPastEvent(booking.event.date));
});

const filteredBookings = computed(() => {
  if (activeTab.value === "upcoming") {
    return upcomingBookings.value;
  } else {
    return pastBookings.value;
  }
});

// Check if an event has already happened
function isPastEvent(date) {
  const eventDate = new Date(date);
  const now = new Date();
  return eventDate < now;
}

// Fetch bookings from API
async function fetchBookings() {
  isLoading.value = true;

  try {
    const response = await api.get("/events/booked");

    if (response.data && response.data.data) {
      bookings.value = response.data.data;
    }
  } catch (error) {
    console.error("Error fetching bookings:", error);
  } finally {
    isLoading.value = false;
  }
}

// Lifecycle hooks
onMounted(() => {
  fetchBookings();
});
</script>
