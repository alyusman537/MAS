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
      <v-main>
        <v-container>
            <v-card class="mx-auto justify-center" max-width="600" flat color="green lighten-5">
                <v-toolbar color="primary" dark flat class="text-center">
                    <p class="text-center">LOGIN</p>
                </v-toolbar>
                <v-card-text>
                    <v-row class="text-center">
                        <v-col cols="10">
                            <v-text-field
                                label="ID Anggota"
                                v-model="nia"
                                required
                            ></v-text-field>
                        </v-col>
                        <v-col cols="10">
                            <v-text-field
                                label="Password"
                                v-model="password"
                                required
                            ></v-text-field>
                        </v-col>
                        <v-col cols="7">
                            <v-btn color="success" rounded depressed block @click="goLogin">SUBMIT</v-btn>
                            <v-row>
                                <v-col cols="6">
                                    <!-- <v-btn color="success" text small>Admin Page</v-btn> -->
                                    <a href="#" class="text-green" style="text-decoration: none;">Admin Page</a>
                                </v-col>
                                <v-col cols="6">
                                    <a href="#" class="text-green text-decoration-none">Lupa Password</a>
                                    <!-- <v-btn color="success" text small>Lupa Password</v-btn> -->
                                </v-col>
                            </v-row>
                        </v-col>
                    </v-row>
                </v-card-text>
            </v-card>
        </v-container>
      </v-main>
    </v-app>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/vue@2.x/dist/vue.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  <script>
    new Vue({
      el: '#app',
      vuetify: new Vuetify(),
      data: {
        nia: null,
        password: null,
      },
      methods: {
        async goLogin() {
            const param = {
                nia: this.nia,
                password: this.password
            }
            await axios.post('<?= base_url();?>api/user-login', param)
            .then((res) => {
                console.log(res.data);
                localStorage.setItem('token', res.data.token)
                localStorage.setItem('nama', res.data.user.nama)
                localStorage.setItem('nia', res.data.user.nia)

                window.open('<?= base_url();?>profile', '_self')
            })
            .catch((err) => {
                console.log(err.response.data);
                
            })
        },
      }
    })
  </script>
</body>
</html>