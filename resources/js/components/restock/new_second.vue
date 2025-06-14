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
	let restock_reason = ref([])
	let restock_qty = ref([])
	let item_status = ref([])
	let restock_issued=ref([]);
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
		isLoading.value = false; 
        getRestockReason()
		itemStatus()
    })
	const getRestockHead = async () => {
        let response = await axios.get(`/api/get_restock_details/${props.id}/${props.source_pr}`)
        form.value = response.data.head
        purpose.value = response.data.purpose
        enduse.value = response.data.enduser
        department.value = response.data.department
        receive_items.value = response.data.all_iss_items
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
			// formData.append('destination', form.value.destination)
			// formData.append('receive_items', JSON.stringify(receive_items.value))
			// formData.append('restock_qty', restock_qty.value)
			for(var i=0;i<receive_items.value.length;i++){
					var receive_items_id = document.getElementsByClassName("receiveitemsid_")[i].value;
					var item_id = document.getElementsByClassName("itemid_")[i].value;
					var item_desc = document.getElementsByClassName("itemdesc_")[i].value;
					var variant_id = document.getElementsByClassName("variantid_")[i].value;
					var restock_qty = document.getElementsByClassName("restockqty_")[i].value;
					var item_status_id = document.getElementsByClassName("itemstatusid_")[i].value;
					var mif_no = document.getElementsByClassName("mif_no_")[i].value;
					// var old_status_id = document.getElementsByClassName("oldstatusid_")[i].value;
					var reason_id = document.getElementsByClassName("reasonid_")[i].value;
					var remarks = document.getElementsByClassName("remarks_")[i].value;
							const create_restock = {
								receive_items_id:receive_items_id,
								item_id:item_id,
								item_desc:item_desc,
								variant_id:variant_id,
								restock_qty:restock_qty,
								item_status_id:item_status_id,
								// old_status_id:old_status_id,
								reason_id:reason_id,
								remarks:remarks,
								mif_no:mif_no
							}
								restock_issued.value.push(create_restock)
					}
			formData.append('restock_issued', JSON.stringify(restock_issued.value))
			axios.post("/api/save_restock",formData).then(function (response) {
				error.value=[]
				router.push('/restock/print/'+response.data)
			}, function (err) {
				error.value=[]
				error.value.push('Error try again!')
				document.getElementById("error").style.display="block"
				setTimeout(() => {
					document.getElementById("error").style.display="none"
				}, 4000);
			// console.log(response.data)
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

	// const checkRestockQty = (counter) => {
	// 	const no_of_rows = receive_items.value.length
	// 	let countererror = 0
	// 	for(var x=0;x<no_of_rows;x++){
	// 		var y = x + 1;
	// 		// if(receive_items.value[x].quantity >  receive_items.value[x].pr_balance){
	// 		var restock_qty =document.getElementById("restock"+x).value;
	// 		if(restock_qty >  receive_items.value[x].pr_balance){
	// 			document.getElementById("restock"+x).style.backgroundColor  = '#FAA0A0'
	// 			countererror++
	// 		} else {
	// 			document.getElementById("restock"+x).style.backgroundColor  = '#ffedd5'
	// 			document.getElementById("saveRestock").disabled = false; 
	// 		}
	// 	}
	// 	checkQty(countererror)
	// }

	const checkissuedRestockQty = (counter) => {
		const no_of_rows = receive_items.value.length
		
		let countererror = 0
		for(var x=0;x<no_of_rows;x++){
			var checker= parseFloat(receive_items.value[x].remaining_qty)
			var y = x + 1;
			// if(receive_items.value[x].quantity >  receive_items.value[x].pr_balance){
			var restock_qty =document.getElementById("restockiss"+x).value;
			// if(restock_qty >  receive_items.value[x].issued_qty){
			if(restock_qty > checker){
				document.getElementById("restockiss"+x).style.backgroundColor  = '#FAA0A0'
				countererror++
			} else {
				document.getElementById("restockiss"+x).style.backgroundColor  = '#ffedd5'
				document.getElementById("saveRestock").disabled = false; 
			}
		}
		checkQty(countererror)
	}

	// const checkRestockQtywh = (counter) => {
	// 	const no_of_rows = receive_items.value.length
		
	// 	let countererror = 0
	// 	for(var x=0;x<no_of_rows;x++){
	// 		var y = x + 1;
	// 		// if(receive_items.value[x].quantity >  receive_items.value[x].pr_balance){
	// 		var restock_qty =document.getElementById("restockiss"+x).value;
	// 		var check_qty = document.getElementById("check_qty"+x).value;
	// 		if(restock_qty >  check_qty){
	// 			document.getElementById("restockiss"+x).style.backgroundColor  = '#FAA0A0'
	// 			countererror++
	// 		} else {
	// 			document.getElementById("restockiss"+x).style.backgroundColor  = '#ffedd5'
	// 			document.getElementById("saveRestock").disabled = false; 
	// 		}
	// 	}
	// 	checkQty(countererror)
	// }

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
						<div class="px-2">	
							<table class="table table-actions table-bordered table-hover">
								<thead>
									<tr>
										<th class="font-xxs" width="1%">#</th>
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
									<tr v-if="receive_items.length > 0" v-for="(ri,index) in receive_items">
										<td class="text-xs p-0">{{ index + 1 }}</td>
										<input type="hidden" class="form-control border my-1 itemid_" v-model="ri.item_id">
										<input type="hidden" class="form-control border my-1 variantid_" v-model="ri.variant_id">
										<input type="hidden" class="form-control border my-1 oldstatusid_" v-model="ri.status_id">
										<input type="hidden" class="form-control border my-1 receiveitemsid_" v-model="ri.receive_items_id">
										<td class="text-xs p-0">
											<textarea type="text" rows="2" class="p-1 m-0 w-full leading-none block" v-model="ri.issued_qty" readonly></textarea>
										</td>
										<td class="text-xs p-0">
											<textarea type="text" rows="2" class="p-1 m-0 w-full leading-none block" readonly>{{ ri.restocked_qty }}</textarea>
										</td>
										<td class="text-xs p-0">
											<textarea type="text" rows="2" :id="'check_qty'+index" class="p-1 m-0 w-full leading-none block" readonly>{{ ri.remaining_qty }}</textarea>
										</td>
										<td class="text-xs p-0" >
											<textarea type="number" rows="2" class="p-1 m-0 w-full leading-none block bg-orange-100 restockqty_" :id="'restockiss' + index" v-model="restock_qty[index]" @blur="checkissuedRestockQty(index)" min="0" onkeypress="return (event.charCode !=8 && event.charCode ==0 || (event.charCode >= 48 && event.charCode <= 57))"></textarea>
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
											<select :id="'itemstatus_'+index" v-model="ri.item_status_id" class="p-1 m-0 w-full leading-none block bg-orange-100 text-xs whitespace-nowrap itemstatusid_">
												<option :value="itm.id" v-for="itm in item_status" :key="itm.id">{{ itm.status }}</option>
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
										<!-- <td class="text-xs px-2">
											<button class="btn btn-xs btn-danger btn-rounded" >
												<TrashIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></TrashIcon>
											</button>
										</td> -->
										<input type="hidden" class="mif_no_"  v-model="ri.mif_no">
									</tr>
									<tr v-else-if="receive_items.length == 0 && !isLoading">
										<td colspan="19" class="text-center">There are no items to restock.</td>
									</tr>
								</tbody>
							</table>
						</div>
						<hr class="border-dashed m-2">	
						<div class="mb-2 mt-2 flex justify-end space-x-10">
							<div class="flex justify-between space-x-1">
								<button @click="cancelTransaction(form.id)" class="btn btn-sm hover:bg-red-600 bg-red-500 text-white">Cancel Transaction</button>
								<button v-if="receive_items.length > 0" @click="onSave()" id="saveRestock" class="btn btn-sm hover:bg-blue-600 bg-blue-500 text-white w-60">Save and Print</button>
							</div>
						</div>
					</div>
				</div>
				
			</div>
		</div>
    </navigation>

	 
</template>
