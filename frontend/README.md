# Frontend Documentation

## Overview

This frontend is a **Vue.js 3** application using **Vue Router**, **Pinia** for state management, and **Tailwind CSS** for styling. It communicates with the **Laravel** backend API via **Axios**.

---

## Prerequisites

- Node.js 23 or higher
- npm

---

## Directory Structure

```plaintext
frontend/
├── public/          // Static assets
├── src/
│   ├── assets/      // CSS, images, etc.
│   ├── components/  // Reusable Vue components
│   ├── router/      // Vue Router configurations
│   ├── services/    // With the api.js file
│   ├── store/       // Pinia state management stores
│   ├── utils/       // Some utils functions.
│   ├── views/       // The Application views.
│   ├── App.vue      // Main root component
│   └── main.js      // Application entry file
│   └── styles.css   // Application main css file.
└── ...
```

---

## Building for Production

```bash
npm run build
```

This creates an optimized production build in the `dist/` folder, ready for deployment to a static hosting service or behind a custom server.

---

## Troubleshooting

- If Tailwind styles aren’t applied, ensure your `postcss.config.js` points to `tailwindcss` and `autoprefixer`.
- If you receive network errors, verify the `VITE_API_URL` or confirm backend availability.
