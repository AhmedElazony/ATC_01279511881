<template>
  <div
    class="min-h-screen flex items-center dark:bg-gray-900 justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8"
  >
    <div class="max-w-md w-full space-y-8">
      <div>
        <router-link to="/">
          <img class="mx-auto h-12 w-auto" src="@/assets/logo.jpg" alt="Logo" />
        </router-link>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
          Reset your password
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-300">
          Enter your email address and we'll send you an otp to verify your
          email
        </p>
      </div>

      <div class="mt-8">
        <div v-if="successMessage" class="rounded-md bg-green-50 p-4 mb-4">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg
                class="h-5 w-5 text-green-400"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
                aria-hidden="true"
              >
                <path
                  fill-rule="evenodd"
                  d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>
            <div class="ml-3">
              <h3 class="text-sm font-medium text-green-800">
                {{ successMessage }}
              </h3>
            </div>
          </div>
        </div>

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

        <form
          class="space-y-6"
          @submit.prevent="sendResetLink"
          v-if="!successMessage"
        >
          <div>
            <div class="mt-1">
              <AppInput
                v-model="form.email"
                type="email"
                label="Email address"
                :error="errors.email"
                required
              />
            </div>
            <p v-if="errors.email" class="mt-2 text-sm text-red-600">
              {{ errors.email }}
            </p>
          </div>

          <div>
            <AppButton
              type="submit"
              variant="primary"
              class="w-full"
              :loading="loading"
              >Send Reset Otp</AppButton
            >
          </div>
        </form>

        <div class="mt-6">
          <div class="relative">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
              <span
                class="px-2 bg-gray-50 text-gray-500 dark:bg-gray-900 dark:text-gray-300"
              >
                Remember your password?
              </span>
            </div>
          </div>

          <div class="mt-6">
            <router-link
              to="/login"
              class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-indigo-600 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              Back to login
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from "vue";
import { useAuthStore } from "@/stores/auth";
import AppInput from "../../components/ui/AppInput.vue";
import AppButton from "../../components/ui/AppButton.vue";
import { useRouter } from "vue-router";
const router = useRouter();
const authStore = useAuthStore();

const form = reactive({
  email: "",
});

const errors = reactive({
  email: "",
});

const loading = ref(false);
const errorMessage = ref("");
const successMessage = ref("");

const sendResetLink = async () => {
  // Reset errors
  errors.email = "";
  errorMessage.value = "";

  // Validate
  if (!form.email) {
    errors.email = "Email is required";
    return;
  }

  try {
    loading.value = true;
    const response = await authStore.forgotPassword(form.email);
    successMessage.value = response.data.message;
    setTimeout(() => {
      router.push("/reset-password");
    }, 3000);
  } catch (error) {
    if (error.response?.data?.errors?.email) {
      errors.email = error.response.data.errors.email[0];
    } else {
      errorMessage.value =
        error.response?.data?.message ||
        "Something went wrong. Please try again.";
    }
  } finally {
    loading.value = false;
  }
};
</script>
