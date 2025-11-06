<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { apiGet } from '../../lib/api'

const props = defineProps<{ projectId: number }>()

const loading = ref(true)
const project = ref<any>(null)
const boards = ref<any[]>([])

onMounted(async () => {
  project.value = await apiGet(`/projects/${props.projectId}`)

  const data = await apiGet('/boards', { project_id: props.projectId })
  boards.value = Array.isArray(data) ? data : (data?.data ?? [])

  loading.value = false
})
</script>

<template>
  <Head :title="project?.name ? `Project · ${project.name}` : 'Project'" />
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold text-gray-800">
        {{ project?.name ?? 'Project' }}
      </h2>
    </template>

    <div v-if="loading" class="text-gray-500">Loading…</div>

    <div v-else class="grid gap-3">
      <div class="rounded-lg border bg-white p-4">
        <div class="text-sm text-gray-500">Key: {{ project.key }}</div>
        <div class="text-sm text-gray-500">Issues: {{ project.issues_count ?? '—' }}</div>
      </div>

      <div class="grid gap-3">
        <div class="text-sm font-medium text-gray-700">Boards</div>

        <Link
          v-for="b in boards"
          :key="b.id"
          :href="`/boards/${b.id}`"
          class="rounded-lg border bg-white p-4 flex items-center justify-between
                 hover:bg-gray-50 focus:bg-gray-50 transition
                 focus:outline-none focus:ring-2 focus:ring-indigo-500"
        >
          <div>
            <div class="font-medium">{{ b.name }}</div>
            <div class="text-xs text-gray-500">
              {{ (b.columns || []).length }} columns
            </div>
          </div>
        </Link>
      </div>
    </div>
  </AuthenticatedLayout>
</template>