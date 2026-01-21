<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold">Dokumenty</h1>
      <RouterLink to="/upload" class="px-4 py-2 rounded-lg bg-white text-zinc-900 font-medium hover:bg-zinc-200">
        Dodaj dokument
      </RouterLink>
    </div>

    <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-4">
      <div class="flex items-center gap-2 mb-4">
        <button v-for="c in categories" :key="c" @click="filter = c"
          class="px-3 py-2 rounded-lg border"
          :class="filter === c ? 'bg-zinc-800 border-zinc-700' : 'bg-zinc-950 border-zinc-800 hover:bg-zinc-900'">
          {{ label(c) }}
        </button>

        <button @click="load" class="ml-auto px-3 py-2 rounded-lg bg-zinc-800 hover:bg-zinc-700">
          Odśwież
        </button>
      </div>

      <p v-if="loading" class="text-zinc-300">Ładowanie...</p>
      <p v-else-if="error" class="text-red-400">{{ error }}</p>

      <div v-else class="space-y-2">
        <div v-if="filtered.length === 0" class="text-zinc-400">Brak dokumentów.</div>

        <div v-for="doc in filtered" :key="doc.id"
          class="p-3 rounded-xl bg-zinc-950 border border-zinc-800 flex items-start justify-between gap-4">
          <div class="min-w-0">
            <div class="font-medium truncate">{{ doc.title ?? doc.original_name }}</div>
            <div class="text-xs text-zinc-400">
              {{ doc.mime_type }} • {{ formatSize(doc.size) }}
            </div>
            <div class="text-xs text-zinc-500 mt-1">{{ doc.original_name }}</div>
          </div>

          <button @click="removeDoc(doc.id)" class="px-3 py-2 rounded-lg bg-zinc-800 hover:bg-red-600">
            Usuń
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from "vue";
import { apiFetch } from "../api";

const docs = ref([]);
const loading = ref(false);
const error = ref("");
const filter = ref("all");

const categories = ["all", "vehicles", "devices", "general"];

function label(c) {
  if (c === "all") return "Wszystkie";
  if (c === "vehicles") return "Pojazdy";
  if (c === "devices") return "Urządzenia";
  return "Ogólne";
}

// Filtr: zakładam, że backend zwraca `documentables` (morph pivoty).
const filtered = computed(() => {
  if (filter.value === "all") return docs.value;

  return docs.value.filter((d) => {
    const list = d.documentables ?? [];
    if (filter.value === "general") return list.length === 0;

    return list.some((x) => {
      const type = (x.documentable_type ?? "").toLowerCase();
      if (filter.value === "vehicles") return type.includes("vehicle");
      if (filter.value === "devices") return type.includes("device");
      return false;
    });
  });
});

function formatSize(bytes) {
  if (bytes === null || bytes === undefined) return "-";
  const kb = bytes / 1024;
  if (kb < 1024) return `${kb.toFixed(0)} KB`;
  return `${(kb / 1024).toFixed(1)} MB`;
}

async function load() {
  loading.value = true;
  error.value = "";
  try {
    docs.value = await apiFetch("/api/documents");
  } catch (e) {
    error.value = e.message || "Błąd pobierania dokumentów";
  } finally {
    loading.value = false;
  }
}

async function removeDoc(id) {
  if (!confirm("Na pewno usunąć dokument?")) return;
  try {
    await apiFetch(`/api/documents/${id}`, { method: "DELETE" });
    docs.value = docs.value.filter((d) => d.id !== id);
  } catch (e) {
    alert(e.message || "Błąd usuwania");
  }
}

onMounted(load);
</script>
