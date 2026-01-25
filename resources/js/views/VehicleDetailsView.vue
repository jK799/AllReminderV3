<template>
    <div class="space-y-6">
      <!-- Header -->
      <section class="flex items-end justify-between gap-4">
        <div>
          <h1 class="text-2xl font-semibold tracking-tight">
            {{ vehicle?.name || "Pojazd" }}
          </h1>
          <p class="text-slate-400 mt-1">
            Szczegóły pojazdu i szybkie akcje.
          </p>
        </div>
  
        <div class="flex gap-2">
          <RouterLink class="btn btn-soft" to="/vehicles">← Lista pojazdów</RouterLink>
          <button class="btn btn-danger" :disabled="loading" @click="removeVehicle">
            Usuń
          </button>
        </div>
      </section>
  
      <!-- States -->
      <div v-if="error" class="card border-red-500/20 bg-red-500/5">
        <div class="card-body text-sm text-red-200">{{ error }}</div>
      </div>
  
      <div v-else-if="loading" class="card">
        <div class="card-body text-sm text-slate-400">Ładowanie szczegółów…</div>
      </div>
  
      <!-- Content -->
      <div v-else-if="vehicle" class="grid gap-4 lg:grid-cols-3">
        <!-- Main card -->
        <div class="card lg:col-span-2">
          <div class="card-body space-y-4">
            <div class="flex items-start justify-between gap-3">
              <div>
                <div class="text-lg font-semibold text-slate-100">
                  {{ vehicle.make || "—" }} {{ vehicle.model || "" }}
                </div>
                <div class="text-sm text-slate-400 mt-1">
                  ID: #{{ vehicle.id }} • Zakup: {{ formatDate(vehicle.purchase_date) }}
                </div>
              </div>
              <span class="badge">
                {{ vehicle.license_plate || "Brak rejestracji" }}
              </span>
            </div>
  
            <div class="grid sm:grid-cols-2 gap-3">
              <Info label="Rok" :value="vehicle.year" />
              <Info label="VIN" :value="vehicle.vin" mono />
              <Info label="Marka" :value="vehicle.make" />
              <Info label="Model" :value="vehicle.model" />
            </div>
  
            <div class="rounded-2xl border border-slate-800/60 bg-slate-950/30 p-4">
              <div class="text-xs text-slate-500">Notatki</div>
              <div class="text-slate-100 mt-1 whitespace-pre-wrap">
                {{ vehicle.notes || "—" }}
              </div>
            </div>
  
            <div class="flex flex-wrap gap-2 pt-1">
              <RouterLink class="btn" to="/services/new">+ Dodaj serwis</RouterLink>
              <RouterLink class="btn" to="/reminders/new">+ Dodaj przypomnienie</RouterLink>
              <RouterLink class="btn" to="/upload">+ Dodaj dokument</RouterLink>
            </div>
          </div>
        </div>
  
        <!-- Side summary -->
        <div class="card">
          <div class="card-body space-y-3">
            <div class="text-sm font-medium text-slate-200">Podsumowanie</div>
  
            <div class="tile">
              <div class="tile-k">Rejestracja</div>
              <div class="tile-v">{{ vehicle.license_plate || "—" }}</div>
            </div>
  
            <div class="tile">
              <div class="tile-k">Rok</div>
              <div class="tile-v">{{ vehicle.year || "—" }}</div>
            </div>
  
            <div class="tile">
              <div class="tile-k">VIN</div>
              <div class="tile-v font-mono text-xs break-all">{{ vehicle.vin || "—" }}</div>
            </div>
  
            <div class="text-xs text-slate-500 pt-2">
              Następnie możemy tu dodać: listę serwisów, przypomnień i dokumentów przypiętych do tego pojazdu.
            </div>
          </div>
        </div>
      </div>
  
      <div v-else class="card">
        <div class="card-body text-sm text-slate-400">Nie znaleziono pojazdu.</div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { onMounted, ref } from "vue";
  import { useRoute, useRouter } from "vue-router";
  import api from "../services/api";
  
  const route = useRoute();
  const router = useRouter();
  
  const loading = ref(false);
  const error = ref("");
  const vehicle = ref(null);
  
  function id() {
    return route.params.id;
  }
  
  async function fetchVehicle() {
    loading.value = true;
    error.value = "";
    try {
      const res = await api.get(`/api/vehicles/${id()}`);
      vehicle.value = res.data;
    } catch (e) {
      error.value = e?.response?.data?.message || e?.message || "Nie udało się pobrać pojazdu.";
    } finally {
      loading.value = false;
    }
  }
  
  function formatDate(d) {
    if (!d) return "—";
    try {
      return new Date(d).toLocaleDateString("pl-PL");
    } catch {
      return "—";
    }
  }
  
  async function removeVehicle() {
    if (!vehicle.value) return;
    if (!confirm(`Usunąć pojazd: "${vehicle.value.name}"?`)) return;
  
    loading.value = true;
    try {
      await api.delete(`/api/vehicles/${vehicle.value.id}`);
      router.push("/vehicles");
    } catch (e) {
      error.value = e?.response?.data?.message || e?.message || "Nie udało się usunąć pojazdu.";
    } finally {
      loading.value = false;
    }
  }
  
  // lokalny komponent Info
  const Info = {
    props: { label: String, value: [String, Number], mono: Boolean },
    template: `
      <div class="tile">
        <div class="tile-k">{{ label }}</div>
        <div class="tile-v" :class="mono ? 'font-mono text-xs break-all' : ''">{{ value || '—' }}</div>
      </div>
    `,
  };
  
  onMounted(fetchVehicle);
  </script>
  
  <style scoped>
  .card { @apply rounded-3xl border border-slate-800/60 bg-white/5 shadow-[0_10px_30px_rgba(0,0,0,0.25)]; }
  .card-body { @apply px-5 py-5; }
  
  .btn { @apply inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-white/10 hover:bg-white/15 border border-slate-800/60 transition text-sm font-medium; }
  .btn-soft { @apply bg-white/5 hover:bg-white/10; }
  .btn-danger { @apply bg-red-500/10 hover:bg-red-500/20 border-red-500/30 text-red-200; }
  
  .badge { @apply text-xs px-2 py-1 rounded-xl bg-white/10 text-slate-200 whitespace-nowrap; }
  
  .tile { @apply rounded-2xl border border-slate-800/60 bg-slate-950/30 px-3 py-2; }
  .tile-k { @apply text-xs text-slate-500; }
  .tile-v { @apply text-slate-100 font-medium mt-0.5; }
  </style>