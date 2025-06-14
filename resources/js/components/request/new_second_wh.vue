<script setup>
import navigation from '@/layouts/navigation.vue';
import { TrashIcon, PlusIcon, XMarkIcon, ArrowUturnLeftIcon } from '@heroicons/vue/24/solid'
import {onMounted, ref} from "vue";
import { useRouter } from "vue-router";
const router = useRouter();

let form = ref({
        id:''
    })

let form_receive=ref([]);
let resultItems=ref([]);
let wh_variant=ref([]);
let wh_items = ref('');
let variant_det = ref('');
let wh_qty = ref(0);
let req_qty = ref(0);
let i_cost = ref(0);

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
	getWHItems()
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

		const SaveNewRequest = (id, method) => {
			if(confirm("Are you sure you want to save this Request?")){
				if(form_receive.value.length>=1){
					//alert(form.value.request_type)
					const formItems= new FormData()
						formItems.append('request_insert_wh', JSON.stringify(form_receive.value))
						axios.post(`/api/save_request/${id}`, formItems).then(function (response) {
							// console.log(response);
							error.value=[]
							router.push('/request/print/'+id)
							}).catch(function(err){
								success.value=''
							});
				}
			}
		}

		const addReqItems= () => {
			let item_selected = wh_items.value
			const whi= item_selected.split("_")
			let id= whi[0]
			let desc = whi[1]

			let variant_selected = variant_det.value
			// if(variant_det.value.length!=0){
				const vis= variant_selected.split("_")
				var var_desc= vis[0]
				let currency = vis[7]
				//var variant_id= vis[1]
				// var composite_id= vis[3]

				if(vis[1] == undefined){
					var variant_id= 0
					var unit_cost= i_cost.value
					var shipping_cost= 0
					var composite_id= 0
					var item_cost = i_cost.value
				}else if(vis[3] != undefined){
					var variant_id= vis[1]
					var composite_id= vis[3]
					var unit_cost= vis[4]
					var shipping_cost= vis[5]
					var item_cost = (parseFloat(unit_cost) + parseFloat(shipping_cost))
				}else{
					var variant_id= vis[1]
					var composite_id= vis[3]
					var unit_cost= vis[4]
					var shipping_cost= vis[5]
					var item_cost = (unit_cost + shipping_cost)
					
				}
				
			// }else{
			// 	var variant_id= 0
			// 	var composite_id= 0
			// 	var var_desc = ""
			// 	var unit_cost= 0
			// 	var shipping_cost= 0
			// }
			// let count = 0

			if(req_qty.value == 0){
				alert('Quantity must not be empty');
				document.getElementById("addreq").disabled = true;
			}else{
				form_receive.value.push({
					item_id:id,
					description :desc,
					var_det :var_desc,
					wh_qty:wh_qty.value,
					req_qty:req_qty.value,
					variantid:variant_id,
					compositeid:composite_id,
					unitcost:unit_cost,
					currency:currency,
					shippingcost:shipping_cost,
					itemcost:item_cost,
					}
				);
				wh_items.value=[]
				variant_det.value=[]
				wh_qty.value=0
				req_qty.value=0
				i_cost.value=0
				document.getElementById("SubmitButton").disabled = false;
			}
			
		}

		const getWHItems = async() => {
			let response = await axios.get("/api/wh_item_list");
			resultItems.value = response.data.wh_items;
    	}

		const removeItem= (index) =>{
			if(confirm("Do you really want to delete this row?")){
				form_receive.value.splice(index,1)

				if(index > 0){
					document.getElementById("SubmitButton").disabled = false;
					document.getElementById("addreq").disabled = false;
				}else{
					document.getElementById("SubmitButton").disabled = true;
					document.getElementById("addreq").disabled = true;
				}
			}
		}

		const itemdetails = async () => {
				let item_selected = wh_items.value
				const vis= item_selected.split("_")
				let id= vis[0]

				let response = await axios.get('/api/search_wh_variant?filter='+id);
				if(response.data.v_flag != 0 && response.data.c_flag == 0){
					wh_variant.value = response.data.items;
					wh_qty.value = 0;
					req_qty.value = 0;
					i_cost.value=0;
					document.getElementById("addreq").disabled = false;
					document.getElementById("variantdet").disabled = false;
				}else if(response.data.v_flag == 0 && response.data.c_flag != 0){
					req_qty.value = 0;
					wh_variant.value = response.data.items;
					wh_qty.value=response.data.quantity
					i_cost.value=response.data.composite_cost
					variant_det.value = '';
					// document.getElementById("variantdet").disabled = true;
					document.getElementById("addreq").disabled = false;
					document.getElementById("variantdet").disabled = false;
				}else{
					req_qty.value = 0;
					i_cost.value=0;
					wh_qty.value=response.data.quantity
					variant_det.value = '';
					document.getElementById("variantdet").disabled = true;
					document.getElementById("addreq").disabled = false;
			}
		}

		const checkVariantqty = async () => {
			let variant_selected = variant_det.value
				const vis= variant_selected.split("_")
				let variantid= vis[1]
				let var_itemid= vis[2]
				var composite_id= vis[3]
				var u_cost= vis[4]
				var s_cost= vis[5]
				var t_cost= vis[6]
				var currency= vis[7]
				// var item_cost = (parseFloat(u_cost) + parseFloat(s_cost))
				//let itemid= vis[1]
				let item_selected = wh_items.value
				const whi= item_selected.split("_")
				let itemid= whi[0]

				let response = await axios.get('/api/search_whvariantqty/'+variantid+'/'+var_itemid+'/'+itemid);
				req_qty.value = 0;
				wh_qty.value=response.data.quantity

				if(variant_det.value.length!=0){
					i_cost.value= t_cost+" "+currency
				}else{
					i_cost.value= 0
				}

				for (var i = 0; i < form_receive.value.length; i++) {
					if(form_receive.value[i].variantid == variantid && form_receive.value[i].compositeid == composite_id && form_receive.value[i].item_id == itemid){
						alert('Variant/Composite Item is already added!');
						document.getElementById("addreq").disabled = true;
					}
				}
				
		}

		const cancelTransaction = (id) => {
			if(confirm("Are you sure you want to cancel transaction?")){
				
				axios.get(`/api/cancel_transaction_request/${id}`).then(function () {
					router.push('/request')
				});
			}
		}

		const WHQtyLimit = async () => {
		const whquantity = document.getElementById("whqty_").value;
		const reqquantity = document.getElementById("reqqty").value;
			if(parseFloat(whquantity) >= parseFloat(reqquantity)){
				document.getElementById("addreq").disabled = false;
			}else{
				alert('Request quantity not equal to warehouse quantity');
				document.getElementById("addreq").disabled = true;
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
							<a href="/dashboard" class="btn btn-secondary btn-xs btn-rounded">
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
								<!-- <div class="flex justify-between space-x-1 mb-2"></div> -->
								<table class="table table-bordered">
									<thead>
										<tr>
											<th width="40%">Item Description</th>
											<th width="40%">Variant/Composite Item</th>
											<th width="5%">Unit Cost</th>
											<th width="5%">Avail Qty</th>
											<th width="5%">Qty</th>
											<th class="space-x-1" width="1%"></th>
										</tr>
									</thead>
								
									<tbody>
										<tr class="!border-b-2 !border-blue-200">
											<td class="p-0">
												<select class="form-control border !py-2 !bg-orange-100" v-model="wh_items" @change="itemdetails()">
													<option v-for="it in resultItems" :value="it.id + '_' + it.item_description">{{  it.item_description }} </option>
												</select>
											</td>
											<td class="p-0">
												<select name="whvariant" :id="'variantdet'" class="form-control border !py-2 !bg-orange-100" v-model="variant_det" @change="checkVariantqty()">
													<!-- <option :value="wh.item_name+ '_' +wh.variant_id+ '_' +wh.item_id+ '_' +wh.supplier+ '_' +wh.brand+ '_' +wh.cat_no+ '_' +wh.serial+ '_' +wh.size+ '_' +wh.color+ '_' +wh.composite_id" v-for="wh in wh_variant" :key="wh.id">{{ wh.variant_data }}</option> -->
													<option :value="wh.variant_data+ '_' +wh.variant_id+ '_' +wh.item_id+ '_' +wh.composite_id+ '_' +wh.unit_cost+ '_' +wh.shipping_cost+ '_' +wh.total_cost+ '_' +wh.currency" v-for="wh in wh_variant" :key="wh.id">{{ wh.variant_data }}</option>
												</select>
											</td>
											<td class="p-0 bg-gray-200">
												<input type="text" :id="'icost'" class="form-control w-full !py-2" v-model="i_cost" readonly>
											</td>
											<td class="p-0 bg-gray-200">
												<input type="number" :id="'whqty_'" class="form-control w-full !py-2" v-model="wh_qty" readonly>
											</td>
											<td class="p-0 bg-gray-200">
												<input type="text"  :id="'reqqty'" class="form-control border !bg-orange-100 w-full !py-2" v-model="req_qty" min="0" onkeypress="return (event.charCode !=8 && event.charCode ==0 || (event.charCode >= 48 && event.charCode <= 57))" @change="WHQtyLimit()">
											</td>
											<td class="p-1" align="center">
												<button @click="addReqItems()" id = "addreq" class="btn btn-xs btn-primary btn-rounded" disabled>
													<PlusIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"></PlusIcon>
												</button>
											</td>
										</tr>
										<tr v-for="(items, index) in form_receive">
											<td>{{ items.description }}</td>
											<td>{{ items.var_det }}</td>
											<td>{{ items.itemcost }} {{ (items.currency!=null) ? items.currency : '' }}</td>
											<td>{{ items.wh_qty }}</td>
											<td>{{ items.req_qty }}</td>
											<input type="hidden" class="p-1 m-0 w-full leading-none" v-model="items.item_id" />
											<input type="hidden" class="p-1 m-0 w-full leading-none" v-model="items.variantid" />
											<input type="hidden" class="p-1 m-0 w-full leading-none" v-model="items.compositeid" />
											<input type="hidden" class="p-1 m-0 w-full leading-none" v-model="items.unitcost" />
											<input type="hidden" class="p-1 m-0 w-full leading-none" v-model="items.currency" />
											<input type="hidden" class="p-1 m-0 w-full leading-none" v-model="items.shippingcost" />
											<td class="space-x-1" >
												<a class="text-white btn btn-xs btn-danger btn-rounded" @click="removeItem(index)">
													<TrashIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"></TrashIcon>
												</a>
											</td>
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
												<button @click="SaveNewRequest(form.id,'save')" id = "SubmitButton" type="submit" class="btn btn-sm py-1.5 bg-blue-500 text-white hover:bg-blue-600 w-60" disabled>Save & Print</button>
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
    </navigation>
</template>
