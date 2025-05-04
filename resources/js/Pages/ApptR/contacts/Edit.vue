<script setup>
import { ref, watch, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import FormError from '@/Components/common/FormError.vue';
import { useInputMask } from '@/composables/useInputMask';
import { parse, isValid } from 'date-fns'
import { useWhatsappValidator } from '@/composables/useWhatsappValidator'

const { applyMask, handleKeydown } = useInputMask();

const toast = useToast();
const emit = defineEmits(['close', 'updated']);
const documentTypes = ref([]);

const props = defineProps({
    visible: Boolean,
    contact: Object,
    documentTypes: Array,
});

const form = useForm({
    document_number: '',
    document_type_id: '',
    birthdate: '',
    full_name: '',
    phones: '',
    whatsapp: '',
    address: '',
    email: '',
    eps: '',
});


const loadDocumentTypes = async () => {
    try {
        setTimeout(async () => {
            const response = await fetch(route('document-types.index'));
            const result = await response.json();
            documentTypes.value = result.data;            
        }, 100);
    } catch (error) {
        console.error('Error loading document types', error);
    }
};

watch(() => props.contact, (newVal) => {
    if (newVal) {  
        form.document_number = newVal.document_number;
        form.document_type_id = newVal.document_type_id; 
        form.birthdate = new Date(newVal.birthdate);
        form.full_name = newVal.full_name;
        form.phones = newVal.phones;
        form.whatsapp = newVal.whatsapp;
        form.address = newVal.address;
        form.email = newVal.email;
        form.eps = newVal.eps;
    }
});

onMounted(() => {
    loadDocumentTypes();
});

const updateContact = () => {

    validateBeforeSubmit()

    if (Object.keys(form.errors).length > 0){ 
        //toast.add({ severity: 'success', summary: 'Éxito', detail: 'Contacto creado', life: 3000 });
        return
    }

    form.put(route('contacts.update', props.contact.id), {
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Éxito', detail: 'Contacto actualizado', life: 3000 });
            emit('updated'); // Notifica al padre que se actualizó
            emit('close');   // Cierra el Dialog
        },
        onError: (errors) => {
            toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo actualizar', life: 3000 });
        }
    });
};

function handleCancel() {
    emit('close');
}


function validateBirthdate() {
  const raw = form.value.birthdate

  if (!raw) return

  const parsed = typeof raw === 'string'
    ? parse(raw, DATE_FORMAT, new Date())
    : raw

  if (isValid(parsed)) {
    form.value.birthdate = parsed
    form.value.errors.birthdate = ''
  } else {
    form.value.errors.birthdate = 'Fecha inválida. Usa el formato dd/mm/aaaa.'
  }
}

const { validateAndFormatWhatsapp } = useWhatsappValidator()

function validateBeforeSubmit() {
    form.errors = {}

    const result = validateAndFormatWhatsapp(form.whatsapp)
    if (!result.valid) {
        form.errors.whatsapp = result.error
    } else {
        form.whatsapp = result.formatted
    }
}

</script>

<template>

<Dialog :visible="visible" modal header="Editar Contacto" @update:visible="emit('close')" style="width: 600px">
    <div class="flex flex-col gap-4">
        <div>
            <label for="document_number" class="block text-sm font-medium">Numero de Documento</label>
            <InputText id="document_number" disabled v-model="form.document_number" class="w-full" />
        </div>
        <div class="flex gap-x-4">
            <div class="w-1/2">
                <label for="document_type_id" class="block text-sm font-medium">Tipo de Documento</label>
                    <Select 
                        v-model="form.document_type_id" 
                        :options="documentTypes" 
                        optionLabel="name" 
                        optionValue="id" 
                        placeholder="Seleccione un tipo" 
                        showClear
                    />
                    <FormError :error="form.errors.document_type_id" />
            </div>
            <div class="w-1/2">
            <label for="birthdate" class="block text-sm font-medium">Fecha de Nacimiento</label>
                <DatePicker 
                    id="birthdate" 
                    v-model="form.birthdate"
                    manualInput
                    dateFormat="dd/mm/yy"
                    showIcon
                    placeholder="dd/mm/aaaa"
                    
                />
                <FormError :error="form.errors.birthdate" />
            </div> 
        </div>       
        <div>
            <label for="full_name" class="block text-sm font-medium">Nombre Completo</label>
            <InputText id="full_name" v-model="form.full_name" class="w-full" 
            @input="(e) => form.full_name = applyMask({
                    value: e.target.value,                    
                    toUppercase:true
                })"
                @keydown="(e) => handleKeydown({
                    event: e,
                    modelValue: form.full_name,
                    maxLength: 70                    
                })"                
            />
            <FormError :error="form.errors.full_name" />
        </div>
        <div class="flex gap-x-4">
            <div class="w-1/2">
                <label for="phones" class="block text-sm font-medium">Telefonos</label>
                <InputText id="phones" v-model="form.phones" class="w-full" />
            </div>
            <div class="w-1/2">
                <label for="whatsapp" class="block text-sm font-medium">Whatsapp</label>
                <InputText 
                    id="whatsapp" 
                    v-model="form.whatsapp" 
                    class="w-full" 
                    @keydown="(e) => handleKeydown({
                        event: e,
                        modelValue: form.whatsapp,
                        maxLength: 12,
                        onlyNumbers: true                    
                    })" 
                />
                <FormError :error="form.errors.whatsapp" />
            </div>
        </div>
        <div>
            <label for="address" class="block text-sm font-medium">Dirección</label>
            <InputText 
                @input="(e) => form.address = applyMask({
                            value: e.target.value,                    
                            toUppercase:true
                        })"
                id="address" 
                v-model="form.address" 
                class="w-full" 
            />
        </div>
        <div>
            <label for="email" class="block text-sm font-medium">Email</label>
            <InputText id="email" v-model="form.email" class="w-full"
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
            <label for="eps" class="block text-sm font-medium">EPS</label>
            <InputText id="eps" v-model="form.eps" class="w-full" 
                @input="(e) => form.eps = applyMask({
                        value: e.target.value,                    
                        toUppercase:true
                    })"
                    @keydown="(e) => handleKeydown({
                        event: e,
                        modelValue: form.eps, // 👈 pasa el valor actual
                        maxLength: 30                    
                    })"                
            />

        </div>

        <div class="flex justify-end gap-2 mt-4">
            <Button label="Cancelar" severity="secondary" @click="handleCancel" />
            <Button label="Guardar" icon="pi pi-check" @click="updateContact" />
        </div>
    </div>
</Dialog>

</template>