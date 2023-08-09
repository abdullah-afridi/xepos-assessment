
import { createRouter, createWebHistory } from 'vue-router'

const routes = [
  {
    path: '/',
    name:"login",
    component: () => import('@/views/Home'),
   
  },
  {
    path: '/dashboard',
    name:"dashboard",
    component: () => import('@/views/Dashboard'),
   
  },
  {
    path: '/employees',
    name:"employees",
    component: () => import('@/views/Employees'),
   
  },
  {
    path: '/company',
    name:"company",
    component: () => import('@/views/Company'),
   
  },
]


const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes,
})

export default router
