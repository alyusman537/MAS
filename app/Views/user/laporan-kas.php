<!DOCTYPE html>
<html>

<head>
  <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@mdi/font@6.x/css/materialdesignicons.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.min.css" rel="stylesheet">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
</head>

<body style="background-color:#B2DFDB;">
  <div id="app">
    <v-app>
      <v-main style="background-color:#B2DFDB;">
        <nav-bar :title="title"></nav-bar>

        <v-container>
          <v-card class="mx-auto justify-center mt-5 pb-7" max-width="800" flat>
            <v-toolbar color="teal" flat class="text-center mx-auto" dark>
              <v-toolbar-title>Mutasi KAS </v-toolbar-title>
            </v-toolbar>
            <v-card-text class="px-2">
              <v-row>
                <v-col cols="6">
                  <v-dialog
                    ref="dialogAwal"
                    v-model="dialogTglAwal"
                    :return-value.sync="dateAwal"
                    persistent
                    width="290px">
                    <template v-slot:activator="{ on, attrs }">
                      <v-text-field
                        v-model="dateAwal"
                        label="Dari Tanggal"
                        prepend-icon="mdi-calendar"
                        readonly
                        v-bind="attrs"
                        v-on="on"
                        @change="getLaporanKas()"></v-text-field>
                    </template>
                    <v-date-picker
                      v-model="dateAwal"
                      scrollable>
                      <v-spacer></v-spacer>
                      <v-btn
                        text
                        color="primary"
                        @click="dialogTglAwal = false">
                        Cancel
                      </v-btn>
                      <v-btn
                        text
                        color="primary"
                        @click="$refs.dialogAwal.save(dateAwal)">
                        OK
                      </v-btn>
                    </v-date-picker>
                  </v-dialog>
                </v-col>
                <v-col cols="6">
                  <v-dialog
                    ref="dialogAkhir"
                    v-model="dialogTglAkhir"
                    :return-value.sync="dateAkhir"
                    persistent
                    width="290px">
                    <template v-slot:activator="{ on, attrs }">
                      <v-text-field
                        v-model="dateAkhir"
                        label="Sampai Tanggal"
                        prepend-icon="mdi-calendar"
                        readonly
                        v-bind="attrs"
                        v-on="on"
                        @change="getLaporanKas()"></v-text-field>
                    </template>
                    <v-date-picker
                      v-model="dateAkhir"
                      scrollable>
                      <v-spacer></v-spacer>
                      <v-btn
                        text
                        color="primary"
                        @click="dialogTglAkhir = false">
                        Cancel
                      </v-btn>
                      <v-btn
                        text
                        color="primary"
                        @click="$refs.dialogAkhir.save(dateAkhir)">
                        OK
                      </v-btn>
                    </v-date-picker>
                  </v-dialog>
                </v-col>
              </v-row>

              <template>
                <v-simple-table>
                  <template v-slot:default>
                    <thead>
                      <tr>
                        <th class="text-left">
                          Tanggal
                        </th>
                        <th class="text-right">
                          Debet
                        </th>
                        <th class="text-right">
                          Kredit
                        </th>
                        <th class="text-right">
                          Saldo
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-right">{{ saldoAwal }}</td>
                      </tr>
                      <tr
                        v-for="item in listMutasi"
                        :key="item.urut">
                        <td >{{ item.tanggal }}</td>
                        <td class="text-right">{{ item.masuk }}</td>
                        <td class="text-right">{{ item.keluar }}</td>
                        <td class="text-right">{{ item.saldo }}</td>
                      </tr>
                    </tbody>
                  </template>
                </v-simple-table>
              </template>

            </v-card-text>
          </v-card>

        </v-container>
      </v-main>
    </v-app>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/vue@2.x/dist/vue.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  <script src="<?= base_url(); ?>api/render/js/dash.js"></script>
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
              accent: '#E8F5E9',
              anchor: '#B2DFDB'
            },
          },
        },
      }),
      data: {
        title: "LAPORAN KAS",
        config: null,
        token: null,
        nia: null,
        dialogTglAwal: false,
        dialogTglAkhir: false,
        dateAkhir: (new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10),
        dateAwal: null,
        saldoAwal: 0,
        saldoAkhir: 0,
        listMutasi: [],

      },
      watch: {},
      created() {
        const localData = JSON.parse(localStorage.getItem("user"));
        this.nia = localData.nia
        this.token = localStorage.getItem('token')
        this.config = {
          headers: {
            Authorization: `Bearer ${this.token}`
          }
        }
        this.dateAwal = (new Date(Date.parse(this.dateAkhir) - (1000 * 60 * 60 * 24 * 30))).toISOString().substr(0, 10);
        this.getLaporanKas()
      },
      methods: {
        toast(ikon, pesan) {
          Toast.fire({
            icon: ikon,
            title: pesan
          });
        },
        keluar() {
          localStorage.clear()
          window.open('<?= base_url(); ?>login', '_self')
        },
        async refresh() {
          await axios.get('<?= base_url() ?>api/user/refresh-token', this.config)
            .then((res) => {
              localStorage.setItem('token', res.data.new_token)
            })
            .catch()
        },
        async getLaporanKas() {
          await axios.get('<?= base_url(); ?>api/user/laporan-kas/' + this.dateAwal + '/' + this.dateAkhir, this.config)
            .then((res) => {
              // console.log('res ', res.data);
              this.refresh()
              this.saldoAwal = parseInt(res.data.saldo_awal)
              let saldo = this.saldoAwal
              let urut = 0
              this.listMutasi = res.data.mutasi.map((val) => {
                const tgl = String(val.tanggal).split(' ')
                const spTanggal = String(tgl[0]).split('-')
                const masuk = parseInt(val.debet)
                const keluar = parseInt(val.kredit)
                saldo = saldo + masuk - keluar
                const dorong = {
                  tanggal: `${spTanggal[2]}-${spTanggal[1]}-${spTanggal[0]}`,
                  masuk: masuk.toLocaleString('ID-id'),
                  keluar: keluar.toLocaleString('ID-id'),
                  saldo: saldo.toLocaleString('ID-id'),
                  nomor: urut + 1

                };
                return dorong
              })
            })
            .catch((err) => {
              if(err.response.status == 401) {
                this.keluar()
              }
              console.log('getlist infq ', err);

            })
        },

      }
    })
  </script>
</body>

</html>