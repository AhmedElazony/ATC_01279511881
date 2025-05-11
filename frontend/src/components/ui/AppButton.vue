<template>
  <button
    :type="type"
    :disabled="disabled || loading"
    :class="[
      'inline-flex items-center justify-center px-4 py-2 border rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors',
      variantClasses,
      sizeClasses,
      disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer',
    ]"
  >
    <span v-if="loading" class="mr-2">
      <svg
        class="animate-spin h-4 w-4"
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
      >
        <circle
          class="opacity-25"
          cx="12"
          cy="12"
          r="10"
          stroke="currentColor"
          stroke-width="4"
        ></circle>
        <path
          class="opacity-75"
          fill="currentColor"
          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
        ></path>
      </svg>
    </span>
    <slot></slot>
  </button>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
  type: {
    type: String,
    default: "button",
  },
  variant: {
    type: String,
    default: "primary",
    validator: (value) =>
      [
        "primary",
        "secondary",
        "success",
        "danger",
        "warning",
        "info",
        "light",
        "dark",
      ].includes(value),
  },
  size: {
    type: String,
    default: "md",
    validator: (value) => ["sm", "md", "lg"].includes(value),
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  loading: {
    type: Boolean,
    default: false,
  },
});

const variantClasses = computed(() => {
  switch (props.variant) {
    case "primary":
      return "bg-indigo-600 border-transparent text-white hover:bg-indigo-700 focus:ring-indigo-500";
    case "secondary":
      return "bg-gray-600 border-transparent text-white hover:bg-gray-700 focus:ring-gray-500";
    case "success":
      return "bg-emerald-600 border-transparent text-white hover:bg-emerald-700 focus:ring-emerald-500";
    case "danger":
      return "bg-red-600 border-transparent text-white hover:bg-red-700 focus:ring-red-500";
    case "warning":
      return "bg-amber-500 border-transparent text-white hover:bg-amber-600 focus:ring-amber-500";
    case "info":
      return "bg-blue-500 border-transparent text-white hover:bg-blue-600 focus:ring-blue-500";
    case "light":
      return "bg-gray-100 border-gray-200 text-gray-700 hover:bg-gray-200 focus:ring-gray-500";
    case "dark":
      return "bg-gray-800 border-transparent text-white hover:bg-gray-900 focus:ring-gray-700";
    default:
      return "bg-indigo-600 border-transparent text-white hover:bg-indigo-700 focus:ring-indigo-500";
  }
});

const sizeClasses = computed(() => {
  switch (props.size) {
    case "sm":
      return "px-3 py-1.5 text-xs";
    case "md":
      return "px-4 py-2 text-sm";
    case "lg":
      return "px-6 py-3 text-base";
    default:
      return "px-4 py-2 text-sm";
  }
});
</script>
