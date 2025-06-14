<script setup>
	import{ onMounted, ref } from "vue"
	import navigation from '@/layouts/navigation.vue';
	import printheader from '@/layouts/header.vue';
	import { CheckCircleIcon, TrashIcon, PlusIcon, XMarkIcon, PencilSquareIcon, ArrowUturnLeftIcon, ExclamationTriangleIcon, CheckIcon  } from '@heroicons/vue/24/solid'
	import { useRouter } from "vue-router"


	const router = useRouter()
	let head = ref({
		id:''
	})
	let items = ref([])
	let listemployees = ref([])
	let listemployeesreq = ref([])
	let listemployeesrec = ref([])
	let listemployeesnoted = ref([])
	let listemployeesapp = ref([])
	let listemployeesins = ref([])
	// let prepared_by=ref();
	// let receivedby=ref([]);
	 //let rec_position=ref();
	 //let ins_position=ref();
	// let notedby=ref([]);
	 //let app_position=ref();

	const props = defineProps({
        id:{
            type:String,
            default:''
        }
    })

	onMounted(async () => {
		getIssuanceHead()
		getUsers()
		getRequested()
		getRecommend()
		getNoted()
		getApproved()
		getInspected()
		
	})

	const getIssuanceHead = async () => {
		let response = await axios.get(`/api/get_issuance_head/${props.id}`)
		head.value = response.data.issuancehead
		items.value = response.data.issuanceitems

		if(head.value.requested_by!=0){
			document.getElementById('requested_by').setAttribute("style","pointer-events:none");
		}else{
			document.getElementById('requested_by').setAttribute("style","pointer-events:visible");
		}
		if(head.value.recommended_by!=0){
			document.getElementById('recommended_by').setAttribute("style","pointer-events:none");
		}else{
			document.getElementById('recommended_by').setAttribute("style","pointer-events:visible");
		}
		if(head.value.approved_by!=0){
			document.getElementById('approved_by').setAttribute("style","pointer-events:none");
		}else{
			document.getElementById('approved_by').setAttribute("style","pointer-events:visible");
		}
		if(head.value.inspected_by!=0){
			document.getElementById('inspected_by').setAttribute("style","pointer-events:none");
		}else{
			document.getElementById('inspected_by').setAttribute("style","pointer-events:visible");
		}
		if(head.value.noted_by_gp!=0){
			document.getElementById('noted_by').setAttribute("style","pointer-events:none");
		}else{
			document.getElementById('noted_by').setAttribute("style","pointer-events:visible");
		}
	}

	const getUsers = async () => {
		let response = await axios.get("/api/employee_list");
		listemployees.value=response.data.users
		
	}

	const getRequested = async () => {
		let response = await axios.get("/api/requestedemp_list");
		listemployeesreq.value=response.data.users
	}

	const getRecommend = async () => {
		let response = await axios.get("/api/recommendemp_list");
		listemployeesrec.value=response.data.users
	}

	const getNoted = async () => {
		let response = await axios.get("/api/notedemp_list");
		listemployeesnoted.value=response.data.users
	}

	const getApproved = async () => {
		let response = await axios.get("/api/approvedemp_list");
		listemployeesapp.value=response.data.users
	}

	const getInspected = async () => {
		let response = await axios.get("/api/inspectedemp_list");
		listemployeesins.value=response.data.users
	}

	const getRequestedby = async() => {
		let response = await axios.get(`/api/get_all_position/${head.value.requested_by}`)
		if(head.value.requested_by!=''){
			head.value.requested_by_pos = response.data;	
		}else{
			head.value.requested_by_pos = '';	
		}
	}

	const getRecommendedby = async() => {
		let response = await axios.get(`/api/get_all_position/${head.value.recommended_by}`)
		if(head.value.recommended_by!=''){
			head.value.recommended_by_pos = response.data;	
		}else{
			head.value.recommended_by_pos = '';	
		}
	}

	const getInspectedby = async() => {
		let response = await axios.get(`/api/get_all_position/${head.value.inspected_by}`)
		if(head.value.inspected_by!=''){
			head.value.inspected_by_pos = response.data;	
		}else{
			head.value.inspected_by_pos = '';	
		}
	}

	const getApprovedby = async() => {
		let response = await axios.get(`/api/get_all_position/${head.value.approved_by}`)
		if(head.value.approved_by!=''){
			head.value.approved_by_pos = response.data;	
		}else{
			head.value.approved_by_pos = '';	
		}
	}

	const getNotedby = async() => {
		let response = await axios.get(`/api/get_all_position/${head.value.noted_by_gp}`)
		if(head.value.noted_by_gp!=''){
			head.value.noted_by_pos_gp = response.data;	
		}else{
			head.value.noted_by_pos_gp = '';	
		}
	}

	const printDiv = () => {
		
		const formData= new FormData()
		formData.append('id', props.id)
		formData.append('contractor', head.value.contractor)
		formData.append('requested_by', head.value.requested_by)
		formData.append('req_position', head.value.requested_by_pos)
		// formData.append('requested_by_name', head.value.requested_by_name ?? '')
		formData.append('recommended_by', head.value.recommended_by)
		formData.append('rec_position', head.value.recommended_by_pos)
		formData.append('approved_by', head.value.approved_by)
		formData.append('app_position', head.value.approved_by_pos)
		formData.append('inspected_by', head.value.inspected_by)
		formData.append('ins_position', head.value.inspected_by_pos)
		formData.append('noted_by_gp', head.value.noted_by_gp)
		formData.append('noted_by_pos_gp', head.value.noted_by_pos_gp)
		axios.post("/api/add_signatory_gp",formData).then(function (response) {
			if(head.value.requested_by!=0){
				document.getElementById('requested_by').setAttribute("style","pointer-events:none");
			}else{
				document.getElementById('requested_by').setAttribute("style","pointer-events:visible");
			}
			if(head.value.recommended_by!=0){
				document.getElementById('recommended_by').setAttribute("style","pointer-events:none");
			}else{
				document.getElementById('recommended_by').setAttribute("style","pointer-events:visible");
			}
			if(head.value.approved_by!=0){
				document.getElementById('approved_by').setAttribute("style","pointer-events:none");
			}else{
				document.getElementById('approved_by').setAttribute("style","pointer-events:visible");
			}
			if(head.value.inspected_by!=0){
				document.getElementById('inspected_by').setAttribute("style","pointer-events:none");
			}else{
				document.getElementById('inspected_by').setAttribute("style","pointer-events:visible");
			}
			if(head.value.noted_by_gp!=0){
			document.getElementById('noted_by').setAttribute("style","pointer-events:none");
			}else{
				document.getElementById('noted_by').setAttribute("style","pointer-events:visible");
			}

			var req =document.getElementById('requested_by').value
			var rec =document.getElementById('recommended_by').value
			var app =document.getElementById('approved_by').value
			var ins =document.getElementById('inspected_by').value
			var not =document.getElementById('noted_by').value	

			if(!req || !rec || !app || !ins || !not){
				if(confirm("Warning: Incomplete signatories. Do you want to proceed?")){
					window.print()
				}
			}else{
				window.print()
			}
			getIssuanceHead()
		});
	}

</script>

<template>
    <navigation>
        <div class="container-fluid">
			<div class="card mb-3" id="print_card">	
				<div class="flex justify-between content-center">
					<div class="flex justify-start space-x-3 ">
						<div class="">
							<a href="/issue" class="btn btn-secondary btn-xs btn-rounded">
								<ArrowUturnLeftIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></ArrowUturnLeftIcon>
							</a>
						</div>
						<div>
							<h6 class="m-0 pt-1 font-bold uppercase">Issue</h6>
						</div>
					</div>	
					<div class="pt-1">	
						<nav aria-label="breadcrumb" role="navigation">
							<ol class="breadcrumb adminx-page-breadcrumb">
								<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
								<li class="breadcrumb-item"><a href="/issue">Issue</a></li>
								<li class="breadcrumb-item active" aria-current="page">Print Gate Pass</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>

			<div class="row" id="print_button">
				<div class="col-lg-12">
					<div class="flex justify-center mb-3 space-x-1">
						<button class="btn btn-sm btn-success" @click="printDiv()"> Print Gatepass</button>
					</div>
				</div>
			</div>
		
			<div class="row">
				<div class="col-lg-12">
					<div class="flex justify-center">
						<page size="A4" id="printme" class="p-2">
							<div >
								<printheader>
									MATERIAL GATE PASS FORM
								</printheader>
								<table class="w-full table-bordsered my-2  border-b">
									<tr>
										<td class="leading-tight text-sm font-bold" width="18%">EMPLOYEE/ CONTRACTOR</td>
										<td class="leading-tight text-sm" width="80%" >
											<div class="flex justify-between">
												<span class="">:</span>
												<select class="text-sm w-full appearance-none tex bg-gray-50 pl-2" v-model="head.contractor">
													<option >Select Employee</option>
													<option :value="emp.id" v-for="emp in listemployees" :key="emp.id">{{ emp.name }}</option>
												</select>
											</div>
										</td>
									</tr>
								</table>
							
								<table class="w-full table-bordsered">
									<tr>
										<td class="leading-tight text-sm font-bold" width="10%">MGP NO.</td>
										<td class="leading-tight text-sm font-bold" width="40%" ><span class="pr-2">:</span>{{ head.mgp_no }}</td>
										<td class="leading-tight text-sm font-bold" width="10%">MIF NO.</td>
										<td class="leading-tight text-sm font-bold" width="40%" ><span class="pr-2">:</span>{{ head.mif_no }}</td>
									</tr>
									<tr>
										<td class="leading-tight text-sm font-bold">JO/PR</td>
										<td class="leading-tight text-sm font-bold"><span class="pr-2">:</span>{{ head.pr_no }}</td>
										<td class="leading-tight text-sm">DATE/TIME</td>
										<td class="leading-tight text-sm"><span class="pr-2">:</span>{{ head.issuance_date }} {{ head.issuance_time }}</td>
									</tr>
									<tr>
										<td class="leading-tight text-sm">DEPARTMENT</td>
										<td class="leading-tight text-sm"><span class="pr-2">:</span>{{ head.department_name }}</td>
										<td class="leading-tight text-sm">ENDUSE</td>
										<td class="leading-tight text-sm" colspan="3"><span class="pr-2">:</span>{{ head.enduse_name }}</td>
									</tr>
									<tr>
										<td class="leading-tight text-sm">PURPOSE</td>
										<td class="leading-tight text-sm" colspan="6"><span class="pr-2">:</span>{{ head.purpose_name }}</td>
									</tr>
								</table>
								<table width="100%" class="table-bordered mt-2">
									<tr>
										<td class="text-sm " width="1%" align="center">#</td>
										<td class="text-sm " width="5%" align="center">Qty</td>
										<td class="text-sm " width="5%" align="center">U/M</td>
										<td class="text-sm " width="10%" align="center">Cat No./SN</td>
										<td class="text-sm px-1" width="60%" align="left">Item Description</td>  
									</tr>
									<tr  v-for="(it,x) in items">
										<td class="text-sm align-top px-1" align="center">{{ x + 1 }}</td>
										<td class="text-sm align-top px-1" align="center">{{ it.issued_qty }}</td>
										<td class="text-sm align-top px-1" align="center">{{ it.uom }}</td>
										<td class="text-sm align-top px-1" align="center">{{ it.catalog_no}}</td>
										<td class="text-sm align-top px-1" align="left">{{ it.item_description }}</td>
									</tr>
								</table>
								<table width="100%">
									<tr>
										<td class="text-sm" >Remarks:</td>
									</tr>
									<tr>
										<td class="text-sm border-b border-gray-200">{{ head.remarks }}</td>
									</tr>
								</table>
								
								<table class="w-full mt-2">
									<tr>
										<td class="text-sm" width="30%">Prepared By:</td>
										<td width="5%"></td>                    
										<td class="text-sm" width="30%">Requested by:</td>
										<td width="5%"></td>
										<td class="text-sm" width="30%">Recommending Approval:</td>
									</tr>
									<tr>
										<td class="border-b text-sm text-center border-gray-200 p-0">
											{{ head.prepared_by_name }}
										</td>     
										<td></td>
									
										<td class="border-b border-gray-200 p-0">
											<!-- <input class="text-sm w-full text-center" type="text" id="requested_by" v-model="head.requested_by_name" autocomplete="off"> -->
											<select class="text-sm w-full text-center appearance-none" id="requested_by" v-model="head.requested_by"  @change="getRequestedby()">
												<option :value="emp.id" v-for="emp in listemployeesreq" :key="emp.id">{{ emp.name }}</option>
											</select>
										</td>
										
										<td></td>
										<td class="border-b border-gray-200 p-0">
											<select class="text-sm w-full text-center appearance-none" id="recommended_by" v-model="head.recommended_by"  @change="getRecommendedby()">
												<option :value="emp.id" v-for="emp in listemployeesrec" :key="emp.id">{{ emp.name }}</option>
											</select>
										</td>   
										       
									</tr>
									<tr>
										<td class="text-sm text-center">
											{{ head.prepared_by_pos }}
										</td>  
										<td></td>
										<td class="text-sm text-center">
											<input class="text-sm w-full text-center" style="pointer-events:none" v-model="head.requested_by_pos">
										</td>
										<td></td>
										<td >
											<input id="recommended_by" class="text-sm w-full text-center" style="pointer-events:none" v-model="head.recommended_by_pos">
										</td>
										
									</tr>
								</table>
								<table class="w-full mt-2">
									<tr>
										<td class="text-sm" width="30%">Noted By:</td>
										<td width="5%"></td>                    
										<td class="text-sm" width="30%">Approved by:</td>
										<td width="5%"></td>
										<td class="text-sm" width="30%">Inspected by:</td>
									</tr>
									<tr>
										<td class="border-b text-sm text-center border-gray-200 p-0">
											<select class="text-sm w-full text-center appearance-none" id="noted_by" v-model="head.noted_by_gp" @change="getNotedby()">
												<option :value="emp.id" v-for="emp in listemployeesnoted" :key="emp.id">{{ emp.name }}</option>
											</select>
										</td>     
										<td></td>
										<td class="border-b border-gray-200 p-0" >
											<select class="text-sm w-full text-center appearance-none" id="approved_by" v-model="head.approved_by" @change="getApprovedby()">
												<option :value="emp.id" v-for="emp in listemployeesapp" :key="emp.id">{{ emp.name }}</option>
											</select>
										</td>
										
										<td></td>
										<td class="border-b border-gray-200 p-0">
											<select class="text-sm w-full text-center appearance-none" id="inspected_by" v-model="head.inspected_by" @change="getInspectedby()">
												<option :value="emp.id" v-for="emp in listemployeesins" :key="emp.id">{{ emp.name }}</option>
											</select>
										</td>       
										  
									</tr>
									<tr>
										<td class="text-sm text-center">
											<input id="position" class="text-sm w-full text-center" style="pointer-events:none" v-model="head.noted_by_pos_gp">
										</td>  
										<td></td>
										<td>
											<input id="position" class="text-sm w-full text-center" style="pointer-events:none" v-model="head.approved_by_pos">
										</td>
										
										<td></td>
										<td >
											<input id="position" class="text-sm w-full text-center" style="pointer-events:none" v-model="head.inspected_by_pos">
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
