<template>
    <div class="space-y-6">
      <section class="flex items-end justify-between gap-3">
        <div>
          <h1 class="text-2xl font-semibold tracking-tight">Dodaj urządzenie</h1>
          <p class="text-slate-400 mt-1">Minimalnie: nazwa. Reszta opcjonalna.</p>
        </div>
  
        <RouterLink class="btn btn-soft" to="/dashboard">← Dashboard</RouterLink>
      </section>
  
      <div v-if="success" class="card border-emerald-500/20 bg-emerald-500/5">
        <div class="card-body text-sm text-emerald-200">
          ✅ Urządzenie zapisane.
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
            <h2 class="card-title">Formularz urządzenia</h2>
            <p class="card-subtitle">Np. laptop, drukarka, mikser audio, itp.</p>
          </div>
        </div>
  
        <form class="card-body space-y-5" @submit.prevent="submit">
          <div class="grid gap-4 sm:grid-cols-2">
            <div>
              <label class="label">Nazwa *</label>
              <input v-model.trim="form.name" class="input" type="text" placeholder="np. MacBook Air M2" />
              <p v-if="fieldErr('name')" class="err">{{ fieldErr('name') }}</p>
            </div>
  
            <div>
              <label class="label">Typ</label>
              <input v-model.trim="form.type" class="input" type="text" placeholder="np. laptop / drukarka / inne" />
              <p v-if="fieldErr('type')" class="err">{{ fieldErr('type') }}</p>
            </div>
  
            <div>
              <label class="label">Producent</label>
              <input v-model.trim="form.brand" class="input" type="text" placeholder="np. Apple" />
              <p v-if="fieldErr('brand')" class="err">{{ fieldErr('brand') }}</p>
            </div>
  
            <div>
              <label class="label">Numer seryjny</label>
              <input v-model.trim="form.serial_number" class="input" type="text" placeholder="opcjonalnie" />
              <p v-if="fieldErr('serial_number')" class="err">{{ fieldErr('serial_number') }}</p>
            </div>
          </div>
  
          <div>
            <label class="label">Notatki</label>
            <textarea v-model.trim="form.notes" class="input min-h-[110px]" placeholder="np. gdzie kupione, gwarancja..."></textarea>
            <p v-if="fieldErr('notes')" class="err">{{ fieldErr('notes') }}</p>
          </div>
  
          <div class="flex flex-wrap gap-2">
            <button class="btn" type="submit" :disabled="loading">
              {{ loading ? "Zapisuję..." : "Zapisz urządzenie" }}
            </button>
  
            <button class="btn btn-soft" type="button" :disabled="loading" @click="reset">
              Wyczyść
            </button>
  
            <RouterLink class="btn btn-soft" to="/upload">
              Dodaj dokument do urządzenia →
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
    type: "",
    brand: "",
    serial_number: "",
    notes: "",
  });
  
  function reset() {
    form.name = "";
    form.type = "";
    form.brand = "";
    form.serial_number = "";
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
    const payload = {};
    for (const [k, v] of Object.entries(form)) {
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
      await api.post("/api/devices", buildPayload());
      reset();
      success.value = true;
    } catch (e) {
      if (e?.response?.status === 422) {
        errors.value = e.response.data.errors || {};
        error.value = e.response.data.message || "Popraw pola formularza.";
      } else {
        error.value = e?.response?.data?.message || e?.message || "Błąd zapisu urządzenia.";
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
  