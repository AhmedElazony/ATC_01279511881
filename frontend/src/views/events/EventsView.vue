<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Page Header -->
    <div class="bg-indigo-600 text-white py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-3xl md:text-4xl font-bold">Discover Events</h1>
        <p class="mt-3 text-lg text-indigo-100">
          Find and book your next experience
        </p>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Search and Filters -->
      <div class="mb-8 bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
        <!-- Search Field -->
        <div class="mb-4">
          <div class="relative">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search events..."
              class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              @input="debouncedSearch"
            />
            <div
              class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
            >
              <svg
                class="h-5 w-5 text-gray-400"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
                aria-hidden="true"
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

        <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
          <!-- Category Filter -->
          <div>
            <label
              class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
              >Category</label
            >
            <select
              v-model="filters.category_id"
              class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              @change="fetchEvents"
            >
              <option value="">All Categories</option>
              <option
                v-for="category in categories"
                :key="category.id"
                :value="category.id"
              >
                {{ category.name }}
              </option>
            </select>
          </div>
        </div>
      </div>

      <!-- Events List -->
      <div>
        <!-- Loading State -->
        <div v-if="isLoading" class="flex justify-center py-12">
          <div
            class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"
          ></div>
        </div>

        <!-- No results -->
        <div
          v-else-if="events.length === 0"
          class="text-center py-12 bg-white dark:bg-gray-800 rounded-lg shadow"
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
              d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
            />
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
            No events found
          </h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Try adjusting your search or filters to find what you're looking
            for.
          </p>
          <div class="mt-6">
            <button
              @click="resetFilters"
              class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              Clear filters
            </button>
          </div>
        </div>

        <!-- Event Cards List -->
        <div
          v-else
          class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
        >
          <EventCard v-for="event in events" :event="event" />
        </div>

        <!-- Pagination -->
        <div
          v-if="pagination.total > pagination.per_page"
          class="mt-8 flex justify-center"
        >
          <nav class="inline-flex rounded-md shadow" aria-label="Pagination">
            <button
              :disabled="pagination.current_page === 1"
              @click="changePage(pagination.current_page - 1)"
              class="relative inline-flex items-center px-3 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300"
            >
              Previous
            </button>

            <span
              class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300"
            >
              {{ pagination.current_page }} of {{ pagination.total_pages }}
            </span>

            <button
              :disabled="pagination.current_page === pagination.total_pages"
              @click="changePage(pagination.current_page + 1)"
              class="relative inline-flex items-center px-3 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300"
            >
              Next
            </button>
          </nav>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import api from "../../services/api";
import EventCard from "../../components/app/EventCard.vue";

const router = useRouter();
const route = useRoute();

// Data
const events = ref([]);
const categories = ref([]);
const tags = ref([]);
const isLoading = ref(true);
const searchQuery = ref("");
let searchTimeout = null;

// Filters
const filters = reactive({
  category_id: "",
  date: "",
  price: "",
  search: "",
  page: 1,
});

// Pagination
const pagination = reactive({
  current_page: 1,
  total_pages: 1,
  total: 0,
  count: 0,
  per_page: 3,
  next_page_url: "",
  prev_page_url: "",
});

// API calls
const fetchEvents = async () => {
  isLoading.value = true;

  try {
    // Build query params
    let queryParams = new URLSearchParams();

    if (filters.category_id)
      queryParams.append("category_id", filters.category_id);
    if (filters.search) queryParams.append("q", filters.search);
    if (filters.page) queryParams.append("page", filters.page);

    const response = await api.get(`/events?${queryParams.toString()}`, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem("token")}`,
      },
    });

    if (response.data && response.data.data.events) {
      events.value = response.data.data.events.items;
      categories.value = response.data.data.categories;
      // Update pagination
      if (response.data.data.events.paginate) {
        pagination.current_page =
          response.data.data.events.paginate.current_page;
        pagination.total_pages = response.data.data.events.paginate.total_pages;
        pagination.total = response.data.data.events.paginate.total;
        pagination.count = response.data.data.events.paginate.count;
        pagination.per_page = response.data.data.events.paginate.per_page;
        pagination.next_page_url =
          response.data.data.events.paginate.next_page_url || "";
        pagination.prev_page_url =
          response.data.data.events.paginate.prev_page_url || "";
      }
    }
  } catch (error) {
    console.error("Error fetching events:", error);
  } finally {
    isLoading.value = false;
  }
};

const fetchCategories = async () => {
  try {
    const response = await api.get("/events/categories");
    if (response.data && response.data.data) {
      categories.value = response.data.data;
    }
  } catch (error) {
    console.error("Error fetching categories:", error);
  }
};

// Actions
const debouncedSearch = () => {
  if (searchTimeout) {
    clearTimeout(searchTimeout);
  }

  searchTimeout = setTimeout(() => {
    filters.search = searchQuery.value;
    filters.page = 1;
    fetchEvents();
  }, 500);
};

const changePage = (page) => {
  filters.page = page;
  fetchEvents();
  // Scroll to top of results
  window.scrollTo({ top: 0, behavior: "smooth" });
};

const resetFilters = () => {
  searchQuery.value = "";
  Object.keys(filters).forEach((key) => {
    if (key !== "page") filters[key] = "";
  });
  filters.page = 1;
  fetchEvents();
};

// Parse URL query params on load
onMounted(() => {
  // Get query params from URL
  if (route.query) {
    if (route.query.category_id) {
      filters.category_id = route.query.category_id;
    }
    if (route.query.search) {
      filters.search = route.query.search;
      searchQuery.value = route.query.search;
    }
    if (route.query.page) {
      filters.page = parseInt(route.query.page) || 1;
    }
  }

  // Fetch categories and events
  Promise.all([fetchCategories(), fetchEvents()]);
});
</script>

<style scoped>
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
