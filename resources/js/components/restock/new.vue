<script setup>
	import navigation from '@/layouts/navigation.vue';
	import { ExclamationCircleIcon ,CheckCircleIcon, PencilSquareIcon, TrashIcon, MinusIcon, PlusIcon, MagnifyingGlassIcon, ChevronLeftIcon, ChevronRightIcon, ArrowUturnLeftIcon } from '@heroicons/vue/24/solid'
	import{ onMounted, ref } from "vue"
    import { useRouter } from "vue-router"
    const router = useRouter()
	let form=ref([])
	let prlist =ref([])
	let error =ref([])
	let success = ref('')
	let purpose_id = ref('')
	let enduse_id = ref('')
	let department_id = ref('')
	let purpose = ref('')
	let enduser = ref('')
	let department = ref('')
	let count = ref(0)
	let purposelist = ref([])
	let enduserlist = ref([])
	let departmentlist = ref([])
	let purpose_arr = ref([])
	let enduser_arr = ref([])
	let department_arr = ref([])
	onMounted(async () => {
		restockheadform()
		prList()
		departmentList()
		purposeList()
		enduseList()
	})

	const restockheadform = async () => {
		let response = await axios.get("/api/create_restock_head");
		form.value = response.data;
	}

	const prList = async () => {
		// let response = await axios.get("/api/pr_request_list");
		// prlist.value = response.data.pr_request;
		let response = await axios.get("/api/pr_restock_list");
		prlist.value = response.data.pr_request; 
	}

	const departmentList = async () => {
		// let response = await axios.get("/api/pr_request_list");
		// prlist.value = response.data.pr_request;
		let response = await axios.get("/api/department_list");
		departmentlist.value = response.data.department; 
	}

	const purposeList = async () => {
		// let response = await axios.get("/api/pr_request_list");
		// prlist.value = response.data.pr_request;
		let response = await axios.get("/api/purpose_list");
		purposelist.value = response.data.purpose; 
	}

	const enduseList = async () => {
		// let response = await axios.get("/api/pr_request_list");
		// prlist.value = response.data.pr_request;
		let response = await axios.get("/api/enduse_list");
		enduserlist.value = response.data.enduse; 
	}

	const getPrdetails = async () => {
		let response = await axios.get("/api/get_pr_details/"+form.value.source_pr);
		purpose_id.value = response.data.purpose_id;
		enduse_id.value = response.data.enduse_id;
		department_id.value = response.data.department_id;
		purpose.value = response.data.purpose;
		enduser.value = response.data.enduser;
		department.value = response.data.department;
		form.value.destination = '';
		count.value = response.data.count;
	}

	const onSave = () => {
		const formData= new FormData()
		var enduseid = document.getElementById("enduse_id").value;
		var enduse_name = document.getElementById("enduse_name").value;
		var departmentid = document.getElementById("department_id").value;
		var department_name = document.getElementById("department_name").value;
		var purposeid = document.getElementById("purpose_id").value;
		var purpose_name = document.getElementById("purpose_name").value;
		formData.append('mrs_no', form.value.mrs_no)
		formData.append('date', form.value.date)
		formData.append('time', form.value.time)
		formData.append('source_pr', form.value.source_pr ?? '')
		formData.append('destination', form.value.destination)
		if(form.value.source_pr=='WH STOCKS'){
			formData.append('department_id', departmentid ?? 0)
			formData.append('purpose_id', purposeid ?? 0)
			formData.append('enduse_id', enduseid ?? 0)
			formData.append('department', department_name ?? '')
			formData.append('purpose', purpose_name ?? '')
			formData.append('enduser', enduse_name ?? '')
		}else{
			formData.append('department_id', department_id.value ?? 0)
			formData.append('purpose_id', purpose_id.value ?? 0)
			formData.append('enduse_id', enduse_id.value ?? 0)
			formData.append('department', department.value ?? '')
			formData.append('purpose', purpose.value ?? '')
			formData.append('enduser', enduser.value ?? '')
		}
		formData.append('user_id', form.value.user_id)
		if(confirm("Are you sure you want to save this transaction?")){
			axios.post("/api/add_restock_head",formData).then(function (response) {
				let parameter = response.data;
				var url_parameter=parameter.split('-')
				var restock_id = url_parameter[0];
				var pr_no = url_parameter[1];
				error.value=[]
				success.value='Successfully saved!'
				if(form.value.source_pr!='WH STOCKS' && form.value.destination=='Warehouse Stock'){
					router.push('/restock/new_second_whs/'+restock_id+'/'+pr_no)
				}else if(form.value.source_pr=='WH STOCKS' || form.value.destination=='Warehouse Stock'){
					router.push('/restock/new_second_whs/'+restock_id+'/'+pr_no)
				}else{
					router.push('/restock/new_second/'+restock_id+'/'+pr_no)
				}
				
			}, function (err) {
				error.value=[]
				document.getElementById("error").style.display="block"
				if (err.response.data.errors.mrs_no) {
					error.value.push(err.response.data.errors.mrs_no[0])
				}
				if (err.response.data.errors.destination) {
					error.value.push(err.response.data.errors.destination[0])
				}
				if (err.response.data.errors.source_pr) {
					error.value.push(err.response.data.errors.source_pr[0])
				}
				setTimeout(() => {
					document.getElementById("error").style.display="none"
				}, 4000);
			});
		}
	}
</script>

<template>
	<div class="col-lg-4 offset-lg-4">
		<div class="flex content-center">
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
		</div>
	</div>
    <navigation>
        <div class="container-fluid">
			<div class="card mb-3">	
				<div class="flex justify-between content-center">
					<div class="flex justify-start space-x-3 ">
						<div class="">
							<a href="/restock" class="btn btn-secondary btn-xs btn-rounded">
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
								<li class="breadcrumb-item active" aria-current="page">Add New Restock</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>	


			<div class="row">
				<div class="col-md-12 col-lg-12 ">
					<div class="card card-main-bg">
						<div class="p-2 pt-3 px-4">
							<div class="row">
								<div class="col-lg-4 px-1">
									<div class="form-group mb-2">
										<div class="flex justify-start ">
											<span class="text-xs text-gray-500 leading-none">MRS NO.</span>
										</div>
										<span class="text-lg uppercase font-bold text-gray-700 w-full leading-none">
											<input type="text" class="form-control border my-1" disabled v-model="form.mrs_no">
										</span>
									</div>										
								</div>
								<div class="col-lg-4 px-1">
									<div class="form-group mb-2">
										<div class="flex justify-start ">
											<span class="text-xs text-gray-500 leading-none uppercase">Source PR Number</span>
										</div>
										<select v-model="form.source_pr" class="form-control border  my-1" @change="getPrdetails()">
											<option :value="'WH STOCKS'">WH STOCKS</option>
											<option :value="p.pr_no" v-for="p in prlist" :key="p.pr_no">{{ p.pr_no }}</option>
										</select>
									</div>										
								</div>
								<div class="col-lg-4 px-1">
									<div class="form-group mb-2">
										<div class="flex justify-start ">
											<span class="text-xs text-gray-500 leading-none uppercase">Destination</span>
										</div>
										<select v-model="form.destination" class="form-control border  my-1">
											<option value="Source PR" v-if="form.source_pr != 'WH STOCKS'">Source PR</option>
											<option value="Warehouse Stock">Warehouse Stock</option>
										</select>
									</div>										
								</div>
							</div>
							<div class="row">
								<td class="col-lg-2 px-1" width="10%">
									<div class="flex justify-start" >
										<span class="text-xs text-gray-500 leading-none">DATE</span>
									</div>
									<span class="text-lg uppercase text-gray-700 w-full leading-none">
										<input type="date" class="form-control border my-1" v-model="form.date">
									</span>
								</td>
								<td class="col-lg-2 px-1"  width="10%">
									<div class="flex justify-start">
										<span class="text-xs text-gray-500 leading-none">TIME</span>
									</div>
									<span class="text-lg uppercase text-gray-700 w-full leading-none">
										<input type="time" class="form-control border my-1 py-1" v-model="form.time">
									</span>
								</td>
								<div class="col-lg-4 px-1">
									<div class="form-group mb-2" v-if="form.source_pr=='WH STOCKS'">
										<div class="flex justify-start ">
											<span class="text-xs text-gray-500 leading-none uppercase">Enduser</span>
										</div>
										<select v-model="enduser_arr" class="form-control border  my-1">
											<option :value="enduse" v-for="enduse in enduserlist">{{ enduse.enduse_name }}</option>
										</select>
										<input type="hidden" id="enduse_id" :value="enduser_arr.id">
										<input type="hidden" id="enduse_name" :value="enduser_arr.enduse_name">
									</div>	
									<div class="form-group mb-2" v-else>
										<div class="flex justify-start ">
											<span class="text-xs text-gray-500 leading-none uppercase">Enduser</span>
										</div>
										<div class="form-control border my-1 py-2 h-8">{{ enduser }}</div>
										<input type="hidden" class="form-control border  my-1" v-model="enduser">
										<input type="hidden" class="form-control border  my-1" v-model="enduse_id">
										<input type="hidden" id="enduse_id" value="">
										<input type="hidden" id="enduse_name" value="">
									</div>									
								</div>
								<div class="col-lg-4 px-1">
									<div class="form-group mb-2" v-if="form.source_pr=='WH STOCKS'">
										<div class="flex justify-start ">
											<span class="text-xs text-gray-500 leading-none uppercase">Department</span>
										</div>
										<select v-model="department_arr" class="form-control border  my-1">
											<option :value="dep" v-for="dep in departmentlist">{{ dep.department_name }}</option>
										</select>
										<input type="hidden" id="department_id" :value="department_arr.id">
										<input type="hidden" id="department_name" :value="department_arr.department_name">
									</div>	
									<div class="form-group mb-2" v-else>
										<div class="flex justify-start ">
											<span class="text-xs text-gray-500 leading-none uppercase">Department</span>
										</div>
										<span class="form-control border my-1 py-2 h-8">{{ department }}</span>
										<input type="hidden" class="form-control border  my-1" v-model="department">
										<input type="hidden" class="form-control border  my-1" v-model="department_id">
										<input type="hidden" id="department_id" value="">
										<input type="hidden" id="department_name" value="">
									</div>										
								</div>
							</div>
							<div class="row mt-2">
								<div class="col-lg-12 px-1">
									<div class="form-group mb-2" v-if="form.source_pr=='WH STOCKS'">
										<div class="flex justify-start ">
											<span class="text-xs text-gray-500 leading-none uppercase">Purpose</span>
										</div>
										<select v-model="purpose_arr" class="form-control border  my-1">
											<option :value="pur" v-for="pur in purposelist">{{ pur.purpose_name }}</option>
										</select>
										<input type="hidden" id="purpose_id" :value="purpose_arr.id">
										<input type="hidden" id="purpose_name" :value="purpose_arr.purpose_name">
										<!-- <textarea text id="purpose_name">{{purpose_arr.purpose_name}}</textarea> -->
									</div>	
									<div class="form-group mb-2" v-else>
										<div class="flex justify-start ">
											<span class="text-xs text-gray-500 leading-none uppercase">Purpose</span>
										</div>
										<span class="form-control border my-1 py-2 h-8">{{ purpose }}</span>
										<input type="hidden" class="form-control border  my-1" v-model="purpose">
										<input type="hidden" class="form-control border  my-1" v-model="purpose_id">
										<input type="hidden" id="purpose_id" value="">
										<input type="hidden" id="purpose_name" value="">
										<!-- <textarea hidden id="purpose_name"></textarea> -->
									</div>										
								</div>
							</div>
						</div>
						<hr class="border-dashed m-2">	
						<div class="px-2 pt-2 mb-2 mt-1 flex justify-end space-x-10">
							<div class="flex justify-between space-x-1">
								<button @click="onSave()" class="btn btn-sm btn-primary btn-rounded w-32">Next</button>
							</div>
						</div>
						
					</div>
				</div>
			</div>
		</div>

    </navigation>

	 
</template>
