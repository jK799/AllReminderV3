<template>
  <div class="space-y-6">
    <!-- HEADER / SUMMARY -->
    <div class="card">
      <div class="card-header">
        <div class="min-w-0">
          <div class="card-title">Pojazd</div>
          <div class="card-subtitle break-words">
            <span v-if="loading" class="text-slate-400">Ładowanie…</span>
            <span v-else>{{ vehicle?.name || `Pojazd #${id}` }}</span>
          </div>
        </div>

        <div class="flex items-center gap-2">
          <RouterLink class="btn" to="/vehicles">Lista pojazdów</RouterLink>
          <button class="btn btn-soft" @click="load" :disabled="loading">
            Odśwież
          </button>
        </div>
      </div>

      <div class="card-body">
        <div v-if="error" class="rounded-xl border border-red-500/20 bg-red-500/10 px-4 py-3">
          <div class="text-red-200 text-sm">{{ error }}</div>
        </div>

        <div v-else class="grid gap-4 lg:grid-cols-3">
          <!-- QUICK INFO -->
          <div class="lg:col-span-2">
            <div class="text-slate-300 text-sm">
              {{ [vehicle?.make, vehicle?.model, vehicle?.year].filter(Boolean).join(" • ") || "—" }}
            </div>

            <div class="mt-3 flex flex-wrap gap-2">
              <span class="tag tag-soft" v-if="vehicle?.license_plate">🚗 {{ vehicle.license_plate }}</span>
              <span class="tag tag-soft" v-if="vehicle?.purchase_date">🧾 {{ formatDate(vehicle.purchase_date) }}</span>
              <span class="tag" v-else>Brak danych</span>
            </div>

            <div class="mt-4 grid gap-3 sm:grid-cols-2">
              <div class="rounded-2xl border border-slate-800/60 bg-slate-900/30 p-4">
                <div class="text-xs uppercase tracking-wide text-slate-400">VIN</div>
                <div class="mt-1 font-medium break-all">{{ vehicle?.vin || "—" }}</div>
              </div>
              <div class="rounded-2xl border border-slate-800/60 bg-slate-900/30 p-4">
                <div class="text-xs uppercase tracking-wide text-slate-400">Notatki</div>
                <div class="mt-1 text-slate-200 text-sm whitespace-pre-line">{{ vehicle?.notes || "—" }}</div>
              </div>
            </div>
          </div>

          <!-- COUNTERS -->
          <div class="grid gap-3">
            <div class="rounded-2xl border border-slate-800/60 bg-slate-900/30 p-4">
              <div class="text-xs uppercase tracking-wide text-slate-400">Przypomnienia</div>
              <div class="mt-2 flex items-end justify-between">
                <div class="text-3xl font-semibold">{{ reminders.length }}</div>
                <span class="pill">dla pojazdu</span>
              </div>
            </div>

            <div class="rounded-2xl border border-slate-800/60 bg-slate-900/30 p-4">
              <div class="text-xs uppercase tracking-wide text-slate-400">Serwisy</div>
              <div class="mt-2 flex items-end justify-between">
                <div class="text-3xl font-semibold">{{ services.length }}</div>
                <span class="pill">dla pojazdu</span>
              </div>
            </div>

            <div class="rounded-2xl border border-slate-800/60 bg-slate-900/30 p-4">
              <div class="text-xs uppercase tracking-wide text-slate-400">Dokumenty</div>
              <div class="mt-2 text-sm text-slate-300">Dodasz w Upload (kategoria: Pojazdy)</div>
              <div class="mt-3">
                <RouterLink class="btn w-full justify-center" to="/upload">Przejdź do Upload</RouterLink>
              </div>
            </div>
          </div>
        </div>

        <div v-if="!loading && !error && !vehicle" class="mt-4 rounded-xl border border-amber-500/20 bg-amber-500/10 px-4 py-3">
          <div class="text-amber-100 text-sm">
            Nie znaleziono pojazdu o ID {{ id }}. Sprawdź, czy istnieje i czy jesteś zalogowany.
          </div>
        </div>
      </div>
    </div>

    <!-- CONTENT -->
    <div class="grid gap-4 lg:grid-cols-2">
      <!-- REMINDERS -->
      <div class="card">
        <div class="card-header">
          <div>
            <div class="card-title">Przypomnienia</div>
            <div class="card-subtitle">Tylko dla tego pojazdu</div>
          </div>
          <span class="pill">{{ reminders.length }}</span>
        </div>

        <div class="card-body">
          <div v-if="loading" class="text-slate-400 text-sm">Ładowanie…</div>

          <div v-else-if="reminders.length === 0" class="text-slate-400 text-sm">
            Brak przypomnień dla tego pojazdu.
          </div>

          <ul v-else class="divide-y divide-slate-800/60">
            <li v-for="r in reminders" :key="r.id" class="py-4">
              <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                  <div class="font-semibold truncate">{{ r.title }}</div>
                  <div class="text-sm text-slate-400 mt-1 whitespace-pre-line">{{ r.description || "—" }}</div>
                  <div class="mt-3 flex gap-2 flex-wrap">
                    <span class="tag tag-soft">⏰ {{ formatDateTime(r.due_at || r.remind_at) }}</span>
                    <span class="tag" :class="r.is_active ? 'tag-ok' : 'tag-muted'">
                      {{ r.is_active ? "Aktywne" : "Nieaktywne" }}
                    </span>
                    <span v-if="r.completed_at" class="tag tag-soft">✅ {{ formatDateTime(r.completed_at) }}</span>
                  </div>
                </div>
                <span class="tag">#{{ r.id }}</span>
              </div>
            </li>
          </ul>
        </div>
      </div>

      <!-- SERVICES -->
      <div class="card">
        <div class="card-header">
          <div>
            <div class="card-title">Serwisy</div>
            <div class="card-subtitle">Tylko dla tego pojazdu</div>
          </div>
          <span class="pill">{{ services.length }}</span>
        </div>

        <div class="card-body">
          <div v-if="loading" class="text-slate-400 text-sm">Ładowanie…</div>

          <div v-else-if="services.length === 0" class="text-slate-400 text-sm">
            Brak serwisów dla tego pojazdu.
          </div>

          <ul v-else class="divide-y divide-slate-800/60">
            <li v-for="s in services" :key="s.id" class="py-4">
              <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                  <div class="font-semibold truncate">{{ s.title }}</div>
                  <div class="text-sm text-slate-400 mt-1 whitespace-pre-line">{{ s.description || "—" }}</div>
                  <div class="mt-3 flex gap-2 flex-wrap">
                    <span class="tag tag-soft">📅 next: {{ formatDate(s.next_due_at) }}</span>
                    <span v-if="s.last_done_at" class="tag tag-soft">🛠 last: {{ formatDate(s.last_done_at) }}</span>
                    <span class="tag" :class="s.is_active ? 'tag-ok' : 'tag-muted'">
                      {{ s.is_active ? "Aktywne" : "Nieaktywne" }}
                    </span>
                    <span v-if="s.interval_value && s.interval_unit" class="tag">↻ {{ s.interval_value }} {{ s.interval_unit }}</span>
                  </div>
                </div>
                <span class="tag">#{{ s.id }}</span>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <!-- QUICK ACTIONS -->
    <div class="card">
      <div class="card-body flex flex-wrap items-center justify-between gap-3">
        <div>
          <div class="font-semibold">Szybkie akcje</div>
          <div class="text-sm text-slate-400">Dodaj serwis / przypomnienie powiązane z tym pojazdem.</div>
        </div>

        <div class="flex flex-wrap gap-2">
          <RouterLink class="btn" to="/services/new">+ Serwis</RouterLink>
          <RouterLink class="btn" to="/reminders/new">+ Przypomnienie</RouterLink>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref, computed } from "vue";
import { useRoute } from "vue-router";
import api from "../services/api";

const route = useRoute();
const id = Number(route.params.id);

const loading = ref(false);
const error = ref("");

const vehicles = ref([]);
const allReminders = ref([]);
const allServices = ref([]);

const vehicle = computed(() => vehicles.value.find((v) => Number(v.id) === id) || null);

const reminders = computed(() =>
  allReminders.value
    .filter((r) => Number(r.vehicle_id) === id)
    .sort((a, b) => new Date(a.due_at || a.remind_at) - new Date(b.due_at || b.remind_at))
);

const services = computed(() =>
  allServices.value
    .filter((s) => Number(s.vehicle_id) === id)
    .sort((a, b) => new Date(a.next_due_at) - new Date(b.next_due_at))
);

function safeArray(data) {
  return Array.isArray(data) ? data : data?.data ?? [];
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

async function load() {
  loading.value = true;
  error.value = "";
  try {
    const [v, r, s] = await Promise.all([
      api.get("/api/vehicles"),
      api.get("/api/reminders"),
      api.get("/api/services"),
    ]);

    vehicles.value = safeArray(v.data);
    allReminders.value = safeArray(r.data);
    allServices.value = safeArray(s.data);
  } catch (e) {
    error.value = e?.response?.data?.message || e?.message || "Błąd ładowania pojazdu.";
  } finally {
    loading.value = false;
  }
}

onMounted(load);
</script>