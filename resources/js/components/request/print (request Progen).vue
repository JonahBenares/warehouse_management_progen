<script setup>
	import{ onMounted, ref } from "vue"
	import navigation from '@/layouts/navigation.vue';
	import printheader from '@/layouts/header.vue';
	import { CheckCircleIcon, TrashIcon, PlusIcon, XMarkIcon, PencilSquareIcon, ArrowUturnLeftIcon, ExclamationTriangleIcon, CheckIcon  } from '@heroicons/vue/24/solid'
	import { useRouter } from "vue-router"
	onMounted(async () => {
		//printDiv()
		getRequestHead()
		getRequestItems()
		getUsers()
		getRequested()
		getReviewed()
		getNoted()
		getApproved()
	})

	const router = useRouter()
	let head = ref({
		id:'',
		requested_by:'',
		noted_by:'',
		approved_by:'',
		reviewed_by:'',
	})

	let listemployees = ref([])
	let listemployeesreq = ref([])
	let listemployeesrev = ref([])
	let listemployeesnoted = ref([])
	let listemployeesapp = ref([])
	let req_items = ref([])
	let max = ref()
	let total = ref()
	let user_id=ref();
	let prepared_by=ref();
	let prep_position=ref();
	let req_position=ref();
	let rev_position=ref();
	let app_position=ref();
	let noted_position=ref();

	const props = defineProps({
        id:{
            type:String,
            default:''
        }
    })

	// const printDiv = async (divName) => {
	// 	var printContents = document.getElementById(divName).innerHTML;
	// 	var originalContents = document.body.innerHTML;
	// 	document.body.innerHTML = printContents;
	// 	window.print();
	// 	document.body.innerHTML = originalContents;
	// }

	const printDiv = (divName) => {
		const formData= new FormData()
		formData.append('id', props.id)
		formData.append('user_id', user_id.value)
		formData.append('requested_by', head.value.requested_by)
		formData.append('reviewed_by', head.value.reviewed_by)
		formData.append('approved_by', head.value.approved_by)
		formData.append('noted_by', head.value.noted_by)

		var req =document.getElementById('requested_by').value
		var rev =document.getElementById('reviewed_by').value
		var app =document.getElementById('approved_by').value
		var not =document.getElementById('noted_by').value

		// if(!req || !rev || !app || !not){
		// 	alert('Warning: Incomplete signatories.');
		// 	window.print()
		// }else{
		axios.post("/api/add_request_signatory",formData).then(function () {
			if(head.value.requested_by!=0){
				document.getElementById('requested_by').setAttribute("style","pointer-events:none");
			}else{
				document.getElementById('requested_by').setAttribute("style","pointer-events:visible");
			}
			if(head.value.reviewed_by!=0){
				document.getElementById('reviewed_by').setAttribute("style","pointer-events:none");
			}else{
				document.getElementById('reviewed_by').setAttribute("style","pointer-events:visible");
			}
			if(head.value.approved_by!=0){
				document.getElementById('approved_by').setAttribute("style","pointer-events:none");
			}else{
				document.getElementById('approved_by').setAttribute("style","pointer-events:visible");
			}
			if(head.value.noted_by!=0){
				document.getElementById('noted_by').setAttribute("style","pointer-events:none");
			}else{
				document.getElementById('noted_by').setAttribute("style","pointer-events:visible");
			}
			// window.print()
			if(!req || !rev || !app || !not){
				if(confirm("Warning: Incomplete signatories. Do you want to proceed?")){
					window.print()
				}
			}else{
				window.print()
			}
		});
	}

	const getRequestHead = async () => {
		let response = await axios.get(`/api/get_request_head/${props.id}`)
		head.value = response.data.head
		user_id.value = response.data.user_id
		prepared_by.value = response.data.prepared_by
		prep_position.value = response.data.prep_position
		req_position.value = response.data.req_position
		rev_position.value = response.data.rev_position
		app_position.value = response.data.app_position
		noted_position.value = response.data.noted_position
			// if(head.value.requested_by!=0){
			// 	document.getElementById('requested_by').setAttribute("style","pointer-events:none");
			// }else{
			// 	document.getElementById('requested_by').setAttribute("style","pointer-events:visible");
			// }
			// if(head.value.reviewed_by!=0){
			// 	document.getElementById('reviewed_by').setAttribute("style","pointer-events:none");
			// }else{
			// 	document.getElementById('reviewed_by').setAttribute("style","pointer-events:visible");
			// }
			// if(head.value.approved_by!=0){
			// 	document.getElementById('approved_by').setAttribute("style","pointer-events:none");
			// }else{
			// 	document.getElementById('approved_by').setAttribute("style","pointer-events:visible");
			// }
			// if(head.value.noted_by!=0){
			// 	document.getElementById('noted_by').setAttribute("style","pointer-events:none");
			// }else{
			// 	document.getElementById('noted_by').setAttribute("style","pointer-events:visible");
			// }
	}

	const getRequestItems = async () => {
		let response = await axios.get(`/api/get_request_items/${props.id}`)
		req_items.value = response.data.req_items
	}

	const getUsers = async () => {
		let response = await axios.get("/api/employee_list");
		listemployees.value=response.data.users
	}

	const getRequested = async () => {
		let response = await axios.get("/api/requestedemp_list");
		listemployeesreq.value=response.data.users
	}

	const getReviewed = async () => {
		let response = await axios.get("/api/reviewedemp_list");
		listemployeesrev.value=response.data.users
	}

	const getNoted = async () => {
		let response = await axios.get("/api/notedemp_list");
		listemployeesnoted.value=response.data.users
	}

	const getApproved = async () => {
		let response = await axios.get("/api/approvedemp_list");
		listemployeesapp.value=response.data.users
	}

	const getRequestedby = async () => {
		let response = await axios.get(`/api/get_request_position/${head.value.requested_by}`)
		if(head.value.requested_by!=''){
			req_position.value = response.data;
		}else{
			req_position.value = '';	
		}
	}

	const getReviewedby = async () => {
		let response = await axios.get(`/api/get_request_position/${head.value.reviewed_by}`)
		if(head.value.reviewed_by!=''){
			rev_position.value = response.data;	
		}else{
			rev_position.value = '';	
		}
	}

	const getApprovedby = async () => {
		let response = await axios.get(`/api/get_request_position/${head.value.approved_by}`)
		if(head.value.approved_by!=''){
			app_position.value = response.data;	
		}else{
			app_position.value = '';	
		}
	}

	const getNotedby = async () => {
		let response = await axios.get(`/api/get_request_position/${head.value.noted_by}`)
		if(head.value.noted_by!=''){
			noted_position.value = response.data;	
		}else{
			noted_position.value = '';	
		}
	}

</script>

<style>
@media print {
.no-btn  {
    display: none;
}
    #printme  {
    display: block;
}
}
</style>

<template>
    <navigation>
        <div class="container-fluid">
			<div class="card mb-3 no-btn">	
				<div class="flex justify-between content-center">
					<div class="flex justify-start space-x-3 ">
						<div class="no-btn">
							<a href="javascript: history.go(-1)" class="btn btn-secondary btn-xs btn-rounded">
								<ArrowUturnLeftIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></ArrowUturnLeftIcon>
							</a>
						</div>
						<div>
							<h6 class="m-0 pt-1 font-bold uppercase">Request</h6>
						</div>
					</div>	
					<div class="pt-1 no-btn">	
						<nav aria-label="breadcrumb" role="navigation">
							<ol class="breadcrumb adminx-page-breadcrumb">
								<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
								<li class="breadcrumb-item"><a href="/request">Request</a></li>
								<li class="breadcrumb-item active" aria-current="page">Print MREQF</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-lg-12 no-btn">
					<div class="flex justify-center mb-3">
						<button class="btn btn-sm btn-success" @click="printDiv('printme')"> Print Report</button>
					</div>
				</div>
			</div>
		
			<div class="row">
				<div class="col-lg-12">
					<div class="flex justify-center">
						<page size="A4" id="printme" class="p-2">
							<div >
								<printheader>
									MATERIAL REQUEST FORM
								</printheader>
								<table class="w-full table-bordsered">
									<tr>
										<td class="leading-tight text-sm font-bold" width="10%">MREQF NO.</td>
										<td class="leading-tight text-sm font-bold" width="30%" ><span class="pr-2">:</span>{{head.mreqf_no}}</td>
										<td class="leading-tight text-sm font-bold" width="8%">JO/PR NO.</td>
										<td class="leading-tight text-sm font-bold" width="26%" ><span class="pr-2">:</span>{{head.pr_no}}</td>
										<td class="leading-tight text-sm" width="5%">DATE/TIME</td>
										<td class="leading-tight text-sm text-right" width="15%"><span class="pr-2">:</span>{{head.request_date}} {{head.request_time}}</td>
									</tr>
									<tr>
										<td class="leading-tight text-sm">DEPARTMENT</td>
										<td class="leading-tight text-sm"><span class="pr-2">:</span>{{head.department_name}}</td>
										<td class="leading-tight text-sm">ENDUSE</td>
										<td class="leading-tight text-sm" colspan="3"><span class="pr-2">:</span>{{head.enduse_name}}</td>
									</tr>
									<tr>
										<td class="leading-tight text-sm">PURPOSE</td>
										<td class="leading-tight text-sm" colspan="6"><span class="pr-2">:</span>{{head.purpose_name}}</td>
									</tr>
								</table>
								<table width="100%" class="table-bordered mt-2">
									<tr>
										<td class="leading-tight text-sm" width="1%" align="center">#</td>
										<td class="leading-tight text-sm" width="5%" align="center">Qty</td>
										<td class="leading-tight text-sm" width="10%" align="center">UOM</td>
										<td class="leading-tight text-sm" width="20%" align="center">Part No.</td>
										<td class="leading-tight text-sm" width="30%" align="center">Item Description</td>  
									</tr>
									<tr v-for="(it, r) in req_items">
										<td class="leading-tight text-sm align-top px-1" align="center">{{ r + 1 }}</td>
										<td class="leading-tight text-sm align-top px-1" align="center">{{ it.req_qty }}</td>
										<td class="leading-tight text-sm align-top px-1" align="center">{{ it.uom }}</td>
										<td class="leading-tight text-sm align-top px-1" align="left">{{ it.pn_no }}</td>
										<td class="leading-tight text-sm align-top px-1" align="left" v-if="it.compose_id != 0">{{ it.item_description }} - {{ it.compose_itemname }}</td>
										<td class="leading-tight text-sm align-top px-1" align="left" v-else>{{ it.item_description }}</td>
									</tr>
								</table>
								<table width="100%">
									<tr>
										<td class="text-sm" >Remarks:</td>
									</tr>
									<tr>
										<td class="text-sm border-b border-gray-200"><br> {{ head.remarks }}</td>
									</tr>
								</table>
								
								<table class="w-full mt-2">
									<tr>
										<!--  -->
										<td width="10%"></td>       
										<td class="text-sm" width="35%">Prepared By:</td>             
									
										<td width="10%"></td>
										<td class="text-sm" width="35%">Requested by:</td>
										<!-- <td class="text-sm" width="35%">Reviewed by:</td> -->
									</tr>
									<tr>
										<td ></td>
										<td class="border-b border-gray-200 p-0">
											<input v-if="head.prepared_by_name==null" class="text-sm w-full text-center" name="" v-model="prepared_by" readonly >
											<input v-else class="text-sm w-full text-center" name="" v-model="head.prepared_by_name" readonly >
										</td>     
										<td></td>
										<td class="border-b border-gray-200 p-0">
											<select class="text-sm w-full text-center appearance-none" id="requested_by" v-model="head.requested_by" @change="getRequestedby()" v-if="head.requested_by_name==null">
												<option :value="emp.id" v-for="emp in listemployeesreq" :key="emp.id">{{ emp.name }}</option>
											</select>
											<input id="requested_by" class="text-sm w-full text-center" style="pointer-events:none" v-model="head.requested_by_name" v-else>
										</td>
										<td ></td>
										
									</tr>
									<tr>
										<td ></td>
										<td>
											<!-- <select class="text-sm w-full text-center appearance-none" >
												<option value = "">Select Your Designation Here..</option>
												<option value = "">Accounting Staff</option>
												<option value = "">Asset and Warehouse Manager</option>
												<option value = "">Parts Inventory Assistant</option>
												<option value = "">Projects and Asset Management Assistant</option>
												<option value = "">Warehouse Assistant</option>
												<option value = "">Warehouse Supervisor</option>
											</select> -->
											<input v-if="head.prepared_by_position==null" class="text-sm w-full text-center" style="pointer-events:none" v-model="prep_position">
											<input v-else class="text-sm w-full text-center" style="pointer-events:none" v-model="head.prepared_by_position" >
										</td>  
										<td></td>
										<!-- <td class="text-sm text-center">End-User/Requester</td> -->
										<td class="text-sm text-center">
											<input v-if="head.requested_by_position==null" class="text-sm w-full text-center" style="pointer-events:none" v-model="req_position">
											<input v-else class="text-sm w-full text-center" style="pointer-events:none" v-model="head.requested_by_position" >
										</td>
										<td ></td>
									
									</tr>
								</table>
								
								<table class="w-full mt-2">
									<tr>
										<td width="10%"></td>
										<td class="text-sm" width="35%">Reviewed by:</td>
										<td width="10%"></td>
										<td class="text-sm" width="35%">Noted by:</td>
										<td width="10%"></td>
									</tr>
									<tr>
										
										<!-- <td class="border-b border-gray-200">
											<select class="text-sm w-full text-center appearance-none" id="approved_by" v-model="head.approved_by" @change="getApprovedby()" v-if="head.approved_by_name==null">
												<option :value="emp.id" v-for="emp in listemployeesapp" :key="emp.id">{{ emp.name }}</option>
											</select>
											<input id="approved_by" class="text-sm w-full text-center" style="pointer-events:none" v-model="head.approved_by_name" v-else>
										</td> -->
										<td></td>
										<td class="border-b border-gray-200 p-0">
											<select class="text-sm w-full text-center appearance-none" id="reviewed_by" v-model="head.reviewed_by" @change="getReviewedby()" v-if="head.reviewed_by_name==null">
												<option :value="emp.id" v-for="emp in listemployeesrev" :key="emp.id">{{ emp.name }}</option>
											</select>
											<input id="reviewed_by" class="text-sm w-full text-center" style="pointer-events:none" v-model="head.reviewed_by_name" v-else>
										</td>         
										
										<td></td>  
										<td class="border-b border-gray-200">
											<select class="text-sm w-full text-center appearance-none" id="noted_by" v-model="head.noted_by" @change="getNotedby()" v-if="head.noted_by_name==null">
												<option :value="emp.id" v-for="emp in listemployeesnoted" :key="emp.id">{{ emp.name }}</option>
											</select>
											<input id="noted_by" class="text-sm w-full text-center" style="pointer-events:none" v-model="head.noted_by_name" v-else>
										</td>           
									</tr>
									<tr>
									
										<!-- <td>
											<input v-if="head.approved_by_position==null" class="text-sm w-full text-center" style="pointer-events:none" v-model="app_position">
											<input v-else class="text-sm w-full text-center" style="pointer-events:none" v-model="head.approved_by_position" >
										</td> -->
										<td></td>
										<td>
											<input v-if="head.noted_by_position==null" class="text-sm w-full text-center" style="pointer-events:none" v-model="noted_position">
											<input v-else class="text-sm w-full text-center" style="pointer-events:none" v-model="head.noted_by_position" >
										</td>
										<td></td>  
										<td>
											<input v-if="head.reviewed_by_position==null" class="text-sm w-full text-center" style="pointer-events:none" v-model="rev_position">
											<input v-else class="text-sm w-full text-center" style="pointer-events:none" v-model="head.reviewed_by_position" >
										</td>              
									</tr>
								</table>
							</div>
						</page>
					</div>
				</div>
			</div>
		</div>

    </navigation>
</template>
