import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import InputNumber from 'primevue/inputnumber';
import Dropdown from 'primevue/dropdown';
import MultiSelect from 'primevue/multiselect';
import Calendar from 'primevue/calendar';
import Checkbox from 'primevue/checkbox';
import RadioButton from 'primevue/radiobutton';
import Dialog from 'primevue/dialog';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Toast from 'primevue/toast';
import ConfirmDialog from 'primevue/confirmdialog';
import Tooltip from 'primevue/tooltip';
import Password from 'primevue/password';
import Datepicker from 'primevue/datepicker';
import Select from 'primevue/select';

export default function registerPrimeVueComponents(app) {
    app.component('Button', Button);
    app.component('InputText', InputText);
    app.component('Textarea', Textarea);
    app.component('InputNumber', InputNumber);
    app.component('Dropdown', Dropdown);
    app.component('MultiSelect', MultiSelect);
    app.component('Calendar', Calendar);
    app.component('Checkbox', Checkbox);
    app.component('RadioButton', RadioButton);
    app.component('Dialog', Dialog);
    app.component('DataTable', DataTable);
    app.component('Column', Column);
    app.component('Toast', Toast);
    app.component('ConfirmDialog', ConfirmDialog);
    app.component('Password', Password);
    app.component('Datepicker', Datepicker);
    app.component('Select', Select);
    app.directive('tooltip', Tooltip);
}