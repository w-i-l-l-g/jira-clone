<script setup>
import { onMounted, ref, computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { apiGet, apiPost } from '../../lib/api'

const props = defineProps({
  slug: { type: String, required: true }
})

const loading = ref(true)
const org = ref(null)

const projects = computed(() => org.value?.projects ?? [])

onMounted(async () => {
  org.value = await apiGet(`/orgs/${props.slug}`)
  loading.value = false
})

const creating = ref(false)
const form = ref({ name: '', key: '' })
async function createProject() {
  creating.value = true
  try {
    const created = await apiPost('/projects', {
      organization_id: org.value.id,
      name: form.value.name,
      key: form.value.key,
      description: ''
    })
    org.value.projects = [created, ...(org.value.projects ?? [])]
    form.value = { name: '', key: '' }
  } finally {
    creating.value = false
  }
}
</script>

<template>
  <Head :title="org ? `${org.name} · Org` : 'Organization'" />
  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center gap-2">
        <h2 class="text-xl font-semibold text-gray-800">
          {{ org?.name || 'Organization' }}
        </h2>
        <span v-if="org" class="text-xs text-gray-500">/ {{ org.slug }}</span>
      </div>
    </template>

    <div v-if="loading" class="text-gray-500">Loading…</div>

    <div v-else class="grid gap-6">
      <!-- Stats -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="rounded-lg border bg-white p-4">
          <div class="text-sm text-gray-500">Projects</div>
          <div class="text-2xl font-semibold">{{ projects.length }}</div>
        </div>
        <div class="rounded-lg border bg-white p-4">
          <div class="text-sm text-gray-500">Members</div>
          <div class="text-2xl font-semibold">—</div>
        </div>
        <div class="rounded-lg border bg-white p-4">
          <div class="text-sm text-gray-500">Boards</div>
          <div class="text-2xl font-semibold">—</div>
        </div>
      </div>

      <!-- Quick create -->
      <div class="rounded-lg border bg-white p-4">
        <div class="font-medium mb-3">Create Project</div>
        <div class="flex flex-col sm:flex-row gap-2">
          <input v-model="form.name" class="w-full sm:w-1/2 rounded border px-3 py-2" placeholder="Project name" />
          <input v-model="form.key" class="w-full sm:w-40 rounded border px-3 py-2 uppercase" placeholder="KEY" />
          <button
            @click="createProject"
            :disabled="creating || !form.name || !form.key"
            class="rounded bg-indigo-600 px-4 py-2 text-white disabled:opacity-50"
          >
            {{ creating ? 'Creating…' : 'Create' }}
          </button>
        </div>
      </div>

      <!-- Projects list -->
      <div class="rounded-lg border bg-white">
        <div class="px-4 py-3 border-b font-medium">Projects</div>
        <div v-if="projects.length === 0" class="p-4 text-gray-500">No projects yet.</div>
        <ul class="divide-y">
            <li v-for="p in projects" :key="p.id">
                <Link
                :href="`/projects/${p.id}`"
                class="block px-4 py-3 hover:bg-gray-50 focus:bg-gray-50 rounded-md
                        outline-none focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"
                >
                <div class="flex items-center justify-between">
                    <div class="font-medium">
                    {{ p.name }}
                    <span class="text-gray-400">({{ p.key }})</span>
                    </div>
                    <div class="text-sm text-gray-500">
                    Issues: {{ p.issues_count ?? 0 }}
                    </div>
                </div>
                </Link>
            </li>
        </ul>
      </div>
    </div>
  </AuthenticatedLayout>
</template>