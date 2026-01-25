<template>
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
          <h1 class="text-2xl font-semibold tracking-tight">Urządzenia</h1>
          <p class="text-slate-400 text-sm">Lista Twoich urządzeń. Kliknij, aby zobaczyć szczegóły.</p>
        </div>
  
        <div class="flex gap-2">
          <button class="btn" @click="fetchDevices" :disabled="loading">
            {{ loading ? "Ładowanie..." : "Odśwież" }}
          </button>
  
          <RouterLink class="btn btn-primary" to="/devices/new">
            + Dodaj urządzenie
          </RouterLink>
        </div>
      </div>
  
      <!-- Search -->
      <div class="card p-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
          <div class="flex-1">
            <input
              v-model="q"
              type="text"
              placeholder="Szukaj po nazwie / marce / modelu / numerze seryjnym..."
              class="input w-full"
            />
          </div>
  
          <div class="flex items-center gap-2 text-sm text-slate-400">
            <span>Wyników:</span>
            <span class="font-semibold text-slate-200">{{ filtered.length }}</span>
          </div>
        </div>
      </div>
  
      <!-- States -->
      <div v-if="error" class="card p-4 border border-rose-900/40 bg-rose-500/10">
        <div class="text-rose-200 font-medium">Błąd</div>
        <div class="text-rose-200/80 text-sm mt-1">{{ error }}</div>
      </div>
  
      <div v-else-if="!loading && filtered.length === 0" class="card p-8 text-center">
        <div class="text-slate-200 font-semibold">Brak urządzeń</div>
        <div class="text-slate-400 text-sm mt-1">
          Dodaj pierwsze urządzenie lub zmień filtr wyszukiwania.
        </div>
        <div class="mt-4">
          <RouterLink class="btn btn-primary" to="/devices/new">+ Dodaj urządzenie</RouterLink>
        </div>
      </div>
  
      <!-- List -->
      <div v-else class="grid gap-3">
        <RouterLink
          v-for="d in filtered"
          :key="d.id"
          class="card p-4 hover:bg-white/5 transition border border-slate-800/60"
          :to="{ name: 'device.show', params: { id: d.id } }"
        >
          <div class="flex items-start justify-between gap-4">
            <div class="min-w-0">
              <div class="flex items-center gap-2">
                <div class="h-9 w-9 rounded-xl bg-white/5 border border-slate-800/60 flex items-center justify-center">
                  <span class="text-sm">🔌</span>
                </div>
                <div class="min-w-0">
                  <div class="text-slate-100 font-semibold truncate">
                    {{ d.name || `Urządzenie #${d.id}` }}
                  </div>
                  <div class="text-slate-400 text-sm truncate">
                    {{ subtitle(d) }}
                  </div>
                </div>
              </div>
  
              <div class="mt-3 flex flex-wrap gap-2 text-xs">
                <span v-if="d.brand" class="badge">Marka: {{ d.brand }}</span>
                <span v-if="d.model" class="badge">Model: {{ d.model }}</span>
                <span v-if="d.serial_number" class="badge">S/N: {{ d.serial_number }}</span>
                <span v-if="d.purchase_date" class="badge">Zakup: {{ d.purchase_date }}</span>
              </div>
            </div>
  
            <div class="shrink-0 text-right text-xs text-slate-400">
              <div>ID: {{ d.id }}</div>
              <div v-if="d.updated_at" class="mt-1">Akt.: {{ fmtDateTime(d.updated_at) }}</div>
            </div>
          </div>
        </RouterLink>
      </div>
    </div>
  </template>
  
  <script setup>
  import { computed, onMounted, ref } from "vue";
  import api from "../services/api";
  
  const devices = ref([]);
  const loading = ref(false);
  const error = ref("");
  const q = ref("");
  
  function subtitle(d) {
    const parts = [];
    if (d.brand) parts.push(d.brand);
    if (d.model) parts.push(d.model);
    if (d.serial_number) parts.push(`S/N ${d.serial_number}`);
    return parts.length ? parts.join(" • ") : "Brak dodatkowych informacji";
  }
  
  function fmtDateTime(value) {
    // value może być "2026-01-25 11:00:00" albo ISO
    const s = String(value).replace(" ", "T");
    const dt = new Date(s);
    if (Number.isNaN(dt.getTime())) return value;
    return dt.toLocaleString("pl-PL");
  }
  
  const filtered = computed(() => {
    const needle = q.value.trim().toLowerCase();
    if (!needle) return devices.value;
  
    return devices.value.filter((d) => {
      const hay = [
        d.name,
        d.brand,
        d.model,
        d.serial_number,
        d.purchase_date,
        d.notes,
        d.id,
      ]
        .filter(Boolean)
        .join(" ")
        .toLowerCase();
  
      return hay.includes(needle);
    });
  });
  
  async function fetchDevices() {
    loading.value = true;
    error.value = "";
    try {
      const res = await api.get("/api/devices");
      devices.value = Array.isArray(res.data) ? res.data : (res.data?.data ?? []);
    } catch (e) {
      error.value = e?.response?.data?.message || e?.message || "Nie udało się pobrać urządzeń.";
    } finally {
      loading.value = false;
    }
  }
  
  onMounted(fetchDevices);
  </script>
  
  <style scoped>
  .card {
    @apply rounded-2xl bg-white/5 border border-slate-800/60 shadow-sm;
  }
  .btn {
    @apply px-3 py-2 rounded-xl bg-white/5 border border-slate-800/60 text-slate-200 hover:bg-white/10 transition disabled:opacity-60 disabled:cursor-not-allowed;
  }
  .btn-primary {
    @apply bg-indigo-500/20 border-indigo-400/30 hover:bg-indigo-500/30;
  }
  .input {
    @apply px-3 py-2 rounded-xl bg-slate-950/40 border border-slate-800/60 text-slate-100 placeholder:text-slate-500 outline-none focus:ring-2 focus:ring-indigo-500/30;
  }
  .badge {
    @apply px-2 py-1 rounded-lg bg-white/5 border border-slate-800/60 text-slate-300;
  }
  </style>