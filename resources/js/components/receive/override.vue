<script setup>
	import{ onMounted, ref } from "vue"
	import navigation from '@/layouts/navigation.vue';
	import { CheckCircleIcon, TrashIcon, LockClosedIcon, XMarkIcon, PencilSquareIcon, ArrowUturnLeftIcon, ExclamationTriangleIcon, CheckIcon  } from '@heroicons/vue/24/solid'
	import { useRouter } from "vue-router"

    const router = useRouter()

	let head = ref({
        id:'',
		overrideid:''
    })
	

	let details = ref([])
	let departments = ref([])
	let enduses = ref([])
	let users = ref([])
	let purposes = ref([])
	let items = ref([])
	let error_items = ref([])
	let success = ref()
	let max = ref()

	const props = defineProps({
        id:{
            type:String,
            default:''
        },
		overrideid:{
            type:String,
            default:''
        }
    })

	onMounted(async () =>{
        getReceiveHead()
		getReceiveDetails()
		getLatestDetailNo(props.id)
		getdepartment()
		getenduse()
		getpurpose()
		getUsers()
		
    })

	const getdepartment = async () => {
		let response = await axios.get("/api/department_list");
		departments.value=response.data.department
		}

	const getenduse = async () => {
		let response = await axios.get("/api/enduse_list");
		enduses.value=response.data.enduse
		}

	const getpurpose = async () => {
		let response = await axios.get("/api/purpose_list");
		purposes.value=response.data.purpose
		}

	const getUsers = async () => {
		let response = await axios.get("/api/employee_list");
		users.value=response.data.users
	}

	
	const getReceiveHead = async () => {
		let response = await axios.get(`/api/get_receive_head/${props.id}`)
		head.value = response.data.head
	}

	const getReceiveDetails = async () => {
		let response = await axios.get(`/api/get_receive_details/${props.id}`)
		details.value = response.data.details
	}

	const getLatestDetailNo = async () => {
		let response = await axios.get(`/api/get_latest_detail_no/${props.id}`)
		max.value = response.data;
		
	}

	const OverrideUpdate = (id) => {
		if(confirm("Are you sure you want to update this transaction?")){
			const formDetails = new FormData()
			formDetails.append('si_or', head.value.si_or)
			formDetails.append('override_userid', props.overrideid)
			formDetails.append('receive_details', JSON.stringify(details.value))

			// const no_of_rows = details.value.length

			// 	for(var x=0;x<no_of_rows;x++){
			// 		// alert(details.value[x].department)
			// 		if(details.value[x].department == null){
			// 			error_items.value.push('Department must not be empty.')
			// 		}
			// 		if(details.value[x].purpose == null){
			// 			error_items.value.push('Purpose must not be empty.')
			// 		}
			// 		if(details.value[x].enduse == null){
			// 			error_items.value.push('Enduse must not be empty.')
			// 		}
			// 		if(details.value[x].inspected == null){
			// 			error_items.value.push('Inspected by must not be empty.')
			// 		}
			// 	}

			// if(error_items.value.length == 0){
				axios.post('/api/override_update/'+id, formDetails).then(function (response) {
						router.push('/receive/print/'+id)
					});
			// }
		}
	}

	const updateHead = ref(false)
	const hideModal = ref(true)
	const openModalHead = () => {
		updateHead.value = !updateHead.value
	}
	const closeModalHead = () => {
		updateHead.value = !hideModal.value
		updatepr.value = !hideModal.value
	}

	const updatepr = ref(false)
	const openUpdate = () => {
		updatepr.value = !updatepr.value
	}
	const closeUpdate = () => {
		updatepr.value = !hideModal.value
	}

const onPrint= (id) => {
	router.push('/receive/print/'+id)
}

</script>

<template>
	<div>
		<div class="col-lg-4 offset-lg-4">
			<div class="flex content-center">
			</div>
		</div>	
	</div>
    <navigation>
        <div class="container-fluid">
			<!-- BreadCrumb -->
			<div class="card mb-3">	
				<div class="flex justify-between content-center">
					<div class="flex justify-start space-x-3 ">
						<div class="">
						</div>
						<div>
							<h6 class="m-0 pt-1 font-bold uppercase">Receive</h6>
						</div>
					</div>	
					<div class="pt-1">	
						<nav aria-label="breadcrumb" role="navigation">
							<ol class="breadcrumb adminx-page-breadcrumb">
								<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
								<li class="breadcrumb-item"><a href="/receive">Receive</a></li>
								<li class="breadcrumb-item active" aria-current="page">Add New Receive</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>	
 			<div class="alert alert-warning2 border-0 shadow-sm" v-if="head.closed == 0">
				<div class="flex justify-start space-x-2 ">
					<div>
						<ExclamationTriangleIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12"></ExclamationTriangleIcon>
					</div> 
					<div class="py-1">
						<h6 class="font-bold m-0 leading-none">Note </h6> 
						<span class="text-gray-400">Please review the details before closing and printing this transaction. Once closed, you will not be able to edit this transaction.</span>
					</div>
				</div>
			</div> 
			<div class="card p-4 card-main-bg">
				<div class=" border-x-2 border-blue-400">
					<div class="" v-if="error_items.length != 0">
						<div class="alert alert-warning mb-2" >
							<div class="flex justify-start space-x-2">
								<div>
									<ExclamationCircleIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"></ExclamationCircleIcon>
								</div> 
								<div class="py-1 flex justify-start space-x-2">
									<h6 class="font-bold m-0">Error! </h6>
									<div>
										<p class="text-sm m-0 text-gray-400" v-for="errit in error_items"> {{ errit }}</p>
									</div>
								</div>
							</div>
							<!-- <hr class="mb-0"> -->
						</div>
					</div>
				</div>
				<!-- <hr class="m-0 mb-2"> -->
				<div class="mb-3">
					<div class="row">
						<div class="col-lg-12">
							<table class="w-full table-bordersed mt-2 mb-2 ">
								<tr>
									<td class="pr-1" width="20%">
										<div class="flex justify-start">
											<span class="text-lg uppercase font-bold text-gray-600 w-full leading-none">
												{{ head.mrecf_no }}
											</span>
										</div>
										<div class="flex justify-start ">
											<span class="text-xs text-gray-500 leading-none pt-1">MRIF NO.</span>
										</div>
									</td>
									<td class="px-1" width="10%">
										<div class="flex justify-start">
											<span class="text-lg uppercase font-bold text-gray-600 leading-none">{{ head.receive_date }}</span>
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
											<!-- <span class="text-md uppercase font-bold text-gray-600 leading-none">{{ head.si_or }}</span> -->
											<input type="text" style="width:90%" class="border-b w-full text-sm pt-1 pl-1 mt-2 bg-orange-100 w-full" v-model="head.si_or">
										</div>
										<div class="flex justify-start" >
											<span class="text-xs text-gray-500 leading-none pt-1">SI/OR NUMBER</span>
										</div>
									</td>
									<td class="pr-1" width="15%">
										<div class="flex justify-start">
											<span class="text-lg uppercase font-bold text-gray-600 w-full leading-none">
												{{ head.waybill_no }}
											</span>
										</div>
										<div class="flex justify-start ">
											<span class="text-xs text-gray-500 leading-none pt-1">WAYBILL NO.</span>
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
										<div class="flex justify-end">
											<button @click="openModalHead()" class="btn btn-xs btn-info btn-rounded" v-if="head.closed == 0">
												<PencilSquareIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></PencilSquareIcon>
											</button>
										</div>
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>	
				
				<div v-for="det in details" class="border-2 border-blue-400 mb-3 rounded ">
					<div class="row">
						<div class="col-lg-12">
							<table class="w-full table-borsdered">
								<tr>
									<td width="2%" rowspan="3" class="align-top p-0 "> 
										<div class="pt-2 p-1 mr-2 px-2 text-md text-center bg-blue-400 font-bold pb-5 text-white">{{ det.detail_no.padStart(2, "0") }}</div>
									</td>
									<td class="pt-2 form-label" width="8%">PR Number</td>
									<td class="pt-2 px-1 text-sm border-b font-bold">{{ det.pr_no }}</td>
									<td class="pt-2 px-1 text-sm" width="3%"></td>
									<td class="pt-2 form-label" width="9%">Inspected by</td>
									<td class="pt-2 px-1 text-sm border-b">
										<select class="w-full text-sm pt-1 mt-2 bg-orange-100 w-full" v-model="det.inspected" v-if="det.pr_no != 'WH STOCKS'">
											<option v-for="user in users" v-bind:key="user.id" v-bind:value="user.id +'~'+ user.name">{{  user.name }}</option>
										</select>
										<input class="w-full text-sm pt-1 mt-2" style="pointer-events:none" v-model="det.inspected_id" v-else>
									</td>
								</tr>
								<tr>
									<td class="form-label" >Department</td>
									<td class="px-1 text-sm border-b" v-if="det.pr_no != 'WH STOCKS'">
										<input type="datalist"  class="border-b w-full text-sm pt-1 pl-1 mt-2 bg-orange-100 w-full" list="deptlist" placeholder="Department" v-model="det.department">	
										<datalist id="deptlist">
											<option v-bind:value="dept.department_name + ' #'+dept.id "  v-for="dept in departments" >{{ dept.department_name }}</option>
										</datalist>
									</td>
									<td v-else>
										<input class="border-b w-full text-sm pt-1 pl-1 mt-2" style="pointer-events:none" v-model="det.department_id">
									</td>
									<td class="px-1 text-sm" width="3%"></td>
									<td class="form-label">Enduse</td>
									<td class="px-1 text-sm border-b" colspan="2" v-if="det.pr_no != 'WH STOCKS'">
										<input type="datalist"  class="border-b w-full text-sm pt-1 pl-1 mt-2 bg-orange-100 w-full" list="enduselist" placeholder="Enduse" v-model="det.enduse" >	
										<datalist id="enduselist">
											<option v-bind:value="end.enduse_name + ' #'+end.id "  v-for="end in enduses" >{{ end.enduse_name }}</option>
										</datalist>
									</td>
									<td v-else>
										<input class="border-b w-full text-sm pt-1 pl-1 mt-2" style="pointer-events:none" v-model="det.enduse_id">
									</td>
								</tr>
								<tr>
									<td class="form-label">Purpose</td>
									<td class="px-1 text-sm border-b " colspan="5" v-if="det.pr_no != 'WH STOCKS'">
										<input type="datalist"  class="border-b w-full text-sm pt-1 pl-1 mt-2 bg-orange-100 w-full" list="purposelist" placeholder="Purpose" v-model="det.purpose" >	
										<datalist id="purposelist">
											<option v-bind:value="purp.purpose_name + ' #'+purp.id "  v-for="purp in purposes" >{{ purp.purpose_name }}</option>
										</datalist>
									</td>
									<td v-else>
										<input class="border-b w-full text-sm pt-1 pl-1 mt-2" style="pointer-events:none" v-model="det.purpose_id">
									</td>
								</tr>
								<!-- <input type="hidden" class='bg-orange-100 w-full' v-model="det.receive_head_id"> -->
							</table>
						</div>
					</div>	
					<div class="row">
						<div class="col-lg-12">
							<table class="table table-actions table-bordered table-hodver mb-0 border-t-2">
								<tbody  v-for="it in det.receive_items.items">
									<tr class="bg-gray-100">
										<td class="p-1 text-center font-bold" rowspan="5">{{ it.item_no }}</td>
										<td class="font-xxs uppercase font-bold" width="20%">Supplier</td>
										<td class="font-xxs uppercase font-bold" width="30%" colspan="2">Description</td>
										<td class="font-xxs uppercase font-bold" width="10%">Item status</td>
										<td class="font-xxs uppercase font-bold" width="15%">Shipping/U & Other Cost</td>
										<td class="font-xxs uppercase font-bold" width="7%">Unit Cost</td>
										<td class="font-xxs uppercase font-bold" width="7%">Currency</td>
										<td class="font-xxs uppercase font-bold" width="7%">Exp Qty</td>
										<td class="font-xxs uppercase font-bold" width="7%">Recv Qty</td>
										<td class="font-xxs uppercase font-bold" width="7%">Total Cost</td>
									</tr>
									<tr>
										<td class="p-1 font-xxss">{{ it.supplier_name }}</td>
										<td class="p-1 font-xxss" colspan="2">{{ it.item_description + ' - ' + it.pn_no}}</td>
										<td class="p-1 font-xxss text-left">{{ it.item_status }}</td>
										<td class="p-1 font-xxss">{{ it.shipping_cost }}</td>
										<td class="p-1 font-xxss">{{ it.unit_cost }}</td>
										<td class="p-1 font-xxss">{{ it.currency }}</td>
										<td class="p-1 font-xxss">{{ it.exp_quantity }}</td>
										<td class="p-1 font-xxss">{{ it.rec_quantity }}</td>
										<td class="p-1 font-xxss">{{ (parseFloat(it.unit_cost) + parseFloat(it.shipping_cost)) *  it.rec_quantity}}</td>
									</tr>
									<tr class="bg-gray-100">
										<td class="font-xxs uppercase font-bold" width="15%">Brand</td>
										<td class="font-xxs uppercase font-bold" width="15%">Cat No.</td>
										<td class="font-xxs uppercase font-bold" width="15%">Serial No.</td>
										<td class="font-xxs uppercase font-bold" width="1%">UOM</td>
										<td class="font-xxs uppercase font-bold" width="5%">Color</td>
										<td class="font-xxs uppercase font-bold" width="5%" colspan="2">Size</td>
										<td class="font-xxs uppercase font-bold text-left" width="5%" colspan="3">Expiry Date</td>
									</tr>
									<tr>
										<td class="p-1 font-xxss">{{ it.brand }}</td>
										<td class="p-1 font-xxss">{{ it.catalog_no }}</td>
										<td class="p-1 font-xxss">{{ it.serial_no }}</td>
										<td class="p-1 font-xxss">{{ it.uom }}</td>
										<td class="p-1 font-xxss">{{ it.color }}</td>
										<td class="p-1 font-xxss" colspan="2">{{ it.size }}</td>
										<td class="p-1 font-xxss text-left" colspan="3">{{ it.expiry_date }} </td>
										
									</tr>
									<tr>
										<td class="p-1 font-xxss text-left" colspan="4"><span class="italic">Remarks:</span> {{ it.remarks }}</td>
										<td class="p-1 font-xxss ">
											<div class="flex justify-center space-x-4">
												<div class="flex justify-center space-x-1">
													<span>Local:</span>
													<CheckCircleIcon v-if="it.location == 'local'" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-emerald-500"></CheckCircleIcon>
												</div>
												<div class="flex justify-center space-x-1">
													<span>Manila:</span>
													<CheckCircleIcon v-if="it.location == 'manila'" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-emerald-500"></CheckCircleIcon>
												</div>
											</div>
										</td>
										<td class="p-1 font-xxss text-left" colspan="4">
											<span class="italic" v-if="it.pr_replenish == 1">Replenish: Yes</span> 
											<span class="italic" v-else>Replenish: No</span> 
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<hr class="border-dashed mb-0">
				<div class="pt-3 mb-2 flex justify-end space-x-1" v-if="head.closed == 1">
					<a href="#" @click="OverrideUpdate(head.id)" type="submit" class="btn btn-sm btn-primary w-52">Save and Print</a>
				</div>
			</div>
		</div>

		<!-- MODAL -->
		<div class="modal pt-5 px-3" :class="{ show:updatepr }">
			<div @click="closeModalHead" class="w-full h-full fixed"></div>
			<div class="modal__content w-1/3 p-4">
				<div class="row">
					<div class="col-lg-12 flex justify-between">
						<p class="mb-0 font-bold">Enter password to update </p>
						<a href="#" class="text-gray-600" @click="closeModalHead">
							<XMarkIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"></XMarkIcon>
						</a>
					</div>
				</div>
				<hr class="m-0 mb-2">
				<div class="modal_s_items">
					<div class="form-group mb-2">
						<input type="text" class="form-control border" >
					</div>	
				</div> 
				<div class="flex justify-end">
					<a class="btn btn-info btn-rounded btn-sm w-32">Override</a>
				</div>		
			</div>
		</div>
    </navigation>
</template>
