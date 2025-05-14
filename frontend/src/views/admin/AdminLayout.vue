<template>
  <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
    <!-- Admin Sidebar (visible on desktop) -->
    <aside
      class="fixed top-0 left-0 z-40 w-64 h-full bg-white dark:bg-gray-800 shadow-md transform transition-transform duration-300 ease-in-out"
      :class="{ '-translate-x-full lg:translate-x-0': !sidebarOpen }"
    >
      <div class="h-full flex flex-col">
        <!-- Logo -->
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
          <div class="flex items-center space-x-2">
            <img src="@/assets/logo.jpg" alt="Logo" class="h-10 w-10 rounded" />
            <span class="text-lg font-bold text-gray-900 dark:text-white"
              >EventSystem Admin</span
            >
          </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-grow px-4 py-6 overflow-y-auto">
          <router-link
            to="/admin/events"
            active-class="bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200"
            class="flex items-center px-4 py-3 mb-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-3"
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
            Events
          </router-link>

          <router-link
            to="/admin/bookings"
            active-class="bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200"
            class="flex items-center px-4 py-3 mb-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-3"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"
              />
            </svg>
            Bookings
          </router-link>

          <router-link
            to="/admin/users"
            active-class="bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200"
            class="flex items-center px-4 py-3 mb-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-3"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
              />
            </svg>
            Users
          </router-link>
        </nav>

        <!-- User profile -->
        <div class="p-4 border-t border-gray-200 dark:border-gray-700">
          <div class="flex items-center">
            <div
              class="flex-shrink-0 h-10 w-10 bg-indigo-500 rounded-full flex items-center justify-center text-white font-bold"
            >
              {{ adminInitials }}
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-gray-900 dark:text-white">
                {{ adminName }}
              </p>
              <button
                @click="logout"
                class="text-xs text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
              >
                Sign out
              </button>
            </div>
          </div>
        </div>
      </div>
    </aside>

    <!-- Main content -->
    <div class="lg:pl-64">
      <!-- Top navigation -->
      <div class="sticky top-0 z-30 bg-white dark:bg-gray-800 shadow-sm">
        <div class="flex justify-between items-center py-3 px-6">
          <!-- Mobile menu button -->
          <button
            @click="toggleSidebar"
            class="lg:hidden text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-white focus:outline-none"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                v-if="sidebarOpen"
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M6 18L18 6M6 6l12 12"
              />
              <path
                v-else
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16"
              />
            </svg>
          </button>

          <div
            class="text-lg font-semibold text-gray-800 dark:text-white lg:hidden"
          >
            EventSystemAdmin
          </div>

          <!-- Right aligned actions -->
          <div class="flex items-center space-x-4">
            <!-- Theme toggle -->
            <button
              @click="toggleDarkMode"
              class="text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-white"
            >
              <svg
                v-if="isDarkMode"
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"
                />
              </svg>
              <svg
                v-else
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"
                />
              </svg>
            </button>

            <router-link
              to="/"
              class="text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300"
            >
              View Website
            </router-link>
          </div>
        </div>
      </div>

      <!-- Page content -->
      <main class="p-6">
        <router-view />
      </main>
    </div>

    <!-- Mobile sidebar overlay -->
    <div
      v-if="sidebarOpen"
      @click="sidebarOpen = false"
      class="fixed inset-0 z-30 bg-gray-600 bg-opacity-50 lg:hidden"
    ></div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";

const authStore = useAuthStore();
const router = useRouter();
const sidebarOpen = ref(false);
const isDarkMode = ref(false);

// Get admin user info
const adminName = computed(() => {
  return authStore.user?.name || "Admin User";
});

const adminInitials = computed(() => {
  if (!authStore.user?.name) return "A";
  return authStore.user.name
    .split(" ")
    .map((n) => n[0])
    .join("")
    .toUpperCase()
    .substring(0, 2);
});

// Toggle sidebar on mobile
const toggleSidebar = () => {
  sidebarOpen.value = !sidebarOpen.value;
};

// Toggle dark mode
const toggleDarkMode = () => {
  isDarkMode.value = !isDarkMode.value;
  if (isDarkMode.value) {
    document.documentElement.classList.add("dark");
    localStorage.theme = "dark";
  } else {
    document.documentElement.classList.remove("dark");
    localStorage.theme = "light";
  }
};

// Logout function
const logout = async () => {
  try {
    await authStore.logout();
    router.push("/login");
  } catch (error) {
    console.error("Logout failed", error);
  }
};

// Check theme preference on component mount
onMounted(() => {
  // Check for dark mode preference
  if (
    localStorage.theme === "dark" ||
    (!("theme" in localStorage) &&
      window.matchMedia("(prefers-color-scheme: dark)").matches)
  ) {
    isDarkMode.value = true;
    document.documentElement.classList.add("dark");
  } else {
    isDarkMode.value = false;
    document.documentElement.classList.remove("dark");
  }
});
</script>
