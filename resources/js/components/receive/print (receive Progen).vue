<script setup>
	import{ onMounted, ref } from "vue"
	import navigation from '@/layouts/navigation.vue';
	import printheader from '@/layouts/header.vue';
	import { CheckCircleIcon, TrashIcon, PlusIcon, XMarkIcon, PencilSquareIcon, ArrowUturnLeftIcon, ExclamationTriangleIcon, CheckIcon  } from '@heroicons/vue/24/solid'
	import { useRouter } from "vue-router"
	// onMounted(async () => {
	// 	//printDiv()
	// })
	onMounted(async () =>{
        getReceiveHead()
		getReceiveDetails()
		getLatestDetailNo(props.id)
		getUsers()
		getReceived()
		getAcknowledge()
		getNoted()
		getTime()
		getPrintables()
    })

    const router = useRouter()

	let head = ref({
        id:'',
		delivered_by:'',
		received_by:'',
		acknowledged_by:'',
		noted_by:''
		
    })
	

	let details = ref([])
	let listemployees = ref([])
	let listemployeesrec = ref([])
	let listemployeesack = ref([])
	let listemployeesnoted = ref([])
	let items = ref([])
	let error = ref([])
	let success = ref()
	let max = ref()
	let total = ref()
	let user_id=ref();
	let timestamp=ref();
	let prepared_by=ref();
	let prepared_position=ref();
	let printed_by=ref();
	let receivedby=ref([]);
	let rec_position=ref();
	let acknowledgedby=ref([]);
	let ack_position=ref();
	let notedby=ref([]);
	let noted_position=ref();
	let request_id = ref('')
	let issuance_id = ref('')

	const props = defineProps({
        id:{
            type:String,
            default:''
        }
    })
	
	const getTime = async() => {
		const today = new Date();
		const date = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
		const time = today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();
		const dateTime = date +' '+ time;
		timestamp.value = dateTime;

	}
	const printDiv = () => {
		const formData= new FormData()
		formData.append('id', props.id)
		formData.append('user_id', user_id.value)
		formData.append('prepared_by', prepared_by.value)
		formData.append('prepared_position', prepared_position.value)
		formData.append('received_by', head.value.received_by)
		formData.append('rec_position', rec_position.value)
		formData.append('acknowledge_by', head.value.acknowledged_by)
		formData.append('ack_position', ack_position.value)
		formData.append('noted_by', head.value.noted_by)
		formData.append('noted_position', noted_position.value)
		formData.append('delivered_by', head.value.delivered_by)


	
		axios.post("/api/add_signatory_receive",formData).then(function () {
			if(head.value.delivered_by){
				document.getElementById('delivered_by').readOnly = true;
			}else{
				document.getElementById('delivered_by').readOnly = false;
			}
			if(head.value.received_by!=0){
				document.getElementById('received_by').setAttribute("style","pointer-events:none");
			}else{
				document.getElementById('received_by').setAttribute("style","pointer-events:visible");
			}
			if(head.value.acknowledged_by!=0){
				document.getElementById('acknowledged_by').setAttribute("style","pointer-events:none");
			}else{
				document.getElementById('acknowledged_by').setAttribute("style","pointer-events:visible");
			}
			if(head.value.noted_by!=0){
				document.getElementById('noted_by').setAttribute("style","pointer-events:none");
			}else{
				document.getElementById('noted_by').setAttribute("style","pointer-events:visible");
			}
			var del =document.getElementById('delivered_by').value
			var rec =document.getElementById('received_by').value
			var ack =document.getElementById('acknowledged_by').value
			var not =document.getElementById('noted_by').value	

			if(!del || !rec || !ack || !not){
				if(confirm("Warning: Incomplete signatories. Do you want to proceed?")){
					window.print()
				}
			}else{
				window.print()
			}

			
		});
	}
	
	const getUsers = async () => {
		let response = await axios.get("/api/employee_list");
		listemployees.value=response.data.users
	}

	const getReceived = async () => {
		let response = await axios.get("/api/receiveemp_list");
		listemployeesrec.value=response.data.users
	}

	const getAcknowledge = async () => {
		let response = await axios.get("/api/acknowledgeemp_list");
		listemployeesack.value=response.data.users
	}

	const getNoted = async () => {
		let response = await axios.get("/api/notedemp_list");
		listemployeesnoted.value=response.data.users
	}
	
	const getReceiveHead = async () => {
		let response = await axios.get(`/api/get_receive_head/${props.id}`)
		head.value = response.data.head
		user_id.value = response.data.user_id
		prepared_by.value = response.data.prepared_by
		prepared_position.value = response.data.prepared_position
		rec_position.value = response.data.rec_position
		ack_position.value = response.data.ack_position
		noted_position.value = response.data.noted_position
		printed_by.value=response.data.printed_by_name
		if(head.value.delivered_by){
			document.getElementById('delivered_by').readOnly = true;
		}else{
			document.getElementById('delivered_by').readOnly = false;
		}
	


		// if(head.value.received_by!=0){
		// 	document.getElementById('received_by').setAttribute("style","pointer-events:none");
		// }else{
		// 	document.getElementById('received_by').setAttribute("style","pointer-events:visible");
		// }
		// if(head.value.acknowledged_by!=0){
		// 	document.getElementById('acknowledged_by').setAttribute("style","pointer-events:none");
		// }else{
		// 	document.getElementById('acknowledged_by').setAttribute("style","pointer-events:visible");
		// }
		// if(head.value.noted_by!=0){
		// 	document.getElementById('noted_by').setAttribute("style","pointer-events:none");
		// }else{
		// 	document.getElementById('noted_by').setAttribute("style","pointer-events:visible");
		// }
	}

	const getReceiveDetails = async () => {
		let response = await axios.get(`/api/get_receive_details/${props.id}`)
		details.value = response.data.details
	}

	const getLatestDetailNo = async () => {
		let response = await axios.get(`/api/get_latest_detail_no/${props.id}`)
		max.value = response.data;	
	}

	const getReceiveby = async () => {
		let response = await axios.get(`/api/get_all_position/${head.value.received_by}`)
		if(head.value.received_by!=''){
			rec_position.value = response.data;	
		}else{
			rec_position.value = '';	
		}
	}

	const getAckby = async () => {
		let response = await axios.get(`/api/get_all_position/${head.value.acknowledged_by}`)
		if(head.value.acknowledged_by!=''){
			ack_position.value = response.data;	
		}else{
			ack_position.value = '';	
		}
	}

	const getPrintables = async () => {
		let response = await axios.get(`/api/get_printables/${props.id}`)
		request_id.value = response.data.request_id
		issuance_id.value = response.data.issuance_id

		
	}

	const getNotedby = async () => {
		let response = await axios.get(`/api/get_all_position/${head.value.noted_by}`)
		if(head.value.noted_by!=''){
			noted_position.value = response.data;	
		}else{
			noted_position.value = '';	
		}
	}

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
							<h6 class="m-0 pt-1 font-bold uppercase">Receive</h6>
						</div>
					</div>	
					<div class="pt-1">	
						<nav aria-label="breadcrumb" role="navigation">
							<ol class="breadcrumb adminx-page-breadcrumb">
								<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
								<li class="breadcrumb-item"><a href="/receive">Receive</a></li>
								<li class="breadcrumb-item active" aria-current="page">Print MRF</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>	
			<div class="row" id="print_button">
				<div class="col-lg-12">
					<div class="flex justify-center mb-3 space-x-1">

						<a href="#" class="btn btn-sm btn-primary" @click="openModalRequest()" v-if="request_id.length > 0">View Request</a>
						<a href="#" class="btn btn-sm btn-primary" @click="openModalIssuance()" v-if="request_id.length > 0">View Issuance</a>
						<a href="#" class="btn btn-sm btn-success" @click="printDiv('printable')"> Print Report</a>
					</div>
				</div>
			</div>
		
			<div class="row">
				<div class="col-lg-12">
					<div class="flex justify-center">
						<page size="A4" id="printable" class="p-2">
							<div >
								<printheader>
									MATERIAL RECEIVING & INSPECTION FORM

								</printheader>
								<table class="w-full table-borsdered">
									<tr>
										<td class="leading-tight text-sm font-bold" width="7%">MRIF NO.</td>
										<td class="leading-tight text-sm font-bold" width="43%" ><span class="pr-2">:</span>{{head.mrecf_no}}</td>
										<td class="leading-tight text-sm" width="7%">DATE</td>
										<td class="leading-tight text-sm" width="43%" ><span class="pr-2">:</span>{{head.receive_date}}</td>
									</tr>
									<tr>
										<td class="leading-tight text-sm">DR NO.</td>
										<td class="leading-tight text-sm"><span class="pr-2">:</span>{{head.dr_no}}</td>
										<td class="leading-tight text-sm">PO NO.</td>
										<td class="leading-tight text-sm"><span class="pr-2">:</span>{{head.po_no}}</td>
									</tr>
									<tr>
										<td class="leading-tight text-sm">SI NO.</td>
										<td class="leading-tight text-sm"><span class="pr-2">:</span>{{head.si_or}}</td>
										<td class="leading-tight text-sm">PCF</td>
										<td class="leading-tight text-sm"><span class="pr-2">:</span>{{(head.pcf!=0) ? 'Yes' : 'No' }}</td>
									</tr>
									<tr>
										<td class="leading-tight text-sm" width="15%">WAYBILL NO.</td>
										<td class="leading-tight text-sm" width="43%" ><span class="pr-2">:</span>{{head.waybill_no}}</td>
									</tr>
								</table>
								
								<div  v-for="(det, index) in details" class="border-2 p-2 border-b-0 mt-2">
									<table class="w-full table-borsdered" >
										<tr>
											<td class="leading-tight text-sm font-bold" width="10%">JO/PR NO.</td>
											<td class="leading-tight text-sm font-bold" width="40%"><span class="pr-2">:</span>{{det.pr_no}}</td>
											<td class="leading-tight text-sm"></td>
											<td class="leading-tight text-sm"></td>
											<!-- <td class="leading-tight text-sm" width="10%">Inspected By</td>
											<td class="leading-tight text-sm"><span class="pr-2">:</span>{{det.inspected_id}}</td> -->
										</tr>
										<tr>
											<td class="leading-tight text-sm">Department</td>
											<td class="leading-tight text-sm"><span class="pr-2">:</span>{{det.department_id}}</td>
											<td class="leading-tight text-sm">End-Use</td>
											<td class="leading-tight text-sm"><span class="pr-2">:</span>{{det.enduse_id}}</td>
										</tr>
										<tr>
											<td class="leading-tight text-sm">Purpose</td>
											<td class="leading-tight text-sm" colspan="3"><span class="pr-2">:</span>{{ det.purpose_id }}</td>
										</tr>
									</table>
									<table width="100%" class="table-bordered">
										<tr>
											<td class="text-xs" width="2%" align="center">#</td>
											<td class="text-xs" width="5%" align="center">Qty</td>
											<td class="text-xs" width="2%" align="center">U/M</td>
											<td class="text-xs" width="10%" align="center">Part No</td>
											<td class="text-xs" width="20%" align="center">Item Description</td>
											<td class="text-xs" width="20%" align="center">Supplier</td>
											<td class="text-xs" width="10%" align="center">Catalog No.</td>
											<td class="text-xs" width="10%" align="center">Brand</td>
											<td class="text-xs" width="10%" align="center">Color</td>
											<td class="text-xs" width="10%" align="center">Size</td>
											<td class="text-xs" width="10%" align="center">Item Status</td>
											<td class="text-xs" width="10%" align="center">Shipping/U & Other Cost</td>
											<td class="text-xs" width="5%" align="center">Cost</td>
											<!-- <td class="text-xs" width="5%" align="center">Shipping Fee</td> -->
											<td class="text-xs" width="10%" align="center">Total Cost</td>
										</tr>
										<span hidden>{{ qty=0 }}</span>
										<tr v-for="it in det.receive_items.items">
											<span hidden>{{ qty+=it.rec_quantity*(parseFloat(it.unit_cost) + parseFloat(it.shipping_cost)) }}</span>
											<td class="text-xs align-top px-1" align="center">{{it.item_no}}</td>
											<td class="text-xs align-top px-1" align="center">{{it.rec_quantity}}</td>
											<td class="text-xs align-top px-1" align="center">{{it.uom}}</td>
											<td class="text-xs align-top px-1" align="center">{{it.pn_no}}</td>
											<td class="text-xs align-top px-1" align="left">{{it.item_description}}</td>
											<td class="text-xs align-top px-1" align="left">{{it.supplier_name}}</td>
											<td class="text-xs align-top px-1" align="left">{{it.catalog_no}}</td>
											<td class="text-xs align-top px-1" align="left">{{it.brand}}</td>
											<td class="text-xs align-top px-1" align="left">{{it.color}}</td>
											<td class="text-xs align-top px-1" align="left">{{it.size}}</td>
											<td class="text-xs align-top px-1" align="left">{{it.item_status}}</td>
											<td class="text-xs align-top px-1" align="left">{{ it.shipping_cost }}</td>
											<td class="text-xs align-top px-1" align="center">{{it.unit_cost}} {{ (it.currency!=null) ? it.currency : '' }}</td>
											<!-- <td class="text-xs align-top px-1" align="right">12</td> -->
											<td class="text-xs align-top px-1 bg-yellow-50" align="center">{{ (parseFloat(it.unit_cost) + parseFloat(it.shipping_cost)) *  it.rec_quantity}}</td>
										</tr>
										
										<tr>
											<td class="text-xs" colspan='13' align='right'><span class="mx-1"><b>Total</b></span></td>
											<td class="text-xs bg-yellow-200" colspan='1' align='center'><b>{{ qty }}</b></td>
										</tr>
									</table>
									<table width="100%">
										<tr>
											<td class="text-sm"  colspan="2">Remarks: </td>
										</tr>
										<tr>
											<td class="text-sm border-b border-gray-200" colspan="2">{{ det.remarks }}</td>
										</tr>
										<tr>
											<td class="pt-3"></td>
										</tr>
										<tr>
											<td class="text-sm" width="10%">Inspected by: </td>
											<td class="text-sm border-b border-gray-200">{{ details[0].inspected_id }}</td>
										</tr>
									</table>
								</div>
								<hr class="border-2 m-0">
							
								<table class="w-full mt-2">
									<tr>
										<td class="text-sm" width="30%">Prepared By:</td>
										<td width="5%"></td>                    
										<td class="text-sm" width="30%">Delivered by:</td>
										<td width="5%"></td>
										<td class="text-sm" width="30%">Received by:</td>
									</tr>
									<tr>
										<td class="border-b border-gray-200 p-0"  style="vertical-align: bottom!important" >
											<input v-if="prepared_by && head.prepared_by_name==null" class="text-sm w-full text-center" name="" v-model="prepared_by" readonly >
											<input v-else class="text-sm w-full text-center" name="" v-model="head.prepared_by_name" readonly >
										</td>     
										<td></td>
										<td class="border-b border-gray-200 p-0">
											<textarea id="delivered_by" class="text-sm w-full text-center" rows="1" style="word-wrap:break-word;" v-model="head.delivered_by"></textarea>
										</td>
										<td></td>
										<td class="border-b border-gray-200 p-0" style="vertical-align: bottom!important">
											<select id="received_by" class="text-sm w-full text-center appearance-none" v-model="head.received_by" @change="getReceiveby()" v-if="head.received_by_name==null">
												<option :value="emp.id" v-for="emp in listemployeesrec" :key="emp.id">{{ emp.name }}</option>
											</select>
											<input id="received_by" class="text-sm w-full text-center" style="pointer-events:none" v-model="head.received_by_name" v-else>
										</td>           
									</tr>
									<tr>
										<td>
											<input class="text-sm w-full text-center" style="pointer-events:none" v-model="prepared_position" v-if="head.prepared_position==null || head.prepared_position==''">
											<input class="text-sm w-full text-center" style="pointer-events:none" v-model="head.prepared_position" v-else>
											<!-- <select class="text-sm w-full text-center appearance-none" style="white-space: break-spaces;" >
												<option value = "">Select Your Designation Here..</option>
												<option value = "">Asset and Warehouse Manager</option>
												<option value = "">Commercial Asst. & Parts Analyst</option>
												<option value = "">Warehouse Assistant</option>
											</select> -->
										</td>  
										<td></td>
										<td class="text-sm text-center" style='vertical-align:top'>Supplier/Driver</td>
										<td></td>
										<td style='vertical-align:top'>
											<input id="rec_position" class="text-sm w-full text-center" style="pointer-events:none" v-model="rec_position" v-if="head.receive_position==null || head.receive_position==''">
											<input id="rec_position" class="text-sm w-full text-center" style="pointer-events:none" v-model="head.receive_position" v-else>
										</td>
									</tr>
								</table>
								
								<table class="w-full mt-2">
									<tr>
										<!-- <td class="text-sm" width="30%">Inspected by:</td> -->
										<td width="5%"></td>
										<td class="text-sm" width="30%">Acknowledged by:</td>
										<td width="5%"></td>
										<td class="text-sm" width="30%">Noted by:</td>
										<td width="5%"></td>
									</tr>
									<tr>
										
										 	 	 	
										<td></td>
										<td class="border-b border-gray-200" style="vertical-align: bottom!important">
											<select id="acknowledged_by" class="text-sm w-full text-center appearance-none" v-model="head.acknowledged_by" @change="getAckby()" v-if="head.acknowledged_by_name==null">
												<option :value="emp.id" v-for="emp in listemployeesack" :key="emp.id">{{ emp.name }}</option>
											</select>
											<input id="acknowledged_by" class="text-sm w-full text-center" style="pointer-events:none" v-model="head.acknowledged_by_name" v-else>
										</td>
										<td></td>
										<td class="border-b border-gray-200" style="vertical-align: bottom!important">
											<select v-if="head.noted_name==null" id="noted_by" class="text-sm w-full text-center appearance-none" v-model="head.noted_by" @change="getNotedby()">
												<option :value="emp.id" v-for="emp in listemployeesnoted" :key="emp.id">{{ emp.name }}</option>
											</select>
											<input id="noted_by" class="text-sm w-full text-center" style="pointer-events:none" v-model="head.noted_name" v-else>
										</td>
										<td></td>                
									</tr>
									<tr>
										<td></td>
										<td>
											<input id="positionack" class="text-sm w-full text-center" style="pointer-events:none" v-model="ack_position" v-if="head.acknowledged_position==null || head.acknowledged_position==''">
											<input id="positionack" class="text-sm w-full text-center" style="pointer-events:none" v-model="head.acknowledged_position" v-else>
										</td>
										<td></td>
										<td>
											<input id="positionnoted" class="text-sm w-full text-center" style="pointer-events:none" v-model="noted_position" v-if="head.noted_position==null || head.noted_position==''">
											<input id="positionnoted" class="text-sm w-full text-center" style="pointer-events:none" v-model="head.noted_position" v-else>
										</td>
										<td></td>                
									</tr>
								</table>
								<br>
								<div class="flex justify-between my-1">
									<div class="">
										<p class="text-sm leading-none m-0">Printed By: {{ printed_by }} / {{ timestamp }}</p>
										<p class="text-xs leading-none m-0">Warehouse Form: Material Receiving Form (Effective June 2018)</p>
									</div>
									<div class="mt-2 ">
										<p class="text-xs leading-none m-0">*Warehouse Copy</p>
									</div>
								</div>
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
							<div v-for="rq in request_id"> <!-- diri loop -->
								<a :href="`/request/print/`+rq.id" class="cursor-pointer" target="blank">
									<div class="px-4 py-2 hover:bg-gray-100"  >
										<span class="font-bold">{{ rq.pr_no }}</span> - {{ rq.mreqf_no }}
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
							<div v-for="is in issuance_id"> <!-- diri loop -->
								<a :href="`/issue/print/`+is.id " class="cursor-pointer" target="_blank">
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
