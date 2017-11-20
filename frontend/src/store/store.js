import Vue from 'vue'
import Vuex from 'vuex'

import Apartament from './modules/apartament'

Vue.use(Vuex)

export const store = new Vuex.Store({
  state: {
    admin: 0,
    snackbar: {
      activator: false,
      error: false,
      success: false,
      msg: ''
    }
  },
  getters: {
    adminState: state => {
      return state.admin
    },
    snackState: state => {
      return state.snackbar
    }
  },
  mutations: {
    isAdmin: (state, value) => {
      state.admin = value
    },
    setSnack: (state, payload) => {
      state.snackbar = payload
    }
  },
  modules: {
    Apartament
  }
})