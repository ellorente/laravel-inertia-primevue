<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref, watch, onMounted } from 'vue';
import { useToast } from 'primevue/usetoast';
import FormError from '@/Components/common/FormError.vue';
import { useInputMask } from '@/composables/useInputMask';
import { useWhatsappValidator } from '@/composables/useWhatsappValidator'
import { parse, isValid } from 'date-fns'

const { applyMask, handleKeydown } = useInputMask();

const toast = useToast();
const emit = defineEmits(['created']); // <-- para avisar al padre

const form = useForm({
    name: '',
    email: '',
    password: '',
    role: 'User',
    status: 1,
});



const rolesCurrent = ref([]);


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

onMounted(() => {
    loadRoles();
});

const saveContact = () => {

    if (Object.keys(form.errors).length > 0){ 
        form.clearErrors();
        //toast.add({ severity: 'success', summary: 'Éxito', detail: 'Contacto creado', life: 3000 });
        return
    }

    form.post(route('users.store'), {
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Éxito', detail: 'Usuario creado', life: 3000 });
            emit('created'); // <-- avisamos que se creó correctamente
            form.reset();
        }
    });
};

</script>

<template>
    <form @submit.prevent="saveContact" class="space-y-4 p-4">
        <div>
            <label class="block mb-1 font-semibold">Nombre completo de usuario</label>
            <InputText 
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

        <div>
            <label class="block mb-1 font-semibold">Email</label>
            <InputText 
                v-model="form.email" 
                class="w-full"
                :class="{ 'p-invalid': form.errors.email }"
                @input="(e) => form.email = applyMask({
                    value: e.target.value,                    
                    toLowercase:true
                })"
                @keydown="(e) => handleKeydown({
                    event: e,
                    modelValue: form.email,
                    maxLength: 50                    
                })"                              
            />            
        </div>

        <div>
            <label class="block mb-1 font-semibold">Password Temporal</label>
            <InputText 
                v-model="form.password"
                :class="{ 'p-invalid': form.errors.password }" 
                class="w-full"
                @keydown="(e) => handleKeydown({
                    event: e,
                    modelValue: form.password,
                    maxLength: 20                    
                })"                              
            />            
        </div>

        <div class="w-1/2">
                <label class="block text-sm font-medium">Rol Asignado</label>
                    <Select 
                        v-model="form.role" 
                        :class="{ 'p-invalid': form.errors.role }" 
                        :options="rolesCurrent" 
                        optionLabel="name"
                        optionValue="name" 
                        placeholder="Seleccione un rol" 
                        showClear
                        multiple
                    />
                    <FormError :error="form.errors.role" />
        </div>     

        <div class="flex justify-end">
            <Button 
                type="submit" 
                label="Guardar" 
                icon="pi pi-save" 
                :loading="form.processing" 
                class="mt-4"
            />
        </div>
    </form>
</template>