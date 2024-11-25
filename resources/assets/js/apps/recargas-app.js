import { createApp } from 'vue'
import AppRecargas from '../components/recargas/AppRecargas'
import NumberInputGroup from '../components/UI/NumberInputGroup'
import Select from '../components/UI/Select'
import Button from '../components/UI/Button'
import Notification from '../components/UI/Notification'

const app = createApp(AppRecargas);
app.component('NumberInputGroup', NumberInputGroup)
app.component('Select', Select)
app.component('Button', Button)
app.component('Notification', Notification)

app.mount('#recargas-app')