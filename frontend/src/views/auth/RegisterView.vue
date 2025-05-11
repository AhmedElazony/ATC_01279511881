<template>
  <div
    class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8"
  >
    <div class="max-w-md w-full space-y-8">
      <div>
        <a href="/">
          <img class="mx-auto h-12 w-auto" src="@/assets/logo.jpg" alt="Logo" />
        </a>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
          Create a new account
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
          Or
          <router-link
            to="/login"
            class="font-medium text-indigo-600 hover:text-indigo-500"
          >
            sign in to your account
          </router-link>
        </p>
      </div>

      <div class="mt-8">
        <div v-if="errorMessage" class="rounded-md bg-red-50 p-4 mb-4">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg
                class="h-5 w-5 text-red-400"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
                aria-hidden="true"
              >
                <path
                  fill-rule="evenodd"
                  d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>
            <div class="ml-3">
              <h3 class="text-sm font-medium text-red-800">
                {{ errorMessage }}
              </h3>
            </div>
          </div>
        </div>

        <form class="space-y-6" @submit.prevent="register">
          <app-input
            v-model="form.name"
            type="text"
            label="Full name"
            :error="errors.name"
            required
          />

          <app-input
            v-model="form.email"
            type="email"
            label="Email address"
            :error="errors.email"
            required
          />

          <app-input
            v-model="form.password"
            type="password"
            label="Password"
            :error="errors.password"
            required
          />

          <app-input
            v-model="form.password_confirmation"
            type="password"
            label="Confirm password"
            :error="errors.password_confirmation"
            required
          />

          <div>
            <app-button
              type="submit"
              variant="primary"
              class="w-full"
              :loading="loading"
            >
              Create account
            </app-button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from "vue";
import { useRouter } from "vue-router";
import AppInput from "@/components/ui/AppInput.vue";
import AppButton from "@/components/ui/AppButton.vue";
import { useAuthStore } from "@/stores/auth";

const router = useRouter();
const authStore = useAuthStore();

const form = reactive({
  name: "",
  email: "",
  password: "",
  password_confirmation: "",
});

const errors = reactive({
  name: "",
  email: "",
  password: "",
  password_confirmation: "",
});

const loading = ref(false);
const errorMessage = ref("");

const register = async () => {
  // Reset errors
  for (const key in errors) {
    errors[key] = "";
  }
  errorMessage.value = "";

  // Validate
  let valid = true;

  if (!form.name) {
    errors.name = "Name is required";
    valid = false;
  }

  if (!form.email) {
    errors.email = "Email is required";
    valid = false;
  }

  if (!form.password) {
    errors.password = "Password is required";
    valid = false;
  } else if (form.password.length < 8) {
    errors.password = "Password must be at least 8 characters";
    valid = false;
  }

  if (form.password !== form.password_confirmation) {
    errors.password_confirmation = "Passwords do not match";
    valid = false;
  }

  if (!valid) return;

  try {
    loading.value = true;
    await authStore.register(form);
    router.push("/verify-email");
  } catch (error) {
    if (error.response?.data?.errors) {
      const serverErrors = error.response.data.errors;
      for (const key in serverErrors) {
        if (key in errors) {
          errors[key] = serverErrors[key][0];
        }
      }
    } else {
      errorMessage.value =
        error.response?.data?.message || "Something went wrong";
    }
  } finally {
    loading.value = false;
  }
};
</script>
