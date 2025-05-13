<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Page Header -->
    <div class="bg-indigo-600 text-white py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-3xl md:text-4xl font-bold">Event Categories</h1>
        <p class="mt-3 text-lg text-indigo-100">
          Explore events by category and find what interests you
        </p>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <!-- Loading State -->
      <div v-if="isLoading" class="flex justify-center py-16">
        <div
          class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"
        ></div>
      </div>

      <!-- No Categories Found -->
      <div
        v-else-if="categories.length === 0"
        class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-8 text-center"
      >
        <svg
          class="h-16 w-16 text-gray-400 mx-auto"
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
          />
        </svg>
        <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">
          No categories found
        </h3>
        <p class="mt-2 text-gray-500 dark:text-gray-400">
          Check back soon for new event categories.
        </p>
      </div>

      <!-- Categories Grid -->
      <div v-else>
        <!-- Search/Filter input -->
        <div class="mb-8">
          <div class="relative max-w-md mx-auto">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search categories..."
              class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white"
            />
            <div
              class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
            >
              <svg
                class="h-5 w-5 text-gray-400"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>
          </div>
        </div>

        <!-- Categories Display -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="category in filteredCategories"
            :key="category.id"
            class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow"
          >
            <div
              class="h-32 bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center"
            >
              <span class="text-6xl">{{ category.icon }}</span>
            </div>

            <div class="p-6">
              <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                {{ category.name }}
              </h3>

              <p
                v-if="category.description"
                class="text-gray-600 dark:text-gray-300 mb-4 line-clamp-2"
              >
                {{ category.description }}
              </p>
              <p
                v-else
                class="text-gray-600 dark:text-gray-300 mb-4 line-clamp-2"
              >
                Explore {{ category.name }} events and discover amazing
                experiences.
              </p>

              <div class="flex justify-between items-center">
                <span class="text-indigo-600 dark:text-indigo-400 font-medium">
                  {{ category.events_count || 0 }}
                  {{ category.events_count === 1 ? "event" : "events" }}
                </span>

                <router-link
                  :to="{ name: 'events', query: { category_id: category.id } }"
                  class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
                >
                  View Events
                </router-link>
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
import api from "../../services/api";

// State
const categories = ref([]);
const isLoading = ref(true);
const searchQuery = ref("");

// Computed properties
const filteredCategories = computed(() => {
  if (!searchQuery.value) return categories.value;

  const query = searchQuery.value.toLowerCase();
  return categories.value.filter((category) =>
    category.name.toLowerCase().includes(query)
  );
});

// Fetch categories from API
const fetchCategories = async () => {
  try {
    isLoading.value = true;
    const response = await api.get("/events/categories");

    if (response.data && response.data.data) {
      // Map API response to add default icons if missing
      categories.value = response.data.data.map((category) => ({
        ...category,
        icon: category.icon || getDefaultIcon(category.name),
      }));
    }
  } catch (error) {
    console.error("Error fetching categories:", error);
    // Fallback data in case of error
    categories.value = getSampleCategories();
  } finally {
    isLoading.value = false;
  }
};

// Helper function to get a default icon based on category name
const getDefaultIcon = (categoryName) => {
  const categoryIcons = {
    music: "🎵",
    technology: "💻",
    business: "💼",
    food: "🍽️",
    art: "🎨",
    sports: "🏆",
    health: "🧘",
    education: "🎓",
    entertainment: "🎭",
    travel: "✈️",
    science: "🔬",
    outdoors: "🏞️",
    fashion: "👗",
    gaming: "🎮",
    networking: "🤝",
    workshop: "🛠️",
  };

  const name = categoryName.toLowerCase();

  // Find a matching icon
  for (const [key, icon] of Object.entries(categoryIcons)) {
    if (name.includes(key)) {
      return icon;
    }
  }

  // Default fallback icon
  return "📅";
};

// Sample categories in case API fails
const getSampleCategories = () => {
  return [
    {
      id: 1,
      name: "Music",
      icon: "🎵",
      events_count: 15,
      description:
        "Concerts, festivals, and live performances from artists around the world",
    },
    {
      id: 2,
      name: "Technology",
      icon: "💻",
      events_count: 12,
      description:
        "Tech conferences, hackathons, and workshops for developers and enthusiasts",
    },
    {
      id: 3,
      name: "Business",
      icon: "💼",
      events_count: 8,
      description:
        "Networking events, seminars, and professional development opportunities",
    },
    {
      id: 4,
      name: "Food & Drink",
      icon: "🍽️",
      events_count: 10,
      description: "Food festivals, cooking classes, and culinary experiences",
    },
    {
      id: 5,
      name: "Arts & Culture",
      icon: "🎨",
      events_count: 7,
      description:
        "Art exhibitions, theater performances, and cultural celebrations",
    },
    {
      id: 6,
      name: "Sports",
      icon: "🏆",
      events_count: 9,
      description: "Sporting events, tournaments, and fitness activities",
    },
  ];
};

// Lifecycle hooks
onMounted(() => {
  fetchCategories();
});
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
