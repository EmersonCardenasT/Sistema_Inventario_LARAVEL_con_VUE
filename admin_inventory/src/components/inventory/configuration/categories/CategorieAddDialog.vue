<script setup>
const props = defineProps({
  isDialogVisible: {
    type: Boolean,
    required: true,
  },
})

const emit = defineEmits([
  'update:isDialogVisible',
  'addCategorie'
])
// console.log(PERMISOS);

const name = ref(null);
const FILE_IMAGEN = ref(null);
const PREVIZUALIZA_IMAGEN = ref(null);
const warning = ref(null);
const error_exist = ref(null);
const success = ref(null);


const store = async () => {
  warning.value = null;
  error_exist.value = null;
  success.value = null;

  if (!name.value) {
    setTimeout(() => {
      warning.value = "Se debe llenar el nombre de la Categoría";
    }, 50);
    return;
  }
  if (!FILE_IMAGEN.value) {
    setTimeout(() => {
      warning.value = "Se debe subir una imagen de la Categoría";
    }, 50);
    return;
  }

//  CUANDO ENVIAMOS IMAGENES TENEMOS QUE ENVIARLAS EN UN FORM DATA, EN VUE USAMOS "FormData" PARA ENVIARLAS.
  let formData = new FormData();
// IMPORTANTE, AL BACKEND TENEMOS QUE ENVIAR EL NOMBRE DEL CAMPO COMO "title", AUNQUE EN EL 
// FRONTEND USAMOS "name" PARA MANTENER LA CONSISTENCIA.
  formData.append('title', name.value);
  formData.append('image', FILE_IMAGEN.value);
  formData.append('state', 1);

  try {
    const resp = await $api("categories", {
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
      success.value = "La Categoría se ha registrado correctamente";
      emit('addCategorie', resp.categorie);
      // Limpiamos los campos
      name.value = "";
      FILE_IMAGEN.value = null;
      PREVIZUALIZA_IMAGEN.value = null;
      warning.value = null;
      error_exist.value = null;
      success.value = null;
      onFormReset();
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
    reader.onloadend = () => PREVIZUALIZA_IMAGEN.value = reader.result;
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
            Add Categoría
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
                cols="6"
                >
                <VFileInput label="Imagen" @change="loadFile($event)"/>
                <VImg v-if="PREVIZUALIZA_IMAGEN" :src="PREVIZUALIZA_IMAGEN" 
                class="mt-2" height="176" width="250" />
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
