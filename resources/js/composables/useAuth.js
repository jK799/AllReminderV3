<<<<<<< HEAD
import { ref } from "vue";
import api, { setToken } from "../services/api";

const user = ref(JSON.parse(localStorage.getItem("user") || "null"));
const token = ref(localStorage.getItem("token") || null);

function setSession(nextToken, nextUser) {
  token.value = nextToken;
  user.value = nextUser;

  if (nextToken) localStorage.setItem("token", nextToken);
  else localStorage.removeItem("token");

  if (nextUser) localStorage.setItem("user", JSON.stringify(nextUser));
  else localStorage.removeItem("user");

  setToken(nextToken);
}

async function fetchMe() {
  if (!token.value) return null;
  try {
    const res = await api.get("/me");
    setSession(token.value, res.data);
    return res.data;
  } catch (e) {
    // token nieaktualny / wylogowany na backendzie
    setSession(null, null);
=======
import { ref, computed } from "vue";
import api from "../services/api";

// Trzymamy stan w module (singleton) – działa w całej apce bez pinia/vuex
const token = ref(localStorage.getItem("token") || null);
const user = ref(localStorage.getItem("user") ? JSON.parse(localStorage.getItem("user")) : null);

function setToken(newToken) {
  token.value = newToken;
  if (newToken) localStorage.setItem("token", newToken);
  else localStorage.removeItem("token");
}

function setUser(newUser) {
  user.value = newUser;
  if (newUser) localStorage.setItem("user", JSON.stringify(newUser));
  else localStorage.removeItem("user");
}

function clearAuth() {
  setToken(null);
  setUser(null);
}

function applyAuthHeader() {
  if (token.value) {
    api.defaults.headers.common["Authorization"] = `Bearer ${token.value}`;
  } else {
    delete api.defaults.headers.common["Authorization"];
  }
}

// Wołasz to raz przy starcie (lub w router guard), żeby odświeżyć usera po refreshu
async function bootstrapMe() {
  applyAuthHeader();

  if (!token.value) {
    clearAuth();
    return null;
  }

  try {
    const { data } = await api.get("/api/me");
    setUser(data);
    return data;
  } catch (e) {
    // token nie działa / wygasł
    clearAuth();
>>>>>>> 180dcfd (frontend: show logged user in navbar + persist session via localStorage)
    return null;
  }
}

<<<<<<< HEAD
async function login(email, password) {
  // Backend powinien zwracać { token: "...", user: {...} }.
  // Jeśli zwraca inaczej – powiedz, dopasuję w 5 sekund.
  const res = await api.post("/login", { email, password });

  const nextToken = res.data?.token ?? res.data?.access_token ?? null;
  const nextUser = res.data?.user ?? null;

  if (!nextToken) {
    throw new Error("Brak tokena w odpowiedzi z /api/login");
  }

  setSession(nextToken, nextUser);

  // jeśli backend nie zwraca usera – dociągnij /me
  if (!nextUser) await fetchMe();

  return user.value;
}

async function logout() {
  try {
    await api.post("/logout");
  } catch (e) {
    // nawet jeśli backend zwróci błąd, czyścimy frontend
  } finally {
    setSession(null, null);
  }
}

export function useAuth() {
  return {
    user,
    token,
    isLoggedIn: () => !!token.value,
    setSession,
    fetchMe,
=======
// Login i logout (pod Twoje endpointy: /api/login, /api/logout)
async function login(payload) {
  // payload: { email, password }
  const { data } = await api.post("/api/login", payload);

  // UWAGA: dopasowane do najczęstszego formatu:
  // { token: "xxx", user: {...} }
  // Jeśli masz inaczej w AuthController – daj znać, dopasuję w 5 sekund.
  if (data?.token) setToken(data.token);
  if (data?.user) setUser(data.user);

  applyAuthHeader();
  return data;
}

async function logout() {
  applyAuthHeader();
  try {
    await api.post("/api/logout");
  } catch (e) {
    // nawet jak backend zwróci błąd, i tak czyścimy lokalnie
  }
  clearAuth();
  applyAuthHeader();
}

export function useAuth() {
  const isLogged = computed(() => !!token.value);

  return {
    token,
    user,
    isLogged,
    setToken,
    setUser,
    clearAuth,
    applyAuthHeader,
    bootstrapMe,
>>>>>>> 180dcfd (frontend: show logged user in navbar + persist session via localStorage)
    login,
    logout,
  };
}
