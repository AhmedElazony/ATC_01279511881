<template>
  <div>
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
        Users Management
      </h1>
      <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
        View and manage system users
      </p>
    </div>

    <!-- Search and filters bar -->
    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow mb-6">
      <div class="flex flex-col md:flex-row gap-4">
        <div class="flex-1">
          <div class="relative">
            <input
              type="text"
              v-model="searchQuery"
              @input="debouncedSearch"
              placeholder="Search by name or email..."
              class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
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
        <div class="w-full md:w-auto flex space-x-3">
          <div>
            <select
              v-model="filters.role"
              @change="fetchUsers"
              class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
            >
              <option value="">All Roles</option>
              <option value="admin">Admin</option>
              <option value="super_admin">Super Admin</option>
              <option value="user">User</option>
            </select>
          </div>
          <!-- Add this button -->
          <button
            v-if="isSuperAdmin"
            @click="openCreateAdminModal"
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-2"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 6v6m0 0v6m0-6h6m-6 0H6"
              />
            </svg>
            New Admin
          </button>
        </div>
      </div>
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
          Error loading users
        </h3>
        <p class="mt-1 text-gray-500 dark:text-gray-400">{{ error }}</p>
        <div class="mt-6">
          <button
            @click="fetchUsers"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Try Again
          </button>
        </div>
      </div>
    </div>

    <!-- No Users State -->
    <div v-else-if="users.length === 0" class="flex justify-center py-12">
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
            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
          />
        </svg>
        <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-white">
          No users found
        </h3>
        <p class="mt-1 text-gray-500 dark:text-gray-400">
          {{
            searchQuery
              ? "Try adjusting your search"
              : "No users have been added yet"
          }}
        </p>
      </div>
    </div>

    <!-- Users Table -->
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
                @click="sortBy('id')"
              >
                ID
                <sort-icon
                  :field="'id'"
                  :current-sort="sortField"
                  :direction="sortDirection"
                />
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer"
                @click="sortBy('name')"
              >
                User
                <sort-icon
                  :field="'name'"
                  :current-sort="sortField"
                  :direction="sortDirection"
                />
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer"
                @click="sortBy('role')"
              >
                Role
                <sort-icon
                  :field="'role'"
                  :current-sort="sortField"
                  :direction="sortDirection"
                />
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer"
                @click="sortBy('created_at')"
              >
                Joined
                <sort-icon
                  :field="'created_at'"
                  :current-sort="sortField"
                  :direction="sortDirection"
                />
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
              >
                Status
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
              >
                Actions
              </th>
            </tr>
          </thead>
          <tbody
            class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"
          >
            <tr
              v-for="user in users"
              :key="user.id"
              class="hover:bg-gray-50 dark:hover:bg-gray-700"
            >
              <td
                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"
              >
                {{ user.id }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-10 w-10">
                    <img
                      v-if="user.avatar_url"
                      class="h-10 w-10 rounded-full object-cover"
                      :src="user.avatar_url"
                      :alt="user.name"
                    />
                    <div
                      v-else
                      class="h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center"
                    >
                      <span
                        class="text-sm font-medium text-indigo-800 dark:text-indigo-200"
                      >
                        {{ getInitials(user.name) }}
                      </span>
                    </div>
                  </div>
                  <div class="ml-4">
                    <div
                      class="text-sm font-medium text-gray-900 dark:text-white"
                    >
                      {{ user.name }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                      {{ user.email }}
                    </div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                  :class="
                    user.role === 'admin' || user.role === 'super_admin'
                      ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200'
                      : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                  "
                >
                  {{ user.role.toUpperCase() }}
                </span>
              </td>
              <td
                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"
              >
                {{ formatDate(user.registered_at) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                  :class="[
                    user.email_verified_at
                      ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                      : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                  ]"
                >
                  {{ user.email_verified_at ? "Verified" : "Unverified" }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                <button
                  @click="viewUserDetails(user)"
                  class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200 mr-2"
                >
                  View
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

    <!-- User Details Modal -->
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
          class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
        >
          <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                <h3
                  class="text-lg leading-6 font-medium text-gray-900 dark:text-white"
                >
                  User Details
                </h3>
                <div class="mt-6">
                  <div class="flex items-center mb-6">
                    <div class="h-20 w-20 flex-shrink-0">
                      <img
                        v-if="selectedUser.avatar_url"
                        class="h-20 w-20 rounded-full object-cover"
                        :src="selectedUser.avatar_url"
                        :alt="selectedUser.name"
                      />
                      <div
                        v-else
                        class="h-20 w-20 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center"
                      >
                        <span
                          class="text-2xl font-medium text-indigo-800 dark:text-indigo-200"
                        >
                          {{ getInitials(selectedUser.name) }}
                        </span>
                      </div>
                    </div>
                    <div class="ml-6">
                      <h4
                        class="text-xl font-medium text-gray-900 dark:text-white"
                      >
                        {{ selectedUser.name }}
                      </h4>
                      <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">
                        {{ selectedUser.email }}
                      </p>
                      <span
                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                        :class="
                          selectedUser.role === 'admin' ||
                          selectedUser.role === 'super_admin'
                            ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200'
                            : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                        "
                      >
                        {{ selectedUser.role.toUpperCase() }}
                      </span>
                    </div>
                  </div>

                  <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                      <div>
                        <div
                          class="text-sm font-medium text-gray-500 dark:text-gray-400"
                        >
                          User ID
                        </div>
                        <div class="mt-1 text-sm text-gray-900 dark:text-white">
                          {{ selectedUser.id }}
                        </div>
                      </div>
                      <div>
                        <div
                          class="text-sm font-medium text-gray-500 dark:text-gray-400"
                        >
                          Joined
                        </div>
                        <div class="mt-1 text-sm text-gray-900 dark:text-white">
                          {{ formatDate(selectedUser.registered_at) }}
                        </div>
                      </div>
                      <div>
                        <div
                          class="text-sm font-medium text-gray-500 dark:text-gray-400"
                        >
                          Email Verification
                        </div>
                        <div class="mt-1 text-sm">
                          <span
                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                            :class="[
                              selectedUser.email_verified_at
                                ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                                : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                            ]"
                          >
                            {{
                              selectedUser.email_verified_at
                                ? "Verified on " +
                                  formatDate(selectedUser.email_verified_at)
                                : "Not Verified"
                            }}
                          </span>
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

    <!-- Admin Creation Modal -->
    <div v-if="showCreateAdminModal" class="fixed z-10 inset-0 overflow-y-auto">
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
          class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
        >
          <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                <h3
                  class="text-lg leading-6 font-medium text-gray-900 dark:text-white"
                >
                  Create New Admin
                </h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                  Add a new administrator to the system
                </p>

                <!-- Form -->
                <form @submit.prevent="createAdmin" class="mt-4">
                  <!-- Form fields -->
                  <div class="space-y-4">
                    <div>
                      <label
                        for="name"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                      >
                        Full Name
                      </label>
                      <input
                        type="text"
                        id="name"
                        v-model="newAdmin.name"
                        required
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        placeholder="John Doe"
                      />
                      <p
                        v-if="errors.name"
                        class="mt-1 text-sm text-red-600 dark:text-red-400"
                      >
                        {{ errors.name }}
                      </p>
                    </div>

                    <div>
                      <label
                        for="email"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                      >
                        Email Address
                      </label>
                      <input
                        type="email"
                        id="email"
                        v-model="newAdmin.email"
                        required
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        placeholder="admin@example.com"
                      />
                      <p
                        v-if="errors.email"
                        class="mt-1 text-sm text-red-600 dark:text-red-400"
                      >
                        {{ errors.email }}
                      </p>
                    </div>

                    <div>
                      <label
                        for="password"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                      >
                        Password
                      </label>
                      <input
                        type="password"
                        id="password"
                        v-model="newAdmin.password"
                        required
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        placeholder="Minimum 8 characters"
                      />
                      <p
                        v-if="errors.password"
                        class="mt-1 text-sm text-red-600 dark:text-red-400"
                      >
                        {{ errors.password }}
                      </p>
                    </div>

                    <div>
                      <label
                        for="password_confirmation"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                      >
                        Confirm Password
                      </label>
                      <input
                        type="password"
                        id="password_confirmation"
                        v-model="newAdmin.password_confirmation"
                        required
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        placeholder="Confirm password"
                      />
                    </div>

                    <div>
                      <label
                        for="role"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                      >
                        Admin Role
                      </label>
                      <select
                        id="role"
                        v-model="newAdmin.role"
                        required
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                      >
                        <option value="admin">Admin</option>
                        <option
                          v-if="currentUserIsSuperAdmin"
                          value="super_admin"
                        >
                          Super Admin
                        </option>
                      </select>
                      <p
                        v-if="errors.role"
                        class="mt-1 text-sm text-red-600 dark:text-red-400"
                      >
                        {{ errors.role }}
                      </p>
                    </div>
                  </div>

                  <!-- Loading state -->
                  <div v-if="isCreating" class="mt-4 flex justify-center">
                    <div
                      class="animate-spin rounded-full h-6 w-6 border-b-2 border-indigo-600"
                    ></div>
                  </div>

                  <!-- Error message -->
                  <div
                    v-if="createError"
                    class="mt-4 p-2 bg-red-100 border border-red-400 text-red-700 dark:bg-red-900 dark:text-red-200 dark:border-red-700 rounded"
                  >
                    {{ createError }}
                  </div>

                  <!-- Success message -->
                  <div
                    v-if="createSuccess"
                    class="mt-4 p-2 bg-green-100 border border-green-400 text-green-700 dark:bg-green-900 dark:text-green-200 dark:border-green-700 rounded"
                  >
                    {{ createSuccess }}
                  </div>
                </form>
              </div>
            </div>
          </div>
          <div
            class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse"
          >
            <button
              type="button"
              @click="createAdmin"
              :disabled="isCreating"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Create Admin
            </button>
            <button
              type="button"
              @click="closeCreateAdminModal"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-gray-600 dark:border-gray-500 dark:text-white dark:hover:bg-gray-500"
            >
              Cancel
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
import { useAuthStore } from "../../stores/auth";

// State
const users = ref([]);
const isLoading = ref(true);
const error = ref(null);
const searchQuery = ref("");
const selectedUser = ref({});
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
  role: "",
  status: "",
  page: 1,
  q: "",
});

const sortField = ref("id");
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
const fetchUsers = async () => {
  isLoading.value = true;
  error.value = null;

  try {
    const params = {
      role: filters.value.role,
      status: filters.value.status,
      page: filters.value.page,
      q: filters.value.q,
      sort_field: sortField.value,
      sort_direction: sortDirection.value,
    };

    const response = await api.get("/admin/users", { params });

    if (response.data && response.data.data) {
      users.value = response.data.data.items || [];
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
    console.error("Error fetching users:", err);
    if (err.response?.status === 401) {
      error.value = "You are not authorized to access this page";
    } else {
      error.value = "Failed to load users. Please try again.";
    }
  } finally {
    isLoading.value = false;
  }
};

// User actions
const viewUserDetails = (user) => {
  selectedUser.value = user;
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
  fetchUsers();
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
  fetchUsers();
};

// Search
const debouncedSearch = () => {
  if (searchTimeout) {
    clearTimeout(searchTimeout);
  }

  searchTimeout = setTimeout(() => {
    filters.value.q = searchQuery.value;
    filters.value.page = 1; // Reset to first page when searching
    fetchUsers();
  }, 500);
};

// Helper functions
const getInitials = (name) => {
  if (!name) return "U";

  return name
    .split(" ")
    .map((part) => part.charAt(0))
    .join("")
    .toUpperCase()
    .substring(0, 2);
};

// Utility functions
const formatDate = (dateString) => {
  if (!dateString) return "N/A";

  const date = new Date(dateString);
  return date.toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
  });
};

const showCreateAdminModal = ref(false);
const newAdmin = ref({
  name: "",
  email: "",
  password: "",
  password_confirmation: "",
  role: "admin",
});
const errors = ref({});
const isCreating = ref(false);
const createError = ref(null);
const createSuccess = ref(null);
const authStore = useAuthStore();

// Check if current user is super admin
const isSuperAdmin = computed(() => {
  return authStore.user && authStore.user.role === "super_admin";
});

// Admin creation methods
const openCreateAdminModal = () => {
  showCreateAdminModal.value = true;
  resetCreateAdminForm();
};

const closeCreateAdminModal = () => {
  showCreateAdminModal.value = false;
  resetCreateAdminForm();
};

const resetCreateAdminForm = () => {
  newAdmin.value = {
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
    role: "admin",
  };
  errors.value = {};
  createError.value = null;
  createSuccess.value = null;
};

const validateForm = () => {
  const validationErrors = {};

  if (!newAdmin.value.name.trim()) {
    validationErrors.name = "Name is required";
  }

  if (!newAdmin.value.email.trim()) {
    validationErrors.email = "Email is required";
  } else if (!/\S+@\S+\.\S+/.test(newAdmin.value.email)) {
    validationErrors.email = "Email is invalid";
  }

  if (!newAdmin.value.password) {
    validationErrors.password = "Password is required";
  } else if (newAdmin.value.password.length < 8) {
    validationErrors.password = "Password must be at least 8 characters";
  } else if (newAdmin.value.password !== newAdmin.value.password_confirmation) {
    validationErrors.password = "Passwords do not match";
  }

  errors.value = validationErrors;
  return Object.keys(validationErrors).length === 0;
};

const createAdmin = async () => {
  if (!validateForm()) return;

  isCreating.value = true;
  createError.value = null;
  createSuccess.value = null;

  try {
    await api.post("/admin/users", {
      name: newAdmin.value.name,
      email: newAdmin.value.email,
      password: newAdmin.value.password,
      password_confirmation: newAdmin.value.password_confirmation,
      role: newAdmin.value.role,
    });

    createSuccess.value = "Admin created successfully";

    // Fetch users again to update the list
    setTimeout(() => {
      fetchUsers();
      closeCreateAdminModal();
    }, 1500);
  } catch (err) {
    console.error("Error creating admin:", err);

    if (err.response?.data?.errors) {
      errors.value = err.response.data.errors;
    } else {
      createError.value =
        err.response?.data?.message ||
        "Failed to create admin. Please try again.";
    }
  } finally {
    isCreating.value = false;
  }
};

// Lifecycle hooks
onMounted(() => {
  fetchUsers();
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
