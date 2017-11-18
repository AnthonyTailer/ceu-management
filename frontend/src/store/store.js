import Vue from 'vue';
import Vuex from 'vuex';
import VueResource from 'vue-resource'
import Auth from '../packages/auth/Auth.js'

Vue.use(Vuex)
Vue.use(VueResource)
Vue.use(Auth)

export const store = new Vuex.Store({
  state: {
    admin: 0,
    modal: false
  },
  getters: {
    adminState: state => {
      return state.admin
    },
    modalState: state => {
      return state.modal
    }
  },
  mutations: {
    isAdmin: (state, value) => {
      state.admin = value
    },
    openModal: (state) => {
      state.modal = true
    },
    closeModal: (state) => {
      state.modal = false
    },
  }
})