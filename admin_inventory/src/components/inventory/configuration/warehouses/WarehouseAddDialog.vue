<script setup>
const props = defineProps({
  isDialogVisible: {
    type: Boolean,
    required: true,
  },
//   Sucursales disponibles para seleccionar
  sucursales:{
    type: Object,
    required: true,
  }
})

const emit = defineEmits([
  'update:isDialogVisible',
  'addWarehouse'
])
// console.log(PERMISOS);

const name = ref(null);
const address = ref(null);
const sucursale_id = ref(null);
const warning = ref(null);
const error_exist = ref(null);
const success = ref(null);


const store = async () => {
  warning.value = null;
  error_exist.value = null;
  success.value = null;

  if (!name.value) {
    setTimeout(() => {
      warning.value = "Se debe llenar el nombre del Almacén";
    }, 50);
    return;
  }
  if (!address.value) {
    setTimeout(() => {
      warning.value = "Se debe llenar la dirección del Almacen";
    }, 50);
    return;
  }
  if (!sucursale_id.value) {
    setTimeout(() => {
      warning.value = "Se debe seleccionar la Sucursal";
    }, 50);
    return;
  }

  let data = {
    name: name.value,
    address: address.value,
    sucursale_id: sucursale_id.value,
    state: 1
  }

  try {
    const resp = await $api("warehouses", {
      method: "POST",
      body: data,
      onResponseError({response}){
        error_exist.value = response._data.error;
      }
    })
    console.log(resp);
    if (resp.message == 403) {
      error_exist.value = resp.message_text;
    }else{
      success.value = "El Almacén se ha registrado correctamente";
      emit('addWarehouse', resp.warehouse);
      // Limpiamos los campos
      name.value = "";
      address.value = '';
      sucursale_id.value = '';
      warning.value = null;
      error_exist.value = null;
      success.value = null;
      onFormReset();
    }
  } catch (error) {
    console.log(error);
    
  }
}

const onFormSubmit = () => {
  emit('update:isDialogVisible', false)
  emit('submit', userData.value)
}

const onFormReset = () => {
  emit('update:isDialogVisible', false)
}

const dialogVisibleUpdate = val => {
  emit('update:isDialogVisible', val)
}

</script>

<template>
  <VDialog
    max-width="650"
    :model-value="props.isDialogVisible"
    @update:model-value="dialogVisibleUpdate"
  >
    <VCard class="pa-sm-11 pa-3">
      <!-- 👉 dialog close btn -->
      <DialogCloseBtn
        variant="text"
        size="default"
        @click="onFormReset"
      />

      <VCardText class="pt-5">
        <div class="text-center pb-6">
          <h4 class="text-h4 mb-2">
            Add Warehouse
          </h4>
        </div>

        <!-- 👉 Form -->
        <VForm
          class="mt-4"
          @submit.prevent="store"
        >
          <VRow>
            <!-- 👉 First Name -->
            <VCol
              cols="12"
            >
              <VTextField
                v-model="name"
                label="Nombre"  
                placeholder="John"
              />
            </VCol>
            <VCol cols="12" v-if="warning">
                 <VAlert
                  closable
                  close-label="Close Alert"
                  color="warning"
                  variant="tonal"
                >
                {{ warning }}
                </VAlert>
            </VCol>
            <VCol cols="12" v-if="error_exist">
                 <VAlert
                  closable
                  close-label="Close Alert"
                  color="error"
                  variant="tonal"
                >
                {{ error_exist }}
                </VAlert>
            </VCol>
            <VCol cols="12" v-if="success">
                 <VAlert
                  closable
                  close-label="Close Alert"
                  color="success"
                  variant="tonal"
                >
                {{ success }}
                </VAlert>
            </VCol>
            <VCol
              cols="12"
            >
            <VTextarea
                v-model="address"
                label="Direccion"
                placeholder=""
            />
            
            <VCol
                cols="6"
                >
                <VSelect
                    :items="props.sucursales"
                    item-title="name"
                    item-value="id"
                    v-model="sucursale_id"
                    label="Sucursal"
                    placeholder="Select Item"
                    eager
                />
            </VCol >

            </VCol>
            <!-- 👉 Submit and Cancel -->
            <VCol
              cols="12"
              class="d-flex flex-wrap justify-center gap-4"
            >
              <VBtn type="submit">
                Guardar
              </VBtn>

              <VBtn
                color="secondary"
                variant="outlined"
                @click="onFormReset"
              >
                Cancel
              </VBtn>
            </VCol>
          </VRow>
        </VForm>
      </VCardText>
    </VCard>
  </VDialog>
</template>
