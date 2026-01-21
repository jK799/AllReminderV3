import axios from "axios";

const api = axios.create({
  baseURL: "/api",
  headers: {
    Accept: "application/json",
  },
});

// ustaw token globalnie dla requestów
export function setToken(token) {
  if (token) {
    api.defaults.headers.common.Authorization = `Bearer ${token}`;
  } else {
    delete api.defaults.headers.common.Authorization;
  }
}

// odczyt tokena z localStorage na starcie
const savedToken = localStorage.getItem("token");
if (savedToken) setToken(savedToken);

export default api;
