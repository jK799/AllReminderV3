<template>
    <div class="min-h-screen p-6">
      <div class="max-w-5xl mx-auto">
        <div class="flex items-center justify-between gap-4">
          <div>
            <h1 class="text-2xl font-semibold">Dokumenty</h1>
            <p class="text-sm text-gray-400">Wirtualna teczka: pojazdy, urządzenia, ogólne.</p>
          </div>
  
          <button
            @click="logout"
            class="px-4 py-2 rounded-xl bg-gray-900 border border-gray-800 hover:bg-gray-800"
          >
            Wyloguj
          </button>
        </div>
  
        <!-- Upload -->
        <div class="mt-6 bg-gray-900 border border-gray-800 rounded-2xl p-4">
          <div class="grid md:grid-cols-3 gap-3">
            <select v-model="category" class="px-4 py-3 rounded-xl bg-gray-950 border border-gray-800">
              <option value="general">Ogólne</option>
              <option value="vehicle">Pojazdy</option>
              <option value="device">Urządzenia</option>
            </select>
  
            <input
              v-model="title"
              type="text"
              placeholder="Tytuł (opcjonalnie)"
              class="px-4 py-3 rounded-xl bg-gray-950 border border-gray-800"
            />
  
            <input
              v-model="notes"
              type="text"
              placeholder="Notatka (opcjonalnie)"
              class="px-4 py-3 rounded-xl bg-gray-950 border border-gray-800"
            />
          </div>
  
          <div class="grid md:grid-cols-2 gap-3 mt-3" v-if="category !== 'general'">
            <input
              v-if="category === 'vehicle'"
              v-model="vehicleId"
              type="number"
              placeholder="vehicle_id (na teraz ręcznie)"
              class="px-4 py-3 rounded-xl bg-gray-950 border border-gray-800"
            />
            <input
              v-if="category === 'device'"
              v-model="deviceId"
              type="number"
              placeholder="device_id (na teraz ręcznie)"
              class="px-4 py-3 rounded-xl bg-gray-950 border border-gray-800"
            />
            <div class="text-sm text-gray-400 flex items-center">
              (W UI zrobimy dropdowny później, jak domkniemy wygląd)
            </div>
          </div>
  
          <div class="flex flex-col md:flex-row items-start md:items-center gap-3 mt-4">
            <input
              ref="fileInput"
              type="file"
              accept="application/pdf,image/*"
              class="block w-full text-sm text-gray-300
                     file:mr-4 file:py-2 file:px-4 file:rounded-xl
                     file:border-0 file:bg-indigo-600 file:text-white
                     hover:file:bg-indigo-500"
              @change="onFileChange"
            />
  
            <button
              @click="upload"
              :disabled="uploading"
              class="px-5 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 disabled:opacity-60 font-medium"
            >
              {{ uploading ? "Wysyłanie..." : "Wyślij plik" }}
            </button>
          </div>
  
          <p class="text-xs text-gray-400 mt-2">
            Dozwolone: PDF lub zdjęcie. Limit: 20MB. 1 plik na request.
          </p>
  
          <p v-if="error" class="text-sm text-red-400 mt-2">{{ error }}</p>
          <p v-if="success" class="text-sm text-green-400 mt-2">{{ success }}</p>
        </div>
  
        <!-- Lista -->
        <div class="mt-6">
          <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold">Lista</h2>
            <button
              @click="fetchDocs"
              class="px-4 py-2 rounded-xl bg-gray-900 border border-gray-800 hover:bg-gray-800"
            >
              Odśwież
            </button>
          </div>
  
          <div class="mt-3 grid gap-3">
            <div
              v-for="d in documents"
              :key="d.id"
              class="bg-gray-900 border border-gray-800 rounded-2xl p-4 flex items-start justify-between gap-4"
            >
              <div>
                <div class="font-medium">
                  {{ d.title ?? d.original_name }}
                </div>
                <div class="text-sm text-gray-400">
                  {{ d.original_name }} • {{ formatSize(d.size) }} • {{ d.mime_type ?? "" }}
                </div>
                <div class="text-xs text-gray-500 mt-1">
                  ID: {{ d.id }} • {{ d.created_at }}
                </div>
                <div v-if="d.notes" class="text-sm text-gray-300 mt-2">{{ d.notes }}</div>
              </div>
  
              <button
                @click="remove(d.id)"
                class="px-4 py-2 rounded-xl bg-red-600/20 text-red-300 border border-red-600/30 hover:bg-red-600/30"
              >
                Usuń
              </button>
            </div>
  
            <div v-if="documents.length === 0" class="text-sm text-gray-400">
              Brak dokumentów.
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { onMounted, ref } from "vue";
  import { useRouter } from "vue-router";
  import api from "../lib/api";
  
  const router = useRouter();
  
  const documents = ref([]);
  const category = ref("general");
  const title = ref("");
  const notes = ref("");
  const vehicleId = ref("");
  const deviceId = ref("");
  
  const file = ref(null);
  const fileInput = ref(null);
  
  const uploading = ref(false);
  const error = ref("");
  const success = ref("");
  
  function onFileChange(e) {
    error.value = "";
    success.value = "";
    file.value = e.target.files?.[0] ?? null;
  }
  
  function formatSize(bytes) {
    if (!bytes && bytes !== 0) return "-";
    const mb = bytes / (1024 * 1024);
    if (mb >= 1) return `${mb.toFixed(2)} MB`;
    const kb = bytes / 1024;
    return `${kb.toFixed(0)} KB`;
  }
  
  async function fetchDocs() {
    error.value = "";
    try {
      const res = await api.get("/api/documents");
      documents.value = Array.isArray(res.data) ? res.data : (res.data?.data ?? []);
    } catch (e) {
      error.value = e?.response?.data?.message ?? "Nie udało się pobrać dokumentów.";
    }
  }
  
  async function upload() {
    error.value = "";
    success.value = "";
  
    if (!file.value) {
      error.value = "Wybierz plik.";
      return;
    }
  
    // limit 20MB
    const max = 20 * 1024 * 1024;
    if (file.value.size > max) {
      error.value = "Plik jest za duży (max 20MB).";
      return;
    }
  
    // typ: pdf lub obraz
    const okPdf = file.value.type === "application/pdf";
    const okImg = file.value.type?.startsWith("image/");
    if (!okPdf && !okImg) {
      error.value = "Dozwolone są tylko PDF lub zdjęcia.";
      return;
    }
  
    // wymagane ID jeśli podpięte
    if (category.value === "vehicle" && !vehicleId.value) {
      error.value = "Podaj vehicle_id.";
      return;
    }
    if (category.value === "device" && !deviceId.value) {
      error.value = "Podaj device_id.";
      return;
    }
  
    uploading.value = true;
    try {
      const fd = new FormData();
      fd.append("file", file.value);
      if (title.value) fd.append("title", title.value);
      if (notes.value) fd.append("notes", notes.value);
  
      fd.append("category", category.value);
      if (category.value === "vehicle") fd.append("vehicle_id", String(vehicleId.value));
      if (category.value === "device") fd.append("device_id", String(deviceId.value));
  
      await api.post("/api/documents/upload", fd, {
        headers: { "Content-Type": "multipart/form-data" },
      });
  
      success.value = "Plik wysłany ✅";
      if (fileInput.value) fileInput.value.value = "";
      file.value = null;
  
      await fetchDocs();
    } catch (e) {
      error.value = e?.response?.data?.message ?? "Upload nieudany.";
    } finally {
      uploading.value = false;
    }
  }
  
  async function remove(id) {
    error.value = "";
    success.value = "";
    try {
      await api.delete(`/api/documents/${id}`);
      success.value = "Usunięto ✅";
      await fetchDocs();
    } catch (e) {
      error.value = e?.response?.data?.message ?? "Nie udało się usunąć.";
    }
  }
  
  function logout() {
    localStorage.removeItem("token");
    router.push({ name: "login" });
  }
  
  onMounted(fetchDocs);
  </script>
  