import { createApp } from 'vue'
import AppAperturasCaja from '../components/aperturas-caja/AppAperturasCaja'
import TextInput from '../components/UI/TextInput'
import NumberInput from '../components/UI/NumberInput'
import Button from '../components/UI/Button'
import ButtonLink from '../components/UI/ButtonLink'
import Notification from '../components/UI/Notification'

const app = createApp(AppAperturasCaja)
app.component('TextInput', TextInput)
app.component('NumberInput', NumberInput)
app.component('Button', Button)
app.component('ButtonLink', ButtonLink)
app.component('Notification', Notification)

app.mount('#aperturas-caja-app')