<script setup>
import { onMounted } from 'vue';

const props = defineProps({
  isDialogVisible: {
    type: Boolean,
    required: true,
  },
  providerSelected: {
    type: Object,
    required: true,
  }
})

const emit = defineEmits([
  'update:isDialogVisible',
  'editProvider'
])
// console.log(PERMISOS);
const full_name = ref(null);
const email = ref(null);
const phone = ref(null);
const ruc = ref(null);
const address = ref(null);
const state = ref(1);
const FILE_IMAGEN = ref(null);
const IMAGEN_PREVIZUALIZA = ref(null);

const warning = ref(null);
const error_exist = ref(null);
const success = ref(null);

const update = async () => {
  warning.value = null;
  error_exist.value = null;
  success.value = null;

  if (!full_name.value) {
    setTimeout(() => {
      warning.value = "Se debe llenar el nombre del Proveedor";
    }, 50);
    return;
  }
  if (!ruc.value) {
    setTimeout(() => {
      warning.value = "Se debe llenar el RUC del Proveedor";
    }, 50);
    return;
  }
  if (!phone.value) {
    setTimeout(() => {
      warning.value = "Se debe llenar el telefono del Proveedor";
    }, 50);
    return;
  }

  let formData = new FormData();
  formData.append('full_name', full_name.value);
  if(email.value){
    formData.append('email', email.value);
  }
  formData.append('ruc', ruc.value);
  formData.append('phone', phone.value);
  formData.append('address', address.value);
  formData.append('state',1);
  if(FILE_IMAGEN.value){
    formData.append('image', FILE_IMAGEN.value);
  }

  try {
    const resp = await $api("providers/" + props.providerSelected.id, {
      method: "POST",
      body: formData,
      onResponseError({response}){
        error_exist.value = response._data.error;
      }
    })
    console.log(resp);
    if (resp.message == 403) {
      error_exist.value = resp.message_text;
    }else{
      success.value = "El Proveedor se ha actualizado correctamente";
      emit('editProvider', resp.provider);
      // Limpiamos los campos
      FILE_IMAGEN.value = "";
      warning.value = null;
      error_exist.value = null;
    //   success.value = null;
    //   onFormReset();
    }
  } catch (error) {
    console.log(error);
    
  }
}

const loadFile = ($event) =>{
    if($event.target.files[0].type.indexOf("image") < 0){
     error_exist.value = "SOLAMENTE PUEDEN SER ARCHIVOS DE TIPO IMAGEN";
      return;
    }
    error_exist.value = '';
    FILE_IMAGEN.value = $event.target.files[0];
    let reader = new FileReader();
    reader.readAsDataURL(FILE_IMAGEN.value);
    reader.onloadend = () => IMAGEN_PREVIZUALIZA.value = reader.result;
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

onMounted(() => {
  full_name.value = props.providerSelected.full_name;
  email.value = props.providerSelected.email;
  phone.value = props.providerSelected.phone;
  ruc.value = props.providerSelected.ruc;
  address.value = props.providerSelected.address;
  state.value = props.providerSelected.state;
  IMAGEN_PREVIZUALIZA.value = props.providerSelected.imagen;
});

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
            Edit Provider : {{ props.providerSelected.full_name }}
          </h4>
        </div>

        <!-- 👉 Form -->
        <VForm
          class="mt-4"
          @submit.prevent="update"
        >
          <VRow>
            <!-- 👉 First Name -->
            <VCol
              cols="6"
            >
              <VTextField
                v-model="full_name"
                label="Nombre del Proveedor"  
                placeholder="EXAMPLE: Juan Perez"
              />
            </VCol>
            
            <VCol
              cols="6"
            >
              <VTextField
                v-model="email"
                label="Correo"  
                placeholder="jose@example.com"
              />
            </VCol>
            <VCol
              cols="6"
            >
              <VTextField
                v-model="phone"
                label="Telefono"  
                type="number"
                placeholder="999999999"
              />
            </VCol>
            <VCol
              cols="8"
            >
              <VTextField
                v-model="ruc"
                label="RUC"  
                placeholder="9999999999999"
                type="number"
              />
            </VCol>

            <VCol
                cols="6"
                >
                <VFileInput label="Imagen del Proveedor" @change="loadFile($event)"/>
                <VImg v-if="IMAGEN_PREVIZUALIZA" :src="IMAGEN_PREVIZUALIZA" 
                class="mt-2" height="176" width="250" />
            </VCol>

            <VCol
              cols="6"
            >
            <VTextarea
                v-model="address"
                label="Direccion"
                placeholder=""
            />

            </VCol>

            <VCol cols="6">
                <VSelect
                    :items="[
                        {
                            name: 'Activo',
                            value: 1,
                        },
                        {
                            name: 'Inactivo',
                            value: 2,
                        },
                    ]"
                    item-title="name"
                    item-value="value"
                    v-model="state"
                    label="Estado"
                    placeholder="Select Item"
                    eager
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
            <!-- 👉 Submit and Cancel -->
            <VCol
              cols="12"
              class="d-flex flex-wrap justify-center gap-4"
            >
              <VBtn type="submit">
                Editar
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
