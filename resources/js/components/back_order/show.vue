<script setup>
import navigation from '@/layouts/navigation.vue';
import { PencilSquareIcon, TrashIcon, CheckCircleIcon, MagnifyingGlassIcon, ChevronLeftIcon, ChevronRightIcon, ArrowUturnLeftIcon } from '@heroicons/vue/24/solid'
import axios from 'axios';
import {onMounted, ref} from "vue";
import { useRouter } from "vue-router";

	onMounted(async () =>{
        getBackorderData()
    })


const router = useRouter()

let BOHead = ref({
	id:''
})

let details = ref([])

const props = defineProps({
        id:{
            type:String,
            default:''
        }
    })

	const getBackorderData = async () => {
		let response = await axios.get(`/api/get_backorder_data/${props.id}`)
		BOHead.value = response.data.BOHead
		details.value = response.data.BODetails
	}

	const onPrint= (id) => {
	router.push('/back_order/print/'+id)
}
</script>

<template>
    <navigation>
        <div class="container-fluid">
			<!-- BreadCrumb -->
			<div class="card mb-3">	
				<div class="flex justify-between content-center">
					<div class="flex justify-start space-x-3 ">
						<div class="">
							<a href="/back_order" class="btn btn-secondary btn-xs btn-rounded">
								<ArrowUturnLeftIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></ArrowUturnLeftIcon>
							</a>
						</div>
						<div>
							<h6 class="m-0 pt-1 font-bold uppercase">Back Order</h6>
						</div>
					</div>	
					<div class="pt-1">	
						<nav aria-label="breadcrumb" role="navigation">
							<ol class="breadcrumb adminx-page-breadcrumb">
								<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
								<li class="breadcrumb-item"><a href="/back_order">Back Order</a></li>
								<li class="breadcrumb-item active" aria-current="page">Add New Back Order</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>	

			<div class="row">
				<div class="col-md-6 col-lg-12">
					<div class="card card-main-bg" >
						<div class="pt-3 px-2">
							<table class="w-full table-bordersed mt-2 mb-2 ">
								<tr>
									<td class="pr-1" width="20%">
										<div class="flex justify-start">
											<span class="text-lg uppercase font-bold text-gray-600 w-full leading-none">{{ BOHead.mrecf_no }}
											</span>
										</div>
										<div class="flex justify-start ">
											<span class="text-xs text-gray-500 leading-none pt-1">MRIF NO.</span>
										</div>
									</td>
									<td class="px-1" width="20%">
										<div class="flex justify-start">
											<span class="text-lg uppercase font-bold text-gray-600 leading-none">{{ BOHead.backorder_date }}</span>
										</div>
										<div class="flex justify-start" >
											<span class="text-xs text-gray-500 leading-none pt-1">DATE</span>
										</div>
									</td>
									
									<td width="8%"></td>
									<td class="px-1  "  width="15%">
										<div class="flex justify-start">
											<span class="text-md uppercase font-bold text-gray-600 leading-none">{{ BOHead.dr_no }}</span>
										</div>
										<div class="flex justify-start">
											<span class="text-xs text-gray-500 leading-none pt-1">DR NUMBER</span>
										</div>
									</td>
									<td class="px-1  " width="15%">
										<div class="flex justify-start">
											<span class="text-md uppercase font-bold text-gray-600 leading-none">{{ BOHead.po_no }}</span>
										</div>
										<div class="flex justify-start" >
											<span class="text-xs text-gray-500 leading-none pt-1">PO NUMBER</span>
										</div>
									</td>
									<td class="px-1  " width="15%">
										<div class="flex justify-start">
											<span class="text-md uppercase font-bold text-gray-600 leading-none">{{ BOHead.si_or }}</span>
										</div>
										<div class="flex justify-start" >
											<span class="text-xs text-gray-500 leading-none pt-1">SI/OR NUMBER</span>
										</div>
									</td>
									<td class="px-1  " width="2%">
										<span class="flex justify-center text-green-600">
											<CheckCircleIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" v-if="BOHead.pcf === 1"></CheckCircleIcon>
											<span v-else></span>
										</span>
										<div class="flex justify-center ">
											<span class="text-xs text-gray-500 leading-none pt-1">PCF</span>
										</div>
										
									</td>
									<td class="p-0 w-0" width="5%">
									</td>
								</tr>
							</table>
						</div>
						<div class="border-2 m-2 border-gray-400 rounded-lg pb-1" v-for="(de, x) in details">
							<div class="">
								<table class="w-full">
									<tr>
										<td width="2%" rowspan="4" class="align-top p-0 "> 
											<div class="pt-2 p-1 mr-2 px-2 text-md text-center bg-gray-400 font-bold pb-5 text-white">{{ x + 1 }}</div>
										</td>
										<td class="pt-2 form-label" width="8%">PR Number</td>
										<td class="pt-2 px-1 text-sm border-b font-bold">{{ de.pr_no }}</td>
										<td class="pt-2 px-1 text-sm" width="3%"></td>
										<td class="pt-2 form-label" width="9%">Inspected by</td>
										<td class="pt-2 px-1 text-sm border-b"></td>
									</tr>
									<tr>
										<td class="form-label" >Department</td>
										<td class="px-1 text-sm border-b">{{ de.department_name}}</td>
										<td class="px-1 text-sm" width="3%"></td>
										<td class="form-label">Enduse</td>
										<td class="px-1 text-sm border-b" colspan="2">{{ de.enduse_name }}</td>
									</tr>
									<tr>
										<td class="form-label">Purpose</td>
										<td class="px-1 text-sm border-b " colspan="5">{{ de.purpose_name }}</td>
									</tr>
								</table>
								<table class="table table-bordered m-0">
									<thead>
										<tr>
											<th class="font-xxs" width="4%">BO Qty</th>
											<th class="font-xxs" width="4%">Shipping Cost</th>
											<th class="font-xxs" width="4%">Unit Cost</th>
											<th class="font-xxs" width="4%">Currency</th>
											<th class="font-xxs" width="4%">Total Cost</th>
											<th class="font-xxs" width="12%">Item Decription</th>
											<th class="font-xxs" width="12%">Supplier</th>
											<th class="font-xxs" width="5%">Uom</th>
											<th class="font-xxs" width="5%">Cat. No</th>
											<th class="font-xxs" width="5%">Brand</th>
											<th class="font-xxs" width="5%">Serial No</th>
											<th class="font-xxs" width="5%">Color</th>
											<th class="font-xxs" width="5%">Size</th>
											<th class="font-xxs" width="5%">Item Status</th>
											<th class="font-xxs" width="5%">Expiry Date</th>
											<th class="font-xxs" width="20%">Remarks</th>
										</tr>
									</thead>
									<tbody>
										<tr v-for="(it, c) in de.receive_items.items">
											<td class="text-xs">{{ it.bo_quantity }}</td>
											<td class="text-xs">{{ it.shipping_cost }}</td>
											<td class="text-xs">{{ it.unit_cost }}</td>
											<td class="text-xs">{{ it.currency }}</td>
											<td class="text-xs">{{ it.bo_quantity *  (parseFloat(it.unit_cost) + parseFloat(it.shipping_cost))}}</td>
											<td class="text-xs">{{ it.item_description }}</td>
											<td class="text-xs">{{ it.supplier_name }}</td>
											<td class="text-xs">{{ it.uom }}</td>
											<td class="text-xs">{{ it.catalog_no }}</td>
											<td class="text-xs">{{ it.brand }}</td>
											<td class="text-xs">{{ it.serial_no }}</td>
											<td class="text-xs">{{ it.color }}</td>
											<td class="text-xs">{{ it.size }}</td>
											<td class="text-xs">{{ it.item_status }}</td>
											<td class="text-xs">{{ it.expiry_date }}</td>
											<td class="text-xs">{{ it.remarks }}</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
						<div class="mb-2 mt-2 flex justify-end space-x-10 px-2">
							<div class="flex justify-between space-x-1">
								<a href="#" @click="onPrint(BOHead.id)" type="submit" class="btn btn-sm btn-primary w-60">Print</a>
							</div>
						</div>
					</div>
					
				</div>
			</div>
		</div>
    </navigation>
</template>
