import axios from "axios";

export const http = axios.create({
  baseURL: "/",
  withCredentials: true,
});

let csrfReady: Promise<void> | null = null;
export async function ensureCsrf() {
  if (!csrfReady) csrfReady = http.get("/sanctum/csrf-cookie").then(() => {});
  return csrfReady;
}

http.interceptors.response.use(
  r => r,
  err => {
    return Promise.reject(err);
  }
);