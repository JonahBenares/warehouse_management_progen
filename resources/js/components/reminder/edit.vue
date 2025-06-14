<script setup>
	import navigation from '@/layouts/navigation.vue';
	import { CheckCircleIcon, ExclamationCircleIcon, ArrowUturnLeftIcon } from '@heroicons/vue/24/solid'
	import axios from 'axios';
	import{ onMounted, ref } from "vue"
    import { useRouter } from "vue-router"
    const router = useRouter()
	let form=ref([])
	let listemployees = ref([])
	let error = ref('')
	let success = ref('')

	const props = defineProps({
		id:{
			type:String,
			default:''
		}
	})

	onMounted(async () => {
		getEditReminder()
		getUsers()
	})

	const getEditReminder = async () => {
	let response = await axios.get(`/api/editreminder/${props.id}`);
	form.value=response.data.reminder
	}

	const getUsers = async () => {
	let response = await axios.get("/api/employee_list");
	listemployees.value=response.data.users
	}

	const onEdit = (id) => {
		if(confirm("Are you sure you want to update this Reminder?")){
			if(form.value.title!=null){
				const formData= new FormData()
				formData.append('reminder_date', form.value.reminder_date)
				formData.append('title', form.value.title)
				formData.append('person_to_notify', form.value.person_to_notify_id)
				formData.append('notes', form.value.notes)
				axios.post(`/api/updatereminder/`+id, formData).then(function () {
					error.value=[]
					success.value='You have successfully updated the reminder!'
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
				alert('Warning: Title is empty.');
			}
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
							<h6 class="m-0 pt-1 font-bold uppercase">Reminder</h6>
						</div>
					</div>	
					<div class="pt-1">	
						<nav aria-label="breadcrumb" role="navigation">
							<ol class="breadcrumb adminx-page-breadcrumb">
								<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
								<li class="breadcrumb-item"><a href="/reminder">Reminder</a></li>
								<li class="breadcrumb-item active" aria-current="page">Update Reminder</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>	

			<div class="row">
				<div class="col-md-12 col-lg-12 ">
					<div class="card">
						<div class="mt-2 mb-2 border-b">
							<h6>Update Reminder</h6>	
						</div>
						<div class="row">
							<div class="col-lg-4">
								<div class="form-group">
									<label class="form-label mb-0">Date</label>
									<input type="date" class="form-control border" v-model="form.reminder_date">
								</div>										
							</div>
                            <div class="col-lg-4">
								<div class="form-group">
									<label class="form-label mb-0">Title</label>
									<input type="text" class="form-control border" v-model="form.title">
								</div>										
							</div>
                            <div class="col-lg-4">
								<div class="form-group">
									<label class="form-label mb-0">Person to Notify</label>
									<select class="form-control border" id="person_to_notify" v-model="form.person_to_notify_id">
										<option :value="emp.id" v-for="emp in listemployees" :key="emp.id">{{ emp.name }}</option>
									</select>
								</div>										
							</div>
						</div>
                        <div class="row">
							<div class="col-lg-12">
								<div class="form-group">
									<label class="form-label mb-0">Notes</label>
									<textarea class="form-control border" v-model="form.notes"></textarea>
								</div>										
							</div>
						</div>
						<div class="pt-2 mb-2 flex justify-end">
							<button @click="onEdit(form.id)" class="btn btn-sm btn-primary btn-rounded w-32">Update</button>
						</div>
					</div>
				</div>

			</div>


		</div>
    </navigation>
</template>
