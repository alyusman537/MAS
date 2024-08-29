Vue.component("admin-nav-bar", {
  template: `
    <div>
  <v-toolbar color="primary" flat dark>
          <v-btn text @click.stop="drawer = !drawer"><v-icon>mdi-menu</v-icon></v-btn>
          <v-toolbar-title>{{title}}</v-toolbar-title>
          <v-spacer></v-spacer>
          <v-toolbar-items>
          <v-btn text @click="keluar" ><v-icon>mdi-exit-to-app</v-icon></v-btn>
          </v-toolbar-items>
        </v-toolbar>
        <v-navigation-drawer
      v-model="drawer"
      absolute
      temporary
      class="px-4"
    >
      <v-list-item class="mt-3">
        <v-list-item-content>
        <v-list-item-title class="font-weight-bold">AL-WAFA BI'AHDILLAH</v-list-item-title>
          <v-list-item-title>ADMINISTRATOR</v-list-item-title>
        </v-list-item-content>
      </v-list-item>
  
      <!--v-divider></v-divider-->
  
      <v-list dense class="mt-3">
  <v-divider></v-divider>
      <v-list-item @click="gotoDashboard">
          <v-list-item-content>
            <v-list-item-title>Dashboard</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
        <v-divider></v-divider>
   
      <v-list-item @click="gotoWilayah">
          <v-list-item-content>
            <v-list-item-title>Master Wilayah</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
        <v-divider></v-divider>
  
        <v-list-item @click="gotoAnggota">
          <v-list-item-content>
            <v-list-item-title>Master Anggota</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
        <v-divider></v-divider>
  
        <v-list-item @click="gotoInfaq">
            <v-list-item-content>
            <v-list-item-title>Master Infaq</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
        <v-divider></v-divider>

        <v-list-item @click="gotoPenerimaanInfaq">
            <v-list-item-content>
            <v-list-item-title>Penerimaan Infaq</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
        <v-divider></v-divider>

        <v-list-item @click="gotoPenerimaanInfaqUmum">
            <v-list-item-content>
            <v-list-item-title>Penerimaan Infaq Umum</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
        <v-divider></v-divider>

        <v-list-item @click="gotoTransaksiKas">
            <v-list-item-content>
            <v-list-item-title>Transaksi Kas</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
        <v-divider></v-divider>

        <v-list-item @click="keluar">
            <v-list-item-content>
            <v-list-item-title>Keluar</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
        <v-divider></v-divider>
  
      </v-list>
  
    </v-navigation-drawer>
    </div>
    `,
  props: ["title"],
  created() {

  },
  data() {
    return {
      // title: "titlenya navbar",
      url: "http://localhost:8080",
      drawer: null,
      group: null,
    };
  },
  methods: {
    async getProfile() {
      await axios
        .get(this.url+"/api/user/profile", this.config)
        .then((res) => {
          this.kodeUser = res.data.nia;
          this.namaUser = res.data.nama;
          this.levelUser = res.data.level;
          this.foto = res.data.foto;
        })
        .catch((err) => {
          console.log('dash ', err.response);
          
          if (err.response.status === 401) {
            localStorage.clear();
            window.open(this.url + "/login", "_self");
          }
        });
    },
    keluar() {
      localStorage.clear();
      window.open(this.url + "/administrator/login", "_self");
    },
    gotoDashboard() {
      window.open(this.url + "/administrator/dashboard", "_self");
    },
    gotoWilayah() {
      window.open(this.url + "/administrator/wilayah", "_self");
    },
    gotoAnggota() {
      window.open(this.url + "/administrator/anggota", "_self");
    },
    gotoInfaq() {
      window.open(this.url + "/administrator/infaq", "_self");
    },
    gotoPenerimaanInfaq() {
      window.open(this.url + "/administrator/penerimaan-infaq", "_self");
    },
    gotoPenerimaanInfaqUmum() {
      window.open(this.url + "/administrator/penerimaan-infaq-umum", "_self");
    },
    gotoTransaksiKas() {
      window.open(this.url + "/administrator/transaksi-kas", "_self");
    },
  },
});
