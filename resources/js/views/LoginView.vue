<template>
    <div class="max-w-md mx-auto bg-slate-900/40 border border-slate-800 rounded-2xl p-6">
      <h1 class="text-2xl font-semibold">Login</h1>
      <p class="text-slate-400 mt-1">Zaloguj się, żeby korzystać z aplikacji.</p>
  
      <form class="mt-6 space-y-4" @submit.prevent="submit">
        <div>
          <label class="text-sm text-slate-300">Email</label>
          <input v-model="email" type="email"
            class="mt-1 w-full rounded-xl bg-slate-950/60 border border-slate-800 px-3 py-2 outline-none focus:ring-2 focus:ring-slate-500" />
        </div>
  
        <div>
          <label class="text-sm text-slate-300">Hasło</label>
          <input v-model="password" type="password"
            class="mt-1 w-full rounded-xl bg-slate-950/60 border border-slate-800 px-3 py-2 outline-none focus:ring-2 focus:ring-slate-500" />
        </div>
  
        <p v-if="error" class="text-sm text-red-400">{{ error }}</p>
  
        <button class="w-full rounded-xl bg-slate-200 text-slate-950 py-2 font-semibold hover:bg-white">
          Zaloguj
        </button>
  
        <p class="text-sm text-slate-400">
          Nie masz konta?
          <router-link class="text-slate-200 underline" to="/register">Zarejestruj</router-link>
        </p>
      </form>
    </div>
  </template>
  
  <script setup>
  import { ref } from "vue";
  import { useRouter } from "vue-router";
  import { login } from "../services/api";
  
  const router = useRouter();
  const email = ref("");
  const password = ref("");
  const error = ref("");
  
  async function submit() {
    error.value = "";
    try {
      await login(email.value, password.value);
      router.push("/dashboard");
    } catch (e) {
      error.value =
        e?.response?.data?.message ||
        e?.response?.data?.errors?.email?.[0] ||
        "Nie udało się zalogować.";
    }
  }
  </script>
  