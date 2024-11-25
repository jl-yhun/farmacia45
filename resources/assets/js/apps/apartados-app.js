import { createApp } from 'vue'
import AppApartados from '../components/apartados/AppApartados'
import TextInput from '../components/UI/TextInput'
import Notification from '../components/UI/Notification'

const app = createApp(AppApartados)
app.component('TextInput', TextInput)
app.component('Notification', Notification)

app.mount('#apartados-app')