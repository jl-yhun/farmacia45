import { createApp } from 'vue'
import AppServicios from '../components/servicios/AppServicios'
import NumberInputGroup from '../components/UI/NumberInputGroup'
import TextInput from '../components/UI/TextInput'
import Button from '../components/UI/Button'
import Select from '../components/UI/Select'
import Notification from '../components/UI/Notification'

const app = createApp(AppServicios);
app.component('NumberInputGroup', NumberInputGroup)
app.component('TextInput', TextInput)
app.component('Button', Button)
app.component('Select', Select)
app.component('Notification', Notification)

app.mount('#servicios-app')