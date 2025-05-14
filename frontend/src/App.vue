<template>
  <AppHeader v-if="route && !hideLayoutForRoutes.includes(route.name)" />
  <div
    class="container w-full flex flex-col min-h-screen dark:bg-gray-900 dark:text-gray-200"
  >
    <main class="">
      <router-view />
    </main>
  </div>
  <AppFooter v-if="route && !hideLayoutForRoutes.includes(route.name)" />
</template>

<script setup>
import { computed, ref } from "vue";
import { useRoute } from "vue-router";
import AppHeader from "./components/layout/AppHeader.vue";
import AppFooter from "./components/layout/AppFooter.vue";

const route = useRoute();

// Routes that should not show the header and footer
const hideLayoutForRoutes = ref([
  "login",
  "register",
  "verify-email",
  "forgot-password",
  "reset-password",
  "admin-bookings",
  "admin-users",
  "admin-categories",
  "admin-events",
]);
</script>

<style>
body {
  margin: 0;
  padding: 0;
  width: 100%;
  overflow-x: hidden;
  font-family: Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto,
    Oxygen, Ubuntu, Cantarell, "Fira Sans", "Droid Sans", "Helvetica Neue",
    sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

/* Remove default margin on all elements */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

html,
body,
#app {
  min-height: 100vh;
  width: 100%;
}

#app {
  display: flex;
  flex-direction: column;
}

/* Global link styling */
a {
  text-decoration: none;
  color: inherit;
}
</style>
