export type Id = number;

export interface Organization { id: Id; name: string; slug: string; }
export interface Project { id: Id; organization_id: Id; name: string; key: string; description?: string|null; issue_seq: number; }
export interface Board { id: Id; project_id: Id; name: string; columns?: Column[]; }
export interface Column { id: Id; board_id: Id; name: string; position: number; }
export interface Issue {
  id: Id; project_id: Id; column_id: Id|null;
  reporter_id?: Id|null; assignee_id?: Id|null;
  key: string; title: string; description?: string|null;
  priority?: string|null; estimate?: number|null;
}
export interface User { id: Id; name: string; email: string; }