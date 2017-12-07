<template>
  <v-tabs fixed centered>
    <v-toolbar class="teal darken-2" dark>
      <v-toolbar-side-icon></v-toolbar-side-icon>
      <v-toolbar-title class="display text-xs-center">Notificações</v-toolbar-title>
      <v-spacer></v-spacer>
      <v-btn icon v-if="this.$store.getters.adminState">
        <v-icon>add</v-icon>
      </v-btn>
    </v-toolbar>
    <v-tabs-bar class="teal darken-4" dark>
      <v-tabs-slider ></v-tabs-slider>
      <v-tabs-item
        v-for="(i, key) in tabs"
        :key="key"
        :href="'#tab-' + key"
        @click.native="getTabNotifications(i)"
      >
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
            <v-alert
              v-for="noty in i.content"
              :key="noty.id"
              :value="true"
              class="info"
              icon="info"
            >
              {{ noty.text }}
              <v-btn flat dark absolute right @click="markAsRead(noty.id, i)">Marcar Como Lida</v-btn>
            </v-alert>
          </v-card-text>
          <v-card-text class="text-xs-center" v-else >Nenhuma Notificação para ser exibida</v-card-text>
        </v-card>
      </v-tabs-content>
    </v-tabs-items>
  </v-tabs>
</template>
<script>
  import { eventBus } from '../main'
  import VueNotifications from 'vue-notifications'
  export default {
    mounted () {
      this.getTabNotifications(this.tabs[0])
    },
    data () {
      return {
        tabs: [
          {name: 'Não Lidas', slug: 'unread', route: 'api/user/get-notifications', content: ''},
          {name: 'Lidas', slug: 'read', route: 'api/user/get-read-notifications', content: ''},
        ],
      }
    },
    methods: {
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
         }else if (noti === null || noti === undefined) {
           localStorage.setItem(notifications, 0)
         }
       })
      },
      markAsRead (id, tab) {
        this.$http.post("api/user/mark-read?token="+this.$auth.getToken(), { id_notification : id}).then( (response) => {
          console.log(response)
          this.getTabNotifications(tab)
        })
      }
    }
  }
</script>
<style>
  .tabs__item, .tabs__item--active {
    color: white !important;
  }
</style>