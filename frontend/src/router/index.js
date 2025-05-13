// src/router/index.js
import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import HomeView from "@/views/HomeView.vue";
import LoginView from "@/views/auth/LoginView.vue";
import RegisterView from "@/views/auth/RegisterView.vue";
import VerifyEmailView from "@/views/auth/VerifyEmailView.vue";
import ForgotPasswordView from "@/views/auth/ForgotPasswordView.vue";
import ResetPasswordView from "@/views/auth/ResetPasswordView.vue";
import EventsView from "../views/events/EventsView.vue";
import CategoriesView from "../views/categories/CategoriesView.vue";
import MyBookingsView from "../views/bookings/MyBookingsView.vue";
import EventDetailsView from "../views/events/EventDetailsView.vue";
import BookingSuccessView from "../views/bookings/BookingSuccessView.vue";
import AdminView from "../views/admin/AdminEventsView.vue";
import AdminLayout from "../views/admin/AdminLayout.vue";
import AdminEventsView from "../views/admin/AdminEventsView.vue";

const routes = [
  {
    path: "/",
    name: "home",
    component: HomeView,
  },
  {
    path: "/login",
    name: "login",
    component: LoginView,
    meta: { guest: true },
  },
  {
    path: "/register",
    name: "register",
    component: RegisterView,
    meta: { guest: true },
  },
  {
    path: "/verify-email",
    name: "verify-email",
    component: VerifyEmailView,
  },
  {
    path: "/forgot-password",
    name: "forgot-password",
    component: ForgotPasswordView,
    meta: { guest: true },
  },
  {
    path: "/reset-password",
    name: "reset-password",
    component: ResetPasswordView,
    meta: { guest: true },
  },
  {
    path: "/events",
    name: "events",
    component: EventsView,
  },
  {
    path: "/categories",
    name: "categories",
    component: CategoriesView,
  },
  {
    path: "/bookings",
    name: "bookings",
    component: MyBookingsView,
    meta: { requiresAuth: true },
  },
  {
    path: "/events/:id",
    name: "event-details",
    component: EventDetailsView,
  },
  {
    path: "/congratulations",
    name: "congratulations",
    component: BookingSuccessView,
    meta: { requiresAuth: true },
  },
  {
    path: "/admin",
    component: AdminLayout,
    meta: { requiresAuth: true, requiresAdmin: true },
    children: [
      {
        path: "", // Default admin route
        // redirect: { name: "admin-dashboard" },
      },
      // {
      //   path: "dashboard",
      //   name: "admin-dashboard",
      //   component: () => import("@/views/admin/AdminDashboardView.vue"), // Create this component
      // },
      {
        path: "events",
        name: "admin-events",
        component: AdminEventsView,
      },
      // // Add more admin routes as needed
      // {
      //   path: "categories",
      //   name: "admin-categories",
      //   component: () => import("@/views/admin/AdminCategoriesView.vue"), // Create this component
      // },
      // {
      //   path: "bookings",
      //   name: "admin-bookings",
      //   component: () => import("@/views/admin/AdminBookingsView.vue"), // Create this component
      // },
      // {
      //   path: "users",
      //   name: "admin-users",
      //   component: () => import("@/views/admin/AdminUsersView.vue"), // Create this component
      // },
    ],
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// Navigation guards
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();
  const isLoggedIn = authStore.isLoggedIn();

  // If there's a token, try to fetch the user to validate the token
  if (isLoggedIn && !authStore.user) {
    try {
      await authStore.fetchUser();
    } catch (error) {
      // Token is invalid, handle accordingly
      console.log("Invalid token", error);
    }
  }

  // Routes that require a guest (not logged in)
  if (to.matched.some((record) => record.meta.guest)) {
    if (isLoggedIn) {
      next({ path: "/" });
      return;
    }
  }

  // Routes that require authentication
  if (to.matched.some((record) => record.meta.requiresAuth)) {
    if (!isLoggedIn) {
      next({ path: "/login", query: { redirect: to.fullPath } });
      return;
    }
  }

  if (to.matched.some((record) => record.meta.requiresAdmin)) {
    if (
      !isLoggedIn ||
      !authStore.user.role ||
      authStore.user.role !== "super_admin"
    ) {
      next({ path: "/" });
      return;
    }
  }

  next();
});

export default router;
