<script setup>
import navigation from '@/layouts/navigation.vue';
import { XMarkIcon, ArrowUturnLeftIcon} from '@heroicons/vue/24/solid'
import {onMounted, ref} from "vue";
import { useRouter } from "vue-router";
const router = useRouter();

let form = ref({
        id:''
    })

let form_receive=ref([]);
const showModal = ref(false)
const hideModal = ref(true)
const showPreview = ref(false)
const hidePreview = ref(true)
const openPreview = () => {
		showPreview.value = !showPreview.value
		GetRequestHead()
	}
	const closePreview = () => {
		showPreview.value = !hidePreview.value
	}
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
})

		const openModel = () => {
				showModal.value = !showModal.value
			}

		const closeModal = () => {
				showModal.value = !hideModal.value
			}


		const GetRequestHead = async () => {
				let response = await axios.get(`/api/create_details/${props.id}`)
				form.value=response.data.request_head
				form_receive.value=response.data.receiveitems
			}

		// const SaveNewRequest = (id) => {
		// 	if(confirm("Are you sure you want to save this Request?")){
		// 		saveTransaction(id)				
		// 	}
		// }

		const SaveNewRequest = (id) => {
			if(confirm("Are you sure you want to save this Request?")){
				const no_of_rows = form_receive.value.length
				let countererror = 0
				for(var x=0;x<no_of_rows;x++){
					if(form_receive.value[x].req_qty ==0){
						countererror++
					}
				}
				if(countererror == no_of_rows){
					alert('Warning: No request quantity set.');
				} else {
					saveTransaction(id)				
				}
			}
			
		}

		const saveTransaction = (id, method) => {
			// if(confirm("Are you sure you want to save this Request?")){
				if(form_receive.value.length>=1){
					const formItems= new FormData()
						formItems.append('request_items', JSON.stringify(form_receive.value))
						axios.post(`/api/save_request/${id}`, formItems).then(function (response) {
							console.log(response);
							error.value=[]
							router.push('/request/print/'+id)
							}).catch(function(err){
								success.value=''
							});
				}
			// }
		}

		const cancelTransaction = (id) => {
			if(confirm("Are you sure you want to cancel transaction?")){
				
				axios.get(`/api/cancel_transaction_request/${id}`).then(function () {
					router.push('/request')
				});
			}
		}

		const QtyLimit = (loop) => {
		const rec_quantity = document.getElementById("rec_quantity"+loop).value;
		const req_quantity = document.getElementById("req_quantity"+loop).value;
		const btn = document.getElementById("SubmitButton");
			if(parseFloat(rec_quantity) >= parseFloat(req_quantity)){
				btn.disabled = false;
			}else{
				alert('Request quantity not equal to received quantity');
				btn.disabled = true;
			}
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
						<div class="py-4 px-2">
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
								<table class="table table-bordered">
									<thead>
										<tr>
											<th class="font-xxs" width="30%">Item Description</th>
											<th class="font-xxs" width="5%">Avail Qty</th>
											<th class="font-xxs" width="5%">Qty</th>
											<!-- <th class="font-xxs" width="5%">Unit Cost</th>
											<th class="font-xxs" width="5%">Shipping Cost</th> -->
											<th class="font-xxs" width="5%">Unit Cost</th>
											<th class="font-xxs" width="15%">Supplier</th>
											<th class="font-xxs" width="5%">UOM</th>
											<th class="font-xxs" width="7%">Cat. No.</th>
											<th class="font-xxs" width="7%">Brand</th>
											<th class="font-xxs" width="7%">Serial No</th>
											<th class="font-xxs" width="7%">Size</th>
											<th class="font-xxs" width="7%">Color</th>
											<th class="font-xxs" width="7%">Item Status</th>
											<th class="font-xxs" width="10">Expiry Date</th>
										</tr>
									</thead>
									<tbody>
										<tr v-for="(items, i) in form_receive">
											<td class="text-xs">{{ items.item_description }}</td>
											<td>
												<span class="text-xs font-bold text-gray-600 w-full leading-none">
													<input type="number" class="w-full bg-gray-200 p-1 h-full" :id="'rec_quantity'+ i" v-model="items.rec_qty" readonly>
												</span>
											</td>
											<td>
												<span class="text-xs font-bold text-gray-600 w-full leading-none">
													<input v-model="items.req_qty" :id="'req_quantity'+ i" type="number" min="0" onkeypress="return (event.charCode !=8 && event.charCode ==0 || (event.charCode >= 48 && event.charCode <= 57))" class="w-full bg-orange-100 p-1 h-full" @change="QtyLimit(i)">
												</span>
											</td>
											<!-- <td class="text-xs">{{ items.unit_cost }}</td>
											<td class="text-xs">{{ items.shipping_cost }}</td> -->
											<td class="text-xs">{{ items.total_cost}} {{ (items.currency!=null) ? items.currency : '' }}</td>
											<td class="text-xs">{{ items.supplier }}</td>
											<td class="text-xs">{{ items.uom }}</td>
											<td class="text-xs">{{ items.catalog_no }}</td>
											<td class="text-xs">{{ items.brand }}</td>
											<td class="text-xs">{{ items.serial_no }}</td>
											<td class="text-xs">{{ items.size }}</td>
											<td class="text-xs">{{ items.color }}</td>
											<td class="text-xs">{{ items.item_status }}</td>
											<td class="text-xs">{{ items.expiry_date }}</td>
											<input type="hidden" class="form-control" v-model="items.currency">
											<input type="hidden" class="form-control" v-model="items.unit_cost">
											<input type="hidden" class="form-control" v-model="items.shipping_cost">
											<input type="hidden" class="form-control" v-model="items.variant_id">
											<input type="hidden" class="form-control" v-model="items.item_id">
										</tr>
									</tbody>
								</table>
							</div>	
							<hr class="border-dashed m-2">	
							<div class="row">
								<div class="col-lg-12"> 
									<div class="mb-0 mt-1 flex justify-end space-x-10">
										<!-- <button  @click="openPreview()" class="btn btn-sm py-1.5 btn-success w-32">Preview</button> -->
										<div class="flex justify-between space-x-5">
											<div class="flex justify-between space-x-1">
												<button @click="cancelTransaction(form.id)" class="btn btn-sm btn-danger" >Cancel Transaction</button>
												<!-- <button @click="saveDraft(form.id)" type="submit" class="btn btn-sm py-1.5 bg-orange-400 hover:bg-orange-600 w-32 text-white">Save as Draft</button> -->
												<button @click="SaveNewRequest(form.id,'save')" id = "SubmitButton" type="submit" class="btn btn-sm py-1.5 bg-blue-500 text-white hover:bg-blue-600 w-60">Save & Print</button>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
			<div class="modal pt-4 px-3" :class="{ show:showPreview }">
				<div></div>
				<div class="modal__content w-full">
					<div class="row mb-4">
						<div class="col-lg-12 flex justify-end">
							<a href="#" class="text-gray-600" @click="closePreview">
								<XMarkIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"></XMarkIcon>
							</a>
						</div>
					</div>
					<div class="p- pt-2">
							<div class="row">
								<div class="col-lg-3">
									<table class="w-full border border-r-0">
										<tr>
											<td class="px-1" width="25%">
												<div class="flex justify-start" >
													<span class="text-xs text-gray-500 leading-none">MREQF NO.</span>
												</div>
											</td>
										</tr>
										<tr>
											<td class="p-1 pt-0  border-b leading-none" >
												<span class="text-base font-bold text-gray-600 w-full leading-none">
													{{ form.mreqf_no }}
												</span>
											</td>
										</tr>
										<tr>
											<td class="px-1 leading-none">
												<span class="text-xs text-gray-500 leading-none">DATE/TIME</span>
											</td>
										</tr>
										<tr>
											<td class="p-1 pt-0 border-b leading-none">
												<span class="text-base font-bold text-gray-600 w-full leading-none" >{{ form.request_date }} - {{ form.request_time }}
												</span>
											</td>
										</tr>
										<tr>
											<td class="px-1 leading-none" colspan="2">
												<span class="text-xs text-gray-500 leading-none">REQUEST TYPE</span>
											</td>
										</tr>
										<tr>
											<td class="p-1 pt-0 leading-none" colspan="2">
												<span class="text-base font-bold text-gray-600 w-full leading-none">
													{{ form.request_type }}
												</span>
											</td>
										</tr>
									</table>
								</div>
								<div class="col-lg-9 pl-0">
									<table class="w-full border">
										<tr>
											<td class="px-1 border-r" width="25%">
												<div class="flex justify-start" >
													<span class="text-xs text-gray-500 leading-none">JO/PR NUMBER</span>
												</div>
											</td>
											<td class="px-1 border-r" width="25%">
												<div class="flex justify-start" >
													<span class="text-xs text-gray-500 leading-none">DEPARTMENT</span>
												</div>
											</td>
											<td class="px-1 border-r" width="25%">
												<div class="flex justify-start" >
													<span class="text-xs text-gray-500 leading-none">END-USE</span>
												</div>
											</td>
										</tr>
										<tr>
											<td class="p-1 pt-0 border-r border-b leading-none">
												<span class="text-base font-bold text-gray-600 w-full leading-none">
													{{ form.pr_no }}
												</span>
											</td>
											<td class="p-1 pt-0 border-r border-b leading-none">
												<span class="text-base font-bold text-gray-600 w-full leading-none">
													{{ form.department_name }}
												</span>
											</td>
											<td class="p-1 pt-0 border-r border-b leading-none">
												<span class="text-base font-bold text-gray-600 w-full leading-none">
													{{ form.enduse_name }}
												</span>
											</td>
										</tr>
										<tr>
											<td class="px-1 border-r leading-none" colspan="3">
												<span class="text-xs text-gray-500 leading-none">PURPOSE</span>
											</td>
										</tr>
										<tr>
											<td class="p-1 pt-0 border-r border-b leading-none" colspan="3">
												<span class="text-base font-bold text-gray-600 w-full leading-none">
													{{ form.purpose_name }}
												</span>
											</td>
										</tr>
										<tr>
											<td class="px-1 border-r leading-none" colspan="3">
												<span class="text-xs text-gray-500 leading-none">REMARKS</span>
											</td>
										</tr>
										<tr>
											<td class="p-1 pt-0 border-r leading-none" colspan="3">
												<span class="text-base font-bold text-gray-600 w-full leading-none">
													{{ form.remarks }}
												</span>
											</td>
										</tr>
									</table>
								</div>
							</div>
						</div> 
				</div>
			</div>
		</Transition>

		<!-- ==================== add modal items ==================== -->
		<!-- <div class="modal main__modal " :class="{ show:showModal }">
			<div class="absolute w-full h-full top-0" @click="closeModal"></div>
            <div class="modal__content  w-5/12">
				<div class="border-b pb-2 mb-2">
					<span class="modal__close btn__close--modal pt-2" @click="closeModal">
						<XMarkIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mt-1"></XMarkIcon>
					</span>
					<h6 class="modal__title m-0">Add Item</h6>
				</div>
                <div class="">
					<div class="row">
						<div class="col-lg-6">
							<div class="form-group">
								<span class="text-base font-bold text-gray-600 w-full leading-none">
									<select id="item_desc" class="form-control border" v-model="req_items.item_desc" @change="itemdetails()">
										<option v-for="it in resultItems" v-bind:key="it.id" v-bind:value="it.id">{{  it.item_description }} - {{  it.pr_no }}</option>
									</select>
								</span>
							</div>	
							<input type="hidden" id="itemdesc" class="form-control border" v-model="req_items.item_description" disabled>									
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label class="form-label mb-0">Avail Qty</label>
								<input id="avail_qty" type="numbers" class="form-control border" v-model="req_items.avail_qty" disabled>
							</div>										
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label class="form-label mb-0">Quantity</label>
								<input type="numbers" class="form-control border" v-model="req_items.req_qty">
							</div>										
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12">
							<div class="form-group">
								<label class="form-label mb-0">UOM</label>
								<input type="text" id="unit" class="form-control border" v-model="req_items.uom" disabled>
							</div>										
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12">
							<div class="form-group">
								<label class="form-label mb-0">Supplier</label>
								<input type="text" id="supp" class="form-control border" v-model="req_items.supplier" disabled>
							</div>										
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12">
							<div class="form-group">
								<label class="form-label mb-0">Catalog No</label>
								<input type="text" id="cat_no" class="form-control border" v-model="req_items.catalog_no" disabled>
							</div>										
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12">
							<div class="form-group">
								<label class="form-label mb-0">Brand</label>
								<input type="text" id="brand" class="form-control border" v-model="req_items.brand" disabled>
							</div>										
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12">
							<div class="form-group">
								<label class="form-label mb-0">Serial No</label>
								<input type="text" id="serial" class="form-control border" v-model="req_items.serial_no" disabled>
							</div>										
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12">
							<div class="form-group">
								<label class="form-label mb-0">Item Status</label>
								<input type="text" id="stat" class="form-control border" v-model="req_items.item_status" disabled>
							</div>										
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12">
							<div class="form-group">
								<label class="form-label mb-0">Expiration Date</label>
								<input type="text" id="expiry" class="form-control border" v-model="req_items.expiry_date" disabled>
							</div>										
						</div>
					</div>
                </div>
				<input type="hidden" id="item" class="form-control border" v-model="req_items.item_id" disabled>
				<input type="hidden" id="variant" class="form-control border" v-model="req_items.variant" disabled>
                <div class="border-t flex justify-end pt-3 mt-2">
                    <button class="btn btn-danger btn-sm mr-2 btn__close--modal" @click="closeModal">
                        Cancel
                    </button>
                    <button class="btn btn-primary btn-sm px-4" id = "SubmitButton" @click="addReqItems(req_items)">Save</button>
                </div>
            </div>
        </div> -->
    </navigation>
</template>
