import { onMounted, ref } from "vue";

export function useAuth() {
    const user = ref(null)

    onMounted(async () => {
        await getCurrentUser()
    })

    const getCurrentUser = async () => {
        try {
            const res = await axios.get('/api/user');
            user.value = res.data;
        } catch (error) {
            console.error('Error al obtener usuario autenticado. ' + error);
        }
    }

    return { user };
}