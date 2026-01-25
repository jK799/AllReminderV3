<template>
  <div class="space-y-8">
    <!-- HEADER -->
    <section class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <h1 class="text-2xl font-semibold tracking-tight">Dashboard</h1>
        <p class="text-slate-400 mt-1">Przegląd statystyk i skrótów.</p>
      </div>

      <div class="flex gap-2">
        <button class="btn" @click="refresh" :disabled="loading">
          {{ loading ? "Ładuje..." : "Odśwież" }}
        </button>
        <RouterLink class="btn btn-soft" to="/upload">+ Upload</RouterLink>
      </div>
    </section>

    <div v-if="error" class="card border-red-500/20 bg-red-500/5">
      <div class="card-body text-sm text-red-200">
        {{ error }}
      </div>
    </div>

    <!-- STATS -->
    <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
      <StatCard label="Pojazdy" :value="stats.vehicles" icon="🚗" />
      <StatCard label="Urządzenia" :value="stats.devices" icon="🛠️" />
      <StatCard label="Przypomnienia aktywne" :value="stats.activeReminders" icon="⏰" />
      <StatCard label="Dokumenty" :value="stats.documents" icon="📄" />
    </section>

    <!-- MAIN GRID -->
    <section class="grid gap-6 lg:grid-cols-2">
      <!-- REMINDERS -->
      <div class="card">
        <div class="card-head">
          <div>
            <h2 class="card-title">Najbliższe przypomnienia</h2>
            <p class="card-subtitle">Sortowane po terminie</p>
          </div>
          <span class="pill">{{ nearestReminders.length }}</span>
        </div>

        <div class="card-body">
          <EmptyState v-if="nearestReminders.length === 0" text="Brak przypomnień." />

          <div v-else class="space-y-3">
            <div v-for="r in nearestReminders" :key="r.id" class="item">
              <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                  <div class="font-semibold truncate">{{ r.title }}</div>
                  <div class="text-sm text-slate-400 truncate">{{ r.description || "—" }}</div>
                </div>

                <span class="badge" :class="r.is_active ? 'badge-ok' : 'badge-off'">
                  {{ r.is_active ? "Aktywne" : "Nieaktywne" }}
                </span>
              </div>

              <div class="mt-3 flex flex-wrap items-center gap-2">
                <span class="tag tag-soft">⏰ {{ formatDateTime(r.due_at || r.remind_at) }}</span>

                <span v-if="r.vehicle_id" class="tag">
                  🚗
                  <RouterLink class="link" :to="{ name: 'vehicle.show', params: { id: r.vehicle_id } }">
                    {{ vehicleName(r.vehicle_id) }}
                  </RouterLink>
                </span>

                <span v-else-if="r.device_id" class="tag">
                  🛠️
                  <RouterLink class="link" :to="{ name: 'device.show', params: { id: r.device_id } }">
                    {{ deviceName(r.device_id) }}
                  </RouterLink>
                </span>

                <span v-else class="tag">📌 Ogólne</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- SERVICES -->
      <div class="card">
        <div class="card-head">
          <div>
            <h2 class="card-title">Najbliższe serwisy</h2>
            <p class="card-subtitle">Po dacie next_due_at</p>
          </div>
          <span class="pill">{{ nearestServices.length }}</span>
        </div>

        <div class="card-body">
          <EmptyState v-if="nearestServices.length === 0" text="Brak serwisów." />

          <div v-else class="space-y-3">
            <div v-for="s in nearestServices" :key="s.id" class="item">
              <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                  <div class="font-semibold truncate">{{ s.title }}</div>
                  <div class="text-sm text-slate-400 truncate">{{ s.description || "—" }}</div>
                </div>

                <span class="badge" :class="s.is_active ? 'badge-ok' : 'badge-off'">
                  {{ s.is_active ? "Aktywne" : "Nieaktywne" }}
                </span>
              </div>

              <div class="mt-3 flex flex-wrap items-center gap-2">
                <span class="tag tag-soft">📅 {{ formatDate(s.next_due_at) }}</span>

                <span v-if="s.vehicle_id" class="tag">
                  🚗
                  <RouterLink class="link" :to="{ name: 'vehicle.show', params: { id: s.vehicle_id } }">
                    {{ vehicleName(s.vehicle_id) }}
                  </RouterLink>
                </span>

                <span v-else-if="s.device_id" class="tag">
                  🛠️
                  <RouterLink class="link" :to="{ name: 'device.show', params: { id: s.device_id } }">
                    {{ deviceName(s.device_id) }}
                  </RouterLink>
                </span>

                <span v-else class="tag">📌 Ogólne</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- LISTS -->
    <section class="grid gap-6 lg:grid-cols-2">
      <!-- VEHICLES -->
      <div class="card">
        <div class="card-head">
          <div>
            <h2 class="card-title">Pojazdy</h2>
            <p class="card-subtitle">Kliknij aby wejść w szczegóły</p>
          </div>
          <span class="pill">{{ vehicles.length }}</span>
        </div>

        <div class="card-body">
          <EmptyState v-if="vehicles.length === 0" text="Brak pojazdów." />

          <div v-else class="grid gap-2">
            <RouterLink
              v-for="v in vehicles"
              :key="v.id"
              class="row"
              :to="{ name: 'vehicle.show', params: { id: v.id } }"
            >
              <div class="flex items-center gap-3 min-w-0">
                <div class="icon">🚗</div>
                <div class="min-w-0">
                  <div class="font-medium truncate">{{ v.name }}</div>
                  <div class="text-xs text-slate-400 truncate">
                    {{ [v.brand, v.model].filter(Boolean).join(" ") || "—" }}
                  </div>
                </div>
              </div>
              <div class="text-xs text-slate-400">{{ v.plate || "" }}</div>
            </RouterLink>
          </div>
        </div>
      </div>

      <!-- DEVICES -->
      <div class="card">
        <div class="card-head">
          <div>
            <h2 class="card-title">Urządzenia</h2>
            <p class="card-subtitle">Kliknij aby wejść w dokumenty</p>
          </div>
          <span class="pill">{{ devices.length }}</span>
        </div>

        <div class="card-body">
          <EmptyState v-if="devices.length === 0" text="Brak urządzeń." />

          <div v-else class="grid gap-2">
            <RouterLink
              v-for="d in devices"
              :key="d.id"
              class="row"
              :to="{ name: 'device.show', params: { id: d.id } }"
            >
              <div class="flex items-center gap-3 min-w-0">
                <div class="icon">🛠️</div>
                <div class="min-w-0">
                  <div class="font-medium truncate">{{ d.name }}</div>
                  <div class="text-xs text-slate-400 truncate">{{ d.type || "urządzenie" }}</div>
                </div>
              </div>
              <div class="text-xs text-slate-400">ID: {{ d.id }}</div>
            </RouterLink>
          </div>
        </div>
      </div>
    </section>

    <!-- FOOT ACTIONS -->
    <section class="card">
      <div class="card-body flex flex-wrap items-center justify-between gap-3">
        <div>
          <div class="font-semibold">Szybkie akcje</div>
          <div class="text-sm text-slate-400">Najczęściej używane skróty.</div>
        </div>
        <div class="flex gap-2">
          <RouterLink class="btn" to="/vehicles/new">+ Dodaj pojazd</RouterLink>
          <RouterLink class="btn" to="/devices/new">+ Dodaj urządzenie</RouterLink>
          <RouterLink class="btn" to="/services/new">+ Dodaj serwis</RouterLink>
          <RouterLink class="btn" to="/reminders/new">+ Dodaj przypomnienie</RouterLink>
          <RouterLink class="btn btn-soft" to="/documents">Lista dokumentów</RouterLink>
          <RouterLink class="btn" to="/upload">Upload dokumentu</RouterLink>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { onMounted, ref, computed, reactive } from "vue";
import api from "../services/api";

// --- mini komponenty lokalne (bez osobnych plików, mniej roboty) ---
const StatCard = {
  props: { label: String, value: [String, Number], icon: String },
  template: `
    <div class="card">
      <div class="card-body flex items-center justify-between">
        <div>
          <div class="text-slate-400 text-sm">{{ label }}</div>
          <div class="text-3xl font-semibold mt-1">{{ value }}</div>
        </div>
        <div class="h-10 w-10 rounded-2xl bg-white/5 border border-slate-800/60 flex items-center justify-center text-lg">
          {{ icon }}
        </div>
      </div>
    </div>
  `,
};

const EmptyState = {
  props: { text: String },
  template: `
    <div class="rounded-2xl border border-slate-800/60 bg-white/5 px-4 py-4 text-sm text-slate-400">
      {{ text }}
    </div>
  `,
};

const loading = ref(false);
const error = ref("");

const vehicles = ref([]);
const devices = ref([]);
const reminders = ref([]);
const services = ref([]);

const stats = reactive({
  vehicles: 0,
  devices: 0,
  documents: 0,
  activeReminders: 0,
});

function safeArray(data) {
  return Array.isArray(data) ? data : (data?.data ?? []);
}

async function fetchList(url) {
  const res = await api.get(url);
  return safeArray(res.data);
}

async function fetchCount(url) {
  const res = await api.get(url);
  const arr = safeArray(res.data);
  if (Array.isArray(arr)) return arr.length;
  if (typeof res.data?.count === "number") return res.data.count;
  return 0;
}

function parseDateTime(x) {
  if (!x) return null;
  const d = new Date(x);
  return isNaN(d.getTime()) ? null : d;
}

function formatDateTime(x) {
  const d = parseDateTime(x);
  if (!d) return "—";
  return d.toLocaleString();
}

function formatDate(x) {
  const d = parseDateTime(x);
  if (!d) return "—";
  return d.toLocaleDateString();
}

function vehicleName(id) {
  const v = vehicles.value.find((x) => x.id === id);
  return v?.name || `Pojazd #${id}`;
}

function deviceName(id) {
  const d = devices.value.find((x) => x.id === id);
  return d?.name || `Urządzenie #${id}`;
}

const nearestReminders = computed(() => {
  return [...reminders.value]
    .filter((r) => r && (r.is_active === 1 || r.is_active === true))
    .sort((a, b) => {
      const da = parseDateTime(a.due_at || a.remind_at)?.getTime() ?? Infinity;
      const db = parseDateTime(b.due_at || b.remind_at)?.getTime() ?? Infinity;
      return da - db;
    })
    .slice(0, 5);
});

const nearestServices = computed(() => {
  return [...services.value]
    .filter((s) => s && (s.is_active === 1 || s.is_active === true))
    .sort((a, b) => {
      const da = parseDateTime(a.next_due_at)?.getTime() ?? Infinity;
      const db = parseDateTime(b.next_due_at)?.getTime() ?? Infinity;
      return da - db;
    })
    .slice(0, 5);
});

async function refresh() {
  loading.value = true;
  error.value = "";

  try {
    const [devicesList, vehiclesList, docsCount, remindersList, servicesList] =
      await Promise.all([
        fetchList("/api/devices"),
        fetchList("/api/vehicles"),
        fetchCount("/api/documents"),
        fetchList("/api/reminders"),
        fetchList("/api/services"),
      ]);

    devices.value = devicesList;
    vehicles.value = vehiclesList;
    reminders.value = remindersList;
    services.value = servicesList;

    stats.devices = devicesList.length;
    stats.vehicles = vehiclesList.length;
    stats.documents = docsCount;
    stats.activeReminders = remindersList.filter(
      (r) => r && (r.is_active === 1 || r.is_active === true)
    ).length;
  } catch (e) {
    error.value =
      e?.response?.data?.message ||
      e?.message ||
      "Błąd pobierania danych dashboardu.";
  } finally {
    loading.value = false;
  }
}

onMounted(refresh);
</script>

<style scoped>
/* bazowe karty */
.card {
  @apply rounded-3xl border border-slate-800/60 bg-white/5 shadow-[0_10px_30px_rgba(0,0,0,0.25)];
}
.card-head {
  @apply px-6 py-5 border-b border-slate-800/60 flex items-start justify-between gap-3;
}
.card-title {
  @apply text-lg font-semibold tracking-tight;
}
.card-subtitle {
  @apply text-sm text-slate-400 mt-1;
}
.card-body {
  @apply px-6 py-5;
}

/* guziki */
.btn {
  @apply px-4 py-2 rounded-2xl bg-white/10 hover:bg-white/15 border border-slate-800/60 transition text-sm font-medium;
}
.btn-soft {
  @apply bg-white/5 hover:bg-white/10;
}
.btn-danger {
  @apply bg-red-500/10 hover:bg-red-500/15 border-red-500/20;
}

/* małe elementy */
.pill {
  @apply px-2.5 py-1 rounded-xl bg-white/5 border border-slate-800/60 text-xs text-slate-300;
}
.badge {
  @apply px-2.5 py-1 rounded-xl text-xs border;
}
.badge-ok {
  @apply bg-emerald-500/10 border-emerald-500/20 text-emerald-200;
}
.badge-off {
  @apply bg-slate-500/10 border-slate-500/20 text-slate-300;
}

.item {
  @apply rounded-2xl border border-slate-800/60 bg-slate-950/40 px-4 py-4;
}

.tag {
  @apply inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-white/5 border border-slate-800/60 text-xs text-slate-200;
}
.tag-soft {
  @apply text-slate-300;
}
.link {
  @apply underline underline-offset-2 hover:text-white;
}

/* list row cards */
.row {
  @apply flex items-center justify-between gap-3 rounded-2xl border border-slate-800/60 bg-slate-950/40 px-4 py-3 hover:bg-white/5 transition;
}
.icon {
  @apply h-9 w-9 rounded-2xl bg-white/5 border border-slate-800/60 flex items-center justify-center;
}
</style>
