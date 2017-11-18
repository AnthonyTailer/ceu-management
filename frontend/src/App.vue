<template>
  <v-app toolbar footer :style="{ 'background-image': 'url(' + backgroundLogin + ')', 'background-size': 'cover' }">
    <v-navigation-drawer v-if="this.$router.currentRoute.name !== 'login'"  persistent overflow light :mini-variant.sync="mini" v-model="drawer">
      <v-toolbar flat class="transparent">
        <v-list class="pa-0">
          <v-list-tile avatar>
            <v-list-tile-avatar>
              <img src="/static/ceu-logo.png"/>
            </v-list-tile-avatar>
            <v-list-tile-content>
              <v-list-tile-title>CEU II</v-list-tile-title>
            </v-list-tile-content>
            <v-list-tile-action>
              <v-btn icon @click.native.stop="mini = !mini">
                <v-icon>chevron_left</v-icon>
              </v-btn>
            </v-list-tile-action>
          </v-list-tile>
        </v-list>
      </v-toolbar>
      <v-list class="pt-0" dense>
        <v-divider></v-divider>
        <v-list-tile class="sideMenuItens" v-if="getAdminState"  @click.native="targetRoute('/dash')">
          <v-list-tile-action>
            <v-icon>dashboard</v-icon>
          </v-list-tile-action>
          <v-list-tile-content>
            <v-list-tile-title>Home/Dashboard</v-list-tile-title>
          </v-list-tile-content>
        </v-list-tile>
        <v-list-tile class="sideMenuItens" v-if="getAdminState"  @click.native="targetRoute('/alunos')">
          <v-list-tile-action>
            <v-icon>face</v-icon>
          </v-list-tile-action>
          <v-list-tile-content>
            <v-list-tile-title>Alunos/Moradores</v-list-tile-title>
          </v-list-tile-content>
        </v-list-tile>
        <v-list-tile class="sideMenuItens" v-if="getAdminState"  @click.native="targetRoute('/aptos')">
          <v-list-tile-action>
            <v-icon>domain</v-icon>
          </v-list-tile-action>
          <v-list-tile-content>
            <v-list-tile-title>Apartamentos/Blocos</v-list-tile-title>
          </v-list-tile-content>
        </v-list-tile>
        <v-list-tile class="sideMenuItens"  @click.native="targetRoute('/aptos/vacancy')">
          <v-list-tile-action>
            <v-icon>domain</v-icon>
          </v-list-tile-action>
          <v-list-tile-content>
            <v-list-tile-title>Vagas</v-list-tile-title>
          </v-list-tile-content>
        </v-list-tile>
        <v-list-tile class="sideMenuItens"  @click.native="targetRoute('/notifications')">
          <v-list-tile-action>
            <v-icon>mail_outline</v-icon>
          </v-list-tile-action>
          <v-list-tile-content>
            <v-list-tile-title>Notificações/Alertas</v-list-tile-title>
          </v-list-tile-content>
        </v-list-tile>
      </v-list>
    </v-navigation-drawer>
    <!-- Menu Fixado em cima -->
    <v-toolbar v-if="this.$router.currentRoute.name !== 'login'" fixed class="teal darken-1" dark>
      <v-toolbar-side-icon @click.native.stop="drawer = !drawer"></v-toolbar-side-icon>
      <v-toolbar-title>CEU II Management</v-toolbar-title>
      <v-spacer></v-spacer>
      
      <!--NOTIFICATION MENU-->
      <div class="text-xs-center">
        <v-menu
          offset-x
          :close-on-content-click="false"
          :nudge-width="200"
          v-model="menu"
        >
          <v-btn icon class="mr-5" slot="activator">
            <v-icon v-if="user.notifications.length > 0" large v-badge="{ value: user.notifications.length , left: true}" class="red--after">mail</v-icon>
            <v-icon v-else large >mail_outline</v-icon>
          </v-btn>
          <v-card>
            <v-list>
              <v-list-tile avatar>
                <v-list-tile-action>
                  <v-icon large v-badge="{ value: user.notifications.length }" class="red--after"></v-icon>
                </v-list-tile-action>
                <v-list-tile-content>
                  <v-list-tile-title>Notificações não lidas</v-list-tile-title>
                </v-list-tile-content>
              </v-list-tile>
            </v-list>
            <v-divider></v-divider>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn flat @click="menu = false">Fechar</v-btn>
              <v-btn class="primary--text" flat @click="seeNotifications()">Ver Notificações</v-btn>
            </v-card-actions>
          </v-card>
        </v-menu>
      </div>
      
      <div class="text-xs-center">
        <v-menu open-on-hover right offset-y class="mr-0">
          <v-btn icon slot="activator"><v-icon large>account_circle</v-icon><v-icon large>keyboard_arrow_down</v-icon></v-btn>
          <v-list>
            <v-list-tile>
              <v-list-tile-title><v-icon>perm_identity</v-icon> Olá! {{ user.name }}.</v-list-tile-title>
            </v-list-tile>
            <v-divider></v-divider>
            <v-list-tile style="cursor: pointer" @click="logout()">
              <v-icon>exit_to_app</v-icon><v-list-tile-title > Sair</v-list-tile-title>
            </v-list-tile>
          </v-list>
        </v-menu>
      </div>
    
    </v-toolbar>
    
    <main fullscreen>
      <router-view></router-view>
    </main>
    <!--<v-footer class="pa-3"  v-if="footer">-->
    <!--<v-spacer></v-spacer>-->
    <!--<div>© {{ new Date().getFullYear() }}</div>-->
    <!--</v-footer>-->
  </v-app>
</template>

<script>
  import { eventBus } from './main'
  import { mapGetters } from 'vuex'
  import { mapActions } from 'vuex'
  import VListTile from "vuetify/src/components/VList/VListTile";
  import VDivider from "../node_modules/vuetify/src/components/VDivider/VDivider.vue";

  export default {
    
    computed: {
      ...mapGetters({
        getAdminState: 'adminState'
      })
    },
    mounted () {
      this.$nextTick(() => {
        window.addEventListener('resize', this.getWindowWidth)
        window.addEventListener('resize', this.getWindowHeight)

        this.getWindowWidth()
        this.getWindowHeight()
      })

      eventBus.listen("getUserNotifications", (data) => {
        this.user.notifications = data.body.notifications
      })

    },
    created: function () {
      console.log(this.$router.currentRoute.name)
      console.log('created menu')
      
      this.backgroundLogin = this.$router.currentRoute.name === 'login' ? '/static/back-login.jpg' : ''
      this.user.name = localStorage.getItem('user') ? localStorage.getItem('user').split(' ')[0] + ' ' + localStorage.getItem('user').split(' ')[1] : ''
    },
    updated () {
      console.log('updated menu')
      this.backgroundLogin = this.$router.currentRoute.name === 'login' ? '/static/back-login.jpg' : ''

    },
    data () {
      return {
        drawer: true,
        footer: false,
        backgroundLogin: '/static/back-login.jpg',
        user: {
          name: '',
          notifications: 0
        },
        mini: false,
        right: null,
        menu: false,
        windowWidth: 0,
        windowHeight: 0
      }
    },
    methods: {
      
      targetRoute: function (target) {
        return this.$router.push(target)
      },
      getWindowWidth: function (event) {
        this.windowWidth = document.documentElement.clientWidth
      },
      getWindowHeight: function (event) {
        this.windowHeight = document.documentElement.clientHeight
      },
      logout: function () {
        this.$auth.destroyToken()
        this.$router.push('/')
      },
      seeNotifications () {
        this.menu = false
        this.targetRoute('/notifications')
      }
    },
    
    watch: {
      windowWidth: function () {
        if (this.windowWidth <= 991) {
          this.mini = true
        } else if (this.windowWidth <= 768) {
          this.drawer = false
          this.mini = false
        } else {
          this.drawer = true
          this.mini = false
        }
      }
    },
    beforeDestroy () {
      window.removeEventListener('resize', this.getWindowWidth)
      window.removeEventListener('resize', this.getWindowHeight)
    }
  }
</script>

<style lang="stylus">
  @import './stylus/main'
  
  .sideMenuItens {
    cursor: pointer
  }
  
  .sideMenuItens:hover {
    background-color: #babaca;
    color: #fff !important;
  }
  
  /* Tooltip container */
  .tooltip {
    position: relative;
    display: inline-block;
  }
  
  /* Tooltip text */
  .tooltip .tooltiptext {
    visibility: hidden;
    width: 120px;
    background-color: black;
    color: #fff;
    text-align: center;
    padding: 5px 0;
    border-radius: 6px;
    
    /* Position the tooltip text - see examples below! */
    position: absolute;
    z-index: 1;
  }
  
  /* Show the tooltip text when you mouse over the tooltip container */
  .tooltip:hover .tooltiptext {
    visibility: visible;
  }
  
  .input-group {
    padding: 18px 5px 0;
  }
</style>
