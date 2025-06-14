<script setup>
	import navigation from '@/layouts/navigation.vue';
	import { PencilSquareIcon, TrashIcon, MinusIcon, PlusIcon, MagnifyingGlassIcon, ChevronLeftIcon, ChevronRightIcon, ArrowUturnLeftIcon } from '@heroicons/vue/24/solid'
	import{ onMounted, ref } from "vue"
    import { useRouter } from "vue-router"
    const router = useRouter()
	let form = ref({
        id:'',
		source_pr:''
    })
	let error = ref([])
	let error_items = ref([])
	let success = ref('')
	let purpose = ref('')
	let enduse = ref('')
	let department = ref('')
	let receive_items = ref([])
	let receive_itemswh = ref([])
	let restock_reason = ref([])
	let restock_qty = ref([])
	let restock_qty_issue = ref([])
	let item_status = ref([])
	let restock_issued_wh=ref([]);
	let restock_no_issue_wh=ref([]);
	const isLoading = ref(true);
	const props = defineProps({
        id:{
            type:String,
            default:''
        },
		source_pr:{
            type:String,
            default:''
        }
    })
	onMounted(async () =>{
		await getRestockHead()
         getRestockReason()
		isLoading.value = false; 
		
		itemStatus()
    })


	const getRestockHead = async () => {
        let response = await axios.get(`/api/get_restock_details/${props.id}/${props.source_pr}`)
        form.value = response.data.head
        purpose.value = response.data.purpose
        enduse.value = response.data.enduser
        department.value = response.data.department
        receive_items.value = response.data.all_iss_items_whs
        receive_itemswh.value = response.data.all_no_iss_items
    }

	const getRestockReason = async () => {
        let response = await axios.get(`/api/all_restockreason`)
        restock_reason.value = response.data.restock_reason
    }

	const onSave = () => {
		if(confirm("Are you sure you want to save this transaction?")){
			const formData= new FormData()
			formData.append('restock_head_id', form.value.id)
			formData.append('source_pr', form.value.source_pr)
			formData.append('destination', form.value.destination)
			
			for(var i=0;i<receive_items.value.length;i++){
					var receive_items_id = document.getElementsByClassName("receiveitemsid_")[i].value;
					var item_id = document.getElementsByClassName("itemid_")[i].value;
					var item_desc = document.getElementsByClassName("itemdesc_")[i].value;
					var variant_id = document.getElementsByClassName("variantid_")[i].value;

					if(document.getElementsByClassName("restockqty_")[i].value ==''){
						var restock_qty = 0;
					}else {
						var restock_qty =document.getElementsByClassName("restockqty_")[i].value;
					}
					
					
					
					var item_status_id = document.getElementsByClassName("itemstatusid_")[i].value;
					var reason_id = document.getElementsByClassName("reasonid_")[i].value;
					var remarks = document.getElementsByClassName("remarks_")[i].value;
					var mif_no = document.getElementsByClassName("mif_no_")[i].value;
							const create_restock = {
								receive_items_id:receive_items_id,
								item_id:item_id,
								item_desc:item_desc,
								variant_id:variant_id,
								restock_qty:restock_qty,
								item_status_id:item_status_id,
								reason_id:reason_id,
								remarks:remarks,
								mif_no:mif_no
							}
							restock_issued_wh.value.push(create_restock)
					}
			formData.append('restock_issued_wh', JSON.stringify(restock_issued_wh.value))
				
			for(var e=0;e<receive_itemswh.value.length;e++){
					var receive_items_id_wh = document.getElementsByClassName("receiveitemsid_whs")[e].value;
					var item_id_wh = document.getElementsByClassName("itemid_whs")[e].value;
					var item_desc_wh = document.getElementsByClassName("itemdesc_whs")[e].value;
					var variant_id_wh = document.getElementsByClassName("variantid_whs")[e].value;

					if(document.getElementsByClassName("restockqty_whs")[e].value ==''){
						var restock_qty_wh = 0;
					}else {
						var restock_qty_wh =document.getElementsByClassName("restockqty_whs")[e].value;
					}

					
					//var restock_qty_wh = document.getElementsByClassName("restockqty_whs")[e].value;
					var item_status_id_wh = document.getElementsByClassName("itemstatusid_whs")[e].value;
					var reason_id_wh = document.getElementsByClassName("reasonid_whs")[e].value;
					var remarks_wh = document.getElementsByClassName("remarks_whs")[e].value;
					var mif_no = document.getElementsByClassName("mif_no_")[e].value;
							const create_restock_wh = {
								receive_items_id_wh:receive_items_id_wh,
								item_id_wh:item_id_wh,
								item_desc_wh:item_desc_wh,
								variant_id_wh:variant_id_wh,
								restock_qty_wh:restock_qty_wh,
								item_status_id_wh:item_status_id_wh,
								reason_id_wh:reason_id_wh,
								remarks_wh:remarks_wh,
								mif_no:mif_no
							}
							restock_no_issue_wh.value.push(create_restock_wh)
					}
			formData.append('restock_no_issue_wh', JSON.stringify(restock_no_issue_wh.value))
			axios.post("/api/save_restock_wh",formData).then(function (response) {
					error.value=[]
					router.push('/restock/print/'+response.data)
				}, function (err) {
			
				error.value=''
				error.value = err.response.data.error;
				success.value=''
				setTimeout(() => {
					error.value='',
					document.getElementById("error").style.display="none"
				}, 4000);
			});
		}
	}

	const cancelTransaction = (id) => {
		if(confirm("Are you sure you want to cancel transaction?")){
			axios.get(`/api/cancel_transaction_restock/${id}`).then(function () {
				router.push('/restock')
			});
		}
	}

	const checkRestockQtywhs = (counter) => {
		const no_of_rows = receive_itemswh.value.length
		let countererror = 0
		for(var x=0;x<no_of_rows;x++){
			var y = x + 1;
			// if(receive_itemswh.value[x].quantity >  receive_itemswh.value[x].pr_balance){
			var checker= parseFloat(receive_itemswh.value[x].remaining_qty)
			var restock_qtywh =document.getElementById("restock"+x).value;
			if(restock_qtywh > checker){
				document.getElementById("restock"+x).style.backgroundColor  = '#FAA0A0'
				countererror++
			} else {
				document.getElementById("restock"+x).style.backgroundColor  = '#ffedd5'
				document.getElementById("saveRestock").disabled = false; 
			}
		}
		checkQty(countererror)
	}

	const checkissuedRestockQty = (counter) => {
		const no_of_rows = receive_items.value.length
		let countererror = 0
		for(var x=0;x<no_of_rows;x++){
			var checker=parseFloat(receive_items.value[x].remaining_qty) 
			var y = x + 1;
			// if(receive_items.value[x].quantity >  receive_items.value[x].pr_balance){
			var restock_qtyiss =document.getElementById("restockiss"+x).value;
			// if(restock_qtyiss >  receive_items.value[x].issued_qty){
			if(restock_qtyiss > checker){
				document.getElementById("restockiss"+x).style.backgroundColor  = '#FAA0A0'
				countererror++
			} else {
				document.getElementById("restockiss"+x).style.backgroundColor  = '#ffedd5'
				document.getElementById("saveRestock").disabled = false; 
			}
		}
		checkQty(countererror)
	}

	const checkQty = (countererror) => {

		if(countererror>0){
			document.getElementById("saveRestock").disabled = true; 
		} else {
			document.getElementById("saveRestock").disabled = false; 
		}
	}
	
	const itemStatus = async () => {
        let response = await axios.get(`/api/itemstatus_list`)
        item_status.value = response.data.item_status
    }
</script>

<template>

    <navigation>
        <div class="container-fluid">
			<div class="card mb-3">	
				<div class="flex justify-between content-center">
					<div class="flex justify-start space-x-3 ">
						<div class="">
							<a href="/restock" class="btn btn-secondary btn-xs btn-rounded">
								<ArrowUturnLeftIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></ArrowUturnLeftIcon>
							</a>
						</div>
						<div>
							<h6 class="m-0 pt-1 font-bold uppercase">Restock</h6>
						</div>
					</div>	
					<div class="pt-1">	
						<nav aria-label="breadcrumb" role="navigation">
							<ol class="breadcrumb adminx-page-breadcrumb">
								<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
								<li class="breadcrumb-item"><a href="/restock">Restock</a></li>
								<li class="breadcrumb-item active" aria-current="page">Add New Restock</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>	

			<div>
		<div class="col-lg-4 offset-lg-4">
			<div class="flex content-center">
				<div class="hide-animates" v-if="success" id="success">
					<div class="alert alert-success alert-top my-2">
						<div class="flex justify-start space-x-1">
							<div>
								<CheckCircleIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12"></CheckCircleIcon>
							</div> 
							<div class="py-1">
								<h6 class="font-bold m-0">Success!</h6>
								<p class="text-sm m-0 text-gray-400"> {{ success }}</p>
							</div>
						</div>
					</div>
				</div>
				<div v-else id="success"></div>
				<div class="hide-animates"  v-if="error.length > 0" id="error">
					<div class="alert alert-danger alert-top my-2" >
						<div class="flex justify-start space-x-2">
							<div>
								<ExclamationCircleIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12"></ExclamationCircleIcon>
							</div> 
							<div class="py-1">
								<h6 class="font-bold m-0">Error!</h6>
								<p class="text-sm m-0 text-gray-400" > {{ error }}</p>
							</div>
						</div>
					</div>
				</div>
				<div v-else id="error"></div>
			</div>
		</div>	
	</div>
			<div class="row mb-3">
				<div class="col-md-12 col-lg-12">
					<div class="card card-main-bg">
						<div class="py-4 px-2">
							<table class="w-full table-borsdered">
								<tr>
									<td class="" width="20%">
										<div class="flex justify-start">
											<span class="text-lg uppercase font-bold text-gray-600 w-full leading-none">
												{{ form.mrs_no }}
											</span>
										</div>
										<div class="flex justify-start ">
											<span class="text-xs text-gray-500 leading-none pt-1 uppercase ">MRW NO</span>
										</div>
									</td>
									<td class="" width="20%">
										<div class="flex justify-start">
											<span class="text-lg uppercase font-bold text-gray-600 w-full leading-none">
												{{ form.source_pr }}
											</span>
										</div>
										<div class="flex justify-start ">
											<span class="text-xs text-gray-500 leading-none pt-1 uppercase ">Source PR Number</span>
										</div>
									</td>
									<td class="" width="20%">
										<div class="flex justify-start">
											<span class="text-lg uppercase font-bold text-gray-600 w-full leading-none">
												{{ form.destination }}
											</span>
										</div>
										<div class="flex justify-start" >
											<span class="text-xs text-gray-500 leading-none pt-1 uppercase">Destination</span>
										</div>
									</td>
									<td width="11%"></td>
									<td width="12%"></td>
									<td class="" width="10%">
										<div class="flex justify-end">
											<span class="text-md uppercase font-bold text-gray-600 w-full leading-none text-right">
												{{ form.date }}
											</span>
										</div>
										<div class="flex justify-end" >
											<span class="text-xs text-gray-500 leading-none pt-1">DATE</span>
										</div>
									</td>
									<td class=""  width="8%">
										<div class="flex justify-end">
											<span class="text-md uppercase font-bold text-gray-600 w-full leading-none text-right">
												{{ form.time }}
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
												{{ form.department }}
											</span>
										</div>
										<div class="flex justify-start ">
											<span class="text-xs text-gray-500 leading-none pt-1 uppercase">Department</span>
										</div>
									</td>
									<td class="" width="20%" colspan="4">
										<div class="flex justify-start">
											<span class="text-sm font-bold text-gray-600 w-full leading-none">
												{{ form.enduse }}
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
												{{ form.purpose }}
											</span>
										</div>
										<div class="flex justify-start" >
											<span class="text-xs text-gray-500 leading-none pt-1">PURPOSE</span>
										</div>
									</td>
								</tr>
							</table>
						</div>
						<div class="px-2 text-center" v-if="receive_items.length == 0 && receive_itemswh.length == 0 && !isLoading">
							<h4>There are no items to restock.</h4>
						</div>
						<div class="px-2">
                            <div v-if="receive_items.length">
                                <span colspan="13" class="text-left">Issued</span>
                            </div>
							<table v-if="receive_items.length" class="table table-actions table-bordered table-hover">
								<thead>
									<tr>
										<th class="font-xxs" width="5%">#</th>
										<th class="font-xxs" width="4%">Issued Qty</th>
										<th class="font-xxs" width="4%">Restocked Qty</th>
										<th class="font-xxs" width="4%">Remaining Qty</th>
										<th class="font-xxs" width="4%">Quantity</th>
										<th class="font-xxs" width="15%">Supplier</th>
										<th class="font-xxs" width="15%">Item Description</th>
										<th class="font-xxs" width="%">Brand</th>
										<th class="font-xxs" width="%">Cat No.</th>
										<th class="font-xxs" width="%">Serial No</th>
										<th class="font-xxs" width="%">Uom</th>
										<th class="font-xxs" width="%">Color</th>
										<th class="font-xxs" width="%">Size</th>
										<th class="font-xxs" width="10%">Item Status</th>
										<th class="font-xxs" width="15%">Reason</th>
										<th class="font-xxs" width="%">Remarks</th>
									</tr>
								</thead>
								<tbody>
									<tr v-for="(ri,index) in receive_items">
										<td class="text-xs p-0">{{ index + 1 }}</td>
										<input type="hidden" class="form-control border my-1 itemid_" v-model="ri.item_id">
										<input type="hidden" class="form-control border my-1 variantid_" v-model="ri.variant_id">
										<!-- <input type="hidden" class="form-control border my-1 oldstatusid_" v-model="ri.status_id"> -->
										<input type="hidden" class="form-control border my-1 receiveitemsid_" v-model="ri.receive_items_id">
										<td class="text-xs p-0">
											<textarea type="text" rows="2" class="p-1 m-0 w-full leading-none block" v-model="ri.issued_qty" readonly></textarea>
										</td>
										<td class="text-xs p-0">
											<textarea type="text" rows="2" class="p-1 m-0 w-full leading-none block" readonly>{{ ri.restocked_qty }}</textarea>
										</td>
										<td class="text-xs p-0">
											<textarea type="text" rows="2" class="p-1 m-0 w-full leading-none block" readonly>{{ ri.remaining_qty }}</textarea>
										</td>
										<td class="text-xs p-0">
											<textarea type="number" rows="2" class="p-1 m-0 w-full leading-none block bg-orange-100 restockqty_" :id="'restockiss' + index" v-model="restock_qty_issue[index]" @blur="checkissuedRestockQty(index)" min="0" onkeypress="return (event.charCode !=8 && event.charCode ==0 || (event.charCode >= 48 && event.charCode <= 57))"></textarea>
										</td>

										<td class="text-xs p-0">
											<textarea type="text" rows="2" class="p-1 m-0 w-full leading-none block "  v-model="ri.supplier_name" readonly></textarea>
										</td>

										<td class="text-xs p-0">
											<textarea type="text" rows="2" class="p-1 m-0 w-full leading-none block itemdesc_" v-model="ri.item_description" readonly></textarea>
										</td>

										<td class="text-xs p-0">
											<textarea type="text" rows="2" class="p-1 m-0 w-full leading-none block " v-model="ri.brand" readonly></textarea>
										</td>

										<td class="text-xs p-0">
											<textarea type="text" rows="2" class="p-1 m-0 w-full leading-none block " v-model="ri.catalog_no" readonly></textarea>
										</td>

										<td class="text-xs p-0">
											<textarea type="text" rows="2" class="p-1 m-0 w-full leading-none block " v-model="ri.serial_no" readonly></textarea>
										</td>

										<td class="text-xs p-0">
											<textarea type="text" rows="2" class="p-1 m-0 w-full leading-none block " v-model="ri.uom" readonly></textarea>
										</td>

										<td class="text-xs p-0">
											<textarea type="text" rows="2" class="p-1 m-0 w-full leading-none block " v-model="ri.color" readonly></textarea>
										</td>

										<td class="text-xs p-0">
											<textarea type="text" rows="2" class="p-1 m-0 w-full leading-none block " v-model="ri.size" readonly></textarea>
										</td>

										<td class="text-xs p-0 bg-orange-100">
											<select class="p-1 m-0 w-full leading-none block bg-orange-100 text-xs whitespace-nowrap itemstatusid_" v-model="ri.item_status_id">
												<option v-for="itm in item_status" :value="itm.id">{{  itm.status }}</option>
											</select>
										</td>

										<td class="text-xs p-0 bg-orange-100">
											<select :id="'reason_'+index" v-model="ri.reason" class="p-1 m-0 w-full leading-none block bg-orange-100 text-xs whitespace-nowrap reasonid_">
												<option :value="rr.id" v-for="rr in restock_reason" :key="rr.id">{{ rr.reason }}</option>
											</select>
										</td>

										<td class="text-xs p-0">
											<textarea :id="'remarks_'+index" v-model="ri.remarks" rows="2" class="p-1 m-0 w-full leading-none block bg-orange-100 remarks_"></textarea>
										</td>
										<input type="hidden" class="mif_no_"  v-model="ri.mif_no">
									</tr>
								</tbody>
							</table>
                            <div v-if="receive_itemswh.length">
                                <span colspan="13" class="text-left">Not Issued</span>
                            </div>
                            <table v-if="receive_itemswh.length" class="table table-actions table-bordered table-hover">
                                <thead>
									<tr>
										<th class="font-xxs" width="1%">#</th>
										
										<!-- <th class="font-xxs" width="4%" v-if="form.destination=='Warehouse Stock' && form.source_pr=='WH Stocks'">Remaining Qty</th> -->
										<th class="font-xxs" width="4%">Restocked Qty</th>
										<th class="font-xxs" width="2%">Remaining Qty</th>
										<th class="font-xxs" width="4%">Quantity</th>
										<th class="font-xxs" width="15%">Supplier</th>
										<th class="font-xxs" width="15%">Item Description</th>
										<th class="font-xxs" width="%">Brand</th>
										<th class="font-xxs" width="%">Cat No.</th>
										<th class="font-xxs" width="%">Serial No</th>
										<th class="font-xxs" width="%">Uom</th>
										<th class="font-xxs" width="%">Color</th>
										<th class="font-xxs" width="%">Size</th>
										<th class="font-xxs" width="10%">Item Status</th>
										<th class="font-xxs" width="15%">Reason</th>
										<th class="font-xxs" width="%">Remarks</th>
									</tr>
								</thead>
								<tbody>
									<tr v-for="(rw,index) in receive_itemswh">

										<td class="text-xs p-0">{{ index + 1 }}</td>
										<input type="hidden" class="form-control border my-1 itemid_whs" v-model="rw.item_id">
										<input type="hidden" class="form-control border my-1 variantid_whs" v-model="rw.variant_id">
										<!-- <input type="hidden" class="form-control border my-1 oldstatusid_whs" v-model="rw.status_id"> -->
										<input type="hidden" class="form-control border my-1 receiveitemsid_whs" v-model="rw.receive_items_id">
										<td class="text-xs p-0">
											<textarea type="text" rows="2" class="p-1 m-0 w-full leading-none block" readonly>{{ rw.restocked_qty }}</textarea>
										</td>
										<td class="text-xs p-0">
											<textarea type="text" rows="2" class="p-1 m-0 w-full leading-none block" readonly >{{ rw.remaining_qty }}</textarea>
										</td>
										<td class="text-xs p-0">
											<textarea type="number" rows="2" :id="'restock' + index" required @blur="checkRestockQtywhs(index)"  class="p-1 m-0 w-full leading-none block bg-orange-100 restockqty_whs" v-model="restock_qty[index]" min="0" onkeypress="return (event.charCode !=8 && event.charCode ==0 || (event.charCode >= 48 && event.charCode <= 57))"></textarea>
										</td>
										<td class="text-xs p-0">
											<textarea type="text" rows="2" class="p-1 m-0 w-full leading-none block "  v-model="rw.supplier_name" readonly></textarea>
										</td>
										<td class="text-xs p-0">
											<textarea type="text" rows="2" class="p-1 m-0 w-full leading-none block itemdesc_whs" v-model="rw.item_description" readonly></textarea>
										</td>
										<td class="text-xs p-0">
											<textarea type="text" rows="2" class="p-1 m-0 w-full leading-none block " v-model="rw.brand" readonly></textarea>
										</td>
										<td class="text-xs p-0">
											<textarea type="text" rows="2" class="p-1 m-0 w-full leading-none block " v-model="rw.catalog_no" readonly></textarea>
										</td>
										<td class="text-xs p-0">
											<textarea type="text" rows="2" class="p-1 m-0 w-full leading-none block " v-model="rw.serial_no" readonly></textarea>
										</td>
										<td class="text-xs p-0">
											<textarea type="text" rows="2" class="p-1 m-0 w-full leading-none block " v-model="rw.uom" readonly></textarea>
										</td>
										<td class="text-xs p-0">
											<textarea type="text" rows="2" class="p-1 m-0 w-full leading-none block " v-model="rw.color" readonly></textarea>
										</td>
										<td class="text-xs p-0">
											<textarea type="text" rows="2" class="p-1 m-0 w-full leading-none block " v-model="rw.size" readonly></textarea>
										</td>
										<td class="text-xs p-0 bg-orange-100">
											<select :id="'itemstatus_'+index" v-model="rw.item_status_id" class="p-1 m-0 w-full leading-none block bg-orange-100 text-xs whitespace-nowrap itemstatusid_whs">
												<option :value="itms.id" v-for="itms in item_status" :key="itms.id">{{ itms.status }}</option>
											</select>
										</td>
										<td class="text-xs p-0 bg-orange-100">
											<select :id="'reason_'+index" v-model="rw.reason" class="p-1 m-0 w-full leading-none block bg-orange-100 text-xs whitespace-nowrap reasonid_whs">
												<option :value="rr.id" v-for="rr in restock_reason" :key="rr.id">{{ rr.reason }}</option>
											</select>
										</td>
										<td class="text-xs p-0">
											<textarea :id="'remarks_'+index" v-model="rw.remarks" rows="2" class="p-1 m-0 w-full leading-none block bg-orange-100 remarks_whs"></textarea>
										</td>
										<input type="hidden" class="mif_no_"  v-model="rw.mif_no">
									</tr>
                                </tbody>
                            </table>
						</div>
						<hr class="border-dashed m-2">	
						<div class="mb-2 mt-2 flex justify-end space-x-10">
							<div class="flex justify-between space-x-1">
								<button @click="cancelTransaction(form.id)" class="btn btn-sm hover:bg-red-600 bg-red-500 text-white">Cancel Transaction</button>
								<button v-if="receive_items.length != 0 && receive_itemswh.length != 0" @click="onSave()" id="saveRestock" class="btn btn-sm hover:bg-blue-600 bg-blue-500 text-white w-60">Save and Print</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
    </navigation>

	 
</template>
