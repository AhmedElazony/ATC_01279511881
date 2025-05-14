<template>
  <div>
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
        Bookings Management
      </h1>
      <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
        View and manage all event bookings
      </p>
    </div>

    <!-- Loading State -->
    <div v-if="isLoading" class="flex justify-center py-12">
      <div
        class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"
      ></div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="flex justify-center py-12">
      <div class="text-center">
        <svg
          class="mx-auto h-12 w-12 text-red-500"
          xmlns="http://www.w3.org/2000/svg"
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
        <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-white">
          Error loading bookings
        </h3>
        <p class="mt-1 text-gray-500 dark:text-gray-400">{{ error }}</p>
        <div class="mt-6">
          <button
            @click="fetchBookings"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Try Again
          </button>
        </div>
      </div>
    </div>

    <!-- No Bookings State -->
    <div v-else-if="bookings.length === 0" class="flex justify-center py-12">
      <div class="text-center">
        <svg
          class="mx-auto h-12 w-12 text-gray-400"
          xmlns="http://www.w3.org/2000/svg"
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
          {{ searchQuery ? "Try adjusting your search" : "No bookings yet" }}
        </p>
      </div>
    </div>

    <!-- Bookings Table -->
    <div
      v-else
      class="bg-white dark:bg-gray-800 shadow overflow-hidden rounded-lg"
    >
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer"
                @click="sortBy('user.name')"
              >
                Customer
                <sort-icon
                  :field="'user.name'"
                  :current-sort="sortField"
                  :direction="sortDirection"
                />
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
              >
                Event
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer"
                @click="sortBy('created_at')"
              >
                Booking Date
                <sort-icon
                  :field="'booked_at'"
                  :current-sort="sortField"
                  :direction="sortDirection"
                />
              </th>
            </tr>
          </thead>
          <tbody
            class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"
          >
            <tr
              v-for="booking in bookings"
              :key="booking.id"
              class="hover:bg-gray-50 dark:hover:bg-gray-700"
            >
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div>
                    <div
                      class="text-sm font-medium text-gray-900 dark:text-white"
                    >
                      {{ booking.user?.name || "Guest User" }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                      {{ booking.user?.email || booking.customer_email }}
                    </div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="h-10 w-10 flex-shrink-0">
                    <img
                      v-if="booking.event?.image_url"
                      :src="booking.event?.image_url"
                      :alt="booking.event?.name"
                      class="h-10 w-10 rounded-full object-cover"
                    />
                    <div
                      v-else
                      class="h-10 w-10 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center"
                    >
                      <svg
                        class="h-6 w-6 text-gray-400"
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
                  </div>
                  <div class="ml-4">
                    <div
                      class="text-sm font-medium text-gray-900 dark:text-white"
                    >
                      {{ booking.event?.name || "Unknown Event" }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                      {{ formatDate(booking.event?.date) }}
                    </div>
                  </div>
                </div>
              </td>
              <td
                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"
              >
                {{ formatDate(booking.booked_at) }}
              </td>
              <td
                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center"
              >
                <button
                  @click="viewBookingDetails(booking)"
                  class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200"
                >
                  View Details
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div
        class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6"
      >
        <div class="flex items-center justify-between">
          <div class="flex-1 flex justify-between sm:hidden">
            <button
              @click="prevPage"
              :disabled="pagination.current_page === 1"
              :class="[
                'relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md',
                pagination.current_page === 1
                  ? 'bg-gray-100 text-gray-400 cursor-not-allowed dark:bg-gray-700 dark:text-gray-500 dark:border-gray-600'
                  : 'bg-white text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 dark:border-gray-600',
              ]"
            >
              Previous
            </button>
            <button
              @click="nextPage"
              :disabled="pagination.current_page === pagination.total_pages"
              :class="[
                'relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md',
                pagination.current_page === pagination.total_pages
                  ? 'bg-gray-100 text-gray-400 cursor-not-allowed dark:bg-gray-700 dark:text-gray-500 dark:border-gray-600'
                  : 'bg-white text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 dark:border-gray-600',
              ]"
            >
              Next
            </button>
          </div>
          <div
            class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between"
          >
            <div>
              <p class="text-sm text-gray-700 dark:text-gray-300">
                Showing
                <span class="font-medium">{{ pagination.count }}</span>
                of
                <span class="font-medium">{{ pagination.total }}</span>
                results
              </p>
            </div>
            <div>
              <nav
                class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
                aria-label="Pagination"
              >
                <button
                  @click="goToPage(1)"
                  :disabled="pagination.current_page === 1"
                  :class="[
                    'relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium',
                    pagination.current_page === 1
                      ? 'text-gray-300 dark:text-gray-600 dark:bg-gray-800 dark:border-gray-600'
                      : 'text-gray-500 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:bg-gray-700',
                  ]"
                >
                  <span class="sr-only">First Page</span>
                  <svg
                    class="h-5 w-5"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M11 19l-7-7 7-7m8 14l-7-7 7-7"
                    />
                  </svg>
                </button>

                <template v-for="page in pageNumbers" :key="page">
                  <button
                    v-if="page !== '...'"
                    @click="goToPage(page)"
                    :class="[
                      'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                      page === pagination.current_page
                        ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600 dark:bg-indigo-900 dark:border-indigo-500 dark:text-indigo-200'
                        : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400 dark:hover:bg-gray-700',
                    ]"
                  >
                    {{ page }}
                  </button>
                  <span
                    v-else
                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400"
                  >
                    ...
                  </span>
                </template>

                <button
                  @click="goToPage(pagination.total_pages)"
                  :disabled="pagination.current_page === pagination.total_pages"
                  :class="[
                    'relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium',
                    pagination.current_page === pagination.total_pages
                      ? 'text-gray-300 dark:text-gray-600 dark:bg-gray-800 dark:border-gray-600'
                      : 'text-gray-500 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:bg-gray-700',
                  ]"
                >
                  <span class="sr-only">Last Page</span>
                  <svg
                    class="h-5 w-5"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M13 5l7 7-7 7M5 5l7 7-7 7"
                    />
                  </svg>
                </button>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Booking Details Modal -->
    <div v-if="showDetailsModal" class="fixed z-10 inset-0 overflow-y-auto">
      <div
        class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"
      >
        <div class="fixed inset-0 transition-opacity">
          <div
            class="absolute inset-0 bg-gray-500 opacity-75 dark:bg-gray-900 dark:opacity-80"
          ></div>
        </div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen"
          >&#8203;</span
        >

        <div
          class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full"
        >
          <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                <div class="flex justify-between items-center">
                  <h3
                    class="text-lg leading-6 font-medium text-gray-900 dark:text-white"
                  >
                    Booking Details
                  </h3>
                  <span
                    :class="[
                      'px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full',
                      selectedBooking.status === 'confirmed'
                        ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                        : selectedBooking.status === 'pending'
                        ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'
                        : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                    ]"
                  >
                    {{
                      selectedBooking.status?.charAt(0).toUpperCase() +
                      selectedBooking.status?.slice(1)
                    }}
                  </span>
                </div>
                <div
                  class="mt-4 border-t border-gray-200 dark:border-gray-700 pt-4"
                >
                  <div class="mb-6">
                    <div
                      class="text-sm font-medium text-gray-500 dark:text-gray-400"
                    >
                      Reference
                    </div>
                    <div
                      class="mt-1 text-base font-medium text-gray-900 dark:text-white"
                    >
                      {{ selectedBooking.reference }}
                    </div>
                  </div>

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Customer Info -->
                    <div>
                      <h4
                        class="font-medium text-gray-900 dark:text-white mb-2"
                      >
                        Customer Information
                      </h4>
                      <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded">
                        <div class="mb-2">
                          <div
                            class="text-sm font-medium text-gray-500 dark:text-gray-400"
                          >
                            Name
                          </div>
                          <div
                            class="mt-1 text-sm text-gray-900 dark:text-white"
                          >
                            {{
                              selectedBooking.user?.name ||
                              selectedBooking.customer_name ||
                              "N/A"
                            }}
                          </div>
                        </div>
                        <div class="mb-2">
                          <div
                            class="text-sm font-medium text-gray-500 dark:text-gray-400"
                          >
                            Email
                          </div>
                          <div
                            class="mt-1 text-sm text-gray-900 dark:text-white"
                          >
                            {{
                              selectedBooking.user?.email ||
                              selectedBooking.customer_email ||
                              "N/A"
                            }}
                          </div>
                        </div>
                        <div>
                          <div
                            class="text-sm font-medium text-gray-500 dark:text-gray-400"
                          >
                            Phone
                          </div>
                          <div
                            class="mt-1 text-sm text-gray-900 dark:text-white"
                          >
                            {{
                              selectedBooking.user?.phone ||
                              selectedBooking.customer_phone ||
                              "N/A"
                            }}
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Event Info -->
                    <div>
                      <h4
                        class="font-medium text-gray-900 dark:text-white mb-2"
                      >
                        Event Information
                      </h4>
                      <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded">
                        <div class="mb-2">
                          <div
                            class="text-sm font-medium text-gray-500 dark:text-gray-400"
                          >
                            Event Name
                          </div>
                          <div
                            class="mt-1 text-sm text-gray-900 dark:text-white"
                          >
                            {{ selectedBooking.event?.name || "N/A" }}
                          </div>
                        </div>
                        <div class="mb-2">
                          <div
                            class="text-sm font-medium text-gray-500 dark:text-gray-400"
                          >
                            Event Date
                          </div>
                          <div
                            class="mt-1 text-sm text-gray-900 dark:text-white"
                          >
                            {{ formatDate(selectedBooking.event?.date) }}
                          </div>
                        </div>
                        <div>
                          <div
                            class="text-sm font-medium text-gray-500 dark:text-gray-400"
                          >
                            Venue
                          </div>
                          <div
                            class="mt-1 text-sm text-gray-900 dark:text-white"
                          >
                            {{ selectedBooking.event?.venue || "N/A" }}
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Additional Info -->
                  <div class="mt-6">
                    <h4 class="font-medium text-gray-900 dark:text-white mb-2">
                      Additional Information
                    </h4>
                    <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded">
                      <div class="mb-2">
                        <div
                          class="text-sm font-medium text-gray-500 dark:text-gray-400"
                        >
                          Booking Date
                        </div>
                        <div class="mt-1 text-sm text-gray-900 dark:text-white">
                          {{ formatDate(selectedBooking.booked_at) }}
                        </div>
                      </div>
                      <div v-if="selectedBooking.notes">
                        <div
                          class="text-sm font-medium text-gray-500 dark:text-gray-400"
                        >
                          Notes
                        </div>
                        <div class="mt-1 text-sm text-gray-900 dark:text-white">
                          {{ selectedBooking.notes }}
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div
            class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse"
          >
            <button
              @click="showDetailsModal = false"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-gray-600 dark:border-gray-500 dark:text-white dark:hover:bg-gray-500"
            >
              Close
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import api from "@/services/api";

// State
const bookings = ref([]);
const events = ref([]);
const isLoading = ref(true);
const error = ref(null);
const searchQuery = ref("");
const selectedBooking = ref({});
const showDetailsModal = ref(false);
let searchTimeout = null;

// Pagination
const pagination = ref({
  current_page: 1,
  total_pages: 0,
  total: 0,
  count: 0,
  per_page: 0,
  next_page_url: "",
  prev_page_url: "",
});

// Filters and sorting
const filters = ref({
  status: "",
  event_id: "",
  page: 1,
  q: "",
});

const sortField = ref("created_at");
const sortDirection = ref("desc");

// Computed properties
const pageNumbers = computed(() => {
  const currentPage = pagination.value.current_page;
  const lastPage = pagination.value.total_pages;
  const pages = [];

  // Always show first page
  pages.push(1);

  // Show dots if not at the beginning
  if (currentPage > 3) {
    pages.push("...");
  }

  // Show current page and surrounding pages
  for (
    let i = Math.max(2, currentPage - 1);
    i <= Math.min(lastPage - 1, currentPage + 1);
    i++
  ) {
    if (pages[pages.length - 1] !== i && pages[pages.length - 1] !== "...") {
      pages.push(i);
    }
  }

  // Show dots if not at the end
  if (currentPage < lastPage - 2) {
    pages.push("...");
  }

  // Always show last page if there is more than one page
  if (lastPage > 1) {
    pages.push(lastPage);
  }

  return pages;
});

// API calls
const fetchBookings = async () => {
  isLoading.value = true;
  error.value = null;

  try {
    const params = {
      status: filters.value.status,
      event_id: filters.value.event_id,
      page: filters.value.page,
      q: filters.value.q,
      sort_field: sortField.value,
      sort_direction: sortDirection.value,
    };

    const response = await api.get("/admin/bookings", { params });

    if (response.data && response.data.data) {
      bookings.value = response.data.data.items || [];
      if (response.data.data.paginate) {
        pagination.value = {
          current_page: response.data.data.paginate.current_page,
          total_pages: response.data.data.paginate.total_pages,
          total: response.data.data.paginate.total,
          count: response.data.data.paginate.count,
          per_page: response.data.data.paginate.per_page,
          next_page_url: response.data.data.paginate.next_page_url || "",
          prev_page_url: response.data.data.paginate.prev_page_url || "",
        };
      }
    }
  } catch (err) {
    console.error("Error fetching bookings:", err);
    if (err.response?.status === 401) {
      error.value = "You are not authorized to access this page";
    } else {
      error.value = "Failed to load bookings. Please try again.";
    }
  } finally {
    isLoading.value = false;
  }
};

const fetchEvents = async () => {
  try {
    const response = await api.get("/admin/bookings", {
      params: { limit: 100 },
    });
    if (response.data && response.data.data) {
      events.value = response.data.data.items || [];
    }
  } catch (error) {
    console.error("Error fetching events:", error);
  }
};

// Booking actions
const viewBookingDetails = (booking) => {
  selectedBooking.value = booking;
  showDetailsModal.value = true;
};

// Pagination handlers
const prevPage = () => {
  if (pagination.value.current_page > 1) {
    goToPage(pagination.value.current_page - 1);
  }
};

const nextPage = () => {
  if (pagination.value.current_page < pagination.value.total_pages) {
    goToPage(pagination.value.current_page + 1);
  }
};

const goToPage = (page) => {
  filters.value.page = page;
  fetchBookings();
  window.scrollTo({ top: 0, behavior: "smooth" });
};

// Sort functions
const sortBy = (field) => {
  if (sortField.value === field) {
    // Toggle direction if clicking the same field
    sortDirection.value = sortDirection.value === "asc" ? "desc" : "asc";
  } else {
    // Default to ascending for new sort field
    sortField.value = field;
    sortDirection.value = "asc";
  }
  filters.value.page = 1; // Reset to first page when sorting
  fetchBookings();
};

// Search
const debouncedSearch = () => {
  if (searchTimeout) {
    clearTimeout(searchTimeout);
  }

  searchTimeout = setTimeout(() => {
    filters.value.q = searchQuery.value;
    filters.value.page = 1; // Reset to first page when searching
    fetchBookings();
  }, 500);
};

// Utility functions
const formatDate = (dateString) => {
  if (!dateString) return "N/A";

  const date = new Date(dateString);
  return date.toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
    hour: "numeric",
    minute: "2-digit",
  });
};

const formatPrice = (price) => {
  if (price === undefined || price === null) return "N/A";

  return new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
  }).format(price);
};

// Lifecycle hooks
onMounted(async () => {
  await Promise.all([fetchEvents(), fetchBookings()]);
});
</script>

<script>
// Sort icon component for table headers
export default {
  components: {
    SortIcon: {
      props: {
        field: String,
        currentSort: String,
        direction: String,
      },
      template: `
        <span v-if="field === currentSort" class="ml-1 inline-block">
          <svg v-if="direction === 'asc'" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
          </svg>
          <svg v-else class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
        </span>
      `,
    },
  },
};
</script>
