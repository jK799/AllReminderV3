<template>
  <div class="space-y-6">
    <section class="flex items-end justify-between gap-4">
      <div>
        <h1 class="text-2xl font-semibold tracking-tight">Dokumenty</h1>
        <p class="text-slate-400 mt-1">
          Wszystkie dokumenty przypisane do Twojego konta.
        </p>
      </div>

      <RouterLink to="/upload" class="btn">
        ➕ Dodaj dokument
      </RouterLink>
    </section>

    <div v-if="error" class="card border-red-500/20 bg-red-500/5">
      <div class="card-body text-red-200 text-sm">
        {{ error }}
      </div>
    </div>

    <div v-if="loading" class="card">
      <div class="card-body text-slate-400 text-sm">
        Ładowanie dokumentów…
      </div>
    </div>

    <div v-if="!loading && documents.length === 0" class="card">
      <div class="card-body text-slate-400 text-sm">
        Brak dokumentów. Dodaj pierwszy dokument.
      </div>
    </div>

    <div v-if="!loading && documents.length" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
      <div
        v-for="doc in documents"
        :key="doc.id"
        class="card hover:border-slate-700 transition"
      >
        <div class="card-body space-y-3">
          <div class="flex items-start justify-between gap-3">
            <div>
              <h3 class="font-medium text-slate-100">
                {{ doc.title || doc.original_name || "Dokument" }}
              </h3>
              <p class="text-xs text-slate-400 mt-1">
                {{ formatDate(doc.created_at) }}
              </p>
            </div>

            <span
              v-if="doc.vehicle"
              class="badge"
            >
              🚗 {{ doc.vehicle.name }}
            </span>

            <span
              v-else-if="doc.device"
              class="badge"
            >
              💻 {{ doc.device.name }}
            </span>
          </div>

          <div class="flex items-center justify-between gap-2">
            <a
              :href="doc.url"
              target="_blank"
              class="btn btn-soft"
            >
              📄 Otwórz
            </a>

            <button
              class="btn btn-danger"
              @click="remove(doc.id)"
            >
              Usuń
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from "vue";
import api from "../services/api";

const documents = ref([]);
const loading = ref(false);
const error = ref("");

async function fetchDocuments() {
  loading.value = true;
  error.value = "";

  try {
    const res = await api.get("/api/documents");
    documents.value = res.data;
  } catch (e) {
    error.value =
      e?.response?.data?.message ||
      e?.message ||
      "Nie udało się pobrać dokumentów.";
  } finally {
    loading.value = false;
  }
}

async function remove(id) {
  if (!confirm("Czy na pewno chcesz usunąć ten dokument?")) return;

  try {
    await api.delete(`/api/documents/${id}`);
    documents.value = documents.value.filter((d) => d.id !== id);
  } catch (e) {
    alert(
      e?.response?.data?.message ||
        e?.message ||
        "Nie udało się usunąć dokumentu."
    );
  }
}

function formatDate(date) {
  if (!date) return "-";
  return new Date(date).toLocaleString("pl-PL");
}

onMounted(fetchDocuments);
</script>

<style scoped>
.card {
  @apply rounded-3xl border border-slate-800/60 bg-white/5
  shadow-[0_10px_30px_rgba(0,0,0,0.25)];
}

.card-body {
  @apply px-5 py-5;
}

.btn {
  @apply inline-flex items-center gap-2 px-4 py-2 rounded-2xl
  bg-white/10 hover:bg-white/15
  border border-slate-800/60
  transition text-sm font-medium;
}

.btn-soft {
  @apply bg-white/5 hover:bg-white/10;
}

.btn-danger {
  @apply bg-red-500/10 hover:bg-red-500/20
  border-red-500/30 text-red-200;
}

.badge {
  @apply text-xs px-2 py-1 rounded-xl
  bg-white/10 text-slate-200 whitespace-nowrap;
}
</style>