<script setup>
	import navigation from '@/layouts/navigation.vue';
	import { PencilSquareIcon, TrashIcon, PlusIcon, MagnifyingGlassIcon, ChevronLeftIcon, ChevronRightIcon, ArrowUturnLeftIcon } from '@heroicons/vue/24/solid'
	
	import {onMounted, ref} from "vue";
	import { useRouter } from "vue-router";

    const router = useRouter()

	let head = ref({
        id:''
    })
	let items = ref([])

    const props = defineProps({
        id:{
            type:String,
            default:''
        }
    })

	onMounted(async () =>{
        getIssuanceHead()
    })

    const getIssuanceHead = async () => {
		let response = await axios.get(`/api/get_issuance_head/${props.id}`)
		head.value = response.data.issuancehead
		items.value = response.data.issuanceitems
		//console.log(response.data)
		
		
		
	}
	
	const printDiv = (id) => {
		router.push('/issue/print/'+id,'_blank')
	}
	const printGatepass = (id) => {
		router.push('/issue/gatepass/'+id)
	}
	// const getReceiveDetails = async () => {
	// 	let response = await axios.get(`/api/get_receive_details/${props.id}`)
	// 	details.value = response.data.details
	// }

	
</script>

<template>
    <navigation>
        <div class="container-fluid">
			<!-- BreadCrumb -->
			<div class="card mb-3">	
				<div class="flex justify-between content-center">
					<div class="flex justify-start space-x-3 ">
						<div class="">
							<a href="/issue" class="btn btn-secondary btn-xs btn-rounded">
								<ArrowUturnLeftIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></ArrowUturnLeftIcon>
							</a>
						</div>
						<div>
							<h6 class="m-0 pt-1 font-bold uppercase">Issue</h6>
						</div>
					</div>	
					<div class="pt-1">	
						<nav aria-label="breadcrumb" role="navigation">
							<ol class="breadcrumb adminx-page-breadcrumb">
								<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
								<li class="breadcrumb-item"><a href="/issue">Issue</a></li>
								<li class="breadcrumb-item active" aria-current="page">Add New Issue</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>	
			
				<div class="row">
					<div class="col-md-12 col-lg-12">
						<div class="card card-main-bg">
							<div class="py-4 px-2">
								<div class="row">
									<div class="col-lg-12">
										<table class="w-full table-borsdered">
											<tr>
												<td class="" width="20%">
													<div class="flex justify-start">
														<span class="text-lg uppercase font-bold text-gray-600 w-full leading-none">
															{{ head.mreqf_no }}
														</span>
													</div>
													<div class="flex justify-start ">
														<span class="text-xs text-gray-500 leading-none pt-1 uppercase ">MREQF NO</span>
													</div>
												</td>
												<td class="" width="20%">
													<div class="flex justify-start">
														<span class="text-lg uppercase font-bold text-gray-600 w-full leading-none">
															{{ head.mif_no }}
														</span>
													</div>
													<div class="flex justify-start ">
														<span class="text-xs text-gray-500 leading-none pt-1 uppercase ">MIF NO</span>
													</div>
												</td>
												<td class="" width="20%">
													<div class="flex justify-start">
														<span class="text-lg uppercase font-bold text-gray-600 w-full leading-none">
															{{ head.pr_no }}
														</span>
													</div>
													<div class="flex justify-start" >
														<span class="text-xs text-gray-500 leading-none pt-1 uppercase">JO/PR NO</span>
													</div>
												</td>
												<td width="11%"></td>
												<td width="12%"></td>
												<td class="" width="10%">
													<div class="flex justify-end">
														<span class="text-md uppercase font-bold text-gray-600 w-full leading-none text-right">
															{{ head.issuance_date }}
														</span>
													</div>
													<div class="flex justify-end" >
														<span class="text-xs text-gray-500 leading-none pt-1">DATE</span>
													</div>
												</td>
												<td class=""  width="8%">
													<div class="flex justify-end">
														<span class="text-md uppercase font-bold text-gray-600 w-full leading-none text-right">
															{{ head.issuance_time }}
														</span>
													</div>
													<div class="flex justify-end" >
														<span class="text-xs text-gray-500 leading-none pt-1 text-right">TIME</span>
													</div>
												</td>
											</tr>
											<tr>
												<td colspan="6" class="py-1"></td>
											</tr>
											<tr>
												<td class="" width="20%">
													<div class="flex justify-start">
														<span class="text-sm font-bold text-gray-600 w-full leading-none">
															{{ head.department_name }}
														</span>
													</div>
													<div class="flex justify-start ">
														<span class="text-xs text-gray-500 leading-none pt-1 uppercase">Department</span>
													</div>
												</td>
												<td class="" width="20%" colspan="4">
													<div class="flex justify-start">
														<span class="text-sm font-bold text-gray-600 w-full leading-none">
															{{ head.enduse_name }}
														</span>
													</div>
													<div class="flex justify-start" >
														<span class="text-xs text-gray-500 leading-none pt-1 uppercase">End-use</span>
													</div>
												</td>
												<td width="10%"></td>
												<td width="10%"></td>
											</tr>
											<tr>
												<td colspan="6" class="py-1"></td>
											</tr>
											<tr>
												<td class="" colspan="8">
													<div class="flex justify-start">
														<span class="text-sm font-bold text-gray-600 w-full leading-none">
															{{ head.purpose_name }}
														</span>
													</div>
													<div class="flex justify-start" >
														<span class="text-xs text-gray-500 leading-none pt-1">PURPOSE</span>
													</div>
												</td>
											</tr>
											<tr>
												<td colspan="6" class="py-1"></td>
											</tr>
											<tr>
												<td class="" colspan="8">
													<div class="flex justify-start">
														<span class="text-sm font-bold text-gray-600 w-full leading-none">
															{{ head.remarks }}
														</span>
													</div>
													<div class="flex justify-start" >
														<span class="text-xs text-gray-500 leading-none pt-1">REMARKS</span>
													</div>
												</td>
											</tr>
											
										</table>
										
									</div>
								</div>
							</div>
							<div class="px-2">
								<div class="row">
									<div class="col-lg-12">
										<table class="table table-bordered">
											<tbody class="border-b-2 border-gray-300" v-for="(it,x) in items">
												<tr>
													<td class="text-center text-sm text-center bg-gray-100 font-bold align-top " rowspan="5" width="2%">{{ x + 1 }}</td>
													<th class="text-center font-xxs uppercase bg-gray-100 py-1" width="3%">In Stock</th>
													<th class="text-center font-xxs uppercase bg-gray-100 py-1" width="3%">PR Qty</th>
													<th class="text-center font-xxs uppercase bg-gray-100 py-1" width="4%">Req Qty</th>
													<th class="text-center font-xxs uppercase bg-gray-100 py-1" width="3%">Qty</th>
													<th class="text-center font-xxs uppercase bg-gray-100 py-1" width="5%">Item Cost</th>
													<th class="text-center font-xxs uppercase bg-gray-100 py-1" width="5%">Currency</th>
													<th class="text-center font-xxs uppercase bg-gray-100 py-1" width="5%">Total Cost</th>
													<th class="font-xxs uppercase bg-gray-100 py-1" width="15%" colspan="4">Supplier</th>
													<th class="font-xxs uppercase bg-gray-100 py-1" width="25%" colspan="4">Item Description</th>
													<th class="text-center font-xxs uppercase bg-gray-100 py-1" width="5%">Item Status</th>
												</tr>
												<tr>
													<td class="text-xs py-1 text-center align-middle p-0 px-2">{{ it.instock }}</td>
													<td class="text-xs py-1 text-center align-middle p-0 px-2">{{ it.pr_qty }}</td>
													<td class="text-xs py-1 text-center align-middle p-0 px-2">{{ it.request_qty }}</td>
													<td class="text-xs py-1 text-center align-middle p-0 px-2" >{{ it.issued_qty }}</td>
													<td class="text-xs py-1 text-center align-middle p-0 px-2">{{ (parseFloat(it.shipping_cost) + parseFloat(it.unit_cost)) }}</td>
													<td class="text-xs py-1 text-center align-middle p-0 px-2">{{ (it.currency!=null) ? it.currency : '' }}</td>
													<td class="text-xs py-1 text-center align-middle p-0 px-2">{{ ((parseFloat(it.shipping_cost) + parseFloat(it.unit_cost)) * it.issued_qty) }}</td>
													<td class="text-xs py-1 align-middle p-0 px-2" colspan="4">{{ it.supplier }}</td>
													<td class="text-xs py-1 align-middle p-0 px-2" colspan="4">{{ it.item_description }}</td>
													<td class="text-xs py-1 text-center align-middle p-0 px-2">{{ it.item_status }}</td>
												</tr>
												<tr>
													<td class="font-xxs font-bold uppercase bg-gray-100 py-1" colspan="3">UOM</td>
													<td class="font-xxs font-bold uppercase bg-gray-100 py-1" colspan="3">Cat. No.</td>
													<td class="font-xxs font-bold uppercase bg-gray-100 py-1" colspan="4">Brand</td>
													<td class="font-xxs font-bold uppercase bg-gray-100 py-1" colspan="3">Serial No</td>
													<td class="font-xxs font-bold uppercase bg-gray-100 py-1" colspan="" width="10%">Color</td>
													<td class="font-xxs font-bold uppercase bg-gray-100 py-1" colspan="2">Size</td>
												</tr>
												<tr>
													<td class="text-xs" colspan="3">{{ it.uom }}</td>
													<td class="text-xs" colspan="3">{{ it.catalog_no }}</td>
													<td class="text-xs" colspan="4">{{ it.brand }}</td>
													<td class="text-xs" colspan="3">{{ it.serial_no }}</td>
													<td class="text-xs">{{ it.color }}</td>
													<td class="text-xs" colspan="2">{{ it.size }}</td>
												</tr>
												<tr>
													<td class="font-xxs uppercase py-0 align-middle" colspan="13">
														<div class="flex justify-start align-middle space-x-1">
															<span class="mt-1 font-bold">Remarks:</span>
															<span class="mt-1 text-xs">{{ it.remarks }}</span>
														</div>
													</td>
													<td class="font-xxs  uppercase align-middle" colspan="3">
														<div class="flex justify-start align-middle space-x-1">
															<span class="mt-1 font-bold ">Expiry Date:</span>
															<span class="mt-1 text-xs">{{ it.expiry_date }}</span>
														</div>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-lg-12">
									<div class="flex justify-center mb-3 space-x-1">
										<router-link :to="`/issue/print/${props.id}`" target="_blank" class="btn btn-sm btn-success">Print MIF</router-link>
										<router-link :to="`/issue/gatepass/${props.id}`" target="_blank" class="btn btn-sm btn-info">Print Gate Pass</router-link>
										
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				
		</div>
	
		
    </navigation>
	
</template>
