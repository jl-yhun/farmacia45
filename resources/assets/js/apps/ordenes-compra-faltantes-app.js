import { createApp } from 'vue'
import AppFaltantes from '../components/ordenes-compra/AppFaltantes'
import TextInput from '../components/UI/TextInput'
import NumberInput from '../components/UI/NumberInput'
import NumberInputGroup from '../components/UI/NumberInputGroup'
import Button from '../components/UI/Button'
import ButtonLink from '../components/UI/ButtonLink'
import Notification from '../components/UI/Notification'
import Select from '../components/UI/Select'
import Popper from "vue3-popper";
import '../../css/popper-theme.css';

const app = createApp(AppFaltantes)
app.component('TextInput', TextInput)
app.component('NumberInput', NumberInput)
app.component('NumberInputGroup', NumberInputGroup)
app.component('Button', Button)
app.component('ButtonLink', ButtonLink)
app.component('Notification', Notification)
app.component('Select', Select)
app.component('Popper', Popper)

app.mount('#ordenes-compra-faltantes-app')