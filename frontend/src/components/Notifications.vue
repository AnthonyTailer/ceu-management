<template>
  <div>
    <v-tabs fixed centered icons>
    <v-toolbar class="teal darken-2" dark>
      <i class="flaticon-mail"></i>
      <v-toolbar-title class="display text-xs-center">Mensagens</v-toolbar-title>
      <v-spacer></v-spacer>
      <v-btn icon v-if="this.$store.getters.adminState" @click="newMessageModal = true">
        <v-icon class="flaticon-multimedia"></v-icon>
      </v-btn>
    </v-toolbar>
    <v-tabs-bar class="teal darken-4" dark>
      <v-tabs-slider></v-tabs-slider>
      <v-tabs-item
        v-for="(i, key) in tabs"
        :key="key"
        :href="'#tab-' + key"
        @click.native="getTabNotifications(i)"
      >
        <v-icon>{{i.icon}}</v-icon>
        {{ i.name }}
      </v-tabs-item>
    </v-tabs-bar>
    <v-tabs-items>
      <v-tabs-content
        v-for="(i, key) in tabs"
        :key="key"
        :id="'tab-' + key"
      >
        <v-card flat>
          <v-card-text v-if="i.content.length > 0">
            <v-btn  v-if="i.slug === 'unread' && selectedMsg.length > 0"  @click="markAsRead(selectedMsg, i)">Marcar '{{selectedMsg.length}}' como Lida </v-btn>
            <v-alert
              v-for="noty in i.content"
              :key="noty.id"
              :value="true"
              :class="getPriorityAlert(noty.priority) + ' ma-1 pa-1'"
              transition="scale-transition"
            >
              <v-layout row wrap>
                <v-flex xs1>
                  <v-checkbox v-if="i.slug === 'unread'" dark v-model="selectedMsg" :value="noty.id"></v-checkbox>
                </v-flex>
                <v-flex xs11>
                  <div>{{ noty.text }}</div>
                  <div class="text-xs-right" >Recebida em {{ noty.date }}</div>
                  <div class="text-xs-right" v-if="i.slug === 'read'">Lida em {{ noty.read_at }}</div>
                </v-flex>
              </v-layout>
            </v-alert>
          </v-card-text>
          <v-card-text class="text-xs-center" v-else >Nenhuma Notificação para ser exibida</v-card-text>
        </v-card>
      </v-tabs-content>
    </v-tabs-items>
  </v-tabs>
  
    <app-modal :dialog="newMessageModal" :type="'primary'">
      <span slot="titleModal" icon style="color: white"><i class="flaticon-multimedia"></i> Nova Mensagem</span>
      <div slot="mainModal">
        <app-message-form></app-message-form>
      </div>
    </app-modal>
  </div>
</template>
<script>
  import { eventBus } from '../main'
  import VueNotifications from 'vue-notifications'
  import Message from './forms/NewMessageForm.vue'
  import Modal from './shared/Modal.vue'
  export default {
    components: {
      appModal: Modal,
      appMessageForm: Message,
    },
    mounted () {
      
      this.getTabNotifications(this.tabs[0])
      
      eventBus.listen('closeModal', (data) => {
        this.newMessageModal  = data
      })
      
      eventBus.listen('notification-created', () => {
        this.getTabNotifications(this.tabs[0])
      })
    },
    data () {
      return {
        selectedMsg: [],
        newMessageModal: false,
        tabs: [
          {name: 'Não Lidas', icon:'email', slug: 'unread', route: 'api/user/get-notifications', content: ''},
          {name: 'Lidas', icon: 'mail_outline', slug: 'read', route: 'api/user/get-read-notifications', content: ''},
        ],
      }
    },
    methods: {
      getPriorityAlert (priority) {
        if ( priority === 'low')
          return 'info'
        else if ( priority === 'medium')
          return 'warning'
        else
          return 'error'
      },
      getTabNotifications (tab) {
       this.$http.get(tab.route+"?token="+this.$auth.getToken()).then( (response) => {
         console.log(response)
         tab.content = response.body.notifications

         let noti = this.$notify.getNotificationsCookie()

         if(noti && tab.slug === 'unread'){
           if(response.body.count > noti){
             localStorage.setItem('notifications', response.body.count)
             let audio = new Audio('static/alert_sound.mp3');
             audio.play()
             VueNotifications.info({message: 'Você possui Notificações pendentes'})
             eventBus.fire('newNotifications', response.body)
           }else {
             eventBus.fire('newNotifications', response.body)
           }
         } else if (noti === null || noti === undefined) {
           localStorage.setItem(notifications, 0)
         }
       })
      },
      markAsRead (idArray, tab) {
        if (idArray.length > 0){
          this.$http.post("api/user/mark-multiple-read?token="+this.$auth.getToken(), { ids : idArray}).then( (response) => {
            console.log(response)
            this.getTabNotifications(tab)
            this.selectedMsg = []
          })
        }
      }
    }
  }
</script>
<style>
  .tabs__item, .tabs__item--active {
    color: white !important;
  }
</style>