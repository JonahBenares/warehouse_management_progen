<script setup>
	import navigation from '@/layouts/navigation.vue';
	import { CheckCircleIcon, ExclamationCircleIcon, ArrowUturnLeftIcon } from '@heroicons/vue/24/solid'
	import axios from 'axios';
	import{ onMounted, ref } from "vue"
    import { useRouter } from "vue-router"
    const router = useRouter()
	let listemployees = ref([])
	let form=ref([])
	let error = ref('')
	let success = ref('')

	onMounted(async () => {
		getUsers()
		reminderform()
	})

	const reminderform = async () => {
		let response = await axios.get("/api/create_reminder");
		form.value = response.data;
	}

	const getUsers = async () => {
	let response = await axios.get("/api/employee_list");
	listemployees.value=response.data.users
	}

	const onSave = () => {
		if(confirm("Are you sure you want to save this Reminder?")){
			if(form.value.reminder_date!=null && form.value.title!=null && form.value.person_to_notify!=null){
					const formData= new FormData()
					formData.append('reminder_date', form.value.reminder_date)
					formData.append('title', form.value.title)
					formData.append('person_to_notify', form.value.person_to_notify)
					formData.append('notes', form.value.notes)
					axios.post("/api/addreminder",formData).then(function () {
						success.value='You have successfully added new reminder!'
						form.value.reminder_date=''
						form.value.title=''
						form.value.person_to_notify=''
						form.value.notes=''
						document.getElementById("success").style.display="block"
						document.getElementById("error").style.display="none"
						setTimeout(() => {
							document.getElementById("success").style.display="none"
						}, 4000);
					}, function (err) {
						error.value = err.response.data.message;
						document.getElementById("error").style.display="block"
						document.getElementById("success").style.display="none"
						setTimeout(() => {
							document.getElementById("error").style.display="none"
						}, 4000);
					});
			}else{
				alert('Warning: Some fields are empty.');
			}
		}
	}

</script>

<template>
	<div>
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
				<div class="hide-animate" v-if="error" id="error">
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
							<h6 class="m-0 pt-1 font-bold uppercase">Reminder</h6>
						</div>
					</div>	
					<div class="pt-1">	
						<nav aria-label="breadcrumb" role="navigation">
							<ol class="breadcrumb adminx-page-breadcrumb">
								<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
								<li class="breadcrumb-item"><a href="/reminder">Reminder</a></li>
								<li class="breadcrumb-item active" aria-current="page">Add New Reminder</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>	

			<div class="row">
				<div class="col-md-12 col-lg-12 ">
					<div class="card">
						<div class="mt-2 mb-2 border-b">
							<h6>Add New Reminder</h6>	
						</div>
						<div class="row">
							<div class="col-lg-4">
								<div class="form-group">
									<label class="form-label mb-0">Date</label>
									<input type="date" class="form-control border" required v-model="form.reminder_date">
								</div>										
							</div>
                            <div class="col-lg-4">
								<div class="form-group">
									<label class="form-label mb-0">Title</label>
									<input type="text" class="form-control border" required v-model="form.title">
								</div>										
							</div>
                            <div class="col-lg-4">
								<div class="form-group">
									<label class="form-label mb-0">Person to Notify</label>
									<select class="form-control border" id="person_to_notify" v-model="form.person_to_notify">
										<option :value="emp.id" v-for="emp in listemployees" :key="emp.id">{{ emp.name }}</option>
									</select>
								</div>										
							</div>
						</div>
                        <div class="row">
							<div class="col-lg-12">
								<div class="form-group">
									<label class="form-label mb-0">Notes</label>
									<textarea type="text" class="form-control border" rows="4" v-model="form.notes"></textarea>
								</div>										
							</div>
						</div>
						<div class="pt-2 mb-2 flex justify-end">
							<button @click="onSave()" class="btn btn-sm btn-primary btn-rounded w-32">Submit</button>
						</div>
					</div>
				</div>

			</div>


		</div>
    </navigation>
</template>
