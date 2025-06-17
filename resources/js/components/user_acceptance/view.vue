<script setup>
	import{ onMounted, ref } from "vue"
	import navigation from '@/layouts/navigation.vue';
	import { CheckCircleIcon, XMarkIcon, PencilSquareIcon, PrinterIcon, ExclamationCircleIcon, CheckIcon , ExclamationTriangleIcon } from '@heroicons/vue/24/solid'
	import { useRouter } from "vue-router"

    const router = useRouter()

	let head = ref({
        id:''
    })
	

	let details = ref([])
	let items = ref([])
	let override_email = ref('')
	let override_password = ref('')
	let error = ref([])
	let password_error = ref([])
	let success = ref()
	let max = ref()
	let checkbox = ref([])
	let eval_date = ref('')
	let eval_user = ref('')
	// let eval_user_reject = ref('')
	let eval_reason = ref('')
	let rejected_qty = ref([])
	let rejected_remarks = ref([])
	let employees = ref([])
	let accemployees = ref([])
	let rejemployees = ref([])
	let insemployees = ref([])
	let pending_items = ref(0)
	let rejected_checker=ref(0)
	let rejected_array=ref([])

	const props = defineProps({
        id:{
            type:String,
            default:''
        }
    })
	onMounted(async () =>{
        getReceiveHead()
		getReceiveDetails()
		getLatestDetailNo(props.id)
		getEmployees()
		getAcceptedEmployees()
		getRejectedEmployees()
		getInspectedEmployees()
    })

	
	const getReceiveHead = async () => {
		let response = await axios.get(`/api/get_receive_head/${props.id}`)
		head.value = response.data.head
		pending_items.value = response.data.pending_items
	}

	const getReceiveDetails = async () => {
		let response = await axios.get(`/api/get_receive_details/${props.id}`)
		details.value = response.data.details
	}

	const getLatestDetailNo = async () => {
		let response = await axios.get(`/api/get_latest_detail_no/${props.id}`)
		max.value = response.data;
		
	}

	const getEmployees = async () => {
		let response = await axios.get(`/api/employee_list`)
		employees.value = response.data.users;
		
	}

	const getAcceptedEmployees = async () => {
		let response = await axios.get(`/api/acceptedemp_list`)
		accemployees.value = response.data.users;
	}

	const getRejectedEmployees = async () => {
		let response = await axios.get(`/api/rejectedemp_list`)
		rejemployees.value = response.data.users;
	}

	const getInspectedEmployees = async () => {
		let response = await axios.get(`/api/inspectedemp_list`)
		insemployees.value = response.data.users;
	}

	const updateHead = ref(false)
	const hideModal = ref(true)
	const acceptModal = ref(false)
    const rejectModal = ref(false)
	const openModalHead = () => {
		updateHead.value = !updateHead.value
	}
	const closeModalHead = () => {
		updateHead.value = !hideModal.value
		updatepr.value = !hideModal.value
		acceptModal.value = !hideModal.value
        rejectModal.value = !hideModal.value
	}

	const updatepr = ref(false)
	const openUpdate = () => {
		updatepr.value = !updatepr.value
	}
	const closeUpdate = () => {
		updatepr.value = !hideModal.value
	}

    const openReject = () => {
		rejectModal.value = !rejectModal.value
		
	}
	
	const saveAccepted = () => {
		const formData = new FormData()
		formData.append('checkbox',JSON.stringify(checkbox.value))
		formData.append('eval_date', eval_date.value)
		formData.append('eval_user', eval_user.value)
		formData.append('receive_head_id', props.id)
		axios.post(`/api/save_accepted`, formData).then(function (response) {
			if(response.data!='error'){
				success.value='Successfully updated!'
				document.getElementById("success").style.display="block"
				document.getElementById("error").style.display="none"
				window.location.reload();
				setTimeout(() => {
					document.getElementById("success").style.display="none"
				}, 4000);
			}else{
				error.value='Please check the checkbox to proceed!'
				document.getElementById("error").style.display="block"
				setTimeout(() => {
					error.value=''
					document.getElementById("error").style.display="none"
				}, 5000);
			}
		}, function (err) {
			document.getElementById("error").style.display="block"
			setTimeout(() => {
			document.getElementById("error").style.display="none"
			}, 4000);
		});
	}

	const saveRejected = () => {
		const formData = new FormData()
		formData.append('checkbox',JSON.stringify(checkbox.value))
		// formData.append('rejected_qty',JSON.stringify(rejected_qty.value))
		formData.append('eval_date', eval_date.value)
		formData.append('eval_user', eval_user.value)
		// formData.append('eval_reason', eval_reason.value)
		formData.append('receive_head_id', props.id)
		checkbox.value.forEach(function (val, index, theArray) {
			formData.append('rejected_qty'+index, rejected_qty.value[val])
			formData.append('rejected_remarks'+index, rejected_remarks.value[val])
		});
		// console.log(JSON.stringify(checkbox.value))
		// const count = document.getElementsByClassName('rejected_qty');
		// for(var x=0;x<count.length;x++){
		// 	var check=document.getElementById("tickbox"+x).checked;
		// 	if(check==true){
		// 		var value=document.getElementById('rejected_qty'+x).value+"-"+x;
		// 		formData.append('rejected_qty', value)
		// 	}
		// }
		axios.post(`/api/save_rejected`, formData).then(function (response) {
			// alert(response.data)
			if(response.data!='error'){
				success.value='Successfully rejected!'
				document.getElementById("success").style.display="block"
				document.getElementById("error").style.display="none"
				window.location.reload();
				setTimeout(() => {
					document.getElementById("success").style.display="none"
				}, 4000);
			}else{
				error.value='Please check the checkbox and put quantity to proceed!'
				document.getElementById("error").style.display="block"
				setTimeout(() => {
					error.value=''
					document.getElementById("error").style.display="none"
				}, 5000);
			}
		}, function (err) {
			document.getElementById("error").style.display="block"
			setTimeout(() => {
			document.getElementById("error").style.display="none"
			}, 4000);
		});
	}

	const onPrint= (id) => {
		router.push('/user_acceptance/print/'+id)
	}

	const checkAcceptanceQty = (counter) => {
		const no_of_rows = document.getElementsByClassName('rejected_qty');
		let countererror = 0
		// for(var x=0;x<no_of_rows.length;x++){
		rejected_checker.value=0
		checkbox.value.forEach(function (val, index, theArray) {
			if(val!=0){
				var checker=parseFloat(document.getElementById('rec_qty'+val).value);
				var rejected_qty=parseFloat(document.getElementById("rejected_qty"+val).value);
				rejected_checker.value+= (!isNaN(rejected_qty)) ? rejected_qty : 0;
				if(rejected_qty > checker){
					document.getElementById("rejected_qty"+val).style.backgroundColor  = '#FAA0A0'
					rejected_remarks.value[val]='';
					document.getElementById("rejected_remarks"+val).style.display="none";
					document.getElementById("rejected_label"+val).style.display="none";
					countererror++
				} else {
					document.getElementById("rejected_qty"+val).style.backgroundColor  = 'white'
					document.getElementById("tickbox"+val).disabled = false; 
					if(!isNaN(rejected_qty) && rejected_qty!=0){
						document.getElementById("rejected_remarks"+val).style.display="block";
						document.getElementById("rejected_label"+val).style.display="block";
					}else{
						document.getElementById("rejected_remarks"+val).style.display="none";
						document.getElementById("rejected_label"+val).style.display="none";
					}
				}
			}
		});
		checkQty(countererror)
	}
	const checkQty = (countererror) => {
		const no_of_rows = document.getElementsByClassName('rejected_qty');
		// for(var x=0;x<no_of_rows.length;x++){
		checkbox.value.forEach(function (val, index, theArray) {
			if(val!=0){
				var check=document.getElementById('tickbox'+val).checked;
				var accepted = document.getElementById('accepted');
				// var rejected = document.getElementById('rejected');
				if(countererror>0 && check==true){ 
					document.getElementById("tickbox"+val).disabled = true; 
					accepted.disabled = true; 
					// rejected.disabled = true; 
				} else {
					document.getElementById("tickbox"+val).disabled = false; 
					if(check==true){
						accepted.disabled = false; 
						// rejected.disabled = false; 
					}
				}
			}
		});
		// }
	}

	const openAccept = () => {
		var array1=[]
		var count_qty=document.getElementsByClassName('tickbox');
		for(var x=0;x<count_qty.length;x++){
			var check=document.getElementsByClassName('tickbox')[x].checked;
			array1.push(check);
		}
		if(!Object.values(array1).includes(false)){
			// var arr=[];
			// checkbox.value.forEach(function (val, index, theArray) {
			// 	if(val!=0){
			// 		var rejected_qty=(!isNaN(parseFloat(document.getElementById("rejected_qty"+val).value))) ? parseFloat(document.getElementById("rejected_qty"+val).value) : 0;
			// 		arr.push(rejected_qty);
			// 	}
			// })
			// const result = arr.find(element => element === 0);
			// if(result!==undefined){
			// 	document.getElementById("check_accepted").style.display="block";
			// }else{
			// 	document.getElementById("check_accepted").style.display="none";
			// }
			acceptModal.value = !acceptModal.value
		}else{
			error.value='Please check all items to proceed!'
			document.getElementById("error").style.display="block"
			setTimeout(() => {
				error.value=''
				document.getElementById("error").style.display="none"
			}, 5000);
		}
	}

	const saveNew = () => {
		const formData = new FormData()
		formData.append('checkbox',JSON.stringify(checkbox.value))
		formData.append('eval_date', eval_date.value)
		formData.append('eval_user', eval_user.value)
		formData.append('receive_head_id', props.id)
		checkbox.value.forEach(function (val, index, theArray) {
			formData.append('rejected_qty'+index, rejected_qty.value[val])
			formData.append('rejected_remarks'+index, rejected_remarks.value[val])
		});
		var array=[]
		var count_qty=document.getElementsByClassName('tickbox');
		for(var x=0;x<count_qty.length;x++){
			var check=document.getElementsByClassName('tickbox')[x].checked;
			array.push(check);
		}
		if(!Object.values(array).includes(false)){
			document.getElementById("savenew").disabled = true; 
			axios.post(`/api/save_newaccepted`, formData).then(function (response) {
				if(response.data!='error'){
					success.value='Successfully saved!'
					document.getElementById("success").style.display="block"
					document.getElementById("error").style.display="none"
					window.location.reload();
					setTimeout(() => {
						document.getElementById("success").style.display="none"
					}, 4000);
				}else{
					error.value='Please check the checkbox and put quantity if reject to proceed!'
					document.getElementById("error").style.display="block"
					setTimeout(() => {
						error.value=''
						document.getElementById("error").style.display="none"
					}, 5000);
				}
			}, function (err) {
				document.getElementById("error").style.display="block"
				setTimeout(() => {
				document.getElementById("error").style.display="none"
				}, 4000);
			});
		}else{
			error.value='Please check all items to proceed!'
			document.getElementById("error").style.display="block"
			setTimeout(() => {
				error.value=''
				document.getElementById("error").style.display="none"
			}, 2000);
		}
	}

	const tickBox = () => {
		checkbox.value.forEach(function (val, index, theArray) {
			if(val!=0){
				var checked=document.getElementById('tickbox'+val).checked;
				var id = document.getElementById("id"+val).value;
				if(checked==true){
					document.getElementById("rej_label"+val).style.display="block"
					document.getElementById("rejected_qty"+val).style.display="block"
				}else{
					document.getElementById("rej_label"+val).style.display="none"
					rejected_qty.value[val]='';
					rejected_remarks.value[val]='';
					document.getElementById("rejected_qty"+val).style.display="none"
					document.getElementById("rejected_label"+val).style.display="none"
					document.getElementById("rejected_remarks"+val).style.display="none"
				}
			}
		});
		// var count_qty=document.getElementsByClassName('tickbox');
		// for(var x=0;x<count_qty.length;x++){
		// 	var check=document.getElementsByClassName('tickbox')[x].checked;
		// 	var id = document.getElementById("id"+x).value;
		// 	if(check==true){
		// 		document.getElementById("rej_label"+x).style.display="block"
		// 		document.getElementById("rejected_qty"+id).style.display="block"
		// 	}else{
		// 		document.getElementById("rej_label"+x).style.display="none"
		// 		rejected_qty.value[id]='';
		// 		rejected_remarks.value[id]='';
		// 		document.getElementById("rejected_qty"+id).style.display="none"
		// 		document.getElementById("rejected_label"+id).style.display="none"
		// 		document.getElementById("rejected_remarks"+id).style.display="none"
		// 	}
		// }
	}
	const popUpremarks = (id) => {
		var popup = document.getElementById("myPopup"+id);
		popup.classList.toggle("show");
	}
</script>
<style>
/* Popup container - can be anything you want */
.popup {
  position: relative;
  display: inline-block;
  cursor: pointer;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

/* The actual popup */
.popup .popuptext {
  visibility: hidden;
  width: 160px;
  background-color: #555;
  color: #fff;
  text-align: center;
  border-radius: 6px;
  padding: 8px 0;
  position: absolute;
  z-index: 1;
  bottom: 125%;
  left: 50%;
  margin-left: -80px;
}

/* Popup arrow */
.popup .popuptext::after {
  content: "";
  position: absolute;
  top: 100%;
  left: 50%;
  margin-left: -5px;
  border-width: 5px;
  border-style: solid;
  border-color: #555 transparent transparent transparent;
}

/* Toggle this class - hide and show the popup */
.popup .show {
  visibility: visible;
  -webkit-animation: fadeIn 1s;
  animation: fadeIn 1s;
}

/* Add animation (fade in the popup) */
@-webkit-keyframes fadeIn {
  from {opacity: 0;} 
  to {opacity: 1;}
}

@keyframes fadeIn {
  from {opacity: 0;}
  to {opacity:1 ;}
}
</style>
<template>
	<div>
		<div class="col-lg-4 offset-lg-4">
			
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
								<p class="text-sm m-0 text-gray-400"> {{ error }}</p>
							</div>
						</div>
					</div>
				</div>
				<div v-else id="error"></div>
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
							<!-- <a :href="'/receive/new_second/'+head.id+'/'+max" class="btn btn-secondary btn-xs btn-rounded">
								<ArrowUturnLeftIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></ArrowUturnLeftIcon>
							</a> -->
						</div>
						<div>
							<h6 class="m-0 pt-1 font-bold uppercase">Receive User Acceptance</h6>
						</div>
					</div>	
					<div class="pt-1">	
						<nav aria-label="breadcrumb" role="navigation">
							<ol class="breadcrumb adminx-page-breadcrumb">
								<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
								<li class="breadcrumb-item"><a href="/user_acceptance/pending">Receive User Acceptance</a></li>
								<li class="breadcrumb-item active" aria-current="page">View</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>	
			<!-- <div class="alert bg-emerald-500 text-white border-0 shadow-sm !mb-3">
				<div class="flex justify-start space-x-2 py-1">
					<div>
						<ExclamationTriangleIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12"></ExclamationTriangleIcon>
					</div> 
					<div class="pt-1">
						<h6 class="font-bold m-0 leading-none uppercase">Instructions </h6> 
						<span class="text-white">In accepting and rejecting items, always click the checkbox and add quantity if the items are rejected.</span>
					</div>
				</div>
			</div>  -->
			<div class="card p-4 card-main-bg">
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
											<span class="text-md uppercase font-bold text-gray-600 leading-none">{{ head.si_or }}</span>
										</div>
										<div class="flex justify-start" >
											<span class="text-xs text-gray-500 leading-none pt-1">SI/OR NUMBER</span>
										</div>
									</td>
									<td class="px-1  " width="15%">
										<div class="flex justify-start">
											<span class="text-md uppercase font-bold text-gray-600 leading-none">{{ head.waybill_no }}</span>
										</div>
										<div class="flex justify-start" >
											<span class="text-xs text-gray-500 leading-none pt-1">WAYBILL NUMBER</span>
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
				<span hidden> {{ x=0 }}</span>
				<div v-for="det in details" class="border-2 border-blue-400 mb-3 rounded ">
					<div class="row">
						<div class="col-lg-12">
							<table class="w-full table-borsdered">
								<tr>
									<td width="2%" rowspan="4" class="align-top p-0 bg-blue-400"> 
										<div class="pt-2 p-1  px-2 text-md text-center bg-blue-400  font-bold pb-5 text-white">{{ det.detail_no.padStart(2, "0") }}</div>
									</td>
									<td class="pt-2 pl-2 form-label" width="8%">PR Number</td>
									<td class="pt-2 px-1 text-sm border-b font-bold">{{ det.pr_no }}</td>
									<td class="pt-2 px-1 text-sm" width="3%"></td>
									<td class="pt-2 form-label" width="9%">Inspected by</td>
									<td class="pt-2 px-1 text-sm border-b">{{ det.inspected_id }}</td>
								</tr>
								<tr>
									<td class="pl-2 form-label" >Department</td>
									<td class="px-1 text-sm border-b">{{ det.department_id }}</td>
									<td class="px-1 text-sm" width="3%"></td>
									<td class="form-label">Enduse</td>
									<td class="px-1 text-sm border-b" colspan="2">{{ det.enduse_id }}</td>
								</tr>
								<tr>
									<td class="pl-2 form-label">Purpose</td>
									<td class="px-1 text-sm " colspan="5">{{ det.purpose_id }}</td>
								</tr>
								<tr>
									<td class="p-1" colspan="5"></td>
								</tr>
							</table>
						</div>
					</div>	
					<div class="row">
						<div class="col-lg-12">
							<table class="table table-actions table-bordered table-hodver mb-0 border-t-2">
								<span hidden>{{ x=1 }}</span>
								<tbody  v-for="(it,index) in det.receive_items.items">
									<tbody v-if="det.explode_modes[index]!='deduct'" :class="(it.eval_flag==1) ? '' : (it.eval_flag==2) ? '!border-2 !border-red-400' : ''">
									<tr class="">
										<td class="p-1 text-center font-bold" rowspan="5">
											{{ x }}
											<!-- {{ it.item_no }} -->
											<!-- {{ det.explode_modes[index] }} -->
										</td>
										<td class="font-xxs uppercase font-bold " width="20%">Supplier</td>
										<td class="font-xxs uppercase font-bold " width="35%" colspan="2">Description</td>
										<td class="font-xxs uppercase font-bold " width="8%">Item status</td>
										<td class="font-xxs uppercase font-bold text-center" width="15%">Shipping/U & Other Cost</td>
										<td class="font-xxs uppercase font-bold text-center" width="7%">Unit Cost</td>
										<td class="font-xxs uppercase font-bold text-center" width="5%">Currency</td>
										<td class="font-xxs uppercase font-bold text-center" width="5%">Exp Qty</td>
										<td class="font-xxs uppercase font-bold text-center" width="5%">Recv Qty</td>
										<!-- <td class="font-xxs uppercase font-bold " width="7%">Original Recv Qty</td> -->
										<td class="font-xxs uppercase font-bold text-center rej-qty" :class="(it.eval_flag==2) ? '!border-2 !border-b-none !border-red-400 bg-red-200' : ''" width="5%">Rej Qty</td>
										<td class="font-xxs uppercase font-bold px-1"><span class="">Total Cost</span></td>
									</tr>
									<tr>
										<td class="p-1 font-xxss">{{ it.supplier_name }}</td>
										<td class="p-1 font-xxss" colspan="2">{{ it.item_description + ' - ' + it.pn_no}}</td>
										<td class="p-1 font-xxss text-left">{{ it.item_status }}</td>
										<td class="p-1 font-xxss text-center">{{ it.shipping_cost }}</td>
										<td class="p-1 font-xxss text-center">{{ it.unit_cost }}</td>
										<td class="p-1 font-xxss text-center">{{ (it.currency!=null) ? it.currency : '' }}</td>
										<td class="p-1 font-xxss text-center">{{ it.exp_quantity }}</td>
										<td class="p-1 font-xxss text-center">{{ it.rec_quantity }}</td>
										<!-- <td class="p-1 font-xxss">{{ it.rec_quantity + it.rejected_qty }}</td> -->
										<td class="p-1 font-xxss text-center rej-qty" :class="(it.eval_flag==2) ? '!border-2 !border-red-400 bg-red-200 font-bold !text-xs !p-0' : ''">
											<div class="popup" @click="popUpremarks(it.id)">
												{{ it.rejected_qty }}
												<span class="popuptext" :id="'myPopup'+it.id">{{ (it.eval_reason!=null) ? it.eval_reason : ''}}</span>
											</div>
										</td>
										<td class="p-1 font-xxss text-center">{{ ((parseFloat(it.unit_cost) + parseFloat(it.shipping_cost)) *  it.rec_quantity).toFixed(2)}}</td>
									</tr>
									<tr class="">
										<td class="font-xxs uppercase font-bold" width="15%">Brand</td>
										<td class="font-xxs uppercase font-bold" width="15%">Cat No.</td>
										<td class="font-xxs uppercase font-bold" width="15%">Serial No.</td>
										<td class="font-xxs uppercase font-bold" width="1%">UOM</td>
										<td class="font-xxs uppercase font-bold" width="5%">Color</td>
										<td class="font-xxs uppercase font-bold" width="5%" colspan="2">Size</td>
										<td class="font-xxs uppercase font-bold text-left" colspan="5">Expiry Date</td>
									</tr>
									<tr>
										<td class="p-1 font-xxss">{{ it.brand }}</td>
										<td class="p-1 font-xxss">{{ it.catalog_no }}</td>
										<td class="p-1 font-xxss">{{ it.serial_no }}</td>
										<td class="p-1 font-xxss">{{ it.uom }}</td>
										<td class="p-1 font-xxss">{{ it.color }}</td>
										<td class="p-1 font-xxss" colspan="2">{{ it.size }}</td>
										<td class="p-1 font-xxss text-left" colspan="5">{{ it.expiry_date }} </td>
										
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
										<td class="p-1 font-xxss text-left" colspan="7">
											<span class="italic" v-if="it.pr_replenish == 1">Replenish: Yes</span> 
											<span class="italic" v-else>Replenish: No</span> 
										</td>
									</tr>
									<tr v-if="it.eval_flag==0">
											<td colspan="2" class="bg-yellow-100 py-2 " align="center">
												<span class="flex justid space-x-2 w-10 ">
													<label class="text-xs font-bold uppercase p-1">Accept/Reject:</label>
													<input type="checkbox" :id="'tickbox'+it.id"  class="!w-9 form-control tickbox" v-model="checkbox[it.id]" :true-value="it.id" false-value="0" v-if="it.eval_flag==0" @change="tickBox()" @click="tickBox()">
												</span>
											</td>
											<td colspan="10" class="bg-yellow-100 py-2">
												<div class="flex space-x-5 p-0 h-6 pl-5">
													<div class="flex space-x-1">
														<label :id="'rej_label'+it.id" style="display: none;" class="text-xs font-bold uppercase p-1" v-if="it.eval_flag==0">Reject Qty:</label>
														<input  style="display: none;" type="number" placeholder="0" :id="'rejected_qty'+it.id" class="!w-20 font-bold border py-2 text-center pl-3 px-0 form-control m-0  rejected_qty" v-model="rejected_qty[it.id]" @blur="checkAcceptanceQty(index)" min="0" onkeypress="return (event.charCode !=8 && event.charCode ==0 || (event.charCode >= 48 && event.charCode <= 57))" v-if="it.eval_flag==0">
													</div>
													<div class="flex space-x-1 w-full" >
														<label :id="'rejected_label'+it.id" style="display: none;" class="text-xs font-bold uppercase p-1" v-if="it.eval_flag==0">Reject Remarks:</label>
														<textarea :id="'rejected_remarks'+it.id" style="display: none;" class="!w-full font-bold border py-2 pl-3 px-0 form-control m-0 z-50 rejected_qty" v-model="rejected_remarks[it.id]" v-if="it.eval_flag==0"></textarea>
													</div>
													<input type="hidden" class="rec_qty" :id="'rec_qty'+it.id" :value="it.rec_quantity">
												</div>
											</td>
									</tr>
									<input type="hidden" class="id" :id="'id'+it.id" :value="it.id">
									<span hidden> {{ x++ }}</span>
									</tbody>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<hr class="border-dashed">
				<div class="flex justify-center space-x-1 my-2">
					<button class="btn bg-green-600 text-white btn-round btn-sm accepted" @click="openAccept()" id="accepted" v-if="pending_items!=0">
						<div class="flex justify-center space-x-1">
							<CheckIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"></CheckIcon>
							<span>Save</span>
						</div>
					</button>
					<!-- <button class="btn bg-red-600 text-white btn-round btn-sm rejected"  @click="openReject()" id="rejected" v-if="pending_items!=0">
						<div class="flex justify-center space-x-1">
							<XMarkIcon l="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"></XMarkIcon >
							<span>Reject</span>
						</div>
					</button> -->
					<button @click="onPrint(props.id)" class="btn btn-primary text-white btn-round btn-sm" v-if="pending_items==0">
						<div class="flex justify-center space-x-1">
							<PrinterIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"></PrinterIcon>
							<span>Print</span>
						</div>
					</button>
				</div>
			</div>
		</div>
		<Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
			<div class="modal pt-4 px-3" :class="{ show:acceptModal }">
				<div @click="closeModalHead" class="w-full h-full fixed"></div>
				<div class="modal__content w-4/12 !mt-20">
					<div class="modal_s_items">
						<div class=" ">
							<div class="row">
								<div class="col-lg-12">
                                    <div class="border-b mb-3">
                                        <h6 class=" font-bold uppercase">Save Receive</h6>
                                    </div>
                                    <div class="">
                                        <div class="form-group w-full">
                                            <label class="form-label mb-0">Date</label>
                                            <input type="date" class="form-control border" v-model="eval_date">
                                        </div>
                                        <div class="form-group w-full" id="check_accepted">
                                            <label class="form-label mb-0">Accepted by</label>
                                            <select class="form-control border py-2" v-model="eval_user">
                                                <option value="">--Select Accepted By--</option>
                                                <option v-for="emp in insemployees" :value="emp.id">{{ emp.name }}</option>
                                            </select>
                                        </div>
										<!-- <div class="form-group w-full" v-if="rejected_checker!=0" id="check_rejected">
                                            <label class="form-label mb-0">Rejected by</label>
                                            <select class="form-control border py-2" v-model="eval_user_reject">
                                                <option value="">--Select Rejected By--</option>
                                                <option v-for="emp in insemployees" :value="emp.id">{{ emp.name }}</option>
                                            </select>
                                        </div> -->
                                    </div>
                                    <div class="pt-2 mb-2 flex justify-end">
                                        <button class="btn btn-sm btn-primary btn-rounded w-32" id="savenew" @click="saveNew()">Submit</button>
                                    </div>
								</div>
							</div>
						</div>		
					</div> 
				</div>
			</div>
		</Transition>
        <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
			<div class="modal pt-4 px-3" :class="{ show:rejectModal }">
				<div @click="closeModalHead" class="w-full h-full fixed"></div>
				<div class="modal__content w-6/12 !mt-20">
					<div class="modal_s_items">
						<div class=" ">
							<div class="row">
								<div class="col-lg-12">
                                    <div class="border-b mb-3">
                                        <h6 class=" font-bold uppercase">Reject Receive</h6>
                                    </div>
                                    <div class="flex justify-between space-x-2">
                                        <div class="form-group w-5/12">
                                            <label class="form-label mb-0">Date</label>
                                            <input type="date" class="form-control border" v-model="eval_date">
                                        </div>
                                        <div class="form-group w-full">
                                            <label class="form-label mb-0">Rejected by</label>
                                            <select class="form-control border py-2" v-model="eval_user">
                                                <option value="">--Select Rejected By--</option>
                                                <option v-for="emp in rejemployees" :value="emp.id">{{ emp.name }}</option>
                                            </select>
                                        </div>
										<!-- <div class="form-group w-full">
                                            <label class="form-label mb-0">Quantity</label>
                                            <input type="number" min="0" class="form-control border" v-model="rejected_qty">
                                        </div> -->
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label mb-0">Reason</label>
                                        <textarea  class="form-control border" v-model="eval_reason"></textarea>
                                    </div>
                                    <div class="pt-2 mb-2 flex justify-end">
                                        <button class="btn btn-sm btn-primary btn-rounded w-32" @click="saveRejected()">Submit</button>
                                    </div>
								</div>
							</div>
						</div>		
					</div> 
				</div>
			</div>
		</Transition>
    </navigation>
</template>
