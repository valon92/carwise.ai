import { createApp } from 'vue'
import { createRouter, createWebHistory } from 'vue-router'
import App from './App.vue'
import './assets/main.css'

// Import views
import Home from './views/Home.vue'
import Diagnose from './views/Diagnose.vue'
import MyCars from './views/MyCars.vue'
import Mechanics from './views/Mechanics.vue'
import Login from './views/Login.vue'
import Register from './views/Register.vue'

// Import components
import Navbar from './components/Navbar.vue'

// Router configuration
const routes = [
  { path: '/', name: 'Home', component: Home },
  { path: '/diagnose', name: 'Diagnose', component: Diagnose },
  { path: '/my-cars', name: 'MyCars', component: MyCars },
  { path: '/mechanics', name: 'Mechanics', component: Mechanics },
  { path: '/login', name: 'Login', component: Login },
  { path: '/register', name: 'Register', component: Register },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

const app = createApp(App)

app.use(router)
app.component('Navbar', Navbar)

app.mount('#app')