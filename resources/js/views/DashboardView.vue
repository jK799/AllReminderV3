<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-start justify-between gap-4">
      <div>
        <h1 class="text-2xl font-semibold tracking-tight">Dashboard</h1>
        <p class="text-slate-400 mt-1">
          Podgląd statystyk i najbliższych terminów.
        </p>
      </div>

      <div class="flex items-center gap-2">
        <button class="btn" @click="refresh" :disabled="loading">
          {{ loading ? "Ładowanie..." : "Odśwież" }}
        </button>
      </div>
    </div>

    <!-- Error -->
    <div v-if="error" class="rounded-2xl border border-red-500/30 bg-red-500/10 p-4">
      <div class="font-medium text-red-200">Błąd</div>
      <div class="text-sm text-red-200/80 mt-1">{{ error }}</div>
      <button class="btn btn-red mt-3" @click="refresh">Spróbuj ponownie</button>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
      <StatCard title="Urządzenia" :value="stats.devices" :loading="loading" />
      <StatCard title="Pojazdy" :value="stats.vehicles" :loading="loading" />
      <StatCard title="Przypomnienia (aktywne)" :value="stats.remindersActive" :loading="loading" />
      <StatCard title="Serwisy (aktywne)" :value="stats.servicesActive" :loading="loading" />
      <StatCard title="Dokumenty" :value="stats.documents" :loading="loading" />
    </div>

    <!-- Lists -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Upcoming reminders -->
      <div class="card">
        <div class="card-header">
          <div>
            <div class="card-title">Najbliższe przypomnienia</div>
            <div class="card-subtitle">Top 5, tylko aktywne i nieukończone</div>
          </div>
          <span class="pill">{{ upcomingReminders.length }}</span>
        </div>

        <div class="card-body">
          <div v-if="loading" class="text-slate-400 text-sm">Ładowanie…</div>

          <div v-else-if="upcomingReminders.length === 0" class="text-slate-400 text-sm">
            Brak nadchodzących przypomnień 🎉
          </div>

          <ul v-else class="divide-y divide-slate-800/60">
            <li v-for="r in upcomingReminders" :key="r.id" class="py-3 flex items-start justify-between gap-3">
              <div class="min-w-0">
                <div class="font-medium truncate">
                  {{ r.title || "Bez tytułu" }}
                </div>
                <div class="text-sm text-slate-400 truncate">
                  {{ r.description || "Brak opisu" }}
                </div>

                <div class="mt-2 flex flex-wrap gap-2">
                  <span class="tag">
                    Termin: {{ formatDateTime(getReminderDate(r)) }}
                  </span>

                  <span v-if="r.vehicle_id" class="tag">Pojazd #{{ r.vehicle_id }}</span>
                  <span v-else-if="r.device_id" class="tag">Urządzenie #{{ r.device_id }}</span>
                  <span v-else class="tag">Ogólne</span>
                </div>
              </div>

              <div class="text-right shrink-0">
                <div class="text-sm text-slate-300">
                  {{ humanDiff(getReminderDate(r)) }}
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>

      <!-- Upcoming services -->
      <div class="card">
        <div class="card-header">
          <div>
            <div class="card-title">Najbliższe serwisy</div>
            <div class="card-subtitle">Top 5, tylko aktywne</div>
          </div>
          <span class="pill">{{ upcomingServices.length }}</span>
        </div>

        <div class="card-body">
          <div v-if="loading" class="text-slate-400 text-sm">Ładowanie…</div>

          <div v-else-if="upcomingServices.length === 0" class="text-slate-400 text-sm">
            Brak nadchodzących serwisów.
          </div>

          <ul v-else class="divide-y divide-slate-800/60">
            <li v-for="s in upcomingServices" :key="s.id" class="py-3 flex items-start justify-between gap-3">
              <div class="min-w-0">
                <div class="font-medium truncate">
                  {{ s.title || "Serwis" }}
                </div>
                <div class="text-sm text-slate-400 truncate">
                  {{ s.description || "Brak opisu" }}
                </div>

                <div class="mt-2 flex flex-wrap gap-2">
                  <span class="tag">
                    Następny termin: {{ formatDate(getServiceDate(s)) }}
                  </span>

                  <span v-if="s.vehicle_id" class="tag">Pojazd #{{ s.vehicle_id }}</span>
                  <span v-else-if="s.device_id" class="tag">Urządzenie #{{ s.device_id }}</span>
                  <span v-else class="tag">Ogólne</span>

                  <span v-if="s.interval_value && s.interval_unit" class="tag">
                    Co {{ s.interval_value }} {{ s.interval_unit }}
                  </span>
                </div>
              </div>

              <div class="text-right shrink-0">
                <div class="text-sm text-slate-300">
                  {{ humanDiff(getServiceDate(s)) }}
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from "vue";
import api from "../services/api";

// ---------- UI helpers ----------
function toDate(x) {
  if (!x) return null;
  const d = new Date(x);
  return isNaN(d.getTime()) ? null : d;
}
function formatDateTime(x) {
  const d = toDate(x);
  if (!d) return "—";
  return new Intl.DateTimeFormat("pl-PL", {
    year: "numeric",
    month: "2-digit",
    day: "2-digit",
    hour: "2-digit",
    minute: "2-digit",
  }).format(d);
}
function formatDate(x) {
  const d = toDate(x);
  if (!d) return "—";
  return new Intl.DateTimeFormat("pl-PL", {
    year: "numeric",
    month: "2-digit",
    day: "2-digit",
  }).format(d);
}
function humanDiff(x) {
  const d = toDate(x);
  if (!d) return "";
  const now = new Date();
  const diffMs = d.getTime() - now.getTime();
  const abs = Math.abs(diffMs);

  const minutes = Math.round(abs / 60000);
  const hours = Math.round(abs / 3600000);
  const days = Math.round(abs / 86400000);

  const future = diffMs >= 0;

  if (minutes < 60) return future ? `za ${minutes} min` : `${minutes} min temu`;
  if (hours < 48) return future ? `za ${hours} h` : `${hours} h temu`;
  return future ? `za ${days} dni` : `${days} dni temu`;
}

// reminder: prefer remind_at, fallback due_at
function getReminderDate(r) {
  return r?.remind_at ?? r?.due_at ?? null;
}
function getServiceDate(s) {
  return s?.next_due_at ?? null;
}

// ---------- state ----------
const loading = ref(false);
const error = ref("");

const stats = reactive({
  devices: 0,
  vehicles: 0,
  remindersActive: 0,
  servicesActive: 0,
  documents: 0,
});

const reminders = ref([]);
const services = ref([]);

const upcomingReminders = computed(() => {
  const list = (reminders.value || [])
    .filter((r) => {
      // tylko aktywne + nieukończone
      const active = r?.is_active === 1 || r?.is_active === true || r?.is_active === "1";
      const notDone = !r?.completed_at;
      return active && notDone;
    })
    .map((r) => ({ ...r, __date: toDate(getReminderDate(r)) }))
    .filter((r) => r.__date)
    .sort((a, b) => a.__date - b.__date)
    .slice(0, 5);

  return list;
});

const upcomingServices = computed(() => {
  const list = (services.value || [])
    .filter((s) => s?.is_active === 1 || s?.is_active === true || s?.is_active === "1")
    .map((s) => ({ ...s, __date: toDate(getServiceDate(s)) }))
    .filter((s) => s.__date)
    .sort((a, b) => a.__date - b.__date)
    .slice(0, 5);

  return list;
});

// ---------- API ----------
async function fetchCount(path) {
  const res = await api.get(path);
  // zakładamy, że API zwraca tablicę (Twoje resource index najpewniej tak robi)
  return Array.isArray(res.data) ? res.data.length : (res.data?.data?.length ?? 0);
}

async function fetchList(path) {
  const res = await api.get(path);
  return Array.isArray(res.data) ? res.data : (res.data?.data ?? []);
}

async function refresh() {
  loading.value = true;
  error.value = "";

  try {
    // równolegle, żeby było szybciej
    const [
      devicesCount,
      vehiclesCount,
      docsCount,
      remindersList,
      servicesList,
    ] = await Promise.all([
      fetchCount("/api/devices"),
      fetchCount("/api/vehicles"),
      fetchCount("/api/documents"),
      fetchList("/api/reminders"),
      fetchList("/api/services"),
    ]);

    stats.devices = devicesCount;
    stats.vehicles = vehiclesCount;
    stats.documents = docsCount;

    reminders.value = remindersList;
    services.value = servicesList;

    stats.remindersActive = remindersList.filter((r) => (r?.is_active == 1 || r?.is_active === true || r?.is_active === "1") && !r?.completed_at).length;
    stats.servicesActive = servicesList.filter((s) => (s?.is_active == 1 || s?.is_active === true || s?.is_active === "1")).length;
  } catch (e) {
    error.value =
      e?.response?.data?.message ||
      e?.message ||
      "Nie udało się pobrać danych do dashboardu.";
  } finally {
    loading.value = false;
  }
}

onMounted(refresh);

// ---------- inline component ----------
const StatCard = {
  props: {
    title: { type: String, required: true },
    value: { type: Number, default: 0 },
    loading: { type: Boolean, default: false },
  },
  template: `
    <div class="rounded-2xl border border-slate-800/60 bg-slate-900/30 p-4">
      <div class="text-sm text-slate-400">{{ title }}</div>
      <div class="mt-2 text-3xl font-semibold tracking-tight">
        <span v-if="loading" class="inline-block h-8 w-16 animate-pulse rounded bg-slate-800/70"></span>
        <span v-else>{{ value }}</span>
      </div>
    </div>
  `,
};
</script>

<style scoped>
.card {
  @apply rounded-2xl border border-slate-800/60 bg-slate-900/30 overflow-hidden;
}
.card-header {
  @apply p-4 border-b border-slate-800/60 flex items-start justify-between gap-4;
}
.card-title {
  @apply text-base font-semibold;
}
.card-subtitle {
  @apply text-sm text-slate-400 mt-1;
}
.card-body {
  @apply p-4;
}
.btn {
  @apply px-3 py-2 rounded-xl border border-slate-800/60 bg-slate-900/40 hover:bg-slate-900/70 transition text-sm;
}
.btn:disabled {
  @apply opacity-60 cursor-not-allowed;
}
.btn-red {
  @apply border-red-500/30 bg-red-500/10 hover:bg-red-500/15;
}
.tag {
  @apply inline-flex items-center rounded-full border border-slate-800/70 bg-slate-900/50 px-2.5 py-1 text-xs text-slate-200;
}
.pill {
  @apply inline-flex items-center rounded-full border border-slate-800/70 bg-slate-900/50 px-2.5 py-1 text-xs text-slate-200;
}
.mono {
  @apply font-mono text-slate-200;
}
</style>
