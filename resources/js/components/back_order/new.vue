<script setup>
import navigation from '@/layouts/navigation.vue';
import { ArrowUturnLeftIcon, CheckCircleIcon } from '@heroicons/vue/24/solid'
import axios from 'axios';
import {onMounted, ref} from "vue";
import { useRouter } from "vue-router";
const router = useRouter()

// let details = ref({
//         id:'',
//     })

let ponolist=ref([]);
let details=ref([]);
let items=ref([]);
let head=ref([]);
let po_no=ref([]);
let error =ref([])
let success = ref('')
let textColor = ref()
let t = ref(1)
let currency =ref([])



	const props = defineProps({
        id:{
            type:String,
            default:''
        },	
    })

	onMounted(async () => {
			getpono()
			getBackorderData()
		})

	const getpono = async () => {
	let response = await axios.get("/api/po_list");
	// ponolist.value=response.data.po
		ponolist.value=response.data
		//console.log(response.data)
	}

	const getBackorderData = async () => {
		let id = po_no.value
		if(props.id != 0 && id == ''){
			let response = await axios.get(`/api/get_receive_data/${props.id}`)
			details.value = response.data.BODetails
			items.value = response.data.BOItems
			head.value = response.data.BOHead
			currency.value = response.data.currency
		}else{
			
			let response = await axios.get('/api/get_receive_data/'+id)
			details.value = response.data.BODetails
			items.value = response.data.BOItems
			head.value = response.data.BOHead
			currency.value = response.data.currency
		
		}
		
	}
	
	const BOQtyLimit = async (loop) => {
		// const bo_quantity = document.getElementById("boq"+loop).value;
		// const bo_qty = document.getElementById("boqty"+loop).value;
		// const btn = document.getElementById("SubmitButton"+loop);
		// 	if(parseFloat(bo_quantity) >= parseFloat(bo_qty)){
		// 		document.getElementById("boqty"+loop).style.backgroundColor  = '#ffedd5'
		// 		btn.disabled = false;
		// 	}else{
		// 		//alert('Quantity not equal to Backorder quantity');
		// 		document.getElementById("boqty"+loop).style.backgroundColor  = '#FAA0A0'
		// 		btn.disabled = true;
		// 	}

		const no_of_rows = items.value.length
		let countererror = 0
		for(var x=0;x<no_of_rows;x++){
			var y = x + 1;

			if(items.value[x].total_bo < items.value[x].bo_qty){

				document.getElementById("boqty"+x).style.backgroundColor  = '#FAA0A0'
				countererror++
			} else {
				document.getElementById("boqty"+x).style.backgroundColor  = '#ffedd5'
				document.getElementById("SubmitButton").disabled = false; 
			}
		}
		checkQty(countererror)
		}

		const checkQty = (countererror) => {
			if(countererror>0){
				alert('Warning: Quantity is greater that backorder qty');
				document.getElementById("SubmitButton").disabled = true; 
			} else {
				document.getElementById("SubmitButton").disabled = false; 
			}
		}

		const SaveNewBackOrder = () => {
			if(confirm("Are you sure you want to save this Backorder?")){
				const no_of_rows = items.value.length
				let countererror = 0
				for(var x=0;x<no_of_rows;x++){

					if(items.value[x].bo_qty ==0){

						countererror++
					} 

				}
				if(countererror == no_of_rows){
					alert('Warning: No backorder quantity set.');
				} else {
					saveTransaction()				
				}
			}
			
		}

		const saveTransaction = () => {
			const formItems= new FormData()
			let id = po_no.value

			if(props.id != 0 && id == ''){
				formItems.append('receive_head_id',props.id)
			}else{
				formItems.append('receive_head_id',po_no.value)
			}
				formItems.append('bodet_insert', JSON.stringify(details.value))
				formItems.append('boit_insert', JSON.stringify(items.value))
				axios.post("/api/save_backorder/", formItems).then(function (response) {
					error.value=[]
					success.value='Successfully saved!'
					router.push('/back_order/print/'+response.data)
					}).catch(function(err){
						success.value=''
					});
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
					<div class="card card-main-bg">
						<div class="p-2 pt-3">
							<div class="row">
								<div class="col-lg-6 offset-lg-3">
									<div class="flex justify-start ">
										<span class="text-xs text-gray-500 leading-none">SELECT PO NUMBER</span>
									</div>
									<span class="text-lg uppercase text-gray-700 w-full leading-none">
										<select name="po_no" id="po_no" class="form-control border  my-1" v-model="po_no" @change="getBackorderData()"> 
											<option :value="po.id" v-for="po in ponolist" :key="po.id">{{ po.po_no }}</option>
										</select>
									</span>
								</div>
								<div class="col-lg-2">
									<!-- <a @click="getBackorderData(event)" class="btn btn-sm hover:bg-blue-600 bg-blue-500 btn-rounded text-white  w-32 mt-3">Load</a> -->
									<!-- <button  @click="getBackorderData($event)" class="btn btn-sm hover:bg-blue-600 bg-blue-500 tbtn-rounded text-white w-32 mt-3" id="saveIssue">Load</button> -->
								</div>
							</div>
							<hr class="border-dashed mb-0">
						</div>
						<div class="px-4">
							<table class="w-full table-bordersed mt-2 mb-2 " v-if="head != undefined">
								<tr>
									<td class="pr-1" width="20%">
										<div class="flex justify-start">
											<span class="text-lg uppercase font-bold text-gray-600 w-full leading-none">
												{{ head.mrif_no }}
											</span>
										</div>
										<div class="flex justify-start ">
											<span class="text-xs text-gray-500 leading-none pt-1">MRIF NO.</span>
										</div>
									</td>
									<td class="px-1" width="10%">
										<div class="flex justify-start">
											<span class="text-lg uppercase font-bold text-gray-600 leading-none">{{ head.date }}</span>
										</div>
										<div class="flex justify-start" >
											<span class="text-xs text-gray-500 leading-none pt-1">DATE</span>
										</div>
									</td>
									
									<td width="8%"></td>
									<td class="px-1  "  width="15%">
										<div class="flex justify-start">
											<span class="text-md uppercase font-bold text-gray-600 leading-none">{{ head.dr_no }}</span>
										</div>
										<div class="flex justify-start">
											<span class="text-xs text-gray-500 leading-none pt-1">DR NUMBER</span>
										</div>
									</td>
									<td class="px-1  " width="15%">
										<div class="flex justify-start">
											<span class="text-md uppercase font-bold text-gray-600 leading-none">{{ head.po_no }}</span>
										</div>
										<div class="flex justify-start" >
											<span class="text-xs text-gray-500 leading-none pt-1">PO NUMBER</span>
										</div>
									</td>
									<td class="px-1  " width="15%">
										<div class="flex justify-start">
											<span class="text-md uppercase font-bold text-gray-600 leading-none">{{ head.si_no }}</span>
										</div>
										<div class="flex justify-start" >
											<span class="text-xs text-gray-500 leading-none pt-1">SI/OR NUMBER</span>
										</div>
									</td>
									<td class="px-1  " width="15%">
										<div class="flex justify-start">
											<span class="text-md uppercase font-bold text-gray-600 leading-none">{{ head.waybill_no }}</span>
										</div>
										<div class="flex justify-start" >
											<span class="text-xs text-gray-500 leading-none pt-1">WAYBILL NUMBER</span>
										</div>
									</td>
									<td class="px-1  " width="2%">
										<span class="flex justify-center text-green-600">
											<CheckCircleIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" v-if="head.pcf === 1"></CheckCircleIcon>
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
						<span hidden>{{ t=1 }}</span>
						<div class="border-2 m-2 border-gray-400 rounded-lg pb-1" v-for="(d, x) in details">
							<table class="w-full">
								<tr>
									<tr>
										<td width="2%" rowspan="4" class="align-top p-0 "> 
											<div class="pt-2 p-1 mr-2 px-2 text-md text-center bg-gray-400 font-bold pb-5 text-white">{{ t }}</div>
										</td>
										<td class="pt-2 form-label" width="8%">PR Number</td>
										<td class="pt-2 px-1 text-sm border-b font-bold">{{ d.pr_no }}</td>
										<td class="pt-2 px-1 text-sm" width="3%"></td>
										<td class="pt-2 form-label" width="9%">Inspected by</td>
										<td class="pt-2 px-1 text-sm border-b"></td>
									</tr>
									<tr>
										<td class="form-label" >Department</td>
										<td class="px-1 text-sm border-b">{{ d.department_name}}</td>
										<td class="px-1 text-sm" width="3%"></td>
										<td class="form-label">Enduse</td>
										<td class="px-1 text-sm border-b" colspan="2">{{ d.enduse_name }}</td>
									</tr>
									<tr>
										<td class="form-label">Purpose</td>
										<td class="px-1 text-sm border-b " colspan="5">{{ d.purpose_name }}</td>
									</tr>
								</tr>
								<input :id="'details_id' + x" type="hidden" class="w-full text-right px-2 bg-orange-100" v-model="d.details_id">
								<input :id="'pr' + x" type="hidden" class="w-full text-right px-2 bg-orange-100 prno" v-model="d.pr_no">
							</table>
							
							<table class="table table-bordered mb-0">
								<thead>
									<tr>
										<th class="font-xxs" width="2%">EXP Qty</th>
										<th class="font-xxs" width="2%">RCV Qty</th>
										<th class="font-xxs" width="2%">BO Qty</th>
										<th class="font-xxs" width="4%">Quantity</th>
										<th class="font-xxs" width="4%">Shipping Cost</th>
										<th class="font-xxs" width="10%">Unit Cost</th>
										<th class="font-xxs" width="2%">Currency</th>
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
							
								<tbody v-for="(it, c) in items">
									<tr v-if="d.details_id == it.dets_id">
										<td class="text-right text-xs">{{ it.exp_quantity }}</td>
										<td class="text-right text-xs">{{ it.rec_quantity }}</td>
										<td class="text-right text-xs">{{ it.bo_quantity }}</td>
										<td class="bg-orange-100 text-xs">
											<input :id="'boqty' + c" type="number" class="w-full text-right px-2 bg-orange-100 boq_" min="0" v-model="it.bo_qty" @change="BOQtyLimit(c)" onkeypress="return (event.charCode !=8 && event.charCode ==0 || (event.charCode >= 48 && event.charCode <= 57))" :style="{ color: textColor }">
											<input :id="'boq' + c" type="hidden" class="w-full text-right px-2 bg-orange-100" v-model="it.total_bo">
										</td>
										<td class="bg-orange-100 text-xs">
											<input :id="'s_cost' + c" type="number" class="w-full text-right px-2 bg-orange-100 boq_" min="0" v-model="it.shipping_cost" onkeypress="return (event.charCode !=8 && event.charCode ==0 || (event.charCode >= 48 && event.charCode <= 57))" :style="{ color: textColor }">
										</td>
										<td class="bg-orange-100 text-xs">
											<input :id="'u_cost' + c" type="number" class="w-full text-right px-2 bg-orange-100 boq_" min="0" v-model="it.unit_cost" onkeypress="return (event.charCode !=8 && event.charCode ==0 || (event.charCode >= 48 && event.charCode <= 57))" :style="{ color: textColor }">
										</td>
										<td class="bg-orange-100 text-xs">
											<select class="p-1 m-0 leading-none w-36 block text-xs whitespace-nowrap" v-model="it.currency">
												<option value="" disabled selected>Select Currency</option>
												<option v-for="cur in currency" v-bind:key="cur" v-bind:value="cur">{{  cur }}</option>
											</select>	
										</td>
										<td class="p-1 font-xxss font-bold text-center bg-green-200">{{ ((it.bo_qty *  (parseFloat(it.unit_cost) + parseFloat(it.shipping_cost)))).toFixed(2)}}</td>
										<!-- <td class="p-0 font-xxss" v-else></td> -->
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
										<td class="text-xs p-1 bg-orange-100">
											<textarea :id="'remarks_' + c" class="bg-orange-100 w-full text-xs px-2" rows="1" v-model="it.remarks"></textarea>
										</td>
										<input type="hidden" class="w-full text-right px-2 bg-orange-100" v-model="it.dets_id">
										<input type="hidden" class="w-full text-right px-2 bg-orange-100" v-model="it.variant_id">
										<input type="hidden" class="w-full text-right px-2 bg-orange-100" v-model="it.supplier_id">
										<input type="hidden" class="w-full text-right px-2 bg-orange-100" v-model="it.unit_cost">
										<input type="hidden" class="w-full text-right px-2 bg-orange-100" v-model="it.selling_price">
										<input type="hidden" class="w-full text-right px-2 bg-orange-100" v-model="it.item_status">
										<input type="hidden" class="w-full text-right px-2 bg-orange-100" v-model="it.item_status_id">
										<input type="hidden" class="w-full text-right px-2 bg-orange-100" v-model="it.pr_replenish">
										<input type="hidden" class="w-full text-right px-2 bg-orange-100" v-model="it.barcode">
										<input type="hidden" class="w-full text-right px-2 bg-orange-100" v-model="it.location">
										<input type="hidden" class="w-full text-right px-2 bg-orange-100" v-model="it.receive_items_id">
										<input type="hidden" class="w-full text-right px-2 bg-orange-100" v-model="it.pn_no">
									</tr>
								</tbody>
							</table>
							<span hidden>{{ t++ }}</span>
						</div>
						<!-- <hr class="border-dashed m-2">	 -->
						<div class="mb-2 mt-2 flex justify-end space-x-10 px-2">
							<div class="flex justify-between space-x-1">
								<!-- <a href="/" type="submit" class="btn btn-sm hover:bg-red-600 bg-red-500 text-white">Cancel Transaction</a> -->
								<!-- <a href="/back_order/print" type="submit" class="btn btn-sm btn-sm  hover:bg-blue-600 bg-blue-500 text-white w-60">Save and Print</a> -->
								<button v-if="details != undefined" @click="SaveNewBackOrder()" id = "SubmitButton" type="submit" class="btn btn-sm py-1.5 bg-blue-500 text-white hover:bg-blue-600 w-60">Save & Print</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
    </navigation>
</template>
