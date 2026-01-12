<script setup>
// import RoleAddDialog from "../../components/inventory/role/RoleAddDialog.vue";

    const headers = [
            { title: 'ID', key: 'id' },
            { title: 'Almacen', key: 'name' },
            { title: 'Direccion', key: 'address' },
            { title: 'Sucursal', key: 'sucursal123' },
            { title: 'Estado', key: 'state' },
            { title: 'Fecha de Registro', key: 'created_at' },
            { title: 'Action', key: 'action' },
        ]

    const isWarehouseAddDialogVisible = ref(false);
    const isWarehouseEditDialogVisible = ref(false);
    const isWarehouseDeleteDialogVisible = ref(false);

    const list_warehouses = ref([]);
    const list_sucursales = ref([]);
    const searchQuery = ref(null);
    const warehouse_selected_edit = ref(null);
    const warehouse_selected_delete = ref(null);

    const list = async () => {
        try {
            const resp = await $api("warehouses?search=" + (searchQuery.value ? searchQuery.value : ''), {
            method: "GET",
            onReponseError({response}){
                console.log(response._data.error);
            }
            })
            console.log(resp);
            list_warehouses.value = resp.warehouses;
            // Llamamos al listado de sucursales que creamos en el controlador
            list_sucursales.value = resp.sucursales;
        } catch (error) {
            console.log(error);
        }
    }

    const addNewWarehouse = (NewWarehouse) => {
        console.log(NewWarehouse);
        let backup = list_warehouses.value;
        list_warehouses.value = [];
        backup.unshift(NewWarehouse);
        setTimeout(() => {
            list_warehouses.value = backup;
        }, 50);
    }

    const addEditWarehouse = (editWarehouse) => {
        console.log(editWarehouse);
        let backup = list_warehouses.value;
        list_warehouses.value = [];
        let INDEX = backup.findIndex((wh) => wh.id == editWarehouse.id);
        if(INDEX != -1){
            backup[INDEX] = editWarehouse;
        }
        setTimeout(() => {
            list_warehouses.value = backup;
        }, 50);
    }

    const addDeleteWarehouse = (Warehouse) => {
        console.log(Warehouse);
        let backup = list_warehouses.value;
        list_warehouses.value = [];
        let INDEX = backup.findIndex((wh) => wh.id == Warehouse.id);
        if(INDEX != -1){
            backup.splice(INDEX,1);
        }
        setTimeout(() => {
            list_warehouses.value = backup;
        }, 50);
    }
    
    const editItem = (item) => {
        console.log(item);
        isWarehouseEditDialogVisible.value = true;
        warehouse_selected_edit.value = item;
    }
    const deleteItem = (item) => {
        isWarehouseDeleteDialogVisible.value = true;
        warehouse_selected_delete.value = item;
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

        <VCard title="🏬 Almacenes">
            <VCardText>
                <VRow class="justify-space-between">
                    <VCol cols="4">
                        <VTextField
                        placeholder="Search Warehouse"
                        density="compact"
                        class="me-3"
                        v-model = "searchQuery"
                        @keyup.enter="list"
                        />
                    </VCol>
                    <VCol cols="3" class="text-end">
                        <VBtn @click="isWarehouseAddDialogVisible = !isWarehouseAddDialogVisible">
                            Add Warehouse 
                            <VIcon end icon="ri-add-line" />
                        </VBtn>
                    </VCol>
                </VRow>    
            </VCardText>
            <VDataTable
                :headers="headers"
                :items="list_warehouses"
                :items-per-page="5"
                class="text-no-wrap"
            >
                <template #item.id="{ item }">
                <span class="text-h6">{{ item.id }}</span>
                </template>
                
                <template #item.sucursal123="{ item }">
                <span class="text-h6">{{ item.sucursal.name }}</span>
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

        <WarehouseAddDialog v-model:isDialogVisible="isWarehouseAddDialogVisible" :sucursales="list_sucursales" @addWarehouse="addNewWarehouse"></WarehouseAddDialog>        
        <WarehouseEditDialog v-if="warehouse_selected_edit && isWarehouseEditDialogVisible" :sucursales="list_sucursales" v-model:isDialogVisible="isWarehouseEditDialogVisible" :warehouseSelected="warehouse_selected_edit" @editWarehouse="addEditWarehouse"></WarehouseEditDialog>        
        <WarehouseDeleteDialog v-if="warehouse_selected_delete && isWarehouseDeleteDialogVisible" v-model:isDialogVisible="isWarehouseDeleteDialogVisible" :warehouseSelected="warehouse_selected_delete" @deleteWarehouse="addDeleteWarehouse"></WarehouseDeleteDialog>        

    </div>
</template>