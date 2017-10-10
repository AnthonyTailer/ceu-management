<template>
  <v-container grid-list-xl text-xs-center fill-height>
    <v-layout row wrap flexbox align-center>
      <v-flex id="login_flex" md6 offset-md3 xs10 offset-xs1>
        <v-card  class="white dark--text">
          <v-card-title primary-title class="blue white--text text-xs-center d-block" >
            <p class="headline mb-0">Casa do Estudante Universitário</p>
          </v-card-title>
          <v-card-text>
            <form v-on:submit.prevent="submit()">
              <v-text-field
                name="input-registration"
                v-model="user.registration"
                label="Matrícula"
                class="input-group--focused"
                :error-messages="errors.collect('matricula')"
                v-validate="'required|digits:9'"
                data-vv-name="matricula"
                required
              ></v-text-field>
              <v-text-field
                name="input-pass"
                v-model="user.password"
                class="input-group--focused"
                label="Coloque sua senha"
                :error-messages="errors.collect('senha')"
                v-validate="'required|min:8'"
                data-vv-name="senha"
                :append-icon="passShow ? 'visibility' : 'visibility_off'"
                :append-icon-cb="() => (passShow = !passShow)"
                :type="passShow ? 'text' : 'password'"
                counter
                @keyup.enter="submit"
                required
              ></v-text-field>
              <v-card-actions>
                <v-btn
                  info
                  @click.native="loader = 'loading'"
                  :disabled="loading"
                  @click="submit"
                >
                  Entrar
                  <span slot="loader" class="custom-loader">
                    <v-icon light>cached</v-icon>
                  </span>
                </v-btn>
                <v-btn flat class="purple--text" @click="clear">Esqueceu a Senha?</v-btn>
              </v-card-actions>
            </form>
          </v-card-text>
          <v-progress-linear :indeterminate="loading"></v-progress-linear>
        </v-card>
        <v-snackbar
          :timeout="6000"
          :error="true"
          :vertical="true"
          v-model="snackbar"
        >
          {{ msg }}
          <v-btn dark flat @click.native="snackbar = false">Fechar</v-btn>
        </v-snackbar>
      </v-flex>
    </v-layout>
  </v-container>
</template>
<script>
  export default {
    $validates: true,
    data () {
      return {
        snackbar: false,
        backgroundImage: '/static/back-login.jpg',
        msg: '',
        loader: null,
        loading: false,
        passShow: false,
        user: {
          registration: null,
          password: null
        }
      }
    },
    methods: {
      submit (e) {
        e.preventDefault()
        this.$validator.validateAll({
          email: this.user.registration,
          pass: this.user.pass }).then(result => {
            if (!result) {
              console.log('validation failed.')
            } else {
              this.loading = true
              this.$http.post('api/login/',
                { password: this.user.password, registration: this.user.registration }
              ).then(response => {
                this.loading = false
                console.log(response)
                if (response.ok) {
                  this.$auth.setToken(response.body.token)
                  this.$router.push('/dash')
                }
              }).catch((response) => {
                this.loading = false
                this.snackbar = true
                let erros = response.body.non_field_errors
                this.msg = erros.join()
              })
            }
          // success stuff.
        }).catch(() => {
          console.log('something went wrong (non-validation related')
        })
      },
      clear () {
        this.pass = ''
        this.registration = ''
        this.$validator.reset()
      }
    }
  }
</script>
<style scoped>
  /*#app  {*/
  /*background: url("/static/back-login.jpg") no-repeat center;*/
  /*background-size: cover;*/
  /*}*/
  
  .custom-loader {
    animation: loader 1s infinite;
    display: flex;
  }
  @-moz-keyframes loader {
    from {
      transform: rotate(0);
    }
    to {
      transform: rotate(360deg);
    }
  }
  @-webkit-keyframes loader {
    from {
      transform: rotate(0);
    }
    to {
      transform: rotate(360deg);
    }
  }
  @-o-keyframes loader {
    from {
      transform: rotate(0);
    }
    to {
      transform: rotate(360deg);
    }
  }
  @keyframes loader {
    from {
      transform: rotate(0);
    }
    to {
      transform: rotate(360deg);
    }
  }

</style>
