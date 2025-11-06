import { defineStore } from "pinia";
import { apiGet, apiPost, apiPatch, apiDelete } from "@/lib/api";
import type { Issue } from "@/types";

export const useIssues = defineStore("issues", {
  state: () => ({
    byColumn: new Map<number, Issue[]>(), // key: column_id
  }),
  actions: {
    async loadForProject(projectId: number, opts: { column_id?: number } = {}) {
      const data = await apiGet<Issue[]>("/issues", { project_id: projectId, ...opts });
      if (opts.column_id) this.byColumn.set(opts.column_id, data);
    },
    issuesIn(columnId: number) { return this.byColumn.get(columnId) || []; },

    async create(issue: Partial<Issue>) {
      const created = await apiPost<Issue>("/issues", issue);
      if (created.column_id) {
        const list = this.issuesIn(created.column_id).slice();
        list.push(created);
        this.byColumn.set(created.column_id, list);
      }
      return created;
    },
    async update(id: number, patch: Partial<Issue>) {
      const updated = await apiPatch<Issue>(`/issues/${id}`, patch);
      return updated;
    },
    async move(id: number, toColumnId: number, position?: number) {
      const updated = await apiPatch<Issue>(`/issues/${id}/move`, { column_id: toColumnId, position });
      return updated;
    },
    async destroy(id: number, columnId?: number) {
      await apiDelete(`/issues/${id}`);
      if (columnId) this.byColumn.set(columnId, this.issuesIn(columnId).filter(i => i.id !== id));
    }
  }
});