<template>
    <div class="max-w-xl space-y-6">
      <h1 class="text-2xl font-semibold">Upload dokumentu</h1>
  
      <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6 space-y-4">
        <div>
          <label class="block text-sm text-zinc-300 mb-1">Kategoria</label>
          <select v-model="category" class="w-full px-3 py-2 rounded-lg bg-zinc-950 border border-zinc-800">
            <option value="vehicles">Pojazdy</option>
            <option value="devices">Urządzenia</option>
            <option value="general">Ogólne</option>
          </select>
        </div>
  
        <div v-if="category === 'vehicles'">
          <label class="block text-sm text-zinc-300 mb-1">vehicle_id</label>
          <input v-model="vehicleId" type="number"
            class="w-full px-3 py-2 rounded-lg bg-zinc-950 border border-zinc-800" />
          <p class="text-xs text-zinc-400 mt-1">Na razie ręcznie (minimum logiki). Potem zrobimy select.</p>
        </div>
  
        <div v-if="category === 'devices'">
          <label class="block text-sm text-zinc-300 mb-1">device_id</label>
          <input v-model="deviceId" type="number"
            class="w-full px-3 py-2 rounded-lg bg-zinc-950 border border-zinc-800" />
          <p class="text-xs text-zinc-400 mt-1">Na razie ręcznie. Potem select.</p>
        </div>
  
        <div>
          <label class="block text-sm text-zinc-300 mb-1">Tytuł (opcjonalnie)</label>
          <input v-model="title" type="text"
            class="w-full px-3 py-2 rounded-lg bg-zinc-950 border border-zinc-800" />
        </div>
  
        <div>
          <label class="block text-sm text-zinc-300 mb-1">Notatki (opcjonalnie)</label>
          <textarea v-model="notes" rows="3"
            class="w-full px-3 py-2 rounded-lg bg-zinc-950 border border-zinc-800"></textarea>
        </div>
  
        <div>
          <label class="block text-sm text-zinc-300 mb-1">Plik (jpg/png/webp/pdf) max 20MB</label>
          <input @change="onFile" type="file" accept=".jpg,.jpeg,.png,.webp,.pdf"
            class="block w-full text-sm text-zinc-300" />
        </div>
  
        <button @click="upload" :disabled="loading"
          class="w-full px-4 py-2 rounded-lg bg-white text-zinc-900 font-medium hover:bg-zinc-200 disabled:opacity-60">
          {{ loading ? "Wysyłanie..." : "Wyślij" }}
        </button>
  
        <p v-if="error" class="text-sm text-red-400">{{ error }}</p>
        <p v-if="success" class="text-sm text-green-400">{{ success }}</p>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, watch } from "vue";
  import { apiFetch } from "../api";
  
  const category = ref("general");
  const vehicleId = ref("");
  const deviceId = ref("");
  const title = ref("");
  const notes = ref("");
  const file = ref(null);
  
  const loading = ref(false);
  const error = ref("");
  const success = ref("");
  
  watch(category, () => {
    if (category.value !== "vehicles") vehicleId.value = "";
    if (category.value !== "devices") deviceId.value = "";
    error.value = "";
    success.value = "";
  });
  
  function onFile(e) {
    file.value = e.target.files?.[0] ?? null;
  }
  
  async function upload() {
    error.value = "";
    success.value = "";
  
    if (!file.value) {
      error.value = "Wybierz plik.";
      return;
    }
  
    const fd = new FormData();
    fd.append("file", file.value);
    fd.append("category", category.value);
  
    if (title.value) fd.append("title", title.value);
    if (notes.value) fd.append("notes", notes.value);
  
    if (category.value === "vehicles") fd.append("vehicle_id", String(vehicleId.value || ""));
    if (category.value === "devices") fd.append("device_id", String(deviceId.value || ""));
  
    loading.value = true;
    try {
      await apiFetch("/api/documents/upload", { method: "POST", body: fd });
      success.value = "Dokument dodany ✅";
      title.value = "";
      notes.value = "";
      file.value = null;
    } catch (e) {
      error.value = e.message || "Błąd uploadu";
    } finally {
      loading.value = false;
    }
  }
  </script>
  