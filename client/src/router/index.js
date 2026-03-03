
import {createRouter,createWebHistory} from 'vue-router'

import HomeView from '../views/HomeView.vue'
import WeatherView from '../views/WeatherView.vue'
import CatchView from '../views/CatchView.vue'
import ProfileView from '../views/ProfileView.vue'
import UsersView from '../views/UsersView.vue'

const onlyAdminGuard = () => {
  const raw = localStorage.getItem('user_data')
  if (!raw) return { path: '/' }

  try {
    const user = JSON.parse(raw)
    if (user?.role === 1) return true
  } catch (error) {
    return { path: '/' }
  }

  return { path: '/' }
}

const routes=[
{path:'/',component:HomeView},
{path:'/weather',component:WeatherView},
{path:'/catches',component:CatchView},
{path:'/profile',component:ProfileView},
{path:'/users',component:UsersView, beforeEnter: onlyAdminGuard},
]

export default createRouter({
history:createWebHistory(),
routes
})
