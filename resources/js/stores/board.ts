import { defineStore } from "pinia";
import { apiGet, apiPost, apiPatch, apiDelete } from "@/lib/api";
import type { Board, Column } from "@/types";

export const useBoards = defineStore("boards", {
  state: () => ({
    boards: [] as Board[],
    columnsByBoard: new Map<number, Column[]>(),
  }),
  actions: {
    async loadBoards(projectId: number) {
      this.boards = await apiGet<Board[]>("/boards", { project_id: projectId });

      this.boards.forEach(b => {
        if (b.columns) this.columnsByBoard.set(b.id, b.columns.slice().sort((a,b)=>a.position-b.position));
      });
    },
    columns(boardId: number) { return this.columnsByBoard.get(boardId) || []; },

    async createColumn(boardId: number, name: string, position?: number) {
      const col = await apiPost<Column>("/columns", { board_id: boardId, name, position });
      const list = this.columns(boardId).slice();
      list.push(col);
      list.sort((a,b)=>a.position-b.position);
      this.columnsByBoard.set(boardId, list);
    },
    async updateColumn(id: number, patch: Partial<Column>, boardId: number) {
      const col = await apiPatch<Column>(`/columns/${id}`, patch);
      const list = this.columns(boardId).map(c => c.id===id ? col : c)
        .sort((a,b)=>a.position-b.position);
      this.columnsByBoard.set(boardId, list);
    },
    async reorderColumns(boardId: number, next: {id:number; position:number}[]) {
      await apiPatch("/columns/reorder", { columns: next });
      const byId = new Map(next.map(n => [n.id, n.position]));
      const list = this.columns(boardId).map(c => ({ ...c, position: byId.get(c.id) ?? c.position }))
        .sort((a,b)=>a.position-b.position);
      this.columnsByBoard.set(boardId, list);
    },
    async deleteColumn(id: number, boardId: number) {
      await apiDelete(`/columns/${id}`);
      this.columnsByBoard.set(boardId, this.columns(boardId).filter(c => c.id !== id));
    },
  }
});