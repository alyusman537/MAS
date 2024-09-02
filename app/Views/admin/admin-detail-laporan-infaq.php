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
              <v-row class="px-3">
                <v-col cols="5">
                  <v-text-field
                    label="Cari Data Infaq"
                    v-model="cari"></v-text-field>
                </v-col>
                <v-col cols="4">
                  <v-btn class="mt-3" color="info" depressed block @click="lihatKartu()"><v-icon>mdi-file-pdf-box</v-icon>export</v-btn>
                </v-col>
                <v-col cols="3">
                  <v-btn class="mt-3" color="info" depressed block @click="balik()"><v-icon>mdi-close</v-icon></v-btn>
                </v-col>
                <v-col cols="12">
                  <!-- this.infaq.kode = da.kode
              this.infaq.acara = da.acara
              this.infaq.keterangan = da.keterangan
              this.infaq.tanggal_acara = da.tanggal_acara
              this.infaq.nominal = parseInt(da.nominal).toLocaleString('ID-id')
              this.infaq.rutin = da.rutin == '1' ? 'Rutin' : 'Insidential'
              this.infaq.aktif = da.aktif == '1' ? 'Aktif' : 'Nonaktif' -->
              <v-divider></v-divider>
              <v-row>
                <v-col cols="5">
                  Kode Infaq
                </v-col>
                <v-col cols="7">
                  <div class="text-right">{{infaq.kode}}</div>
                </v-col>
              </v-row>
              <v-divider></v-divider>

              <v-row>
                <v-col cols="5">
                  Tujuan Infaq
                </v-col>
                <v-col cols="7">
                  <div class="text-right">{{infaq.acara}}</div>
                </v-col>
              </v-row>
              <v-divider></v-divider>

              <v-row>
                <v-col cols="5">
                  Keterangan
                </v-col>
                <v-col cols="7">
                  <div class="text-right">{{infaq.keterangan}}</div>
                </v-col>
              </v-row>
              <v-divider></v-divider>

              <v-row>
                <v-col cols="5">
                  Nominal
                </v-col>
                <v-col cols="7">
                  <div class="text-right">{{infaq.nominal}}</div>
                </v-col>
              </v-row>
              <v-divider></v-divider>

              <v-row>
                <v-col cols="5">
                  Sifat Infaq
                </v-col>
                <v-col cols="7">
                  <div class="text-right">{{infaq.rutin}}</div>
                </v-col>
              </v-row>
              <v-divider></v-divider>
              <br>

                </v-col>
              </v-row>
              <v-data-table
                :headers="header"
                :items="list"
                :search="cari"
                class="elevation-0">
                <template v-slot:item.is_lunas="{ item }">
                  <v-chip :color="item.warna" small>{{item.is_lunas}}</v-chip>
                </template>
                
              </v-data-table>
            </v-card-text>
          </v-card>

        </v-container>
      </v-main>
    </v-app>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/vue@2.x/dist/vue.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  <script src="<?= base_url(); ?>api/render/js/dash-admin.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    const swal = (judul, pesan, ikon) => {
      Swal.fire({
        title: judul,
        text: pesan,
        icon: ikon
      });
    }
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
        title: "DETAIL KARTU INFAQ",
        config: null,
        token: null,
        header: [{
            text: 'ID Anggota',
            align: 'start', // sortable: false,
            value: 'nia',
          },
          {
            text: 'Nama',
            value: 'nama'
          },
          {
            text: 'Wilayah',
            value: 'wilayah'
          },
          {
            text: 'Nom. Bayar',
            value: 'bayar'
          },
          {
            text: 'Tgl. Bayar',
            value: 'tanggal_bayar'
          },
          {
            text: 'Penerima',
            value: 'validator',
          },
          {
            text: 'Tgl. Penerimaan',
            value: 'tanggal_validasi',
          },
          {
            text: 'Status',
            value: 'is_lunas',
          },
        ],
        list: [],
        cari: null,
        kode_infaq: null,
        infaq: {
          id: null,
          kode: null,
          acara: null,
          keterangan: null,
          tanggal_acara: null,
          nominal: null,
          rutin: null,
          aktif: null,
        },
      },
      watch: {},
      created() {
        this.token = localStorage.getItem('admin-token')
        this.config = {
          headers: {
            Authorization: `Bearer ${this.token}`
          }
        }
        const pageURL = window.location.href;
        this.kode_infaq = pageURL.substr(pageURL.lastIndexOf('/') + 1);
        console.log('las segm ', this.kode_infaq);

        this.getList()
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
        async getList() {
          await axios.get('<?= base_url(); ?>api/admin/laporan-infaq-detail/' + this.kode_infaq, this.config)
            .then((res) => {
              console.log('data ', res.data);
              this.refresh()
              this.list = []
              this.list = res.data.data_bayar.map((el) => {
                const dorong = {
                  nia: el.nia,
                  nama: el.nama,
                  wilayah: el.wilayah,
                  bayar: parseInt(el.bayar).toLocaleString('ID-id'),
                  tanggal_bayar: el.tanggal_bayar,
                  validator: el.validator,
                  tanggal_validasi: el.tanggal_validasi,
                  is_lunas: el.is_lunas,
                  warna: el.is_lunas == 'Lunas' ? 'success' : (el.is_lunas == 'Pending' ? 'warning' : 'error')
                }
                return dorong
              })

              const da = res.data.infaq
              this.infaq.kode = da.kode
              this.infaq.acara = da.acara
              this.infaq.keterangan = da.keterangan
              this.infaq.tanggal_acara = da.tanggal_acara
              this.infaq.nominal = parseInt(da.nominal).toLocaleString('ID-id')
              this.infaq.rutin = da.rutin == '1' ? 'Rutin' : 'Insidential'
              this.infaq.aktif = da.aktif == '1' ? 'Aktif' : 'Nonaktif'

              console.log('infaq ', this.infaq);
              
            })
            .catch((err) => {
              if (err.response.status === 401) {
                this.keluar()
              }
              console.log('getlist infq ', err.response);
            })
        },
        async lihatDetail(kode_infaq) {
          await axios.get('<?= base_url(); ?>api/admin/laporan-infaq-bayar/' + kode_infaq, this.config)
            .then((res) => {
              console.log('data bayar', res.data);
              this.refresh()
              // this.infaq.id 
            })
            .catch((err) => {
              if (err.response.status === 401) {
                this.keluar()
              }
              console.log('getlist infq ', err.response);
            })
        },
        lihatKartu() {
          window.open('<?= base_url();?>api/pdf/kartu-infaq/'+this.kode_infaq, '_blank')
        },
        balik() {
          window.open('<?= base_url();?>administrator/laporan-infaq', '_self')
        },

      }
    })
  </script>
</body>

</html>