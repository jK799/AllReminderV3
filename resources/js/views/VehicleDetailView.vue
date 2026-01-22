<template>
    <div class="space-y-6">
      <div class="card">
        <div class="card-header">
          <div>
            <div class="card-title">Pojazd</div>
            <div class="card-subtitle">
              {{ vehicle?.name || `Pojazd #${id}` }}
            </div>
          </div>
          <RouterLink class="btn" to="/dashboard">Wróć</RouterLink>
        </div>
  
        <div v-if="error" class="card-body">
          <div class="text-red-300 text-sm">{{ error }}</div>
        </div>
  
        <div class="card-body" v-else>
          <div class="text-slate-300 text-sm">
            {{ [vehicle?.brand, vehicle?.model].filter(Boolean).join(" ") || "—" }}
          </div>
        </div>
      </div>
  
      <div class="grid gap-4 lg:grid-cols-2">
        <!-- PRZYPOMNIENIA -->
        <div class="card">
          <div class="card-header">
            <div>
              <div class="card-title">Przypomnienia</div>
              <div class="card-subtitle">Tylko dla tego pojazdu</div>
            </div>
            <span class="pill">{{ reminders.length }}</span>
          </div>
  
          <div class="card-body">
            <div v-if="reminders.length === 0" class="text-slate-400 text-sm">
              Brak przypomnień dla tego pojazdu.
            </div>
  
            <ul v-else class="divide-y divide-slate-800/60">
              <li v-for="r in reminders" :key="r.id" class="py-3">
                <div class="font-semibold">{{ r.title }}</div>
                <div class="text-sm text-slate-400">{{ r.description || "—" }}</div>
                <div class="mt-2 flex gap-2 flex-wrap">
                  <span class="tag tag-soft">⏰ {{ formatDateTime(r.due_at || r.remind_at) }}</span>
                  <span class="tag">{{ r.is_active ? "Aktywne" : "Nieaktywne" }}</span>
                </div>
              </li>
            </ul>
          </div>
        </div>
  
        <!-- SERWISY -->
        <div class="card">
          <div class="card-header">
            <div>
              <div class="card-title">Serwisy</div>
              <div class="card-subtitle">Tylko dla tego pojazdu</div>
            </div>
            <span class="pill">{{ services.length }}</span>
          </div>
  
          <div class="card-body">
            <div v-if="services.length === 0" class="text-slate-400 text-sm">
              Brak serwisów dla tego pojazdu.
            </div>
  
            <ul v-else class="divide-y divide-slate-800/60">
              <li v-for="s in services" :key="s.id" class="py-3">
                <div class="font-semibold">{{ s.title }}</div>
                <div class="text-sm text-slate-400">{{ s.description || "—" }}</div>
                <div class="mt-2 flex gap-2 flex-wrap">
                  <span class="tag tag-soft">📅 {{ formatDate(s.next_due_at) }}</span>
                  <span class="tag">{{ s.is_active ? "Aktywne" : "Nieaktywne" }}</span>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>
  
      <div class="card">
        <div class="card-body flex flex-wrap items-center justify-between gap-3">
          <div class="text-sm text-slate-300">
            Dokumenty do pojazdu wrzucisz przez Upload (kategoria: Pojazdy).
          </div>
          <RouterLink class="btn" to="/upload">Upload</RouterLink>
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
  
  const error = ref("");
  
  const vehicles = ref([]);
  const allReminders = ref([]);
  const allServices = ref([]);
  
  const vehicle = computed(() => vehicles.value.find(v => v.id === id) || null);
  
  const reminders = computed(() =>
    allReminders.value
      .filter(r => Number(r.vehicle_id) === id)
      .sort((a, b) => (new Date(a.due_at || a.remind_at) - new Date(b.due_at || b.remind_at)))
  );
  
  const services = computed(() =>
    allServices.value
      .filter(s => Number(s.vehicle_id) === id)
      .sort((a, b) => (new Date(a.next_due_at) - new Date(b.next_due_at)))
  );
  
  function safeArray(data) {
    return Array.isArray(data) ? data : (data?.data ?? []);
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
    }
  }
  
  onMounted(load);
  </script>
  