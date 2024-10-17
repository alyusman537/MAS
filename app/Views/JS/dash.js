Vue.component("nav-bar", {
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
      <v-list-item>
        <v-list-item-avatar size="75">
          <v-img :src="foto"></v-img>
        </v-list-item-avatar>
  
        <v-list-item-content>
        <v-list-item-title class="font-weight-bold">{{ namaUser }} </v-list-item-title>
          <v-list-item-title>{{ kodeUser }}</v-list-item-title>
        </v-list-item-content>
      </v-list-item>
  
      <!--v-divider></v-divider-->
  
      <v-list dense class="mt-5">
  <v-divider></v-divider>
      <v-list-item @click="gotoProfile">
          <v-list-item-content>
            <v-list-item-title>Profile</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
        <v-divider></v-divider>
   
      <v-list-item @click="gotoInfaq">
          <v-list-item-content>
            <v-list-item-title>Data Infaq</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
        <v-divider></v-divider>
  
        <v-list-item @click="gotoUmum">
          <v-list-item-content>
            <v-list-item-title>Infaq Umum</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
        <v-divider></v-divider>
  
        <v-list-item @click="gotoKas">
            <v-list-item-content>
            <v-list-item-title>Laporan Kas</v-list-item-title>
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
  
  data() {
    return {
      // title: "titlenya navbar",
      url: "http://localhost:8080",
      // url: "https://alwafa.alyusman.my.id",
      drawer: null,
      group: null,
      config: null,
      namaUser: null,
      kodeUser: null,
      levelUser: null,
      foto: null,
    };
  },
  created() {
    if(!localStorage.getItem('token')) {
      window.open(this.url+'/login', '_self')
    }
    const token = localStorage.getItem("token");
    const localData = JSON.parse(localStorage.getItem("user"));
    this.namaUser = localData.nama;
    this.kodeUser = localData.nia;
    this.foto = localData.foto;
    // console.log("foto ", this.foto);

    // this.config = {
    //   headers: {
    //     Authorization: `Bearer ${token}`,
    //   },
    // };
    //   this.getProfile()
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
      window.open(this.url + "/login", "_self");
    },
    gotoProfile() {
      window.open(this.url + "/profile", "_self");
    },
    gotoInfaq() {
      window.open(this.url + "/infaq", "_self");
    },
    gotoUmum() {
      window.open(this.url + "/infaq-umum", "_self");
    },
    gotoKas() {
      window.open(this.url + "/laporan-kas", "_self");
    },
  },
});
