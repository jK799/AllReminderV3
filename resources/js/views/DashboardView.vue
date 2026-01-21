<template>
    <div>
      <h1 class="text-2xl font-semibold">Dashboard</h1>
      <p class="mt-1 text-sm text-zinc-400">
        Na razie proste menu — potem tu wrzucimy statystyki i skróty.
      </p>
  
      <div class="mt-6 grid gap-3 md:grid-cols-3">
        <RouterLink class="card" to="/app/documents">📁 Dokumenty</RouterLink>
        <RouterLink class="card" to="/app/upload">⬆️ Upload</RouterLink>
        <a class="card" href="/api/ping" target="_blank">🟢 API Ping</a>
      </div>
    </div>
  </template>
  
  <style scoped>
  .card {
    @apply rounded-2xl border border-zinc-800 bg-zinc-900 p-4 hover:bg-zinc-800;
  }
  </style>
  