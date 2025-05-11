<template>
  <div class="min-h-screen bg-gray-50">
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
            <router-link
              to="/events"
              class="bg-white text-indigo-600 hover:bg-indigo-50 px-6 py-3 rounded-md font-medium text-lg transition-colors"
            >
              Browse Events
            </router-link>
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
            Event Image
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
            class="bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow"
          >
            <div
              class="aspect-video bg-gray-200 flex items-center justify-center text-xl font-semibold text-gray-400"
            >
              Event Image
            </div>
            <div class="p-6">
              <div class="flex justify-between items-start">
                <div>
                  <h3 class="text-xl font-bold text-gray-900">
                    {{ event.title }}
                  </h3>
                  <p class="text-gray-500 mt-1">
                    {{ formatDate(event.start_date) }}
                  </p>
                </div>
                <span
                  class="bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full text-sm font-medium"
                >
                  {{ event.category }}
                </span>
              </div>
              <p class="mt-3 text-gray-600 line-clamp-3">
                {{ event.description }}
              </p>
              <div class="mt-4 flex justify-between items-center">
                <p class="text-indigo-600 font-bold">
                  {{ formatPrice(event.price) }}
                </p>
                <router-link
                  :to="`/events/${event.id}`"
                  class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded font-medium transition-colors"
                >
                  View Details
                </router-link>
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
    <section class="py-16 bg-gray-100">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-8">
          Browse by Category
        </h2>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
          <div
            v-for="category in categories"
            :key="category.id"
            class="bg-white p-6 rounded-lg shadow hover:shadow-md transition-shadow text-center"
          >
            <div
              class="w-12 h-12 mx-auto bg-indigo-100 rounded-full flex items-center justify-center mb-4"
            >
              <span class="text-indigo-600 text-xl">{{ category.icon }}</span>
            </div>
            <h3 class="font-medium text-gray-900">{{ category.name }}</h3>
            <p class="text-sm text-gray-500 mt-1">
              {{ category.count }} events
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Call to Action -->
    <section class="py-16 bg-indigo-700 text-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-bold mb-4">
          Ready to discover amazing events?
        </h2>
        <p class="text-xl text-indigo-200 mb-8">
          Sign up today and start exploring events near you
        </p>
        <router-link
          to="/register"
          class="bg-white text-indigo-700 hover:bg-indigo-50 px-8 py-4 rounded-md font-bold text-lg inline-block transition-colors"
        >
          Create an Account
        </router-link>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";

// Sample data - replace with API calls in production
const events = ref([
  {
    id: 1,
    title: "Tech Conference 2025",
    description:
      "Join us for the biggest tech conference of the year with speakers from top companies sharing insights on AI, blockchain, and more.",
    start_date: "2025-06-15T09:00:00",
    category: "Technology",
    price: 299.99,
  },
  {
    id: 2,
    title: "Summer Music Festival",
    description:
      "A two-day music festival featuring top artists from around the world. Food, drinks, and camping available.",
    start_date: "2025-07-22T16:00:00",
    category: "Music",
    price: 149.5,
  },
  {
    id: 3,
    title: "Business Leadership Workshop",
    description:
      "Develop your leadership skills with this interactive workshop led by industry experts.",
    start_date: "2025-05-10T10:00:00",
    category: "Business",
    price: 79.99,
  },
]);

const categories = ref([
  { id: 1, name: "Music", count: 42, icon: "🎵" },
  { id: 2, name: "Technology", count: 18, icon: "💻" },
  { id: 3, name: "Sports", count: 24, icon: "⚽" },
  { id: 4, name: "Food & Drink", count: 15, icon: "🍷" },
  { id: 5, name: "Arts", count: 32, icon: "🎨" },
  { id: 6, name: "Business", count: 12, icon: "💼" },
  { id: 7, name: "Wellness", count: 8, icon: "🧘" },
  { id: 8, name: "Education", count: 16, icon: "📚" },
]);

const isLoading = ref(false);

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

// In a real application, you would fetch events from an API
onMounted(async () => {
  try {
    isLoading.value = true;
    // const response = await fetch('/api/events/featured');
    // events.value = await response.json();

    // Simulating API delay
    await new Promise((resolve) => setTimeout(resolve, 500));

    // Using sample data for now
  } catch (error) {
    console.error("Error fetching events:", error);
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
