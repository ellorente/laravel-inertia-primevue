<script setup>
import { ref } from 'vue';

import AppMenuItem from './AppMenuItem.vue';

import { usePage } from '@inertiajs/vue3';

const { props } = usePage();


const model = ref([
    {
        label: 'Home',
        items: [{ label: 'Dashboard', icon: 'pi pi-fw pi-home', to: '/dashboard' }]
    },
    {
        label: 'Contactos',
        items: [
            { label: 'Contactos', icon: 'pi pi-fw pi-users', to: '/apptr/contacts' },
        ]
    },
]);

// Agregar menú de Users solo si es admin
if (props.user?.isAdmin) {
    model.value.push(
    {
        label: 'Administración',
        icon: 'pi pi-fw pi-briefcase',
        to: '/pages',
        items: [
            {
                label: 'User Management',
                icon: 'pi pi-fw pi-user',
                items: [
                    {
                        label: 'Users',
                        icon: 'pi pi-fw pi-user',
                        to: '/users'
                    },
                    {
                        label: 'Roles',
                        icon: 'pi pi-fw pi-lock',
                        to: '/auth/error'
                    },
                    {
                        label: 'Permissions',
                        icon: 'pi pi-fw pi-check-square',
                        to: '/auth/access'
                    }
                ]
            },
            {
                label: 'Configuraciones',
                icon: 'pi pi-fw pi-cog',
                items: [
                    {
                        label: 'EvolutionApi',
                        icon: 'pi pi-fw pi-whatsapp',
                        to: '/users'
                    },
                ]
            },
        ]
    },
    
);
}

/*
const model = ref([
    {
        label: 'Home',
        items: [{ label: 'Dashboard', icon: 'pi pi-fw pi-home', to: '/dashboard' }]
    },
    {
        label: 'Contactos',
        items: [
            { label: 'Contactos', icon: 'pi pi-fw pi-users', to: '/apptr/contacts' },
        ]
    },  
    {
        label: 'Users',
        icon: 'pi pi-fw pi-briefcase',
        to: '/pages',
        items: [
            {
                label: 'User Management',
                icon: 'pi pi-fw pi-user',
                items: [
                    {
                        label: 'Users',
                        icon: 'pi pi-fw pi-user',
                        to: '/users'
                    },
                    {
                        label: 'Roles',
                        icon: 'pi pi-fw pi-lock',
                        to: '/auth/error'
                    },
                    {
                        label: 'Permisions',
                        icon: 'pi pi-fw pi-check-square',
                        to: '/auth/access'
                    }
                ]
            },

        ]
    },
]);

*/
</script>

<template>
    <ul class="layout-menu">
        <template v-for="(item, i) in model" :key="item">
            <app-menu-item v-if="!item.separator" :item="item" :index="i"></app-menu-item>
            <li v-if="item.separator" class="menu-separator"></li>
        </template>
    </ul>
</template>

<style lang="scss" scoped></style>


