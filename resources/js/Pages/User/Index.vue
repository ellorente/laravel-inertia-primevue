<script setup>
import AppLayout from '@/Layout/AppLayout.vue';
import { ref, onMounted, computed, watch } from 'vue';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';
import { FilterMatchMode } from '@primevue/core/api';
import { useForm, usePage} from '@inertiajs/vue3';
import EditUser from './Edit.vue';
import CreateUser from './Create.vue';
import Dialog from 'primevue/dialog';

const dt = ref(null);
const loading = ref(false);
const totalRecords = ref(0);
const userData = ref([]);
const selectedUsers = ref([]);
const selectAll = ref(false);
const first = ref(0);
const confirm = useConfirm();
const toast = useToast();
const form = useForm({});

const page = usePage();

const isAdmin = page.props.user.isAdmin;

/* //se puede capturar todos los roles y luego buscar dentro del array con esto:
   //la configuración de varios roles sedebe buscar en el Middleware HandleInertiaRequests
const userRoles = page.props.user.roles;
const isAdmin = userRoles.includes('Admin');*/

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    'name': {value: '', matchMode: 'contains'},
    'email': {value: '', matchMode: 'contains'},
    'roles': {value: '', matchMode: 'contains'},
    'status': {value: '', matchMode: 'contains'},
});

const lazyParams = ref({});

const loadLazyData = (event) => {
    loading.value = true;
    
    lazyParams.value = { ...lazyParams.value, first: event?.first || first.value };

    try {
        
        setTimeout(async () => {
            const response = await fetch(route('users.show-users', {
                page: JSON.stringify(event?.page+1),
                //page: (typeof event?.page === 'number' && event.page >= 0) ? event.page + 1 : 1,                 
                sortField: event?.sortField, sortOrder: event?.sortOrder, 
                filter: lazyParams.value.filters,
                include: [], 
                lazyEvent: JSON.stringify(lazyParams.value) })).then(async (res) => {
                const users = await res.json();
                userData.value = users?.data.data;
                totalRecords.value = users?.data.total;
                loading.value = false;
            });
        }, 100);
    }  catch (e) {
        userData.value = [];
        totalRecords.value = 0;
        loading.value = false;
    }
};

const onPage = (event) => {
    lazyParams.value = event;
    loadLazyData(event);
};
const onSort = (event) => {
    lazyParams.value = event;
    loadLazyData(event);
};
const onFilter = (event) => {
    lazyParams.value.filters = filters.value ;
    loadLazyData({ filters: filters.value });
};


const onSelectAllChange = async (event) => {
    selectAll.value = event?.checked;
    try {
        const response = await fetch(route('users.show-users', { 
            page: JSON.stringify(event?.page+1), 
            //page: (typeof event?.page === 'number' && event.page >= 0) ? event.page + 1 : 1,
            filter: { 'name': event?.filters?.name.value} , 
            include: [] })).then(async (res) => {
            const users = await res.json();
            selectAll.value = true;
            selectedUsers.value = users?.data.data;

        });

        
    } catch(e) {
        selectAll.value = false;
        selectedUsers.value = [];
    }
};
const onRowSelect = () => {    
    selectAll.value = selectedUsers.value?.length === totalRecords.value;
};
const onRowUnselect = () => {
    selectAll.value = false;
};



//Editar Registro

const userToEdit = ref(null);
const dialogVisible = ref(false);

const editContact = (users) => {
    userToEdit.value = users;
    dialogVisible.value = true;
};

const onCloseDialog = () => {
    dialogVisible.value = false;
    userToEdit.value = null;
};

const onUserUpdated = () => {
    loadLazyData();
};
//Fin editar Registro


const confirmDelete = (id) => {
        confirm.require({
            message: 'Do you want to delete this record?',
            header: 'Danger Zone',
            icon: 'pi pi-info-circle',
            rejectLabel: 'Cancel',
            acceptLabel: 'Delete',
            rejectClass: 'p-button-secondary p-button-outlined',
            acceptClass: 'p-button-danger',
            accept: () => {
                form.delete(route('users.destroy', id), {
                    onSuccess: (res) => {
                        //isSuccess.value = true;
                        //insertRecord.value = 'yes';
                        toast.add({ severity: 'success', summary: 'Deleted', detail: 'Registro Eliminado Correctamente', life: 2000 });
                        loadLazyData(); 
                    },
                    onError: (err) => {
                        const errorFieldName = Object.keys(form.errors);

                        if (typeof err === 'object') {
                            Object.values(err).filter((er) => {
                                toast.add({ severity: 'error', summary: 'Error', detail: er, life: 1000 });
                            })
                            
                            return ;
                        }

                        toast.add({ severity: 'error', summary: 'Error', detail: err[0], life: 1000 });
                    }  
                })
            },
            reject: () => {
                toast.add({ severity: 'error', summary: 'Rejected', detail: 'You have rejected', life: 1000 });
            }
        });
    };

onMounted(() => {
    lazyParams.value = {
        first: dt.value.first,
        rows: dt.value.rows,
        sortField: null,
        sortOrder: null,
        filters: filters.value
    };
    loadLazyData();
});

//Cambio de filtro

const selectedFilter = ref('name');

const filterOptions = [
    { label: 'Nombre', value: 'name' },
    //{ label: 'Documento', value: 'document_number' },
];

// Para cambiar dinámicamente el placeholder
const getFilterLabel = computed(() => {
    const selected = filterOptions.find(opt => opt.value === selectedFilter.value);
    return selected ? selected.label : '';
});

watch(selectedFilter, (newVal) => {
    // Limpiar todos los filtros
    Object.keys(filters.value).forEach(key => {
        filters.value[key].value = '';
    });

    // Luego carga los datos sin filtros
    loadLazyData();
});


//Para Crear Nuevo
const showCreateDialog = ref(false);

const openCreateDialog = () => {
    showCreateDialog.value = true;
};

const handleCreated = () => {
    //showCreateDialog.value = false;
    loadLazyData(); // <-- recargar los datos de la tabla
};

const getSeverity = (status) => {
    switch (status) {
        case 0:
            return 'danger';

        case 1:
            return 'success';

        default:
            return null;
    }
};

const getStatus = (status) => {
    switch (status) {
        case 0:
            return 'Inactivo';

        case 1:
            return 'Activo';

        default:
            return null;
    }
};

</script>

<template>
    <AppLayout>
    <InertiaHead title="Users" />    
    <div class="card">
        <div class="font-semibold text-xl mb-4">Users</div>
        <Toast />
        <DataTable 
            :value="userData"
            showGridlines 
            lazy 
            paginator 
            :rows="10"
            v-model:filters="filters"
            ref="dt" 
            dataKey="id"
            :totalRecords="totalRecords" 
            :loading="loading"
            @page="onPage($event)" 
            @sort="onSort($event)" 
            @filter="onFilter($event)" 
            filterDisplay="row"
            :globalFilterFields="['name','email', 'status']"
            v-model:selection="selectedUsers" 
            :selectAll="selectAll" 
            @select-all-change="onSelectAllChange" 
            @row-select="onRowSelect" 
            @row-unselect="onRowUnselect" 
            tableStyle="min-width: 70rem;"
        >



            <template #header>
                <div class="flex items-center justify-between w-full space-x-4">

                    <Button icon="pi pi-plus" label="Nuevo Usuario" @click="openCreateDialog"/>

                    <div class="flex items-center space-x-2">
                        

                        <!-- Selector de campo de búsqueda -->
                        <Select 
                            v-model="selectedFilter" 
                            :options="filterOptions" 
                            optionLabel="label" 
                            optionValue="value"
                            placeholder="Buscar por..."
                            class="w-48" 
                        />

                        <!-- Input de búsqueda -->
                        <IconField>
                            <InputIcon>
                                <i class="pi pi-search" />
                            </InputIcon>
                            <InputText 
                                v-model="filters[selectedFilter].value" 
                                :placeholder="`Buscar por ${getFilterLabel}`"
                                @update:modelValue="onFilter"
                            />
                        </IconField>
                    </div>
                </div>
            </template>            
            <template #empty> Registros no Enconterados </template>
            <template #loading> Cargando datos de Registros. Por favor esperar. </template>

            <Column field="name" header="Nombre Usurio" filterField="name" filterMatchMode="contains" sortable style="border-bottom: 1px solid #d4cfcf; "></Column>
            <Column field="email" header="Email" filterField="email" filterMatchMode="contains" sortable style="border-bottom: 1px solid #d4cfcf; "></Column>
            <Column field="roles.name" header="Rol" filterField="roles.name" filterMatchMode="contains" sortable style="border-bottom: 1px solid #d4cfcf; ">
                <template #body="{ data }">
                    {{ data.roles.map(r => r.name).join(', ') }}
                </template>
            </Column>
            <Column field="status" header="Status" filterField="status" filterMatchMode="contains" sortable style="border-bottom: 1px solid #d4cfcf; ">
                <template #body="slotProps">
                    <Tag :value="getStatus(slotProps.data.status)" :severity="getSeverity(slotProps.data.status)" />
                </template>
            </Column>


            
            <Column v-if="isAdmin" header="Action" style="border-bottom: 1px solid #d4cfcf; text-align: center;">
                <template #body="slotProps">
                    <Button icon="pi pi-pencil" class="p-button-info p-mr-2" size="small" @click="editContact(slotProps.data)" />
                    <Button icon="pi pi-trash" class="p-button-danger" size="small" @click="confirmDelete(slotProps.data.id)" />
                </template>
            </Column>
            

        </DataTable>
        <ConfirmDialog />
        

        <EditUser 
            :visible="dialogVisible" 
            :users="userToEdit" 
            @close="onCloseDialog" 
            @updated="onUserUpdated" 
        />

        <Dialog 
            v-model:visible="showCreateDialog" 
            header="Nuevo Usuario" 
            modal 
            :style="{ width: '600px' }"
        >
            <CreateUser @created="handleCreated" />
        </Dialog>
    </div>
    </AppLayout>
</template>
