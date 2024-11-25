import { createApp } from 'vue'
import AppVentas from '../components/ventas/AppVentas'
import TextInput from '../components/UI/TextInput'
import Notification from '../components/UI/Notification'
import ConfirmModal from '../components/UI/ConfirmModal'
import Button from '../components/UI/Button'
import DateInput from '../components/UI/DateInput'
import Popper from "vue3-popper";
import '../../css/popper-theme.css';

const mountId = '#ventas-app'
const mountEl = document.querySelector(mountId)

const app = createApp(AppVentas, { ...mountEl.dataset })
app.component('TextInput', TextInput)
app.component('Notification', Notification)
app.component('ConfirmModal', ConfirmModal)
app.component('Button', Button)
app.component('DateInput', DateInput)
app.component('Popper', Popper)

app.mount(mountId)