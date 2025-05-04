<script setup>
import AppLayout from '@/Layout/AppLayout.vue';
import DeleteUserForm from '@/Pages/Profile/Partials/DeleteUserForm.vue';
import LogoutOtherBrowserSessionsForm from '@/Pages/Profile/Partials/LogoutOtherBrowserSessionsForm.vue';
import SectionBorder from '@/Jetstream/Components/SectionBorder.vue';
import TwoFactorAuthenticationForm from '@/Pages/Profile/Partials/TwoFactorAuthenticationForm.vue';
import UpdatePasswordForm from '@/Pages/Profile/Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from '@/Pages/Profile/Partials/UpdateProfileInformationForm.vue';



import { router } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { route } from 'ziggy-js'; // ziggy import

const activeSection = ref('profile');
const expandedKeys = ref({});

onMounted(() => {
  const sectionFromUrl = new URLSearchParams(window.location.search).get('section');
  if (sectionFromUrl) {
    activeSection.value = sectionFromUrl;
    expandedKeys.value = { [sectionFromUrl]: true };
  }
});

// Cambiar sección y actualizar la URL (sin recargar)
const changeSection = (section) => {
  activeSection.value = section;
  expandedKeys.value = { [section]: true };

  const url = route('profile.show', {}, false, Ziggy) + `?section=${section}`;
  router.visit(url, {
    preserveScroll: true,
    preserveState: true,
    replace: true,
  });
};

// Opciones del menú lateral
const menuItems = [
  {
    key: 'profile',
    label: 'Profile',
    icon: 'pi pi-user',
    command: () => changeSection('profile'),
  },
  {
    key: 'password',
    label: 'Password',
    icon: 'pi pi-lock',
    command: () => changeSection('password'),
  },
  {
    key: 'browsersessions',
    label: 'Browser Sessions',
    icon: 'pi pi-power-off',
    command: () => changeSection('browsersessions'),
  },
  {
    key: 'twofactor',
    label: 'Two Factor Authentication',
    icon: 'pi pi-envelope',
    command: () => changeSection('twofactor'),
  },
  {
    key: 'deleteuser',
    label: 'Delete User',
    icon: 'pi pi-user-minus',
    command: () => changeSection('deleteuser'),
  },
];




defineProps({
    confirmsTwoFactorAuthentication: Boolean,
    sessions: Array,
});
</script>

<template>
    <AppLayout>
        <template>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Profile
            </h2>
        </template>

        <div class="card">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

              <PanelMenu :model="menuItems" v-model:expandedKeys="expandedKeys">
                <template #item="{ item }">
                  <a
                    @click="item.command"
                    :class="[
                      'flex items-center px-4 py-2 cursor-pointer',
                      activeSection === item.key ? 'text-primary font-semibold' : 'hover:text-primary'
                    ]"
                  >
                    <i :class="item.icon"></i>
                    <span class="ml-2">{{ item.label }}</span>
                  </a>
                </template>
              </PanelMenu>

              <div class="col-span-3">
                    <UpdateProfileInformationForm :user="$page.props.auth.user" v-if="activeSection === 'profile'" />
                    <UpdatePasswordForm class="mt-10 sm:mt-0" v-if="activeSection === 'password'" />
                    <LogoutOtherBrowserSessionsForm :sessions="sessions" class="mt-10 sm:mt-0" v-if="activeSection === 'browsersessions'" />
                    <TwoFactorAuthenticationForm
                        :requires-confirmation="confirmsTwoFactorAuthentication"
                        class="mt-10 sm:mt-0" v-if="activeSection === 'twofactor'"
                    />
                    <DeleteUserForm class="mt-10 sm:mt-0" v-if="activeSection === 'deleteuser'" />
                </div>
                <!-- Sidebar 
                <div class="col-span-1">
                <PanelMenu :model="menuItems" />
                </div>

               
                <div class="col-span-3">
                    <UpdateProfileInformationForm :user="$page.props.auth.user" v-if="activeSection === 'profile'" />
                    <UpdatePasswordForm class="mt-10 sm:mt-0" v-if="activeSection === 'password'" />
                    <LogoutOtherBrowserSessionsForm :sessions="sessions" class="mt-10 sm:mt-0" v-if="activeSection === 'browsersessions'" />
                    <TwoFactorAuthenticationForm
                        :requires-confirmation="confirmsTwoFactorAuthentication"
                        class="mt-10 sm:mt-0" v-if="activeSection === 'twofactor'"
                    />
                    <DeleteUserForm class="mt-10 sm:mt-0" v-if="activeSection === 'deleteuser'" />
                </div>
              -->
            </div>
        </div>

    </AppLayout>
</template>
