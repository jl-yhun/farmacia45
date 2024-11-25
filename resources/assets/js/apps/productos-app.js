import { createApp } from 'vue'
import AppProductos from '../components/productos/AppProductos.vue'
import ButtonLink from '../components/UI/ButtonLink'
import Button from '../components/UI/Button'
import Notification from '../components/UI/Notification'
import TextInput from '../components/UI/TextInput'
import InputTag from '../components/UI/InputTag'
import DateInput from '../components/UI/DateInput'
import NumberInput from '../components/UI/NumberInput'
import NumberInputGroup from '../components/UI/NumberInputGroup'
import Select from '../components/UI/Select'
import TextArea from '../components/UI/TextArea'
import ConfirmModal from '../components/UI/ConfirmModal'
import { ModelListSelect } from 'vue-search-select'
import "vue-search-select/dist/VueSearchSelect.css"
import Popper from "vue3-popper";
import '../../css/popper-theme.css';

const mountId = '#productos-app'
const mountEl = document.querySelector(mountId)

const app = createApp(AppProductos, { ...mountEl.dataset });
app.component('ButtonLink', ButtonLink)
app.component('Button', Button)
app.component('Notification', Notification)
app.component('TextInput', TextInput)
app.component('DateInput', DateInput)
app.component('NumberInput', NumberInput)
app.component('NumberInputGroup', NumberInputGroup)
app.component('Select', Select)
app.component('TextArea', TextArea)
app.component('ConfirmModal', ConfirmModal)
app.component('ModelListSelect', ModelListSelect)
app.component('Popper', Popper);
app.component('InputTag', InputTag)

app.mount(mountId)