import { createRouter, createWebHistory } from "vue-router";


import loginForm from  '../components/login.vue'
import dashboard from '../components/dashboard.vue'

import reminderIndex from '../components/reminder/index.vue'
import reminderNew from '../components/reminder/new.vue'
import reminderEdit from '../components/reminder/edit.vue'

import item_listIndex from '../components/item_list/index.vue'
import item_listNew from '../components/item_list/new.vue'
import item_listEdit from '../components/item_list/edit.vue'

import categoryIndex from '../components/category/index.vue'
import categoryNew from '../components/category/new.vue'
import categoryEdit from '../components/category/edit.vue'

import subCategoryEdit from '../components/sub_category/edit.vue'

import groupIndex from '../components/group/index.vue'
import groupNew from '../components/group/new.vue'
import groupEdit from '../components/group/edit.vue'

import warehouseIndex from '../components/warehouse/index.vue'
import warehouseNew from '../components/warehouse/new.vue'
import warehouseEdit from '../components/warehouse/edit.vue'

import rackIndex from '../components/rack/index.vue'
import rackNew from '../components/rack/new.vue'
import rackEdit from '../components/rack/edit.vue'

import uomIndex from '../components/uom/index.vue'
import uomNew from '../components/uom/new.vue'
import uomEdit from '../components/uom/edit.vue'

import supplierIndex from '../components/supplier/index.vue'
import supplierNew from '../components/supplier/new.vue'
import supplierEdit from '../components/supplier/edit.vue'

import departmentIndex from '../components/department/index.vue'
import departmentNew from '../components/department/new.vue'
import departmentEdit from '../components/department/edit.vue'

import purposeIndex from '../components/purpose/index.vue'
import purposeNew from '../components/purpose/new.vue'
import purposeEdit from '../components/purpose/edit.vue'

import employeesIndex from '../components/employees/index.vue'
import employeesNew from '../components/employees/new.vue'
import employeesEdit from '../components/employees/edit.vue'

import enduseIndex from '../components/enduse/index.vue'
import enduseNew from '../components/enduse/new.vue'
import enduseEdit from '../components/enduse/edit.vue'

import locationIndex from '../components/location/index.vue'
import locationNew from '../components/location/new.vue'
import locationEdit from '../components/location/edit.vue'

import item_statusIndex from '../components/item_status/index.vue'
import item_statusNew from '../components/item_status/new.vue'
import item_statusEdit from '../components/item_status/edit.vue'

import restock_reasonIndex from '../components/restock_reason/index.vue'
import restock_reasonNew from '../components/restock_reason/new.vue'
import restock_reasonEdit from '../components/restock_reason/edit.vue'

import change_password from '../components/change_password/index.vue'

import receiveIndex from '../components/receive/index.vue'
import receiveNew from '../components/receive/new.vue'
import receiveSecond from '../components/receive/new_second.vue'
import receiveThird from '../components/receive/new_third.vue'
import receiveOverride from '../components/receive/override.vue'
import receiveEdit from '../components/receive/edit.vue'
import receivePrint from '../components/receive/print.vue'
import receivePrintDraft from '../components/receive/print_draft.vue'

import receive_draftIndex from '../components/receive_draft/index.vue'

import userAcceptanceIndex from '../components/user_acceptance/index.vue'
import userAcceptanceAccepted from '../components/user_acceptance/accepted.vue'
import userAcceptanceRejected from '../components/user_acceptance/rejected.vue'
import userAcceptanceView from '../components/user_acceptance/view.vue'
import userAcceptancePrint from '../components/user_acceptance/print.vue'
import userAcceptanceBackorder from '../components/user_acceptance/backorder_index.vue'
import userAcceptanceBackorderCompleted from '../components/user_acceptance/backorder_completed.vue'
import userAcceptanceBackorderView from '../components/user_acceptance/backorder_view.vue'
import userAcceptanceBackorderPrint from '../components/user_acceptance/backorder_print.vue'


import requestIndex from '../components/request/index.vue'
import requestNew from '../components/request/new.vue'
import requestNewSecond from '../components/request/new_second.vue'
import requestNewSecondWH from '../components/request/new_second_wh.vue'
import requestShow from '../components/request/show.vue'
import requestEdit from '../components/request/edit.vue'
import requestPrint from '../components/request/print.vue'

import salesIndex from '../components/sales/index.vue'
import salesNew from '../components/sales/new.vue'
import salesNewSecond from '../components/sales/new_second.vue'
import salesShow from '../components/sales/show.vue'
import salesEdit from '../components/sales/edit.vue'
import salesPrint from '../components/sales/print.vue'

import issueIndex from '../components/issue/index.vue'
import issueNew from '../components/issue/new.vue'
import issueShow from '../components/issue/show.vue'
import issuePrint from '../components/issue/print.vue'
import issueGatepass from '../components/issue/gatepass.vue'

import restockIndex from '../components/restock/index.vue'
import restockNew from '../components/restock/new.vue'
import restockNewSecond from '../components/restock/new_second.vue'
import restockNewSecondwhs from '../components/restock/new_second_whs.vue'
import restockShow from '../components/restock/show.vue'
import restockPrint from '../components/restock/print.vue'

import borrowIndex from '../components/borrow/index.vue'
import borrowNew from '../components/borrow/new.vue'
import borrowNewSecond from '../components/borrow/new_second.vue'
import borrowEdit from '../components/borrow/edit.vue'
import borrowShow from '../components/borrow/show.vue'
import borrowPrint from '../components/borrow/print.vue'

import back_orderIndex from '../components/back_order/index.vue'
import back_orderNew from '../components/back_order/new.vue'
import back_orderEdit from '../components/back_order/edit.vue'
import back_orderShow from '../components/back_order/show.vue'
import back_orderPrint from '../components/back_order/print.vue'

import gatepassIndex from '../components/gatepass/index.vue'
import gatepassShow from '../components/gatepass/show.vue'
import gatepassNew from '../components/gatepass/new.vue'
import gatepassNewSecond from '../components/gatepass/new_second.vue'
import gatepassNewSecondTry from '../components/gatepass/new_second_try.vue'
import gatepassOverall from '../components/gatepass/overall.vue'
import gatepassIncomplete from '../components/gatepass/incomplete.vue'
import gatepassCompleted from '../components/gatepass/completed.vue'
import gatepassPrint from '../components/gatepass/print.vue'


import pr_overall from '../components/reports/pr_overall.vue'
import variants from '../components/reports/variants.vue'
import pr_variants from '../components/reports/pr_variants.vue'
import stockcard from '../components/reports/stockcard.vue'
import tr_receive from '../components/reports/tr_receive.vue'
import tr_issued from '../components/reports/tr_issued.vue'
import tr_restock from '../components/reports/tr_restock.vue'
import tr_borrow from '../components/reports/tr_borrow.vue'
import tr_backorder from '../components/reports/tr_backorder.vue'

import importItemsIndex from '../components/import_items/index.vue'
import notFound from '../components/notFound.vue'

const routes = [
    {
        path:'/',
        name:'login',
        component: loginForm,
        meta:{
            requiresAuth:false
        }
    },
    {
        path:'/dashboard',
        name: 'dashboard',
        component: dashboard,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/reminder',
        component: reminderIndex,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/reminder/new',
        component: reminderNew,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/reminder/edit/:id',
        component: reminderEdit,
        props:true,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/item_list',
        component: item_listIndex,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/item_list/new',
        component: item_listNew,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/item_list/edit/:id',
        component: item_listEdit,
        props:true,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/category',
        component: categoryIndex,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/category/new',
        component: categoryNew,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/category/edit/:id',
        component: categoryEdit,
        props:true,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/sub_category/edit/:id',
        component: subCategoryEdit,
        props:true,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/group',
        component: groupIndex,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/group/new',
        component: groupNew,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/group/edit/:id',
        component: groupEdit,
        props:true,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/warehouse',
        component: warehouseIndex,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/warehouse/new',
        component: warehouseNew,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/warehouse/edit/:id',
        component: warehouseEdit,
        props:true,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/rack',
        component: rackIndex,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/rack/new',
        component: rackNew,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/rack/edit/:id',
        component: rackEdit,
        props:true,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/uom',
        component: uomIndex,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/uom/new',
        component: uomNew,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/uom/edit/:id',
        component: uomEdit,
        props:true,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/supplier',
        component: supplierIndex,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/supplier/new',
        component: supplierNew,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/supplier/edit/:id',
        component: supplierEdit,
        props:true,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/department',
        component: departmentIndex,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/department/new',
        component: departmentNew,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/department/edit/:id',
        component: departmentEdit,
        props:true,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/purpose',
        component: purposeIndex,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/purpose/new',
        component: purposeNew,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/purpose/edit/:id',
        component: purposeEdit,
        props:true,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/employees',
        component: employeesIndex,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/employees/new',
        component: employeesNew,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/employees/edit/:id',
        component: employeesEdit,
        props:true,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/enduse',
        component: enduseIndex,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/enduse/new',
        component: enduseNew,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/enduse/edit/:id',
        component: enduseEdit,
        props:true,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/location',
        component: locationIndex,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/location/new',
        component: locationNew,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/location/edit/:id',
        component: locationEdit,
        props:true,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/item_status',
        component: item_statusIndex,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/item_status/new',
        component: item_statusNew,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/item_status/edit/:id',
        component: item_statusEdit,
        props:true,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/restock_reason',
        component: restock_reasonIndex,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/restock_reason/new',
        component: restock_reasonNew,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/restock_reason/edit/:id',
        component: restock_reasonEdit,
        props:true,
        meta:{
            requiresAuth:true
        }
    },

    {
        path:'/change_password',
        component: change_password,
        meta:{
            requiresAuth:true
        }
    },
    
    {
        path:'/receive',
        component: receiveIndex,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/receive/new',
        component: receiveNew,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/receive/new_second/:id/:detail_id',
        component: receiveSecond,
        props:true,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/receive/new_third/:id',
        component: receiveThird,
        props:true,
        meta:{
            requiresAuth:true
        }
    },

    {
        path:'/receive/override/:id/:overrideid',
        component: receiveOverride,
        props:true,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/receive/edit',
        component: receiveEdit,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/receive/print/:id',
        component: receivePrint,
        props:true,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/receive/print_draft/:id',
        component: receivePrintDraft,
        props:true,
        meta:{
            requiresAuth:true
        }
    },

    {
        path:'/receive_draft',
        component: receive_draftIndex,
        meta:{
            requiresAuth:true
        }
    },

    {
        path:'/user_acceptance/pending',
        component: userAcceptanceIndex,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/user_acceptance/accepted',
        component: userAcceptanceAccepted,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/user_acceptance/rejected',
        component: userAcceptanceRejected,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/user_acceptance/view/:id',
        component: userAcceptanceView,
        props:true,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/user_acceptance/print/:id',
        component: userAcceptancePrint,
        props:true,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/user_acceptance/backorder_index',
        component: userAcceptanceBackorder,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/user_acceptance/backorder_completed',
        component: userAcceptanceBackorderCompleted,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/user_acceptance/backorder_view/:id',
        component: userAcceptanceBackorderView,
        props:true,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/user_acceptance/backorder_print/:id',
        component: userAcceptanceBackorderPrint,
        props:true,
        meta:{
            requiresAuth:true
        }
    },

    {
        path:'/request',
        component: requestIndex,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/request/new',
        component: requestNew,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/request/new_second/:id',
        component: requestNewSecond,
        props:true

    },
    {
        path:'/request/new_second_wh/:id',
        component: requestNewSecondWH,
        props:true

    },
    {
        path:'/request/show/:id',
        component: requestShow,
        props:true,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/request/edit/:id',
        component: requestEdit,
        props:true,
        meta:{
            requiresAuth:true
        }
    },

    {
        path:'/request/print/:id',
        component: requestPrint,
        props:true,
        meta:{
            requiresAuth:true
        }
    },



    {
        path:'/sales',
        component: salesIndex,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/sales/new',
        component: salesNew,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/sales/new_second/',
        component: salesNewSecond,

    },
    {
        path:'/sales/show/:id',
        component: salesShow,
        props:true,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/sales/edit/:id',
        component: salesEdit,
        props:true,
        meta:{
            requiresAuth:true
        }
    },

    {
        path:'/sales/print/',
        component: salesPrint,
        props:true,
        meta:{
            requiresAuth:true
        }
    },

    {
        path:'/issue',
        component: issueIndex
    },
    {
        path:'/issue/new',
        component: issueNew
    },
    {
        path:'/issue/show/:id',
        props:true,
        component: issueShow,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/issue/print/:id',
        props:true,
        component: issuePrint,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/issue/gatepass/:id',
        props:true,
        component: issueGatepass
    },
    {
        path:'/restock',
        component: restockIndex
    },
    {
        path:'/restock/new',
        component: restockNew
    },
    {
        path:'/restock/new_second/:id/:source_pr',
        component: restockNewSecond,
        props:true,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/restock/new_second_whs/:id/:source_pr',
        component: restockNewSecondwhs,
        props:true,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/restock/show/:id',
        component: restockShow,
        props:true,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/restock/print/:id',
        component: restockPrint,
        props:true,
        meta:{
            requiresAuth:true
        }
    },

    {
        path:'/borrow',
        component: borrowIndex
    },
    {
        path:'/borrow/new',
        component: borrowNew
    },
    {
        path:'/borrow/new_second',
        component: borrowNewSecond
    },
    {
        path:'/borrow/edit',
        component: borrowEdit
    },

    {
        path:'/borrow/show/:id',
        component: borrowShow,
        props:true,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/borrow/print/:id',
        component: borrowPrint,
        props:true,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/back_order',
        component: back_orderIndex
    },
    {
        path:'/back_order/new/:id',
        component: back_orderNew,
        props:true,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/back_order/edit',
        component: back_orderEdit
    },
    {
        path:'/back_order/show/:id',
        component: back_orderShow,
        props:true,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/back_order/print/:id',
        component: back_orderPrint,
        props:true,
        meta:{
            requiresAuth:true
        }
    },

    {
        path:'/gatepass',
        component: gatepassIndex,
        props:true,
        meta:{
            requiresAuth:true
        }
    },

    {
        path:'/gatepass/show/:id',
        component: gatepassShow,
        props:true,
        meta:{
            requiresAuth:true
        }
    },

    {
        path:'/gatepass/new',
        component: gatepassNew,
        props:true,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/gatepass/new_second/:id',
        component: gatepassNewSecond,
        props:true,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/gatepass/new_second_try/:id',
        component: gatepassNewSecondTry,
        props:true,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/gatepass/overall',
        component: gatepassOverall,
        props:true,
        meta:{
            requiresAuth:true
        }
    },

    {
        path:'/gatepass/incomplete',
        component: gatepassIncomplete,
        props:true,
        meta:{
            requiresAuth:true
        }
    },

    {
        path:'/gatepass/completed',
        component: gatepassCompleted,
        props:true,
        meta:{
            requiresAuth:true
        }
    },

    {
        path:'/gatepass/print/:id',
        component: gatepassPrint,
        props:true,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/reports/pr_overall',
        component: pr_overall,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/reports/variants',
        component: variants,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/reports/pr_variants',
        component: pr_variants,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/reports/stockcard_item/:variant_id/:item_id/:supplier_id/:catalog_no/:brand/:department_id',
        component: stockcard,
        props:true,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/reports/stockcard',
        component: stockcard,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/reports/tr_receive',
        component: tr_receive,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/reports/tr_issued',
        component: tr_issued,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/reports/tr_restock',
        component: tr_restock,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/reports/tr_borrow',
        component: tr_borrow,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/reports/tr_backorder',
        component: tr_backorder,
        meta:{
            requiresAuth:true
        }
    },
    {
        path:'/import_items',
        component: importItemsIndex,
        meta:{
            requiresAuth:true
        }
    },
    
    // {
    //     path:'/invoice/show/:id',
    //     component: invoiceShow,
    //     props:true
    // },
    // {
    //     path:'/invoice/edit/:id',
    //     component: invoiceEdit,
    //     props:true
    // },
    {
        path:'/:pathMatch(.*)*',
        name:'notFound',
        component: notFound,
        meta:{
            requiresAuth:false
        }
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

router.beforeEach((to,from) => {
    if(to.meta.requiresAuth && !localStorage.getItem('token') ){
        return { name: 'login'}
    } 
})

// alert(localStorage.getItem("userToken"))
// axios.interceptors.response.use(function (response) {
//     // Any status code that lie within the range of 2xx cause this function to trigger
//     // Do something with response data
//     return response;
//     }, function (error) {
//     // Any status codes that falls outside the range of 2xx cause this function to trigger
//     // Do something with response error
//     if(error.response.status === 403) {
//         // redirect to login page
//         window.location.href = "/login";
//     }
//     return Promise.reject(error);
//   });


// router.beforeEach((to, from, next) => {
//     const requiresAuth = to.matched.some(x => x.meta.requiresAuth);
  
//     if (to.meta.requiresAuth && !localStorage.getItem('token')) {
//         return { name: 'login'}
//     } else if (to.meta.requiresAuth && localStorage.getItem('token')) {
//       next();
//     } else {
//       next();
//     }
//   });

export default router