<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Loading State -->
    <div v-if="isLoading" class="flex justify-center py-24">
      <div
        class="animate-spin rounded-full h-16 w-16 border-b-2 border-indigo-600"
      ></div>
    </div>

    <!-- Error State -->
    <div
      v-else-if="error"
      class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center"
    >
      <svg
        class="mx-auto h-16 w-16 text-red-500"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
        />
      </svg>
      <h2 class="mt-4 text-2xl font-bold text-gray-900 dark:text-white">
        {{ error }}
      </h2>
      <router-link
        to="/events"
        class="mt-6 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700"
      >
        Back to Events
      </router-link>
    </div>

    <!-- Event Details -->
    <div v-else class="pb-16">
      <!-- Event Banner/Header -->
      <div class="relative bg-indigo-600 text-white">
        <!-- Event Image -->
        <div class="w-full h-80 md:h-96 lg:h-[32rem] relative">
          <img
            v-if="event.image_url"
            :src="event.image_url"
            :alt="event.name"
            class="w-full h-full object-cover"
          />
          <div
            v-else
            class="w-full h-full bg-gradient-to-br from-indigo-600 to-purple-700 flex items-center justify-center"
          >
            <svg
              class="h-24 w-24 text-white/40"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="1.5"
                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
              />
            </svg>
          </div>

          <!-- Overlay gradient -->
          <div
            class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"
          ></div>

          <!-- Category badge -->
          <div class="absolute top-4 right-4 z-10">
            <span
              class="bg-indigo-100 text-indigo-800 text-sm px-3 py-1 rounded-full font-medium shadow-md dark:bg-indigo-900 dark:text-indigo-200"
            >
              {{ event.category?.name }}
            </span>
          </div>

          <!-- Back button -->
          <button
            @click="$router.back()"
            class="absolute top-4 left-4 z-10 p-2 rounded-full bg-white/20 backdrop-blur-sm hover:bg-white/30 transition"
          >
            <svg
              class="h-5 w-5 text-white"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M10 19l-7-7m0 0l7-7m-7 7h18"
              />
            </svg>
          </button>

          <!-- Event title and basic info overlay -->
          <div class="absolute bottom-0 left-0 right-0 p-6 md:p-8">
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">
              {{ event.name }}
            </h1>
            <div class="flex flex-wrap gap-4 text-white/90">
              <div class="flex items-center">
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
              <div class="flex items-center">
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
              <div class="flex items-center font-semibold">
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
                  event.price > 0 ? formatPrice(event.price) : "Free"
                }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Main content -->
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Event details (left column on desktop) -->
          <div class="lg:col-span-2">
            <!-- Success message -->
            <div
              v-if="bookingSuccess"
              class="bg-green-50 dark:bg-green-900/30 rounded-lg p-4 mb-6"
            >
              <div class="flex">
                <div class="flex-shrink-0">
                  <svg
                    class="h-5 w-5 text-green-400"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </div>
                <div class="ml-3">
                  <p
                    class="text-sm font-medium text-green-800 dark:text-green-200"
                  >
                    {{ bookingSuccess }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Description -->
            <div class="prose prose-lg dark:prose-invert max-w-none mb-8">
              <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">
                About this event
              </h2>
              <p
                v-if="event.description"
                class="text-gray-600 dark:text-gray-300 whitespace-pre-line"
              >
                {{ event.description }}
              </p>
              <p v-else class="text-gray-500 dark:text-gray-400 italic">
                No description provided for this event.
              </p>
            </div>

            <!-- Tags (if available) -->
            <div v-if="event.tags && event.tags.length > 0" class="mb-8">
              <h3
                class="text-xl font-semibold mb-4 text-gray-900 dark:text-white"
              >
                Tags
              </h3>
              <div class="flex flex-wrap gap-2">
                <span
                  v-for="tag in event.tags"
                  :key="tag.id"
                  class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm dark:bg-gray-700 dark:text-gray-300"
                >
                  {{ tag.name }}
                </span>
              </div>
            </div>

            <!-- Event date details -->
            <div class="mb-8">
              <h3
                class="text-xl font-semibold mb-4 text-gray-900 dark:text-white"
              >
                Date & Time
              </h3>
              <div
                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700"
              >
                <div class="flex items-start">
                  <div class="flex-shrink-0 mr-4">
                    <div
                      class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/50 rounded-lg flex items-center justify-center"
                    >
                      <svg
                        class="h-6 w-6 text-indigo-600 dark:text-indigo-400"
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
                    </div>
                  </div>
                  <div>
                    <p class="font-medium text-gray-900 dark:text-white">
                      {{ formatDateLong(event.date) }}
                    </p>
                    <p class="text-gray-500 dark:text-gray-400">
                      {{ formatTime(event.date) }}
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Location details -->
            <div class="mb-8">
              <h3
                class="text-xl font-semibold mb-4 text-gray-900 dark:text-white"
              >
                Location
              </h3>
              <div
                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700"
              >
                <div class="flex items-start">
                  <div class="flex-shrink-0 mr-4">
                    <div
                      class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/50 rounded-lg flex items-center justify-center"
                    >
                      <svg
                        class="h-6 w-6 text-indigo-600 dark:text-indigo-400"
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
                    </div>
                  </div>
                  <div>
                    <p class="font-medium text-gray-900 dark:text-white">
                      {{ event.venue || "Virtual Event" }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Booking sidebar (right column) -->
          <div>
            <div
              class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 sticky top-8"
            >
              <h3
                class="text-xl font-semibold mb-4 text-gray-900 dark:text-white"
              >
                Book this event
              </h3>

              <!-- Price display -->
              <div class="mb-6">
                <p
                  class="text-3xl font-bold text-indigo-600 dark:text-indigo-400"
                >
                  {{ event.price > 0 ? formatPrice(event.price) : "Free" }}
                </p>
              </div>

              <!-- Booking actions -->
              <div class="space-y-4">
                <!-- For authenticated users -->
                <template v-if="isAuthenticated">
                  <!-- Already booked message -->
                  <div
                    v-if="event.booked_by_user"
                    class="text-center p-4 bg-green-50 rounded-md dark:bg-green-900/30"
                  >
                    <svg
                      class="h-8 w-8 mx-auto text-green-500 dark:text-green-400 mb-2"
                      xmlns="http://www.w3.org/2000/svg"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                      />
                    </svg>
                    <p class="text-green-800 dark:text-green-200 font-medium">
                      You've already booked this event
                    </p>
                    <router-link
                      to="/bookings"
                      class="mt-3 inline-block text-sm text-indigo-600 dark:text-indigo-400 hover:underline"
                    >
                      View your bookings
                    </router-link>
                  </div>

                  <!-- Book now button (hidden for admins) -->
                  <div
                    v-else-if="isAdmin"
                    class="text-center p-4 bg-gray-50 rounded-md dark:bg-gray-700/30"
                  >
                    <p class="text-gray-700 dark:text-gray-300 font-medium">
                      Admin account - booking disabled
                    </p>
                  </div>
                  <button
                    v-else
                    @click="bookEvent"
                    :disabled="isBooking || isEventPassed"
                    :class="[
                      'w-full py-3 px-6 text-center rounded-md text-white font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors',
                      isEventPassed
                        ? 'bg-gray-400 cursor-not-allowed'
                        : isBooking
                        ? 'bg-indigo-400 cursor-wait'
                        : 'bg-indigo-600 hover:bg-indigo-700',
                    ]"
                  >
                    <span v-if="isEventPassed">Event has passed</span>
                    <span v-else-if="isBooking">Processing...</span>
                    <span v-else>Book Now</span>
                  </button>
                </template>

                <!-- For non-authenticated users -->
                <router-link
                  v-else
                  to="/login"
                  class="block w-full py-3 px-6 text-center rounded-md bg-indigo-600 text-white font-medium hover:bg-indigo-700 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  Login to Book
                </router-link>
              </div>

              <!-- Additional info -->
              <div class="mt-6 text-sm text-gray-500 dark:text-gray-400">
                <p class="mb-2">
                  <span class="font-medium">Category:</span>
                  {{ event.category?.name || "Uncategorized" }}
                </p>
                <p>
                  <span class="font-medium">Added:</span>
                  {{ formatDateShort(event.added_at) }}
                </p>
              </div>

              <div
                v-if="linkCopied"
                class="mt-2 text-sm text-green-600 dark:text-green-400"
              >
                Link copied to clipboard!
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
import { useRoute, useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import api from "@/services/api";

// Route and stores
const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();
const isAuthenticated = computed(() => authStore.isLoggedIn());
const isAdmin = computed(
  () => authStore.user.role === "admin" || authStore.user.role === "super_admin"
);

// State
const event = ref({});
const isLoading = ref(true);
const error = ref(null);
const isBooking = ref(false);
const bookingSuccess = ref("");
const linkCopied = ref(false);

// Check if event has already passed
const isEventPassed = computed(() => {
  if (!event.value.date) return false;
  return new Date(event.value.date) < new Date();
});

// Fetch event details
const fetchEventDetails = async () => {
  const eventId = route.params.id;
  isLoading.value = true;
  error.value = null;

  try {
    const headers = {};
    if (authStore.token) {
      headers.Authorization = `Bearer ${authStore.token}`;
    }

    const response = await api.get(`/events/${eventId}/show`, { headers });

    if (response.data.data.event) {
      event.value = response.data.data.event;
    } else {
      throw new Error("Event data not found");
    }
  } catch (err) {
    if (err.status === 404) {
      error.value = "Event not found. Please check the URL.";
    } else {
      console.error("Error fetching event details:", err);
      error.value = "Unable to load event details. Please try again later.";
    }
  } finally {
    isLoading.value = false;
  }
};

// Book event
const bookEvent = async () => {
  if (!isAuthenticated.value || isBooking.value || isEventPassed.value) return;

  isBooking.value = true;
  try {
    await api.post(`/events/${event.value.id}/book`);
    bookingSuccess.value = "You have successfully booked this event!";
    event.value.booked_by_user = true;

    // Wait a brief moment to show success message before redirecting
    setTimeout(() => {
      // Redirect to congratulations page with event details as query params
      router.push({
        path: "/congratulations",
        query: {
          eventId: event.value.id,
          eventName: event.value.name,
          eventDate: event.value.date,
          eventVenue: event.value.venue || "Virtual Event",
        },
      });
    }, 1500);
  } catch (err) {
    console.error("Error booking event:", err);
    if (err.response?.status === 401) {
      // User is not authenticated or token expired
      authStore.logout();
      router.push("/login");
    } else if (err.response?.data?.message) {
      alert(err.response.data.message);
    } else {
      alert("Failed to book event. Please try again later.");
    }
  } finally {
    isBooking.value = false;
  }
};

// Copy event link
const copyEventLink = () => {
  navigator.clipboard
    .writeText(window.location.href)
    .then(() => {
      linkCopied.value = true;
      setTimeout(() => {
        linkCopied.value = false;
      }, 3000);
    })
    .catch((err) => {
      console.error("Failed to copy link:", err);
    });
};

// Format functions
const formatDate = (dateString) => {
  if (!dateString) return "Date not available";

  const date = new Date(dateString);
  return date.toLocaleDateString("en-US", {
    weekday: "short",
    month: "short",
    day: "numeric",
  });
};

const formatDateShort = (dateString) => {
  if (!dateString) return "";
  return new Date(dateString).toLocaleDateString("en-US", {
    month: "short",
    day: "numeric",
    year: "numeric",
  });
};

const formatDateLong = (dateString) => {
  if (!dateString) return "Date not available";

  const date = new Date(dateString);
  return date.toLocaleDateString("en-US", {
    weekday: "long",
    month: "long",
    day: "numeric",
    year: "numeric",
  });
};

const formatTime = (dateString) => {
  if (!dateString) return "";

  const date = new Date(dateString);
  return date.toLocaleTimeString("en-US", {
    hour: "numeric",
    minute: "2-digit",
    hour12: true,
  });
};

const formatPrice = (price) => {
  return new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
  }).format(price);
};

// Lifecycle
onMounted(() => {
  fetchEventDetails();
});
</script>

<style scoped>
/* Fixed aspect ratio for event cards */
.aspect-video {
  aspect-ratio: 16/9;
}

/* Responsive prose styling */
.prose img {
  @apply rounded-lg shadow-md;
}

.prose a {
  @apply text-indigo-600 dark:text-indigo-400;
}

/* Fix for sticky positioning */
.sticky {
  position: sticky;
}
</style>
