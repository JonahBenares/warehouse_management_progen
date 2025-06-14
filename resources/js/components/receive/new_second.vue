<script setup>
	import{ onMounted, ref } from "vue"
	
	import navigation from '@/layouts/navigation.vue';
	import { CheckCircleIcon, TrashIcon, PlusIcon, XMarkIcon, ExclamationCircleIcon, ArrowUturnLeftIcon, ChevronRightIcon, ChevronLeftIcon  } from '@heroicons/vue/24/solid'
    import { useRouter } from "vue-router"
import { it } from "vuetify/locale";
    const router = useRouter()

	let form = ref({
        id:'',
		detail_id:''
    })
	
	let rows = ref([])
	
	let departments = ref([])
	let enduses = ref([])
	let users = ref([])
	let inspected = ref([])
	let purposes = ref([])
	let details = ref([])
	let alldetails = ref([])
	let supplier = ref([])
	let brand = ref([])
	let resultItems = ref([])
	let resultPR = ref([])
	let itemStatus = ref([])
	let error = ref([])
	let error_items = ref([])
	let success = ref('')
	let max = ref('')
	let uom=ref([]);
	let colors=ref([]);
	let sizes=ref([]);
	let prreplenish=ref([]);
	let currency=ref([]);
	

	const props = defineProps({
        id:{
            type:String,
            default:''
        },
		detail_id:{
            type:String,
            default:''
        }
    })

	
	onMounted(async () =>{
        getReceiveHead()
		getReceiveDetails(props.id, props.detail_id)
		getReceiveItems(props.id, props.detail_id)
		supplierList()
		getItems()
		getPR()
		statusList()
		getLatestDetailNo(props.id)
		brandList()
		getAllReceiveDetails()
    })

    const getReceiveHead = async () => {
        let response = await axios.get(`/api/get_receive_head/${props.id}`)
        form.value = response.data.head
		
    }

	const getReceiveDetails = async (id, detail_id) => {
		
        let response = await axios.get(`/api/create_receive_details/${props.id}/${props.detail_id}`)
		departments.value = response.data.department
		enduses.value = response.data.enduse
		users.value = response.data.user
		inspected.value = response.data.inspected
		purposes.value = response.data.purpose
		details.value = response.data.formData;
		currency.value = response.data.currency;
    }

	const getAllReceiveDetails = async () => {
		let response = await axios.get(`/api/get_receive_details/${props.id}`)
		alldetails.value = response.data.details
	}

	const getReceiveItems = async (id, detail_id) => {
		
        let response = await axios.get(`/api/create_receive_items/${props.id}/${props.detail_id}`)
		rows.value = response.data.formItems;
 
    }

	const supplierList = async () => {
		let response = await axios.get("/api/supplier_list");
		supplier.value = response.data.supplier;
	}

	const brandList = async () => {
		let response = await axios.get("/api/brand_list");
		brand.value = response.data.brand;
	}

	const getItems = async() => {
		let response = await axios.get("/api/item_list_composite");
		resultItems.value = response.data.items;

    }

	const getPR = async() => {
		let response = await axios.get("/api/pr_list");
		resultPR.value = response.data.pr;
		//resultPR.push('WH Stocks')
		
    }
	const statusList = async() => {
		let response = await axios.get("/api/status_list");
		itemStatus.value = response.data.status;
	
    }

	const getLatestDetailNo = async(id) => {

		let response = await axios.get("/api/get_latest_detail_no/"+id);
		max.value = response.data;
    }

	
	
	const showModal = ref(false)
	const hideModal = ref(true)
	const openModel = () => {
		showModal.value = !showModal.value
		getAllReceiveDetails()
	}
	const closeModal = () => {
		showModal.value = !hideModal.value
	}

	const addRow= () =>{
		rows.value.push(
			{
				id:"",
			 supplier: "",
			 description: "",
			 brand: "",
			 color:"",
			 size:"",
			 catalog_no: "",
			 serial_no: "",
			 uom: "",
			 unit_cost: "",
			 currency: "",
			 shipping_cost:"",
			 exp_quantity: "",
			 rec_quantity: "",
			 remarks: "",
			 item_status: "",
			 expiry_date: "",
			 location: "",
			 pr_replenish:""
			}
		);
	}

	const removeRow= (row, id) =>{

		if(confirm("Do you really want to delete this row?")){
			rows.value.splice(row,1)
			
			if(id){
				axios.get(`/api/delete_receive_item/${id}`);
			}
		}
	}

	const totalCost = () => {
        let total = 0
        rows.value.map((data)=>{
            total = total + (data.rec_quantity*data.unit_cost)
        })
        return total
    }

	const autosuggestColor = async (index) => {
	for (var i = 0; i < rows.value.length; i++) {
		if(index==i){
			let response = await fetch('/api/search_colors?filter='+rows.value[i].color);
			colors.value = await response.json();
		}
	} 
}

const autosuggestSize = async (index) => {
	for (var i = 0; i < rows.value.length; i++) {
		if(index==i){
			let response = await fetch('/api/search_size?filter='+rows.value[i].size);
			sizes.value = await response.json();
		}
	}
}


	const autosuggestUom = async (index) => {

		for (var i = 0; i < rows.value.length; i++) {
			if(index==i){
				let response = await fetch('/api/search_uom?filter='+rows.value[i].uom);
				uom.value = await response.json();
			}
		}
	}


	const saveDraft = (id) => {
		
		const formDetails = new FormData()

		let wh_stocks=document.getElementById('wh_stocks');
			if(wh_stocks.checked){
				var prno = "WH Stocks";
			} else {
				var prno = details.value.pr_no;
			}

			
		formDetails.append('receive_head_id', details.value.receive_head_id)
		formDetails.append('detail_no', details.value.detail_no)
		formDetails.append('pr_no', prno)
		formDetails.append('department_id', details.value.department_id)
		formDetails.append('inspected_id', details.value.inspected_id)
		formDetails.append('enduse_id', details.value.enduse_id)
		formDetails.append('purpose_id', details.value.purpose_id)
		formDetails.append('receive_items', JSON.stringify(rows.value))
		
	
		axios.post(`/api/save_draft_details/${form.value.id}`, formDetails).then(function () {
			
			error.value=[]
			error_items.value=[]
		
			success.value='Saved as draft'
			}, function (err) {

				error.value=[]
				error_items.value=[]
				
				
		});

	}

	const saveTransaction = (id, method) => {
		
		if(confirm("Are you sure you want to save this transaction?")){
			error_items.value=[]
				const formDetails = new FormData()

				let wh_stocks=document.getElementById('wh_stocks');
				if(wh_stocks.checked){
					var prno = "WH Stocks";
				} else {
					var prno = details.value.pr_no;
				}

				
				formDetails.append('receive_head_id', details.value.receive_head_id)
				formDetails.append('detail_no', details.value.detail_no)
				formDetails.append('pr_no', prno)
				formDetails.append('department_id', details.value.department_id)
				formDetails.append('inspected_id', details.value.inspected_id)
				formDetails.append('enduse_id', details.value.enduse_id)
				formDetails.append('purpose_id', details.value.purpose_id)
				formDetails.append('receive_items', JSON.stringify(rows.value))

				const no_of_rows = rows.value.length
				
				for(var x=0;x<no_of_rows;x++){
					var y = x + 1;
					if(rows.value[x].supplier == ''){
						error_items.value.push('Supplier in row ' + y + ' must not be empty.')
					}
					if(rows.value[x].description == ''){
						error_items.value.push('Item description in row ' + y + ' must not be empty.')
					}
					
					if(rows.value[x].exp_quantity == ''){
						error_items.value.push('Expected quantity in row ' + y + ' must not be empty.')
					}
					if(rows.value[x].rec_quantity == ''){
						error_items.value.push('Received quantity in row ' + y + ' must not be empty.')
					}

					
					if(parseInt(rows.value[x].rec_quantity) > parseInt(rows.value[x].exp_quantity)){
						error_items.value.push('Received quantity in row ' + y + ' must not be greater than the expected quantity.')
					}
					if(rows.value[x].item_status == ''){
						error_items.value.push('Item status in row ' + y + ' must not be empty.')
					}
					
				}
		
		
			axios.post(`/api/save_receive/${form.value.id}`, formDetails).then(function (response) {
				
				error.value=[]
					document.getElementById("success").style.display="block"
					if(error_items.value.length == 0){
						if(method=='save'){
							router.push('/receive/new_third/'+id)
						} else {
							window.location.href = '/receive/new_second/'+id+'/'+method;
						}
					}
					
				}, function (err) {
				
					error.value=[]
					document.getElementById("error").style.display="block"

					
					if (err.response.data.errors.pr_no) {
						error.value.push(err.response.data.errors.pr_no[0])
					}
					if (err.response.data.errors.department_id) {
						error.value.push(err.response.data.errors.department_id[0])
					}
					if (err.response.data.errors.enduse_id) {
						error.value.push(err.response.data.errors.enduse_id[0])
					}
					if (err.response.data.errors.inspected_id) {
						error.value.push(err.response.data.errors.inspected_id[0])
					}
					if (err.response.data.errors.purpose_id) {
						error.value.push(err.response.data.errors.purpose_id[0])
					}
					setTimeout(() => {
						document.getElementById("error").style.display="none"
					}, 4000);
				});
			 }
		}
	
		

		const addNewPR = (id, detailNo) => {
			if(confirm("Are you sure you want to add new PR?")){
				var new_detail = parseInt(detailNo) + 1
				saveTransaction(id, new_detail)				
			}
		}

		const cancelPR = (id, detailNo) => {
			if(confirm("Are you sure you want to cancel PR?")){
				if(detailNo == 1){
					//var new_detail=2;
					axios.get(`/api/cancel_pr/${id}/${detailNo}`).then(function () {
					window.history.back();
					});
				} else {
					var new_detail = parseInt(detailNo) - 1
					axios.get(`/api/cancel_pr/${id}/${detailNo}`).then(function () {
						window.location.href = '/receive/new_second/'+id+'/'+new_detail;
					});
				}
				
			}
		}

		const cancelTransaction = (id) => {
			if(confirm("Are you sure you want to cancel transaction?")){
				
				axios.get(`/api/cancel_transaction/${id}`).then(function () {
					router.push('/receive')
				});
			}
		}

		const navigate = (id, detailNo, path) => {
			if(path == 'back'){
				axios.get(`/api/navigate/${id}/${detailNo}/back`).then(function (response) {

					if(response.data!=''){
						window.location.href = '/receive/new_second/'+id+'/'+response.data
						// onBeforeRouteUpdate(async (to, from) => {
						// 	router.push('/receive/new_second/'+id+'/'+response.data)
						// })
					}
				});
			}
			if(path == 'next'){
				axios.get(`/api/navigate/${id}/${detailNo}/next`).then(function (response) {
					if(response.data!=''){
						window.location.href = '/receive/new_second/'+id+'/'+response.data
						//router.push('/receive/new_second/'+id+'/'+response.data)
					}
				});
			}
		}

		const checkWH = () => {

			let wh_stocks=document.getElementById('wh_stocks');
			if(wh_stocks.checked){
				document.getElementById('prno').value = '';
				document.getElementById('prno').disabled = true;
				
			}else{
				document.getElementById('prno').disabled = false;
				
			}
			
		}


		const getPRReplenish = (event, counter) =>{
			let item = event.target.value

			let wh_stocks=document.getElementById('wh_stocks');
				if(wh_stocks.checked){
					var pr = "WH Stocks";
				} else {
					var pr = document.getElementById('prno').value;
				}

			
			
			const myitem = item.split("~")
			let item_id = myitem[0];

			if(pr==""){
				alert("PR number is empty. Kindly fill out PR number first.");
				rows.value[counter].description=''
				
			} else {
				axios.get(`/api/get_PR_replenish/${item_id}/${pr}`).then(function (response) {
					
					var ct = response.data.length
					if(ct>0){

					alert("This item has been borrowed by this PR and needs to be replenished. Please check the PR to Replenish checkbox if you want to replenish this item.")
					prreplenish.value = response.data
					}
				
				});
			}
		}

		const emptyItems = ($event) =>{
		
			for(var y=0;y<rows.value.length;y++){
				rows.value[y].description=''
			}

			var pr = $event.target.value
			axios.get(`/api/get_pr_existing/${pr}`).then(function (response) {

				details.value.inspected_id = response.data.inspected_id
				details.value.department_id = response.data.department_id
				details.value.enduse_id = response.data.enduse_id
				details.value.purpose_id = response.data.purpose_id
			});
		}
</script>

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
								<p class="text-sm m-0 text-gray-400" v-for="err in error"> {{ err }}</p>
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
							<a href="/receive" class="btn btn-secondary btn-xs btn-rounded">
								<ArrowUturnLeftIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></ArrowUturnLeftIcon>
							</a>
						</div>
						<div class="flex justify-between ">
							<h6 class="m-0 pt-1 font-bold uppercase">Receive</h6>
							<!-- <h6 class="m-0 uppercase pt-1 mr-1">add new</h6> -->
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
			<div class="row">
				<div class="col-md-12 col-lg-12">
					<div class="card card-main-bg">
						<div class="p-2 ">
							<div class="px-3">
								<table class="w-full table-bordesred mt-3 mb-3">
									<tr>
										<td class="pr-1" width="20%">
											<div class="flex justify-start">
												<span class="text-lg uppercase font-bold text-gray-600 w-full leading-none">
													{{ form.mrecf_no }}
												</span>
											</div>
											<div class="flex justify-start ">
												<span class="text-xs text-gray-500 leading-none pt-1">MRIF NO.</span>
											</div>
										</td>
										<td class="px-1" width="15%">
											<div class="flex justify-start">
												<span class="text-lg uppercase font-bold text-gray-600 leading-none">{{ form.receive_date }}</span>
											</div>
											<div class="flex justify-start" >
												<span class="text-xs text-gray-500 leading-none pt-1">DATE</span>
											</div>
										</td>
										
										<td width="3%"></td>
										<td class="px-1  "  width="15%">
											<div class="flex justify-start">
												<span class="text-md uppercase font-bold text-gray-600 leading-none">{{ form.dr_no }}</span>
											</div>
											<div class="flex justify-start">
												<span class="text-xs text-gray-500 leading-none pt-1">DR NUMBER</span>
											</div>
										</td>
										<td class="px-1  " width="15%">
											<div class="flex justify-start">
												<span class="text-md uppercase font-bold text-gray-600 leading-none">{{ form.po_no }}</span>
											</div>
											<div class="flex justify-start" >
												<span class="text-xs text-gray-500 leading-none pt-1">PO NUMBER</span>
											</div>
										</td>
										<td class="px-1  " width="15%">
											<div class="flex justify-start">
												<span class="text-md uppercase font-bold text-gray-600 leading-none">{{ form.si_or }}</span>
											</div>
											<div class="flex justify-start" >
												<span class="text-xs text-gray-500 leading-none pt-1">SI/OR NUMBER</span>
											</div>
										</td>
										<td class="pr-1" width="20%">
											<div class="flex justify-start">
												<span class="text-lg uppercase font-bold text-gray-600 w-full leading-none">
													{{ form.waybill_no }}
												</span>
											</div>
											<div class="flex justify-start ">
												<span class="text-xs text-gray-500 leading-none pt-1">WAYBILL NO.</span>
											</div>
										</td>
										<td class="px-1  " width="2%">
											<span class="flex justify-center text-green-600">
												<CheckCircleIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" v-if="form.pcf === 1"></CheckCircleIcon>
												<span v-else></span>
											</span>
											<div class="flex justify-center ">
												<span class="text-xs text-gray-500 leading-none pt-1">PCF</span>
											</div>
										</td>
									</tr>
								</table>
							</div>
							<div class="row">
								<div class="col-lg-12">
									<div class="border-t-2 border-x-2 border-blue-400 rounded-t-lg">
										<table width="100%" class="table-borde5red">
											<tr class="">
												<td rowspan="4" width="3%" class="bg-blue-400 rounded-tl-sm">
													<input type="text" disabled v-model="details.detail_no" class=" bg-blue-400 text-lg text-center font-bold text-white rounded w-full">
												<input type="hidden"  v-model="details.receive_head_id">
												</td>
												<td width="12%"><label class="form-label mb-0 ml-2 mt-2">PR/JO/WH Stocks</label></td>
												<td  width="14%">
													<div class="!align-bottom">
														<div v-if="details.pr_no == 'WH Stocks'">
															<input type="checkbox" class="" id="wh_stocks" @change="checkWH()" checked>
															<label class="form-label mb-0 ml-2 mt-2">Warehouse Stock</label>
														</div>
														<div v-else>
															<input type="checkbox" class="" id="wh_stocks" @change="checkWH()">
															<label class="form-label mb-0 ml-2 mt-2">Warehouse Stock</label>
														</div>
													</div>
												</td>
												
												<td width="22%">
													<div v-if="details.pr_no == 'WH Stocks'">
														<input type="datalist" class="border-b w-full text-sm pt-1 pl-1  mt-2" id='prno' list="prlist" placeholder="PR/JO Number" disabled>	
														<datalist id="prlist">
															<option :value="respr.pr_no"  v-for="respr in resultPR"></option>
														</datalist>
													</div>
													<div v-else>
														<input type="datalist" class="border-b w-full text-sm pt-1 pl-1  mt-2" id='prno' list="prlist" placeholder="PR/JO Number" v-model="details.pr_no" @blur="emptyItems($event)">	
														<datalist id="prlist">
															<option :value="respr.pr_no"  v-for="respr in resultPR"></option>
														</datalist>
													</div>
												</td>
												<td width="4%"></td>
												<td width="9%"><label class="form-label mb-0 mt-2">Inspected by</label></td>
												<td width="41%">
													<select class="border-b w-full text-sm pt-1 mt-2" v-model="details.inspected_id">
														<option v-for="user in inspected" v-bind:key="user.id" v-bind:value="user.id +'~'+ user.name">{{  user.name }}</option>
													</select>
												</td>
												<td rowspan="3" width="5%" class="px-2"></td>
											</tr>
											<tr>
												<td><label class="form-label mb-0 ml-2">Department</label></td>
												<td colspan="2">
													<!-- <select class="border-b w-full text-sm pt-1" v-model="details.department_id" >
														<option v-for="dept in departments" v-bind:key="dept.id"  v-bind:value="dept.id  +'~'+ dept.department_name ">{{  dept.department_name }}</option>
													</select> -->
													<input type="datalist"  class="border-b w-full text-sm pt-1 pl-1  mt-2" list="deptlist" placeholder="Department" v-model="details.department_id" >	
													<datalist id="deptlist">
														<option v-bind:value="dept.department_name + ' #'+dept.id "  v-for="dept in departments" >{{ dept.department_name }}</option>
													</datalist>
													
												</td>
												<td></td>
											
												<td><label class="form-label mb-0">End-Use</label></td>
												<td>
													<!-- <select class="border-b w-full text-sm pt-1" v-model="details.enduse_id"  >
														<option v-for="end in enduses" v-bind:key="end.id" v-bind:value="end.id +'~'+ end.enduse_name">{{  end.enduse_name }}</option>
													</select> -->
													<input type="datalist"  class="border-b w-full text-sm pt-1 pl-1  mt-2" list="enduselist" placeholder="Enduse" v-model="details.enduse_id" >	
													<datalist id="enduselist">
														<option v-bind:value="end.enduse_name + ' #'+end.id "  v-for="end in enduses" >{{ end.enduse_name }}</option>
													</datalist>
												</td>
											</tr>
											<tr>
												<td><label class="form-label mb-2 ml-2">Purpose</label></td>
												<td colspan="5">
													<!-- <select class="border-b w-full text-sm pt-1 mb-3" v-model="details.purpose_id">
														<option v-for="purp in purposes" v-bind:key="purp.id" v-bind:value=" purp.id +'~'+ purp.purpose_name ">{{  purp.purpose_name }}</option>
													</select> -->

													<input type="datalist"  class="border-b w-full text-sm pt-1 pl-1  mt-2" list="purposelist" placeholder="Purpose" v-model="details.purpose_id" >	
													<datalist id="purposelist">
														<option v-bind:value="purp.purpose_name + ' #'+purp.id "  v-for="purp in purposes" >{{ purp.purpose_name }}</option>
													</datalist>
												</td>
											</tr>
											
										</table>
									</div>
								</div>
							</div>
							
							<div class="row" id="items">
								<div class="col-lg-12 ">
									<!-- <p class="text-danger" v-for="errit in error_items" v-if="error_items.length > 0"></p> -->
									<div class=" border-x-2 border-blue-400">
										<div class="" v-if="error_items.length > 0">
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
										
										
										<table class="table table-actions table-bordered  table-s mb-0">
											<tbody class="!border-t-2 !border-blue-400" v-for="(row,i) in rows">
												<tr class="bg-gray-200">
													<td class="p-0 text-center font-bold" rowspan="5" width="2%">{{ i + 1}}</td>
													<!-- <td class="font-xxs" width="3%">#</td> -->
													<td class="font-xxs uppercase font-bold" width="20%">Supplier</td>
													<td class="font-xxs uppercase font-bold" width="30%" colspan="2">Description</td>
													<td class="font-xxs uppercase font-bold">Item Status</td>
													<td class="font-xxs uppercase font-bold text-center" width="15%">Shipping/U & Other Cost</td>
													<td class="font-xxs uppercase font-bold text-center" width="7%">Unit Cost</td>
													<td class="font-xxs uppercase font-bold text-center" width="3%">Currency</td>
													<td class="font-xxs uppercase font-bold text-center" width="7%">Exp Qty</td>
													<td class="font-xxs uppercase font-bold text-center" width="7%">Recv Qty</td>
													<td class="font-xxs uppercase font-bold text-center" width="7%">Total Cost</td>
													<td class="p-1  font-bold" rowspan="5" width="5%">
														<div class="p-0 mb-1">
															<a class="text-white btn btn-xs btn-primary btn-rounded" @click="addRow()" >
																<PlusIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></PlusIcon>
															</a>
														</div>
														<div>
															<a class="text-white btn btn-xs btn-danger btn-rounded" @click="removeRow(i, row.id)">
																<TrashIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></TrashIcon>
															</a>
															
															<!-- <button class="btn btn-md !text-xs py-1 bg-blue-500 hover:bg-blue-600 text-white px-3">
															Add Item
														</button> -->
														</div>
													</td>
												</tr>
												<tr >
													<td class="p-0 font-xxss">
														<select class="p-1 m-0 w-full leading-none block text-xs whitespace-nowrap" v-model="row.supplier" :id="'supplier' + i">
															<option v-for="sup in supplier" v-bind:key="sup.id" v-bind:value="sup.id +'~'+ sup.supplier_name">{{  sup.supplier_name }}</option>
														</select>
													</td>
													
													<td class="p-0 font-xxss" colspan="2"> 
														
														<select class="p-1 m-0 w-full leading-none block text-xs whitespace-nowrap" :id="'description_' + i" v-model="row.description" @change="getPRReplenish($event, i)">
															<option v-for="it in resultItems" v-bind:key="it.id" v-bind:value="it.id +'~'+ it.item_description+'~'+ it.pn_no">{{  it.item_description+' - '+ it.pn_no }}</option>
														</select>																										
													</td>
													<td  class="p-0 font-xxss">
														<select class="p-1 m-0 leading-none w-28 block text-xs whitespace-nowrap" v-model="row.item_status">
															<option value="" disabled selected>Select Item Status</option>
															<option v-for="stat in itemStatus" v-bind:key="stat.id" v-bind:value="stat.id +'~'+ stat.status">{{  stat.status }}</option>
														</select>
													</td>
													
													<td class="p-0 font-xxss">
														<input type="text" rows="1" class="p-1 m-0 w-full leading-none block text-center" v-model="row.shipping_cost">
													</td>
													<td class="p-0 font-xxss">
														<input type="text" rows="1" class="p-1 m-0 w-full leading-none block text-center" v-model="row.unit_cost">
													</td>
													<td class="p-0 font-xxss">
														<select class="p-1 m-0 leading-none  block text-xs whitespace-nowrap" v-model="row.currency">
															<option value="" disabled selected>Select Currency</option>
															<option v-for="cur in currency" v-bind:key="cur" v-bind:value="cur">{{  cur }}</option>
														</select>
													</td>
													<td class="p-0 font-xxss">
														<input type="text" rows="1" class="p-1 m-0 w-full leading-none block text-center" v-model="row.exp_quantity">
													</td>
													<td class="p-0 font-xxss">
														<input type="text" rows="1" class="p-1 m-0 w-full leading-none block text-center"  v-model="row.rec_quantity">
													</td>
													<td class="p-1 font-xxss font-bold text-center bg-green-200"  v-if="row.rec_quantity">{{ (row.rec_quantity *  (parseFloat(row.unit_cost) + parseFloat(row.shipping_cost))).toFixed(2)}}</td>
													<td class="p-0 font-xxss" v-else></td>
												</tr>
												<tr class="bg-gray-200">
													<td class="font-xxs uppercase font-bold text-left">Brand</td>
													<td class="font-xxs uppercase font-bold text-left">Cat No.</td>
													<td class="font-xxs uppercase font-bold text-left">Serial No.</td>
													<td class="font-xxs uppercase font-bold text-left">UOM</td>
													<td class="font-xxs uppercase font-bold text-left">Color</td>
													<td class="font-xxs uppercase font-bold text-left" colspan="3">Size</td>
													<td class="font-xxs uppercase font-bold text-left" colspan="3">Expiry Date</td>
												</tr>
												<tr>
													<td class="p-0 font-xxss">
														<input type="text"  class="p-1 m-0 w-full leading-none block" list="brandlist" v-model="row.brand">
														<datalist id="brandlist">
															<option :value="br.brand"  v-for="br in brand"></option>
														</datalist>
													</td>
													<td class="p-0 font-xxss">
														<input type="text" rows="1" class="p-1 m-0 w-full leading-none block " v-model="row.catalog_no">
													</td>
													<td class="p-0 font-xxss">
														<input type="text" rows="1" class="p-1 m-0 w-full leading-none block " v-model="row.serial_no">
													</td>
													<td class="p-0 font-xxss">
														<input type="text" rows="1" class="p-1 m-0 w-full leading-none block " @keyup="autosuggestUom(i)"  v-model="row.uom" list="uom_list">
														<datalist id="uom_list">
															<option :value="u.uom" v-for="u in uom" :key="u.uom"></option>
														</datalist>
													</td>
													<td class="p-0 font-xxss">
														<input type="text" rows="1" class="p-1 m-0 w-full leading-none block " @keyup="autosuggestColor(i)" v-model="row.color" list="colors_list">
														<datalist id="colors_list">
															<option :value="c.color" v-for="c in colors" :key="c.color"></option>
														</datalist>
													</td>
													<td class="p-0 font-xxss" colspan="2">
														<input type="text" rows="1" class="p-1 m-0 w-full leading-none block " @keyup="autosuggestSize(i)" v-model="row.size" list="sizes_list">
														<datalist id="sizes_list">
															<option :value="s.size" v-for="s in sizes" :key="s.size"></option>
														</datalist>
													</td>
													<td class="p-0 font-xxss" colspan="3">
														<input type="text" v-model="row.expiry_date" class="p-1 m-0 w-full leading-none block " placeholder="Expiry Date" onfocus="(this.type='date')" onblur="(this.type='text')">
													</td>
												</tr>
												<tr>
													<td class="p-0" colspan="5">
														<textarea type="text" class="px-1 pt-1 w-full text-xs" placeholder="Remarks" rows="1" v-model="row.remarks"></textarea>
													</td>
													<td class="p-1 font-xxss">
														<div class="flex justify-center space-x-1">
															<span>Local</span><input type="radio" class="" value="local" v-model="row.location">
														</div>
													</td>  
													<td class="p-1 font-xxss">
														<div class="flex justify-center space-x-1">
															<span>Manila</span><input type="radio" class="" value="manila" v-model="row.location">
														</div>
													</td>
													<td class="p-1 font-xxss bg-orange-100 text-center" colspan="3">
														<div class="flex justify-center space-x-2"><input type="checkbox" v-model="row.pr_replenish" :true-value="1" :false-value="0"> <span>Replenish Item</span></div>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-lg-12">
									<div class="border-b-2 border-x-2 border-blue-400 p-2 rounded-b-lg bg-gray-200">
										<div class="flex justify-between">
											<div class="flex justify-between space-x-1">
												<button  @click="navigate(form.id, details.detail_no, 'back')" class="btn btn-md !text-xs py-1 bg-blue-400 hover:bg-blue-500 text-white px-3" title="Previous PR">
													Prev PR
												</button>
												<button v-if="max > details.detail_no" @click="navigate(form.id, details.detail_no, 'next')" class=" btn btn-md !text-xs py-1 bg-blue-400 hover:bg-blue-500 text-white px-3" title="Next PR">
													Next PR
												</button>
											</div>
											<div>
												<div class="flex justify-between space-x-1">
													<span class="px-5"></span>
													<button @click="cancelPR(form.id, details.detail_no)" type="submit" class="btn btn-md !text-xs py-1 bg-red-500 hover:bg-red-600 text-white px-3">Cancel PR</button>
													<button @click="addNewPR(form.id, details.detail_no)" class="btn btn-md  !text-xs py-1 bg-indigo-400 hover:bg-indigo-600 text-white px-3">Save & Add PR</button>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							
							<br class="border-dashed">
							<div class="row">
								<div class="col-lg-12"> 
									<div class="mb-2 mt-1 flex justify-between space-x-10">
										<button  @click="openModel()" class="btn btn-sm py-1.5 btn-success w-32">Preview</button>
										<div class="flex justify-between space-x-5">
											
											<div class="flex justify-between space-x-1">
												<button @click="cancelTransaction(form.id)"  class="btn btn-sm btn-danger" >Cancel Transaction</button>
												<button @click="saveDraft(form.id)" type="submit" class="btn btn-sm py-1.5 bg-orange-400 hover:bg-orange-600 w-32 text-white">Save as Draft</button>
												<button v-if="max < details.detail_no || max ==  details.detail_no" @click="saveTransaction(form.id,'save')" type="submit" class="btn btn-sm py-1.5 bg-blue-500 text-white hover:bg-blue-600 w-60 "> <span class="font-bold">Save & Finish</span></button>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>	
					</div>
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
			<div class="modal pt-4 px-3" :class="{ show:showModal }">
				<div @click="closeModal" class="w-full h-full fixed"></div>
				<div class="modal__content w-full">
					<!-- <div class="row mb-4">
						<div class="col-lg-12 flex justify-end">
							<a href="#" class="text-gray-600" @click="closeModal">
								<XMarkIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"></XMarkIcon>
							</a>
						</div>
					</div> -->
					<div class="modal_s_items">
						<div class=" p-3 px-4">
							<div class="row">
								<div class="col-lg-12">
									<table class="w-full border-collapse">
										<tr>
											<td class="pr-1" width="20%">
												<div class="flex justify-start">
													<span class="text-lg uppercase font-bold text-gray-600 w-full leading-none">
														{{ form.mrecf_no }}
													</span>
												</div>
												<div class="flex justify-start ">
													<span class="text-xs text-gray-500 leading-none pt-1">MRIF NO.</span>
												</div>
											</td>
											<td class="px-1" width="15%">
												<div class="flex justify-start">
													<span class="text-lg uppercase font-bold text-gray-600 leading-none">{{ form.receive_date }}</span>
												</div>
												<div class="flex justify-start" >
													<span class="text-xs text-gray-500 leading-none pt-1">DATE</span>
												</div>
											</td>
											
											<td width="3%"></td>
											<td class="px-1  "  width="15%">
												<div class="flex justify-start">
													<span class="text-md uppercase font-bold text-gray-600 leading-none">{{ form.dr_no }}</span>
												</div>
												<div class="flex justify-start">
													<span class="text-xs text-gray-500 leading-none pt-1">DR NUMBER</span>
												</div>
											</td>
											<td class="px-1  " width="15%">
												<div class="flex justify-start">
													<span class="text-md uppercase font-bold text-gray-600 leading-none">{{ form.po_no }}</span>
												</div>
												<div class="flex justify-start" >
													<span class="text-xs text-gray-500 leading-none pt-1">PO NUMBER</span>
												</div>
											</td>
											<td class="px-1  " width="15%">
												<div class="flex justify-start">
													<span class="text-md uppercase font-bold text-gray-600 leading-none">{{ form.si_or }}</span>
												</div>
												<div class="flex justify-start" >
													<span class="text-xs text-gray-500 leading-none pt-1">SI/OR NUMBER</span>
												</div>
											</td>
											<td class="pr-1" width="20%">
												<div class="flex justify-start">
													<span class="text-lg uppercase font-bold text-gray-600 w-full leading-none">
														{{ form.waybill_no }}
													</span>
												</div>
												<div class="flex justify-start ">
													<span class="text-xs text-gray-500 leading-none pt-1">WAYBILL NO.</span>
												</div>
											</td>
											<td class="px-1" width="2%">
												<span class="flex justify-center text-green-600">
													<CheckCircleIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" v-if="form.pcf === 1"></CheckCircleIcon>
													<span v-else></span>
												</span>
												<div class="flex justify-center ">
													<span class="text-xs text-gray-500 leading-none pt-1">PCF</span>
												</div>
											</td>
											<td>
												<div class=" flex justify-end">
													<a href="#" class="text-gray-600" @click="closeModal">
														<XMarkIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"></XMarkIcon>
													</a>
												</div>
											</td>
										</tr>
									</table>
								</div>
							</div>
						</div>	
					
						<div v-for="det in alldetails" class="border-2 border-blue-400 rounded mb-3 ">
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
											<td class="pt-2 px-1 text-sm border-b">{{ det.inspected_id }}</td>
										</tr>
										<tr>
											<td class="form-label" >Department</td>
											<td class="px-1 text-sm border-b">{{ det.department_id }}</td>
											<td class="px-1 text-sm" width="3%"></td>
											<td class="form-label">Enduse</td>
											<td class="px-1 text-sm border-b" colspan="2">{{ det.enduse_id }}</td>
										</tr>
										<tr>
											<td class="form-label">Purpose</td>
											<td class="px-1 text-sm border-b " colspan="5">{{ det.purpose_id }}</td>
										</tr>
									</table>
								</div>
							</div>	
							<div class="row">
								<div class="col-lg-12">
									<table class="table table-actions table-bordered table-hodver mb-0 border-t-2">
										<tbody  v-for="it in det.receive_items.items">
											<tr class="bg-gray-100">
												<td class="p-1 text-center font-bold" rowspan="5" width="2%">{{ it.item_no }}</td>
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
												<td class="p-1 font-xxss">{{ parseFloat((it.unit_cost + it.shipping_cost) * it.rec_quantity).toFixed(2)}}</td>
											</tr>
											<tr class="bg-gray-100">
												<td class="font-xxs uppercase font-bold" width="15%">Brand</td>
												<td class="font-xxs uppercase font-bold" width="15%">Cat No.</td>
												<td class="font-xxs uppercase font-bold" width="15%">Serial No.</td>
												<td class="font-xxs uppercase font-bold" width="1%">UOM</td>
												<td class="font-xxs uppercase font-bold" width="5%">Color</td>
												<td class="font-xxs uppercase font-bold" width="5%" colspan="2">Size</td>
												<td class="font-xxs uppercase font-bold" width="5%" colspan="2">Expiry Date</td>
											</tr>
											<tr>
												<td class="p-1 font-xxss">{{ it.brand }}</td>
												<td class="p-1 font-xxss">{{ it.catalog_no }}</td>
												<td class="p-1 font-xxss">{{ it.serial_no }}</td>
												<td class="p-1 font-xxss">{{ it.uom }}</td>
												<td class="p-1 font-xxss">{{ it.color }}</td>
												<td class="p-1 font-xxss" colspan="2">{{ it.size }}</td>
												<td class="p-1 font-xxss text-left" colspan="2">{{ it.expiry_date }} </td>
												
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
									<!-- <table class="table table-actions table-bordered table-hover mb-0">
										<thead>
											<tr>
												<th class="font-xxs" width="1%" >#</th>
												<th class="font-xxs" width="20%">Supplier</th>
												<th class="font-xxs" width="20%">Description</th>
												<th class="font-xxs" width="15%">Brand</th>
												<th class="font-xxs" width="15%">Cat No.</th>
												<th class="font-xxs" width="15%">Serial No.</th>
												<th class="font-xxs" width="1%">UOM</th>
												<th class="font-xxs" width="5%">Color</th>
												<th class="font-xxs" width="5%">Size</th>
												<th class="font-xxs" width="3%">Unit Cost</th>
												<th class="font-xxs" width="5%">Exp Qty</th>
												<th class="font-xxs" width="5%">Recv Qty</th>
												<th class="font-xxs" width="5%">Total Cost</th>
											</tr>
											
										</thead>
										<tbody  v-for="it in det.receive_items.items">
											<tr>
												<td class="p-1 text-center" rowspan="2">{{ it.item_no }}</td>
												<td class="p-1 font-xxss">{{ it.supplier_name }}</td>
												<td class="p-1 font-xxss">{{ it.item_description }}</td>
												<td class="p-1 font-xxss">{{ it.brand }}</td>
												<td class="p-1 font-xxss">{{ it.catalog_no }}</td>
												<td class="p-1 font-xxss">{{ it.serial_no }}</td>
												<td class="p-1 font-xxss">{{ it.uom }}</td>
												<td class="p-1 font-xxss">{{ it.color }}</td>
												<td class="p-1 font-xxss">{{ it.size }}</td>
												<td class="p-1 font-xxss">{{ it.unit_cost }}</td>
												<td class="p-1 font-xxss">{{ it.exp_quantity }}</td>
												<td class="p-1 font-xxss">{{ it.rec_quantity }}</td>
												<td class="p-1 font-xxss">{{ it.unit_cost *  it.rec_quantity}}</td>
											</tr>
											<tr>
												<td class="p-1 font-xxss text-left"><span class="italic">Item Status:</span> {{ it.item_status }}</td>
												<td class="p-1 font-xxss text-left"><span class="italic">Expiry Date:</span>{{ it.expiry_date }} </td>
												<td class="p-1 font-xxss text-left" colspan="4"><span class="italic">Remarks:</span> {{ it.remarks }}</td>
												<td class="p-1 font-xxss text-left" colspan="1">
													<div class="flex justify-center space-x-1">
														<span class="italic">Local:</span>
														<CheckCircleIcon v-if="it.location == 'local'" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"></CheckCircleIcon>
													</div>
												</td>
												<td class="p-1 font-xxss text-left" colspan="1">
													<div class="flex justify-center space-x-1">
													<span class="italic">Manila:</span>
														<CheckCircleIcon v-if="it.location == 'manila'" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"></CheckCircleIcon>
													</div>
												</td>
												<td class="p-1 font-xxss text-left" colspan="4"><span class="italic">Replenish:</span> </td>
											</tr>
										</tbody>
									</table> -->
								</div>
							</div>
						</div>	
					</div> 
				</div>
			</div>
		</Transition>

    </navigation>
</template>
