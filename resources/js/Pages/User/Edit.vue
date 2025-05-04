<script setup>
import { ref, watch, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import FormError from '@/Components/common/FormError.vue';
import { useInputMask } from '@/composables/useInputMask';

const { applyMask, handleKeydown } = useInputMask();

const toast = useToast();
const emit = defineEmits(['close', 'updated']);
const rolesCurrent = ref([]);

const props = defineProps({
    visible: Boolean,
    users: Object,
    rolesCurrent: Array,
});
const form = useForm({
    name: '',
    email: '',
    roles: '',
    status: '',
});

const loadRoles = async () => {
    try {
        //const response = await fetch(route('roles.showRoles'));
        const response = await fetch(route('roles.showRoles'), {
            credentials: 'same-origin',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
        });
        if (!response.ok) {
            const errorText = await response.text();
            console.error("Error:", errorText);
            return;
        }
        const result = await response.json();
        rolesCurrent.value = result.data;

    } catch (error) {
        console.error('Error loading Roles', error);
    }
};


watch(() => props.users, (newVal) => {
    
    if (newVal) {  
        form.name = newVal.name;
        form.email = newVal.email;        
        form.status = newVal.status;
        form.roles = Array.isArray(newVal.roles) && newVal.roles.length > 0 ? newVal.roles[0].name : '';
    }
    
});

onMounted(() => {
    loadRoles();
    
});

const updateContact = () => {

    if (Object.keys(form.errors).length > 0){ 
        //toast.add({ severity: 'success', summary: 'Éxito', detail: 'Contacto creado', life: 3000 });
        return
    }

    form.put(route('users.update', props.users.id), {
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Éxito', detail: 'Usuario actualizado', life: 3000 });
            emit('updated'); // Notifica al padre que se actualizó
            emit('close');   // Cierra el Dialog
        },
        onError: (errors) => {
            console.log (errors)
            toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo actualizar', life: 3000 });
        }
    });
};

function handleCancel() {
    emit('close');
}

const status = ref([
    { name: 'Inactivo', id: 0 },
    { name: 'Activo', id: 1 },
]);
</script>

<template>

<Dialog :visible="visible" modal header="Editar Usuario" @update:visible="emit('close')" style="width: 600px">
    <div class="flex flex-col gap-4">
        <div>
            <label for="email" class="block text-sm font-medium">Email</label>
            <InputText id="email" disabled v-model="form.email" class="w-full"                            
            />
        </div>
        <div>
            <label for="name" class="block text-sm font-medium">Nombre Usuario</label>
            <InputText 
            id="name" 
            v-model="form.name" 
            class="w-full"
            :class="{ 'p-invalid': form.errors.name }"
            @input="(e) => form.name = applyMask({
                value: e.target.value,                    
                toCapitalize:true
            })"
            @keydown="(e) => handleKeydown({
                event: e,
                modelValue: form.name,
                maxLength: 70                    
            })"   
            />
            <FormError :error="form.errors.name" />
        </div>
        <div class="flex gap-x-4">
            <div class="w-1/2">
                <label for="roles" class="block text-sm font-medium">Rol Asignado</label>
                    <Select 
                        v-model="form.roles" 
                        :options="rolesCurrent" 
                        optionLabel="name" 
                        optionValue="name" 
                        placeholder="Seleccione un rol" 
                        showClear
                        multiple
                    />
                    <FormError :error="form.errors.roles" />
            </div>
            <div class="w-1/2">
                <label for="status" class="block text-sm font-medium">Estado</label>
                    <Select 
                        v-model="form.status" 
                        :options="status" 
                        optionLabel="name" 
                        optionValue="id" 
                        placeholder="Select status" 
                        showClear
                    />
                    <FormError :error="form.errors.status" />
            </div>
        </div>       


        <div class="flex justify-end gap-2 mt-4">
            <Button label="Cancelar" severity="secondary" @click="handleCancel" />
            <Button label="Guardar" icon="pi pi-check" @click="updateContact" />
        </div>
    </div>
</Dialog>

</template>