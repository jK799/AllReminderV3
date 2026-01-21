<template>
    <div class="max-w-md mx-auto bg-slate-900/40 border border-slate-800 rounded-2xl p-6">
      <h1 class="text-2xl font-semibold">Rejestracja</h1>
      <p class="text-slate-400 mt-1">Załóż konto w 30 sekund.</p>
  
      <form class="mt-6 space-y-4" @submit.prevent="submit">
        <div>
          <label class="text-sm text-slate-300">Imię</label>
          <input v-model="name" class="mt-1 w-full rounded-xl bg-slate-950/60 border border-slate-800 px-3 py-2 outline-none focus:ring-2 focus:ring-slate-500" />
        </div>
  
        <div>
          <label class="text-sm text-slate-300">Email</label>
          <input v-model="email" type="email" class="mt-1 w-full rounded-xl bg-slate-950/60 border border-slate-800 px-3 py-2 outline-none focus:ring-2 focus:ring-slate-500" />
        </div>
  
        <div>
          <label class="text-sm text-slate-300">Hasło</label>
          <input v-model="password" type="password" class="mt-1 w-full rounded-xl bg-slate-950/60 border border-slate-800 px-3 py-2 outline-none focus:ring-2 focus:ring-slate-500" />
        </div>
  
        <div>
          <label class="text-sm text-slate-300">Powtórz hasło</label>
          <input v-model="password_confirmation" type="password" class="mt-1 w-full rounded-xl bg-slate-950/60 border border-slate-800 px-3 py-2 outline-none focus:ring-2 focus:ring-slate-500" />
        </div>
  
        <p v-if="error" class="text-sm text-red-400">{{ error }}</p>
  
        <button class="w-full rounded-xl bg-slate-200 text-slate-950 py-2 font-semibold hover:bg-white">
          Zarejestruj
        </button>
  
        <p class="text-sm text-slate-400">
          Masz konto?
          <router-link class="text-slate-200 underline" to="/login">Zaloguj</router-link>
        </p>
      </form>
    </div>
  </template>
  
  <script setup>
  import { ref } from "vue";
  import { useRouter } from "vue-router";
  import api, { setToken } from "../services/api";
  
  const router = useRouter();
  const name = ref("");
  const email = ref("");
  const password = ref("");
  const password_confirmation = ref("");
  const error = ref("");
  
  async function submit() {
    error.value = "";
    try {
      const res = await api.post("/api/register", {
        name: name.value,
        email: email.value,
        password: password.value,
        password_confirmation: password_confirmation.value,
      });
  
      setToken(res.data.token);
      router.push("/dashboard");
    } catch (e) {
      error.value =
        e?.response?.data?.message ||
        "Nie udało się zarejestrować (sprawdź dane).";
    }
  }
  </script>
  