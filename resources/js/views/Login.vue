<template>
    <div class="min-h-screen flex items-center justify-center p-6">
      <div class="w-full max-w-md bg-gray-900 border border-gray-800 rounded-2xl p-6">
        <h1 class="text-xl font-semibold">Logowanie</h1>
        <p class="text-sm text-gray-400 mt-1">Zaloguj się, aby przejść do dokumentów.</p>
  
        <div class="mt-6 space-y-3">
          <input
            v-model="email"
            type="email"
            placeholder="Email"
            class="w-full px-4 py-3 rounded-xl bg-gray-950 border border-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-600"
          />
          <input
            v-model="password"
            type="password"
            placeholder="Hasło"
            class="w-full px-4 py-3 rounded-xl bg-gray-950 border border-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-600"
          />
  
          <button
            @click="submit"
            :disabled="loading"
            class="w-full py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 disabled:opacity-60 font-medium"
          >
            {{ loading ? "Logowanie..." : "Zaloguj" }}
          </button>
  
          <p v-if="error" class="text-sm text-red-400">{{ error }}</p>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref } from "vue";
  import { useRouter } from "vue-router";
  import api from "../lib/api";
  
  const router = useRouter();
  const email = ref("");
  const password = ref("");
  const loading = ref(false);
  const error = ref("");
  
  async function submit() {
    error.value = "";
    loading.value = true;
  
    try {
      const res = await api.post("/api/login", {
        email: email.value,
        password: password.value,
      });
  
      const token = res?.data?.token;
      if (!token) throw new Error("Brak tokena w odpowiedzi.");
  
      localStorage.setItem("token", token);
      await router.push({ name: "documents" });
    } catch (e) {
      error.value = e?.response?.data?.message ?? e?.message ?? "Błąd logowania";
    } finally {
      loading.value = false;
    }
  }
  </script>
  