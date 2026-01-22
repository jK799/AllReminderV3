<template>
  <div class="min-h-screen bg-slate-950 text-slate-100">
    <header class="border-b border-slate-800/60">
      <div class="mx-auto max-w-5xl px-6 py-4 flex items-center justify-between">
        <div class="font-semibold tracking-tight">AllReminderV3</div>

        <nav class="flex items-center gap-4 text-sm">
          <RouterLink class="hover:text-white text-slate-300" to="/dashboard">Dashboard</RouterLink>
          <RouterLink class="hover:text-white text-slate-300" to="/documents">Dokumenty</RouterLink>
          <RouterLink class="hover:text-white text-slate-300" to="/upload">Upload</RouterLink>

          <div class="w-px h-5 bg-slate-800/70 mx-1"></div>

          <!-- GUEST -->
          <template v-if="!isLoggedIn">
            <RouterLink class="hover:text-white text-slate-300" to="/login">Login</RouterLink>
            <RouterLink class="hover:text-white text-slate-300" to="/register">Rejestracja</RouterLink>
          </template>

          <!-- AUTH -->
          <template v-else>
            <span class="text-slate-200">{{ userName }}</span>
            <button
              class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 transition"
              @click="onLogout"
            >
              Wyloguj
            </button>
          </template>
        </nav>
      </div>
    </header>

    <main class="mx-auto max-w-5xl px-6 py-8">
      <slot />
    </main>
  </div>
</template>

<script setup>
import { computed } from "vue";
import { useRouter } from "vue-router";
import { useAuth } from "../composables/useAuth";

const router = useRouter();
const auth = useAuth();

const isLoggedIn = computed(() => auth.isLoggedIn());
const userName = computed(() => auth.user.value?.name ?? auth.user.value?.email ?? "Użytkownik");

async function onLogout() {
  await auth.logout();
  router.push("/login");
}
</script>
