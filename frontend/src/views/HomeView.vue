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
              v-if="!isAuthenticated"
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
          <EventCard v-for="event in events" :event="event" />
        </div>
        <SeeMoreLink to="/events">View All Events</SeeMoreLink>
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
              {{ category.events_count }} events
            </p>
          </div>
        </div>
        <SeeMoreLink to="/categories">View All Categories</SeeMoreLink>
      </div>
    </section>

    <!-- Call to Action -->
    <section
      v-if="!isAuthenticated"
      class="py-16 bg-indigo-700 text-white dark:bg-blue-900"
    >
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
import { ref, onMounted, computed } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "../stores/auth";
import LinkButton from "../components/ui/LinkButton.vue";
import api from "../services/api";
import EventCard from "../components/app/EventCard.vue";
import SeeMoreLink from "../components/ui/SeeMoreLink.vue";

// Use ref for events data that will be populated from API
const events = ref([]);
const categories = ref([]);
const isLoading = ref(true);
const categoriesLoading = ref(true);
const authStore = useAuthStore();
const isAuthenticated = computed(() => authStore.isLoggedIn());

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
      events_count: category.events_count || 0,
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
