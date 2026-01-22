<template>
    <div class="max-w-md mx-auto rounded-2xl border border-slate-800/60 bg-slate-900/40 p-6">
      <h1 class="text-2xl font-semibold">Rejestracja</h1>
  
      <form class="mt-6 space-y-4" @submit.prevent="onSubmit">
        <div>
          <label class="text-sm text-slate-300">Imię</label>
          <input v-model="name" required class="mt-1 w-full rounded-xl bg-slate-950 border border-slate-800 px-3 py-2 outline-none focus:border-slate-600" />
        </div>
  
        <div>
          <label class="text-sm text-slate-300">Email</label>
          <input v-model="email" type="email" required class="mt-1 w-full rounded-xl bg-slate-950 border border-slate-800 px-3 py-2 outline-none focus:border-slate-600" />
        </div>
  
        <div>
          <label class="text-sm text-slate-300">Hasło</label>
          <input v-model="password" type="password" required class="mt-1 w-full rounded-xl bg-slate-950 border border-slate-800 px-3 py-2 outline-none focus:border-slate-600" />
        </div>
  
        <div>
          <label class="text-sm text-slate-300">Powtórz hasło</label>
          <input v-model="password_confirmation" type="password" required class="mt-1 w-full rounded-xl bg-slate-950 border border-slate-800 px-3 py-2 outline-none focus:border-slate-600" />
        </div>
  
        <p v-if="error" class="text-sm text-red-300">{{ error }}</p>
  
        <button class="w-full rounded-xl bg-indigo-600 hover:bg-indigo-500 transition px-4 py-2 font-medium">
          Zarejestruj
        </button>
      </form>
  
      <p class="text-slate-400 text-sm mt-4">
        Masz konto?
        <RouterLink to="/login" class="text-slate-200 hover:text-white underline">Login</RouterLink>
      </p>
    </div>
  </template>
  
  <script setup>
  import { ref } from "vue";
  import { useRouter } from "vue-router";
  import { useAuth } from "../composables/useAuth";
  
  const router = useRouter();
  const auth = useAuth();
  
  const name = ref("");
  const email = ref("");
  const password = ref("");
  const password_confirmation = ref("");
  const error = ref("");
  
  async function onSubmit() {
    error.value = "";
    try {
      await auth.register({
        name: name.value,
        email: email.value,
        password: password.value,
        password_confirmation: password_confirmation.value,
      });
      router.push("/dashboard");
    } catch (e) {
      error.value = "Nie udało się zarejestrować (sprawdź dane).";
    }
  }
  </script>
  