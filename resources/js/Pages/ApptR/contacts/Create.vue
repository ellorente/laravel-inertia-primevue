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



const documentTypes = ref([]);


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

onMounted(() => {
    loadDocumentTypes();
});

const saveContact = () => {

    validateBeforeSubmit()

    if (Object.keys(form.errors).length > 0){ 
        //toast.add({ severity: 'success', summary: 'Éxito', detail: 'Contacto creado', life: 3000 });
        return
    }

    form.post(route('contacts.store'), {
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Éxito', detail: 'Contacto creado', life: 3000 });
            emit('created'); // <-- avisamos que se creó correctamente
            form.reset();
        }
    });
};

const DATE_FORMAT = 'dd/MM/yyyy'

function validateBirthdate() {
  const raw = form.birthdate

  if (!raw) return

  const parsed = typeof raw === 'string'
    ? parse(raw, DATE_FORMAT, new Date())
    : raw

  if (isValid(parsed)) {
    form.birthdate = parsed
    form.errors.birthdate = ''
  } else {
    form.errors.birthdate = 'Fecha inválida. Usa el formato dd/mm/aaaa.'
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
    <form @submit.prevent="saveContact" class="space-y-4 p-4">

        <div>
            <label class="block mb-1 font-semibold">Número de documento</label>
            <InputText 
                v-model="form.document_number" 
                class="w-full"
                :class="{ 'p-invalid': form.errors.document_number }"
                placeholder="Número de documento. Max: 10 nros"
                @input="(e) => form.document_number = applyMask({
                    value: e.target.value,
                    maxLength: 10,
                    onlyNumbers: true
                })"
                @keydown="(e) => handleKeydown({
                    event: e,
                    modelValue: form.document_number,
                    maxLength: 10,
                    onlyNumbers: true
                })"
            />
            <FormError :error="form.errors.document_number" />
        </div>

        <div class="flex gap-x-4">
            <div class="w-[50%]">
                <label class="block mb-1 font-semibold">Tipo de documento</label>
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

            <div class="w-[50%]">
                <label class="block mb-1 font-semibold">Fecha de nacimiento</label>
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
            <label class="block mb-1 font-semibold">Nombre completo</label>
            <InputText 
                v-model="form.full_name" 
                class="w-full" 
                :class="{ 'p-invalid': form.errors.full_name }"
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
                <label class="block mb-1 font-semibold">Telefonos</label>
                <InputText 
                    v-model="form.phones" 
                    class="w-full" 
                />            
            </div>
            <div class="w-1/2">
                <label class="block mb-1 font-semibold">Whatsapp</label>
                <InputText 
                    v-model="form.whatsapp" 
                    class="w-full"
                    :class="{ 'p-invalid': form.errors.whatsapp }"
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
            <label class="block mb-1 font-semibold">Dirección</label>
            <InputText 
                @input="(e) => form.address = applyMask({
                        value: e.target.value,                    
                        toUppercase:true
                    })"
                v-model="form.address" 
                class="w-full"                
            />            
        </div>        

        <div>
            <label class="block mb-1 font-semibold">Email</label>
            <InputText 
                v-model="form.email" 
                class="w-full"
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
            <label class="block mb-1 font-semibold">EPS</label>
            <InputText 
                v-model="form.eps" 
                class="w-full"
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