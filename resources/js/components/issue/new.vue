<script setup>
	import navigation from '@/layouts/navigation.vue';
	import { PencilSquareIcon, TrashIcon, PlusIcon, MagnifyingGlassIcon, ChevronLeftIcon, ChevronRightIcon, ArrowUturnLeftIcon } from '@heroicons/vue/24/solid'
	import axios from 'axios';
	import {onMounted, ref} from "vue";
	import { useRouter } from "vue-router";
	const router = useRouter()
	let form=ref([])
	let items=ref([])
	let requestno=ref([])
	let error =ref([])
	let success = ref('')
	let error_items = ref([])
	let textColor = ref()

	onMounted(async () => {
		requestheadform()
	})

	const requestheadform = async () => {
		let response = await axios.get("/api/create_issuance_head");
		form.value = response.data.formData;
		requestno.value = response.data.request_nos;

	}

	const getRequestData = (event) => { 
		//let response =  axios.get("/api/get_request_data/"+event.target.value);

		axios.get(`/api/get_request_data/`+event.target.value).then(function (response) {
			form.value = response.data.formData
			items.value = response.data.itemData
			//console.log(response.data)
		});
		
	}

	const checkIssueQty = (counter) => {
		

		const no_of_rows = items.value.length
		let countererror = 0
		for(var x=0;x<no_of_rows;x++){
			var y = x + 1;
			if(items.value[x].issue_qty >  items.value[x].request_qty || items.value[x].issue_qty >  items.value[x].instock || items.value[x].issue_qty >  items.value[x].pr_qty){

				document.getElementById("issue"+x).style.backgroundColor  = '#FAA0A0'
				countererror++
			} else {
			
				document.getElementById("issue"+x).style.backgroundColor  = '#ffedd5'
				document.getElementById("saveIssue").disabled = false; 
			}
		}
		
		checkQty(countererror)
	}

	const checkQty = (countererror) => {

		if(countererror>0){
			document.getElementById("saveIssue").disabled = true; 
		} else {
			document.getElementById("saveIssue").disabled = false; 
		}
	}
	const onSave = () => {
	
		if(confirm("Are you sure you want to save this transaction?")){
			const formData= new FormData()
			formData.append('mreqf_no', form.value.mreqf_no)
			formData.append('request_head_id', form.value.request_head_id)
			formData.append('issuance_date', form.value.issuance_date)
			formData.append('issuance_time', form.value.issuance_time)
			formData.append('mif_no', form.value.mif_no)
			formData.append('pr_no', form.value.pr_no)
			formData.append('enduse_id', form.value.enduse_id)
			formData.append('enduse_name', form.value.enduse_name)
			formData.append('department_id', form.value.department_id)
			formData.append('department_name', form.value.department_name)
			formData.append('purpose_id', form.value.purpose_id)
			formData.append('purpose_name', form.value.purpose_name)
			formData.append('user_id', form.value.user_id)
			formData.append('prepared_by', form.value.prepared_by)
			formData.append('prepared_by_name', form.value.prepared_by_name)
			formData.append('prepared_by_pos', form.value.prepared_by_pos)
			formData.append('remarks', form.value.remarks)
			formData.append('issue_items', JSON.stringify(items.value))
			
			const no_of_rows = items.value.length

			
			axios.post("/api/save_issuance",formData).then(function (response) {
			
				
					error.value=[]
					//success.value='Successfully saved!'
					router.push('/issue/print/'+response.data)
					
			}, function (err) {
				error.value=[]
				document.getElementById("error").style.display="block"
				if (err.response.data.errors.mreqf_no) {
					error.value.push(err.response.data.errors.mreqf_no[0])
				}
				if (err.response.data.errors.issuance_date) {
					error.value.push(err.response.data.errors.issuance_date[0])
				}
				if (err.response.data.errors.issuance_time) {
					error.value.push(err.response.data.errors.issuance_time[0])
				}
				if (err.response.data.errors.mif_no) {
					error.value.push(err.response.data.errors.mif_no[0])
				}
				setTimeout(() => {
					document.getElementById("error").style.display="none"
				}, 4000);
			});
		}
		
	}
	
</script>

<template>
	<!-- <p class="text-success" v-if="success">{{ success }}</p>
	<p class="text-danger" v-for="err in error" v-if="error.length > 0">{{ err }}</p> -->
	<div class="col-lg-4 offset-lg-4">
		<div class="flex content-center">
			<div class="hide-animate" v-if="success" id="success">
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
			<div class="hide-animate" v-if="error.length > 0" id="error">
				<div class="alert alert-danger alert-top my-2" >
					<div class="flex justify-start space-x-2">
						<div>
							<ExclamationCircleIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12"></ExclamationCircleIcon>
						</div> 
						<div class="py-1">
							<h6 class="font-bold m-0">Error!</h6>
							<p class="text-sm m-0 text-gray-400" v-for="err in error"> {{ err }}</p>
						</div>
					</div>
				</div>
			</div>
			<div v-else id="error"></div>
		</div>
	</div>
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
							<h6 class="m-0 pt-1 uppercase font-bold">Issue</h6>
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
							<div class="pt-3 p-2">
								<div class="row">
									<div class="col-lg-12">
									
										<table class="w-full table-borsdered ">
											<tr>
												<td width="25%" class="px-1">
													<div class="flex justify-start ">
														<span class="text-xs text-gray-500 leading-none">MIF NO.</span>
													</div>
													<span class="text-lg uppercase font-bold text-gray-700 w-full leading-none">
														<input type="text" class="form-control border my-1" disabled v-model="form.mif_no">
													</span>
												</td>
												<td width="25%" class="px-1">
													<div class="flex justify-start ">
														<span class="text-xs text-gray-500 leading-none">MREQF NO.</span>
													</div>
													<span class="text-lg text-gray-700 w-full leading-none">
														<input type="datalist" class=" form-control border w-full text-sm " list="requestno" v-model="form.mreqf_no" @blur="getRequestData($event)">	
														<datalist id="requestno">
															<option :value="req.mreqf_no"  v-for="req in requestno" :key="req.mreqf_no">{{ req.mreqf_no }}</option>
														</datalist>

														<!-- <select type="text"  class="form-control border my-1" placeholder="" @change="getRequestData($event)" v-model="form.mreqf_no">
															<option value="0">-- Select MReqF Number --</option>
															<option v-for="req in requestno" :value="req.mreqf_no"  :key="req.mreqf_no">{{ req.mreqf_no }}</option>
														</select> -->
													</span>
												</td>
												<td width="10%"  class="px-1">
													<div class="flex justify-start ">
														<span class="text-xs text-gray-500 leading-none">DATE</span>
													</div>
													<div class="text-lg text-gray-700 w-full leading-none">
														<input type="date" class="form-control border my-1" v-model="form.issuance_date" >
													</div>
												</td>
												<td width="10%" class="px-1">
													<div class="flex justify-start ">
														<span class="text-xs text-gray-500 leading-none">TIME</span>
													</div>
													<div class="text-lg text-gray-700 w-full leading-none">
														<input type="time" class="form-control border my-1" v-model="form.issuance_time">
													</div>
												</td>
											</tr>
											<tr>
												<td class="px-1">
													<div class="flex justify-start ">
														<span class="text-xs text-gray-500 leading-none">DEPARTMENT</span>
													</div>
													<div class="text-lg text-gray-700 w-full leading-none">
														<input type="text" class="form-control border my-1 bg-white" disabled v-model="form.department_name">
													</div>
												</td>
												<td class="px-1">
													<div class="flex justify-start ">
														<span class="text-xs text-gray-500 leading-none">END-USE</span>
													</div>
													<div class="text-lg text-gray-700 w-full leading-none">
														<input type="text" class="form-control border my-1 bg-white" disabled v-model="form.enduse_name">
													</div>
												</td>
												<td class="px-1" colspan="2">
													<div class="flex justify-start ">
														<span class="text-xs text-gray-500 leading-none">JO/PR NUMBER</span>
													</div>
													<div class="text-lg text-gray-700 w-full leading-none">
														<input type="text" class="form-control border my-1 bg-white" disabled v-model="form.pr_no">
													</div>
												</td>
											</tr>
											<tr>
												<td class="px-1" colspan="2">
													<div class="flex justify-start ">
														<span class="text-xs text-gray-500 leading-none">PURPOSE</span>
													</div>
													<div class="text-lg text-gray-700 w-full leading-none">
														<input type="text" class="form-control border my-1 bg-white" disabled v-model="form.purpose_name">
													</div>
												</td>
												<td class="px-1" colspan="2">
													<div class="flex justify-start ">
														<span class="text-xs text-gray-500 leading-none">REMARKS</span>
													</div>
													<div class="text-lg text-gray-700 w-full leading-none">
														<!-- <input type="text" class="form-control border my-1 bg-white" disabled v-model="form.purpose"> -->
														<textarea type="text" rows="1"  class="form-control border my-1 bg-white" v-model="form.remarks"></textarea>
														<input type="hidden" class="text-sm w-full " v-model="form.request_head_id">
														<input type="hidden" class="text-sm w-full " v-model="form.user_id">
														<input type="hidden" class="text-sm w-full " v-model="form.prepared_by">
														<input type="hidden" class="text-sm w-full " v-model="form.prepared_by_name">
														<input type="hidden" class="text-sm w-full " v-model="form.prepared_by_pos">
														<input type="hidden" class="form-control border my-1 bg-white"  v-model="form.department_id">
														<input type="hidden" class="form-control border my-1 bg-white"  v-model="form.enduse_id">
														<input type="hidden" class="form-control border my-1 bg-white"  v-model="form.purpose_id">
													</div>
												</td>
											</tr>
										</table>
										
									</div>
								</div>
							</div>
							<div class="p-2">
								<div class="row">
									<div class="col-lg-12">
										<table class="table table-bordered mx-1">
											<tbody class="border-b-2 border-gray-300" v-for="(it,x) in items">
												<tr>
													<td class="text-sm text-center bg-gray-100 font-bold align-top " rowspan="5" width="2%">{{ x + 1 }}</td>
													<th class="font-xxs uppercase bg-gray-100 py-1 text-center" width="3%">In Stock</th>
													<th class="font-xxs uppercase bg-gray-100 py-1 text-center" width="3%">PR Qty</th>
													<th class="font-xxs uppercase bg-gray-100 py-1 text-center" width="4%">Req Qty</th>
													<th class="font-xxs uppercase bg-gray-100 py-1 text-center" width="3%">Qty</th>
													<th class="font-xxs uppercase bg-gray-100 py-1 text-center" width="5%">Item Cost</th>
													<th class="font-xxs uppercase bg-gray-100 py-1 text-center" width="5%">Currency</th>
													<th class="font-xxs uppercase bg-gray-100 py-1 text-center" width="5%">Total Cost</th>
													<th class="font-xxs uppercase bg-gray-100 py-1" width="15%" colspan="4">Supplier</th>
													<th class="font-xxs uppercase bg-gray-100 py-1" width="25%" colspan="4">Item Description</th>
													<th class="font-xxs uppercase bg-gray-100 py-1 text-center" width="5%">Item Status</th>
												</tr>
												<tr>
													<input type="hidden" placeholder="0" v-model="it.composite_item_id" >
													<input type="hidden" placeholder="0" v-model="it.variant_id" >
													<input type="hidden" placeholder="0" v-model="it.item_description" >
													<input type="hidden" placeholder="0" v-model="it.request_items_id" >
													<input type="hidden" placeholder="0" v-model="it.unit_cost" >
													<input type="hidden" placeholder="0" v-model="it.currency" >
													<input type="hidden" placeholder="0" v-model="it.shipping_cost" >
													<td class="text-xs text-center align-middle p-0 px-2">
														<input type="text" class="w-full  p-1 text-center align-middle" v-model="it.instock" disabled>
													</td>
													<td class="text-xs text-center align-middle p-0 px-2">
														<input type="text" class="w-full  p-1 text-center align-middle" v-model="it.pr_qty" disabled>
													</td>
													<td class="text-xs text-center align-middle p-0 px-2">
														<input type="text"  class="w-full  p-1 text-center align-middle" v-model="it.request_qty" disabled>
													</td>
													<td class="text-xs p-0 px-2 bg-orange-100" >
														<input type="text" placeholder="0" class="w-full h-full bg-orange-100 p-1 text-center align-middle" :id="'issue' + x" v-model="it.issue_qty" required @blur="checkIssueQty(x)" :style="{ color: textColor }">
													</td>
													<td class="text-xs text-center align-middle p-0 px-2">{{ (parseFloat(it.shipping_cost) + parseFloat(it.unit_cost))}}</td>
													<td class="text-xs text-center align-middle p-0 px-2">{{ (it.currency!=null) ? it.currency : '' }}</td>
													<td class="text-xs text-center align-middle p-0 px-2">{{ ((parseFloat(it.shipping_cost) + parseFloat(it.unit_cost)) * it.issue_qty).toFixed(2)}}</td>
													<td class="text-xs align-middle p-0 px-2" colspan="4">{{ it.supplier }}</td>
													<td class="text-xs align-middle p-0 px-2" colspan="4">{{ it.item_description_display }}</td>
													<td class="text-xs text-center">{{ it.item_status }}</td>
												</tr>
												<tr>
													<td class="font-xxs font-bold uppercase bg-gray-100 py-1" colspan="3">UOM</td>
													<td class="font-xxs font-bold uppercase bg-gray-100 py-1" colspan="3">Cat. No.</td>
													<td class="font-xxs font-bold uppercase bg-gray-100 py-1" colspan="4">Brand</td>
													<td class="font-xxs font-bold uppercase bg-gray-100 py-1" colspan="3" width="12%">Serial No</td>
													<td class="font-xxs font-bold uppercase bg-gray-100 py-1" colspan="" width="12%">Color</td>
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
													<td class="font-xxs uppercase bg-orange-100 py-0 align-middle" colspan="13">
														<div class="flex justify-start align-middle space-x-1">
															<span class="font-bold">Remarks:</span>
															<textarea v-model="it.remarks" class="w-full text-xs bg-orange-100 !py-0 h-full" rows="1" ></textarea>
														</div>
													</td>
													<td class="font-xxs  uppercase align-middle" colspan="3">
														<div class="flex justify-start align-middle space-x-1">
															<span class="font-bold ">Expiry Date:</span>
															<span class=" text-xs">{{ it.expiry_date }}</span>
														</div>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<hr class="border-dashed m-2">	
							<div class="mb-2 mt-2 flex justify-end space-x-10">
								<div class="flex justify-between space-x-1">
									<button @click="onSave()" class="btn btn-sm hover:bg-blue-600 bg-blue-500 text-white w-60" id="saveIssue">Save and Print</button>
								</div>
							</div>
							<!-- <div class="pt-3 mb-2 mt-2 border-t flex justify-end space-x-10">
								<div class="flex justify-between space-x-1">
									<button @click="onSave()" class="btn btn-sm btn-primary btn-rounded w-32" id="saveIssue">Save and Print</button>
								</div>
							</div> -->
						</div>
					</div>
				</div>
			
		</div>
    </navigation>
</template>
