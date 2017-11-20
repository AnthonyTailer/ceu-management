import Vue from 'vue'
import VueResource from 'vue-resource'
import Auth from '../../packages/auth/Auth.js'
import { eventBus } from "../../main";

Vue.use(VueResource)
Vue.use(Auth)


const state = {
  newOneApartament: false,
  editOneApartament: false,
  removeApartament: false,
  apartament: {
    id: '',
    number: '',
    block: '',
    building: '',
    capacity: '',
    vacancy_type: 'MF'
  },
  allAptos: []
};

const getters = {
  getAptoNewState: state => {
    return state.newOneApartament
  },
  getAptoEditState: state => {
    return state.editOneApartament
  },
  getAptoRemoveState: state => {
    return state.removeApartament
  },
  getApartamentState: state => {
    return state.apartament
  },
  getAllAptosState: state => {
    return state.allAptos
  }
}

const mutations = {
  setAptoNewState: (state, payload) => {
    state.newOneApartament = payload
  },
  setAptoEditState: (state, payload) => {
    state.editOneApartament = payload
  },
  setAptoRemoveState: (state, payload) => {
    state.removeApartament = payload
  },
  setAllAptos: (state, payload) => {
    state.allAptos = payload
  },

  newApartament: (state, payload) => {
    state.apartament = payload
  },
  setEditApartament: (state, payload) => {
    state.apartament = payload
  },
  setRemoveApartament: (state, payload) => {
    state.apartament = payload
  }
}

const actions = {
  newApartament: ({ commit, dispatch },payload) => {
    let data = {
      number: payload.number,
      block: payload.block,
      building: payload.building,
      capacity: payload.capacity,
      vacancy_type: payload.vacancy_type
    }

    let snack = {}

    Vue.http.post('api/apto/register?token='+ Vue.auth.getToken(), data)
      .then( (response) => {
        snack = {
          activator: true,
          error: false,
          success: true,
          msg: response.body.message
        }

        commit('newApartament', {number: '', block: '', building: '', capacity: '', vacancy_type: ''})
        commit('setSnack', snack)
        dispatch('setAllAptos')
        eventBus.fire('closeModal', false)

      }).catch( (response) => {

        snack = {
          activator: true,
          error: true,
          success: false,
          msg: ''
        }

        let msg = ''

        if (response.body.hasOwnProperty('errors')) {
          for (let i in response.body.errors) {
            response.body.errors[i].forEach((item) => {
              msg += item + '<br>'
            })
          }
        } else {
          msg = response.body.message
        }
        snack.msg = msg
        commit('setSnack', snack)
    })
  },
  editApartament: ({ commit, dispatch },payload) => {
    let data = {
      id: payload.id,
      number: payload.number,
      block: payload.block,
      building: payload.building,
      capacity: payload.capacity,
      vacancy_type: payload.vacancy_type
    }

    let snack = {}

    Vue.http.put(`api/apto/${data.number}?token=`+ Vue.auth.getToken(), data)
      .then( (response) => {
        snack = {
          activator: true,
          error: false,
          success: true,
          msg: response.body.message
        }

        commit('newApartament', {id: '', number: '', block: '', building: '', capacity: '', vacancy_type: ''})
        commit('setSnack', snack)
        dispatch('setAllAptos')
        eventBus.fire('closeModal', false)

      }).catch( (response) => {

      snack = {
        activator: true,
        error: true,
        success: false,
        msg: ''
      }

      let msg = ''

      if (response.body.hasOwnProperty('errors')) {
        for (let i in response.body.errors) {
          response.body.errors[i].forEach((item) => {
            msg += item + '<br>'
          })
        }
      } else {
        msg = response.body.message
      }
      snack.msg = msg
      commit('setSnack', snack)
    })
  },
  removeApartament: ( {commit, dispatch}, payload) => {

    let snack = {}

    Vue.http.delete(`api/apto/${payload.number}?token=`+ Vue.auth.getToken(), payload.number)
      .then( (response) => {

        snack = {
          activator: true,
          error: false,
          success: true,
          msg: response.body.message
        }

        commit('newApartament', {id: '', number: '', block: '', building: '', capacity: '', vacancy_type: ''})
        commit('setSnack', snack)
        dispatch('setAllAptos')
        eventBus.fire('closeModal', false)

      }).catch( (response) =>  {
        snack = {
          activator: true,
          error: true,
          success: false,
          msg: ''
        }

        let msg = ''

        if (response.body.hasOwnProperty('errors')) {
          for (let i in response.body.errors) {
            response.body.errors[i].forEach((item) => {
              msg += item + '<br>'
            })
          }
        } else {
          msg = response.body.message
        }
        snack.msg = msg
        commit('setSnack', snack)
    })
  },
  setAllAptos: ({ commit }) => {
    Vue.http.get('api/apto/all?token='+ Vue.auth.getToken())
      .then((response) => {
        console.log(response)

        let result = response.body.aptos
        for (let i in result) {
          Object.assign(result[i], {'value': false })
        }

        commit('setAllAptos', result)
      })
  },
  setEditApartament: ( {commit}, payload) => {

    let data = {
      id: payload.id,
      number: payload.number,
      block: payload.block,
      building: payload.building,
      capacity: payload.capacity,
      vacancy_type: payload.vacancy_type
    }

    commit('setEditApartament', data)
  },
  setRemoveApartament: ( {commit}, payload) => {

    let data = {
      id: payload.id,
      number: payload.number,
      block: payload.block,
      building: payload.building,
      capacity: payload.capacity,
      vacancy_type: payload.vacancy_type
    }

    commit('setRemoveApartament', data)
  },
  setAptoNewState: ({ commit }, payload) => {
    commit('setAptoNewState', payload)
  },
  setAptoEditState: ({ commit }, payload) => {
    commit('setAptoEditState', payload)
  },
  setAptoRemoveState: ({ commit }, payload) => {
    commit('setAptoRemoveState', payload)
  },
}

export default {
  state,
  getters,
  mutations,
  actions
}