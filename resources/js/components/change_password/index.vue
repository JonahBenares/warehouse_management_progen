<script setup>
import navigation from '@/layouts/navigation.vue';
import { PencilSquareIcon, TrashIcon, PlusIcon, MagnifyingGlassIcon, ChevronLeftIcon, ChevronRightIcon, ArrowUturnLeftIcon } from '@heroicons/vue/24/solid'
import {onMounted, ref} from "vue";
let form=ref([]);
let error = ref('')
let success = ref('')
let credentials=ref([]);
let errorMessage=ref('');
let errorMessageLength=ref('');
onMounted(async () => {
	getCredentials()
	credentialForm()
})
const getCredentials = async () => {
	const response = await fetch(`/api/change_password`);
    credentials.value = await response.json();
}

const credentialForm = async () => {
	let response = await axios.get("/api/create_credential");
	form.value = response.data;
}

const checkLengthPass = () => {
	var newpassword = document.getElementById('newpassword');
	const button = document.getElementById("btnSave");
	if(newpassword.value.length < 6){
		errorMessageLength.value = 'You have to enter at least 6 digit!';
		button.disabled = true;
	}else{
		errorMessageLength.value = ''
		button.disabled = false;
	}
}

const validateForm = () => {
	const button = document.getElementById("btnSave");
	if (form.value.new_password !== form.value.confirm_password) {
		errorMessage.value = 'Passwords do not match';
		button.disabled = true;
		return false;
	}
	errorMessage.value = '';
	button.disabled = false;
	return true;
}

const onSave = () => {
	const formData=new FormData()
	formData.append('password',form.value.new_password)
	formData.append('user_id',credentials.value.id)
	axios.post("/api/edit_password",formData).then(function (response) {
		error.value=''
		success.value='Successfully saved!'
		form = ref([])
		//console.log(response);
	}).catch(function(err){
		success.value=''
		error.value=''
		if (err.response.data.errors.password) {
			error.value=err.response.data.errors.password[0]
		}
	});
}
</script>

<template>
    <navigation>
        <div class="container-fluid">
			<!-- BreadCrumb -->
			<div class="card mb-3">	
				<div class="flex justify-between content-center">
					<div class="flex justify-start space-x-3 ">
						<div class="">
							<a href="/dashboard" class="btn btn-secondary btn-xs btn-rounded">
								<ArrowUturnLeftIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"></ArrowUturnLeftIcon>
							</a>
						</div>
						<!-- <div class="border-r"></div>	 -->
						<div>
							<h5 class="m-0 ">{{credentials.name}}</h5>
						</div>
					</div>	
					<div class="pt-1">	
						<nav aria-label="breadcrumb" role="navigation">
							<ol class="breadcrumb adminx-page-breadcrumb">
								<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
								<li class="breadcrumb-item active" aria-current="page">Change Password</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>	
			<div class="row">
				<div class="col-md-12 col-lg-12 ">
					<div class="card">
						<div class="mt-2 mb-2 border-b">
							<h6>Change Password</h6>	
						</div>
						<span>
							<p class="text-success" v-if="success">{{ success }}</p>
							<p class="text-danger" v-if="error">{{ error }}</p>
						</span>
						<div class="row">
							<div class="col-lg-6">
								<div class="form-group">
									<label class="form-label">New Password</label>
									<input type="password" id="newpassword" class="form-control border" placeholder="New Password" v-model="form.new_password" @keyup="checkLengthPass()">
									<p v-if="errorMessageLength" style="color:red">{{ errorMessageLength }}</p>
								</div>		
								<div class="form-group">
									<label class="form-label">Retype New Password</label>
									<input type="password" class="form-control border" placeholder="Retype New Password" @keyup="validateForm()" v-model="form.confirm_password">
									<p v-if="errorMessage" style="color:red">{{ errorMessage }}</p>
								</div>									
							</div>
							<div class="col-lg-6">
																	
							</div>
						</div>
						<div class="pt-2 mb-2">
							<input type="hidden" class="form-control border" v-model="credentials.id">
							<button @click="onSave()" id="btnSave" class="btn btn-sm btn-primary btn-rounded w-32">Submit</button>
						</div>
					</div>
				</div>

			</div>


		</div>
    </navigation>
</template>
