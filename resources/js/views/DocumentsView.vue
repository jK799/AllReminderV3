<template>
  <div>
    <h1 class="text-2xl font-semibold">Dokumenty</h1>
    <p class="mt-1 text-sm text-zinc-400">
      Tu docelowo pokażemy listę dokumentów (GET /api/documents).
    </p>

    <div class="mt-6 rounded-2xl border border-zinc-800 bg-zinc-900 p-4">
      <button class="btn" @click="load" :disabled="loading">
        {{ loading ? "Ładuję..." : "Pobierz dokumenty" }}
      </button>

      <p v-if="error" class="mt-3 text-sm text-red-400">{{ error }}</p>

      <pre v-if="docs" class="mt-4 overflow-auto rounded-xl bg-zinc-950 p-3 text-xs text-zinc-200">{{ docs }}</pre>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import api from "../api";

const loading = ref(false);
const error = ref("");
const docs = ref("");

async function load() {
  loading.value = true;
  error.value = "";
  docs.value = "";

  try {
    const res = await api.get("/documents");
    docs.value = JSON.stringify(res.data, null, 2);
  } catch (e) {
    error.value = e?.response?.data?.message || "Błąd pobierania /api/documents";
  } finally {
    loading.value = false;
  }
}
</script>

<style scoped>
.btn {
  @apply rounded-xl border border-zinc-800 bg-zinc-950 px-3 py-2 text-sm hover:bg-zinc-800;
}
</style>
