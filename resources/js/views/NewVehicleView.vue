<template>
    <div class="space-y-6">
      <section class="flex items-end justify-between gap-3">
        <div>
          <h1 class="text-2xl font-semibold tracking-tight">Dodaj pojazd</h1>
          <p class="text-slate-400 mt-1">Uzupełnij podstawowe dane. Resztę możesz dopisać później.</p>
        </div>
  
        <RouterLink class="btn btn-soft" to="/dashboard">← Dashboard</RouterLink>
      </section>
  
      <div v-if="success" class="card border-emerald-500/20 bg-emerald-500/5">
        <div class="card-body text-sm text-emerald-200">
          ✅ Pojazd zapisany.
        </div>
      </div>
  
      <div v-if="error" class="card border-red-500/20 bg-red-500/5">
        <div class="card-body text-sm text-red-200">
          {{ error }}
        </div>
      </div>
  
      <div class="card">
        <div class="card-head">
          <div>
            <h2 class="card-title">Formularz pojazdu</h2>
            <p class="card-subtitle">Minimalnie: nazwa. Reszta opcjonalna.</p>
          </div>
        </div>
  
        <form class="card-body space-y-5" @submit.prevent="submit">
          <div class="grid gap-4 sm:grid-cols-2">
            <div>
              <label class="label">Nazwa *</label>
              <input v-model.trim="form.name" class="input" type="text" placeholder="np. BMW E60" />
              <p v-if="fieldErr('name')" class="err">{{ fieldErr('name') }}</p>
            </div>
  
            <div>
              <label class="label">Tablica rejestracyjna</label>
              <input v-model.trim="form.plate" class="input" type="text" placeholder="np. KR 1234A" />
              <p v-if="fieldErr('plate')" class="err">{{ fieldErr('plate') }}</p>
            </div>
  
            <div>
              <label class="label">Marka</label>
              <input v-model.trim="form.brand" class="input" type="text" placeholder="np. BMW" />
              <p v-if="fieldErr('brand')" class="err">{{ fieldErr('brand') }}</p>
            </div>
  
            <div>
              <label class="label">Model</label>
              <input v-model.trim="form.model" class="input" type="text" placeholder="np. 530d" />
              <p v-if="fieldErr('model')" class="err">{{ fieldErr('model') }}</p>
            </div>
  
            <div>
              <label class="label">Rok</label>
              <input v-model.number="form.year" class="input" type="number" min="1900" max="2100" placeholder="np. 2007" />
              <p v-if="fieldErr('year')" class="err">{{ fieldErr('year') }}</p>
            </div>
  
            <div>
              <label class="label">VIN</label>
              <input v-model.trim="form.vin" class="input" type="text" placeholder="opcjonalnie" />
              <p v-if="fieldErr('vin')" class="err">{{ fieldErr('vin') }}</p>
            </div>
          </div>
  
          <div>
            <label class="label">Notatki</label>
            <textarea v-model.trim="form.notes" class="input min-h-[110px]" placeholder="np. historia serwisowa, uwagi..."></textarea>
            <p v-if="fieldErr('notes')" class="err">{{ fieldErr('notes') }}</p>
          </div>
  
          <div class="flex flex-wrap gap-2">
            <button class="btn" type="submit" :disabled="loading">
              {{ loading ? "Zapisuję..." : "Zapisz pojazd" }}
            </button>
  
            <button class="btn btn-soft" type="button" :disabled="loading" @click="reset">
              Wyczyść
            </button>
  
            <RouterLink class="btn btn-soft" to="/upload">
              Dodaj dokument do pojazdu →
            </RouterLink>
          </div>
        </form>
      </div>
    </div>
  </template>
  
  <script setup>
  import { reactive, ref } from "vue";
  import api from "../services/api";
  
  const loading = ref(false);
  const success = ref(false);
  const error = ref("");
  const errors = ref({});
  
  const form = reactive({
    name: "",
    plate: "",
    brand: "",
    model: "",
    year: null,
    vin: "",
    notes: "",
  });
  
  function reset() {
    form.name = "";
    form.plate = "";
    form.brand = "";
    form.model = "";
    form.year = null;
    form.vin = "";
    form.notes = "";
    errors.value = {};
    error.value = "";
    success.value = false;
  }
  
  function fieldErr(key) {
    const e = errors.value?.[key];
    return Array.isArray(e) ? e[0] : "";
  }
  
  function buildPayload() {
    // wysyłamy tylko pola, które mają wartość (bez null/""), żeby backend miał łatwiej
    const payload = {};
    for (const [k, v] of Object.entries(form)) {
      if (v === null) continue;
      if (typeof v === "string" && v.trim() === "") continue;
      payload[k] = v;
    }
    return payload;
  }
  
  async function submit() {
    success.value = false;
    error.value = "";
    errors.value = {};
  
    if (!form.name.trim()) {
      errors.value = { name: ["Nazwa jest wymagana."] };
      return;
    }
  
    loading.value = true;
    try {
      await api.post("/api/vehicles", buildPayload());
      success.value = true;
      reset();
      success.value = true;
    } catch (e) {
      if (e?.response?.status === 422) {
        errors.value = e.response.data.errors || {};
        error.value = e.response.data.message || "Popraw pola formularza.";
      } else {
        error.value = e?.response?.data?.message || e?.message || "Błąd zapisu pojazdu.";
      }
    } finally {
      loading.value = false;
    }
  }
  </script>
  
  <style scoped>
  .card { @apply rounded-3xl border border-slate-800/60 bg-white/5 shadow-[0_10px_30px_rgba(0,0,0,0.25)]; }
  .card-head { @apply px-6 py-5 border-b border-slate-800/60; }
  .card-title { @apply text-lg font-semibold tracking-tight; }
  .card-subtitle { @apply text-sm text-slate-400 mt-1; }
  .card-body { @apply px-6 py-5; }
  
  .label { @apply block text-sm text-slate-300 mb-1; }
  .input { @apply w-full rounded-2xl border border-slate-800/60 bg-slate-950/40 px-4 py-2.5 text-slate-100 placeholder:text-slate-500 outline-none focus:ring-2 focus:ring-white/10; }
  .err { @apply text-xs text-red-300 mt-1; }
  
  .btn { @apply px-4 py-2 rounded-2xl bg-white/10 hover:bg-white/15 border border-slate-800/60 transition text-sm font-medium; }
  .btn-soft { @apply bg-white/5 hover:bg-white/10; }
  </style>
  