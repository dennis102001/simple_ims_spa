import {createRouter, createWebHistory} from 'vue-router'
import Dashboard from "./pages/Dashboard.vue"
import MainLayout from './components/MainLayout.vue'
import Login from './pages/Login.vue'
import Signup from './pages/Signup.vue'
import useUserStore from './store/user.js'
import Items from './pages/Items.vue'
import Stockin from './pages/Stockin.vue'
import Stockout from './pages/Stockout.vue'
import Users from './pages/Users.vue'
import TransactionHistory from './pages/TransactionHistory.vue'
import NotFound from './pages/NotFound.vue'
import Attributes from './pages/Attributes/Attributes.vue'
import CreatePO from './pages/CreatePO.vue'
import ManagePO from './pages/ManagePO.vue'
import CreateSO from './pages/CreateSO.vue'
import ManageSO from './pages/ManageSO.vue'
import Suppliers from './pages/Suppliers.vue'
import Customers from './pages/Customers.vue'
import Settings from './pages/Settings.vue'


const routes = [
    {
        path: '/',
        component: MainLayout,
        children: [
            {
                path: '/',
                name: 'Dashboard',
                component: Dashboard,
                meta: { requiresAuth: true }
            },
            {
                path: '/items',
                name: 'Items',
                component: Items,
                meta: { requiresAuth: true }
            },
            {
                path: '/stock-in',
                name: 'Stockin',
                component: Stockin,
                meta: { requiresAuth: true }
            },
            {
                path: '/stock-out',
                name: 'Stockout',
                component: Stockout,
                meta: { requiresAuth: true }
            },
            {
                path: '/users',
                name: 'Users',
                component: Users,
                meta: { requiresAuth: true, requiresAdmin: true},
            },
            {
                path: '/transaction-history',
                name: 'TransactionHistory',
                component: TransactionHistory,
                meta: { requiresAuth: true }
            },
            {
                path: '/attributes',
                name: 'Attributes',
                component: Attributes,
                meta: { requiresAuth: true }
            },
            {
                path: '/create-purchase-order',
                name: 'CreatePO',
                component: CreatePO,
                meta: { requiresAuth: true }
            },
            {
                path: '/manage-purchase-orders',
                name: 'ManagePO',
                component: ManagePO,
                meta: { requiresAuth: true }
            },
            {
                path: '/sales',
                name: 'CreateSO',
                component: CreateSO,
                meta: { requiresAuth: true }
            },
            {
                path: '/manage-sale-orders',
                name: 'ManageSO',
                component: ManageSO,
                meta: { requiresAuth: true }
            },
                        {
                path: '/suppliers',
                name: 'Suppliers',
                component: Suppliers,
                meta: { requiresAuth: true }
            },
                        {
                path: '/customers',
                name: 'Customers',
                component: Customers,
                meta: { requiresAuth: true }
            },
            {
                path: '/settings',
                name: "Settings",
                component: Settings,
                meta: { requiresAuth: true }
            },
        ],

    },
    {
        path: '/login',
        component: Login,
        name: 'Login'
    },
    {
        path: '/signup',
        component: Signup,
        name: 'Signup'
    },
    {
        path: '/:pathMatch(.*)*',
        name: 'NotFound',
        component: NotFound,
    }
    
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

router.beforeEach(async (to, from, next) => {
    console.log('changed route');

    const userStore = useUserStore()

    // requires auth routes
    if(to.meta.requiresAuth){
        console.log('requires auth true');
        console.log(userStore.user);


        // not auth users
        if(!userStore.user){
            // this does not run but the await userStore... runs
            console.log('!userStore.user');

            try {
                // can't see this log even though it is before the await
                console.log('inside try 1');
                // this really runs, it populates the user object
                await userStore.fetchUser()
                // but not these, i can't see these logs
                console.log('inside try 2');
                // console.log(userStore.user);
                
            } catch {
                return next({ name: 'Login' })
            }
        }
        
        if(to.meta.requiresAdmin && userStore.user.role !== 'admin'){
            return next({ name: 'NotFound' });
        }

    }
    
    next();
    
})

export default router