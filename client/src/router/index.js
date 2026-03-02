
import {createRouter,createWebHistory} from 'vue-router'

import HomeView from '../views/HomeView.vue'
import WeatherView from '../views/WeatherView.vue'
import CatchView from '../views/CatchView.vue'

const routes=[
{path:'/',component:HomeView},
{path:'/weather',component:WeatherView},
{path:'/catches',component:CatchView},
]

export default createRouter({
history:createWebHistory(),
routes
})
