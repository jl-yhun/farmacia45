import { createApp } from 'vue'
import AppTransferencias from '../components/transferencias/AppTransferencias'
import NumberInputGroup from '../components/UI/NumberInputGroup'
import TextArea from '../components/UI/TextArea'
import Button from '../components/UI/Button'
import Notification from '../components/UI/Notification'

const app = createApp(AppTransferencias);
app.component('NumberInputGroup', NumberInputGroup)
app.component('TextArea', TextArea)
app.component('Button', Button)
app.component('Notification', Notification)

app.mount('#transferencias-app')