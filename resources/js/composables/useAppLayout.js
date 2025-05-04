import { ref, computed, onMounted, onUnmounted, watchEffect } from 'vue';
import { usePage, useForm, router} from '@inertiajs/vue3';
import { route } from 'ziggy-js';


export function useAppLayout() {
    const page = usePage();
    const currentRoute = computed(() => {
        // Access page.url to trigger re-computation on navigation.
        /* eslint-disable @typescript-eslint/no-unused-vars */
        const url = page.url;
        /* eslint-enable @typescript-eslint/no-unused-vars */
        return route().current();
    });

    // User menu and logout functionality.
    const logoutForm = useForm({});
    const logout = () => {
        logoutForm.post(route('logout'));
    };

    const userMenuItems = ref( [
        {
            label: 'Settings',
            icon: 'pi pi-cog',
            command: () => router.visit(route('profile.show'))
            
        },
        {
            separator: true
        },
        {
            label: 'Log Out',
            icon: 'pi pi-sign-out',
            command: () => logout(),
        },
    ]);

    // Mobile menu
    const mobileMenuOpen = ref(false);
    const windowWidth = ref(window.innerWidth);
    const updateWidth = () => {
        windowWidth.value = window.innerWidth;
    };
    onMounted(() => {
        window.addEventListener('resize', updateWidth);
    });
    onUnmounted(() => {
        window.removeEventListener('resize', updateWidth);
    });
    watchEffect(() => {
        if (windowWidth.value > 1024) {
            mobileMenuOpen.value = false;
        }
    });

    return {
        currentRoute,
        //menuItems,
        userMenuItems,
        mobileMenuOpen,
        logout,
    };
}
/*

import { useForm, router} from '@inertiajs/vue3';
import { route } from 'ziggy-js'


const items = ref([
    {
        label: 'Router Link',
        icon: 'pi pi-palette',        
        command: () => router.visit(route('profile.show'))
    },
    {
        label: 'Programmatic',
        icon: 'pi pi-link',
        command: () => {
            routervr.push('/introduction');
        }
    },
    {
        label: 'External',
        icon: 'pi pi-home',
        url: 'https://vuejs.org/'
    }
]);



    <div class="card flex justify-center">
        <Menu :model="items">
            <template #item="{ item, props }">
                <a v-if="item.route" v-ripple :href="item.route" v-bind="props.action" @click.prevent="() => inertia.visit(item.route)">
                    <span :class="item.icon" />
                    <span class="ml-2">{{ item.label }}</span>
                </a>
                <a v-else-if="item.command" v-ripple href="#" v-bind="props.action" @click.prevent="item.command">
                    <span :class="item.icon" />
                    <span class="ml-2">{{ item.label }}</span>
                </a>
                <a v-else v-ripple :href="item.url" :target="item.target" v-bind="props.action">
                    <span :class="item.icon" />
                    <span class="ml-2">{{ item.label }}</span>
                </a>
            </template>
        </Menu>
    </div>
*/