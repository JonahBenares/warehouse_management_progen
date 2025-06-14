<script setup>
import navigation from '@/layouts/navigation.vue';
import { CheckCircleIcon, ExclamationCircleIcon, ArrowUturnLeftIcon } from '@heroicons/vue/24/solid'
import {onMounted, ref} from "vue";
import { useRouter } from "vue-router";
const router = useRouter();
let form=ref([]);
let error = ref([])
let success = ref('')
let request_type=ref('');
let prnolist=ref([]);
let pr_no=ref('');
let departmentlist=ref([]);
// let department_id=ref([]);
let enduselist=ref([]);
// let enduse_id=ref([]);
let purposelist=ref([]);
let department=ref('');
let enduse=ref('');
let purpose=ref('');

onMounted(async () => {
	RequestHeadForm()
	getprno()
	getdepartment()
	getenduse()
	getpurpose()
	chooseprno()
})
	
    // const today = new Date();
    // const RequestDate = today.getFullYear() + '-' + (today.getMonth()+1) + '-' + today.getDate();
	// const RequestTime = today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();

	const RequestHeadForm = async () => {
	let response = await axios.get("/api/create_head");
	form.value = response.data;
	}

	const getprno = async () => {
	let response = await axios.get("/api/pr_request_list");
	prnolist.value=response.data.pr_request
	}

	const getdepartment = async () => {
	let response = await axios.get("/api/department_list");
	departmentlist.value=response.data.department
	}

	const getenduse = async () => {
	let response = await axios.get("/api/enduse_list");
	enduselist.value=response.data.enduse
	}

	const getpurpose = async () => {
	let response = await axios.get("/api/purpose_list");
	purposelist.value=response.data.purpose
	}

	const chooseprno = async () => {
		var prno = document.getElementById("pr_no").value;
		// var dept_id = document.getElementById("department_id");
		// var dept_name = document.getElementById("department_name");
		// var end_id = document.getElementById("enduse_id");
		// var end_name = document.getElementById("enduse_name");
		// var pur_id = document.getElementById("purpose_id");
		// var pur_name = document.getElementById("purpose_name");
		const btn = document.getElementById("SubmitButton");
		const dep = document.getElementById("department_");
		const end = document.getElementById("enduse_");
		const pur = document.getElementById("purpose_");
		let response = await axios.get("/api/choose_prno/"+prno);
		
		// dept_id.value=response.data.department_id
		// dept_name.value=response.data.department_name
		department.value=response.data.department_id

		// end_id.value=response.data.enduse_id
		// end_name.value=response.data.enduse_name
		enduse.value=response.data.enduse_id

		// pur_id.value=response.data.purpose_id
		// pur_name.value=response.data.purpose_name
		purpose.value=response.data.purpose_id
		if(prno != ''){
			btn.disabled = false
			dep.disabled = true;
			end.disabled = true;
			pur.disabled = true;
		}

	}

	// const onSave = () => {
	// 		if(confirm("Are you sure you want to proceed?")){
	// 			saveTransaction()				
	// 		}
	// 	}

		const saveTransaction = () => {
			if(confirm("Are you sure you want to proceed?")){
				//error.value=[]
				// var dept_name = document.getElementById('department_name').value;
				// var end_name = document.getElementById('enduse_name').value;
				// var pur_name = document.getElementById('purpose_name').value;
				// var prno = document.getElementById("pr_no").value;
				
				const formData=new FormData()
				const btn = document.getElementById("SubmitButton");
				const type = document.getElementById("request_type").value;
				
				formData.append('mreqf_no', form.value.mreqf_no)
				formData.append('request_date',form.value.request_date)
				formData.append('request_time',form.value.request_time)
				formData.append('request_type',request_type.value)
				if(type == 'WH STOCKS'){
					formData.append('pr_no','WH STOCKS')
				}else{
					formData.append('pr_no',pr_no.value)
				}
				formData.append('department_id',department.value)
				//formData.append('department_name',dept_name)
				formData.append('enduse_id',enduse.value)
				//formData.append('enduse_name',end_name)
				formData.append('purpose_id',purpose.value)
				//formData.append('purpose_name',pur_name)
				formData.append('remarks',form.value.remarks)
				formData.append('user_id', form.value.user_id)
				
				// if(request_type.value == 'With PR'){
				// 	if(prno == ''){
				// 		error.value.push('PR No must not be empty.')
				// 	}
				// }

				if(request_type.value == 'With PR'){
						if(pr_no.value == ''){
							//alert('PR No must not be empty.')
							error.value.push('PR No must not be empty.')
							btn.disabled = true;
						}else{
								axios.post("/api/add_head",formData).then(function (response) {
								btn.disabled = false;
								error.value=[]
								success.value='Successfully saved!'
								router.push('/request/new_second/'+response.data)
							}, function (err){
								error.value=[]
									document.getElementById("error").style.display="block"
									if (err.response.data.errors.request_type) {
										error.value.push(err.response.data.errors.request_type[0])
									}
									setTimeout(() => {
										document.getElementById("error").style.display="none"
									}, 4000);
							});
						}
					}else{
						axios.post("/api/add_head",formData).then(function (response) {
						btn.disabled = false;
							error.value=[]
							success.value='Successfully saved!'
							router.push('/request/new_second_wh/'+response.data)
						}, function (err){
								error.value=[]
									document.getElementById("error").style.display="block"
									if (err.response.data.errors.request_type) {
										error.value.push(err.response.data.errors.request_type[0])
									}
									setTimeout(() => {
										document.getElementById("error").style.display="none"
									}, 4000);
							});
					}
			}
		}

	const DisableInput = () => {
		
	const type = document.getElementById("request_type").value;
	const prno = document.getElementById("pr_no");
	const dept_ = document.getElementById("department_");
	const end_ = document.getElementById("enduse_");
	const pur_ = document.getElementById("purpose_");
		if (type == 'WH STOCKS'){
			pr_no.value = '';
			department.value = '';
			enduse.value = '';
			purpose.value = '';
			prno.disabled = true;
			dept_.disabled = false;
			end_.disabled = false;
			pur_.disabled = false;
		}else{
			pr_no.value = '';
			department.value = '';
			enduse.value = '';
			purpose.value = '';
			prno.disabled = false;
			// department.disabled = false;
			// enduse.disabled = false;
			// purpose.disabled = false;
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
			<!-- BreadCrumb -->
			<div class="card mb-3">	
				<div class="flex justify-between content-center">
					<div class="flex justify-start space-x-3 ">
						<div class="">
							<a href="/request" class="btn btn-secondary btn-xs btn-rounded">
								<ArrowUturnLeftIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></ArrowUturnLeftIcon>
							</a>
						</div>
						<div>
							<h6 class="m-0 pt-1 font-bold uppercase">Request</h6>
						</div>
					</div>	
					<div class="pt-1">	
						<nav aria-label="breadcrumb" role="navigation">
							<ol class="breadcrumb adminx-page-breadcrumb">
								<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
								<li class="breadcrumb-item"><a href="/request">Request</a></li>
								<li class="breadcrumb-item active" aria-current="page">Add New Request</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>	

			<div class="row">
				<div class="col-md-12 col-lg-12">
					<div class="card card-main-bg">
						<div class="pt-3 p-2">
							<div class="row">
								<div class="col-lg-12">
									<table class="w-full table-borderded">
										<tr>
											<td width="25%" class="px-1">
												<div class="flex justify-start ">
													<span class="text-xs text-gray-500 leading-none">MREQF NO.</span>
												</div>
												<span class="text-lg uppercase font-bold text-gray-700 w-full leading-none">
													<input type="text" class="form-control border my-1" disabled v-model="form.mreqf_no">
												</span>
											</td>
											<td width="25%" class="px-1">
												<div class="flex justify-start ">
													<span class="text-xs text-gray-500 leading-none">REQUEST TYPE</span>
												</div>
												<span class="text-lg uppercase text-gray-700 w-full leading-none">
													<select name="request_type" id="request_type" class="form-control border my-1" v-model="request_type" @change="DisableInput()">
														<option value="With PR">With PR</option>
														<option value="WH STOCKS">WH STOCKS</option>
													</select>
												</span>
											</td>
											<td width="15%" class="px-1">
												<div class="flex justify-start ">
													<span class="text-xs text-gray-500 leading-none">DATE</span>
												</div>
												<span class="text-lg uppercase text-gray-700 w-full leading-none">
													<input type="text" class="form-control border my-1" disabled  id="request_date" v-model="form.request_date" >
												</span>
											</td>
											<td width="10%" class="px-1">
												<div class="flex justify-start ">
													<span class="text-xs text-gray-500 leading-none">TIME</span>
												</div>
												<span class="text-lg uppercase text-gray-700 w-full leading-none">
													<input type="text" class="form-control border my-1" disabled  id="request_time" v-model="form.request_time" >
												</span>
											</td>
										</tr>
										<tr>
											<td class="px-1" >
												<div class="flex justify-start" >
													<span class="text-xs text-gray-500 leading-none">JO/PR NUMBER</span>
												</div>
												<span class="text-base  text-gray-600 w-full leading-none">
													<select name="pr_no" id="pr_no" class="form-control border  my-1" v-model="pr_no" @change="chooseprno()"> 
														<option :value="pr.pr_no" v-for="pr in prnolist" :key="pr.id">{{ pr.pr_no }}</option>
													</select>
												</span>
											</td>
											<td class="px-1" >
												<div class="flex justify-start" >
													<span class="text-xs text-gray-500 leading-none">DEPARTMENT</span>
												</div>
												<span class="text-base  text-gray-600 w-full leading-none">
													<select name="department" id="department_" class="form-control border  my-1" v-model="department">
														<option :value="department.id" v-for="department in departmentlist" :key="department.id">{{ department.department_name }}</option>
													</select>
													<!-- <input type="hidden" name="department_id" id="department_id" class="form-control"> -->
													<!-- <input type="hidden" name="department_name" id="department_name" class="form-control"> -->
												</span>
											</td> 
											<td class="px-1"  colspan="2">
												<div class="flex justify-start" >
													<span class="text-xs text-gray-500 leading-none">END-USE</span>
												</div>
												<span class="text-base  text-gray-600 w-full leading-none">
													<select name="enduse" id="enduse_" class="form-control border  my-1" v-model="enduse">
														<option :value="enduse.id" v-for="enduse in enduselist" :key="enduse.id">{{ enduse.enduse_name }}</option>
													</select>
													<!-- <input type="hidden" name="enduse_id" id="enduse_id" class="form-control"> -->
													<!-- <input type="hidden" name="enduse_name" id="enduse_name" class="form-control"> -->
												</span>
											</td>
										</tr>
										<tr>
											<td colspan="2" class="px-1">
												<div class="flex justify-start" >
													<span class="text-xs text-gray-500 leading-none">PURPOSE</span>
												</div>
												<span class="text-base  text-gray-600 w-full leading-none">
													<select name="purpose" id="purpose_" class="form-control border  my-1" v-model="purpose">
														<option :value="purpose.id" v-for="purpose in purposelist" :key="purpose.id">{{ purpose.purpose_name }}</option>
													</select>
													<!-- <input type="hidden" name="purpose_id" id="purpose_id" class="form-control"> -->
													<!-- <input type="hidden" name="purpose_name" id="purpose_name" class="form-control"> -->
												</span>
											</td>
											<td colspan="2" class="px-1">
												<div class="flex justify-start" >
													<span class="text-xs text-gray-500 leading-none">REMARKS</span>
												</div>
												<span class="text-lg  text-gray-600 w-full leading-none">
													<textarea name="" id="" rows="1" class="form-control border  my-1" v-model="form.remarks"></textarea>
												</span>
											</td>
										</tr>
										<input type="hidden" class="form-control border " v-model ='form.user_id'>
									</table>
								</div>
							</div>
						</div>
						<!-- <span>
							<p class="text-success" v-if="success">{{ success }}</p>
							<p class="text-danger" v-for="err in error" v-if="error.length > 0">{{ err }}</p>
						</span> -->
						<!-- <div class="p-2 pt-3">
							<div class="row">
								<div class="col-lg-3 ">
									<table class="w-full border border-r-0">
										<tr>
											<td class="px-1" colspan="2" >
												<div class="flex justify-start" >
													<span class="text-xs text-gray-500 leading-none">MREQF NO.</span>
												</div>
											</td>
										</tr>
										<tr>
											<td class="p-0 pt-0 border-r border-b leading-none" colspan="2">
												<span class="text-base font-bold text-gray-600 w-full leading-none">
													<input type="text" class="form-control border" disabled v-model="form.mreqf_no">
												</span>
											</td>
										</tr>
										<tr>
											<td class="px-1 leading-none">
												<span class="text-xs text-gray-500 leading-none">DATE</span>
											</td>
											<td class="px-1 leading-none">
												<span class="text-xs text-gray-500 leading-none">TIME</span>
											</td>
										</tr>
										<tr>
											<td class="p-0 pt-0 border-b leading-none" width="60%">
												<span class="text-base font-bold text-gray-600 w-full leading-none">
													<input name="request_date" id="request_date" type="text" class="form-control" v-model="RequestDate" disabled>
												</span>
											</td>
											<td class="p-0 pt-0 border-b leading-none" width="40%">
												<span class="text-base font-bold text-gray-600 w-full leading-none">
													<input name="request_time" id="request_time" type="text" class="form-control" v-model="RequestTime" disabled>
												</span>
											</td>
										</tr>
										<tr>
											<td class="px-1 leading-none" colspan="2">
												<span class="text-xs text-gray-500 leading-none">REQUEST TYPE</span>
											</td>
										</tr>
										<tr>
											<td class="p-0 pt-0 leading-none" colspan="2">
												<span class="text-base font-bold text-gray-600 w-full leading-none">
													<select name="request_type" id="request_type" class="form-control" v-model="request_type">
														<option value="With PR">With PR</option>
														<option value="WH Stocks">WH Stocks</option>
													</select>
												</span>
											</td>
										</tr>
									</table>
								</div>
								<div class="col-lg-9 pl-0">
									<table class="w-full border-4">
										<tr>
											<td class="px-1 border-r" width="25%">
												<div class="flex justify-start" >
													<span class="text-xs text-gray-500 leading-none">JO/PR NUMBER</span>
												</div>
											</td>
											<td class="px-1 border-r" width="25%">
												<div class="flex justify-start" >
													<span class="text-xs text-gray-500 leading-none">DEPARTMENT</span>
												</div>
											</td>
											<td class="px-1 border-r" width="25%">
												<div class="flex justify-start" >
													<span class="text-xs text-gray-500 leading-none">END-USE</span>
												</div>
											</td>
										</tr>
										<tr>
											<td class="p-0 pt-0 border-r border-b leading-none">
												<span class="text-base font-bold text-gray-600 w-full leading-none">
													<select name="pr_no" id="pr_no" class="form-control border" v-model="pr_no" @change="chooseprno()">
														<option :value="pr.pr_no" v-for="pr in prnolist" :key="pr.id">{{ pr.pr_no }}</option>
													</select>
												</span>
											</td>
											<td class="p-0 pt-0 border-r border-b leading-none">
												<span class="text-base font-bold text-gray-600 w-full leading-none">
													<select name="department" id="department" class="form-control border" v-model="form.department_id">
														<option :value="department.id" v-for="department in departmentlist" :key="department.id">{{ department.department_name }}</option>
													</select>
													<input type="hidden" name="department_id" id="department_id" class="form-control">
													<input type="hidden" name="department_name" id="department_name" class="form-control">
												</span>
											</td>
											<td class="p-0 pt-0 border-r border-b leading-none">
												<span class="text-base font-bold text-gray-600 w-full leading-none">
													<select name="enduse" id="enduse" class="form-control border" v-model="form.enduse_id">
														<option :value="enduse.id" v-for="enduse in enduselist" :key="enduse.id">{{ enduse.enduse_name }}</option>
													</select>
													<input type="hidden" name="enduse_id" id="enduse_id" class="form-control">
													<input type="hidden" name="enduse_name" id="enduse_name" class="form-control">
												</span>
											</td>
										</tr>
										<tr>
											<td class="px-1 border-r leading-none" colspan="3">
												<span class="text-xs text-gray-500 leading-none">PURPOSE</span>
											</td>
										</tr>
										<tr>
											<td class="p-0 pt-0 border-r border-b leading-none" colspan="3">
												<span class="text-base font-bold text-gray-600 w-full leading-none">
													<select name="purpose" id="purpose" class="form-control border" v-model="form.purpose_id">
														<option :value="purpose.id" v-for="purpose in purposelist" :key="purpose.id">{{ purpose.purpose_name }}</option>
													</select>
													<input type="hidden" name="purpose_id" id="purpose_id" class="form-control">
													<input type="hidden" name="purpose_name" id="purpose_name" class="form-control">
												</span>
											</td>
										</tr>
										<tr>
											<td class="px-1 border-r leading-none" colspan="3">
												<span class="text-xs text-gray-500 leading-none">REMARKS</span>
											</td>
										</tr>
										<tr>
											<td class="p-0 pt-0 border-r leading-none" colspan="3">
												<span class="text-lg font-bold text-gray-600 w-full leading-none">
													<textarea name="" id="" rows="1" class="form-control" v-model="form.remarks"></textarea>
												</span>
											</td>
										</tr>
										<input type="hidden" class="form-control border " v-model ='form.user_id'>
									</table>
								</div>
							</div>
						</div> -->
						<hr class="border-dashed m-2">	
						<div class="mb-2 mt-2 flex justify-end space-x-10">
							<div class="flex justify-between space-x-1">
								<button @click="saveTransaction()" id = "SubmitButton" class="btn btn-sm hover:bg-blue-600 bg-blue-500 text-white w-60">Next</button>
							</div>
						</div>
						<!-- <div class="pt-3 mb-2 mt-2 border-t flex justify-end space-x-10">
							<div class="flex justify-between space-x-1">
								<button @click="onSave()" class="btn btn-sm btn-primary btn-rounded w-32">Next</button>
							</div>
						</div> -->
					</div>
				</div>
			</div>
		</div>
    </navigation>
</template>
