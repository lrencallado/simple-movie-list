<script setup lang="ts" generic="TData, TValue">
import type { ColumnDef, SortingState } from '@tanstack/vue-table'
import { ref, computed, watch } from 'vue'
import { Button } from '@/components/ui/button'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import {
  FlexRender,
  getCoreRowModel,
  getPaginationRowModel,
  getSortedRowModel,
  useVueTable,
} from '@tanstack/vue-table'

import { valueUpdater } from '@/lib/utils'

// Define props
const props = withDefaults(
  defineProps<{
    columns: ColumnDef<TData, TValue>[]
    data: TData[]
    pagination?: boolean
    pageSize?: number
    totalItems?: number
  }>(),
  {
    pagination: true,
    pageSize: 10,
    totalItems: 0,
  }
)

// Define emits for page change
const emit = defineEmits<{
  (e: 'page-change', page: number): void
}>()

// Track current page
const currentPage = ref(1)

// Compute total pages
const totalPages = computed(() => {
  const perPage = props.pageSize ?? 10 // Default to 10 if undefined
  const total = props.totalItems ?? 0   // Default to 0 if undefined
  return Math.ceil(total / perPage) || 1
})
// Watch for data changes and reset pagination if needed
watch(() => props.data, () => {
  if (currentPage.value > totalPages.value) {
    currentPage.value = 1
  }
})

// Handle pagination
const goToPage = (page: number) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page
    emit('page-change', page) // Emit event
  }
}

// Sorting state
const sorting = ref<SortingState>([])

// Setup TanStack Table
const table = useVueTable({
  get data() { return props.data },
  get columns() { return props.columns },
  getCoreRowModel: getCoreRowModel(),
  getSortedRowModel: getSortedRowModel(),
  getPaginationRowModel: getPaginationRowModel(),
  onSortingChange: updaterOrValue => valueUpdater(updaterOrValue, sorting),
  state: {
    get sorting() { return sorting.value },
  },
  manualPagination: true, // Manual pagination
})
</script>

<template>
  <div>
    <div class="border rounded-md">
      <Table>
        <TableHeader>
          <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
            <TableHead v-for="header in headerGroup.headers" :key="header.id">
              <FlexRender v-if="!header.isPlaceholder" :render="header.column.columnDef.header" :props="header.getContext()" />
            </TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <template v-if="table.getRowModel().rows?.length">
            <TableRow v-for="row in table.getRowModel().rows" :key="row.id">
              <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id">
                <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
              </TableCell>
            </TableRow>
          </template>
          <template v-else>
            <TableRow>
              <TableCell :colspan="columns.length" class="h-24 text-center"> No results. </TableCell>
            </TableRow>
          </template>
        </TableBody>
      </Table>
    </div>

    <!-- ✅ Pagination UI -->
    <div v-if="pagination" class="flex items-center justify-between py-4">
      <div class="text-sm text-gray-500">
        Page {{ currentPage }} of {{ totalPages }}
      </div>
      <div class="flex items-center space-x-2">
        <Button variant="outline" size="sm" :disabled="currentPage === 1" @click="goToPage(currentPage - 1)">
          Previous
        </Button>
        <Button variant="outline" size="sm" :disabled="currentPage === totalPages" @click="goToPage(currentPage + 1)">
          Next
        </Button>
      </div>
    </div>
  </div>
</template>
