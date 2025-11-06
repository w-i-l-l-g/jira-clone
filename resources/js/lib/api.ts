import { http, ensureCsrf } from "./http";

export async function apiGet<T>(url: string, params?: any) {
  await ensureCsrf();
  return http.get<T>(`/api${url}`, { params }).then(r => r.data);
}
export async function apiPost<T>(url: string, data?: any) {
  await ensureCsrf();
  return http.post<T>(`/api${url}`, data).then(r => r.data);
}
export async function apiPut<T>(url: string, data?: any) {
  await ensureCsrf();
  return http.put<T>(`/api${url}`, data).then(r => r.data);
}
export async function apiPatch<T>(url: string, data?: any) {
  await ensureCsrf();
  return http.patch<T>(`/api${url}`, data).then(r => r.data);
}
export async function apiDelete<T>(url: string) {
  await ensureCsrf();
  return http.delete<T>(`/api${url}`).then(r => r.data);
}