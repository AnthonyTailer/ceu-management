<template>
  <div>
    <form @submit.prevent="submitApartament()">
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
  
      <v-divider></v-divider>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn
          type="submit"
          class="primary"
          dark
        >
          Salvar
        </v-btn>
        <v-btn class="grey lighten-1 black--text" dark @click.prevent="closeModal">Fechar</v-btn>
      </v-card-actions>
    </form>
  </div>
</template>
<script>
  import { eventBus } from '../../main'

  export default {
    $validate: true,
    computed: {
      apartament: {
        get () {
          return this.$store.getters.getApartamentState
        },
        set (value) {
          this.$store.dispatch('newApartament', value)
        }
      },
      number () {
        return this.apartament.number
      },
      apartamentNewState: {
        get () {
          return this.$store.getters.getAptoNewState
        },
        set (value) {
          this.$store.dispatch('setAptoNewState', value)
        }
      },
      apartamentEditState: {
        get () {
          return this.$store.getters.getAptoEditState
        },
        set (value) {
          this.$store.dispatch('setAptoEditState', value)
        }
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
        editApto: false
      }
    },
    methods: {
      closeModal() {
        this.editApto = false
        this.$validator.reset()
        this.$store.commit('newApartament', this.initialData())
        eventBus.fire('closeModal', false)
      },
      initialData () {
        return {
          id: null,
          number: null,
          block: null,
          building: null,
          vacancy: null,
          vacancy_type: null,
          capacity: null
        }
      },
      submitApartament () {
        if ( this.apartamentNewState ){
          this.$validator.validateAll('apartament-form').then(result => {
            if (!result) {
              console.log('Apartament Create -> validation failed.', result)
            } else {
              console.log('Submited Apartament', this.apartament )
              this.$store.dispatch('newApartament', this.apartament)
            }
          }).catch(() => {
            console.log('something went wrong (non-validation related')
          })
        }
        else if ( this.apartamentEditState ) {
          this.$validator.validateAll('apartament-form').then(result => {
            if (!result) {
              console.log('Apartament Edit -> validation failed.', result)
            } else {
              console.log('Submited Apartament', this.apartament )
              this.$store.dispatch('editApartament', this.apartament)
            }
          }).catch(() => {
            console.log('something went wrong (non-validation related')
          })
        }
      },
    }
  }


</script>