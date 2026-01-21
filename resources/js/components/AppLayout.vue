<template>
    <div class="min-h-screen bg-zinc-950 text-zinc-100">
      <header class="border-b border-zinc-800 bg-zinc-950/80 backdrop-blur">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3">
          <div class="flex items-center gap-3">
            <div class="h-9 w-9 rounded-xl bg-zinc-800"></div>
            <div>
              <div class="text-sm font-semibold">AllReminderV3</div>
              <div class="text-xs text-zinc-400">Vue 3 + Vite + Tailwind</div>
            </div>
          </div>
  
          <nav class="flex items-center gap-2">
            <RouterLink class="btn" to="/app">Dashboard</RouterLink>
            <RouterLink class="btn" to="/app/documents">Dokumenty</RouterLink>
            <RouterLink class="btn" to="/app/upload">Upload</RouterLink>
  
            <button v-if="isLogged" class="btn btn-red" @click="logout">
              Wyloguj
            </button>
            <RouterLink v-else class="btn" to="/app/login">Login</RouterLink>
          </nav>
        </div>
      </header>
  
      <main class="mx-auto max-w-6xl px-4 py-6">
        <RouterView />
      </main>
    </div>
  </template>
  
  <script setup>
  import { computed } from "vue";
  import { useRouter } from "vue-router";
  import { getToken, setToken } from "../api";
  
  const router = useRouter();
  const isLogged = computed(() => !!getToken());
  
  function logout() {
    setToken(null);
    router.push({ name: "login" });
  }
  </script>
  
  <style scoped>
  .btn {
    @apply rounded-xl border border-zinc-800 bg-zinc-900 px-3 py-2 text-sm hover:bg-zinc-800;
  }
  .btn-red {
    @apply border-red-900/60 bg-red-950/40 hover:bg-red-900/40;
  }
  </style>
  