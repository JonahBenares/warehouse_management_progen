<script setup>
	import{ onMounted, ref } from "vue"
	import navigation from '@/layouts/navigation.vue';
	import printheader from '@/layouts/header.vue';
	import { CheckCircleIcon, TrashIcon, PlusIcon, XMarkIcon, PencilSquareIcon, ArrowUturnLeftIcon, ExclamationTriangleIcon, CheckIcon  } from '@heroicons/vue/24/solid'
	import { useRouter } from "vue-router"

	const router = useRouter()
	let items = ref([])
	let listemployees = ref([])
	let listemployeesrec = ref([])
	let timestamp=ref();
	let month=ref();
	let year=ref();
	let printed_by=ref();
	let rec_position=ref('');
	let cpgc_guard=ref('');
	let npc_guard=ref('');
	let display_image=ref('');

	let form = ref({
        id:'',
		received_by:'',
    })

    const props = defineProps({
        id:{
            type:String,
            default:''
        }
    })

    onMounted(async () => {
	    GetGatepassHead()
		getTime()
		getUsers()
		getReceived()
    })

    const GetGatepassHead = async () => {
        let response = await axios.get(`/api/gatepass_details/${props.id}`)
        form.value=response.data.gp_head
        items.value=response.data.gp_items
        display_image.value=response.data.display_image
		// rec_position.value = response.data.rec_position
		printed_by.value=response.data.printed_by_name
    }

	const getUsers = async () => {
		let response = await axios.get("/api/employee_list");
		listemployees.value=response.data.users
	}

	const getReceived = async () => {
		let response = await axios.get("/api/receiveemp_list");
		listemployeesrec.value=response.data.users
	}
	
	const getTime = async() => {
		const today = new Date();
		const date = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
		const month_now = today.toLocaleString('default', { month: 'long' });
		const year_now = today.getFullYear();
		const time = today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();
		const dateTime = date +' '+ time;
		timestamp.value = dateTime;
		month.value = month_now;
		year.value = year_now;
	}

	const getReceiveby = async () => {
		let response = await axios.get(`/api/get_all_position/${form.value.received_by}`)
		if(form.value.received_by!=''){
			rec_position.value = response.data;	
		}else{
			rec_position.value = '';	
		}
	}

	const printDiv = () => {
		const formData= new FormData()
		var recposition = document.getElementById('rec_position').value;

		formData.append('id', props.id)
		// formData.append('user_id', user_id.value)
		formData.append('received_by', form.value.received_by)
		formData.append('rec_position', recposition)
		formData.append('cpgc_guard', cpgc_guard.value)
		formData.append('npc_guard', npc_guard.value)
	
		axios.post("/api/add_signatory_gatepass",formData).then(function () {
			if(form.value.received_by!=0){
				document.getElementById('received_by').setAttribute("style","pointer-events:none");
			}else{
				document.getElementById('received_by').setAttribute("style","pointer-events:visible");
			}
			if(form.value.cpgc_guard_name){
				document.getElementById('cpgc_guard').readOnly = true;
			}else{
				document.getElementById('cpgc_guard').readOnly = false;
			}
			if(form.value.npc_guard_name){
				document.getElementById('npc_guard').readOnly = true;
			}else{
				document.getElementById('npc_guard').readOnly = false;
			}
			var rec =document.getElementById('received_by').value
			var cpgc =document.getElementById('cpgc_guard').value
			var npc =document.getElementById('npc_guard').value	

			if(!rec || !cpgc || !npc){
				if(confirm("Warning: Incomplete signatories. Do you want to proceed?")){
					window.print()
				}
			}else{
				window.print()
			}
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
							<a href="javascript: history.go(-1)" class="btn btn-secondary btn-xs btn-rounded">
								<ArrowUturnLeftIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></ArrowUturnLeftIcon>
							</a>
						</div>
						<div>
							<h6 class="m-0 pt-1 font-bold uppercase">Material Gatepass</h6>
						</div>
					</div>	
					<div class="pt-1">	
						<nav aria-label="breadcrumb" role="navigation">
							<ol class="breadcrumb adminx-page-breadcrumb">
								<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
								<li class="breadcrumb-item"><a href="/gatepass">Material Gatepass</a></li>
								<li class="breadcrumb-item active" aria-current="page">Print Gatepass</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>	
			<div class="row" id="print_button">
				<div class="col-lg-12">
					<div class="flex justify-center mb-3 space-x-1">
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
									MATERIAL GATEPASS FORM

								</printheader>
								<table class="w-full table-borsdered">
									<tr>
										<td class="leading-tight text-sm font-bold" width="10%">MGP NO.</td>
										<td class="leading-tight text-sm font-bold" width="40%" ><span class="pr-2">:</span>{{ form.gatepass_no }}</td>
										<td class="leading-tight text-sm" width="9%">DESTINATION</td>
										<td class="leading-tight text-sm" width="41%" ><span class="pr-2">:</span>{{ form.destination }}</td>
									</tr>
									<tr>
										<td class="leading-tight text-sm">TO COMPANY</td>
										<td class="leading-tight text-sm"><span class="pr-2">:</span>{{ form.to_company }}</td>
										<td class="leading-tight text-sm">VEHICLE NO.</td>
										<td class="leading-tight text-sm"><span class="pr-2">:</span>{{ form.vehicle_no }}</td>
									</tr>
									<tr>
										<td class="leading-tight text-sm">DATE ISSUED</td>
										<td class="leading-tight text-sm"><span class="pr-2">:</span>{{ form.date_issued }}</td>
									</tr>
									<tr>
										<td class="leading-tight text-sm">REMARKS</td>
										<td class="leading-tight text-sm"><span class="pr-2">:</span>{{ form.remarks }}</td>
									</tr>
								</table>
								
								<div  class="mt-2">
									<table width="100%" class="table-bordered">
										<tr class="bg-gray-100">
											<td class="text-xs font-bold" width="2%" align="center">#</td>
											<td class="text-xs font-bold" width="20%" align="center">Item Description</td>
											<td class="text-xs font-bold" width="5%" align="center">QTY</td>
											<td class="text-xs font-bold" width="5%" align="center">U/M</td>
											<td class="text-xs font-bold" width="10%" align="center">Type</td>
											<td class="text-xs font-bold" width="20%" align="center">Remarks</td>
										</tr>
										<tr v-for="(i, x) in items">
											<td class="text-xs" align="center">{{ x + 1 }}</td>
											<td class="text-xs" align="center">{{ i.item_description }}</td>
											<td class="text-xs" align="center">{{ i.quantity }}</td>
											<td class="text-xs" align="center">{{ i.uom }}</td>
											<td class="text-xs" align="center">{{ i.type }}</td>
											<td class="text-xs" align="center">{{ i.remarks }}</td>
										</tr>
										<tr>
											<td colspan="6" align="center">**nothing follows**</td>
										</tr>
									</table>
								</div>
								<hr class="border-2 m-0">
								<table class="w-full mt-2 table-bosrdered">
									<tr>
										<td class="text-sm" width="30%">Prepared By:</td>
										<td width="5%"></td>                    
										<td width="65%" rowspan="12">
											<!-- <img src="" class="w-full " alt=""> -->
											<img :src="'/gatepass_items/'+display_image" id="img" v-if="display_image!=null"/>
										</td>                    
									</tr>
									<tr>
										<td class="border-b border-gray-200 p-0"  style="vertical-align: bottom!important" >
											{{ form.prepared_by_name }}
										</td>           
									</tr>
									<tr>
										<td class="text-sm" width="30%">Warehouse In-Charge</td>
										<td width="5%"></td>                      
									</tr>
									<tr>
										<td>
											<br>
											<br>
										</td>           
									</tr>
									<tr>
										<td class="text-sm" width="30%">Noted By:</td>
										<td width="5%"></td>                     
									</tr>
									<tr>
										<td class="border-b border-gray-200 p-0"  style="vertical-align: bottom!important" >
											{{ form.noted_by_name }}
										</td>           
									</tr>
									<tr>
										<td class="text-sm" width="30%">Facility In Charge</td>
										<td width="5%"></td>                       
									</tr>
									<tr>
										<td>
											<br>
											<br>
										</td>           
									</tr>
									<tr>
										<td class="text-sm" width="30%">Approved By:</td>
										<td width="5%"></td>                     
									</tr>
									<tr>
										<td class="border-b border-gray-200 p-0"  style="vertical-align: bottom!important" >
											{{ form.approved_by_name }}
										</td>           
									</tr>
									<tr>
										<td class="text-sm" width="30%">SIPC Utility</td>
										<td width="5%"></td>                      
									</tr>
									<tr>
										<td>
											<br>
											<br>
										</td>           
									</tr>
								</table>
								<table class="w-full mt-2">
									<tr>
										<td class="text-sm" width="30%">Received By:</td>
										<td width="5%"></td>                    
										<td class="text-sm" width="30%">Verified by:</td>
										<td width="5%"></td>
										<td class="text-sm" width="30%"></td>
									</tr>
									<tr>
										<td class="border-b border-gray-200 p-0"  style="vertical-align: bottom!important" >
											<select id="received_by" class="text-sm w-full text-center appearance-none" v-model="form.received_by" @change="getReceiveby()" v-if="form.received_by_name==null">
												<option :value="emp.id" v-for="emp in listemployeesrec" :key="emp.id">{{ emp.name }}</option>
											</select>
											<input id="received_by" class="text-sm w-full text-center" style="pointer-events:none" v-model="form.received_by_name" v-else>
										</td>     
										<td></td>
										<td class="border-b border-gray-200 p-0">
											<input id="cpgc_guard" class="text-sm w-full text-center" v-model="cpgc_guard" v-if="form.cpgc_guard_name==null">
											<input id="cpgc_guard" class="text-sm w-full text-center" style="pointer-events:none" v-model="form.cpgc_guard_name" v-else>
										</td>
										<td></td>
										<td class="border-b border-gray-200 p-0" style="vertical-align: bottom!important">
											<input id="npc_guard" class="text-sm w-full text-center" v-model="npc_guard" v-if="form.npc_guard_name==null">
											<input id="npc_guard" class="text-sm w-full text-center" style="pointer-events:none" v-model="form.npc_guard_name" v-else>
										</td>           
									</tr>
									<tr>
										<td class="text-sm" width="30%">
											<input id="rec_position" class="text-sm w-full text-center" style="pointer-events:none" v-model="rec_position" v-if="form.received_by_position==null || form.received_by_position==''">
											<input id="rec_position" class="text-sm w-full text-center" style="pointer-events:none" v-model="form.received_by_position" v-else>
										</td>
										<td width="5%"></td>                    
										<td class="text-sm" width="30%">CPGC Guard</td>
										<td width="5%"></td>
										<td class="text-sm" width="30%">NPC Guard</td>
									</tr>
								</table>
								
								
								<br>
								<div class="flex justify-between my-1">
									<div class="">
										<p class="text-sm leading-none m-0">Printed By: {{ printed_by }} / {{ timestamp }} </p>
										<p class="text-xs leading-none m-0">Warehouse Form: Material Receiving Form (Effective {{ month }} {{ year }})</p>
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
    </navigation>
</template>
