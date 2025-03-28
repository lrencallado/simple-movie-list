<script setup lang="ts">
import { ref, watch, h} from 'vue'
import { router, useForm, Head } from '@inertiajs/vue3'
import { Input } from '@/components/ui/input'
import InputError from '@/components/InputError.vue'
import { Button } from '@/components/ui/button'
import { Label } from '@/components/ui/label'
import DataTable from '@/components/DataTable.vue'
import { useDebounceFn } from '@vueuse/core'
import { Badge } from '@/components/ui/badge'
import DropdownAction from '@/components/DataTableDropdown.vue'
import { ArrowUpDown } from 'lucide-vue-next'
import { TagsInput, TagsInputInput, TagsInputItem, TagsInputItemDelete, TagsInputItemText } from '@/components/ui/tags-input'
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog'
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
} from '@/components/ui/alert-dialog'
import Toaster from '@/components/ui/toast/Toaster.vue'
import { useToast } from '@/components/ui/toast/use-toast'

const props = defineProps({
    movies: Object,
    search: String
})
const { toast } = useToast()
const movieId = ref(null)
const movieForm = useForm({
    id: null,
    title: '',
    director: '',
    genres: [],
    release_date: ''
});
const deleteConfirmation = ref(false)
const formAction = ref(null)
const search = ref(props.search)
const debounceSearch = useDebounceFn(() => {
    router.get(route('movies.index'), { search: search.value }, { preserveState: true })
}, 300)
watch(search, debounceSearch)

const columns = [
    {
        accessorKey: "title",
        header: ({ column }) => {
            return h(Button, {
                variant: 'ghost',
                onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
            }, () => ['Title', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
        },
        cell: (row) => h('div', { class: 'font-bold' }, row.getValue())
    },
    {
        accessorKey: "director",
        header: ({ column }) => {
            return h(Button, {
                variant: 'ghost',
                onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
            }, () => ['Director', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
        },
        cell: (row) => h('div', { class: 'font-bold' }, row.getValue())
    },
    {
        accessorKey: "genres",
        header: "Genres",
        cell: (row) => h('div', { class: 'flex flex-wrap gap-1' },
            row.getValue().map(g =>
                h(Badge, { variant: 'secondary' }, () => g)
            )
        ),
    },
    {
        accessorKey: "release_date",
        header: ({ column }) => {
            return h(Button, {
                variant: 'ghost',
                onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
            }, () => ['Release Date', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
        },
        cell: (row) => {
            const date = new Date(row.getValue());
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
            });
        },
    },
    {
        id: 'actions',
        enableHiding: false,
        cell: ({ row }) => {
        const data = row.original

        return h('div', { class: 'relative' }, h(DropdownAction, {
            data,
            onEdit: (movie) => {
                movieForm.id = movie.id
                movieForm.title = movie.title
                movieForm.director = movie.director
                movieForm.release_date = movie.release_date
                movieForm.genres = movie.genres
                showFormModal('edit')
            },
            onDelete: (movie) => {
                movieId.value = movie.id
                deleteConfirmation.value = true
            }
        }))
        },
    },
];

const showFormModal = (action) => {
    const formModalButton = document.getElementById('form-modal-button')
    formAction.value = action
    if (action === 'add') {
        movieForm.reset()
    }

    formModalButton.click()
}

const submit = () => {
    let url = route('movies.store')
    if (formAction.value === 'edit') {
        url = route('movies.update', movieForm.id)
    }
    movieForm.post(url, {
        onSuccess: () => {
            toast({
                title: 'Success!',
                description: 'The movie was added/updated successfully.',
            })
        },
        onError: (errors: any) => {
            toast({
                title: 'Uh oh! Something went wrong.',
                description: 'There was an error adding/updating the movie.',
                variant: 'destructive'
            })
        }
    })
}

const deleteMovie = () => {
    router.delete(route('movies.destroy', movieId.value), {
        onSuccess: () => {
            deleteConfirmation.value = false
            movieId.value = null
            toast({
                title: 'Success!',
                description: 'The movie was deleted successfully.',
            })
            fixOverflowAndPointerEvents()
        },
        onError: (errors: any) => {
            deleteConfirmation.value = false
            toast({
                title: 'Uh oh! Something went wrong.',
                description: 'There was an error deleting the movie.',
                variant: 'destructive'
            })
            fixOverflowAndPointerEvents()
        }
    })
}

const closeDeleteConfirmation = () => {
    deleteConfirmation.value = false
    movieId.value = null
    fixOverflowAndPointerEvents()
}

const fixOverflowAndPointerEvents = () => {
    setInterval(() => {
        document.body.style.removeProperty('overflow')
        document.body.style.removeProperty('pointer-events')
    }, 500)
}

</script>
<template>
    <Head title="Movie List" />
    <div class="p-4 sm:w-3/4 mx-auto">
        <div class="mb-4 flex gap-2 justify-between">
            <Input v-model="search" type="search" placeholder="Search by title, director, or genre..." class="w-2/4" />
            <Button @click="showFormModal('add')" class="">
                Add Movie
            </Button>
        </div>
        <DataTable
            :columns="columns"
            :data="props.movies?.data"
            :pagination="props.movies.data.length > 0"
            :page-size="props.movies?.meta.per_page"
            :total-items="props.movies?.meta.total"
            @page-change="(page) => router.get(route('movies.index'), { page, search: search }, { preserveState: true })"
        />
        <Dialog>
            <DialogTrigger as-child>
                <Button id="form-modal-button" variant="outline" class="invisible">
                    Add/Edit
                </Button>
            </DialogTrigger>
            <DialogContent class="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>{{ formAction === 'add' ? 'Add New' : 'Edit' }} Movie</DialogTitle>
                    <DialogDescription>
                    Make changes to your list here. Click save when you're done.
                    </DialogDescription>
                </DialogHeader>
                <form @submit.prevent="submit">
                <div class="grid gap-4 py-4">
                    <div class="grid grid-cols-4 items-center gap-4">
                        <Label for="title" class="text-right">
                            Title
                        </Label>
                        <Input v-model="movieForm.title" id="title" class="col-span-3 w-full" />
                    </div>
                    <InputError class="text-right" :message="movieForm.errors.title" />
                    <div class="grid grid-cols-4 items-center gap-4">
                        <Label for="director" class="text-right">
                            Director
                        </Label>
                        <Input v-model="movieForm.director" id="director" class="col-span-3" />
                    </div>
                    <InputError class="text-right" :message="movieForm.errors.director" />
                    <div class="grid grid-cols-4 items-center gap-4">
                        <Label for="release-date" class="text-right">
                            Release Date
                        </Label>
                        <Input type="date" v-model="movieForm.release_date" id="release-date" class="col-span-3" />
                    </div>
                    <InputError class="text-right" :message="movieForm.errors.release_date" />
                    <div class="grid grid-cols-1 items-center gap-4">
                        <TagsInput v-model="movieForm.genres">
                            <TagsInputItem v-for="item in movieForm.genres" :key="item" :value="item">
                                <TagsInputItemText />
                                <TagsInputItemDelete />
                            </TagsInputItem>
                            <TagsInputInput placeholder="Genres..." />
                        </TagsInput>
                    </div>
                    <InputError class="text-right" :message="movieForm.errors.genders" />
                </div>
                <DialogFooter>
                    <Button type="submit">
                    Save changes
                    </Button>
                </DialogFooter>
            </form>
            </DialogContent>
        </Dialog>
        <AlertDialog :open="deleteConfirmation">
            <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>Are you absolutely sure?</AlertDialogTitle>
                <AlertDialogDescription>
                This action cannot be undone. This will permanently delete the record.
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogCancel @click="closeDeleteConfirmation">Cancel</AlertDialogCancel>
                <AlertDialogAction @click="deleteMovie">Continue</AlertDialogAction>
            </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    </div>
    <Toaster />
</template>
