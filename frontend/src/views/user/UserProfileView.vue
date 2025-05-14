<template>
  <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
    <div class="py-10">
      <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
            My Profile
          </h1>
          <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            View your account information
          </p>
        </div>

        <!-- Loading State -->
        <div v-if="isLoading" class="flex justify-center py-16">
          <div
            class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"
          ></div>
        </div>

        <!-- Error State -->
        <div
          v-else-if="error"
          class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 text-center"
        >
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
              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
            />
          </svg>
          <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-white">
            Error Loading Profile
          </h3>
          <p class="mt-1 text-gray-500 dark:text-gray-400">{{ error }}</p>
          <div class="mt-4">
            <button
              @click="fetchProfile"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              Try Again
            </button>
          </div>
        </div>

        <!-- Profile Content -->
        <div
          v-else
          class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden"
        >
          <!-- Profile Header -->
          <div
            class="px-6 py-6 bg-indigo-50 dark:bg-indigo-900 flex flex-col items-center sm:flex-row sm:items-start"
          >
            <div class="flex-shrink-0 mb-4 sm:mb-0">
              <div
                v-if="!user.avatar_url"
                class="h-24 w-24 rounded-full bg-indigo-200 dark:bg-indigo-700 flex items-center justify-center text-indigo-800 dark:text-indigo-200 text-2xl font-bold uppercase"
              >
                {{ getInitials(user.name) }}
              </div>
              <img
                v-else
                :src="user.avatar_url"
                :alt="user.name"
                class="h-24 w-24 rounded-full object-cover border-4 border-white dark:border-gray-700"
              />
            </div>
            <div class="sm:ml-6 text-center sm:text-left">
              <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                {{ user.name }}
              </h2>
              <p class="text-gray-600 dark:text-gray-400 mt-1">
                {{ user.email }}
              </p>
              <div
                class="mt-2 flex flex-wrap justify-center sm:justify-start gap-2"
              >
                <span
                  class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200"
                >
                  {{
                    user.email_verified_at
                      ? "Email Verified"
                      : "Email Not Verified"
                  }}
                </span>
                <span
                  class="px-2 py-1 text-xs font-medium rounded-full"
                  :class="
                    user.role === 'admin' || user.role === 'super_admin'
                      ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200'
                      : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
                  "
                >
                  {{ formatRole(user.role) }}
                </span>
              </div>
            </div>
          </div>

          <!-- Profile Details -->
          <div class="px-6 py-6">
            <div
              class="border-b border-gray-200 dark:border-gray-700 pb-5 mb-5"
            >
              <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                Personal Information
              </h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
              <!-- Full Name -->
              <div>
                <div
                  class="text-sm font-medium text-gray-500 dark:text-gray-400"
                >
                  Full Name
                </div>
                <div class="mt-1 text-base text-gray-900 dark:text-white">
                  {{ user.name }}
                </div>
              </div>

              <!-- Email -->
              <div>
                <div
                  class="text-sm font-medium text-gray-500 dark:text-gray-400"
                >
                  Email Address
                </div>
                <div
                  class="mt-1 text-base text-gray-900 dark:text-white flex items-center"
                >
                  {{ user.email }}
                  <svg
                    v-if="user.email_verified_at"
                    class="ml-1.5 h-4 w-4 text-green-500"
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
              </div>

              <!-- Phone -->
              <div>
                <div
                  class="text-sm font-medium text-gray-500 dark:text-gray-400"
                >
                  Phone Number
                </div>
                <div class="mt-1 text-base text-gray-900 dark:text-white">
                  {{ user.phone || "Not provided" }}
                </div>
              </div>

              <!-- Member Since -->
              <div>
                <div
                  class="text-sm font-medium text-gray-500 dark:text-gray-400"
                >
                  Member Since
                </div>
                <div class="mt-1 text-base text-gray-900 dark:text-white">
                  {{ formatDate(user.registered_at) }}
                </div>
              </div>

              <!-- Address -->
              <div class="md:col-span-2">
                <div
                  class="text-sm font-medium text-gray-500 dark:text-gray-400"
                >
                  Address
                </div>
                <div class="mt-1 text-base text-gray-900 dark:text-white">
                  {{ user.address || "Not provided" }}
                </div>
              </div>
            </div>

            <!-- Account Statistics -->
            <div
              class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-5"
            >
              <h3
                class="text-lg font-medium text-gray-900 dark:text-white mb-4"
              >
                Account Statistics
              </h3>

              <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-indigo-50 dark:bg-gray-700 p-4 rounded-lg">
                  <div
                    class="text-sm font-medium text-gray-500 dark:text-gray-400"
                  >
                    Total Bookings
                  </div>
                  <div
                    class="mt-1 text-2xl font-bold text-indigo-600 dark:text-indigo-400"
                  >
                    {{ userStats.total_bookings || 0 }}
                  </div>
                </div>

                <div class="bg-indigo-50 dark:bg-gray-700 p-4 rounded-lg">
                  <div
                    class="text-sm font-medium text-gray-500 dark:text-gray-400"
                  >
                    Upcoming Events
                  </div>
                  <div
                    class="mt-1 text-2xl font-bold text-indigo-600 dark:text-indigo-400"
                  >
                    {{ userStats.upcoming_events || 0 }}
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
import { ref, onMounted } from "vue";
import { useAuthStore } from "@/stores/auth";
import api from "@/services/api";

// State
const authStore = useAuthStore();
const user = ref({});
const userStats = ref({});
const isLoading = ref(true);
const error = ref(null);

// Fetch user profile data
const fetchProfile = async () => {
  isLoading.value = true;
  error.value = null;

  try {
    // Use the authenticated user from store
    if (authStore.user) {
      user.value = authStore.user;
    } else {
      // Fetch user if not in store
      await authStore.fetchUser();
      user.value = authStore.user;
    }

    // Fetch additional stats
    const statsResponse = await api.get("/user/stats");
    if (statsResponse.data && statsResponse.data.data) {
      userStats.value = statsResponse.data.data;
    }
  } catch (err) {
    console.error("Error fetching profile:", err);
    error.value = "Failed to load your profile. Please try again.";
  } finally {
    isLoading.value = false;
  }
};

// Format date to readable string
const formatDate = (dateString) => {
  if (!dateString) return "N/A";

  const date = new Date(dateString);
  return date.toLocaleDateString("en-US", {
    year: "numeric",
    month: "long",
    day: "numeric",
  });
};

// Get initials from name
const getInitials = (name) => {
  if (!name) return "?";

  return name
    .split(" ")
    .map((part) => part.charAt(0))
    .join("")
    .toUpperCase()
    .substring(0, 2);
};

// Format role for display
const formatRole = (role) => {
  if (!role) return "User";

  if (role === "super_admin") return "Super Admin";
  if (role === "admin") return "Administrator";
  return role.charAt(0).toUpperCase() + role.slice(1);
};

// Lifecycle hooks
onMounted(() => {
  fetchProfile();
});
</script>
