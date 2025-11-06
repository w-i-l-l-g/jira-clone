<script setup>
import { onMounted, ref } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { apiGet } from '../../lib/api'

const loading = ref(true)
const orgs = ref([])

onMounted(async () => {
  const data = await apiGet('/orgs')
  orgs.value = Array.isArray(data) ? data : (data?.data ?? [])
  loading.value = false
})

const orgHref = (o) => (typeof route === 'function' ? route('orgs.show', o.slug) : `/orgs/${o.slug}`)
</script>

<template>
  <Head title="Organizations" />
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold text-gray-800">Organizations</h2>
    </template>

    <div v-if="loading" class="text-gray-500">Loading…</div>

    <div v-else class="grid gap-3">
      <Link
        v-for="o in orgs"
        :key="o.id"
        :href="orgHref(o)"
        class="block rounded-lg border bg-white p-4 transition hover:shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
      >
        <div class="font-medium">{{ o.name }}</div>
        <div class="text-sm text-gray-500">Slug: {{ o.slug }}</div>
      </Link>
    </div>
  </AuthenticatedLayout>
</template>