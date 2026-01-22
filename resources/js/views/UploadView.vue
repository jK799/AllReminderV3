<template>
  <div class="space-y-6">
    <section class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <h1 class="text-2xl font-semibold tracking-tight">Upload dokumentu</h1>
        <p class="text-slate-400 mt-1">PDF lub zdjęcie. Maks. 20MB. Jedna sztuka na wysyłkę.</p>
      </div>

      <div class="flex gap-2">
        <RouterLink class="btn btn-soft" to="/vehicles/new">+ Pojazd</RouterLink>
        <RouterLink class="btn btn-soft" to="/devices/new">+ Urządzenie</RouterLink>
      </div>
    </section>

    <div v-if="success" class="card border-emerald-500/20 bg-emerald-500/5">
      <div class="card-body text-sm text-emerald-200">
        ✅ Dokument wgrany.
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
          <h2 class="card-title">Formularz uploadu</h2>
          <p class="card-subtitle">Wybierz kategorię i opcjonalnie przypnij dokument.</p>
        </div>
      </div>

      <form class="card-body space-y-5" @submit.prevent="submit">
        <div class="grid gap-4 lg:grid-cols-3">
          <div class="lg:col-span-1">
            <label class="label">Kategoria *</label>
            <select v-model="form.category" class="input">
              <option value="vehicles">Pojazdy</option>
              <option value="devices">Urządzenia</option>
              <option value="general">Ogólne</option>
            </select>
            <p v-if="fieldErr('category')" class="err">{{ fieldErr('category') }}</p>
          </div>

          <div class="lg:col-span-2">
            <label class="label">Tytuł (opcjonalnie)</label>
            <input v-model.trim="form.title" class="input" type="text" placeholder="np. OC 2026 / Faktura / Dowód zakupu" />
            <p v-if="fieldErr('title')" class="err">{{ fieldErr('title') }}</p>
          </div>
        </div>

        <!-- przypięcie -->
        <div v-if="form.category !== 'general'" class="grid gap-4 sm:grid-cols-2">
          <div v-if="form.category === 'vehicles'">
            <label class="label">Przypnij do pojazdu *</label>
            <select v-model="form.vehicle_id" class="input">
              <option value="">— wybierz pojazd —</option>
              <option v-for="v in vehicles" :key="v.id" :value="String(v.id)">
                {{ v.name || ("Pojazd #" + v.id) }}
              </option>
            </select>
            <p class="hint" v-if="vehicles.length === 0">Brak pojazdów — dodaj pojazd i wróć.</p>
            <p v-if="fieldErr('vehicle_id')" class="err">{{ fieldErr('vehicle_id') }}</p>
          </div>

          <div v-if="form.category === 'devices'">
            <label class="label">Przypnij do urządzenia *</label>
            <select v-model="form.device_id" class="input">
              <option value="">— wybierz urządzenie —</option>
              <option v-for="d in devices" :key="d.id" :value="String(d.id)">
                {{ d.name || ("Urządzenie #" + d.id) }}
              </option>
            </select>
            <p class="hint" v-if="devices.length === 0">Brak urządzeń — dodaj urządzenie i wróć.</p>
            <p v-if="fieldErr('device_id')" class="err">{{ fieldErr('device_id') }}</p>
          </div>

          <div>
            <label class="label">Notatki (opcjonalnie)</label>
            <input v-model.trim="form.notes" class="input" type="text" placeholder="np. ważne do 01.02.2026" />
            <p v-if="fieldErr('notes')" class="err">{{ fieldErr('notes') }}</p>
          </div>
        </div>

        <!-- file -->
        <div>
          <label class="label">Plik *</label>
          <input
            ref="fileInput"
            class="file"
            type="file"
            accept=".pdf,image/jpeg,image/png,image/webp"
            @change="onFileChange"
          />
          <div class="mt-2 text-sm text-slate-400">
            <span v-if="fileName">Wybrano: <span class="text-slate-200">{{ fileName }}</span></span>
            <span v-else>PDF/JPG/PNG/WEBP, max 20MB</span>
          </div>
          <p v-if="fieldErr('file')" class="err">{{ fieldErr('file') }}</p>
        </div>

        <div class="flex flex-wrap gap-2">
          <button class="btn" type="submit" :disabled="loading">
            {{ loading ? "Wysyłam..." : "Wyślij dokument" }}
          </button>

          <button class="btn btn-soft" type="button" :disabled="loading" @click="reset">
            Wyczyść
          </button>

          <RouterLink class="btn btn-soft" to="/documents">
            Lista dokumentów →
          </RouterLink>
        </div>

        <div class="text-xs text-slate-500">
          * Backend i tak waliduje: maks 20MB + typ pliku. Jeśli coś nie przejdzie — pokażę błąd z API.
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref, reactive, computed, watch } from "vue";
import api from "../services/api";

const loading = ref(false);
const success = ref(false);
const error = ref("");
const errors = ref({});

const vehicles = ref([]);
const devices = ref([]);

const fileInput = ref(null);
const selectedFile = ref(null);

const form = reactive({
  category: "vehicles", // vehicles | devices | general
  title: "",
  notes: "",
  vehicle_id: "",
  device_id: "",
});

const fileName = computed(() => selectedFile.value?.name || "");

function fieldErr(key) {
  const e = errors.value?.[key];
  return Array.isArray(e) ? e[0] : "";
}

function safeArray(data) {
  return Array.isArray(data) ? data : (data?.data ?? []);
}

async function loadPickers() {
  try {
    const [v, d] = await Promise.all([
      api.get("/api/vehicles"),
      api.get("/api/devices"),
    ]);
    vehicles.value = safeArray(v.data);
    devices.value = safeArray(d.data);
  } catch (_) {
    // nie blokujemy uploadu — co najwyżej nie będzie listy do wyboru
  }
}

watch(
  () => form.category,
  (cat) => {
    // pilnujemy spójności przypięcia
    if (cat !== "vehicles") form.vehicle_id = "";
    if (cat !== "devices") form.device_id = "";
    success.value = false;
    error.value = "";
    errors.value = {};
  }
);

function onFileChange(e) {
  success.value = false;
  error.value = "";
  errors.value = {};

  const f = e.target.files?.[0] || null;
  selectedFile.value = f;

  if (!f) return;

  // prosta walidacja klienta (backend i tak waliduje)
  const maxBytes = 20 * 1024 * 1024; // 20MB
  const okTypes = ["application/pdf", "image/jpeg", "image/png", "image/webp"];

  if (f.size > maxBytes) {
    selectedFile.value = null;
    if (fileInput.value) fileInput.value.value = "";
    errors.value = { file: ["Plik jest za duży (max 20MB)."] };
    return;
  }

  if (!okTypes.includes(f.type)) {
    selectedFile.value = null;
    if (fileInput.value) fileInput.value.value = "";
    errors.value = { file: ["Dozwolone formaty: PDF / JPG / PNG / WEBP."] };
    return;
  }
}

function reset() {
  form.category = "vehicles";
  form.title = "";
  form.notes = "";
  form.vehicle_id = "";
  form.device_id = "";
  selectedFile.value = null;
  if (fileInput.value) fileInput.value.value = "";
  errors.value = {};
  error.value = "";
  success.value = false;
}

function validateClient() {
  const e = {};

  if (!form.category) e.category = ["Kategoria jest wymagana."];
  if (!selectedFile.value) e.file = ["Wybierz plik."];

  if (form.category === "vehicles" && !form.vehicle_id) {
    e.vehicle_id = ["Wybierz pojazd (albo zmień kategorię na Ogólne)."];
  }
  if (form.category === "devices" && !form.device_id) {
    e.device_id = ["Wybierz urządzenie (albo zmień kategorię na Ogólne)."];
  }

  errors.value = e;
  return Object.keys(e).length === 0;
}

async function submit() {
  success.value = false;
  error.value = "";
  errors.value = {};

  if (!validateClient()) return;

  loading.value = true;
  try {
    const fd = new FormData();
    fd.append("file", selectedFile.value);
    fd.append("category", form.category);

    if (form.title.trim()) fd.append("title", form.title.trim());
    if (form.notes.trim()) fd.append("notes", form.notes.trim());

    if (form.category === "vehicles") fd.append("vehicle_id", form.vehicle_id);
    if (form.category === "devices") fd.append("device_id", form.device_id);

    await api.post("/api/documents/upload", fd, {
      headers: { "Content-Type": "multipart/form-data" },
    });

    success.value = true;
    reset();
    success.value = true;
  } catch (e) {
    if (e?.response?.status === 422) {
      errors.value = e.response.data.errors || {};
      error.value = e.response.data.message || "Popraw pola formularza.";
    } else {
      error.value = e?.response?.data?.message || e?.message || "Błąd uploadu dokumentu.";
    }
  } finally {
    loading.value = false;
  }
}

onMounted(loadPickers);
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
.hint { @apply text-xs text-slate-500 mt-1; }

.file { @apply block w-full rounded-2xl border border-slate-800/60 bg-slate-950/40 px-4 py-2.5 text-sm text-slate-200 file:mr-4 file:rounded-xl file:border-0 file:bg-white/10 file:px-4 file:py-2 file:text-sm file:font-medium file:text-slate-100 hover:file:bg-white/15; }

.btn { @apply px-4 py-2 rounded-2xl bg-white/10 hover:bg-white/15 border border-slate-800/60 transition text-sm font-medium; }
.btn-soft { @apply bg-white/5 hover:bg-white/10; }
</style>
