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
          <v-card max-width="600" color="secondary" flat class="mt-15">
            <v-toolbar color="primary" flat dark>
              <div class="mx-auto">
                <h4>Permintaan Reset Password</h4>
              </div>
            </v-toolbar>
            <v-card-text class="mt-4 px-10">
              <v-row class="text-center">
                <v-col cols="12">
                  <v-text-field
                    label="ID Anggota"
                    v-model="nia"
                    color="teal"
                    outlined
                    required
                    filled
                    hint="Masukkan nomor ID Anggota Anda"></v-text-field>
                  <v-text-field
                    label="Nomor WA"
                    v-model="wa"
                    type="number"
                    pattern="[0-9\s]{13,19}"
                    outlined
                    required
                    filled
                    hint="Masukkan nomor WA Anda"></v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-btn color="info" block depressed height="55" @click="mintaOtp()">Minta OTP</v-btn>
                </v-col>
                <v-col cols="12">
                  <v-otp-input
                    length="5"
                    v-model="otp"></v-otp-input>
                </v-col>

                <v-col cols="12">
                  <v-btn color="primary" height="50" rounded depressed block @click="kirimOtp">kirim permintaan reset password</v-btn>
                </v-col>
                <v-col cols="12">
                  <v-btn color="error" height="50" rounded depressed block @click="goLoginPage">batal</v-btn>
                  <v-btn color="error" height="50" rounded depressed block @click="waktuJalan">jalan</v-btn>
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>
          <h3>{{ timerCount }}</h3>
        </v-container>
      </v-main>
    </v-app>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/vue@2.x/dist/vue.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.min.js"></script>
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
        wa: null,
        otp: null,
        token_otp: null,
        /////////
        timerCount: 180,
        minutes: 3,
        seconds: 0,
        isEnded: false,
        //////////
        password: null,
        showPassword: false,
        logo: '<?= base_url(); ?>logo_alwafa_hijau.png',
        bgImg: '<?= base_url() ?>payung.jpg',
        config: null,
      },
      created() {
        // this.waktuJalan()
      },
      watch: {
        timerCount: {
          handler(value) {
            if (value > 0) {
              setTimeout(() => {
                this.timerCount--;
              }, 1000);
            }
          },
          immediate: true // This ensures the watcher is triggered upon creation
        }
      },
      methods: {
        async goLoginPage() {
          window.open('<?= base_url() ?>login', '_self')
        },
        async mintaOtp() {
          const param = {
            nia: this.nia,
            wa: this.wa
          }
          await axios.post('<?= base_url() ?>api/user/minta-otp', param)
            .then((res) => {
              console.log(res.data);
              this.token_otp = res.data.token_otp
              Toast.fire({
                icon: 'success',
                title: res.data.pesan
              })
            })
            .catch((err) => {
              console.log(err.response);
              if (err.response.status == 409) {
                const errNia = err.response.data.messages.nia ? err.response.data.messages.nia + '\n' : ''
                const errWa = err.response.data.messages.wa ? err.response.data.messages.wa + '\n' : ''
                Toast.fire({
                  icon: "error",
                  title: errNia + errWa
                });
              } else if (err.response.status == 402) {
                Toast.fire({
                  icon: "error",
                  title: err.response.data.messages.error
                });
              } else {
                Toast.fire({
                  icon: "error",
                  title: JSON.stringify(err.response.data)
                });
              }
            })
        },
        async kirimOtp() {
          const param = {
            token_otp: this.token_otp,
            otp: this.otp
          }
          console.log(param);

          await axios.post('<?= base_url(); ?>api/user/kirim-otp', param)
            .then((res) => {
              console.log(res.data);
              Toast.fire({
                icon: "success",
                title: res.data.pesan
              });
              this.minutes = 3
              this.seconds = 0
              // this.goLoginPage()
              // this.wa = null 
              // this.otp = nul
              // this.token_otp = null
              // this.nia = null
            })
            .catch((err) => {
              console.log(err.response.data);
              if (err.response.status === 402) {
                Toast.fire({
                  icon: "error",
                  title: err.response.data.messages.error
                });
              }
              if (err.response.status === 409) {
                const err_otp = !err.response.data.messages.otp ? '' : err.response.data.messages.otp + '\n'
                const err_token_otp = !err.response.data.messages.token_otp ? '' : err.response.data.messages.token_otp
                const pesan = err_otp + err_token_otp
                Toast.fire({
                  icon: "error",
                  title: pesan
                });
              }
            })
        },
        updateRemaining(distance) {
          this.minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60))
          this.seconds = Math.floor((distance % (1000 * 60)) / 1000)
          console.log('minutes :' + this.minutes + ' detik: ' + this.seconds);

        },

        tick() {
          const currentTime = new Date()
          const distance = Math.max(this.endDate - currentTime, 0)
          this.updateRemaining(distance)

          if (distance === 0) {
            clearInterval(this.timer)
            this.isEnded = true
          }
        },
        waktuJalan() {
          this.tick()
          this.timer = setInterval(this.tick.bind(this), 1000)
        },
        goLoginPage() {
          window.open('<?= base_url(); ?>login', '_self')
        },
        gotReset() {
          window.open('<?= base_url(); ?>user/reset', '_self')
        }
      }
    })
  </script>
</body>

</html>