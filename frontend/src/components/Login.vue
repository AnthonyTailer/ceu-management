<template>
  <v-container grid-list-xl text-xs-center fill-height>
    <v-layout row wrap flexbox align-center>
      <v-flex id="login_flex" md6 offset-md3 xs10 offset-xs1>
        <v-card  class="white dark--text">
          <v-card-title primary-title class="blue white--text text-xs-center d-block" >
              <p class="headline mb-0">Casa do Estudante Universitário</p>
          </v-card-title>
          <v-card-text>
            <form>
              <v-text-field
                name="input-email"
                v-model="user.email"
                label="E-mail"
                class="input-group--focused"
                :error-messages="errors.collect('email')"
                v-validate="'required|email'"
                data-vv-name="email"
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
        <v-alert error dismissible v-model="alert">
          {{msg}}
        </v-alert>
      </v-flex>
    </v-layout>
  </v-container>
</template>
<script>
  export default {
    $validates: true,
    data () {
      return {
        alert: false,
        msg: '',
        loader: null,
        loading: false,
        passShow: false,
        user: {
          email: null,
          password: null
        }
      }
    },
    methods: {
      submit (e) {
        e.preventDefault()
        this.$validator.validateAll({ email: this.user.email, pass: this.user.pass }).then(result => {
          if (!result) {
            console.log('validation failed.')
          } else {
            this.loading = true
            this.$auth.login({
              body: {password: this.user.password, email: this.user.email},
              success: function (response) {
                console.log('Usuário logado com sucesso.')
                console.log(response)
                this.loading = false
              },
              error: function (e) {
                console.log('Usuário e/ou senha inválidos.', e)
                this.loading = false
                this.alert = true
                this.msg = e.body
              },
              rememberMe: true,
              redirect: '/alunos'
            })
          }
          // success stuff.
        }).catch(() => {
          console.log('something went wrong (non-validation related')
        })
      },
      clear () {
        this.pass = ''
        this.email = ''
        this.$validator.reset()
      }
    }
  }
</script>
<style >
  body {
    /* Center and scale the image nicely */
    background: url("../../static/back-login.jpg") no-repeat center;
    height: 100%;
    background-size: cover;
  }

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
