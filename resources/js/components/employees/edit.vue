<script setup>
import navigation from '@/layouts/navigation.vue';
import { CheckCircleIcon, ExclamationCircleIcon, ArrowUturnLeftIcon } from '@heroicons/vue/24/solid'
import axios from 'axios';
import { fromJSON } from 'postcss';
import { onMounted, ref } from "vue";
import { useRouter } from "vue-router";
const router = useRouter();
let form = ref({ id:'' })
let randomstr = ref('')
let error = ref([])
let success = ref('')
let listdepartment=ref([]);
let department_id=ref([]);
const props = defineProps({
	id:{
		type:String,
		default:''
	}
})

onMounted(async () => {
	getEditEmployee()
	getdepartment()
})

const getEditEmployee = async () => {
	let response = await axios.get(`/api/edit_employees/${props.id}`);
	form.value=response.data.employees
	randomstr.value=response.data
}

const getdepartment = async () => {
	//await new Promise(resolve => setTimeout(resolve, 5000));
	let response = await axios.get("/api/department_list");
	listdepartment.value=response.data.department
}

const onEdit = (id) => {
	const formData=new FormData()
	let checkbox=document.getElementById('checkbox');
	if(checkbox.checked){
		var access=1
	}else{
		var access=0
	}
	formData.append('name',form.value.name)
	//if(form.value.temp_password==null && form.value.access==0 && form.value.email!=null){
		formData.append('email',form.value.email ?? '')
		if((form.value.temp_password==null || form.value.temp_password=='') && form.value.access==1 && form.value.email!=null){
			formData.append('temp_password',randomstr.value.random_string)
		}else{
			formData.append('temp_password',form.value.temp_password ?? '')
		}
		
		//formData.append('password',randomstr.value.random_string)
	//}
	formData.append('department_id',form.value.department_id)
	formData.append('position',form.value.position)
	formData.append('contact_no',form.value.contact_no)
	formData.append('user_type',form.value.user_type ?? '')
	formData.append('access',access)
	formData.append('noted_flag',form.value.noted_flag)
	formData.append('inspected_flag',form.value.inspected_flag)
	formData.append('delivered_flag',form.value.delivered_flag)
	formData.append('reviewed_flag',form.value.reviewed_flag)
	formData.append('released_flag',form.value.released_flag)
	formData.append('requested_flag',form.value.requested_flag)
	formData.append('approved_flag',form.value.approved_flag)
	formData.append('acknowledge_flag',form.value.acknowledge_flag)
	formData.append('received_flag',form.value.received_flag)
	formData.append('returned_flag',form.value.returned_flag)
	formData.append('recommend_flag',form.value.recommend_flag)
	// formData.append('accepted_flag',form.value.accepted_flag)
	// formData.append('rejected_flag',form.value.rejected_flag)
	axios.post(`/api/update_employees/`+id, formData).then(function () {
		error.value=[]
		success.value='You have successfully updated the data!'
		document.getElementById("success").style.display="block"
		document.getElementById("error").style.display="none"
		setTimeout(() => {
			document.getElementById("success").style.display="none"
		}, 3000);
	}).catch(function(err){
		success.value=''
		error.value=[]
		document.getElementById("error").style.display="block"
		if (err.response.data.errors.name) {
			error.value.push(err.response.data.errors.name[0])
		}
		if (err.response.data.errors.position) {
			error.value.push(err.response.data.errors.position[0])
		}
		if (err.response.data.errors.department_id) {
			error.value.push(err.response.data.errors.department_id[0])
		}
		if (err.response.data.errors.contact_no) {
			error.value.push(err.response.data.errors.contact_no[0])
		}
		if (err.response.data.errors.email) {
			error.value.push(err.response.data.errors.email[0])
		}
		if (err.response.data.errors.user_type) {
			error.value.push(err.response.data.errors.user_type[0])
		}
		if (err.response.data.errors.password) {
			error.value.push(err.response.data.errors.password[0])
		} 
		if (err.response.data.errors.temp_password) {
			error.value.push(err.response.data.errors.temp_password[0])
		} 
		document.getElementById("success").style.display="none"
		setTimeout(() => {
			document.getElementById("error").style.display="none"
		}, 3000);
	});
}

const showCredentials = () => {
	let show=document.getElementById('showCred');
	let checkbox=document.getElementById('checkbox');
	if(checkbox.checked){
		show.style.display = 'block';   
	}else{
		show.style.display = 'none'; 
	}
}
</script>

<template>
	<div>
		<div class="col-lg-4 offset-lg-4">
			<div class="flex content-center">
				<div class="hide-animates" v-if="success" id="success">
					<div class="alert alert-info alert-top my-2">
						<div class="flex justify-start space-x-1">
							<div>
								<CheckCircleIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12"></CheckCircleIcon>
							</div> 
							<div class="py-1">
								<h6 class="font-bold m-0">Updated!</h6>
								<p class="text-sm m-0 text-gray-400"> {{ success }}</p>
							</div>
						</div>
					</div>
				</div>
				<div v-else id="success"></div>
				<div class="hide-animates"  v-if="error.length > 0" id="error">
					<div class="alert alert-danger alert-top my-2" >
						<div class="flex justify-start space-x-2">
							<div>
								<ExclamationCircleIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12"></ExclamationCircleIcon>
							</div> 
							<div class="py-1">
								<h6 class="font-bold m-0">Error!</h6>
								<p class="text-sm m-0 text-gray-400"  v-for="err in error"> {{ err }}</p>
							</div>
						</div>
					</div>
				</div>
				<div v-else id="error"></div>
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
							<a onclick="history.back()" class="btn btn-secondary btn-xs btn-rounded text-white">
								<ArrowUturnLeftIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></ArrowUturnLeftIcon>
							</a>
						</div>
						<div>
							<h6 class="m-0 pt-1 font-bold uppercase">Employees</h6>
						</div>
					</div>	
					<div class="pt-1">	
						<nav aria-label="breadcrumb" role="navigation">
							<ol class="breadcrumb adminx-page-breadcrumb">
								<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
								<li class="breadcrumb-item"><a href="/employees">Employees</a></li>
								<li class="breadcrumb-item active" aria-current="page">Update Employee</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>	

			<div class="row">
				<div class="col-md-12 col-lg-12 ">
					<div class="card">
						<div class="mt-2 mb-2 border-b">
							<h6>Update Employee</h6>	
						</div>
						<!-- <span>
							<p class="text-success" v-if="success">{{ success }}</p>
							<p class="text-danger" v-for="err in error" v-if="error.length > 0">{{ err }}</p>
						</span> -->
						<div class="row">
							<div class="col-lg-6">
								<div class="form-group">
									<label class="form-label">Employee Name</label>
									<input type="text" class="form-control border" placeholder="Employee Name" v-model="form.name">
								</div>										
							</div>
							<div class="col-lg-6">
								<div class="form-group">
									<label class="form-label">Position</label>
									<input type="text" class="form-control border" placeholder="Position" v-model="form.position">
								</div>										
							</div>
						</div>
						<div class="row">
							<div class="col-lg-6">
								<div class="form-group">
									<label class="form-label">Department</label>
									<!-- <input type="text" class="form-control border" placeholder="Department" > -->
									<select name="department" id="department" class="form-control border" v-model="form.department_id">
										<option value="">--Select Department--</option>
										<option :value="department.id" v-for="department in listdepartment" :key="department.id" :selected="form.department_id==department.id">{{ department.department_name }}</option>
									</select>
									<!-- <input type="text" name="department" v-model="form.department_id" class="form-control border" list="department_list" placeholder="Department" autocomplete="off">
									<datalist id="department_list">
									<option :value="department.department_name" :data-id="department.id" v-for="department in listdepartment" :key="department.id"></option>
									</datalist> -->
								</div>										
							</div>
							<div class="col-lg-6">
								<div class="form-group">
									<label class="form-label">Contact Number</label>
									<input type="text" class="form-control border" placeholder="Contact Number" v-model="form.contact_no">
								</div>										
							</div>
							<!-- <div class="col-lg-3">
								<div class="form-group">
									<label class="form-label">Email</label>
									<input type="text" class="form-control border" placeholder="Email" v-model="form.email">
								</div>										
							</div>
							<div class="col-lg-3">
								<div class="form-group">
									<label class="form-label">Password</label>
									<input type="password" class="form-control border" placeholder="*****" v-model="form.password">
								</div>										
							</div> -->
						</div>
						<hr class=" border-dashed">
						<div class="row">
							<div class="col-lg-12">
								<div class="form-check">
									<label class="form-check-label text-sm">
									<input class="form-check-input"  id="checkbox" v-model="form.access"  true-value="1" false-value="0" type="checkbox" :checked="form.access==1" @click="showCredentials()"> Check the box if employee can access the system.
									</label>
								</div>
							</div>
						</div>
						<div class="row" v-if="form.access==1" id="showCred">
							<div class="col-lg-6">
								<div class="form-group">
									<label class="form-label">Email</label>
									<input type="text" class="form-control border" v-model="form.email" placeholder="Email">
									<!-- <input type="hidden" class="form-control border" v-model="form.temp_password" placeholder="Password" readonly> -->
								</div>										
							</div>
							<div class="col-lg-6" v-if="form.change_password==0">
								<div class="form-group">
									<label class="form-label">Password</label>
									<input v-if="form.temp_password==null || form.temp_password==''" type="text" class="form-control border" v-model="randomstr.random_string" placeholder="Password" readonly>
									<input v-else-if="form.temp_password!=null || form.temp_password!=''" type="text" class="form-control border" v-model="form.temp_password" placeholder="Password" readonly>
								</div>										
							</div>
							<div class="col-lg-6">
									<div class="form-group">
										<label class="form-label mb-0">Type</label>
										<select class="form-control border" v-model="form.user_type">
											<option value=""></option>
											<option value="Admin">Admin</option>
											<option value="Staff">Staff</option>
										</select>
									</div>										
								</div>
						</div>
						<!-- <div  v-if="form.access==0" class="row" style="display: none;" id="showCred">
							<div class="col-lg-3">
								<div class="form-group">
									<label class="form-label">Email</label>
									<input type="text" class="form-control border" v-model="form.email" placeholder="Email">
								</div>										
							</div>
							<div class="col-lg-3" v-if="form.temp_password==null">
								<div class="form-group">
									<label class="form-label">Password</label>
									<input type="text" class="form-control border" v-model="randomstr.random_string" placeholder="Password" readonly>
								</div>										
							</div>
						</div> -->
						<hr class="border-dashed">
						<div class="row ">
							<div class="col-lg-12">
								<p class="mb-0">Signatories</p>
								<table class="table table-bordered">
									<tr>
										<td align="center">Noted By</td>
										<td align="center">Inspected By</td>
										<td align="center">Delivered By</td>
										<td align="center">Reviewed By</td>
										<td align="center">Released By</td>
										<td align="center">Received By</td>
										<td align="center">Returned By</td>
										<td align="center">Requested By</td>
										<td align="center">Recommending Apporval</td>
										<td align="center">Approved By</td>
										<td align="center">Acknowledge By</td>
										<!-- <td align="center">Accepted By</td>
										<td align="center">Rejected By</td> -->
									</tr>
									<tr>
										<td align="center"><input type="checkbox" v-model="form.noted_flag" true-value="1" false-value="0"></td>
										<td align="center"><input type="checkbox" v-model="form.inspected_flag" true-value="1" false-value="0"></td>
										<td align="center"><input type="checkbox" v-model="form.delivered_flag" true-value="1" false-value="0"></td>
										<td align="center"><input type="checkbox" v-model="form.reviewed_flag" true-value="1" false-value="0"></td>
										<td align="center"><input type="checkbox" v-model="form.released_flag" true-value="1" false-value="0"></td>
										<td align="center"><input type="checkbox" v-model="form.received_flag" true-value="1" false-value="0"></td>
										<td align="center"><input type="checkbox" v-model="form.returned_flag" true-value="1" false-value="0"></td>
										<td align="center"><input type="checkbox" v-model="form.requested_flag" true-value="1" false-value="0"></td>
										<td align="center"><input type="checkbox" v-model="form.recommend_flag" true-value="1" false-value="0"></td>
										<td align="center"><input type="checkbox" v-model="form.approved_flag" true-value="1" false-value="0"></td>
										<td align="center"><input type="checkbox" v-model="form.acknowledge_flag" true-value="1" false-value="0"></td>
										<!-- <td align="center"><input type="checkbox" v-model="form.accepted_flag" true-value="1" false-value="0"></td>
										<td align="center"><input type="checkbox" v-model="form.rejected_flag" true-value="1" false-value="0"></td> -->
									</tr>
								</table>
							</div>
						</div>
						<div class="pt-2 mb-2 flex justify-end">
							<button @click="onEdit(form.id)" class="btn btn-sm btn-info btn-rounded w-32">Update</button>
						</div>
					</div>
				</div>
			</div>
		</div>
    </navigation>
</template>
