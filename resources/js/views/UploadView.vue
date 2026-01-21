<template>
    <div>
      <h1 class="text-2xl font-semibold">Upload</h1>
      <p class="mt-1 text-sm text-zinc-400">
        Wysyłamy 1 plik na request. Dozwolone: jpg/jpeg/png/webp/pdf. Limit: 20MB.
      </p>
  
      <div class="mt-6 rounded-2xl border border-zinc-800 bg-zinc-900 p-4">
        <label class="block text-sm text-zinc-300">Kategoria</label>
        <select v-model="category" class="inp mt-1">
          <option value="vehicles">Pojazdy</option>
          <option value="devices">Urządzenia</option>
          <option value="general">Ogólne</option>
        </select>
  
        <label class="mt-3 block text-sm text-zinc-300">Tytuł (opcjonalnie)</label>
        <input v-model="title" class="inp mt-1" type="text" />
  
        <label class="mt-3 block text-sm text-zinc-300">Notatka (opcjonalnie)</label>
        <input v-model="notes" class="inp mt-1" type="text" />
  
        <label class="mt-3 block text-sm text-zinc-300">Plik</label>
        <input class="mt-1 block w-full text-sm" type="file" @change="onFile" />
  
        <div class="mt-4 flex gap-2">
          <button class="btn" @click="upload" :disabled="loading || !file">
            {{ loading ? "Wysyłam..." : "Wyślij" }}
          </button>
        </div>
  
        <p v-if="error" class="mt-3 text-sm text-red-400">{{ error }}</p>
  
        <pre v-if="result" class="mt-4 overflow-auto rounded-xl bg-zinc-950 p-3 text-xs text-zinc-200">{{ result }}</pre>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref } from "vue";
  import api from "../api";
  
  const category = ref("general");
  const title = ref("");
  const notes = ref("");
  const file = ref(null);
  
  const loading = ref(false);
  const error = ref("");
  const result = ref("");
  
  function onFile(e) {
    file.value = e.target.files?.[0] || null;
  }
  
  async function upload() {
    error.value = "";
    result.value = "";
    loading.value = true;
  
    try {
      const fd = new FormData();
      fd.append("file", file.value);
      fd.append("category", category.value);
      if (title.value) fd.append("title", title.value);
      if (notes.value) fd.append("notes", notes.value);
  
      // backend: POST /api/documents/upload
      const res = await api.post("/documents/upload", fd, {
        headers: { "Content-Type": "multipart/form-data" },
      });
  
      result.value = JSON.stringify(res.data, null, 2);
    } catch (e) {
      error.value = e?.response?.data?.message || "Błąd uploadu /api/documents/upload";
    } finally {
      loading.value = false;
    }
  }
  </script>
  
  <style scoped>
  .inp {
    @apply w-full rounded-xl border border-zinc-800 bg-zinc-950 px-3 py-2 text-sm outline-none focus:border-zinc-600;
  }
  .btn {
    @apply rounded-xl border border-zinc-800 bg-zinc-950 px-3 py-2 text-sm hover:bg-zinc-800;
  }
  </style>
  