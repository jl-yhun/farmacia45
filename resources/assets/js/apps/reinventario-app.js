import { createApp } from 'vue'
import AppReinventario from '../components/reinventario/AppReinventario'
import TextInput from '../components/UI/TextInput'
import NumberInput from '../components/UI/NumberInput'
import NumberInputGroup from '../components/UI/NumberInputGroup'
import Button from '../components/UI/Button'
import ButtonLink from '../components/UI/ButtonLink'
import Popper from "vue3-popper";
import '../../css/popper-theme.css';

const app = createApp(AppReinventario)
app.component('TextInput', TextInput)
app.component('NumberInputGroup', NumberInputGroup)
app.component('NumberInput', NumberInput)
app.component('Button', Button)
app.component('ButtonLink', ButtonLink)
app.component('Popper', Popper)

app.mount('#reinventario-app')