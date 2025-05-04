<script setup>
import AppLayout from '@/Layout/AppLayout.vue';
import { ref, onMounted, computed, watch } from 'vue';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';
import { FilterMatchMode } from '@primevue/core/api';
import { useForm} from '@inertiajs/vue3';
import EditContact from './Edit.vue';
import CreateContact from './Create.vue';
import Dialog from 'primevue/dialog';

const dt = ref(null);
const loading = ref(false);
const totalRecords = ref(0);
const contactData = ref([]);
const selectedContacts = ref([]);
const selectAll = ref(false);
const first = ref(0);
const confirm = useConfirm();
const toast = useToast();
const form = useForm({});

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    'document_number': {value: '', matchMode: 'contains'},
    'full_name': {value: '', matchMode: 'contains'},
    'phones': {value: '', matchMode: 'contains'},
    'whatsapp': {value: '', matchMode: 'contains'},
    'address': {value: '', matchMode: 'contains'},
    'email': {value: '', matchMode: 'contains'},
    'eps': {value: '', matchMode: 'contains'},
});

const lazyParams = ref({});

const loadLazyData = (event) => {
    loading.value = true;
    
    lazyParams.value = { ...lazyParams.value, first: event?.first || first.value };

    try {
        setTimeout(async () => {
            const response = await fetch(route('contacts.show-contacts', {
                page: JSON.stringify(event?.page+1),
                //page: (typeof event?.page === 'number' && event.page >= 0) ? event.page + 1 : 1,
                sortField: event?.sortField, sortOrder: event?.sortOrder, 
                filter: lazyParams.value.filters,
                include: [], 
                lazyEvent: JSON.stringify(lazyParams.value) })).then(async (res) => {
                const contacts = await res.json();
    
                contactData.value = contacts?.data.data;
                totalRecords.value = contacts?.data.total;
                loading.value = false;
                
            });
        }, 100);
    }  catch (e) {
        contactData.value = [];
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
        const response = await fetch(route('contacts.show-contacts', { 
            page: JSON.stringify(event?.page+1),
            //page: (typeof event?.page === 'number' && event.page >= 0) ? event.page + 1 : 1,
            filter: { 'full_name': event?.filters?.full_name.value} , 
            include: [] })).then(async (res) => {
            const contacts = await res.json();

            selectAll.value = true;
            selectedContacts.value = contacts?.data.data;

        });

        
    } catch(e) {
        selectAll.value = false;
        selectedContacts.value = [];
    }
};
const onRowSelect = () => {    
    selectAll.value = selectedContacts.value?.length === totalRecords.value;
};
const onRowUnselect = () => {
    selectAll.value = false;
};



//Editar Registro

const contactToEdit = ref(null);
const dialogVisible = ref(false);

const editContact = (contact) => {
    contactToEdit.value = contact;
    dialogVisible.value = true;
};

const onCloseDialog = () => {
    dialogVisible.value = false;
    contactToEdit.value = null;
};

const onContactUpdated = () => {
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
                form.delete(route('contacts.destroy', id), {
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

function formatDate(dateString) {
  const date = new Date(dateString);
  const day = String(date.getDate()).padStart(2, '0');
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const year = date.getFullYear();
  return `${month}/${day}/${year}`;
}
function birthdateTemplate(rowData) {
    console.log('Birthdate recibido:', rowData.birthdate);
    return formatDate(rowData.birthdate);
}

//Cambio de filtro

const selectedFilter = ref('full_name');

const filterOptions = [
    { label: 'Nombre', value: 'full_name' },
    { label: 'Documento', value: 'document_number' },
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

</script>

<template>
    <AppLayout>
    <InertiaHead title="Contacs" />    
    <div class="card">
        <div class="font-semibold text-xl mb-4">Contacts</div>
        <Toast />
        <DataTable 
            :value="contactData"
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
            :globalFilterFields="['document_number', 'full_name', 'phones', 'whatsapp', 'address', 'email', 'eps']"
            v-model:selection="selectedContacts" 
            :selectAll="selectAll" 
            @select-all-change="onSelectAllChange" 
            @row-select="onRowSelect" 
            @row-unselect="onRowUnselect" 
            tableStyle="min-width: 70rem;"
        >



            <template #header>
                <div class="flex items-center justify-between w-full space-x-4">

                    <Button icon="pi pi-plus" label="Nuevo Contacto" @click="openCreateDialog"/>

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
            <template #empty> Contactos no Enconterados </template>
            <template #loading> Cargando datos de contactos. Por favor esperar. </template>
            <Column field="document_type.name" header="Tipo Documento" style="border-bottom: 1px solid #d4cfcf; "></Column>
            <Column field="document_number" header="Documento" filterField="document_number" filterMatchMode="contains" sortable style="border-bottom: 1px solid #d4cfcf; text-align: center;">
                            <template #body="{ data }">
                                {{ data.document_number }}
                            </template>
            </Column>
            <Column field="birthdate" header="Fecha Nacimiento" style="border-bottom: 1px solid #d4cfcf; ">
                <template #body="{ data }">
                                {{ formatDate(data.birthdate) }}
                            </template>
            </Column>
            <Column field="full_name" header="Nombre Completo" filterField="full_name" filterMatchMode="contains" sortable style="border-bottom: 1px solid #d4cfcf; "></Column>
            <Column field="phones" header="Telefonos" filterField="phones" filterMatchMode="contains" sortable style="border-bottom: 1px solid #d4cfcf; text-align: center;"></Column>
            <Column field="whatsapp" header="Whatsapp" filterField="whatsapp" filterMatchMode="contains" sortable style="border-bottom: 1px solid #d4cfcf; text-align: center;"></Column>
            <Column field="address" header="Dirección" filterField="address" filterMatchMode="contains" sortable style="border-bottom: 1px solid #d4cfcf;"></Column>
            <Column field="email" header="Email" filterField="email" filterMatchMode="contains" sortable style="border-bottom: 1px solid #d4cfcf; "></Column>
            <Column field="eps" header="EPS" filterField="eps" filterMatchMode="contains" sortable style="border-bottom: 1px solid #d4cfcf; "></Column>
            <Column header="Action" style="border-bottom: 1px solid #d4cfcf; text-align: center;">
                <template #body="slotProps">
                    <Button icon="pi pi-pencil" class="p-button-info p-mr-2" size="small" @click="editContact(slotProps.data)" />
                    <Button icon="pi pi-trash" class="p-button-danger" size="small" @click="confirmDelete(slotProps.data.id)" />
                </template>
            </Column>

        </DataTable>
        <ConfirmDialog />
        

        <EditContact 
            :visible="dialogVisible" 
            :contact="contactToEdit" 
            @close="onCloseDialog" 
            @updated="onContactUpdated" 
        />

        <Dialog 
            v-model:visible="showCreateDialog" 
            header="Nuevo Contacto" 
            modal 
            :style="{ width: '600px' }"
        >
            <CreateContact @created="handleCreated" />
        </Dialog>
    </div>
    </AppLayout>
</template>
