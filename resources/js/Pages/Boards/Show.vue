<script setup lang="ts">
import { onMounted, ref, computed, reactive, watch } from 'vue'
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { apiGet, apiPatch, apiPost } from '../../lib/api'
import draggable from 'vuedraggable'

const props = defineProps<{ boardId: number }>()

const loading = ref(true)
const board = ref<any>(null)
const columns = ref<any[]>([])
const issues = ref<any[]>([])

// map columnId -> array of issues in that column for vuedraggable
const laneIssues = reactive<Record<number, any[]>>({})

// issue details popup
const detailOpen = ref(false)
const detailIssue = ref<any | null>(null)
function openDetails(issue: any) {
  detailIssue.value = issue
  detailOpen.value = true
}

const createOpen = ref(false)
const createColumnId = ref<number | null>(null)
const creating = ref(false)
const createForm = reactive<{ title: string; description: string; priority: string }>({
  title: '',
  description: '',
  priority: 'medium',
})

function openCreate(colId: number) {
  createColumnId.value = colId
  createForm.title = ''
  createForm.description = ''
  createForm.priority = 'medium'
  createOpen.value = true
}

async function submitCreate() {
  if (!board.value || !createColumnId.value) return
  const title = createForm.title.trim()
  if (!title) return

  try {
    creating.value = true
    const created = await apiPost('/issues', {
      project_id: board.value.project_id,
      column_id: createColumnId.value,
      title,
      description: createForm.description.trim() || null,
      priority: createForm.priority,
    })
    // optimistic add
    laneIssues[createColumnId.value]?.unshift(created)
    issues.value.unshift(created)
    createOpen.value = false
  } finally {
    creating.value = false
  }
}

function toArray<T = any>(x: any): T[] {
  if (!x) return []
  if (Array.isArray(x)) return x
  if (Array.isArray(x.data)) return x.data
  return []
}

const grouped = computed(() => {
  const map = new Map<number, any[]>()
  for (const c of columns.value) map.set(c.id, [])
  for (const i of issues.value) {
    const bucket = map.get(i.column_id)
    if (bucket) bucket.push(i)
  }
  return map
})

watch([columns, issues], () => {
  for (const c of columns.value) {
    laneIssues[c.id] = grouped.value.get(c.id)?.slice() ?? []
  }
}, { immediate: true })

async function onChange(columnId: number, evt: any) {
  try {
    if (evt?.added?.element) {
      const issue = evt.added.element
      if (issue.column_id !== columnId) {
        await apiPatch(`/issues/${issue.id}/move`, { column_id: columnId })
        issue.column_id = columnId // optimistic update
      }
    }
  } catch (e) {
    console.error('Move failed, reverting', e)
    await reload()
  }
}

async function reload() {
  loading.value = true
  try {
    const b = await apiGet(`/boards/${props.boardId}`)
    board.value = b
    columns.value = toArray(b?.columns).slice().sort((a: any, z: any) => a.position - z.position)

    const iss = await apiGet('/issues', { project_id: b.project_id })
    issues.value = toArray(iss)
  } finally {
    loading.value = false
  }
}

onMounted(reload)
</script>

<template>
  <Head :title="board?.name ? `Board · ${board.name}` : 'Board'" />
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold text-gray-800">
        {{ board?.name ?? 'Board' }}
      </h2>
    </template>

    <div v-if="loading" class="text-gray-500">Loading…</div>

    <div v-else class="flex gap-4 overflow-x-auto">
      <div
        v-for="col in columns"
        :key="col.id"
        class="min-w-[280px] shrink-0 rounded-lg border bg-white"
      >
        <div class="border-b px-3 py-2 text-sm font-medium flex items-center justify-between">
          <span>{{ col.name }}</span>
          <div class="flex items-center gap-2">
            <button
              class="rounded bg-indigo-600 px-2 py-1 text-xs text-white hover:bg-indigo-700"
              @click="openCreate(col.id)"
              type="button"
            >
              Add
            </button>
            <span class="text-xs text-gray-400">{{ laneIssues[col.id]?.length ?? 0 }}</span>
          </div>
        </div>

        <div class="p-3">
          <draggable
            :list="laneIssues[col.id]"
            :group="{ name: 'issues', pull: true, put: true }"
            item-key="id"
            @change="onChange(col.id, $event)"
            class="space-y-2 min-h-[8px]"
          >
            <template #item="{ element: card }">
              <div
                class="rounded border px-3 py-2 text-sm bg-white shadow-sm cursor-grab active:cursor-grabbing hover:bg-gray-50"
                @click="openDetails(card)"
              >
                <div class="font-medium truncate">{{ card.title }}</div>
                <div class="text-xs text-gray-500">{{ card.key }}</div>
              </div>
            </template>
            <template #footer>
              <div
                v-if="(laneIssues[col.id] || []).length === 0"
                class="text-xs text-gray-400 italic py-2"
              >
                No issues
              </div>
            </template>
          </draggable>
        </div>
      </div>
    </div>

    <!-- Create issue modal -->
    <div
      v-if="createOpen"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
      role="dialog"
      aria-modal="true"
    >
      <div class="bg-white rounded-xl shadow-2xl w-[520px] max-w-[90vw] border">
        <div class="flex items-center justify-between border-b px-5 py-3">
          <h3 class="text-lg font-semibold">Create Issue</h3>
          <button
            class="text-gray-400 hover:text-gray-600 text-lg"
            @click="createOpen = false"
            aria-label="Close"
          >✕</button>
        </div>

        <div class="p-5 space-y-4">
          <div>
            <label class="block text-sm text-gray-700 mb-1">Title</label>
            <input
              v-model="createForm.title"
              type="text"
              placeholder="Short summary"
              class="w-full rounded border px-3 py-2 text-sm"
            />
          </div>

          <div>
            <label class="block text-sm text-gray-700 mb-1">Description</label>
            <textarea
              v-model="createForm.description"
              rows="4"
              placeholder="Details (optional)"
              class="w-full rounded border px-3 py-2 text-sm"
            ></textarea>
          </div>

          <div>
            <label class="block text-sm text-gray-700 mb-1">Priority</label>
            <select
              v-model="createForm.priority"
              class="w-full rounded border px-3 py-2 text-sm"
            >
              <option value="low">Low</option>
              <option value="medium">Medium</option>
              <option value="high">High</option>
              <option value="urgent">Urgent</option>
            </select>
          </div>
        </div>

        <div class="border-t px-5 py-3 flex items-center justify-end gap-2">
          <button class="px-4 py-2 text-sm rounded" @click="createOpen = false" type="button">
            Cancel
          </button>
          <button
            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded hover:bg-indigo-700 disabled:opacity-50"
            :disabled="creating || !createForm.title.trim()"
            @click="submitCreate"
            type="button"
          >
            {{ creating ? 'Creating…' : 'Create' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Issue details modal -->
    <div
      v-if="detailOpen && detailIssue"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
      role="dialog"
      aria-modal="true"
    >
      <div class="bg-white rounded-xl shadow-2xl w-[520px] max-w-[90vw] border">
        <div class="flex items-center justify-between border-b px-5 py-3">
          <h3 class="text-lg font-semibold">
            {{ detailIssue.key }} — {{ detailIssue.title }}
          </h3>
          <button
            class="text-gray-400 hover:text-gray-600 text-lg"
            @click="detailOpen = false"
            aria-label="Close"
          >
            ✕
          </button>
        </div>

        <div class="p-5 space-y-3 text-sm text-gray-700">
          <p v-if="detailIssue.description" class="whitespace-pre-line">
            {{ detailIssue.description }}
          </p>

          <div class="grid grid-cols-2 gap-3">
            <div><span class="text-gray-500">Priority:</span> {{ detailIssue.priority ?? '—' }}</div>
            <div><span class="text-gray-500">Estimate:</span> {{ detailIssue.estimate ?? '—' }}</div>
            <div><span class="text-gray-500">Assignee:</span> {{ detailIssue.assignee?.name ?? '—' }}</div>
            <div><span class="text-gray-500">Reporter:</span> {{ detailIssue.reporter?.name ?? '—' }}</div>
          </div>
        </div>

        <div class="border-t px-5 py-3 text-right">
          <button
            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded hover:bg-indigo-700"
            @click="detailOpen = false"
          >
            Close
          </button>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>