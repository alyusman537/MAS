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
            <!--v-card color="teal" flat class="text-center mx-auto py-3" dark>
              <h3 class="mx-auto">Daftar Infaq</h3>
            </v-card-->
            <v-card-text class="px-2">
              <v-data-table
                :headers="headerInfaq"
                :items="listInfaq"
                class="elevation-0">
                <template v-slot:item.lunas="{ item }">
                  <v-btn
                    :color="item.warna_lunas"
                    depressed
                    rounded
                    small
                    dark
                    @click="loadPembayaran(item)">
                    {{ item.lunas }}
                  </v-btn>
                </template>
              </v-data-table>
            </v-card-text>
          </v-card>

          <v-dialog
            v-model="dialogBayar">
            <v-card flat max-width="500" class="mx-auto">
              <v-toolbar color="primary" flat dark>
                <v-toolbar-title>Pembayaran Infaq</v-toolbar-title>
              </v-toolbar>
              <v-card-text class="py-8">
                <v-row>
                  <v-col cols="12">
                    <v-simple-table>
                      <template v-slot:default>
                        <tbody>
                          <tr>
                            <td>Acara</td>
                            <td class="text-right font-weight-bold">{{ infaq.acara }}</td>
                          </tr>
                          <tr>
                            <td>Tanggal Acara</td>
                            <td class="text-right font-weight-bold">{{ infaq.tanggal }}</td>
                          </tr>
                          <tr>
                            <td>Nominal</td>
                            <td class="text-right font-weight-bold">{{ infaq.nominal }}</td>
                          </tr>
                          <tr>
                            <td>Nomor Pembayaran</td>
                            <td class="text-right font-weight-bold">{{ infaq.nomor_pembayaran }}</td>
                          </tr>
                        </tbody>
                      </template>
                    </v-simple-table>
                  </v-col>
                  <v-col cols="6">
                    <v-menu
                      v-model="menu2"
                      :close-on-content-click="false"
                      :nudge-right="40"
                      transition="scale-transition"
                      offset-y
                      min-width="auto">
                      <template v-slot:activator="{ on, attrs }">
                        <v-text-field
                          v-model="infaq.tanggal_bayar"
                          label="Pilih Tanggal Pembayaran"
                          readonly
                          outlined
                          v-bind="attrs"
                          v-on="on"></v-text-field>
                      </template>
                      <v-date-picker
                        v-model="infaq.tanggal_bayar"
                        @input="menu2 = false"></v-date-picker>
                    </v-menu>
                  </v-col>
                  <v-col cols="6">
                    <v-text-field
                      class="text-right"
                      outlined
                      v-model="infaq.nominal_bayar"
                      type="number"
                      pattern="[0-9\s]{13,19}"
                      label="Nominal Pembayaran"></v-text-field>
                  </v-col>
                  <v-col cols="12">

                    <v-file-input
                      label="Unggah bukti pembayaran"
                      outlined
                      dense
                      show-size
                      hint="Harus berupa file jpg atau jpeg dan ukuran maximal 2mb"
                      @change="upload">
                    </v-file-input>
                  </v-col>
                  <v-col cols="12">
                    <v-btn color="success" block depressed @click="bayarInfaq()">Bayar sekarang</v-btn>
                    <!-- <v-btn color="success" block depressed @click="uploadBuktiBayar()">Bayar sekarang</v-btn> -->
                    <!-- bayarInfaq -->
                  </v-col>
                  <v-col cols="12">
                    <v-btn color="error" block depressed @click="dialogBayar = false">Batal</v-btn>
                  </v-col>
                </v-row>
              </v-card-text>
            </v-card>
          </v-dialog>
          
        </v-container>
      </v-main>
    </v-app>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/vue@2.x/dist/vue.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.min.js"></script>
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
        title: "DAFTAR INFAQ",
        config: null,
        token: null,
        nia: null,
        headerInfaq: [{
            text: 'Tanggal',
            align: 'start', // sortable: false,
            value: 'tanggal',
          },
          {
            text: 'Acara',
            value: 'acara'
          },
          {
            text: 'nominal',
            value: 'nominal',
            align: 'end'
          },
          {
            text: 'Bayar',
            value: 'bayar',
            align: 'end'
          },
          {
            text: 'Status',
            value: 'lunas'
          },
          {
            text: 'Penerima',
            value: 'validator'
          },
        ],
        listInfaq: [],
        dialogBayar: false,
        infaq: {
          nomor_pembayaran: null,
          acara: null,
          tanggal: null,
          nominal: null,
          terbayar: null,
          nominal_bayar: 0,
          tanggal_bayar: (new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10),
        },
        menu2: false,
        attFile: null,
        linkFoto: null,
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
        this.getListInfaq()
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
        async getListInfaq() {
          await axios.get('<?= base_url(); ?>/api/user/home/daftar-infaq/' + this.nia, this.config)
            .then((res) => {
              console.log(res.data);
              this.listInfaq = res.data.map((val) => {
                const spTanggal = String(val.tanggal).split('-')
                const nominal = parseInt(val.nominal)
                const bayar = parseInt(val.bayar)
                const status_lunas = bayar >= nominal ? true : false
                const dorong = {
                  'nomor_pembayaran': val.nomor_pembayaran,
                  'acara': val.acara,
                  'tanggal': `${spTanggal[2]}-${spTanggal[1]}-${spTanggal[0]}`,
                  'nominal': nominal.toLocaleString('ID-id'),
                  'bayar': bayar.toLocaleString('ID-id'),
                  'lunas': val.validator != null && status_lunas === true? 'Lunas' : ( val.validator == null && status_lunas === false ? 'Bayar' : 'Pending'),
                  'warna_lunas': val.validator != null && status_lunas === true? 'success' : ( val.validator == null && status_lunas === false ? 'error' : 'warning'),
                  'validator': val.validator,
                  'intBayar': bayar,
                  'intNominal': nominal


                };
                return dorong
              })
              console.log(this.listInfaq);
            })
            .catch((err) => {
              if(err.response.status === 401) {
                localStorage.clear()
                window.open("<?= base_url()?>login", '_self')
              }
              console.log('getlist infq ', err);

            })
        },
        loadPembayaran(item) {
          if(item.intBayar >= item.intNominal) {
            this.toast('warning', 'Infaq ini sedang menunggu penerimaan oleh admin.')
            return false
          };
          
          this.infaq.nomor_pembayaran = item.nomor_pembayaran
          this.infaq.acara = item.acara
          this.infaq.tanggal = item.tanggal
          this.infaq.terbayar = item.bayar
          this.infaq.nominal = item.nominal
          this.infaq.pembayaran = null

          this.dialogBayar = true
        },
        async bayarInfaq() {
          if (this.infaq.nominal_bayar == 0 || this.infaq.nominal_bayar == '') {
            this.toast('error', 'Anda belum memasukkan nominal pembayaran')
            return false
          }
          let fdata = new FormData()
            fdata.append('bayar', this.infaq.nominal_bayar)
            fdata.append("tanggal_bayar", this.infaq.tanggal_bayar)
            fdata.append("bukti", this.attFile)
          // try {
          // } catch (error) {
          //   console.log(error);
          // }

          // return console.log(option);
          const param = {
            bayar: this.infaq.nominal_bayar,
            tanggal_bayar: this.infaq.tanggal_bayar,
            form: fdata
          }
          console.log(param);
          
          await axios.post('<?= base_url(); ?>/api/user/pembayaran/' + this.infaq.nomor_pembayaran, fdata, this.config)
            .then((res) => {
              // console.log(res.data);
              this.refresh()
              this.toast('success', res.data.pesan)
              this.getListInfaq()
              this.dialogBayar = false
            })
            .catch((err) => {
              if(err.response.status == 401) {
                this.keluar()
              }
              if (err.response.status === 402) {
                this.toast('error', err.response.data.messages.error)
              }
              if (err.response.status === 409) {
                alert(JSON.stringify(err.response.data.messages))
              }
              console.log(err.response.data);

            })
        },
        upload(event) {
          console.log("nama", event);
          console.log("type", event.type);
          console.log("ukuran", event.size);

          if (event.type != "image/jpeg") {
            this.toast('error', "Silakan upload file yang dengan ekstensi .jpeg atau.jpg");
            this.linkFoto = "";
            this.attFile = null;
            return false;
          }
          this.attFile = event;
          this.linkFoto = URL.createObjectURL(event);
        },
        async uploadBuktiBayar() {
          if (this.attFile == null) {
            this.toast('error', "Anda belum memilih foto");
            return false;
          }
          let fdata = new FormData();
          fdata.append("bukti", this.attFile);
          return console.log(fdata);
          
          await axios
            .post('<?= base_url(); ?>api/user/pembayaran-bukti/' + this.infaq.nomor_pembayaran, fdata, this.config)
            .then((res) => {
              console.log(res.data);
              this.bayarInfaq()
            })
            .catch((err) => {
              console.log(err.response);
            });
        },

      }
    })
  </script>
</body>

</html>