<template>
  <div class="space-y-6">
    <!-- HEADER -->
    <div class="card">
      <div class="card-head">
        <div class="min-w-0">
          <h1 class="card-title">Pojazdy</h1>
          <p class="card-subtitle">Lista wszystkich pojazdów + wyszukiwanie</p>
        </div>

        <div class="flex items-center gap-2">
          <RouterLink class="btn" to="/vehicles/new">+ Dodaj pojazd</RouterLink>
          <button class="btn btn-soft" @click="load" :disabled="loading">
            {{ loading ? "Ładuje…" : "Odśwież" }}
          </button>
        </div>
      </div>

      <div class="card-body">
        <div v-if="error" class="rounded-2xl border border-red-500/20 bg-red-500/10 px-4 py-3">
          <div class="text-red-200 text-sm">{{ error }}</div>
        </div>

        <div class="mt-3 grid gap-3 sm:grid-cols-3">
          <div class="mini">
            <div class="mini-k">Wszystkie</div>
            <div class="mini-v">{{ vehicles.length }}</div>
          </div>
          <div class="mini">
            <div class="mini-k">Z VIN</div>
            <div class="mini-v">{{ withVin }}</div>
          </div>
          <div class="mini">
            <div class="mini-k">Z tablicą</div>
            <div class="mini-v">{{ withPlate }}</div>
          </div>
        </div>

        <!-- SEARCH -->
        <div class="mt-5 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
          <input
            v-model="q"
            type="text"
            class="input md:max-w-md"
            placeholder="Szukaj: nazwa, marka, model, rok, VIN, tablica…"
          />

          <div class="text-sm text-slate-400">
            <span v-if="loading">Ładowanie…</span>
            <span v-else>Wyświetlasz {{ filtered.length }} z {{ vehicles.length }}</span>
          </div>
        </div>

        <!-- LIST -->
        <div class="mt-5">
          <div v-if="loading" class="text-slate-400 text-sm">Ładowanie…</div>

          <div v-else-if="filtered.length === 0" class="text-slate-400 text-sm">
            Brak pojazdów do wyświetlenia.
            <span v-if="q">Wyczyść wyszukiwanie albo dodaj nowy pojazd.</span>
          </div>

          <ul v-else class="list">
            <li v-for="v in filtered" :key="v.id" class="row">
              <div class="min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                  <div class="font-semibold truncate">
                    {{ v.name || `Pojazd #${v.id}` }}
                  </div>
                  <span class="tag tag-soft" v-if="v.license_plate">🚗 {{ v.license_plate }}</span>
                  <span class="tag" v-if="v.year">{{ v.year }}</span>
                </div>

                <div class="mt-1 text-sm text-slate-400">
                  {{ [v.make, v.model].filter(Boolean).join(" ") || "—" }}
                  <span v-if="v.purchase_date"> • zakup: {{ formatDate(v.purchase_date) }}</span>
                </div>

                <div class="mt-3 flex gap-2 flex-wrap">
                  <span class="tag tag-soft" v-if="v.vin">VIN: {{ v.vin }}</span>
                  <span class="tag tag-soft" v-if="v.notes">📝 Notatki</span>
                </div>
              </div>

              <div class="flex items-center gap-2 shrink-0">
                <RouterLink class="btn" :to="{ name: 'vehicle.show', params: { id: v.id } }">
                  Szczegóły
                </RouterLink>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <!-- FOOT -->
    <div class="card">
      <div class="card-body flex flex-wrap items-center justify-between gap-3">
        <div>
          <div class="font-semibold">Tip</div>
          <div class="text-sm text-slate-400">
            Kliknij „Szczegóły”, żeby zobaczyć serwisy i przypomnienia powiązane z pojazdem.
          </div>
        </div>
        <div class="flex gap-2">
          <RouterLink class="btn" to="/vehicles/new">+ Dodaj pojazd</RouterLink>
          <RouterLink class="btn btn-soft" to="/dashboard">Dashboard</RouterLink>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from "vue";
import api from "../services/api";

const loading = ref(false);
const error = ref("");
const vehicles = ref([]);
const q = ref("");

function safeArray(data) {
  return Array.isArray(data) ? data : data?.data ?? [];
}

function parseDate(x) {
  if (!x) return null;
  const d = new Date(x);
  return isNaN(d.getTime()) ? null : d;
}

function formatDate(x) {
  const d = parseDate(x);
  return d ? d.toLocaleDateString("pl-PL") : "—";
}

const withVin = computed(() => vehicles.value.filter((v) => !!v.vin).length);
const withPlate = computed(() => vehicles.value.filter((v) => !!v.license_plate).length);

const filtered = computed(() => {
  const term = q.value.trim().toLowerCase();
  if (!term) return vehicles.value;

  return vehicles.value.filter((v) => {
    const blob = [v.name, v.make, v.model, v.year, v.vin, v.license_plate, v.notes]
      .filter(Boolean)
      .join(" ")
      .toLowerCase();
    return blob.includes(term);
  });
});

async function load() {
  loading.value = true;
  error.value = "";
  try {
    const res = await api.get("/api/vehicles");
    vehicles.value = safeArray(res.data);
  } catch (e) {
    error.value = e?.response?.data?.message || e?.message || "Błąd ładowania pojazdów.";
  } finally {
    loading.value = false;
  }
}

onMounted(load);
</script>

<style scoped>
.card { @apply rounded-3xl border border-slate-800/60 bg-white/5 shadow-[0_10px_30px_rgba(0,0,0,0.25)]; }
.card-head { @apply px-6 py-5 border-b border-slate-800/60 flex items-start justify-between gap-3; }
.card-title { @apply text-lg font-semibold tracking-tight; }
.card-subtitle { @apply text-sm text-slate-400 mt-1; }
.card-body { @apply px-6 py-5; }

.btn { @apply px-4 py-2 rounded-2xl bg-white/10 hover:bg-white/15 border border-slate-800/60 transition text-sm font-medium; }
.btn-soft { @apply bg-white/5 hover:bg-white/10; }

.input { @apply w-full rounded-2xl bg-slate-950/40 border border-slate-800/60 px-4 py-2 text-slate-100 placeholder:text-slate-500 outline-none focus:ring-2 focus:ring-white/10; }

.mini { @apply rounded-2xl border border-slate-800/60 bg-slate-900/30 p-4; }
.mini-k { @apply text-xs uppercase tracking-wide text-slate-400; }
.mini-v { @apply mt-2 text-3xl font-semibold; }

.list { @apply divide-y divide-slate-800/60 rounded-2xl border border-slate-800/60 bg-slate-900/20 overflow-hidden; }
.row { @apply p-4 flex items-start justify-between gap-4 hover:bg-slate-900/40 transition; }

.tag { @apply inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-white/5 border border-slate-800/60 text-xs text-slate-200; }
.tag-soft { @apply text-slate-300; }
</style>