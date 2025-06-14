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
	let listemployeesrel = ref([])
	let listemployeesrec = ref([])
	let listemployeesnoted = ref([])
	let prepared_by=ref();
	let receivedby=ref([]);
	// let rec_position=ref();
	//let ack_position=ref();
	let notedby=ref([]);
	//let noted_position=ref();

	const props = defineProps({
        id:{
            type:String,
            default:''
        }
    })

	onMounted(async () => {
		getIssuanceHead()
		getUsers()
		getReleased()
		getReceived()
		getNoted()
	})

	const getIssuanceHead = async () => {
		let response = await axios.get(`/api/get_issuance_head/${props.id}`)
		head.value = response.data.issuancehead
		items.value = response.data.issuanceitems

		

		if(head.value.released_by!=0){
			document.getElementById('released_by').setAttribute("style","pointer-events:none");
		}else{
			document.getElementById('released_by').setAttribute("style","pointer-events:visible");
		}
		if(head.value.received_by!=0){
			document.getElementById('received_by').setAttribute("style","pointer-events:none");
		}else{
			document.getElementById('received_by').setAttribute("style","pointer-events:visible");
		}
		if(head.value.noted_by!=0){
			document.getElementById('noted_by').setAttribute("style","pointer-events:none");
		}else{
			document.getElementById('noted_by').setAttribute("style","pointer-events:visible");
		}
	}

	const getUsers = async () => {
		let response = await axios.get("/api/employee_list");
		listemployees.value=response.data.users
		
	}

	const getReleased = async () => {
		let response = await axios.get("/api/releasedemp_list");
		listemployeesrel.value=response.data.users
	}

	const getReceived = async () => {
		let response = await axios.get("/api/receiveemp_list");
		listemployeesrec.value=response.data.users
	}

	const getNoted = async () => {
		let response = await axios.get("/api/notedemp_list");
		listemployeesnoted.value=response.data.users
	}

	const printDiv = () => {
		
		const formData= new FormData()
		formData.append('id', props.id)
		formData.append('prepared_by_id', head.value.prepared_by_id)
		formData.append('prepared_by', head.value.prepared_by_name)
		// formData.append('prepared_by_pos', head.value.designation)
		formData.append('prepared_by_pos', head.value.prepared_by_pos)
		formData.append('received_by', head.value.received_by)
		formData.append('rec_position', head.value.received_by_pos)
		formData.append('released_by', head.value.released_by)
		formData.append('released_position', head.value.released_by_pos)
		formData.append('noted_by', head.value.noted_by)
		formData.append('noted_position', head.value.noted_by_pos)
		axios.post("/api/add_signatory",formData).then(function (response) {
			// if(head.value.prepared_by!=null){
			// 	document.getElementById('prepared_by').readOnly = true;
			// }else{
			// 	document.getElementById('prepared_by').readOnly = false;
			// }
			if(head.value.received_by!=0){
				document.getElementById('received_by').setAttribute("style","pointer-events:none");
			}else{
				document.getElementById('received_by').setAttribute("style","pointer-events:visible");
			}
			if(head.value.released_by!=0){
				document.getElementById('released_by').setAttribute("style","pointer-events:none");
			}else{
				document.getElementById('released_by').setAttribute("style","pointer-events:visible");
			}
			if(head.value.noted_by!=0){
				document.getElementById('noted_by').setAttribute("style","pointer-events:none");
			}else{
				document.getElementById('noted_by').setAttribute("style","pointer-events:visible");
			}

			var rec =document.getElementById('received_by').value
			var rel =document.getElementById('released_by').value
			var not =document.getElementById('noted_by').value

			if(!rel || !rec || !not){
				if(confirm("Warning: Incomplete signatories. Do you want to proceed?")){
					window.print()
				}
			}else{
				window.print()
			}
		});
	}

	const getReceiveby = async () => {
		let response = await axios.get(`/api/get_all_position/${head.value.received_by}`)
		if(head.value.received_by!=''){
			head.value.received_by_pos = response.data;	
		}else{
			head.value.received_by_pos = '';	
		}
	}

	const getReleasedby = async () => {
		let response = await axios.get(`/api/get_all_position/${head.value.released_by}`)
		if(head.value.released_by!=''){
			head.value.released_by_pos = response.data;	
		}else{
			head.value.released_by_pos = '';	
		}
	}

	const getNotedby = async () => {
		let response = await axios.get(`/api/get_all_position/${head.value.noted_by}`)
		if(head.value.noted_by!=''){
			head.value.noted_by_pos = response.data;	
		}else{
			head.value.noted_by_pos = '';	
		}
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
								<li class="breadcrumb-item active" aria-current="page">Print MIF</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>

			<div class="row" id="print_button">
				<div class="col-lg-12">
					<div class="flex justify-center mb-3 space-x-1">
						<button class="btn btn-sm btn-success" @click="printDiv()"> Print Report</button>
						<a :href="'/issue/gatepass/'+ props.id"  target="_blank" class="btn btn-sm btn-info" > Print Gate Pass</a>
					</div>
				</div>
			</div>
		
			<div class="row">
				<div class="col-lg-12">
					<div class="flex justify-center">
						<page size="A4" id="printme" class="p-2">
							<div >
								<printheader>
									MATERIAL ISSUANCE FORM
								</printheader>
							
								<table class="w-full table-bordsered">
									<tr>
										<td class="leading-tight text-sm font-bold" width="10%">MIF NO.</td>
										<td class="leading-tight text-sm font-bold" width="40%" ><span class="pr-2">:</span>{{  head.mif_no }}</td>
										<td class="leading-tight text-sm font-bold" width="10%">MREQF NO.</td>
										<td class="leading-tight text-sm font-bold" width="40%" ><span class="pr-2">:</span>{{ head.mreqf_no }}</td>
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
										<td class="text-sm text-center text-sm" width="1%">#</td>
										<td class="text-sm text-center text-sm" width="5%">Qty</td>
										<td class="text-sm text-center text-sm" width="5%">U/M</td>
										<td class="text-sm text-center text-sm" width="10%">Catalog No.</td>
										<td class="text-sm text-center text-sm" width="30%">Item Description</td> 
										<td class="text-sm text-center text-sm" width="10%">Item Cost</td>                   
										<td class="text-sm text-center text-sm" width="10%">Brand</td>
										<td class="text-sm text-center text-sm" width="10%">Serial No.</td>
										<td class="text-sm text-center text-sm" width="10%">Inv. Balance</td>
										<td class="text-sm text-center text-sm" width="10%">Remarks</td>
									</tr>
									<tr  v-for="(it,x) in items">
										<td class="text-sm align-top px-1" align="center">{{ x + 1}}</td>
										<td class="text-sm align-top px-1" align="center">{{ it.issued_qty }}</td>
										<td class="text-sm align-top px-1" align="center">{{ it.uom }}</td>
										<td class="text-sm align-top px-1" align="center">{{ it.catalog_no }}</td>
										<td class="text-sm align-top px-1" align="center">{{ it.item_description }}</td>
										<td class="text-sm align-top px-1" align="center">{{ parseFloat(it.shipping_cost  + it.unit_cost).toFixed(2) }} {{ (it.currency!=null) ? it.currency : '' }}</td>
										<td class="text-sm align-top px-1" align="center">{{ it.brand }}</td>
										<td class="text-sm align-top px-1" align="center">{{ it.serial_no }}</td>
										<td class="text-sm align-top px-1" align="center">{{ it.inventory_balance }}</td>
										<td class="text-sm align-top px-1" align="center">{{ it.remarks }}</td>
									</tr>
									
								</table>
								<table width="100%">
									<tr>
										<td class="text-sm" >Remarks:</td>
									</tr>
									<tr v-if="head.remarks =='undefined' ">
										<td class="text-sm border-b border-gray-200"><br></td>
									</tr>
									<tr v-else>
										<td class="text-sm border-b border-gray-200"><br>{{ head.remarks }}</td>
									</tr>
								</table>
								
								<table class="w-full mt-2">
									<tr>
										<td width="10%"></td>
										<td class="text-sm" width="35%">Prepared by:</td>
										<td width="10%"></td>
										<td class="text-sm" width="35%">Released by:</td>
										<td width="10%"></td>
									</tr>
									<tr>
										<td></td>
										<td class="border-b border-gray-200">
											
											<div >
												<input class="text-sm w-full text-center" type="text" readonly v-model="head.prepared_by_name">
												<input class="text-sm w-full text-center" type="hidden" v-model="head.prepared_by_id">
											</div>
										
										</td>
										<td></td>
										<td class="border-b border-gray-200">
											
											<div >
												<select  class="text-sm w-full text-center appearance-none" id="released_by" v-model="head.released_by"  @change="getReleasedby()">
													<option :value="emp.id" v-for="emp in listemployeesrel" :key="emp.id">{{ emp.name }}</option>
												</select>
											</div>	
											
										</td>
										<td></td>                
									</tr>
									<tr>
										<td></td>
										<td>
											<div v-if="head.prepared_by_name == null">
												<input class="text-sm w-full text-center" type="text" v-model="head.designation">
											</div>
											<div v-else class="text-sm w-full text-center appearance-none">
												{{ head.prepared_by_pos }}
											</div>
										</td>
										<td></td>
										<td class="text-sm text-center" style='vertical-align:top'>
											<!-- Supplier/Driver -->
											<input class="text-sm w-full text-center" v-model="head.released_by_pos">
										</td>
										<td></td>                
									</tr>
								</table>
								<table class="w-full mt-0">
									<tr>
										<td width="10%"></td>
										<td class="text-sm" width="35%">Received by:</td>
										<td width="10%"></td>
										<td class="text-sm" width="35%">Noted by:</td>
										<td width="10%"></td>
									</tr>
									<tr>
										<td></td>
										<td class="border-b border-gray-200">
											<div >
												<select class="text-sm w-full text-center appearance-none" id="received_by" v-model="head.received_by" @change="getReceiveby()">
													<option :value="emp.id" v-for="emp in listemployeesrec" :key="emp.id">{{ emp.name }}</option>
												</select>
											</div>
											
										</td>
										<td></td>
										<td class="border-b border-gray-200">
											<div >
												<select class="text-sm w-full text-center appearance-none" id="noted_by" v-model="head.noted_by" @change="getNotedby()">
													<option :value="emp.id" v-for="emp in listemployeesnoted" :key="emp.id">{{ emp.name }}</option>
												</select>
											</div>	
										
										</td>
										<td></td>                
									</tr>
									<tr>
										<td></td>
										<td>
											<div >
												<input class="text-sm w-full text-center" v-model="head.received_by_pos">
											</div>
											
											
										</td>
										<td></td>
										<td>
											<div>
												<input  class="text-sm w-full text-center" v-model="head.noted_by_pos">
											</div>
										
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

    </navigation>
</template>
