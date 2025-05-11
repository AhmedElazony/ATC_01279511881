<template>
  <div
    class="min-h-screen flex items-center justify-center dark:bg-gray-900 bg-gray-50 py-12 px-4 sm:px-6 lg:px-8"
  >
    <div class="max-w-md w-full space-y-8">
      <div>
        <router-link to="/">
          <img class="mx-auto h-12 w-auto" src="@/assets/logo.jpg" alt="Logo" />
        </router-link>
        <h2
          class="mt-6 text-center text-3xl font-extrabold text-gray-900 dark:text-white"
        >
          Verify your email
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-300">
          Please enter the verification code sent to
          <span class="font-medium text-indigo-600 dark:text-indigo-400">{{
            userEmail
          }}</span>
        </p>
      </div>

      <div class="mt-8">
        <div
          v-if="successMessage"
          class="rounded-md bg-green-50 dark:bg-green-900 p-4 mb-4"
        >
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
              <h3
                class="text-sm font-medium text-green-800 dark:text-green-200"
              >
                {{ successMessage }}
              </h3>
            </div>
          </div>
        </div>

        <div
          v-if="errorMessage"
          class="rounded-md bg-red-50 dark:bg-red-900 p-4 mb-4"
        >
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
              <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
                {{ errorMessage }}
              </h3>
            </div>
          </div>
        </div>

        <form class="space-y-6" @submit.prevent="verifyEmail">
          <app-input
            v-model="form.otp"
            type="text"
            label="Verification code"
            :error="errors.otp"
            required
            autofocus
            class="text-center text-lg"
            placeholder="Enter 6-digit code"
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
            <p class="text-sm text-gray-600 dark:text-gray-400">
              Didn't receive a code?
              <a
                @click.prevent="resendCode"
                href="#"
                class="font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 cursor-pointer"
                :class="{ 'opacity-50 cursor-not-allowed': resendLoading }"
              >
                {{ resendLoading ? "Sending..." : "Resend code" }}
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
import { useRouter, useRoute } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import AppInput from "@/components/ui/AppInput.vue";
import AppButton from "@/components/ui/AppButton.vue";

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();

// Get the user email from store
const userEmail = ref("");

const form = reactive({
  otp: "",
});

const errors = reactive({
  otp: "",
});

const loading = ref(false);
const resendLoading = ref(false);
const errorMessage = ref("");
const successMessage = ref("");

onMounted(() => {
  console.log("Auth store state:", {
    user: authStore.user,
    registrationEmail: authStore.registrationEmail,
    localstorage: {
      userEmail:
        localStorage.getItem("userEmail") || localStorage.getItem("tempEmail"),
      registrationEmail: localStorage.getItem("registrationEmail"),
    },
  });

  // Check multiple sources for the email
  console.log(authStore.tempEmail);
  if (authStore.registrationEmail || authStore.tempEmail) {
    userEmail.value = authStore.registrationEmail || authStore.tempEmail;
    console.log("Using registration email from store:", userEmail.value);
  } else {
    console.error("No email found in any location");
    errorMessage.value = "Email information missing. Please register first.";
    // setTimeout(() => {
    //   router.push("/register");
    // }, 3000);
  }
});

const verifyEmail = async () => {
  // Reset errors
  errors.otp = "";
  errorMessage.value = "";

  // Validate
  if (!form.otp) {
    errors.otp = "Verification code is required";
    return;
  }

  console.log("Submitting verification with:", {
    email: userEmail.value,
    otp: form.otp,
  });

  try {
    loading.value = true;
    const response = await authStore.verifyEmail({
      email: userEmail.value,
      otp: form.otp,
    });
    console.log("Verification success:", response);

    successMessage.value = "Email verified successfully!";

    // Only redirect if we have a successful response
    if (response && response.success) {
      setTimeout(() => {
        router.push("/");
      }, 2000);
    }
  } catch (error) {
    console.error("Verification error details:", {
      error,
      response: error.response,
      data: error.response?.data,
    });

    if (error.response?.data?.errors?.otp) {
      errors.otp = error.response.data.errors.otp[0];
    } else {
      errorMessage.value =
        error.response?.data?.message || "Invalid verification code";
      stop();
    }
  } finally {
    loading.value = false;
  }
};

// const resendCode = async () => {
//   if (resendLoading.value) return;

//   try {
//     resendLoading.value = true;
//     await authStore.sendVerificationEmail(userEmail.value);
//     successMessage.value = "Verification code sent successfully!";
//     errorMessage.value = "";
//   } catch (error) {
//     successMessage.value = "";
//     errorMessage.value =
//       error.response?.data?.message || "Failed to send verification code";
//   } finally {
//     resendLoading.value = false;
//   }
// };
</script>
