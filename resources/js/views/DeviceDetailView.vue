<template>
    <div class="space-y-6">
      <div class="card">
        <div class="card-header">
          <div>
            <div class="card-title">Urządzenie</div>
            <div class="card-subtitle">
              {{ device?.name || `Urządzenie #${id}` }}
            </div>
          </div>
          <RouterLink class="btn" to="/dashboard">Wróć</RouterLink>
        </div>
  
        <div v-if="error" class="card-body">
          <div class="text-red-300 text-sm">{{ error }}</div>
        </div>
  
        <div class="card-body" v-else>
          <div class="text-slate-300 text-sm">
            Typ: {{ device?.type || "—" }}
          </div>
        </div>
      </div>
  
      <div class="card">
        <div class="card-header">
          <div>
            <div class="card-title">Dokumenty</div>
            <div class="card-subtitle">Powiązane z tym urządzeniem</div>
          </div>
          <span class="pill">{{ deviceDocuments.length }}</span>
        </div>
  
        <div class="card-body">
          <div v-if="deviceDocuments.length === 0" class="text-slate-400 text-sm">
            Brak dokumentów dla tego urządzenia.
          </div>
  
          <ul v-else class="divide-y divide-slate-800/60">
            <li v-for="doc in deviceDocuments" :key="doc.id" class="py-3 flex items-start justify-between gap-3">
              <div class="min-w-0">
                <div class="font-semibold truncate">{{ doc.title || doc.original_name || "Dokument" }}</div>
                <div class="text-sm text-slate-400 truncate">
                  {{ doc.notes || doc.mime_type || "—" }}
                </div>
                <div class="mt-2 flex gap-2 flex-wrap">
                  <span class="tag tag-soft">📦 {{ prettySize(doc.size) }}</span>
                  <span class="tag tag-soft">📄 {{ doc.mime_type || "—" }}</span>
                </div>
              </div>
  
              <!-- jeśli masz publiczny link do pliku przez /storage -->
              <a
                v-if="doc.path"
                class="btn shrink-0"
                :href="fileUrl(doc.path)"
                target="_blank"
                rel="noreferrer"
              >
                Otwórz
              </a>
            </li>
          </ul>
        </div>
      </div>
  
      <div class="card">
        <div class="card-body flex flex-wrap items-center justify-between gap-3">
          <div class="text-sm text-slate-300">
            Chcesz dodać dokument do tego urządzenia? Wrzuć przez Upload.
          </div>
          <RouterLink class="btn" to="/upload">Upload</RouterLink>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { onMounted, ref, computed } from "vue";
  import { useRoute } from "vue-router";
  import api from "../services/api";
  
  const route = useRoute();
  const id = Number(route.params.id);
  
  const error = ref("");
  
  const devices = ref([]);
  const documents = ref([]);
  
  const device = computed(() => devices.value.find(d => d.id === id) || null);
  
  function safeArray(data) {
    return Array.isArray(data) ? data : (data?.data ?? []);
  }
  
  /**
   * Filtrowanie dokumentów po urządzeniu:
   * - jeśli API zwraca np. doc.device_id -> działa
   * - jeśli masz pivot documentables -> spróbujemy znaleźć wpis z documentable_id == id i type zawiera "Device"
   */
  const deviceDocuments = computed(() => {
    return documents.value.filter((doc) => {
      // 1) najprostszy przypadek:
      if (Number(doc.device_id) === id) return true;
  
      // 2) pivot w doc.documentables (jeśli backend tak zwraca)
      if (Array.isArray(doc.documentables)) {
        return doc.documentables.some((p) => {
          const type = String(p.documentable_type || "");
          return Number(p.documentable_id) === id && type.toLowerCase().includes("device");
        });
      }
  
      // 3) czasem backend może zwracać pojedyncze pole:
      if (doc.documentable_type && doc.documentable_id) {
        const type = String(doc.documentable_type);
        return Number(doc.documentable_id) === id && type.toLowerCase().includes("device");
      }
  
      return false;
    });
  });
  
  function prettySize(bytes) {
    if (!bytes && bytes !== 0) return "—";
    const b = Number(bytes);
    if (Number.isNaN(b)) return "—";
    if (b < 1024) return `${b} B`;
    if (b < 1024 * 1024) return `${(b / 1024).toFixed(1)} KB`;
    return `${(b / (1024 * 1024)).toFixed(1)} MB`;
  }
  
  function fileUrl(path) {
    // jeśli w DB trzymasz np. "documents/xxx.pdf"
    // i masz storage:link -> /storage/...
    if (path.startsWith("http")) return path;
    return `/storage/${path.replace(/^\/?storage\/?/, "")}`;
  }
  
  async function load() {
    error.value = "";
    try {
      const [d, docs] = await Promise.all([
        api.get("/api/devices"),
        api.get("/api/documents"),
      ]);
  
      devices.value = safeArray(d.data);
      documents.value = safeArray(docs.data);
    } catch (e) {
      error.value = e?.response?.data?.message || e?.message || "Błąd ładowania urządzenia.";
    }
  }
  
  onMounted(load);
  </script>
  