<script setup>
	import{ onMounted, ref } from "vue"
	import navigation from '@/layouts/navigation.vue';
	import printheader from '@/layouts/header.vue';
	import { ArrowUturnLeftIcon, XMarkIcon } from '@heroicons/vue/24/solid'
	import { useRouter } from "vue-router"

	onMounted(async () =>{
        getBorrowHead()
		getBorrowDetails()
		//getLatestDetailNo(props.id)
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
        reviewed_by:'',
        approved_by:'',
        noted_by:'',
    })

	const showModalRequest = ref(false)
	const showModalIssuance = ref(false)
	const hideModal = ref(true)
	const openModalRequest = () => {
		showModalRequest.value = !showModalRequest.value
	}
	const openModalIssuance = () => {
		showModalIssuance.value = !showModalIssuance.value
	}
	const closeModal = () => {
		showModalRequest.value = !hideModal.value
		showModalIssuance.value = !hideModal.value
	}

	let details = ref([])
	let listemployees = ref([])
	let listemployeesreq = ref([])
	let listemployeesapp = ref([])
	let listemployeesnoted = ref([])
	let listemployeesrev = ref([])
	let user_id=ref();
	let prepared_by=ref();
	let prep_position=ref();
	let req_position=ref();
	let rev_position=ref();
	let app_position=ref();
	let noted_position=ref();
	let request_list = ref([])
	let issuance_list = ref([])

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
		formData.append('noted_by',  head.value.noted_by)
		formData.append('req_position', req_position.value ?? '')
		formData.append('rev_position',  rev_position.value ?? '')
		formData.append('app_position',  app_position.value ?? '')
		formData.append('noted_position',  noted_position.value ?? '')

		var req =document.getElementById('requested_by').value
		var rev =document.getElementById('reviewed_by').value
		var app =document.getElementById('approved_by').value
		var not =document.getElementById('noted_by').value

		// if(!req || !rev || !app || !not){
		// 	alert('Warning: Incomplete signatories.');
		// 	window.print()
		// }
		axios.post("/api/add_borrow_signatory",formData).then(function () {
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
	
	const getBorrowHead = async () => {
		let response = await axios.get(`/api/get_borrow_head/${props.id}`)
		head.value = response.data.head
		request_list.value = response.data.request_items
		issuance_list.value = response.data.issuance_items
		user_id.value = response.data.user_id
		prepared_by.value = response.data.prepared_by
		prep_position.value = response.data.prep_position
		req_position.value = response.data.req_position
		rev_position.value = response.data.rev_position
		app_position.value = response.data.app_position
		noted_position.value = response.data.noted_position
	}

	const getBorrowDetails = async () => {
		let response = await axios.get(`/api/get_print_details/${props.id}`)
		details.value = response.data.details
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
		let response = await axios.get(`/api/get_borrow_position/${head.value.requested_by}`)
		if(head.value.requested_by!=''){
			req_position.value = response.data;
		}else{
			req_position.value = '';	
		}
	}

	const getReviewedby = async () => {
		let response = await axios.get(`/api/get_borrow_position/${head.value.reviewed_by}`)
		if(head.value.reviewed_by!=''){
			rev_position.value = response.data;	
		}else{
			rev_position.value = '';	
		}
	}

	const getApprovedby = async () => {
		let response = await axios.get(`/api/get_borrow_position/${head.value.approved_by}`)
		if(head.value.approved_by!=''){
			app_position.value = response.data;	
		}else{
			app_position.value = '';	
		}
	}

	const getNotedby = async () => {
		let response = await axios.get(`/api/get_borrow_position/${head.value.noted_by}`)
		if(head.value.noted_by!=''){
			noted_position.value = response.data;	
		}else{
			noted_position.value = '';	
		}
	}

	// const showReqTransaction = (id) => {
	// 	window.open('/request/show/'+id, '_blank');
	// 	}

</script>

<template>
    <navigation>
        <div class="container-fluid">
			<div class="card mb-3" id="print_card">	
				<div class="flex justify-between content-center">
					<div class="flex justify-start space-x-3 ">
						<div class="">
							<a href="javascript: history.go(-1)" class="btn btn-secondary btn-xs btn-rounded">
								<ArrowUturnLeftIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></ArrowUturnLeftIcon>
							</a>
						</div>
						<div>
							<h6 class="m-0 pt-1 font-bold uppercase">Borrow</h6>
						</div>
					</div>	
					<div class="pt-1">	
						<nav aria-label="breadcrumb" role="navigation">
							<ol class="breadcrumb adminx-page-breadcrumb">
								<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
								<li class="breadcrumb-item"><a href="/borrow">Borrow</a></li>
								<li class="breadcrumb-item active" aria-current="page">Print MBF</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>

			<div class="row" id="print_button">
				<div class="col-lg-6 offset-lg-3">
					<div class="flex justify-center mb-3 space-x-1">
						<!-- <span class=" w-60">
							<select name="" id="" class="form-control">
								<option>View Request</option><option :value="req.id"  v-for="req in request_list" :key="req.id">{{ req.mreqf_no }}</option>
							</select>
						</span> -->
						<!-- <button @click="openModalRequest()" class="btn btn-sm btn-primary" > View Request</button> -->
						<a href="#" class="btn btn-sm btn-primary" @click="openModalRequest()">View Request</a>
						<a href="#" class="btn btn-sm btn-primary" @click="openModalIssuance()">View Issuance</a>
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
									MATERIAL BORROW FORM
								</printheader>
								<table class="w-full table-bordesred">
									<tr>
										<td class="leading-tight text-sm font-bold" width="11%">MBR NO.</td>
										<td class="leading-tight text-sm font-bold" width="26%" ><span class="pr-2">:</span>{{ head.mbr_no}}</td>
										<td class="leading-tight text-sm font-bold" width="5%">DATE/TIME</td>
										<td class="leading-tight text-sm font-bold" width="30%"><span class="pr-2">:</span>{{ head.borrow_date}}, {{ head.borrow_time}}</td>
									</tr>
									<tr>										
										<td class="leading-tight text-sm font-bold">BORROWER'S NAME</td>
										<td class="leading-tight text-sm font-bold"><span class="pr-2">:</span>{{ head.borrowed_by_user_name}}</td>
									</tr>
								</table>
								<div class="mt-2 border-t-2 p-2" v-for="(det, c) in details">
									<table class="w-full table-bordersed">
										<tr>
											<td class="leading-tight text-sm" width="10%">DEPARTMENT</td>
											<td class="leading-tight text-sm" width="40%"><span class="pr-2">:</span>{{ det.department}}</td>
											<td class="leading-tight text-sm" width="10%">ENDUSE</td>
											<td class="leading-tight text-sm" colspan="3"><span class="pr-2">:</span>{{ det.enduse}}</td>
										</tr>
										<tr>
											<td class="leading-tight text-sm">PURPOSE</td>
											<td class="leading-tight text-sm" colspan="6"><span class="pr-2">:</span>{{ det.purpose}}</td>
										</tr>
									</table>
									<table width="100%" class="table-bordered">
										<tr>
											<td class="leading-tight text-sm" width="1%" align="center">#</td>
											<td class="leading-tight text-sm" width="5%" align="center">Qty Borrowed</td>
											<td class="leading-tight text-sm" width="30%" align="left">Item Description</td>  
											<td class="leading-tight text-sm" width="10%" align="left">Borrowed By</td>
											<td class="leading-tight text-sm" width="10%" align="left">Borrowed From</td>
										</tr>
										<tr v-for="(it, c) in det.borrow_items.items">
											<td class="leading-tight text-sm align-top px-1" align="center">{{ c + 1 }}</td>
											<td class="leading-tight text-sm align-top px-1" align="center">{{ it.quantity }}</td>
											<td class="leading-tight text-sm align-top px-1" align="left">{{ it.item_description }}</td>
											<td class="leading-tight text-sm align-top px-1" align="left">{{ it.borrowed_by }}</td>
											<td class="leading-tight text-sm align-top px-1" align="left">{{ it.borrowed_from }}</td>
										</tr>
									</table>
								
								<table width="100%">
									<tr>
										<td class="text-sm" >Remarks:</td>
									</tr>
									<tr>
										<td class="text-sm border-b border-gray-200"><br> {{  det.remarks }}</td>
									</tr>
								</table>
							</div>
								<table class="w-full mt-2">
									<tr>
										<td class="text-sm" width="30%">Prepared By:</td>
										<td width="5%"></td>                    
										<td class="text-sm" width="30%">Requested by:</td>
										<td width="5%"></td>
										<td class="text-sm" width="30%">Reviewed by:</td>
									</tr>
									<tr>
										<td class="border-b border-gray-200 p-0">
											<input v-if="head.prepared_by_name==null || head.prepared_by_name==''" class="text-sm w-full text-center" name="" v-model="prepared_by" readonly >
											<input v-else class="text-sm w-full text-center" name="" v-model="head.prepared_by_name" readonly >
										</td>     
										<td></td>
										<td class="border-b border-gray-200 p-0">
											<select class="text-sm w-full text-center appearance-none" id="requested_by" v-model="head.requested_by" @change="getRequestedby()" v-if="head.requested_by_name==null || head.requested_by_name==''">
												<option :value="emp.id" v-for="emp in listemployeesreq" :key="emp.id">{{ emp.name }}</option>
											</select>
											<input id="requested_by" class="text-sm w-full text-center" style="pointer-events:none" v-model="head.requested_by_name" v-else>
										</td>
										<td></td>
										<td class="border-b border-gray-200 p-0">
											<select class="text-sm w-full text-center appearance-none" id="reviewed_by" v-model="head.reviewed_by" @change="getReviewedby()" v-if="head.reviewed_by_name==null || head.reviewed_by_name==''">
												<option :value="emp.id" v-for="emp in listemployeesrev" :key="emp.id">{{ emp.name }}</option>
											</select>
											<input id="reviewed_by" class="text-sm w-full text-center" style="pointer-events:none" v-model="head.reviewed_by_name" v-else>
										</td>           
									</tr>
									<tr>
										<td>
											<input v-if="head.prepared_by_position==null || head.prepared_by_position==''" class="text-sm w-full text-center" style="pointer-events:none" v-model="prep_position">
											<input v-else class="text-sm w-full text-center" style="pointer-events:none" v-model="head.prepared_by_position" >
										</td>  
										<td></td>
										<td class="text-sm text-center">
											<input v-if="head.requested_by_position==null || head.requested_by_position==''" class="text-sm w-full text-center" style="pointer-events:none" v-model="req_position">
											<input v-else class="text-sm w-full text-center" style="pointer-events:none" v-model="head.requested_by_position" >
										</td>
										<td></td>
										<td>
											<input v-if="head.reviewed_by_position==null || head.reviewed_by_position==''" class="text-sm w-full text-center" style="pointer-events:none" v-model="rev_position">
											<input v-else class="text-sm w-full text-center" style="pointer-events:none" v-model="head.reviewed_by_position" >
										</td>
									</tr>
								</table>
								
								<table class="w-full mt-2">
									<tr>
										<td width="10%"></td>
										<td class="text-sm" width="35%">Approved by:</td>
										<td width="10%"></td>
										<td class="text-sm" width="35%">Noted by:</td>
										<td width="10%"></td>
									</tr>
									<tr>
										<td></td>
										<td class="border-b border-gray-200">
											<select class="text-sm w-full text-center appearance-none" id="approved_by" v-model="head.approved_by" @change="getApprovedby()" v-if="head.approved_by_name==null || head.approved_by_name==''">
												<option :value="emp.id" v-for="emp in listemployeesapp" :key="emp.id">{{ emp.name }}</option>
											</select>
											<input id="approved_by" class="text-sm w-full text-center" style="pointer-events:none" v-model="head.approved_by_name" v-else>
										</td>
										<td></td>
										<td class="border-b border-gray-200">
											<select class="text-sm w-full text-center appearance-none" id="noted_by" v-model="head.noted_by" @change="getNotedby()" v-if="head.noted_by_name==null || head.noted_by_name==''">
												<option :value="emp.id" v-for="emp in listemployeesnoted" :key="emp.id">{{ emp.name }}</option>
											</select>
											<input id="noted_by" class="text-sm w-full text-center" style="pointer-events:none" v-model="head.noted_by_name" v-else>
										</td>
										<td></td>                
									</tr>
									<tr>
										<td></td>
										<td>
											<input v-if="head.approved_by_position==null || head.approved_by_position==''" class="text-sm w-full text-center" style="pointer-events:none" v-model="app_position">
											<input v-else class="text-sm w-full text-center" style="pointer-events:none" v-model="head.approved_by_position" >
										</td>
										<td></td>
										<td>
											<input v-if="head.noted_by_position==null || head.noted_by_position==''" class="text-sm w-full text-center" style="pointer-events:none" v-model="noted_position">
											<input v-else class="text-sm w-full text-center" style="pointer-events:none" v-model="head.noted_by_position" >
										</td>
										<td></td>                
									</tr>
								</table>
							</div>
						</page>
					</div>
				</div>
			</div>
		</div>
		<Transition>
			<div class="modal p-0 !bg-transparent" :class="{ show:showModalRequest }">
				<div @click="closeModal" class="w-full h-full fixed"></div>
				<div class="flex justify-end">
					<div class="modal__content !m-0 !top-44 w-96 side-modal p-0 rounded-tb-md ">
						<div class="bg-blue-500 p-2 rounded-tl-md">
							<div class="col-lg-12 flex justify-between">
								<span class="text-white">Request</span>
								<a href="#" class="text-gray-100" @click="closeModal">
									<XMarkIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"></XMarkIcon>
								</a>
							</div>
						</div>
						<div class="h-96 overflow-y-scroll">
							<div v-for="req in request_list">
								<!-- <a @click="showReqTransaction(req.id)" class="cursor-pointer"> -->
									<a :href="`/request/show/`+req.id " class="cursor-pointer" target="_blank">
									<div class="px-4 py-2 hover:bg-gray-100"  >
										<span class="font-bold">{{ req.pr_no }} </span> -  {{ req.mreqf_no }}
									</div> 
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</Transition>
		<Transition>
			<div class="modal p-0 !bg-transparent" :class="{ show:showModalIssuance }">
				<div @click="closeModal" class="w-full h-full fixed"></div>
				<div class="flex justify-end">
					<div class="modal__content !m-0 !top-44 w-96 side-modal p-0 rounded-tb-md ">
						<div class="bg-blue-500 p-2 rounded-tl-md">
							<div class="col-lg-12 flex justify-between">
								<span class="text-white">Issuance</span>
								<a href="#" class="text-gray-100" @click="closeModal">
									<XMarkIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"></XMarkIcon>
								</a>
							</div>
						</div>
						<div class="h-96 overflow-y-scroll">
							<div v-for="is in issuance_list"> <!-- diri loop -->
								<a :href="`/issue/show/`+is.id " class="cursor-pointer" target="_blank">
									<div class="px-4 py-2 hover:bg-gray-100"  >
										<span class="font-bold">{{ is.pr_no }}</span> -  {{ is.mif_no }}
									</div> 
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</Transition>
    </navigation>
</template>
