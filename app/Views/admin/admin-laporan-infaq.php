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
            <v-card-text class="px-2">
              <v-row class="px-3">
                <v-col cols="7">
                  <v-text-field
                    label="Cari Data Infaq"
                    v-model="cari"></v-text-field>
                </v-col>
              </v-row>
              <v-data-table
                :headers="header"
                :items="list"
                :search="cari"
                class="elevation-0">
                <!--status: el.aktif == '1' ? true : false,
                  aktif: el.aktif == '1' ? 'Aktif' : 'Nonaktif',
                  warna: el.aktif == '1' ? 'success' : 'error'-->
                <template v-slot:item.aktif="{ item }">
                  <div v-if="item.status" class="green--text">{{ item.aktif }}</div>
                  <div v-else class="red--text">{{ item.aktif }}</div>
                </template>
                <template v-slot:item.aksi="{ item }">
                  <v-btn
                    color="info"
                    depressed
                    rounded
                    small
                    dark
                    @click="lihatDetail(item.kode)">Lihat Detail</v-btn>
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
        title: "KARTU INFAQ",
        config: null,
        token: null,
        header: [
          {
            text: 'Kode Infaq',
            align: 'start', // sortable: false,
            value: 'kode',
          },
          {
            text: 'Acara',
            value: 'acara'
          },
          {
            text: 'Tgl. Pembayaran max',
            value: 'tanggal_acara'
          },
          {
            text: 'Nominal Infaq',
            value: 'nominal'
          },
          {
            text: 'Sifat Iuran',
            value: 'rutin'
          },
          {
            text: 'Aksi',
            value: 'aksi',
          },
        ],
        list: [],
        cari: null,
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
          await axios.get('<?= base_url(); ?>api/admin/laporan-infaq', this.config)
            .then((res) => {
              console.log('data ', res.data);
              this.refresh()
              this.list = []
              this.list = res.data.map((el) => {
                const dorong = {
                  id: el.id,
                  kode: el.kode,
                  acara: el.acara,
                  keterangan: el.keterangan,
                  tanggal_acara: el.tanggal_acara,
                  nominal: parseInt(el.nominal).toLocaleString('ID-id'),
                  rutin: el.rutin == '1' ? 'Rutin' : 'Insidentil',
                  status: el.aktif == '1' ? true : false,
                  aktif: el.aktif == '1' ? 'Aktif' : 'Nonaktif',
                  warna: el.aktif == '1' ? 'success' : 'error'
                }
                return dorong
              })
            })
            .catch((err) => {
              if (err.response.status === 401) {
                this.keluar()
              }
              console.log('getlist infq ', err.response);
            })
        },
        lihatDetail(kode_infaq) {
          window.open('<?= base_url(); ?>administrator/laporan-infaq-detail/'+kode_infaq, '_self')
        },
      }
    })
  </script>
</body>

</html>