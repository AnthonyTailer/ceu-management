import Vue from 'vue'
import VueResource from 'vue-resource'
import Auth from '../../packages/auth/Auth.js'
import { eventBus } from "../../main"

Vue.use(VueResource)
Vue.use(Auth)


const state = {
  newOneStudent: false,
  editOneStudent: false,
  removeOneStudent: false,
  student: {
    id: null,
    fullName: '',
    registration: '',
    id_course: null,
    id_apto: null,
    age: null,
    genre: 'M',
    email: '',
    rg: '',
    cpf: '',
    phone1: '',
    is_bse_active: false,
    is_admin: false
  },
  allStudents: []
};

const getters = {
  getStudentNewState: state => {
    return state.newOneStudent
  },
  getStudentEditState: state => {
    return state.editOneStudent
  },
  getStudentRemoveState: state => {
    return state.removeOneStudent
  },
  getStudentState: state => {
    return state.student
  },
  getAllStudentsState: state => {
    return state.allStudents
  }
}

const mutations = {
  setInitialData: (state) => {
    state.student = {
      id: null,
      fullName: '',
      registration: '',
      id_course: null,
      id_apto: null,
      age: null,
      genre: 'M',
      email: '',
      rg: '',
      cpf: '',
      phone1: '',
      is_bse_active: false,
      is_admin: false
    }
  },
  setStudentNewState: (state, payload) => {
    state.newOneStudent = payload
  },
  setStudentEditState: (state, payload) => {
    state.editOneStudent = payload
  },
  setStudentRemoveState: (state, payload) => {
    state.removeStudent = payload
  },
  setAllStudents: (state, payload) => {
    state.allStudents = payload
  },
  newStudent: (state, payload) => {
    state.student = payload
  },
  setEditStudent: (state, payload) => {
    state.student = payload
  },
  setRemoveStudent: (state, payload) => {
    state.student = payload
  }
}

const actions = {
  newStudent: ({ commit, dispatch },payload) => {
    let data = {
      fullName: payload.fullName,
      registration: payload.registration,
      id_course: payload.id_course,
      id_apto: payload.id_apto,
      age: payload.age,
      genre: payload.genre,
      email: payload.email,
      rg: payload.rg,
      cpf: payload.cpf,
      // phone1: payload.phone1,
      is_bse_active: payload.is_bse_active,
      is_admin: payload.is_admin
    }

    let snack = {}

    Vue.http.post('api/user/register?token='+ Vue.auth.getToken(), data)
      .then( (response) => {
        snack = {
          activator: true,
          error: false,
          success: true,
          msg: response.body.message
        }

        commit('setInitialData')
        commit('setSnack', snack)
        dispatch('setAllStudents')
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
  editStudent: ({ commit, dispatch },payload) => {
    let data = {
      id: payload.id,
      fullName: payload.fullName,
      registration: payload.registration,
      id_course: payload.id_course,
      id_apto: payload.id_apto,
      age: payload.age,
      genre: payload.genre,
      email: payload.email,
      rg: payload.rg,
      cpf: payload.cpf,
      // phone1: payload.phone1,
      is_bse_active: payload.is_bse_active,
      is_admin: payload.is_admin
    }

    let snack = {}

    Vue.http.put(`api/user/edit?token=`+ Vue.auth.getToken(), data)
      .then( (response) => {
        snack = {
          activator: true,
          error: false,
          success: true,
          msg: response.body.message
        }

        commit('setInitialData')
        commit('setSnack', snack)
        dispatch('setAllStudents')
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
  removeStudent: ( {commit, dispatch}, payload) => {

    let snack = {}

    Vue.http.delete(`api/user/${payload.id}?token=`+ Vue.auth.getToken(), payload.number)
      .then( (response) => {

        snack = {
          activator: true,
          error: false,
          success: true,
          msg: response.body.message
        }

        commit('setInitialData')
        commit('setSnack', snack)
        dispatch('setAllStudents')
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
  setAllStudents: ({ commit }) => {

      Vue.http.get('api/users?token='+ Vue.auth.getToken())
        .then(response => {
          console.log(response)
          let result = response.body.data
          for (let i = 0; i < result.length; i++) {
            Object.assign(result[i], {'value': false })
          }

          console.log('GET Users -> ',result)
          commit('setAllStudents', result)
        })

  },
  setEditStudent: ( {commit}, payload) => {

    let data = {
      id: payload.id,
      fullName: payload.fullName,
      registration: payload.registration,
      id_course: payload.id_course,
      id_apto: payload.id_apto,
      age: payload.age,
      genre: payload.genre,
      email: payload.email,
      rg: payload.rg,
      cpf: payload.cpf,
      // phone1: payload.phone1,
      is_bse_active: payload.is_bse_active,
      is_admin: payload.is_admin
    }

    commit('setEditStudent', data)
  },
  setRemoveStudent: ( {commit}, payload) => {

    let data = {
      id: payload.id,
      fullName: payload.fullName,
      registration: payload.registration,
      id_course: payload.id_course,
      id_apto: payload.id_apto,
      age: payload.age,
      genre: payload.genre,
      email: payload.email,
      rg: payload.rg,
      cpf: payload.cpf,
      // phone1: payload.phone1,
      is_bse_active: payload.is_bse_active,
      is_admin: payload.is_admin
    }

    commit('setRemoveStudent', data)
  },
  setStudentNewState: ({ commit }, payload) => {
    commit('setStudentNewState', payload)
  },
  setStudentEditState: ({ commit }, payload) => {
    commit('setStudentEditState', payload)
  },
  setStudentRemoveState: ({ commit }, payload) => {
    commit('setStudentRemoveState', payload)
  },
}

export default {
  state,
  getters,
  mutations,
  actions
}

