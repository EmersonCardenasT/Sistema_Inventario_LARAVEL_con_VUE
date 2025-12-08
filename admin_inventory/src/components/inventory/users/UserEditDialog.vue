<script setup>
const props = defineProps({
  isDialogVisible: {
    type: Boolean,
    required: true,
  },
  sucursales: {
    type:Object,
    required:true,
  },
  roles: {
    type: Object,
    required:true,
  },
  selectedUser:{
    type: Object,
    required: true,
  }
})

const emit = defineEmits([
  'update:isDialogVisible',
  'editUser'
])
// console.log(PERMISOS);
const name = ref(null);
const surname = ref(null);
const email = ref(null);
const phone = ref(null);
const type_document = ref('DNI');
const n_document = ref(null);
const role_id = ref(null);
const sucursale_id = ref(null);
const gender = ref(null);
const password = ref(null);
const FILE_AVATAR = ref(null);
const IMAGEN_PREVIZUALIZA = ref(null);
const state = ref(1);

const warning = ref(null);
const error_exist = ref(null);
const success = ref(null);

const update = async () => {
  warning.value = null;
  error_exist.value = null;
  success.value = null;

  if (!name.value) {
    setTimeout(() => {
      warning.value = "Se debe llenar el nombre del Usuario";
    }, 50);
    return;
  }
  if (!surname.value) {
    setTimeout(() => {
      warning.value = "Se debe llenar el apellido del Usuario";
    }, 50);
    return;
  }
  if (!email.value) {
    setTimeout(() => {
      warning.value = "Se debe llenar el correo del Usuario";
    }, 50);
    return;
  }
  if (!sucursale_id.value) {
    setTimeout(() => {
      warning.value = "Se debe llenar la sucursal del Usuario";
    }, 50);
    return;
  }
  if (!role_id.value) {
    setTimeout(() => {
      warning.value = "Se debe llenar el rol del Usuario";
    }, 50);
    return;
  }
  if (!gender.value) {
    setTimeout(() => {
      warning.value = "Se debe seleccionar el genero del Usuario";
    }, 50);
    return;
  }
  if (!phone.value) {
    setTimeout(() => {
      warning.value = "Se debe llenar el telefono del Usuario";
    }, 50);
    return;
  }
//   if (!password.value) {
//     setTimeout(() => {
//       warning.value = "Se debe llenar la contraseña del Usuario";
//     }, 50);
//     return;
//   }
//   if (!FILE_AVATAR.value) {
//     setTimeout(() => {
//       warning.value = "Se debe subir una imagen del Usuario";
//     }, 50);
//     return;
//   }

  let formData = new FormData();
  formData.append('name', name.value);
  formData.append('surname', surname.value);
  formData.append('email', email.value);
  formData.append('role_id', role_id.value);
  formData.append('sucursale_id', sucursale_id.value);
  formData.append('gender', gender.value);
  if (password.value) {
    formData.append('password', password.value);
  }
  if (FILE_AVATAR.value) {
      formData.append('imagen', FILE_AVATAR.value);
  }
  formData.append('phone', phone.value);
  formData.append('state', state.value);
  if (!type_document.value) {
    formData.append('type_document', type_document.value);
  }
  if (!n_document.value) {
    formData.append('n_document', n_document.value);
  }

  try {
    const resp = await $api("users/"+props.selectedUser.id, {
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
      success.value = "El usuario se ha editado correctamente";
      emit('editUser', resp.user);
      // Limpiamos los campos
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
    FILE_AVATAR.value = $event.target.files[0];
    let reader = new FileReader();
    reader.readAsDataURL(FILE_AVATAR.value);
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
    name.value = props.selectedUser.name;
    surname.value = props.selectedUser.surname;
    email.value = props.selectedUser.email;
    phone.value = props.selectedUser.phone;
    role_id.value = props.selectedUser.role_id;
    sucursale_id.value = props.selectedUser.sucursale_id;
    gender.value = props.selectedUser.gender;
    type_document.value = props.selectedUser.type_document;
    n_document.value = props.selectedUser.n_document;
    IMAGEN_PREVIZUALIZA.value = props.selectedUser.avatar;
})

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
            Edit User : {{  props.selectedUser.full_name  }}
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
                v-model="name"
                label="Nombre"  
                placeholder="Jose"
              />
            </VCol>
            <VCol
              cols="6"
            >
              <VTextField
                v-model="surname"
                label="Apellido"  
                placeholder="Mendoza"
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
              cols="6"
            >
              <VSelect
                    :items="[
                        'DNI',
                        'PASAPORTE',
                        'CEDULA',
                        'CARNET DE EXTRANJERIA',
                        'TARJETA MILITAR',
                    ]"
                    v-model="type_document"
                    label="Tipo de Documento"
                    placeholder="Select Item"
                    eager
                />
            </VCol>
            <VCol
              cols="6"
            >
              <VTextField
                v-model="n_document"
                label="N° de Documento"  
                placeholder="John"
                type="number"
              />
            </VCol>
            <VCol
              cols="6"
            >
                <VSelect
                    :items="props.roles"
                    item-title="name"
                    item-value="id"
                    v-model="role_id"
                    label="Rol"
                    placeholder="Select Item"
                    eager
                />
            </VCol>
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
            <VCol
                cols="6"
                >
                <VRadioGroup v-model="gender">
                <VRadio
                    label="Masculino"
                    value="M"
                />
                <VRadio
                    label="Femenino"
                    value="F"
                />
                </VRadioGroup>
            </VCol>
             <VCol
              cols="6"
                 >
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
                    item-value="id"
                    v-model="state"
                    label="Estado"
                    placeholder="Select Item"
                    eager
                />
            </VCol>
            <VCol
                cols="6"
                >
                <VFileInput label="Avatar del Usuario" @change="loadFile($event)"/>
                <VImg v-if="IMAGEN_PREVIZUALIZA" :src="IMAGEN_PREVIZUALIZA" 
                class="mt-2" height="176" width="250" />
            </VCol>
           <VCol
              cols="6"
            >
              <VTextField
                v-model="password"
                label="Contraseña"  
                placeholder="**********"
                type="password"
                 
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
