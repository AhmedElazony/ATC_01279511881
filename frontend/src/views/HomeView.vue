<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="bg-indigo-600 text-white">
      <div
        class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 flex flex-col md:flex-row items-center"
      >
        <div class="md:w-1/2 mb-10 md:mb-0">
          <h1 class="text-4xl md:text-5xl font-bold leading-tight">
            Find and Book Amazing Events
          </h1>
          <p class="mt-4 text-lg md:text-xl text-indigo-100">
            Discover local events, book tickets, and create unforgettable
            memories. From concerts to workshops, find what inspires you.
          </p>
          <div class="mt-8 flex flex-wrap gap-4">
            <LinkButton to="/events">Browse Events</LinkButton>
            <router-link
              to="/register"
              class="bg-indigo-500 hover:bg-indigo-400 text-white px-6 py-3 rounded-md font-medium text-lg transition-colors"
            >
              Sign Up Now
            </router-link>
          </div>
        </div>
        <div class="md:w-1/2 md:pl-10">
          <!-- Hero image -->
          <div
            class="bg-indigo-500 rounded-lg aspect-video shadow-xl flex items-center justify-center text-4xl font-bold"
          >
            <img
              src="@/assets/logo.jpg"
              alt="Hero Image"
              class="w-full h-full object-cover rounded-lg"
            />
          </div>
        </div>
      </div>
    </section>

    <!-- Featured Events Section -->
    <section class="py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-8">Featured Events</h2>

        <div v-if="isLoading" class="flex justify-center py-12">
          <div
            class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"
          ></div>
        </div>

        <div v-else-if="events.length === 0" class="text-center py-12">
          <p class="text-gray-500 text-lg">No events found. Check back soon!</p>
        </div>

        <div
          v-else
          class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8"
        >
          <!-- Event Cards -->
          <div
            v-for="event in events"
            :key="event.id"
            class="bg-white dark:bg-gray-700 rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow"
          >
            <div
              class="aspect-video dark:bg-gray-500 dark:text-white bg-gray-200 flex items-center justify-center text-xl font-semibold text-gray-400"
            >
              <img
                v-if="event.image_url"
                :src="event.image_url"
                alt="Event Image"
                class="w-full h-full object-cover"
              />
              <span v-else>No Image</span>
            </div>
            <div class="p-6">
              <div class="flex justify-between items-start">
                <div>
                  <h3
                    class="text-xl font-bold text-gray-900 dark:text-gray-200"
                  >
                    {{ event.name }}
                  </h3>
                  <p class="text-gray-500 mt-1 dark:text-gray-300">
                    {{ formatDate(event.date) }}
                  </p>
                </div>
                <span
                  class="bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full text-sm font-medium"
                >
                  {{ event.category.name }}
                </span>
              </div>
              <p class="mt-3 text-gray-600 line-clamp-3 dark:text-gray-300">
                {{ event.description }}
              </p>
              <div class="mt-4 flex justify-between items-center">
                <p class="text-indigo-600 font-bold dark:text-indigo-400">
                  {{ formatPrice(event.price) }}
                </p>
                <LinkButton :to="`/events/${event.id}`">
                  View Details</LinkButton
                >
              </div>
            </div>
          </div>
        </div>

        <div class="mt-12 text-center">
          <router-link
            to="/events"
            class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-medium"
          >
            View all events
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
      </div>
    </section>

    <!-- Categories Section -->
    <section class="py-16 bg-gray-100 dark:bg-gray-800">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-8 dark:text-white">
          Browse by Category
        </h2>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
          <div
            v-for="category in categories"
            :key="category.id"
            class="bg-white p-6 rounded-lg shadow hover:shadow-md transition-shadow text-center dark:bg-gray-500"
          >
            <div
              class="w-12 h-12 mx-auto bg-indigo-100 rounded-full flex items-center justify-center mb-4 dark:bg-gray-600"
            >
              <span class="text-indigo-600 text-xl">{{ category.icon }}</span>
            </div>
            <h3 class="font-medium text-gray-900 dark:text-gray-100">
              {{ category.name }}
            </h3>
            <p class="text-sm text-gray-500 mt-1 dark:text-gray-300">
              {{ category.count }} events
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Call to Action -->
    <section class="py-16 bg-indigo-700 text-white dark:bg-blue-900">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-bold mb-4">
          Ready to discover amazing events?
        </h2>
        <p class="text-xl text-indigo-200 mb-8">
          Sign up today and start exploring events near you
        </p>
        <LinkButton to="/register">Create an account</LinkButton>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import LinkButton from "../components/ui/LinkButton.vue";
import api from "../services/api";

// Use ref for events data that will be populated from API
const events = ref([]);
const categories = ref([]);
const isLoading = ref(true);
const categoriesLoading = ref(true);

// Format date helper function
const formatDate = (dateString) => {
  const options = {
    weekday: "short",
    year: "numeric",
    month: "short",
    day: "numeric",
  };
  return new Date(dateString).toLocaleDateString("en-US", options);
};

// Format price helper function
const formatPrice = (price) => {
  return new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
  }).format(price);
};

// Fetch events and categories on component mount
onMounted(async () => {
  try {
    // Fetch featured events
    isLoading.value = true;
    categoriesLoading.value = true;
    const response = await api.get("/home");
    events.value = response.data.data.events || [];
    categories.value = response.data.data.categories.map((category) => ({
      id: category.id,
      name: category.name,
      icon: category.icon || "📅", // Use a default icon if none provided
      count: category.events_count || 0,
    }));
  } catch (error) {
    console.error("Error fetching data:", error);
  } finally {
    isLoading.value = false;
  }
});
</script>

<style scoped>
/* You can add custom styles here if needed */
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
