<!DOCTYPE html>
<html>

<head>
  <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@mdi/font@6.x/css/materialdesignicons.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.min.css" rel="stylesheet">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
</head>

<body>
  <div id="app">
    <v-app>
      <v-img :src="bgImg">
        <v-main>
          <v-container> <br>
            <v-card class="mx-auto my-auto justify-center" max-width="500" height="425" flat style="background-color: #fff;  opacity: .8;"> <!-- color="green lighten-5"-->
              <v-card style="background-color: #fff; border: 0.5px solid rgba(255, 255, 255, 0.5);backdrop-filter: blur(2px);-webkit-backdrop-filter: blur(8px);" flat>
                <v-card class="mx-auto mt-3" flat>
                    <h3 class="text-center teal--text mt-2">AL WAFA BI'AHDILLAH</h3>
                    <h3 class="text-center teal--text mt-2">ADMINISTRATOR</h3>    
                </v-card>
                <v-card-text>
                    <div class="mx-auto mt-5 ml-5 mr-5" max-width="450" flat> <!--color="green lighten-5"-->
                      <v-row class="text-center">
                        <v-col cols="12">
                          <v-text-field
                            label="ID Anggota"
                            rounded
                            append-icon="mdi-account"
                            v-model="nia"
                            color="teal"
                            outlined
                            required
                            filled
                            class="text-green"></v-text-field>

                          <v-text-field
                            label="Password"
                            v-model="password"
                            rounded
                            outlined
                            required
                            :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                            :type="showPassword ? 'text' : 'password'"
                            @click:append="showPassword = !showPassword">
                          </v-text-field>
                        </v-col>

                        <v-col cols="12">
                          <v-btn color="primary" height="50" rounded depressed block @click="goLogin">LOGIN</v-btn>
                          <v-btn color="teal" text @click="goUser">User Page</v-btn>
                          <!-- <a href="#" class="teal--text text-center mt-2" style="text-decoration: none;">User Page</a> -->
                          <!-- <v-row class="mt-1">
                            <v-col cols="6">
                            </v-col>
                            <v-col cols="6">
                              <a href="#" class="teal--text text-decoration-none">Lupa Password</a>
                            </v-col>
                          </v-row> -->
                        </v-col>
                      </v-row>
                    </div>
                </v-card-text>
              </v-card>
            </v-card>

          </v-container>
        </v-main>
    </v-app>
    </v-img>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/vue@2.x/dist/vue.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    const Toast = Swal.mixin({
      toast: true,
      position: "top-end",
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true,
      didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
      }
    });
    new Vue({
      el: '#app',
      vuetify: new Vuetify({
        theme: {
          themes: {
            light: {
              primary: '#11A39C', // #E53935
              secondary: '#B2DFDB', // #FFCDD2
              // accent: colors.indigo.base, // #3F51B5
            },
          },
        },
      }),
      data: {
        nia: null,
        password: null,
        showPassword: false,
        logo: '<?= base_url(); ?>logo_alwafa_hijau.png',
        bgImg: '<?= base_url() ?>gunung.jpg',
        config: null,
      },
      created() {
        // const tokenAdmin = localStorage.getItem('admin-token')
        // if(tokenAdmin) {
        //     localStorage.removeItem('admin-token')
        // }
      },
      methods: {
        toast(ikon, pesan){
            Toast.fire({
                  icon: ikon,
                  title: pesan
                });
        },
        async goLogin() {
          const param = {
            nia: String(this.nia).toUpperCase(),
            password: String(this.password).toUpperCase()
          }
          await axios.post('<?= base_url(); ?>api/admin-login', param)
            .then((res) => {
              console.log(res.data);
              localStorage.clear()
              localStorage.setItem('admin-token', res.data.token)
              window.open('<?= base_url(); ?>administrator/dashboard', '_self')
            })
            .catch((err) => {
              console.log(err.response.data);
              if (err.response.status === 409) {
                
              }
              if (err.response.status === 400) {
                const err_nia = !err.response.data.messages.nia ? '' : err.response.data.messages.nia
                const err_pass = !err.response.data.messages.password ? '' : err.response.data.messages.password
                const pesan = err_nia + '\n' + err_pass
                Toast.fire({
                  icon: "error",
                  title: pesan
                });
              }
            })
        },
        goUser(){
          window.open('<?= base_url();?>login', '_self')
        },
      }
    })
  </script>
</body>

</html>