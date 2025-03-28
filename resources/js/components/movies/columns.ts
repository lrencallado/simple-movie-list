import { h } from 'vue'
import { ArrowUpDown, ChevronDown } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'

interface Payment {
    id: string
    amount: number
    status: 'pending' | 'processing' | 'success' | 'failed'
    email: string
}

export const payments: Payment[] = [
    {
        id: '728ed52f',
        amount: 100,
        status: 'pending',
        email: 'm@example.com',
    },
    {
        id: '489e1d42',
        amount: 125,
        status: 'processing',
        email: 'example@gmail.com',
    }
]

export const columns: ColumnDef<Payment>[] = [
    {
      accessorKey: 'id',
      header: () => h('div', { class: 'text-left' }, 'ID'),
    },
    {
        accessorKey: 'amount',
        header: () => h('div', { class: 'text-left' }, 'Amount'),
        cell: ({ row }) => {
            const amount = Number.parseFloat(row.getValue('amount'))
            const formatted = new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
            }).format(amount)

            return h('div', { class: 'text-left font-medium' }, formatted)
        },
    },
    {
        accessorKey: 'status',
        header: () => h('div', { class: 'text-left' }, 'Status'),
    },
    {
        accessorKey: 'email',
        header: ({ column }) => {
            return h(Button, {
                variant: 'ghost',
                onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
            }, () => ['Email', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
        },
        cell: ({ row }) => h('div', { class: 'lowercase' }, row.getValue('email')),
    },
]
