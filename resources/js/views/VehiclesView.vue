<template>
    <div class="space-y-6">
      <!-- Header -->
      <section class="flex items-end justify-between gap-4">
        <div>
          <h1 class="text-2xl font-semibold tracking-tight">Pojazdy</h1>
          <p class="text-slate-400 mt-1">Lista Twoich pojazdów. Kliknij, aby wejść w szczegóły.</p>
        </div>
  
        <div class="flex gap-2">
          <RouterLink class="btn btn-soft" to="/dashboard">← Dashboard</RouterLink>
          <RouterLink class="btn" to="/vehicles/new">+ Dodaj pojazd</RouterLink>
        </div>
      </section>
  
      <!-- Top bar -->
      <div class="card">
        <div class="card-body flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
          <div class="flex gap-2 w-full sm:max-w-md">
            <input v-model.trim="q" class="input" placeholder="Szukaj (nazwa / marka / rejestracja / VIN)..." />
          </div>
  
          <div class="text-sm text-slate-400">
            Razem: <span class="text-slate-200 font-medium">{{ filtered.length }}</span>
          </div>
        </div>
      </div>
  
      <!-- States -->
      <div v-if="error" class="card border-red-500/20 bg-red-500/5">
        <div class="card-body text-sm text-red-200">{{ error }}</div>
      </div>
  
      <div v-else-if="loading" class="card">
        <div class="card-body text-sm text-slate-400">Ładowanie pojazdów…</div>
      </div>
  
      <div v-else-if="filtered.length === 0" class="card">
        <div class="card-body text-sm text-slate-400">
          Brak pojazdów lub brak wyników wyszukiwania.
        </div>
      </div>
  
      <!-- Grid -->
      <div v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <button
          v-for="v in filtered"
          :key="v.id"
          class="card card-hover text-left"
          @click="goDetails(v.id)"
        >
          <div class="card-body space-y-3">
            <div class="flex items-start justify-between gap-3">
              <div>
                <div class="text-lg font-semibold text-slate-100 leading-tight">
                  {{ v.name }}
                </div>
                <div class="text-sm text-slate-400 mt-1">
                  {{ subtitle(v) }}
                </div>
              </div>
  
              <span class="badge">#{{ v.id }}</span>
            </div>
  
            <div class="grid grid-cols-2 gap-3 text-sm">
              <div class="tile">
                <div class="tile-k">Rejestracja</div>
                <div class="tile-v">{{ v.license_plate || "—" }}</div>
              </div>
              <div class="tile">
                <div class="tile-k">Rok</div>
                <div class="tile-v">{{ v.year || "—" }}</div>
              </div>
              <div class="tile col-span-2">
                <div class="tile-k">VIN</div>
                <div class="tile-v font-mono text-xs break-all">{{ v.vin || "—" }}</div>
              </div>
            </div>
  
            <div class="flex items-center justify-between pt-1">
              <span class="text-xs text-slate-500">
                Zakup: {{ formatDate(v.purchase_date) }}
              </span>
              <span class="text-sm text-slate-200">Szczegóły →</span>
            </div>
          </div>
        </button>
      </div>
    </div>
  </template>
  
  <script setup>
  import { computed, onMounted, ref } from "vue";
  import { useRouter } from "vue-router";
  import api from "../services/api";
  
  const router = useRouter();
  const loading = ref(false);
  const error = ref("");
  const vehicles = ref([]);
  const q = ref("");
  
  function extractList(res) {
    // wspiera zarówno: [..] jak i {data:[..]}
    return Array.isArray(res.data) ? res.data : (res.data?.data ?? []);
  }
  
  async function fetchVehicles() {
    loading.value = true;
    error.value = "";
    try {
      const res = await api.get("/api/vehicles");
      vehicles.value = extractList(res);
    } catch (e) {
      error.value = e?.response?.data?.message || e?.message || "Nie udało się pobrać pojazdów.";
    } finally {
      loading.value = false;
    }
  }
  
  const filtered = computed(() => {
    const s = q.value.toLowerCase().trim();
    if (!s) return vehicles.value;
  
    return vehicles.value.filter((v) => {
      const hay = [
        v.name,
        v.make,
        v.model,
        v.license_plate,
        v.vin,
        v.year,
      ]
        .filter(Boolean)
        .join(" ")
        .toLowerCase();
  
      return hay.includes(s);
    });
  });
  
  function subtitle(v) {
    const left = [v.make, v.model].filter(Boolean).join(" ");
    const right = v.license_plate ? ` • ${v.license_plate}` : "";
    return (left || "—") + right;
  }
  
  function formatDate(d) {
    if (!d) return "—";
    try {
      return new Date(d).toLocaleDateString("pl-PL");
    } catch {
      return "—";
    }
  }
  
  function goDetails(id) {
    router.push(`/vehicles/${id}`);
  }
  
  onMounted(fetchVehicles);
  </script>
  
  <style scoped>
  .card { @apply rounded-3xl border border-slate-800/60 bg-white/5 shadow-[0_10px_30px_rgba(0,0,0,0.25)]; }
  .card-hover { @apply hover:border-slate-700 hover:bg-white/7 transition; }
  .card-body { @apply px-5 py-5; }
  
  .input { @apply w-full rounded-2xl bg-slate-950/40 border border-slate-800/60 px-4 py-2 text-slate-100 placeholder:text-slate-500 outline-none focus:ring-2 focus:ring-white/10; }
  .btn { @apply inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-white/10 hover:bg-white/15 border border-slate-800/60 transition text-sm font-medium; }
  .btn-soft { @apply bg-white/5 hover:bg-white/10; }
  
  .badge { @apply text-xs px-2 py-1 rounded-xl bg-white/10 text-slate-200 whitespace-nowrap; }
  
  .tile { @apply rounded-2xl border border-slate-800/60 bg-slate-950/30 px-3 py-2; }
  .tile-k { @apply text-xs text-slate-500; }
  .tile-v { @apply text-slate-100 font-medium mt-0.5; }
  </style>