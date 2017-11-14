import Vue from 'vue'
import VueResource from 'vue-resource'
import Auth from '../auth/Auth.js'

Vue.use(VueResource)
Vue.use(Auth)


export default function (Vue) {


  Vue.permissions = {

    isAdmin () {
      Vue.http.get('api/user/is-admin?token='+Vue.auth.getToken()).then( (response) => {
        return response.data
      })
    }
  }

  Object.defineProperties(Vue.prototype, {
    $permissions: {
      get () {
        return Vue.permissions
      }
    }
  })
}
