<template>
    <div class="space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-semibold">Dodaj pojazd</h1>
          <p class="text-slate-400 mt-1">Uzupełnij dane pojazdu. Wymagana jest tylko nazwa.</p>
        </div>
        <RouterLink class="btn" to="/dashboard">Powrót</RouterLink>
      </div>
  
      <div class="card">
        <form class="grid gap-4" @submit.prevent="onSubmit">
          <div v-if="error" class="alert alert-red">{{ error }}</div>
          <div v-if="success" class="alert alert-green">{{ success }}</div>
  
          <div class="grid gap-2">
            <label class="label">Nazwa *</label>
            <input v-model.trim="form.name" class="input" placeholder="np. BMW E60" required />
          </div>
  
          <div class="grid md:grid-cols-2 gap-4">
            <div class="grid gap-2">
              <label class="label">Marka</label>
              <input v-model.trim="form.make" class="input" placeholder="np. BMW" />
            </div>
            <div class="grid gap-2">
              <label class="label">Model</label>
              <input v-model.trim="form.model" class="input" placeholder="np. Seria 5" />
            </div>
          </div>
  
          <div class="grid md:grid-cols-2 gap-4">
            <div class="grid gap-2">
              <label class="label">Rok</label>
              <input v-model.trim="form.year" class="input" placeholder="np. 2008" />
            </div>
            <div class="grid gap-2">
              <label class="label">VIN</label>
              <input v-model.trim="form.vin" class="input" placeholder="17 znaków" />
            </div>
          </div>
  
          <div class="grid md:grid-cols-2 gap-4">
            <div class="grid gap-2">
              <label class="label">Nr rejestracyjny</label>
              <input v-model.trim="form.license_plate" class="input" placeholder="np. KR1E60" />
            </div>
            <div class="grid gap-2">
              <label class="label">Data zakupu</label>
              <input v-model="form.purchase_date" type="date" class="input" />
            </div>
          </div>
  
          <div class="grid gap-2">
            <label class="label">Notatki</label>
            <textarea v-model.trim="form.notes" class="textarea" rows="4" placeholder="np. serwis, ubezpieczenie..."></textarea>
          </div>
  
          <div class="flex items-center gap-3 pt-2">
            <button class="btn btn-primary" :disabled="loading">
              <span v-if="loading">Zapisywanie...</span>
              <span v-else>Zapisz pojazd</span>
            </button>
            <button class="btn" type="button" @click="reset">Wyczyść</button>
          </div>
        </form>
      </div>
    </div>
  </template>
  
  <script setup>
  import { reactive, ref } from "vue";
  import { useRouter } from "vue-router";
  import api from "../services/api";
  
  const router = useRouter();
  
  const loading = ref(false);
  const error = ref("");
  const success = ref("");
  
  const form = reactive({
    name: "",
    make: "",
    model: "",
    year: "",
    vin: "",
    license_plate: "",
    purchase_date: "",
    notes: "",
  });
  
  function reset() {
    error.value = "";
    success.value = "";
    Object.assign(form, {
      name: "",
      make: "",
      model: "",
      year: "",
      vin: "",
      license_plate: "",
      purchase_date: "",
      notes: "",
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
  
  async function onSubmit() {
    error.value = "";
    success.value = "";
  
    if (!form.name) {
      error.value = "Nazwa jest wymagana.";
      return;
    }
  
    loading.value = true;
    try {
      await api.post("/api/vehicles", {
        name: form.name,
        make: form.make || null,
        model: form.model || null,
        year: form.year || null,
        vin: form.vin || null,
        license_plate: form.license_plate || null,
        purchase_date: form.purchase_date || null,
        notes: form.notes || null,
      });
  
      success.value = "Pojazd dodany ✅";
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
  .btn { @apply px-4 py-2 rounded-xl bg-slate-800/70 hover:bg-slate-700/70 transition text-slate-100; }
  .btn-primary { @apply bg-indigo-600 hover:bg-indigo-500; }
  .alert { @apply rounded-xl px-4 py-3 text-sm border; }
  .alert-red { @apply bg-red-500/10 border-red-500/30 text-red-200; }
  .alert-green { @apply bg-emerald-500/10 border-emerald-500/30 text-emerald-200; }
  </style>