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
      <!-- <v-main style="background-color:#B2DFDB;"> -->
      <v-main>
        <admin-nav-bar :title="title"></admin-nav-bar>

        <v-container>
          <v-card class="mx-auto justify-center mt-5 pb-7" max-width="800" flat color="teal lighten-5">
            <!--v-card color="teal" flat class="text-center mx-auto py-3" dark>
              <h3 class="mx-auto">Daftar Infaq</h3>
            </v-card-->
            <v-card-text class="px-2">
              <v-row class="px-4">
                <v-col cols="8">
                <p>Saldo Kas Awal Bulan</p>  
                <p> Pemasukan</p>
                  <p>Pengeluaran</p>
                  <p>Saldo Kas Akhir Bulan</p>
                </v-col>
                <v-col cols="4">
                <p class="font-weight-bold text-right">300.000</p>  
                <p class="font-weight-bold text-right">1.000.000</p>
                  <p class="font-weight-bold text-right">500.000</p>
                  <p class="font-weight-bold text-right">800.000</p>
                </v-col>
              </v-row>
              <template>
                <v-divider></v-divider>
                <v-simple-table>
                  <template v-slot:default>
                    <tbody>
                      <tr>
                        <td>Jumlah Anggota Aktif</td>
                        <td class="text-right">
                          <v-btn color="info" small depressed rounded width="75">2025</v-btn>
                        </td>
                      </tr>
                      <tr>
                        <td>Tagihan infaq belum terbayar</td>
                        <td class="text-right"><v-btn color="info" small depressed>2025</v-btn></td>
                      </tr>
                      <tr>
                        <td>Tagihan infaq belum Diterima</td>
                        <td class="text-right"><v-btn color="info" small depressed>2025</v-btn></td>
                      </tr>
                      <tr>
                        <td>Infaq belum Diterima</td>
                        <td class="text-right"><v-btn color="info" small depressed>2025</v-btn></td>
                      </tr>
                      <tr></tr>
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
  <script src="<?= base_url(); ?>api/render/js/dash-admin.js"></script>
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
        title: "DASHBOARD",
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
        this.token = localStorage.getItem('admin-token')
        this.config = {
          headers: {
            Authorization: `Bearer ${this.token}`
          }
        }
        // this.getListInfaq()
        this.getSaldoPerbulan()
      },
      methods: {
        toast(ikon, pesan) {
          Toast.fire({
            icon: ikon,
            title: pesan
          });
        },
        async getListInfaq() {
          await axios.get('<?= base_url(); ?>api/admin/saldo-akhir', this.config)
            .then((res) => {
              console.log(res.data);
              
            })
            .catch((err) => {
              console.log('getlist infq ', err);

            })
        },

        async getSaldoPerbulan() {
          // const tglSekarang = 
          await axios.get('<?= base_url(); ?>api/admin/saldo-perbulan', this.config)
            .then((res) => {
              console.log(res.data);
              
            })
            .catch((err) => {
              console.log('getlist infq ', err);

            })
        },
        loadPembayaran(item) {
          if (item.intBayar >= item.intNominal) {
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
          const param = {
            bayar: this.infaq.nominal_bayar,
            tanggal_bayar: this.infaq.tanggal_bayar
          }
          await axios.put('<?= base_url(); ?>/api/user/pembayaran/' + this.infaq.nomor_pembayaran, param, this.config)
            .then((res) => {
              console.log(res.data);
              this.toast('success', res.data.pesan)
              this.dialogBayar = false
            })
            .catch((err) => {
              if (err.response.status === 402) {
                this.toast('error', err.response.data.messages.error)
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