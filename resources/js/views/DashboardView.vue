<template>
  <div class="space-y-6">
    <!-- TOP: nagłówek -->
    <div class="card">
      <div class="card-header">
        <div>
          <div class="card-title">Dashboard</div>
          <div class="card-subtitle">Przegląd statystyk i skrótów.</div>
        </div>

        <button class="btn" @click="refresh" :disabled="loading">
          {{ loading ? "Ładuje..." : "Odśwież" }}
        </button>
      </div>

      <div v-if="error" class="card-body">
        <div class="text-red-300 text-sm">
          {{ error }}
        </div>
      </div>
    </div>

    <!-- STATY -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
      <div class="card">
        <div class="card-body">
          <div class="text-slate-400 text-sm">Pojazdy</div>
          <div class="text-2xl font-semibold mt-1">{{ stats.vehicles }}</div>
        </div>
      </div>

      <div class="card">
        <div class="card-body">
          <div class="text-slate-400 text-sm">Urządzenia</div>
          <div class="text-2xl font-semibold mt-1">{{ stats.devices }}</div>
        </div>
      </div>

      <div class="card">
        <div class="card-body">
          <div class="text-slate-400 text-sm">Przypomnienia aktywne</div>
          <div class="text-2xl font-semibold mt-1">{{ stats.activeReminders }}</div>
        </div>
      </div>

      <div class="card">
        <div class="card-body">
          <div class="text-slate-400 text-sm">Dokumenty</div>
          <div class="text-2xl font-semibold mt-1">{{ stats.documents }}</div>
        </div>
      </div>
    </div>

    <!-- DWIE KOLUMNY -->
    <div class="grid gap-4 lg:grid-cols-2">
      <!-- PRZYPOMNIENIA -->
      <div class="card">
        <div class="card-header">
          <div>
            <div class="card-title">Najbliższe przypomnienia</div>
            <div class="card-subtitle">Sortowane po terminie</div>
          </div>
          <span class="pill">{{ nearestReminders.length }}</span>
        </div>

        <div class="card-body">
          <div v-if="nearestReminders.length === 0" class="text-slate-400 text-sm">
            Brak przypomnień.
          </div>

          <ul v-else class="divide-y divide-slate-800/60">
            <li v-for="r in nearestReminders" :key="r.id" class="py-3 flex items-start justify-between gap-3">
              <div class="min-w-0">
                <div class="font-semibold truncate">{{ r.title }}</div>
                <div class="text-sm text-slate-400 truncate">
                  {{ r.description || "—" }}
                </div>

                <div class="mt-2 flex flex-wrap items-center gap-2">
                  <span class="tag" v-if="r.vehicle_id">
                    
                    <RouterLink
                      class="underline underline-offset-2 hover:text-white"
                      :to="{ name: 'vehicle.show', params: { id: r.vehicle_id } }"
                    >
                      {{ vehicleName(r.vehicle_id) }}
                    </RouterLink>
                  </span>

                  <span class="tag" v-else-if="r.device_id">
                    🔧
                    <RouterLink
                      class="underline underline-offset-2 hover:text-white"
                      :to="{ name: 'device.show', params: { id: r.device_id } }"
                    >
                      {{ deviceName(r.device_id) }}
                    </RouterLink>
                  </span>

                  <span class="tag" v-else>
                    
                  </span>

                  <span class="tag tag-soft">
                    ⏰ {{ formatDateTime(r.due_at || r.remind_at) }}
                  </span>
                </div>
              </div>

              <div class="text-xs text-slate-400 shrink-0">
                {{ r.is_active ? "Aktywne" : "Nieaktywne" }}
              </div>
            </li>
          </ul>
        </div>
      </div>

      <!-- SERWISY -->
      <div class="card">
        <div class="card-header">
          <div>
            <div class="card-title">Najbliższe serwisy</div>
            <div class="card-subtitle">Po dacie next_due_at</div>
          </div>
          <span class="pill">{{ nearestServices.length }}</span>
        </div>

        <div class="card-body">
          <div v-if="nearestServices.length === 0" class="text-slate-400 text-sm">
            Brak serwisów.
          </div>

          <ul v-else class="divide-y divide-slate-800/60">
            <li v-for="s in nearestServices" :key="s.id" class="py-3 flex items-start justify-between gap-3">
              <div class="min-w-0">
                <div class="font-semibold truncate">{{ s.title }}</div>
                <div class="text-sm text-slate-400 truncate">
                  {{ s.description || "—" }}
                </div>

                <div class="mt-2 flex flex-wrap items-center gap-2">
                  <span class="tag" v-if="s.vehicle_id">
                    
                    <RouterLink
                      class="underline underline-offset-2 hover:text-white"
                      :to="{ name: 'vehicle.show', params: { id: s.vehicle_id } }"
                    >
                      {{ vehicleName(s.vehicle_id) }}
                    </RouterLink>
                  </span>

                  <span class="tag" v-else-if="s.device_id">
                    🔧
                    <RouterLink
                      class="underline underline-offset-2 hover:text-white"
                      :to="{ name: 'device.show', params: { id: s.device_id } }"
                    >
                      {{ deviceName(s.device_id) }}
                    </RouterLink>
                  </span>

                  <span class="tag" v-else>
                    
                  </span>

                  <span class="tag tag-soft">
                    📅 {{ formatDate(s.next_due_at) }}
                  </span>
                </div>
              </div>

              <div class="text-xs text-slate-400 shrink-0">
                {{ s.is_active ? "Aktywne" : "Nieaktywne" }}
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <!-- LISTY: POJAZDY + URZĄDZENIA -->
    <div class="grid gap-4 lg:grid-cols-2">
      <!-- POJAZDY -->
      <div class="card">
        <div class="card-header">
          <div>
            <div class="card-title">Pojazdy</div>
            <div class="card-subtitle">Kliknij aby wejść w szczegóły</div>
          </div>
          <span class="pill">{{ vehicles.length }}</span>
        </div>

        <div class="card-body">
          <div v-if="vehicles.length === 0" class="text-slate-400 text-sm">
            Brak pojazdów
          </div>

          <ul v-else class="divide-y divide-slate-800/60">
            <li v-for="v in vehicles" :key="v.id" class="py-2 flex items-center justify-between gap-3">
              <RouterLink
                class="font-medium hover:underline underline-offset-2 truncate"
                :to="{ name: 'vehicle.show', params: { id: v.id } }"
              >
                {{ v.name }}
              </RouterLink>

              <span class="text-sm text-slate-400 shrink-0 truncate">
                {{ [v.brand, v.model].filter(Boolean).join(" ") }}
              </span>
            </li>
          </ul>
        </div>
      </div>

      <!-- URZĄDZENIA -->
      <div class="card">
        <div class="card-header">
          <div>
            <div class="card-title">Urządzenia</div>
            <div class="card-subtitle">Kliknij aby wejść w dokumenty</div>
          </div>
          <span class="pill">{{ devices.length }}</span>
        </div>

        <div class="card-body">
          <div v-if="devices.length === 0" class="text-slate-400 text-sm">
            Brak urządzeń
          </div>

          <ul v-else class="divide-y divide-slate-800/60">
            <li v-for="d in devices" :key="d.id" class="py-2 flex items-center justify-between gap-3">
              <RouterLink
                class="font-medium hover:underline underline-offset-2 truncate"
                :to="{ name: 'device.show', params: { id: d.id } }"
              >
                {{ d.name }}
              </RouterLink>

              <span class="text-sm text-slate-400 shrink-0 truncate">
                {{ d.type || "urządzenie" }}
              </span>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <!-- CTA -->
    <div class="card">
      <div class="card-body flex flex-wrap gap-3 items-center justify-between">
        <div class="text-sm text-slate-300">
          Szybkie akcje:
        </div>
        <div class="flex gap-2">
          <RouterLink class="btn" to="/upload">Upload dokumentu</RouterLink>
          <RouterLink class="btn" to="/documents">Lista dokumentów</RouterLink>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref, computed, reactive } from "vue";
import api from "../services/api";

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
  // jeśli API zwraca listę -> policz
  const arr = safeArray(res.data);
  if (Array.isArray(arr)) return arr.length;

  // jeśli API zwraca {count: X}
  if (typeof res.data?.count === "number") return res.data.count;

  // fallback:
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
  if (!id) return null;
  const v = vehicles.value.find((x) => x.id === id);
  return v?.name || `Pojazd #${id}`;
}

function deviceName(id) {
  if (!id) return null;
  const d = devices.value.find((x) => x.id === id);
  return d?.name || `Urządzenie #${id}`;
}

const nearestReminders = computed(() => {
  const list = [...reminders.value]
    .filter((r) => r && (r.is_active === 1 || r.is_active === true))
    .sort((a, b) => {
      const da = parseDateTime(a.due_at || a.remind_at)?.getTime() ?? Infinity;
      const db = parseDateTime(b.due_at || b.remind_at)?.getTime() ?? Infinity;
      return da - db;
    })
    .slice(0, 6);

  return list;
});

const nearestServices = computed(() => {
  const list = [...services.value]
    .filter((s) => s && (s.is_active === 1 || s.is_active === true))
    .sort((a, b) => {
      const da = parseDateTime(a.next_due_at)?.getTime() ?? Infinity;
      const db = parseDateTime(b.next_due_at)?.getTime() ?? Infinity;
      return da - db;
    })
    .slice(0, 6);

  return list;
});

async function refresh() {
  loading.value = true;
  error.value = "";

  try {
    const [
      devicesList,
      vehiclesList,
      docsCount,
      remindersList,
      servicesList,
    ] = await Promise.all([
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
    stats.activeReminders = remindersList.filter((r) => r && (r.is_active === 1 || r.is_active === true)).length;
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
