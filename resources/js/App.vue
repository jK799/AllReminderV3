<template>
    <div class="min-h-screen bg-zinc-950 text-zinc-100">
      <header class="border-b border-zinc-800 bg-zinc-950/80 backdrop-blur">
        <div class="mx-auto max-w-5xl px-4 py-4 flex items-center justify-between">
          <div class="font-semibold">AllReminder</div>
  
          <nav class="flex items-center gap-2">
            <RouterLink
              v-if="isAuthed"
              to="/documents"
              class="px-3 py-2 rounded-lg hover:bg-zinc-800"
              >Dokumenty</RouterLink
            >
            <RouterLink
              v-if="isAuthed"
              to="/upload"
              class="px-3 py-2 rounded-lg hover:bg-zinc-800"
              >Upload</RouterLink
            >
  
            <button
              v-if="isAuthed"
              @click="logout"
              class="px-3 py-2 rounded-lg bg-zinc-800 hover:bg-zinc-700"
            >
              Wyloguj
            </button>
          </nav>
        </div>
      </header>
  
      <main class="mx-auto max-w-5xl px-4 py-8">
        <RouterView />
      </main>
    </div>
  </template>
  
  <script setup>
  import { computed } from "vue";
  import { useRouter } from "vue-router";
  import { clearToken, getToken } from "./api";
  
  const router = useRouter();
  const isAuthed = computed(() => !!getToken());
  
  function logout() {
    clearToken();
    router.push("/login");
  }
  </script>
  