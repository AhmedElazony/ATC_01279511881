<template>
  <div
    class="min-h-screen flex items-center justify-center dark:bg-gray-900 bg-gray-50 py-12 px-4 sm:px-6 lg:px-8"
  >
    <div class="max-w-md w-full space-y-8">
      <div>
        <img class="mx-auto h-12 w-auto" src="@/assets/logo.jpg" alt="Logo" />
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
          Verify your email
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-300">
          Please enter the verification code sent to your email
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

        <form class="space-y-6" @submit.prevent="verifyEmail">
          <app-input
            v-model="form.email"
            type="email"
            label="Email address"
            :error="errors.email"
            required
          />

          <app-input
            v-model="form.otp"
            type="text"
            label="Verification code"
            :error="errors.otp"
            required
          />

          <div>
            <app-button
              type="submit"
              variant="primary"
              class="w-full"
              :loading="loading"
            >
              Verify email
            </app-button>
          </div>

          <div class="text-center">
            <p class="text-sm text-gray-600">
              Didn't receive a code?
              <a
                @click.prevent="resendCode"
                href="#"
                class="font-medium text-indigo-600 hover:text-indigo-500 cursor-pointer"
              >
                Resend code
              </a>
            </p>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import AppInput from "@/components/ui/AppInput.vue";
import AppButton from "@/components/ui/AppButton.vue";

const router = useRouter();
const authStore = useAuthStore();

const form = reactive({
  email: "",
  otp: "",
});

const errors = reactive({
  email: "",
  otp: "",
});

const loading = ref(false);
const resendLoading = ref(false);
const errorMessage = ref("");
const successMessage = ref("");

onMounted(() => {
  if (authStore.user) {
    form.email = authStore.user.email;
  }
});

const verifyEmail = async () => {
  // Reset errors
  errors.email = "";
  errors.otp = "";
  errorMessage.value = "";

  // Validate
  let valid = true;

  if (!form.email) {
    errors.email = "Email is required";
    valid = false;
  }

  if (!form.otp) {
    errors.otp = "Verification code is required";
    valid = false;
  }

  if (!valid) return;

  try {
    loading.value = true;
    await authStore.verifyEmail(form);
    successMessage.value = "Email verified successfully!";
    setTimeout(() => {
      router.push("/");
    }, 2000);
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

const resendCode = async () => {
  if (!form.email) {
    errors.email = "Email is required";
    return;
  }

  try {
    resendLoading.value = true;
    await authStore.sendVerificationEmail(form.email);
    successMessage.value = "Verification code sent successfully!";
    errorMessage.value = "";
  } catch (error) {
    successMessage.value = "";
    errorMessage.value =
      error.response?.data?.message || "Failed to send verification code";
  } finally {
    resendLoading.value = false;
  }
};
</script>
