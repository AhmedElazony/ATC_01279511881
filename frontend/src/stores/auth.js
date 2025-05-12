// src/stores/auth.js
import { defineStore } from "pinia";
import { ref } from "vue";
import api from "@/services/api";

export const useAuthStore = defineStore("auth", () => {
  const user = ref(null);
  const token = ref(localStorage.getItem("token") || null);
  // Add a separate email ref for registration flow
  const registrationEmail = ref("");
  const tempEmail = ref("");

  // Load the token from localStorage
  if (token.value) {
    api.defaults.headers.common["Authorization"] = `Bearer ${token.value}`;
  }

  async function register(formData) {
    try {
      const response = await api.post("/auth/register", formData);
      console.log("Register response:", response.data);

      // Store the email for verification flow
      registrationEmail.value = formData.email;

      // Check if the response contains a token
      if (response.data && response.data.data.token) {
        // Store the token and user data
        token.value = response.data.data.token;
        localStorage.setItem("token", token.value);

        // Set the authorization header for future API requests
        api.defaults.headers.common["Authorization"] = `Bearer ${token.value}`;
      } else {
        console.log("No token received from registration API");
      }

      return response.data;
    } catch (error) {
      console.error("Registration error:", error);
      throw error;
    }
  }

  async function login(credentials) {
    const response = await api.post("/auth/login", credentials);

    if (response.data.data.token) {
      token.value = response.data.data.token;
      user.value = response.data.data.user;

      localStorage.setItem("token", token.value);
      api.defaults.headers.common["Authorization"] = `Bearer ${token.value}`;
    }

    return response.data;
  }

  async function logout() {
    if (token.value) {
      try {
        await api.delete("/auth/logout");
      } catch (error) {
        console.error("Error during logout:", error);
      }

      token.value = null;
      user.value = null;

      localStorage.removeItem("token");
      delete api.defaults.headers.common["Authorization"];
    }
  }

  async function verifyEmail(data) {
    try {
      const headers = {};
      if (token.value) {
        headers["Authorization"] = `Bearer ${token.value}`;
      }
      // Send request to backend
      const response = await api.post("/auth/verify-email", data, { headers });
      console.log("Verification API response:", response.data.data);

      // If we get a token back, save it
      if (response.data.data.token) {
        token.value = response.data.data.token;
        user.value = response.data.data.user;

        localStorage.setItem("token", token.value);
        api.defaults.headers.common["Authorization"] = `Bearer ${token.value}`;
      }

      // Clear registration email as it's no longer needed
      localStorage.removeItem("registrationEmail");
      registrationEmail.value = null;

      return response.data;
    } catch (error) {
      console.error("API verification error:", {
        status: error.response?.status,
        data: error.response?.data,
      });
      throw error;
    }
  }

  async function fetchUser() {
    try {
      const response = await api.get("/auth/user");
      user.value = response.data.data.user;
      return response.data;
    } catch (error) {
      console.error("Error fetching user:", error);
      throw error;
    }
  }

  async function sendVerificationEmail(email) {
    return await api.post("/auth/send-verification-email", { email });
  }

  async function forgotPassword(email) {
    const response = await api.post("/auth/forgot-password", { email });
    if (response.status === 200) {
      tempEmail.value = email;
    }

    return response;
  }

  async function resetPassword(data) {
    return await api.post("/auth/reset-password", data);
  }

  const isLoggedIn = () => !!token.value;

  return {
    user,
    token,
    registrationEmail,
    tempEmail,
    register,
    login,
    logout,
    verifyEmail,
    fetchUser,
    sendVerificationEmail,
    forgotPassword,
    resetPassword,
    isLoggedIn,
  };
});
