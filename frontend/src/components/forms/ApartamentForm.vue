<template>
 <form>
   <v-layout row>
     <v-flex x6 sm6 md6>
       <v-text-field
         name="input-number"
         v-model="apartament.number"
         label="Apartamento"
         class="input-group"
         :error-messages="errors.collect('apartamento')"
         v-validate="'required|numeric|min:4'"
         data-vv-name="apartamento"
         required
       >
       </v-text-field>
     </v-flex>
     <v-flex x6 sm6 md6>
       <v-text-field
         name="input-capacity"
         v-model="apartament.capacity"
         label="Capacidade"
         class="input-group"
         :error-messages="errors.collect('capacidade')"
         v-validate="'required|numeric'"
         data-vv-name="capacidade"
         required
       >
       </v-text-field>
     </v-flex>
   </v-layout>
   <v-layout row>
     <!--<v-flex x4 sm4 md4>-->
       <!--<v-text-field-->
         <!--name="input-vacancy"-->
         <!--v-model="apartament.vacancy"-->
         <!--label="Qtde. de Vagas"-->
         <!--class="input-group"-->
         <!--:error-messages="errors.collect('vagas')"-->
         <!--v-validate="'required|numeric'"-->
         <!--data-vv-name="vagas"-->
         <!--required-->
       <!--&gt;-->
       <!--</v-text-field>-->
     <!--</v-flex>-->
     <v-flex x12 sm6 md6>
       <v-text-field
         name="input-block"
         v-model="apartament.block"
         label="Bloco"
         class="input-group"
         :error-messages="errors.collect('bloco')"
         v-validate="'required|numeric'"
         data-vv-name="bloco"
         required
       >
       </v-text-field>
     </v-flex>
     <v-flex x12 sm6 md6>
       <v-text-field
         name="input-building"
         v-model="apartament.building"
         label="Prédio"
         class="input-group"
         :error-messages="errors.collect('prédio')"
         v-validate="'required'"
         data-vv-name="prédio"
         required
       >
       </v-text-field>
     </v-flex>
   </v-layout>
   <v-layout row wrap>
     <v-flex x4 sm4 md4>
       <v-radio-group  label="Gênero das Vagas" class="input-group" v-model="apartament.vacancy_type" :mandatory="false" row v-validate="'required'" :error-messages="errors.collect('Gênero das vagas')" data-vv-name="gênero das vagas">
         <v-radio color="blue" label="Masculino" value="M"></v-radio>
         <v-radio color="pink" label="Feminino" value="F"></v-radio>
         <v-radio color="green" label="Misto" value="MF"></v-radio>
       </v-radio-group>
     </v-flex>
   </v-layout>
   <v-snackbar
     :timeout="8000"
     :error="snackError"
     :success="snackSuccess"
     :vertical="true"
     v-model="snackbar"
   >
     <div v-html="snackMsg"></div>
     <v-btn dark flat @click.native="snackbar = false">Fechar</v-btn>
   </v-snackbar>
 </form>
</template>
<script>
  import { eventBus } from '../../main'
  
  export default {
    $validate: true,
    computed: {
      number () {
        return this.apartament.number
      }
    },
    watch: {
      number (value) {
        if(!this.editApto){
          this.apartament.block = value ? value.toString().substr(0,2) : ''
          this.apartament.building = value ? value.toString().substr(0,1) : ''
        }
      }
    },
    data () {
      return {
        scopeValidation: 'apartament-form',
        snackbar: false,
        snackError: false,
        snackSuccess: false,
        snackMsg: '',
        editApto: false,
        apartament: {
          number: '',
          block: '',
          building: '',
          capacity: '',
          vacancy_type: 'MF',
        }
      }
    },
    beforeCreate() {
      eventBus.listen('getApartamentData', data => {
        this.apartament = data;
        this.editApto = true
      })

      eventBus.listen('deleteApartamentData', data => {
        this.apartament = data;
      })
      
      eventBus.listen('createApartamentSubmit', () => {
        this.createApartament()
      })

      eventBus.listen('updateApartamentSubmit', () => {
        this.updateApartament()
      })

      eventBus.listen('deleteApartamentSubmit', () => {
        this.deleteApartament()
      })

      eventBus.listen('closeModal', () => {
        this.apartament = this.initialData()
        this.editApto = false
        this.$validator.reset()
      })
    },
    methods: {
      initialData () {
        return {
          number: null,
          block: null,
          building: null,
          vacancy: null,
          vacancy_type: null,
          capacity: null
        }
      },
      createApartament () {
        this.editApto = false
        this.$validator.validateAll().then(result => {
          if (!result) {
            console.log('Apartament Create -> validation failed.')
          } else {
            console.log('Submited Apartament', this.apartament )
            
            this.$http.post('api/apto/register?token='+ this.$auth.getToken(), this.apartament)
              .then( (response) => {
                this.snackMsg = response.body.message
                let aptos = response.body.aptos
                eventBus.fire('apartamentCreated', {'message' : this.snackMsg , 'aptos' : aptos})
                eventBus.closeModal(true)
              }).catch( (response) => {
              this.snackbar = true
              let msg = ' '

              if (response.body.errors) {
                for (let i in response.body.errors) {
                  response.body.errors[i].forEach((item) => {
                    msg += item + '<br>'
                  })
                }

              } else {
                msg = response.body.message
              }
              this.snackMsg = msg
              this.snackSuccess = false
              this.snackError = true
            })
          }
        }).catch(() => {
          console.log('something went wrong (non-validation related')
        })
      },
      updateApartament: function() {
        this.editApto = false
        this.$validator.validateAll().then(result => {
          if (!result) {
            console.log('Apartament Updated -> validation failed.')
          } else {
            console.log('Apartament Update Submit')
            this.$validator.reset()
            console.log(this.apartament)
            this.$http.put(`api/apto/${this.apartament.number}?token=`+ this.$auth.getToken(), this.apartament)
              .then( (response) => {
                
                this.snackMsg = response.body.message
                let aptos = response.body.aptos
                eventBus.fire('apartamentUpdated', {'message' : this.snackMsg , 'aptos' : aptos})
                eventBus.closeModal(true)

              }).catch( (response) =>  {
              this.snackbar = true
              let msg = ' '

              if( response.body.errors ){
                for( let i in response.body.errors){
                  response.body.errors[i].forEach( (item) => {
                    msg += item + '<br>'
                  })
                }

              }else {
                msg = response.body.message
              }
              this.snackMsg = msg
              this.snackSuccess = false
              this.snackError = true
            })
          }
        }).catch( () => {
          console.log('something went wrong (non-validation related')
        })
      },
      deleteApartament () {
        this.$http.delete(`api/apto/${this.apartament.number}?token=`+ this.$auth.getToken(), this.apartament.number)
          .then( (response) => {
            eventBus.closeModal(true)
            this.snackMsg = response.body.message
            eventBus.fire('apartamentDeleted', this.snackMsg)

          }).catch( (response) =>  {
          this.snackbar = true
          let msg = ' '

          if( response.body.errors ){
            for( let i in response.body.errors){
              response.body.errors[i].forEach( (item) => {
                msg += item + '<br>'
              })
            }

          }else {
            msg = response.body.message
          }
          this.snackMsg = msg
          this.snackSuccess = false
          this.snackError = true
        })
      }
    }
  }
  
  
</script>