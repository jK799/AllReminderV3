<template>
    <div class="max-w-md mx-auto rounded-2xl border border-slate-800/60 bg-slate-900/40 p-6">
      <h1 class="text-2xl font-semibold">Login</h1>
  
      <form class="mt-6 space-y-4" @submit.prevent="onSubmit">
        <div>
          <label class="text-sm text-slate-300">Email</label>
          <input v-model="email" type="email" required class="mt-1 w-full rounded-xl bg-slate-950 border border-slate-800 px-3 py-2 outline-none focus:border-slate-600" />
        </div>
  
        <div>
          <label class="text-sm text-slate-300">Hasło</label>
          <input v-model="password" type="password" required class="mt-1 w-full rounded-xl bg-slate-950 border border-slate-800 px-3 py-2 outline-none focus:border-slate-600" />
        </div>
  
        <p v-if="error" class="text-sm text-red-300">{{ error }}</p>
  
        <button class="w-full rounded-xl bg-indigo-600 hover:bg-indigo-500 transition px-4 py-2 font-medium">
          Zaloguj
        </button>
      </form>
  
      <p class="text-slate-400 text-sm mt-4">
        Nie masz konta?
        <RouterLink to="/register" class="text-slate-200 hover:text-white underline">Rejestracja</RouterLink>
      </p>
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
  
  async function onSubmit() {
    error.value = "";
    try {
      await auth.login(email.value, password.value);
      router.push("/dashboard");
    } catch (e) {
      error.value = "Nieprawidłowy email lub hasło.";
    }
  }
  </script>
  