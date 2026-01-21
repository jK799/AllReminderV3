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
    return null;
  }
}

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
    login,
    logout,
  };
}
