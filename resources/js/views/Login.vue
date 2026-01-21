<template>
    <div class="max-w-md mx-auto">
      <h1 class="text-2xl font-semibold mb-6">Logowanie</h1>
  
      <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6 space-y-4">
        <div>
          <label class="block text-sm text-zinc-300 mb-1">E-mail</label>
          <input v-model="email" type="email"
            class="w-full px-3 py-2 rounded-lg bg-zinc-950 border border-zinc-800 focus:outline-none focus:ring-2 focus:ring-zinc-600" />
        </div>
  
        <div>
          <label class="block text-sm text-zinc-300 mb-1">Hasło</label>
          <input v-model="password" type="password"
            class="w-full px-3 py-2 rounded-lg bg-zinc-950 border border-zinc-800 focus:outline-none focus:ring-2 focus:ring-zinc-600" />
        </div>
  
        <button @click="login" :disabled="loading"
          class="w-full px-4 py-2 rounded-lg bg-white text-zinc-900 font-medium hover:bg-zinc-200 disabled:opacity-60">
          {{ loading ? "Logowanie..." : "Zaloguj" }}
        </button>
  
        <p v-if="error" class="text-sm text-red-400">{{ error }}</p>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref } from "vue";
  import { useRouter } from "vue-router";
  import { apiFetch, setToken } from "../api";
  
  const router = useRouter();
  const email = ref("");
  const password = ref("");
  const loading = ref(false);
  const error = ref("");
  
  async function login() {
    error.value = "";
    loading.value = true;
  
    try {
      const res = await apiFetch("/api/login", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email: email.value, password: password.value }),
      });
  
      const token = res?.token; // <- jeśli masz inne pole, podmienimy
      if (!token) throw new Error("Brak tokena w odpowiedzi z /api/login");
  
      setToken(token);
      router.push("/documents");
    } catch (e) {
      error.value = e.message || "Błąd logowania";
    } finally {
      loading.value = false;
    }
  }
  </script>
  