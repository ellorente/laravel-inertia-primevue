<script setup>
import { FilterMatchMode } from 'primevue/api';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';
import { onMounted, onUpdated, ref } from 'vue';


const dt = ref(null);
const loading = ref(false);
const totalRecords = ref(0);
const brandData = ref(null);
const selectedBrand = ref(null);
const selectAll = ref(false);
const first = ref(0);
const rows = ref(0);

const noImage = ref('/No-Image-Placeholder.svg');
const confirm = useConfirm();
const toast = useToast();

const noImageFound = (e) => {
    e.target!.src = noImage.value
}


const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    'id': {value: '', matchMode: 'contains'},
    'brand_name': {value: '', matchMode: 'contains'}
});
const lazyParams = ref({});
const columns = ref([
    {field: 'brand_name', header: 'Brand Name'},
    {field: 'brand_logo', header: 'Brand Logo'},
    {field: 'created_at', header: 'Created At'},
    {field: 'active', header: 'Active'}
]);


const loadLazyData = (event) => {
    loading.value = true;
    
    lazyParams.value = { ...lazyParams.value, first: event?.first || first.value };
    
    try {
        setTimeout(async () => {
            const response = await fetch(route('admin.brands.show-brands', {
                 page: JSON.stringify(event?.page+1), 
                 sortField: event?.sortField, sortOrder: event?.sortOrder, 
                 filter: { 'brand_name': event?.filters?.brand_name.value }, 
                 include: [], 
                 lazyEvent: JSON.stringify(lazyParams.value) })).then(async (res) => {
                const brands = await res.json();
    
                brandData.value = brands?.data.data;
                totalRecords.value = brands?.data.total;
                loading.value = false;
    
            });
        }, 100);
    }  catch (e) {
        brandData.value = [];
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
    loadLazyData(event);
};

const onSelectAllChange = async (event) => {
    selectAll.value = event?.checked;
    try {
        const response = await fetch(route('admin.brands.show-brands', { 
            page: JSON.stringify(event?.page+1), 
            filter: { 'brand_name': event?.filters?.brand_name.value} , 
            include: [] })).then(async (res) => {
            const brands = await res.json();

            selectAll.value = true;
            selectedBrand.value = brands?.data.data;

        });

        
    } catch(e) {
        selectAll.value = false;
        selectedBrand.value = [];
    }
};
const onRowSelect = () => {
    selectAll.value = selectedBrand.value?.length === totalRecords.value;
};
const onRowUnselect = () => {
    selectAll.value = false;
};

const editBrand = (id) => {
    visible.value = true;
    recordId.value = id;
}

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
                form.delete(route('admin.brands.destroy', id), {
                    onSuccess: (res) => {
                        isSuccess.value = true;
                        insertRecord.value = 'yes';
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

</script>

<template>
    <div>
        <Toast />
        <DataTable :value="brandData"  :rowsPerPageOptions="[5, 10, 20, 50]" showGridlines stripedRows  lazy paginator :first="first" :rows="10" v-model:filters="filters" ref="dt" dataKey="id"
            :totalRecords="totalRecords" :loading="loading" @page="onPage($event)" @sort="onSort($event)" @filter="onFilter($event)" filterDisplay="row"
            :globalFilterFields="['id', 'brand_name']"
            v-model:selection="selectedBrand" :selectAll="selectAll" @select-all-change="onSelectAllChange" @row-select="onRowSelect" @row-unselect="onRowUnselect" tableStyle="min-width: 50rem; background-color: #fff;">
            
            
            <Column field="id" header="#SL" filterField="id" filterMatchMode="contains" sortable style="border-bottom: 1px solid #d4cfcf; text-align: center;"></Column>
            <Column field="brand_name" header="Brand Name" filterField="brand_name" filterMatchMode="contains" sortable style="border-bottom: 1px solid #d4cfcf;">
                <template #body="{ data }">
                    <div class="flex align-items-center gap-2" >
                        
                        <span>{{ data.brand_name?.split(" ").map((word: string) => word[0].toUpperCase() + word.substring(1)).join(" ") }}</span>
                    </div>
                </template>
                <template #filter="{filterModel,filterCallback}">
                    <InputText type="text" v-model="filterModel.value" @keydown.enter="filterCallback()" class="p-column-filter" placeholder="Search"/>
                </template>
            </Column>

            <Column field="brand_logo" header="Brand Image" sortable style="border-bottom: 1px solid #d4cfcf;">
                <template #body="{ data }">
                    <div class="flex align-items-center gap-2" >
                        
                        <span><img :src="data.brand_logo.includes('https://') === true ? data.brand_logo : '/storage/'+data.brand_logo" class="h-10" @error="(e) => e.target.src = data.brand_logo" /></span>
                    </div>
                </template>
            </Column>

            <Column field="active" header="Active" sortable style="border-bottom: 1px solid #d4cfcf;">
                <template #body="{ data }">
                    <div class="flex align-items-center gap-2" >
                        
                        <span>{{ data.active === 1 ? 'Published' : 'Un Published' }}</span>
                    </div>
                </template>
            </Column>

            <Column header="Action" style="border-bottom: 1px solid #d4cfcf;">
                <template #body="slotProps">
                    <span class="ml-0 font-sm text-slate-700 p-1" @click = "editBrand(slotProps.data.id)"><span class="material-symbols-outlined">edit_square</span></span>
                    <span class="ml-0 font-sm text-slate-700 p-1" @click = "confirmDelete(slotProps.data.id)"><span class="material-symbols-outlined">delete</span></span>
                </template>
            </Column>
        </DataTable>
        <ConfirmDialog></ConfirmDialog>
    </div>
</template>

