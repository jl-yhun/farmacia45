import { createApp } from 'vue'
import AppDiff from '../components/reinventario/AppDiff'
import ButtonLink from '../components/UI/ButtonLink'
import Button from '../components/UI/Button'
import ConfirmModal from '../components/UI/ConfirmModal'
import Popper from "vue3-popper";
import '../../css/popper-theme.css';

const app = createApp(AppDiff)
app.component('ButtonLink', ButtonLink)
app.component('Button', Button)
app.component('ConfirmModal', ConfirmModal)
app.component('Popper', Popper)

app.mount('#reinventario-diff-app')