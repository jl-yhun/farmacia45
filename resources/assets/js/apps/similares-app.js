import { createApp } from 'vue'
import AppSimilares from '../components/similares/AppSimilares'
import NumberInputGroup from '../components/UI/NumberInputGroup'
import TextArea from '../components/UI/TextArea'
import Button from '../components/UI/Button'
import Notification from '../components/UI/Notification'
import { ModelListSelect } from 'vue-search-select'
import "vue-search-select/dist/VueSearchSelect.css"

const mountId = '#similares-app'
const mountEl = document.querySelector(mountId)

const app = createApp(AppSimilares, { ...mountEl.dataset });
app.component('NumberInputGroup', NumberInputGroup)
app.component('TextArea', TextArea)
app.component('Button', Button)
app.component('Notification', Notification)
app.component('ModelListSelect', ModelListSelect)

app.mount(mountId)