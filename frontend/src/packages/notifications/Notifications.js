import Vue from 'vue'
import VueNotifications from 'vue-notifications'
import VueResource from 'vue-resource'
import Auth from '../auth/Auth.js'
import { eventBus } from "../../main";

Vue.use(VueResource)
Vue.use(Auth)

export default function (Vue) {
  Vue.notify = {

    getNotifications () {
      if(Vue.auth.isAuthenticated()){
        Vue.http.get('api/user/get-notifications?token='+ Vue.auth.getToken())
          .then( (response) => {
            console.log(response)

            let noti = Vue.notify.getNotificationsCookie()

            if(noti){
              if(response.body.count > noti){
                localStorage.setItem('notifications', response.body.count)
                let audio = new Audio('static/alert_sound.mp3');
                audio.play()
                VueNotifications.info({message: 'Você possui Notificações pendentes'})
                eventBus.fire('newNotifications', response.body)
              }else {
                eventBus.fire('newNotifications', response.body)
              }
            }else {
              localStorage.setItem(notifications, 0)
            }
          })
      }
    },

    getNotificationsEachMinute () {
      setInterval(function () {
        Vue.notify.getNotifications()
      }, 300000);
    },

    getNotificationsCookie () {
      let notifications = localStorage.getItem('notifications')

      if (!notifications)
        return null
      else
        return notifications
    }
  }

  Object.defineProperties(Vue.prototype, {
    $notify: {
      get () {
        return Vue.notify
      }
    }
  })
}
