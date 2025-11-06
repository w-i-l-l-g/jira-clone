<script setup>
import { Link, usePage } from '@inertiajs/vue3'
const page = usePage()
const user = page.props.auth?.user ?? null
</script>

<template>
  <div class="min-h-screen bg-gray-100">
    <header class="bg-white shadow">
      <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8 flex items-center justify-between">
        <div class="flex items-center gap-6">
          <Link href="/" class="font-semibold">Not-Jira</Link>
          <nav class="hidden sm:flex gap-4 text-sm text-gray-600">
            <Link href="/">Dashboard</Link>
            <Link href="/orgs">Orgs</Link>
          </nav>
        </div>

        <div class="flex items-center gap-3" v-if="user">
          <span class="text-sm text-gray-600">{{ user.name }}</span>
          <form method="POST" :action="route('logout')">
            <input type="hidden" name="_token" :value="page.props.csrf_token" />
            <button class="text-sm text-gray-600 hover:text-gray-900">Logout</button>
          </form>
        </div>
      </div>
    </header>

    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
      <slot />
    </main>
  </div>
</template>