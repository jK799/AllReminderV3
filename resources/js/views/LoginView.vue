<template>
    <div class="max-w-md">
      <div class="bg-slate-900/40 border border-slate-800 rounded-2xl p-6">
        <h1 class="text-2xl font-semibold">Login</h1>
        <p class="text-slate-400 mt-1">Zaloguj się do aplikacji.</p>
  
        <form class="mt-6 space-y-4" @submit.prevent="submit">
          <div>
            <label class="text-sm text-slate-300">Email</label>
            <input v-model="email" type="email" class="mt-1 w-full rounded-xl bg-slate-950 border border-slate-800 px-3 py-2 outline-none" />
          </div>
  
          <div>
            <label class="text-sm text-slate-300">Hasło</label>
            <input v-model="password" type="password" class="mt-1 w-full rounded-xl bg-slate-950 border border-slate-800 px-3 py-2 outline-none" />
          </div>
  
          <p v-if="error" class="text-sm text-red-400">{{ error }}</p>
  
          <button class="w-full rounded-xl bg-slate-200 text-slate-900 py-2 font-medium hover:opacity-90">
            Zaloguj
          </button>
        </form>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref } from "vue";
  import { useRouter } from "vue-router";
  import { useAuth } from "../composables/useAuth";
  
  const router = useRouter();
  const auth = useAuth();
  
  const email = ref("");
  const password = ref("");
  const error = ref("");
  
  async function submit() {
    error.value = "";
    try {
      await auth.login(email.value, password.value);
      router.push({ name: "dashboard" });
    } catch (e) {
      error.value = e?.response?.data?.message ?? e?.message ?? "Błąd logowania";
    }
  }
  </script>
  