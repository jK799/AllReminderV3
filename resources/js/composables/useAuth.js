import { ref } from "vue";
import {
  getStoredUser,
  bootstrapAuth,
  login as apiLogin,
  register as apiRegister,
  logout as apiLogout,
  getToken,
} from "../services/api";

const user = ref(getStoredUser());
const bootstrapped = ref(false);

async function ensureBootstrapped() {
  if (bootstrapped.value) return;
  bootstrapped.value = true;

  const me = await bootstrapAuth();
  user.value = me;
}

export function useAuth() {
  return {
    user,

    isLoggedIn() {
      return !!getToken() && !!user.value;
    },

    async bootstrap() {
      await ensureBootstrapped();
      return user.value;
    },

    async login(email, password) {
      const me = await apiLogin(email, password);
      user.value = me;
      return me;
    },

    async register(payload) {
      const me = await apiRegister(payload);
      user.value = me;
      return me;
    },

    async logout() {
      await apiLogout();
      user.value = null;
    },
  };
}
