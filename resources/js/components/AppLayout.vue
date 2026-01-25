<template>
  <div class="min-h-screen bg-slate-950 text-slate-100">
    <!-- background glow -->
    <div class="pointer-events-none fixed inset-0 -z-10">
      <div class="absolute -top-40 left-1/2 h-[520px] w-[920px] -translate-x-1/2 rounded-full bg-indigo-500/10 blur-[120px]"></div>
      <div class="absolute -bottom-40 left-1/3 h-[420px] w-[820px] rounded-full bg-cyan-500/10 blur-[120px]"></div>
    </div>

    <header class="sticky top-0 z-20 border-b border-slate-800/60 bg-slate-950/70 backdrop-blur">
      <div class="mx-auto max-w-6xl px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="h-9 w-9 rounded-xl bg-white/5 border border-slate-800/60 flex items-center justify-center">
            <span class="text-sm font-semibold">AR</span>
          </div>
          <div>
            <div class="font-semibold leading-tight">AllReminderV3</div>
            <div class="text-xs text-slate-400 -mt-0.5">Panel użytkownika</div>
          </div>
        </div>

        <nav class="flex items-center gap-2 text-sm">
          <RouterLink class="nav-link" to="/dashboard">Dashboard</RouterLink>
          <RouterLink class="nav-link" to="/vehicles">Pojazdy</RouterLink>
          <RouterLink class="nav-link" to="/documents">Dokumenty</RouterLink>
          <RouterLink class="nav-link" to="/upload">Upload</RouterLink>

          <div class="mx-2 hidden sm:block h-6 w-px bg-slate-800/70"></div>

          <template v-if="!isLoggedIn">
            <RouterLink class="nav-link" to="/login">Login</RouterLink>
            <RouterLink class="nav-link" to="/register">Rejestracja</RouterLink>
          </template>

          <template v-else>
            <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white/5 border border-slate-800/60">
              <div class="h-6 w-6 rounded-lg bg-white/10 flex items-center justify-center text-xs">👤</div>
              <span class="text-slate-200 font-medium">{{ userName }}</span>
            </div>

            <button class="btn btn-danger" @click="onLogout">
              Wyloguj
            </button>
          </template>
        </nav>
      </div>
    </header>

    <main class="mx-auto max-w-6xl px-6 py-8">
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
  router.push({ name: "login" });
}
</script>

<style scoped>
.nav-link {
  @apply px-3 py-1.5 rounded-xl text-slate-300 hover:text-white hover:bg-white/5 transition border border-transparent;
}
.nav-link.router-link-active {
  @apply bg-white/5 border-slate-800/60 text-white;
}
</style>
