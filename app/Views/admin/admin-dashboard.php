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
                <p>Saldo Kas Bulan Kemarin</p>  
                <p> Pemasukan Bulan Ini</p>
                  <p>Pengeluaran Bulan Ini</p>
                  <p>Saldo Kas Akhir</p>
                </v-col>
                <v-col cols="4">
                <p class="font-weight-bold text-right">{{ saldo_kas_bulan_kemarin }}</p>  
                <p class="font-weight-bold text-right">{{ pemasukan_bulan_ini }}</p>
                  <p class="font-weight-bold text-right">{{ pengeluaran_bulan_ini }}</p>
                  <p class="font-weight-bold text-right">{{ saldo_akhir_bulan_ini }}</p>
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
                          <v-btn color="info" small depressed rounded width="75">{{ anggota_aktif }}</v-btn>
                        </td>
                      </tr>
                      <tr>
                        <td>Tagihan infaq belum terbayar</td>
                        <td class="text-right">
                          <v-btn color="info" small depressed rounded width="75">{{ infaq_belum_terbayar }}</v-btn>
                        </td>
                      </tr>
                      <tr>
                        <td>Tagihan infaq belum Diterima</td>
                        <td class="text-right">
                          <v-btn color="info" small depressed rounded width="75">{{ infaq_belum_diterima}}</v-btn>
                        </td>
                      </tr>
                      <tr>
                        <td>Infaq Umum belum Diterima</td>
                        <td class="text-right">
                          <v-btn color="info" small depressed rounded width="75">{{ umum_belum_diterima }}</v-btn>
                        </td>
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
        saldo_kas_bulan_kemarin: 0,
        pemasukan_bulan_ini: 0,
        pengeluaran_bulan_ini: 0,
        saldo_akhir_bulan_ini: 0,
        anggota_aktif: 0,
        infaq_belum_terbayar: 0,
        infaq_belum_diterima: 0,
        umum_belum_diterima: 0
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
        this.getHome()
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
          window.open('<?= base_url(); ?>administrator/login', '_self')
        },
        async refresh() {
          await axios.get('<?= base_url() ?>api/admin-refresh', this.config)
            .then((res) => {
              localStorage.setItem('admin-token', res.data.new_token)
            })
            .catch()
        },
        async getHome() {
          // const tglSekarang = 
          await axios.get('<?= base_url(); ?>api/admin/home', this.config)
            .then((res) => {
              this.refresh()
              this.saldo_kas_bulan_kemarin = parseInt(res.data.saldo.saldo_bulan_lalu).toLocaleString('ID-id')
              this.pemasukan_bulan_ini = parseInt(res.data.saldo.pemasukan_bulan_ini).toLocaleString('ID-id')
              this.pengeluaran_bulan_ini = parseInt(res.data.saldo.pengeluaran_bulan_ini).toLocaleString('ID-id')
              this.saldo_akhir_bulan_ini = parseInt(res.data.saldo.saldo_akhir_bulan_ini).toLocaleString('ID-id')

              this.anggota_aktif = parseInt(res.data.anggota).toLocaleString('ID-id')
        this.infaq_belum_terbayar = parseInt(res.data.belum).toLocaleString('ID-id')
        this.infaq_belum_diterima = parseInt(res.data.pending).toLocaleString('ID-id')
        this.umum_belum_diterima = parseInt(res.data.umum).toLocaleString('ID-id')
              
            })
            .catch((err) => {
              if(err.response.status == 401 ){
                this.keluar()
              }
              console.log('getlist infq ', err);

            })
        },
        async getAnggotaAktif() {
          // const tglSekarang = 
          await axios.get('<?= base_url(); ?>api/admin/anggota-jumlah', this.config)
            .then((res) => {
              console.log(res.data);
              this.anggota_aktif = parseInt(res.data.jumlah_anggota_aktif).toLocaleString('ID-id')
              
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