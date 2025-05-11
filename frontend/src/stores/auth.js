// src/stores/auth.js
import { defineStore } from "pinia";
import { ref } from "vue";
import api from "@/services/api";

export const useAuthStore = defineStore("auth", () => {
  const user = ref(null);
  const token = ref(localStorage.getItem("token") || null);

  // Load the token from localStorage
  if (token.value) {
    api.defaults.headers.common["Authorization"] = `Bearer ${token.value}`;
  }

  async function register(formData) {
    const response = await api.post("/auth/register", formData);

    if (response.data.token) {
      token.value = response.data.token;
      user.value = response.data.user;

      localStorage.setItem("token", token.value);
      api.defaults.headers.common["Authorization"] = `Bearer ${token.value}`;
    }

    return response.data;
  }

  async function login(credentials) {
    const response = await api.post("/auth/login", credentials);

    if (response.data.token) {
      token.value = response.data.token;
      user.value = response.data.user;

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

  async function fetchUser() {
    if (token.value) {
      try {
        const response = await api.get("/auth/user");
        user.value = response.data.user;
        return user.value;
      } catch (error) {
        logout();
        throw error;
      }
    }
    return null;
  }

  async function verifyEmail(data) {
    const response = await api.post("/auth/verify-email", data);

    if (response.data.token) {
      token.value = response.data.token;
      user.value = response.data.user;

      localStorage.setItem("token", token.value);
      api.defaults.headers.common["Authorization"] = `Bearer ${token.value}`;
    }

    return response.data;
  }

  async function sendVerificationEmail(email) {
    return await api.post("/auth/send-verification-email", { email });
  }

  async function forgotPassword(email) {
    return await api.post("/auth/forgot-password", { email });
  }

  async function resetPassword(data) {
    return await api.post("/auth/reset-password", data);
  }

  const isLoggedIn = () => !!token.value;

  return {
    user,
    token,
    register,
    login,
    logout,
    fetchUser,
    verifyEmail,
    sendVerificationEmail,
    forgotPassword,
    resetPassword,
    isLoggedIn,
  };
});
