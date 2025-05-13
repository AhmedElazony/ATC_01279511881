<template>
  <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
    <div class="py-10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
          <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
            Event Management
          </h1>
          <p class="mt-2 text-gray-600 dark:text-gray-400">
            Create, edit and manage your events
          </p>
        </div>

        <!-- Action Bar -->
        <div
          class="flex flex-col md:flex-row md:items-center md:justify-between bg-white dark:bg-gray-800 p-4 rounded-lg shadow mb-6"
        >
          <div class="flex-1 mb-4 md:mb-0">
            <div class="relative">
              <input
                type="text"
                v-model="searchQuery"
                @input="debouncedSearch"
                placeholder="Search events..."
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
              />
              <span class="absolute right-3 top-2 text-gray-400">
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
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                  />
                </svg>
              </span>
            </div>
          </div>
          <div class="flex flex-col sm:flex-row gap-4">
            <div class="w-full sm:w-auto">
              <select
                v-model="filters.category_id"
                @change="fetchEvents"
                class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
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

            <button
              @click="openCreateModal"
              class="flex items-center justify-center px-6 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              <svg
                class="h-4 w-4 mr-2"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 4v16m8-8H4"
                />
              </svg>
              Add Event
            </button>
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
              Error loading events
            </h3>
            <p class="mt-1 text-gray-500 dark:text-gray-400">{{ error }}</p>
            <div class="mt-6">
              <button
                @click="fetchEvents"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                Try Again
              </button>
            </div>
          </div>
        </div>

        <!-- No Events State -->
        <div v-else-if="events.length === 0" class="flex justify-center py-12">
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
                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
              />
            </svg>
            <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-white">
              No events found
            </h3>
            <p class="mt-1 text-gray-500 dark:text-gray-400">
              {{
                searchQuery
                  ? "Try adjusting your search"
                  : "Get started by creating a new event"
              }}
            </p>
            <div class="mt-6">
              <button
                @click="openCreateModal"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
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
                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                  />
                </svg>
                Create New Event
              </button>
            </div>
          </div>
        </div>

        <!-- Events Table -->
        <div
          v-else
          class="bg-white dark:bg-gray-800 shadow overflow-hidden rounded-lg"
        >
          <div class="overflow-x-auto">
            <table
              class="min-w-full divide-y divide-gray-200 dark:divide-gray-700"
            >
              <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                  <th
                    scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                  >
                    Event
                  </th>
                  <th
                    scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                  >
                    Featured?
                  </th>
                  <th
                    scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                  >
                    Category
                  </th>
                  <th
                    scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                  >
                    Tags
                  </th>
                  <th
                    scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                  >
                    Date
                  </th>
                  <th
                    scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                  >
                    Price
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
                  v-for="event in events"
                  :key="event.id"
                  class="hover:bg-gray-50 dark:hover:bg-gray-700"
                >
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="h-10 w-10 flex-shrink-0">
                        <img
                          v-if="event.image_url"
                          :src="event.image_url"
                          :alt="event.name"
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
                          {{ event.name }}
                        </div>
                        <div
                          class="text-sm text-gray-500 dark:text-gray-400 truncate max-w-xs"
                        >
                          {{ truncateText(event.description, 50) }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div
                      class="text-sm font-medium text-gray-900 dark:text-white flex items-center"
                    >
                      <span
                        v-if="!!event.featured"
                        class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200"
                      >
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-4 w-4 mr-1"
                          viewBox="0 0 20 20"
                          fill="currentColor"
                        >
                          <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                          />
                        </svg>
                        Featured
                      </span>
                      <span v-else class="text-gray-400 dark:text-gray-600">
                        Not featured
                      </span>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span
                      v-if="event.category"
                      class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200"
                    >
                      {{ event.category.name }}
                    </span>
                    <span
                      v-else
                      class="text-gray-500 dark:text-gray-400 text-sm"
                    >
                      Uncategorized
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <!-- Add this after the event description in your table -->
                    <div
                      v-if="event.tags && event.tags.length > 0"
                      class="mt-1 flex flex-wrap gap-1"
                    >
                      <span
                        v-for="tag in event.tags"
                        :key="tag.id"
                        class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300"
                      >
                        {{ tag.name }}
                      </span>
                    </div>
                  </td>
                  <td
                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"
                  >
                    {{ formatDate(event.date) }}
                  </td>
                  <td
                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"
                  >
                    {{ event.price > 0 ? formatPrice(event.price) : "Free" }}
                  </td>
                  <td
                    class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
                  >
                    <button
                      @click="openEditModal(event)"
                      class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200 mr-4"
                    >
                      Edit
                    </button>
                    <router-link
                      :to="`/events/${event.id}`"
                      target="_blank"
                      class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200 mr-4"
                    >
                      View
                    </router-link>
                    <button
                      @click="confirmDelete(event)"
                      class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-200"
                    >
                      Delete
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
                    <button
                      @click="prevPage"
                      :disabled="pagination.current_page === 1"
                      :class="[
                        'relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium',
                        pagination.current_page === 1
                          ? 'text-gray-300 dark:text-gray-600 dark:bg-gray-800 dark:border-gray-600'
                          : 'text-gray-500 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:bg-gray-700',
                      ]"
                    >
                      <span class="sr-only">Previous</span>
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
                          d="M15 19l-7-7 7-7"
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
                      @click="nextPage"
                      :disabled="
                        pagination.current_page === pagination.total_pages
                      "
                      :class="[
                        'relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium',
                        pagination.current_page === pagination.total_pages
                          ? 'text-gray-300 dark:text-gray-600 dark:bg-gray-800 dark:border-gray-600'
                          : 'text-gray-500 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:bg-gray-700',
                      ]"
                    >
                      <span class="sr-only">Next</span>
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
                          d="M9 5l7 7-7 7"
                        />
                      </svg>
                    </button>
                    <button
                      @click="goToPage(pagination.total_pages)"
                      :disabled="
                        pagination.current_page === pagination.total_pages
                      "
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
      </div>
    </div>

    <!-- Event Form Modal -->
    <div v-if="showModal" class="fixed z-10 inset-0 overflow-y-auto">
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
          <form @submit.prevent="saveEvent">
            <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
              <div class="sm:flex sm:items-start">
                <div class="w-full">
                  <h3
                    class="text-lg leading-6 font-medium text-gray-900 dark:text-white"
                  >
                    {{ isEditMode ? "Edit Event" : "Create New Event" }}
                  </h3>
                  <div class="mt-6 space-y-6">
                    <!-- Event Name -->
                    <div>
                      <label
                        for="name"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                      >
                        Event Name
                      </label>
                      <input
                        type="text"
                        id="name"
                        v-model="eventForm.name"
                        required
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                      />
                    </div>

                    <!-- Event Description -->
                    <div>
                      <label
                        for="description"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                      >
                        Description
                      </label>
                      <textarea
                        id="description"
                        v-model="eventForm.description"
                        rows="3"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                      ></textarea>
                    </div>

                    <!-- Event Category -->
                    <div>
                      <label
                        for="category"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                      >
                        Category
                      </label>
                      <select
                        id="category"
                        v-model="eventForm.category_id"
                        required
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                      >
                        <option disabled value="">Select a category</option>
                        <option
                          v-for="category in categories"
                          :key="category.id"
                          :value="category.id"
                        >
                          {{ category.name }}
                        </option>
                      </select>
                    </div>
                    <!-- Event Tags -->
                    <div>
                      <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                      >
                        Tags
                      </label>
                      <div class="mt-1">
                        <div class="flex flex-wrap items-center gap-2 mb-2">
                          <!-- Display existing tags -->
                          <span
                            v-for="(tag, index) in eventForm.tags"
                            :key="index"
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200"
                          >
                            {{ tag.name }}
                            <button
                              type="button"
                              @click="removeTag(index)"
                              class="ml-1.5 inline-flex items-center justify-center h-4 w-4 rounded-full text-indigo-400 hover:bg-indigo-200 hover:text-indigo-600 dark:hover:bg-indigo-800 focus:outline-none"
                            >
                              <span class="sr-only">Remove tag</span>
                              <svg
                                class="h-2 w-2"
                                stroke="currentColor"
                                fill="none"
                                viewBox="0 0 8 8"
                              >
                                <path
                                  stroke-linecap="round"
                                  stroke-width="1.5"
                                  d="M1 1l6 6m0-6L1 7"
                                />
                              </svg>
                            </button>
                          </span>

                          <!-- Input for new tag -->
                          <div class="relative">
                            <input
                              type="text"
                              v-model="newTag"
                              @keydown.enter.prevent="addTag"
                              placeholder="Add tag..."
                              class="py-1 px-3 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            />
                            <button
                              type="button"
                              @click="addTag"
                              class="ml-2 inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-indigo-900 dark:text-indigo-200 dark:hover:bg-indigo-800"
                            >
                              Add
                            </button>
                          </div>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                          Press Enter or click Add to add a tag
                        </p>
                      </div>
                    </div>
                    <!-- Event Date/Time -->
                    <div>
                      <label
                        for="date"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                      >
                        Date and Time
                      </label>
                      <input
                        type="datetime-local"
                        id="date"
                        v-model="eventForm.date"
                        required
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                      />
                    </div>

                    <!-- Event Venue -->
                    <div>
                      <label
                        for="venue"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                      >
                        Venue
                      </label>
                      <input
                        type="text"
                        id="venue"
                        v-model="eventForm.venue"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                      />
                    </div>

                    <!-- Event Price -->
                    <div>
                      <label
                        for="price"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                      >
                        Price
                      </label>
                      <div class="mt-1 relative rounded-md shadow-sm">
                        <div
                          class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                        >
                          <span
                            class="text-gray-500 sm:text-sm dark:text-gray-400"
                          >
                            $
                          </span>
                        </div>
                        <input
                          type="number"
                          id="price"
                          v-model="eventForm.price"
                          min="0"
                          step="0.01"
                          class="pl-7 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                          placeholder="0.00"
                        />
                      </div>
                    </div>
                    <!-- Featured Event Checkbox -->
                    <div class="flex items-center">
                      <input
                        id="featured"
                        v-model="eventForm.featured"
                        type="checkbox"
                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded dark:border-gray-600 dark:bg-gray-700"
                      />
                      <label
                        for="featured"
                        class="ml-2 block text-sm text-gray-700 dark:text-gray-300"
                      >
                        Featured Event (will be highlighted on the homepage)
                      </label>
                    </div>

                    <!-- Event Image -->
                    <div>
                      <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                      >
                        Event Image
                      </label>
                      <div class="mt-1 flex items-center">
                        <div
                          v-if="imagePreview || eventForm.image_url"
                          class="flex-shrink-0 h-16 w-16 mr-4"
                        >
                          <img
                            :src="imagePreview || eventForm.image_url"
                            alt="Event preview"
                            class="h-16 w-16 rounded-md object-cover"
                          />
                        </div>
                        <div class="flex-1">
                          <input
                            type="file"
                            ref="fileInput"
                            @change="handleFileUpload"
                            accept="image/*"
                            class="text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900 dark:file:text-indigo-300"
                          />
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
                type="submit"
                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm"
                :disabled="isSaving"
              >
                {{
                  isSaving
                    ? "Saving..."
                    : isEditMode
                    ? "Update Event"
                    : "Create Event"
                }}
              </button>
              <button
                type="button"
                @click="closeModal"
                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-gray-600 dark:border-gray-500 dark:text-white dark:hover:bg-gray-500"
              >
                Cancel
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed z-10 inset-0 overflow-y-auto">
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
              <div
                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900 sm:mx-0 sm:h-10 sm:w-10"
              >
                <svg
                  class="h-6 w-6 text-red-600 dark:text-red-400"
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
              </div>
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <h3
                  class="text-lg leading-6 font-medium text-gray-900 dark:text-white"
                >
                  Delete Event
                </h3>
                <div class="mt-2">
                  <p class="text-sm text-gray-500 dark:text-gray-400">
                    Are you sure you want to delete the event "{{
                      eventToDelete?.name
                    }}"? This action cannot be undone.
                  </p>
                </div>
              </div>
            </div>
          </div>
          <div
            class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse"
          >
            <button
              @click="deleteEvent"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
              :disabled="isDeleting"
            >
              {{ isDeleting ? "Deleting..." : "Delete" }}
            </button>
            <button
              @click="showDeleteModal = false"
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
import { useAuthStore } from "@/stores/auth";
import api from "@/services/api";

// State
const events = ref([]);
const categories = ref([]);
const isLoading = ref(true);
const error = ref(null);
const searchQuery = ref("");
const isSaving = ref(false);
const isDeleting = ref(false);
let searchTimeout = null;

// Modals
const showModal = ref(false);
const isEditMode = ref(false);
const eventForm = ref({
  name: "",
  description: "",
  date: "",
  venue: "",
  price: 0,
  featured: false,
  category_id: "",
  image: null,
  tags: [],
});
const imagePreview = ref(null);
const showDeleteModal = ref(false);
const eventToDelete = ref(null);

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

const filters = ref({
  category_id: "",
  page: 1,
  q: "",
});

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

// For tag management
const newTag = ref("");

const addTag = () => {
  if (newTag.value.trim()) {
    // Check if tag already exists
    const tagExists = eventForm.value.tags.some(
      (tag) => tag.name.toLowerCase() === newTag.value.trim().toLowerCase()
    );

    if (!tagExists) {
      eventForm.value.tags.push({ name: newTag.value.trim() });
    }
    newTag.value = "";
  }
};

const removeTag = (index) => {
  eventForm.value.tags.splice(index, 1);
};

// API calls
const fetchEvents = async () => {
  isLoading.value = true;
  error.value = null;

  try {
    const params = {
      category_id: filters.value.category_id,
      q: filters.value.q,
      page: filters.value.page,
    };

    const response = await api.get("/admin/events", { params });

    if (response.data && response.data.data) {
      events.value = response.data.data.items || [];
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
    console.error("Error fetching events:", err);
    if (err.response?.status === 401) {
      error.value = "You are not authorized to access this page";
    } else {
      error.value = "Failed to load events. Please try again.";
    }
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

// Event actions
const openCreateModal = () => {
  isEditMode.value = false;
  eventForm.value = {
    name: "",
    description: "",
    date: formatDateForInput(new Date()),
    venue: "",
    price: 0,
    featured: false,
    category_id: categories.value.length > 0 ? categories.value[0].id : "",
    image: null,
    tags: [],
  };
  imagePreview.value = null;
  showModal.value = true;
};

const openEditModal = (event) => {
  isEditMode.value = true;
  eventForm.value = {
    id: event.id,
    name: event.name,
    description: event.description,
    date: formatDateForInput(new Date(event.date)),
    venue: event.venue,
    price: event.price,
    featured: event.featured,
    category_id: event.category?.id || "",
    image_url: event.image_url,
    tags: event.tags,
  };
  imagePreview.value = null;
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  eventForm.value = {
    name: "",
    description: "",
    date: "",
    venue: "",
    price: 0,
    featured: false,
    category_id: "",
    image: null,
    tags: [],
  };
  imagePreview.value = null;
  newTag.value = "";
};

const handleFileUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    eventForm.value.image = file;
    const reader = new FileReader();
    reader.onload = (e) => {
      imagePreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
  }
};

const saveEvent = async () => {
  isSaving.value = true;

  try {
    const formData = new FormData();
    Object.keys(eventForm.value).forEach((key) => {
      if (
        key !== "image" &&
        key !== "image_url" &&
        key !== "tags" &&
        key !== "featured" &&
        eventForm.value[key] !== null
      ) {
        formData.append(key, eventForm.value[key]);
      }
    });

    if (eventForm.value.image) {
      formData.append("image", eventForm.value.image);
    }

    if (eventForm.value.tags && eventForm.value.tags.length > 0) {
      formData.append(
        "tags",
        JSON.stringify(eventForm.value.tags.map((tag) => tag.name))
      );
    }

    formData.append("featured", eventForm.value.featured === true ? "1" : "0");

    let response;
    if (isEditMode.value) {
      formData.append("_method", "PUT");
      response = await api.post(
        `/admin/events/${eventForm.value.id}/update`,
        formData,
        {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        }
      );
    } else {
      response = await api.post("/admin/events/store", formData, {
        headers: {
          "Content-Type": "multipart/form-data",
        },
      });
    }

    closeModal();
    fetchEvents();
  } catch (err) {
    console.error("Error saving event:", err);
    alert(err.response.data.message);
  } finally {
    isSaving.value = false;
  }
};

const confirmDelete = (event) => {
  eventToDelete.value = event;
  showDeleteModal.value = true;
};

const deleteEvent = async () => {
  if (!eventToDelete.value) return;

  isDeleting.value = true;
  try {
    await api.delete(`/admin/events/${eventToDelete.value.id}/destroy`);
    showDeleteModal.value = false;
    eventToDelete.value = null;
    fetchEvents();
  } catch (err) {
    console.error("Error deleting event:", err);
    alert("Failed to delete event. Please try again.");
  } finally {
    isDeleting.value = false;
  }
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
  fetchEvents();
  window.scrollTo({ top: 0, behavior: "smooth" });
};

// Search
const debouncedSearch = () => {
  if (searchTimeout) {
    clearTimeout(searchTimeout);
  }

  searchTimeout = setTimeout(() => {
    filters.value.q = searchQuery.value;
    filters.value.page = 1;
    fetchEvents();
  }, 500);
};

// Utility functions
const formatDate = (dateString) => {
  if (!dateString) return "Date not available";

  const date = new Date(dateString);
  return date.toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
    hour: "numeric",
    minute: "2-digit",
  });
};

const formatDateForInput = (date) => {
  const d = new Date(date);
  const year = d.getFullYear();
  const month = String(d.getMonth() + 1).padStart(2, "0");
  const day = String(d.getDate()).padStart(2, "0");
  const hours = String(d.getHours()).padStart(2, "0");
  const minutes = String(d.getMinutes()).padStart(2, "0");

  return `${year}-${month}-${day}T${hours}:${minutes}`;
};

const formatPrice = (price) => {
  return new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
  }).format(price);
};

const truncateText = (text, length) => {
  if (!text) return "";
  return text.length > length ? text.substring(0, length) + "..." : text;
};

// Lifecycle hooks
onMounted(async () => {
  await fetchCategories();
  await fetchEvents();
});
</script>
