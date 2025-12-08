<script setup>
import RoleAddDialog from "../../components/inventory/role/RoleAddDialog.vue";
    const data = ref([]);

    const headers = [
        { title: 'ID', key: 'id' },
        { title: 'Role', key: 'name' },
        { title: 'Fecha de Registro', key: 'created_at' },
        { title: 'Permisos', key: 'permissions_pluck' },
        { title: 'Action', key: 'action' },
        ]

    const isRoleAddDialogVisible = ref(false);
    const isRoleEditDialogVisible = ref(false);
    const isRoleDeleteDialogVisible = ref(false);

    const list_roles = ref([]);
    const searchQuery = ref(null);
    const role_selected_edit = ref(null);
    const role_selected_delete = ref(null);

    const list = async () => {
        try {
            const resp = await $api("role?search=" + (searchQuery.value ? searchQuery.value : ''), {
            method: "GET",
            onReponseError({response}){
                console.log(response._data.error);
            }
            })
            console.log(resp);
            list_roles.value = resp.roles;
        } catch (error) {
            console.log(error);
        }
    }

    const addNewRole = (NewRole) => {
        console.log(NewRole);
        let backup = list_roles.value;
        list_roles.value = [];
        backup.unshift(NewRole);
        setTimeout(() => {
            list_roles.value = backup;
        }, 50);
    }

    const addEditRole = (editRole) => {
        console.log(editRole);
        let backup = list_roles.value;
        list_roles.value = [];
        let INDEX = backup.findIndex((rol) => rol.id == editRole.id);
        if(INDEX != -1){
            backup[INDEX] = editRole;
        }
        setTimeout(() => {
            list_roles.value = backup;
        }, 50);
    }

    const addDeleteRole = (Role) => {
        console.log(Role);
        let backup = list_roles.value;
        list_roles.value = [];
        let INDEX = backup.findIndex((rol) => rol.id == Role.id);
        if(INDEX != -1){
            backup.splice(INDEX,1);
        }
        setTimeout(() => {
            list_roles.value = backup;
        }, 50);
    }
    
    const editItem = (item) => {
        console.log(item);
        isRoleEditDialogVisible.value = true;
        role_selected_edit.value = item;
    }
    const deleteItem = (item) => {
        isRoleDeleteDialogVisible.value = true;
        role_selected_delete.value = item;
    }

    // ESTE EVENTO SE EJECUTA ANTES QUE SE RENDERIZE TODO EL CONTENIDO,
    // OUNMOUNTED ES PARTE DE VUE
    onMounted(() => {
        list();
    })
</script>
<template>
    <div>

        <VCard title="🔐 Roles y Permisos">
            <VCardText>
                <VRow class="justify-space-between">
                    <VCol cols="4">
                        <VTextField
                        placeholder="Search Role"
                        density="compact"
                        class="me-3"
                        v-model = "searchQuery"
                        @keyup.enter="list"
                        />
                    </VCol>
                    <VCol cols="3" class="text-end">
                        <VBtn @click="isRoleAddDialogVisible = !isRoleAddDialogVisible">
                            Add Role 
                            <VIcon end icon="ri-add-line" />
                        </VBtn>
                    </VCol>
                </VRow>    
            </VCardText>
            <VDataTable
                :headers="headers"
                :items="list_roles"
                :items-per-page="5"
                class="text-no-wrap"
            >
                <template #item.id="{ item }">
                <span class="text-h6">{{ item.id }}</span>
                </template>
                <template #item.permissions_pluck="{ item }">
                    <ul>
                        <li v-for="(permission, index) in item.permissions_pluck" :key="index">
                            {{ permission }}
                        </li>
                    </ul>
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

        <RoleAddDialog v-model:isDialogVisible="isRoleAddDialogVisible" @addRole="addNewRole"></RoleAddDialog>        
        <RoleEditDialog v-if="role_selected_edit && isRoleEditDialogVisible" v-model:isDialogVisible="isRoleEditDialogVisible" :roleSelected="role_selected_edit" @editRole="addEditRole"></RoleEditDialog>        
        <RoleDeleteDialog v-if="role_selected_delete && isRoleDeleteDialogVisible" v-model:isDialogVisible="isRoleDeleteDialogVisible" :roleSelected="role_selected_delete" @deleteRole="addDeleteRole"></RoleDeleteDialog>        

    </div>
</template>