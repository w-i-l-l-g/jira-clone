import { defineStore } from "pinia";
import { apiGet } from "@/lib/api";
import type { Organization, Project } from "@/types";

export const useOrg = defineStore("org", {
  state: () => ({
    orgs: [] as Organization[],
    projects: [] as Project[],
    currentOrgId: null as number|null,
    currentProjectId: null as number|null,
  }),
  getters: {
    currentOrg(s) { return s.orgs.find(o => o.id === s.currentOrgId) || null; },
    currentProject(s) { return s.projects.find(p => p.id === s.currentProjectId) || null; },
  },
  actions: {
    async loadOrgs() { this.orgs = await apiGet<Organization[]>("/orgs"); },
    async loadProjects(orgId: number) {
      this.currentOrgId = orgId;
      this.projects = await apiGet<Project[]>("/projects", { organization_id: orgId });
    },
  }
});