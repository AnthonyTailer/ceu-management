<template>
  <v-layout row justify-center>
    <v-dialog v-model="dialog" persistent width="100%">
      <!--<v-btn primary dark slot="activator">Open Dialog</v-btn>-->
      <v-card>
        <v-card-title :class="`pa-1 ${type}`">
          <h5 class="ma-1 d-flex justify-center fill-height "><slot name="titleModal"></slot></h5>
        </v-card-title>
        <v-divider></v-divider>
        <v-card-text class="modal-body">
          <!--<v-container grid-list-md  class="fill-height">-->
              <!-- Conteudo principal do Modal-->
              <slot name="mainModal"></slot>
          <!--</v-container>-->
        </v-card-text>
      </v-card>
    </v-dialog>
  </v-layout>
</template>

<script>
  import { eventBus } from '../../main'
  export default {
    props: {
      dialog: {
        type: Boolean
      },
      type: {
        type: String
      }
    },
    watch: {
      dialog: function () {
        console.log('OpenModal -> ' + this.dialog)
      }
    }
  }
</script>
