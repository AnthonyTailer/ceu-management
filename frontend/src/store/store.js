import Vue from 'vue';
import Vuex from 'vuex';
import VueResource from 'vue-resource'
import Auth from '../packages/auth/Auth.js'

Vue.use(Vuex)
Vue.use(VueResource)
Vue.use(Auth)

export const store = new Vuex.Store({
  state: {
    admin: 0
  },
  getters: {
    adminState: state => {
      return state.admin
    }
  },
  mutations: {
    isAdmin: (state, value) => {
      state.admin = value
    }
  },
  actions: {
    isAdmin: ({ commit }) => {
      let value = 0;

      Vue.http.get('api/user/is-admin?token='+Vue.auth.getToken()).then( (response) => {
        console.log(response)
        value = response.body.data
        commit('isAdmin', value)
      })

    }
  }
})