import axios from "axios";

const api = axios.create({
  baseURL: "", // wołamy po prostu /api/...
  headers: {
    Accept: "application/json",
  },
});

// ====== localStorage keys ======
const TOKEN_KEY = "token";
const USER_KEY = "user";

// ====== TOKEN ======
export function getToken() {
  return localStorage.getItem(TOKEN_KEY);
}

export function setToken(token) {
  localStorage.setItem(TOKEN_KEY, token);
  api.defaults.headers.common.Authorization = `Bearer ${token}`;
}

export function clearToken() {
  localStorage.removeItem(TOKEN_KEY);
  delete api.defaults.headers.common.Authorization;
}

// ====== USER ======
export function getStoredUser() {
  const raw = localStorage.getItem(USER_KEY);
  return raw ? JSON.parse(raw) : null;
}

export function setStoredUser(user) {
  localStorage.setItem(USER_KEY, JSON.stringify(user));
}

export function clearStoredUser() {
  localStorage.removeItem(USER_KEY);
}

// ====== BOOTSTRAP (po odświeżeniu) ======
export async function bootstrapAuth() {
  const token = getToken();
  if (!token) return null;

  api.defaults.headers.common.Authorization = `Bearer ${token}`;

  try {
    const res = await api.get("/api/me");
    setStoredUser(res.data);
    return res.data;
  } catch (e) {
    clearToken();
    clearStoredUser();
    return null;
  }
}

// ====== AUTH HELPERS ======
export async function login(email, password) {
  const res = await api.post("/api/login", { email, password });
  setToken(res.data.token);

  const me = await api.get("/api/me");
  setStoredUser(me.data);

  return me.data;
}

export async function register(payload) {
  const res = await api.post("/api/register", payload);
  setToken(res.data.token);

  const me = await api.get("/api/me");
  setStoredUser(me.data);

  return me.data;
}

export async function logout() {
  try {
    await api.post("/api/logout");
  } catch (_) {
    // ignorujemy
  }
  clearToken();
  clearStoredUser();
}

export default api;
