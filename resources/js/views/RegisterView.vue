<template>
    <div class="max-w-md mx-auto bg-slate-900/60 border border-slate-800 rounded-2xl p-6">
      <h1 class="text-xl font-semibold mb-1">Rejestracja</h1>
      <p class="text-slate-400 text-sm mb-6">Załóż konto</p>
  
      <form class="space-y-4" @submit.prevent="onSubmit">
        <div>
          <label class="text-sm text-slate-300">Imię</label>
          <input v-model="name" class="mt-1 w-full rounded-xl bg-slate-950 border border-slate-800 px-3 py-2" />
        </div>
  
        <div>
          <label class="text-sm text-slate-300">Email</label>
          <input v-model="email" type="email" class="mt-1 w-full rounded-xl bg-slate-950 border border-slate-800 px-3 py-2" />
        </div>
  
        <div>
          <label class="text-sm text-slate-300">Hasło</label>
          <input v-model="password" type="password" class="mt-1 w-full rounded-xl bg-slate-950 border border-slate-800 px-3 py-2" />
        </div>
  
        <div>
          <label class="text-sm text-slate-300">Powtórz hasło</label>
          <input v-model="password_confirmation" type="password" class="mt-1 w-full rounded-xl bg-slate-950 border border-slate-800 px-3 py-2" />
        </div>
  
        <button class="w-full rounded-xl bg-white text-slate-900 font-semibold py-2">
          Zarejestruj
        </button>
  
        <p class="text-sm text-slate-400">
          Masz konto?
          <RouterLink class="text-white underline" to="/login">Login</RouterLink>
        </p>
      </form>
    </div>
  </template>
  
  <script setup>
  import { ref } from "vue";
  import { useRouter } from "vue-router";
  import api from "../services/api";
  
  const router = useRouter();
  
  const name = ref("");
  const email = ref("");
  const password = ref("");
  const password_confirmation = ref("");
  
  async function onSubmit() {
    const res = await api.post("/auth/register", {
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: password_confirmation.value,
    });
  
    localStorage.setItem("token", res.data.token);
    router.push("/dashboard");
  }
  </script>
  