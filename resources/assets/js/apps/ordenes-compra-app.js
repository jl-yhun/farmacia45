import { createApp } from 'vue'
import AppOrdenesCompra from '../components/ordenes-compra/AppOrdenesCompra'
import TextInput from '../components/UI/TextInput'
import NumberInput from '../components/UI/NumberInput'
import NumberInputGroup from '../components/UI/NumberInputGroup'
import Button from '../components/UI/Button'
import ButtonLink from '../components/UI/ButtonLink'
import Notification from '../components/UI/Notification'
import ConfirmModal from '../components/UI/ConfirmModal'
import Select from '../components/UI/Select'
import VueJsTour from '@globalhive/vuejs-tour'
import Popper from "vue3-popper";
import '../../css/popper-theme.css';
import '@globalhive/vuejs-tour/dist/style.css'


const app = createApp(AppOrdenesCompra)
app.use(VueJsTour)
app.component('TextInput', TextInput)
app.component('NumberInput', NumberInput)
app.component('NumberInputGroup', NumberInputGroup)
app.component('Button', Button)
app.component('ButtonLink', ButtonLink)
app.component('Notification', Notification)
app.component('ConfirmModal', ConfirmModal)
app.component('Select', Select)
app.component('Popper', Popper)

app.mount('#ordenes-compra-app')