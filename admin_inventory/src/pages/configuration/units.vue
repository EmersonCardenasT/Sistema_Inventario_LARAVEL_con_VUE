<script setup>
// import RoleAddDialog from "../../components/inventory/role/RoleAddDialog.vue";

// CABEZERAS DE LAS TABLAS
    const headers = [
            { title: 'ID', key: 'id' },
            { title: 'Unidad', key: 'name' },
            { title: 'Descripcion', key: 'description' },
            { title: 'Estado', key: 'state' },
            { title: 'Fecha de Registro', key: 'created_at' },
            { title: 'Action', key: 'action' },
        ]

    const isUnitAddDialogVisible = ref(false);
    const isUnitEditDialogVisible = ref(false);
    const isUnitDeleteDialogVisible = ref(false);

    const list_units = ref([]);
    const searchQuery = ref(null);
    const unit_selected_edit = ref(null);
    const unit_selected_delete = ref(null);

    const list = async () => {
        try {
            const resp = await $api("units?search=" + (searchQuery.value ? searchQuery.value : ''), {
            method: "GET",
            onReponseError({response}){
                console.log(response._data.error);
            }
            })
            console.log(resp);
            list_units.value = resp.units;
        } catch (error) {
            console.log(error);
        }
    }

    const addNewUnit = (NewUnit) => {
        console.log(NewUnit);
        let backup = list_units.value;
        list_units.value = [];
        backup.unshift(NewUnit);
        setTimeout(() => {
            list_units.value = backup;
        }, 50);
    }

    const addEditUnit = (editUnit) => {
        console.log(editUnit);
        let backup = list_units.value;
        list_units.value = [];
        let INDEX = backup.findIndex((unit) => unit.id == editUnit.id);
        if(INDEX != -1){
            backup[INDEX] = editUnit;
        }
        setTimeout(() => {
            list_units.value = backup;
        }, 50);
    }

    const addDeleteUnit = (Unit) => {
        console.log(Unit);
        let backup = list_units.value;
        list_units.value = [];
        let INDEX = backup.findIndex((unit) => unit.id == Unit.id);
        if(INDEX != -1){
            backup.splice(INDEX,1);
        }
        setTimeout(() => {
            list_units.value = backup;
        }, 50);
    }
    
    const editItem = (item) => {
        console.log(item);
        isUnitEditDialogVisible.value = true;
        unit_selected_edit.value = item;
    }
    const deleteItem = (item) => {
        isUnitDeleteDialogVisible.value = true;
        unit_selected_delete.value = item;
    }

    // ESTE EVENTO SE EJECUTA ANTES QUE SE RENDERIZE TODO EL CONTENIDO,
    // OUNMOUNTED ES PARTE DE VUE
    onMounted(() => {
        list();
    })

    definePage({ meta: { permission: 'settings' }});
    
</script>
<template>
    <div>

        <VCard title="📦 Units">
            <VCardText>
                <VRow class="justify-space-between">
                    <VCol cols="4">
                        <VTextField
                        placeholder="Search Unit"
                        density="compact"
                        class="me-3"
                        v-model = "searchQuery"
                        @keyup.enter="list"
                        />
                    </VCol>
                    <VCol cols="3" class="text-end">
                        <VBtn @click="isUnitAddDialogVisible = !isUnitAddDialogVisible">
                            Add Units
                            <VIcon end icon="ri-add-line" />
                        </VBtn>
                    </VCol>
                </VRow>    
            </VCardText>
            <VDataTable
                :headers="headers"
                :items="list_units"
                :items-per-page="5"
                class="text-no-wrap"
            >
                <template #item.id="{ item }">
                <span class="text-h6">{{ item.id }}</span>
                </template>
                <template #item.state="{ item }">
                    <VChip color="primary" v-if="item.state == 1">
                        Activo
                    </VChip>
                    <VChip color="error" v-if="item.state == 2">
                        Inactivo
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

        <UnitAddDialog v-model:isDialogVisible="isUnitAddDialogVisible" @addUnit="addNewUnit"></UnitAddDialog>        
        <UnitEditDialog v-if="unit_selected_edit && isUnitEditDialogVisible" v-model:isDialogVisible="isUnitEditDialogVisible" :unitSelected="unit_selected_edit" @editUnit="addEditUnit"></UnitEditDialog>        
        <UnitDeleteDialog v-if="unit_selected_delete && isUnitDeleteDialogVisible" v-model:isDialogVisible="isUnitDeleteDialogVisible" :unitSelected="unit_selected_delete" @deleteUnit="addDeleteUnit"></UnitDeleteDialog>        

    </div>
</template>