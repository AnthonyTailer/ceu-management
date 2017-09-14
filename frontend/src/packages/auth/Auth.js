export default function (Vue) {
  Vue.auth = {
    // set token
    // get token
    // destroy token
    // isAuthenticated
    setToken (token) {
      localStorage.setItem('token', token)
    },

    getToken () {
      let token = localStorage.getItem('token')

      if (!token) {
        return null
      } else {
        return token
      }
    },

    destroyToken () {
      localStorage.removeItem('token')
    },

    isAuthenticated () {
      if (this.getToken()) {
        return true
      } else {
        return false
      }
    }
  }

  Object.defineProperties(Vue.prototype, {
    $auth: {
      get () {
        return Vue.auth
      }
    }
  })
}
