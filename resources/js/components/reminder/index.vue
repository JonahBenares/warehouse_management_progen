<script setup>
import navigation from '@/layouts/navigation.vue';
import { CheckCircleIcon, ExclamationCircleIcon, PencilSquareIcon, TrashIcon, PlusIcon, MagnifyingGlassIcon, Bars3Icon, ArrowUturnLeftIcon, CheckIcon } from '@heroicons/vue/24/solid'
import axios from "axios";
import {onMounted, ref} from "vue";
import { useRouter } from "vue-router";

const router = useRouter();
let reminders=ref([]);
let searchReminders=ref([]);
let error = ref([]);
let success = ref('');
let update = ref('');

onMounted(async () => {
	getReminders()
})

const props = defineProps({
        id:{
            type:String,
            default:''
        }
    })

	const getReminders = (page = 1) => {
		axios.get(`/api/get_all_reminders?page=${page}&filter=${searchReminders.value}`).then(function (response) {
			reminders.value = response.data.head
		//console.log(response.data)
		});
		
	}

	const doneReminder = (id) => {
		if(confirm("Are you sure you want to done this Reminder?")){
			axios.post('/api/done_reminder/'+id).then(function (response) {
						update.value='Successfully done!'
						getReminders(); 
					});
		}
	}

	const onEdit = (id) =>{
	router.push('/reminder/edit/'+id)
	}

	const DeleteReminder = (id) => {
		if(confirm("Are you sure you want to delete this reminder?")){
			axios.get(`/api/delete_reminder/${id}`).then(function () {
				getReminders(); 
			});
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
				<div class="hide-animate" v-if="update" id="update">
					<div class="alert alert-info alert-top my-2">
						<div class="flex justify-start space-x-1">
							<div>
								<CheckCircleIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12"></CheckCircleIcon>
							</div> 
							<div class="py-1">
								<h6 class="font-bold m-0">Updated!</h6>
								<p class="text-sm m-0 text-gray-400"> {{ update }}</p>
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
								<li class="breadcrumb-item active" aria-current="page">Reminder</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>	

			<div class="row">
				<div class="col-md-12 col-lg-12 ">
					<div class="card">
						<div class="table-responsive-md">
							<div class="flex justify-between pb-2 mt-2 mb-2">
								<div class="flex justify-between">
									<div class="input-group border rounded-xl w-80">
										<div class="input-group-prepend">
											<div class="input-group-icon pt-1 pl-1">
												<MagnifyingGlassIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"></MagnifyingGlassIcon>
											</div>
										</div>
										<input type="text" class="form-control border-0" id="search" placeholder="Type to search..."  @keyup="getReminders()" v-model="searchReminders">
									</div>
								</div>
								<div class="flex justify-between space-x-1">
									<a href="/reminder/new" class="btn btn-sm btn-primary btn-rounded">
										<div class="flex justify-between space-x-2" >
											<PlusIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"></PlusIcon>
											<span>Add New</span>
										</div>
									</a>
								</div>
							</div>
							<table class="table table-bordsered tabluiojhnbe-hover mb-0">
								<thead>
									<tr>
										<th width="10%">Date</th>
										<th width="20%">Title</th>
										<th width="40%">Notes</th>
										<th width="20%">Person to Notify</th>
										<th width="8%" align="center">
											<div class="flex justify-center">
												<Bars3Icon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"></Bars3Icon>
											</div>
										</th>
									</tr>
								</thead>
								<tbody>
									<tr v-for="r in reminders.data" :key="r.id" :class="(r.done==1) ? 'bg-yellow-50' : ''">
										<td>{{ r.reminder_date }}</td>
										<td>{{ r.title }}</td>
										<td>{{ r.notes }}</td>
										<td>{{ r.person_to_notify_name }}</td>
										<td class="p-1 flex justify-center space-x-1 w-full" v-if="r.done != 1">
                                            <button @click="doneReminder(r.id)" class="btn btn-xs btn-success btn-rounded  text-white" >
                                                <CheckIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></CheckIcon>
                                            </button>
                                            <button @click="onEdit(r.id)" class="btn btn-xs btn-primary btn-rounded  text-white">
                                                <PencilSquareIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></PencilSquareIcon>
                                            </button>
                                            <button @click="DeleteReminder(r.id)" class="btn btn-xs btn-danger btn-rounded  text-white">
                                                <TrashIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></TrashIcon>
                                            </button>
										</td>
										<td align="center" v-else >
											<span class="text-xs bg-yellow-500 py-1 px-2 rounded text-white"> DONE</span>
										</td>
									</tr>
								</tbody>
							</table>
							<div class="flex justify-end p-2 border-t">
								<nav aria-label="Page navigation example">
									<TailwindPagination
											:data="reminders"
											:limit="1"
											@pagination-change-page="getReminders"
										/>
								</nav>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
    </navigation>
</template>
