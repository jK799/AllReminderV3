<template>
<<<<<<< HEAD
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
              <span class="text-slate-200">
                {{ userName }}
              </span>
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
    router.push({ name: "login" });
  }
  </script>
  
=======
  <div class="min-h-screen bg-slate-950 text-slate-100">
    <header class="border-b border-slate-800/80">
      <div class="mx-auto max-w-6xl px-4 py-4 flex items-center justify-between">
        <RouterLink to="/app" class="text-lg font-semibold tracking-wide">
          AllReminderV3
        </RouterLink>

        <nav class="flex items-center gap-2">
          <RouterLink class="btn" to="/app">Dashboard</RouterLink>
          <RouterLink class="btn" to="/app/documents">Dokumenty</RouterLink>
          <RouterLink class="btn" to="/app/upload">Upload</RouterLink>

          <div v-if="isLogged" class="flex items-center gap-2">
            <span class="text-sm text-slate-300 hidden sm:inline">
              {{ userLabel }}
            </span>
            <button class="btn btn-red" @click="handleLogout">Wyloguj</button>
          </div>

          <RouterLink v-else class="btn" to="/app/login">Login</RouterLink>
        </nav>
      </div>
    </header>

    <main class="mx-auto max-w-6xl px-4 py-8">
      <RouterView />
    </main>
  </div>
</template>

<script setup>
import { computed, onMounted } from "vue";
import { useRouter } from "vue-router";
import { useAuth } from "../composables/useAuth";

const router = useRouter();
const { isLogged, user, bootstrapMe, logout } = useAuth();

const userLabel = computed(() => {
  if (!user.value) return "";
  // Priorytet: name, potem email
  return user.value.name || user.value.email || "Użytkownik";
});

// po refreshu dociągnij /api/me jeśli jest token
onMounted(async () => {
  await bootstrapMe();
});

async function handleLogout() {
  await logout();
  router.push("/app/login");
}
</script>

<style scoped>
.btn {
  @apply px-3 py-2 rounded-xl border border-slate-700/60 bg-slate-900/30 hover:bg-slate-900/60 transition text-sm;
}
.btn-red {
  @apply border-red-500/40 bg-red-950/30 hover:bg-red-950/60;
}
</style>
>>>>>>> 180dcfd (frontend: show logged user in navbar + persist session via localStorage)
