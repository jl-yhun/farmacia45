import { createApp } from 'vue'
import AppMovimientos from '../components/movimientos/AppMovimientos'
import NumberInputGroup from '../components/UI/NumberInputGroup'
import TextArea from '../components/UI/TextArea'
import Select from '../components/UI/Select'
import Button from '../components/UI/Button'
import Notification from '../components/UI/Notification'

const app = createApp(AppMovimientos);
app.component('NumberInputGroup', NumberInputGroup)
app.component('TextArea', TextArea)
app.component('Select', Select)
app.component('Button', Button)
app.component('Notification', Notification)

app.mount('#movimientos-app')