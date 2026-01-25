<template>
    <div class="space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-semibold">Dodaj przypomnienie</h1>
          <p class="text-slate-400 mt-1">Przypomnienie może być ogólne albo przypięte do pojazdu/urządzenia.</p>
        </div>
        <RouterLink class="btn" to="/dashboard">Powrót</RouterLink>
      </div>
  
      <div class="card">
        <form class="grid gap-4" @submit.prevent="onSubmit">
          <div v-if="error" class="alert alert-red">{{ error }}</div>
          <div v-if="success" class="alert alert-green">{{ success }}</div>
  
          <div class="grid gap-2">
            <label class="label">Tytuł *</label>
            <input v-model.trim="form.title" class="input" placeholder="np. Opłacić OC" required />
          </div>
  
          <div class="grid gap-2">
            <label class="label">Opis</label>
            <textarea v-model.trim="form.description" class="textarea" rows="3" placeholder="Szczegóły przypomnienia..."></textarea>
          </div>
  
          <div class="grid md:grid-cols-2 gap-4">
            <div class="grid gap-2">
              <label class="label">Pojazd (opcjonalnie)</label>
              <select class="select" v-model="form.vehicle_id" @change="onVehiclePicked">
                <option :value="null">— brak —</option>
                <option v-for="v in vehicles" :key="v.id" :value="v.id">
                  {{ v.name }}
                </option>
              </select>
            </div>
  
            <div class="grid gap-2">
              <label class="label">Urządzenie (opcjonalnie)</label>
              <select class="select" v-model="form.device_id" @change="onDevicePicked">
                <option :value="null">— brak —</option>
                <option v-for="d in devices" :key="d.id" :value="d.id">
                  {{ d.name }}
                </option>
              </select>
            </div>
          </div>
  
          <div class="grid md:grid-cols-3 gap-4">
            <div class="grid gap-2">
              <label class="label">Termin (due_at)</label>
              <input type="datetime-local" class="input" v-model="form.due_at" />
            </div>
            <div class="grid gap-2">
              <label class="label">Przypomnij (remind_at)</label>
              <input type="datetime-local" class="input" v-model="form.remind_at" />
            </div>
            <div class="grid gap-2">
              <label class="label">Aktywne</label>
              <select class="select" v-model.number="form.is_active">
                <option :value="1">Tak</option>
                <option :value="0">Nie</option>
              </select>
            </div>
          </div>
  
          <div class="flex items-center gap-3 pt-2">
            <button class="btn btn-primary" :disabled="loading">
              <span v-if="loading">Zapisywanie...</span>
              <span v-else>Zapisz przypomnienie</span>
            </button>
            <button class="btn" type="button" @click="reset">Wyczyść</button>
          </div>
        </form>
      </div>
    </div>
  </template>
  
  <script setup>
  import { onMounted, reactive, ref } from "vue";
  import { useRouter } from "vue-router";
  import api from "../services/api";
  
  const router = useRouter();
  
  const loading = ref(false);
  const error = ref("");
  const success = ref("");
  
  const vehicles = ref([]);
  const devices = ref([]);
  
  const form = reactive({
    title: "",
    description: "",
    vehicle_id: null,
    device_id: null,
    due_at: "",
    remind_at: "",
    is_active: 1,
  });
  
  function reset() {
    error.value = "";
    success.value = "";
    Object.assign(form, {
      title: "",
      description: "",
      vehicle_id: null,
      device_id: null,
      due_at: "",
      remind_at: "",
      is_active: 1,
    });
  }
  
  function extractError(e) {
    const msg = e?.response?.data?.message;
    const errors = e?.response?.data?.errors;
    if (errors) {
      const firstKey = Object.keys(errors)[0];
      if (firstKey) return errors[firstKey]?.[0] ?? msg ?? "Błąd walidacji.";
    }
    return msg ?? "Wystąpił błąd.";
  }
  
  function onVehiclePicked() {
    if (form.vehicle_id) form.device_id = null;
  }
  function onDevicePicked() {
    if (form.device_id) form.vehicle_id = null;
  }
  
  async function loadLists() {
    try {
      const [vRes, dRes] = await Promise.all([
        api.get("/api/vehicles"),
        api.get("/api/devices"),
      ]);
      vehicles.value = Array.isArray(vRes.data) ? vRes.data : (vRes.data?.data ?? []);
      devices.value = Array.isArray(dRes.data) ? dRes.data : (dRes.data?.data ?? []);
    } catch (_) {}
  }
  
  onMounted(loadLists);
  
  // datetime-local -> backend (Laravel) spokojnie przyjmie "YYYY-MM-DDTHH:mm"
  function toNullable(v) {
    return v && String(v).trim() !== "" ? v : null;
  }
  
  async function onSubmit() {
    error.value = "";
    success.value = "";
  
    if (!form.title) {
      error.value = "Tytuł jest wymagany.";
      return;
    }
  
    loading.value = true;
    try {
      await api.post("/api/reminders", {
        title: form.title,
        description: toNullable(form.description),
        vehicle_id: form.vehicle_id || null,
        device_id: form.device_id || null,
        due_at: toNullable(form.due_at),
        remind_at: toNullable(form.remind_at),
        is_active: form.is_active ? 1 : 0,
      });
  
      success.value = "Przypomnienie dodane ✅";
      setTimeout(() => router.push("/dashboard"), 500);
    } catch (e) {
      error.value = extractError(e);
    } finally {
      loading.value = false;
    }
  }
  </script>
  
  <style scoped>
  .card { @apply bg-slate-900/40 border border-slate-800/60 rounded-2xl p-6; }
  .label { @apply text-sm text-slate-300; }
  .input { @apply w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800/70 text-slate-100 placeholder:text-slate-600 focus:outline-none focus:ring-2 focus:ring-slate-700; }
  .textarea { @apply w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800/70 text-slate-100 placeholder:text-slate-600 focus:outline-none focus:ring-2 focus:ring-slate-700; }
  .select { @apply w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800/70 text-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-700; }
  .btn { @apply px-4 py-2 rounded-xl bg-slate-800/70 hover:bg-slate-700/70 transition text-slate-100; }
  .btn-primary { @apply bg-indigo-600 hover:bg-indigo-500; }
  .alert { @apply rounded-xl px-4 py-3 text-sm border; }
  .alert-red { @apply bg-red-500/10 border-red-500/30 text-red-200; }
  .alert-green { @apply bg-emerald-500/10 border-emerald-500/30 text-emerald-200; }
  </style>