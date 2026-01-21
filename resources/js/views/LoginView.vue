<template>
    <div class="mx-auto max-w-md">
      <h1 class="text-2xl font-semibold">Logowanie</h1>
      <p class="mt-1 text-sm text-zinc-400">
        Logujemy się tokenem Bearer. Po zalogowaniu zapisuję token w localStorage.
      </p>
  
      <div class="mt-6 rounded-2xl border border-zinc-800 bg-zinc-900 p-4">
        <label class="block text-sm text-zinc-300">Email</label>
        <input v-model="email" class="inp mt-1" type="email" />
  
        <label class="mt-3 block text-sm text-zinc-300">Hasło</label>
        <input v-model="password" class="inp mt-1" type="password" />
  
        <button class="mt-4 w-full rounded-xl bg-white px-3 py-2 text-sm font-semibold text-black hover:bg-zinc-200"
                @click="login" :disabled="loading">
          {{ loading ? "Loguję..." : "Zaloguj" }}
        </button>
  
        <p v-if="error" class="mt-3 text-sm text-red-400">{{ error }}</p>
  
        <div class="mt-4 text-xs text-zinc-400">
          <div class="font-semibold text-zinc-300">Ważne</div>
          <div>Ten ekran zakłada endpoint: <code>/api/auth/login</code> (podam Ci go w następnym kroku).</div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref } from "vue";
  import { useRouter } from "vue-router";
  import api, { setToken } from "../api";
  
  const router = useRouter();
  
  const email = ref("");
  const password = ref("");
  const loading = ref(false);
  const error = ref("");
  
  async function login() {
    error.value = "";
    loading.value = true;
  
    try {
      const res = await api.post("/auth/login", {
        email: email.value,
        password: password.value,
      });
  
      // oczekujemy: { token: "..." }
      setToken(res.data.token);
      router.push({ name: "dashboard" });
    } catch (e) {
      error.value =
        e?.response?.data?.message || "Nie udało się zalogować (sprawdź API /auth/login).";
    } finally {
      loading.value = false;
    }
  }
  </script>
  
  <style scoped>
  .inp {
    @apply w-full rounded-xl border border-zinc-800 bg-zinc-950 px-3 py-2 text-sm outline-none focus:border-zinc-600;
  }
  code {
    @apply rounded bg-zinc-950 px-1 py-0.5;
  }
  </style>
  