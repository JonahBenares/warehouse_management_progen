<script setup>
	import{ onMounted, ref } from "vue"
	import navigation from '@/layouts/navigation.vue';
	import printheader from '@/layouts/header.vue';
	import { CheckCircleIcon, TrashIcon, PlusIcon, XMarkIcon, PencilSquareIcon, ArrowUturnLeftIcon, ExclamationTriangleIcon, CheckIcon  } from '@heroicons/vue/24/solid'
	import { useRouter } from "vue-router"
	// let head = ref('');
	let head = ref({
        id:'',
		returned_by:'',
		inspected_by:'',
		acknowledged_by:'',
		noted_by:''
		
    })
	// let purpose = ref('');
	// let enduse = ref('');
	// let department = ref('');
	let details = ref([]);
	let listemployees = ref([]);
	let listemployeesret = ref([]);
	let listemployeesins = ref([]);
	let listemployeesack = ref([]);
	let listemployeesnoted = ref([]);
	let ret_position=ref();
	let ins_position=ref();
	let ack_position=ref();
	let noted_position=ref();
	const props = defineProps({
        id:{
            type:String,
            default:''
        }
    })
	onMounted(async () => {
		restockShowform()
		getUsers()
		getReturned()
		getInspected()
		getAcknowledge()
		getNoted()
	})

	const restockShowform = async () => {
		let response = await axios.get(`/api/getshow_details/${props.id}`)
		head.value = response.data.head;
		details.value = response.data.details;
		// purpose.value = response.data.purpose
        // enduse.value = response.data.enduser
        // department.value = response.data.department
        ret_position.value = response.data.ret_position
        ins_position.value = response.data.ins_position
        ack_position.value = response.data.ack_position
        noted_position.value = response.data.noted_position
	}

	const printDiv = () => {
		const formData= new FormData()
		formData.append('id', props.id)
		formData.append('returned_by', head.value.returned_by ?? 0)
		formData.append('ret_position', ret_position.value ?? '')
		formData.append('inspected_by', head.value.inspected_by ?? 0)
		formData.append('ins_position', ins_position.value ?? '')
		formData.append('acknowledged_by', head.value.acknowledged_by ?? 0)
		formData.append('ack_position', ack_position.value ?? '')
		formData.append('noted_by', head.value.noted_by ?? 0)
		formData.append('noted_position', noted_position.value ?? '')
		var ret =document.getElementById('returned_by').value
		var ins =document.getElementById('inspected_by').value
		var ack =document.getElementById('acknowledged_by').value
		var not =document.getElementById('noted_by').value
		// if(!ret || !ins || !ack || !not){
		// 	alert('Warning: Incomplete signatories.');
		// }
		axios.post("/api/add_signatory_restock",formData).then(function () {
			if(head.value.returned_by!=0){
				document.getElementById('returned_by').setAttribute("style","pointer-events:none");
			}else{
				document.getElementById('returned_by').setAttribute("style","pointer-events:visible");
			}
			if(head.value.inspected_by!=0){
				document.getElementById('inspected_by').setAttribute("style","pointer-events:none");
			}else{
				document.getElementById('inspected_by').setAttribute("style","pointer-events:visible");
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
			// window.print()
			if(!ret || !ins || !ack || !not){
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

	const getReturned = async () => {
		let response = await axios.get("/api/requestedemp_list");
		listemployeesret.value=response.data.users
	}

	const getInspected = async () => {
		let response = await axios.get("/api/reviewedemp_list");
		listemployeesins.value=response.data.users
	}

	const getNoted = async () => {
		let response = await axios.get("/api/notedemp_list");
		listemployeesnoted.value=response.data.users
	}

	const getAcknowledge = async () => {
		let response = await axios.get("/api/approvedemp_list");
		listemployeesack.value=response.data.users
	}

	const getRetrunedby = async () => {
		let response = await axios.get(`/api/get_all_position/${head.value.returned_by}`)
		if(head.value.returned_by!=''){
			ret_position.value = response.data;	
		}else{
			ret_position.value = '';	
		}
	}

	const getInspectedby = async () => {
		let response = await axios.get(`/api/get_all_position/${head.value.inspected_by}`)
		if(head.value.inspected_by!=''){
			ins_position.value = response.data;	
		}else{
			ins_position.value = '';	
		}
	}

	const getAcknowledgedby = async () => {
		let response = await axios.get(`/api/get_all_position/${head.value.acknowledged_by}`)
		if(head.value.acknowledged_by!=''){
			ack_position.value = response.data;	
		}else{
			ack_position.value = '';	
		}
	}

	const getNotedby = async () => {
		let response = await axios.get(`/api/get_all_position/${head.value.noted_by}`)
		if(head.value.noted_by!=''){
			noted_position.value = response.data;	
		}else{
			noted_position.value = '';	
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
							<a href="javascript: history.go(-1)" class="btn btn-secondary btn-xs btn-rounded">
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
								<li class="breadcrumb-item active" aria-current="page">Print MRW</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>

			<div class="row" id="print_button">
				<div class="col-lg-12">
					<div class="flex justify-center mb-3 space-x-1">
						<button class="btn btn-sm btn-success" @click="printDiv()"> Print Report</button>
					</div>
				</div>
			</div>
		
			<div class="row">
				<div class="col-lg-12">
					<div class="flex justify-center">
						<page size="A4" id="printme" class="p-2">
							<div >
								<printheader>
									MATERIAL RESTOCK TO WAREHOUSE FORM
								</printheader>
								<table class="w-full table-bordsered">
									<tr>
										<td class="leading-tight text-sm font-bold">MRW NO.</td>
										<td class="leading-tight text-sm font-bold"><span class="pr-2">:</span>{{ head.mrs_no }}</td>
										<td class="leading-tight text-sm">DATE/TIME</td>
										<td class="leading-tight text-sm"><span class="pr-2">:</span>{{ head.date }} {{ head.time }}</td>
									</tr>
									<tr>
										<td class="leading-tight text-sm font-bold" width="10%">SOURCE PR</td>
										<td class="leading-tight text-sm font-bold" width="40%" ><span class="pr-2">:</span>{{ head.source_pr }}</td>
										<td class="leading-tight text-sm font-bold" width="10%">DESTINATION</td>
										<td class="leading-tight text-sm font-bold" width="40%" ><span class="pr-2">:</span>{{ head.destination }}</td>
									</tr>
									<tr>
										<td class="leading-tight text-sm">DEPARTMENT</td>
										<td class="leading-tight text-sm"><span class="pr-2">:</span>{{ head.department }}</td>
										<td class="leading-tight text-sm">ENDUSE</td>
										<td class="leading-tight text-sm" colspan="3"><span class="pr-2">:</span>{{ head.enduse }}</td>
									</tr>
									<tr>
										<td class="leading-tight text-sm">PURPOSE</td>
										<td class="leading-tight text-sm" colspan="6"><span class="pr-2">:</span>{{ head.purpose }}</td>
									</tr>
								</table>
								<table width="100%" class="table-bordered mt-2">
									<tr>
										<td class="text-sm text-center" width="1%">#</td>
										<td class="text-sm text-center" width="3%">Qty</td>
										<td class="text-sm text-left" width=" 15%">Supplier</td>
										<td class="text-sm text-center" width=" 20%">Item Description</td>                    
										<td class="text-sm text-center" width="">Brand</td>
										<td class="text-sm text-center" width="">Cat No.</td>
										<td class="text-sm text-center" width="">Serial No.</td>
										<td class="text-sm text-center" width="">Uom</td>
										<td class="text-sm text-center" width="%">Color</td>
										<td class="text-sm text-center" width="%">Size</td>
										<td class="text-sm text-center" width="%">Item Status</td>
										<td class="text-sm text-center" width="%">Soure MIF #</td>
										<td class="text-sm text-center" width="">Reason</td>
										<td class="text-sm text-center" width="">Remarks</td>
									</tr>
									<tr v-for="(d, index) in details">
										<td class="text-sm align-top px-1" align="center">{{ index + 1}}</td>
										<td class="text-sm align-top px-1" align="center">{{d.quantity }}</td>
										<td class="text-sm align-top px-1" align="left">{{ d.supplier_name }}</td>
										<td class="text-sm align-top px-1" align="center">{{ d.item_description }}</td>
										<td class="text-sm align-top px-1" align="center">{{ d.brand }}</td>
										<td class="text-sm align-top px-1" align="center">{{ d.catalog_no }}</td>
										<td class="text-sm align-top px-1" align="center">{{ d.serial_no }}</td>
										<td class="text-sm align-top px-1" align="center">{{ d.uom }}</td>
										<td class="text-sm align-top px-1" align="center">{{ d.color }}</td>
										<td class="text-sm align-top px-1" align="center">{{ d.size }}</td>
										<td class="text-sm align-top px-1" align="center">{{ d.item_status }}</td>
										<td class="text-sm align-top px-1" align="center">{{ d.mif_no }}</td>
										<td class="text-sm align-top px-1" align="center">{{ d.reason }}</td>
										<td class="text-sm align-top px-1" align="center">{{ d.remarks }}</td>
									</tr>
								</table>
								<table class="w-full mt-2" >
									<tr>
										<td width="10%"></td>
										<td class="text-sm" width="35%">Returned by:</td>
										<td width="10%"></td>
										<td class="text-sm" width="35%">Inspected & Received by:</td>
										<td width="10%"></td>
									</tr>
									<tr>
										<td></td>
										<td class="border-b border-gray-200">
											<select id="returned_by" class="text-sm w-full text-center appearance-none" v-model="head.returned_by" @change="getRetrunedby()" v-if="head.returned_by_name==null || head.returned_by_name==''">
												<option :value="emp.id" v-for="emp in listemployeesret" :key="emp.id">{{ emp.name }}</option>
											</select>
											<input type="text" id="returned_by" class="text-sm w-full text-center" style="pointer-events:none" v-model="head.returned_by_name" v-else>
										</td>
										<td></td>
										<td class="border-b border-gray-200">
											<select id="inspected_by" class="text-sm w-full text-center appearance-none" v-model="head.inspected_by" @change="getInspectedby()" v-if="head.inspected_by_name==null || head.inspected_by_name==''">
												<option :value="emp.id" v-for="emp in listemployeesins" :key="emp.id">{{ emp.name }}</option>
											</select>
											<input type="text" id="inspected_by" class="text-sm w-full text-center" style="pointer-events:none" v-model="head.inspected_by_name" v-else>
										</td>
										<td></td>                
									</tr>
									<tr>
										<td></td>
										<td>
											<input type="text" id="returned_by_position" class="text-sm w-full text-center" style="pointer-events:none" v-model="ret_position" v-if="head.returned_by_position==null || head.returned_by_position==''">
											<input type="text" id="returned_by_position" class="text-sm w-full text-center" style="pointer-events:none" v-model="head.returned_by_position" v-else>
										</td>
										<td></td>
										<td>
											<input type="text" id="inspected_by_position" class="text-sm w-full text-center" style="pointer-events:none" v-model="ins_position" v-if="head.inspected_by_position==null || head.inspected_by_position==''">
											<input type="text" id="inspected_by_position" class="text-sm w-full text-center" style="pointer-events:none" v-model="head.inspected_by_position" v-else>
										</td>
										<td></td>                
									</tr>
								</table>
								<table class="w-full mt-0">
									<tr>
										<td width="10%"></td>
										<td class="text-sm" width="35%">Acknowledged by:</td>
										<td width="10%"></td>
										<td class="text-sm" width="35%">Noted by:</td>
										<td width="10%"></td>
									</tr>
									<tr>
										<td></td>
										<td class="border-b border-gray-200">
											{{  }}
											<select id="acknowledged_by" class="text-sm w-full text-center appearance-none" v-model="head.acknowledged_by" @change="getAcknowledgedby()" v-if="head.acknowledged_by_name==null || head.acknowledged_by_name==''">
												<option :value="emp.id" v-for="emp in listemployeesack" :key="emp.id">{{ emp.name }}</option>
											</select>
											<input type="text" id="acknowledged_by" class="text-sm w-full text-center" style="pointer-events:none" v-model="head.acknowledged_by_name" v-else>
										</td>
										<td></td>
										<td class="border-b border-gray-200">
											<select id="noted_by" class="text-sm w-full text-center appearance-none" v-model="head.noted_by" @change="getNotedby()" v-if="head.noted_by_name==null || head.noted_by_name==''">
												<option :value="emp.id" v-for="emp in listemployeesnoted" :key="emp.id">{{ emp.name }}</option>
											</select>
											<input type="text" id="noted_by" class="text-sm w-full text-center" style="pointer-events:none" v-model="head.noted_by_name" v-else>
										</td>
										<td></td>                
									</tr>
									<tr>
										<td></td>
										<td>
											<input type="text" id="acknowledged_by_position" class="text-sm w-full text-center" style="pointer-events:none" v-model="ack_position" v-if="head.acknowledged_by_position==null || head.acknowledged_by_position=='' ">
											<input type="text" id="acknowledged_by_position" class="text-sm w-full text-center" style="pointer-events:none" v-model="head.acknowledged_by_position" v-else>
										</td>
										<td></td>
										<td>
											<input type="text" id="noted_by_position" class="text-sm w-full text-center" style="pointer-events:none" v-model="noted_position" v-if="head.noted_by_position==null || head.noted_by_position==''">
											<input type="text" id="noted_by_position" class="text-sm w-full text-center" style="pointer-events:none" v-model="head.noted_by_position" v-else>
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
