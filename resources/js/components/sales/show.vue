<script setup>
import navigation from '@/layouts/navigation.vue';
import { CheckCircleIcon, TrashIcon, PlusIcon, XMarkIcon, ExclamationCircleIcon, ArrowUturnLeftIcon, ChevronRightIcon, ChevronLeftIcon } from '@heroicons/vue/24/solid'
import {onMounted, ref} from "vue";
import { useRouter } from "vue-router";
const router = useRouter();

let form = ref({
        id:''
    })
let req_items=ref([]);
let error = ref([])
let success = ref('')

const props = defineProps({
        id:{
            type:String,
            default:''
        }
    })

onMounted(async () => {
	GetRequestHead()
	GetRequestItems()
})


		const GetRequestHead = async () => {
			let response = await axios.get(`/api/get_request_head/${props.id}`)
			form.value=response.data.head
		}

		const GetRequestItems = async () => {
			let response = await axios.get(`/api/get_request_items/${props.id}`)
			req_items.value=response.data.req_items
		}

		const PrintRequest = (id) => {
		router.push('/request/print/'+id)
		}

		// const saveDraft = (id) => {

		// const formDetails = new FormData()
		// formDetails.append('receive_head_id', details.value.receive_head_id)
		// formDetails.append('detail_no', details.value.detail_no)
		// formDetails.append('pr_no', details.value.pr_no)
		// formDetails.append('department', details.value.department)
		// formDetails.append('inspected_by', details.value.inspected_by)
		// formDetails.append('enduse', details.value.enduse)
		// formDetails.append('purpose', details.value.purpose)
		// formDetails.append('request_items', JSON.stringify(rows.value))

		// axios.post(`/api/save_draft_details/${form.value.id}`, formDetails).then(function () {
			
		// 	error.value=[]
		// 	error_items.value=[]

		// 	success.value='Saved as draft'
		// 	}, function (err) {

		// 		error.value=[]
		// 		error_items.value=[]
				
				
		// });

		// }

	// 	const saveTransaction = (id, method) => {
	// 	if(form_receive.value.length>=1){
    //         const formItems= new FormData()
	// 		formItems.append('request_items', JSON.stringify(form_receive.value))
	// 		axios.post(`/api/save_request/${id}`, formItems).then(function (response) {
	// 			error.value=[]
	// 			router.push('/request/new_second/'+response.data)
	// 			}).catch(function(err){
	// 				success.value=''
	// 			});
	// 		}
	// 	}

	// const cancelTransaction = (id) => {
	// 		if(confirm("Are you sure you want to cancel transaction?")){
				
	// 			axios.get(`/api/cancel_transaction/${id}`).then(function () {
	// 				router.push('/request')
	// 			});
	// 		}
	// 	}

</script>

<template>
    <navigation>
        <div class="container-fluid">
			<!-- BreadCrumb -->
			<div class="card mb-3">	
				<div class="flex justify-between content-center">
					<div class="flex justify-start space-x-3 ">
						<div class="">
							<a href="/request" class="btn btn-secondary btn-xs btn-rounded">
								<ArrowUturnLeftIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></ArrowUturnLeftIcon>
							</a>
						</div>
						<div>
							<h6 class="m-0 pt-1 font-bold uppercase">Request</h6>
						</div>
					</div>
					<div class="pt-1">	
						<nav aria-label="breadcrumb" role="navigation">
							<ol class="breadcrumb adminx-page-breadcrumb">
								<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
								<li class="breadcrumb-item"><a href="/request">Request</a></li>
								<li class="breadcrumb-item active" aria-current="page">Add New Request</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>

			<div class="row mb-3">
				<div class="col-md-12 col-lg-12">
					<div class="card card-main-bg">						
						<div class="pt-4 pb-3 px-2">
							<table class="w-full table-bordersed">
								<tr>
									<td class="" width="20%">
										<div class="flex justify-start">
											<span class="text-lg uppercase font-bold text-gray-600 w-full leading-none">
												{{ form.mreqf_no }}
											</span>
										</div>
										<div class="flex justify-start ">
											<span class="text-xs text-gray-500 leading-none pt-1">MREQF NO.</span>
										</div>
									</td>
									<td class="" width="20%">
										<div class="flex justify-start">
											<span class="text-md uppercase font-bold text-gray-600 w-full leading-none">
												{{ form.request_type }}
											</span>
										</div>
										<div class="flex justify-start" >
											<span class="text-xs text-gray-500 leading-none pt-1">REQUEST TYPE</span>
										</div>
									</td>
									<td width="11%"></td>
									<td width="12%"></td>
									<td width="20%"></td>
									<td class="" width="10%">
										<div class="flex justify-end">
											<span class="text-md uppercase font-bold text-gray-600 w-full leading-none text-right">
												{{ form.request_date }} 
											</span>
										</div>
										<div class="flex justify-end" >
											<span class="text-xs text-gray-500 leading-none pt-1">DATE</span>
										</div>
									</td>
									<td class=""  width="7%">
										<div class="flex justify-end">
											<span class="text-md uppercase font-bold text-gray-600 w-full leading-none text-right">
												{{ form.request_time }}
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
									<td class="" >
										<div class="flex justify-start">
											<span class="text-md uppercase font-bold text-gray-600 w-full leading-none">
												{{ form.pr_no }}
											</span>
										</div>
										<div class="flex justify-start" >
											<span class="text-xs text-gray-500 leading-none pt-1">PR NUMBER</span>
										</div>
									</td>
									<td class="">
										<div class="flex justify-start">
											<span class="text-md font-bold text-gray-600 w-full leading-none">
												{{ form.department_name }}
											</span>
										</div>
										<div class="flex justify-start" >
											<span class="text-xs text-gray-500 leading-none pt-1">DEPARTMENT</span>
										</div>
									</td>
									<td class="" colspan="5">
										<div class="flex justify-start">
											<span class="text-sm font-bold text-gray-600 w-full leading-none">
												{{ form.enduse_name }}
											</span>
										</div>
										<div class="flex justify-start ">
											<span class="text-xs text-gray-500 leading-none pt-1">END-USE</span>
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
												{{ form.purpose_name }}
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
												{{ form.remarks }}
											</span>
										</div>
										<div class="flex justify-start" >
											<span class="text-xs text-gray-500 leading-none pt-1">REMARKS</span>
										</div>
									</td>
								</tr>
							</table>
							<div class="mt-2">	
								<table class="table table-bordered mb-0">
									<thead>
										<tr>
											<th class="font-xxs" width="30%">Item Decription</th>
											<th class="font-xxs">Request Qty</th>
											<th class="font-xxs">Shipping Cost</th>
											<th class="font-xxs">Unit Cost</th>
											<th class="font-xxs" width="20%">Supplier</th>
											<th class="font-xxs" width="5%">UOM</th>
											<th class="font-xxs" width="7%">Cat. No.</th>
											<th class="font-xxs" width="7%">Brand</th>
											<th class="font-xxs" width="7%">Serial No</th>
											<th class="font-xxs" width="7%">Size</th>
											<th class="font-xxs" width="7%">Color</th>
											<th class="font-xxs" width="7%">Item Status</th>
											<th class="font-xxs" width="7%">Expiry Date</th>
										</tr>
									</thead>
									<tbody>
										<tr v-for="it in req_items">
											<td class="text-xs" v-if="it.compose_id != 0">{{ it.item_description }} - {{ it.compose_itemname }}</td>
											<td class="text-xs" v-else>{{ it.item_description }}</td>
											<td class="text-xs">{{ it.req_qty }}</td>
											<td class="text-xs">{{ it.shipping_cost }}</td>
											<td class="text-xs">{{ it.unit_cost }} {{ (it.currency!=null) ? it.currency : '' }}</td>
											<td class="text-xs">{{ it.supplier_name }}</td>
											<td class="text-xs">{{ it.uom }}</td>
											<td class="text-xs">{{ it.catalog_no }}</td>
											<td class="text-xs">{{ it.brand }}</td>
											<td class="text-xs">{{ it.serial_no }}</td>
											<td class="text-xs">{{ it.size }}</td>
											<td class="text-xs">{{ it.color }}</td>
											<td class="text-xs">{{ it.item_status }}</td>
											<td class="text-xs">{{ it.expiry_date }}</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
						<hr class="border-dashed m-2">	
						<div class="mb-2 mt-2 flex justify-end space-x-10">
							<div class="flex justify-between space-x-1">
								<!-- <a href="/" type="submit" class="btn btn-sm hover:bg-red-600 bg-red-500 text-white">Cancel Transaction</a> -->
								<a @click="PrintRequest(form.id)" class="btn btn-sm hover:bg-blue-600 bg-blue-500 text-white w-60">Print</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

    </navigation>
</template>
