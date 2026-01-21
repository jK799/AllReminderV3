<template>
    <div class="min-h-screen">
      <nav class="border-b border-slate-800 bg-slate-900/40">
        <div class="mx-auto max-w-6xl px-4 py-3 flex items-center justify-between">
          <div class="font-semibold tracking-wide">
            AllReminderV3
          </div>
  
          <div class="flex gap-3 text-sm">
            <RouterLink class="hover:text-white text-slate-300" to="/dashboard">Dashboard</RouterLink>
            <RouterLink class="hover:text-white text-slate-300" to="/documents">Dokumenty</RouterLink>
            <RouterLink class="hover:text-white text-slate-300" to="/upload">Upload</RouterLink>
            <RouterLink class="hover:text-white text-slate-300" to="/login">Login</RouterLink>
          </div>
        </div>
      </nav>
  
      <main class="mx-auto max-w-6xl px-4 py-6">
        <RouterView />
      </main>
    </div>
  </template>
  