<script setup>
import UserAddDialog from "../../components/inventory/users/UserAddDialog.vue";
import RoleEditDialog from "../../components/inventory/role/RoleEditDialog.vue";

    const headers = [
        { title: 'ID', key: 'id' },
        { title: 'Nombre Completo', key: 'full_name' },
        { title: 'Email', key: 'email' },
        { title: 'User', key: 'user' },
        { title: 'Sucursal', key: 'sucursale' },
        { title: 'Telefono', key: 'phone' },
        { title: 'Estado', key: 'state' },
        { title: 'Fecha de Registro', key: 'created_at' },
        { title: 'Action', key: 'action' },
        ]

    const isUserAddDialogVisible = ref(false);
    const isUserEditDialogVisible = ref(false);
    const isUserDeleteDialogVisible = ref(false);

    const list_users = ref([]);
    const sucursales = ref([]);
    const roles = ref([]);
    const searchQuery = ref(null);
    const user_selected_edit = ref(null);
    const user_selected_delete = ref(null);

    const list = async () => {
        try {
            const resp = await $api("users?search=" + (searchQuery.value ? searchQuery.value : ''), {
            method: "GET",
            onReponseError({response}){
                console.log(response._data.error);
            }
            })
            console.log(resp);
            list_users.value = resp.users;
        } catch (error) {
            console.log(error);
        }
    }

    // CON ESTO VAMOS A LISTAR LOS DATOS DE LA FUNCION CONFIG 
    const config = async () => {
        try {
            const resp = await $api("users/config", {
                method: "GET",
                onReponseError({response}){
                    console.log(response._data.error);
                }
            })
            console.log(resp);
            sucursales.value = resp.sucursales;
            roles.value = resp.roles;
        } catch (error) {
            console.log(error);
        }
    }

    const addNewUser = (NewUser) => {
        console.log(NewUser);
        let backup = list_users.value;
        list_users.value = [];
        backup.unshift(NewUser);
        setTimeout(() => {
            list_users.value = backup;
        }, 50);
    }

    const addEditUser = (editUser) => {
        console.log(editUser);
        let backup = list_users.value;
        list_users.value = [];
        let INDEX = backup.findIndex((rol) => rol.id == editUser.id);
        if(INDEX != -1){
            backup[INDEX] = editUser;
        }
        setTimeout(() => {
            list_users.value = backup;
        }, 50);
    }

    const addDeleteUser = (User) => {
        console.log(User);
        let backup = list_users.value;
        list_users.value = [];
        let INDEX = backup.findIndex((rol) => rol.id == User.id);
        if(INDEX != -1){
            backup.splice(INDEX,1);
        }
        setTimeout(() => {
            list_users.value = backup;
        }, 50);
    }
    
    const editItem = (item) => {
        console.log(item);
        isUserEditDialogVisible.value = true;
        user_selected_edit.value = item;
    }
    const deleteItem = (item) => {
        isUserDeleteDialogVisible.value = true;
        user_selected_delete.value = item;
    }

    const avatarText = value => {
        if(!value)
            return '';
        const nameArray = value.split('')

        return nameArray.map(word => word.charAt(0).toUpperCase()).join('')
    }

    // ESTE EVENTO SE EJECUTA ANTES QUE SE RENDERIZE TODO EL CONTENIDO,
    // OUNMOUNTED ES PARTE DE VUE
    onMounted(() => {
        config();
        list();
    });
</script>
<template>
    <div>

        <VCard title="👦 Usuarios">
            <VCardText>
                <VRow class="justify-space-between">
                    <VCol cols="4">
                        <VTextField
                        placeholder="Search User"
                        density="compact"
                        class="me-3"
                        v-model = "searchQuery"
                        @keyup.enter="list"
                        />
                    </VCol>
                    <VCol cols="3" class="text-end">
                        <VBtn @click="isUserAddDialogVisible = !isUserAddDialogVisible">
                            Add User 
                            <VIcon end icon="ri-add-line" />
                        </VBtn>
                    </VCol>
                </VRow>    
            </VCardText>
            <VDataTable
                :headers="headers"
                :items="list_users"
                :items-per-page="5"
                class="text-no-wrap"
            >
                <template #item.id="{ item }">
                    <span class="text-h6">{{ item.id }}</span>
                </template>

                <template #item.full_name="{ item }">
                     <div class="d-flex align-center">
                         <VAvatar
                                 size="32"
                                 :color="item.avatar ? '' : 'primary'"
                                 :class="item.avatar ? '' : 'v-avatar-light-bg primary--text'"
                                 :variant="!item.avatar ? 'tonal' : undefined"
                             >
                             <VImg
                                 v-if="item.avatar"
                                 :src="item.avatar"
                             />
                             <span
                                 v-else
                                 class="text-sm"
                             >{{ avatarText(item.full_name) }}</span>
                         </VAvatar>
                         <div class="d-flex flex-column ms-3">
                            <span class="d-block font-weight-medium text-high-emphasis text-truncate">{{ item.full_name }}</span>
                            <!-- <small>{{ item.post }}</small> -->
                        </div>
                     </div>
                </template>
                <template #item.user="{ item }">
                    <span class="text-h6">{{ item.name }}</span>
                </template>
                <template #item.sucursale="{ item }">
                    <span class="text-h6">{{ item.sucursale.name }}</span>
                </template>

                <template #item.state="{ item }">
                    <VChip color="primary" v-if="item.state == 1">
                        Activo
                    </VChip>
                    <VChip color="error" v-if="item.state == 2">
                        Activo
                    </VChip>
                </template>

                <!-- Actions -->
                <template #item.action="{ item }">
                <div class="d-flex gap-1">
                    <IconBtn
                    size="small"
                    @click="editItem(item)"
                    >
                    <VIcon icon="ri-pencil-line" />
                    </IconBtn>
                    <IconBtn
                    size="small"
                    @click="deleteItem(item)"
                    >
                    <VIcon icon="ri-delete-bin-line" />
                    </IconBtn>
                </div>
                </template>
            </VDataTable>
        </VCard>

        <UserAddDialog v-model:isDialogVisible="isUserAddDialogVisible" :sucursales="sucursales" :roles="roles" @addUser="addNewUser"></UserAddDialog>
        <UserEditDialog v-if="user_selected_edit && isUserEditDialogVisible" :sucursales="sucursales" :roles="roles" v-model:isDialogVisible="isUserEditDialogVisible" :selectedUser="user_selected_edit" @editUser="addEditUser"></UserEditDialog>        
        <UserDeleteDialog v-if="user_selected_delete && isUserDeleteDialogVisible" v-model:isDialogVisible="isUserDeleteDialogVisible" :userSelected="user_selected_delete" @deleteUser="addDeleteUser"></UserDeleteDialog>        

    </div>
</template>