<script setup>
	import navigation from '@/layouts/navigation.vue';
	import { CheckCircleIcon, ExclamationCircleIcon, SparklesIcon, PlusIcon, XMarkIcon, MagnifyingGlassIcon, InboxArrowDownIcon, EyeIcon, Bars3Icon} from '@heroicons/vue/24/solid'
	import {onMounted, ref} from "vue";
	import { useRouter } from "vue-router";
	const router = useRouter()
	let credentials=ref([])
	let bo_list=ref([])
	let reminder_list=ref([])
	let searchReminder=ref([])
	let searchAcceptance=ref([])
	let searchBackorder=ref([])
	let searchItems=ref([])
	let listemployees = ref([])
	let form = ref([])
	let reminder_date = ref('');
	let title = ref('');
	let notes = ref('');
	let person_to_notify = ref('');
	let error = ref([]);
	let success = ref('');
	let update = ref('');
	let acceptance = ref([]);
	let moqitems = ref([]);

	onMounted(async () => {
		getDashboard()
		getBackorder()
		getReminder()
		getUsers()
		getAllUseracceptance()
		getAllItems()
	})


	const getDashboard = async () => {
		const response = await fetch(`/api/dashboard`);
		credentials.value = await response.json();
	}

	const getBackorder = async (page = 1) => {
		const response = await axios.get(`/api/backorder_list?page=${page}&filter=${searchBackorder.value}`);
		bo_list.value = response.data
		//console.log(response.data)
		
	}

	const getReminder = (page = 1) => {
		axios.get(`/api/reminder_list?page=${page}&filter=${searchReminder.value}`).then(function (response) {
		reminder_list.value = response.data.head
		//console.log(response.data)
		});
		
	}

	const showTransaction = (id) => {
		router.push('/back_order/new/'+id)
	}

	// const doneReminder = (id) => {
	// 	if(confirm("Are you sure you want to done this Reminder?")){
	// 		UpdateDone(id)		
	// 	}
	// }

	const doneReminder = (id) => {
		if(confirm("Are you sure you want to done this Reminder?")){
			axios.post('/api/done_reminder/'+id).then(function (response) {
						update.value='Successfully done!'
						getReminder();
						closeAddModal();
					// }).catch(function(err){
					// 	document.getElementById("success").style.display="none"
					// 	window.location.reload();
					});
		}
	}

	const getUsers = async () => {
	let response = await axios.get("/api/employee_list");
	listemployees.value=response.data.users
	}

	// const addReminder = () => {
	// 	if(confirm("Are you sure you want to save this Reminder?")){
	// 		saveTransaction()		
	// 	}
	// }

	const addReminder = () => {
		if(confirm("Are you sure you want to save this Reminder?")){
			const formData=new FormData()
			formData.append('reminder_date', reminder_date.value) 
			formData.append('title', title.value)
			formData.append('notes', notes.value)
			formData.append('person_to_notify_id', person_to_notify.value ?? 0)
			axios.post(`/api/add_reminder/`, formData).then(function (response) {
					error.value=[]
					success.value='Successfully saved!'
					getReminder();
					closeAddModal();
				}, function (err) {
					success.value=''
					error.value=[]
					document.getElementById("error").style.display="block"
					if (err.response.data.errors.reminder_date) {
						error.value.push(err.response.data.errors.reminder_date[0])
					}
					if (err.response.data.errors.title) {
						error.value.push(err.response.data.errors.title[0])
					}
					if (err.response.data.errors.person_to_notify_id) {
						error.value.push(err.response.data.errors.person_to_notify_id[0])
					}
					setTimeout(() => {
						document.getElementById("error").style.display="none"
					}, 4000);
				}
			);
		}
	}

	const EditReminder = (id) => {
		const formData=new FormData()
		formData.append('reminder_date',form.value.reminder_date)
		formData.append('title',form.value.title)
		formData.append('notes',form.value.notes)
		formData.append('person_to_notify_id',form.value.person_to_notify_id)
		axios.post(`/api/update_reminder/`+id, formData).then(function () {
			error.value=[]
				update.value='You have successfully updated the reminder!'
				getReminder();
				closeEditModal();
			}, function (err) {
				update.value=''
				error.value=[]
				document.getElementById("error").style.display="block"
				if (err.response.data.errors.reminder_date) {
					error.value.push(err.response.data.errors.reminder_date[0])
				}
				if (err.response.data.errors.title) {
					error.value.push(err.response.data.errors.title[0])
				}
				if (err.response.data.errors.person_to_notify_id) {
					error.value.push(err.response.data.errors.person_to_notify_id[0])
				}
				setTimeout(() => {
					document.getElementById("error").style.display="none"
				}, 4000);
			}
		);
	}

	const showAddModal = ref(false)
	const showEditModal = ref(false)
	const hideModal = ref(true)
	const hideEditModal = ref(true)
	
	const openAddModal = () => {
		showAddModal.value = !showAddModal.value
	}
	const openEditModal = async (id) => {
		showEditModal.value = !showEditModal.value
		let response = await axios.get('/api/edit_reminder/'+id)
       	form.value = response.data.reminder_update
	}
	const closeAddModal = () => {
		showAddModal.value = !hideModal.value
	}
	const closeEditModal = () => {
		showEditModal.value = !hideEditModal.value
	}
	//const remNote = ref(false);

	const OpenNotes= (index) => {
		var shownotes = document.getElementById("notes_disp_"+index);
		var displaySetting = shownotes.style.display;
		if (displaySetting == 'block') {
			shownotes.style.display = 'none';
		}else {
			shownotes.style.display = 'block';
		}
	}

	const getAllUseracceptance = async (page = 1) => {
		let response = await axios.get(`/api/get_all_useracceptance_dashboard?page=${page}&filter=${searchAcceptance.value}`);
		acceptance.value=response.data.receivearray
	}

	const showTransactionUseraccept = (id) => {
		router.push('/user_acceptance/view/'+id)
	}

	const showTransactionUseracceptBack = (id) => {
		router.push('/user_acceptance/backorder_view/'+id)
	}

	const getAllItems = async (page = 1) => {
		let response = await axios.get(`/api/moq_display_dashboard?page=${page}&filter=${searchItems.value}`);
		moqitems.value=response.data.items
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
						<!-- <div class="border-r"></div>	 -->
						<div>
							<h6 class="m-0 pt-1 font-bold uppercase">Dashboard</h6>
						</div>
					</div>	
					<div class="pt-1">	
						<nav aria-label="breadcrumb" role="navigation">
							<ol class="breadcrumb adminx-page-breadcrumb">
								<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
								<li class="breadcrumb-item active" aria-current="page">Dashboard</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>
		


			<div class="row">
				<div class="col-lg-8 pr-2">
					
					<div class="card p-0 mb-3">
						<div class="card-header border-0 !bg-blue-500 rounded-t-lg ">
							<div class="flex justify-between">
								<span class="text-white text-lg">User Acceptance (Pending)</span>
								<a href="/user_acceptance/pending" class="btn btn-sm text-white p-0 mt-1">
									<EyeIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"></EyeIcon>
								</a>
							</div>
						</div>
						<div class="px-3 py-2 border-b ">
							<div class="flex justify-between space-x-2">
								<span class="my-1 text-gray-500">
									<MagnifyingGlassIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"></MagnifyingGlassIcon>
								</span>
								<input type="text" class="form-control !bg-blue-50" placeholder="Search" id="search" @keyup="getAllUseracceptance()" v-model="searchAcceptance">
							</div>
						</div>
						<div class="card-body p-0" id="card1">
							<div class="h-48 overflow-y-hidden hover:!overflow-y-scroll   px-3 pt-3">
								<div class="mr-2">
									<table class="table table-bordereds">
										<tr class="text-xs uppercase font-bold bg-gray-100">
											<td scope="col" width="15%">From</td>
											<td scope="col" width="15%">MrecF No.</td>
											<td scope="col" width="14%">Receive Date</td>
											<td scope="col" width="12%">DR No.</td>
											<td scope="col" width="12%">PO No.</td>
											<td scope="col" width="12%">SI No.</td>
											<td scope="col" width="12%">Waybill No.</td>
											<td scope="col" width="5%" class="text-center">Pending Items</td>
											<td scope="col" width="20%">PR No/s</td>
											<td scope="col" width="1%" align="center" class="pr-2">
												<div class="flex justify-center">
													<Bars3Icon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"></Bars3Icon>
												</div>
											</td>
										</tr>
										<tbody>
											<tr v-for="a in acceptance">
												<td>{{ a.from }}</td>
												<td>{{ a.mrecf_no }}</td>
												<td>{{ a.receive_date }}</td>
												<td>{{ a.dr_no }}</td>
												<td>{{ a.po_no }}</td>
												<td>{{ a.si_no }}</td>
												<td>{{ a.waybill_no }}</td>
												<td align="center">
													<span class="badge badge-pill badge-danger">{{ a.pending_items }}</span>
												</td>
												<td align="center">
													<span class="text-white btn btn-xs py-0 !text-xs bg-gray-400 rounded mr-1" v-for="pr in a.receive_details ">{{ pr.pr_no + ' ' }}</span>
												</td>
												<td class="pl-2 pr-2 font-bold">
													<div class="space-x-1 flex justify-center">
														<a @click="showTransactionUseraccept(a.id)" class="text-white btn btn-xs bg-yellow-500 btn-rounded" v-if="a.from=='Receive'">
															<EyeIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></EyeIcon>
														</a>
														<a @click="showTransactionUseracceptBack(a.id)" class="text-white btn btn-xs bg-yellow-500 btn-rounded" v-else-if="a.from=='Backorder'">
															<EyeIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></EyeIcon>
														</a>
													</div>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
					<div class="card p-0 mb-3">
						<div class="card-header border-0 !bg-blue-500 rounded-t-lg ">
							<div class="flex justify-between">
								<span class="text-white text-lg">Back Order</span>
								<a href="/back_order" class="btn btn-sm text-white p-0 mt-1">
									<EyeIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"></EyeIcon>
								</a>
							</div>
						</div>
						<div class="px-3 py-2 border-b ">
							<div class="flex justify-between space-x-2">
								<span class="my-1 text-gray-500">
									<MagnifyingGlassIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"></MagnifyingGlassIcon>
								</span>
								<input type="text" class="form-control !bg-blue-50" placeholder="Search" id="search" @keyup="getBackorder()" v-model="searchBackorder">
							</div>
						</div>
						<div class="card-body p-0" id="card1">
							<div class="h-48 overflow-y-hidden hover:!overflow-y-scroll  px-3 pt-3">
								<div class="mr-2">
									<table class="table table-bordereds">
										<tr class="text-xs uppercase font-bold bg-gray-100">
											<td width="23%">PO No.</td>
											<td width="23%">PR No.</td>
											<td>Item</td>
											<td class="text-center" width="10%">Exp Qty</td>
											<td class="text-center" width="10%">Rcv'd Qty</td>
											<td class="text-center" width="10%">BO Qty</td>
											<td class="text-center" width="1%">
												<span>
													<Bars3Icon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"></Bars3Icon>
												</span>
											</td>
										</tr>										
										<tr v-for="bl in bo_list">
											<td>{{ bl.po_no }}</td>
											<td>{{ bl.pr_no }}</td>
											<td>{{ bl.item_desc }}</td>
											<td class="text-center">{{ bl.exp_qty }}</td>
											<td class="text-center">{{ bl.rec_qty }}</td>
											<td class="text-center">{{ bl.bo_qty }}</td>
											<td>
												<a @click="showTransaction(bl.headid)" class="btn btn-success text-white btn-xs" title="Receive">
													<InboxArrowDownIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3" />
												</a>
											</td>
										</tr>
									</table>
								</div>
							</div>
						</div>
					</div>
					<div class="card p-0">
						<div class="card-header border-0 !bg-blue-500 rounded-t-lg ">
							<div class="flex justify-between">
								<span class="text-white text-lg">Minimum Order Quantity</span>
								<a href="/item_list" class="btn btn-sm text-white p-0 mt-1">
									<EyeIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"></EyeIcon>
								</a>
							</div>
						</div>
						<div class="px-3 py-2 border-b ">
							<div class="flex justify-between space-x-2">
								<span class="my-1 text-gray-500">
									<MagnifyingGlassIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"></MagnifyingGlassIcon>
								</span>
								<input type="text" class="form-control !bg-blue-50" placeholder="Search" id="search" @keyup="getAllItems()" v-model="searchItems">
							</div>
						</div>
						<div class="card-body p-0" id="card1">
							<div class="h-48 overflow-y-hidden hover:!overflow-y-scroll  px-3 pt-3">
								<div class="mr-2">
									<table class="table table-bordereds">
										<tr class="text-xs uppercase font-bold bg-gray-100">
											<td>Item</td>
											<td class="text-center" width="10%">MOQ</td>
											<td class="text-center" width="10%">Cur. Inv.</td>
										</tr>										
										<tr v-for="m in moqitems">
											<td :class="(m.running_balance==0) ? 'text-xs align-top px-1 !border-2 !border-red-400 font-bold py-1 bg-red-300' : ''">{{ m.item_desc }}</td>
											<td :class="(m.running_balance==0) ? 'text-xs align-top px-1 !border-2 !border-red-400 font-bold py-1 bg-red-300 text-center' : 'text-center'">{{ m.moq }}</td>
											<td :class="(m.running_balance==0) ? 'text-xs align-top px-1 !border-2 !border-red-400 font-bold py-1 bg-red-300 text-center' : 'text-center'">{{ m.running_balance }}</td>
										</tr>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-4 pl-2">
					<div class="card p-0">
						<div class="card-header border-0 !bg-blue-500 rounded-t-lg ">
							<div class="flex justify-between">
								<span class="text-white text-lg">Reminder</span>
								<button class="btn btn-sm text-white p-0" @click="openAddModal()">
									<PlusIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"></PlusIcon>
								</button>
							</div>
						</div>
						<div class="px-3 py-2 border-b ">
							<div class="flex justify-between space-x-2">
								<span class="my-1 text-gray-500">
									<MagnifyingGlassIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"></MagnifyingGlassIcon>
								</span>
								<input type="text" class="form-control !bg-blue-50" placeholder="Search" id="search" @keyup="getReminder()" v-model="searchReminder">
							</div>
						</div>
						<div class="card-body px-0 py-2" id="card1">
							<div class="h-96 hover:overflow-y-scroll overflow-y-hidden px-4">
								<div class="menu-rem" v-for="(rl, i) in reminder_list.data"><!--  loop here -->
									<div class="cursor-pointer" @click="OpenNotes(i)">
									<!-- <div class="cursor-pointer" @click="remNote = !remNote"> -->
										<div  class="flex justify-between">
											<span class="text-base w-15 font-bold">{{ rl.reminder_date }}</span>
											<div id="menu-rem" class="space-x-1">
												<button class="btn btn-xs btn-primary py-0 font-xxs" @click="openEditModal(rl.id)">Edit</button>
												<button class="btn btn-xs btn-success py-0 font-xxs" @click="doneReminder(rl.id)">Done</button>
											</div>
										</div>
										<p class="leading-tight text-sm m-0 pt-0 ">{{ rl.title }}</p>
									</div>
									<Transition
										enter-active-class="transition ease-out duration-250"
										enter-from-class="opacity-0 h-1/2"
										enter-to-class="opacity-100 h-full"
										leave-active-class="transition ease-in duration-100"
										leave-from-class="opacity-100 h-full"
										leave-to-class="opacity-0 h-1/2"
									>
										<!-- <div class="border-t border-dashed py-1 mt-1" v-show="remNote">
											<div class="text-sm leading-tight">Notes: {{ rl.notes }}</div>
											<div class="text-sm leading-tight">Person to Remind: {{ rl.person_to_notify_name }}</div>
										</div> -->
										<div class="border-t border-dashed py-1 mt-1" style="display: none;" :id="'notes_disp_'+i">
											<div class="text-sm leading-tight">Notes: {{ rl.notes }}</div>
											<div class="text-sm leading-tight">Person to Remind: {{ rl.person_to_notify_name }}</div>
										</div>
									</Transition>
									<hr class="my-2">
								</div>
							</div>
						</div>
						<div class="flex justify-end p-2 border-t">
							<nav aria-label="Page navigation example">
								<TailwindPagination
									:data="reminder_list"
									:limit="1"
									@pagination-change-page="getReminder"
								/>
							</nav>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="modal p-0" :class="{ show:showAddModal }">
			<div @click="closeAddModal" class="w-full h-full fixed"></div>
			<div class="modal__content w-5/12 p-4 mt-5">
				<div class="row">
					<div class="col-lg-12 flex justify-between">
						<p class="mb-0 font-bold">Add New Reminder</p>
						<a href="#" class="text-gray-600" @click="closeAddModal">
							<XMarkIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"></XMarkIcon>
						</a>
					</div>
				</div>
				<hr>
				<div class="row">
					<div class="col-lg-12">
						<div class="form-group mb-1">
							<label class="form-label mb-0">Date</label>
							<input type="date" class="form-control border" required v-model="reminder_date">
						</div>									
					</div>
					<div class="col-lg-12">
						<div class="form-group mb-1">
							<label class="form-label mb-0">Title</label>
							<input type="text" class="form-control border" required v-model="title">
						</div>									
					</div>
					<div class="col-lg-12">
						<div class="form-group mb-1">
							<label class="form-label mb-0">Notes</label>
							<textarea type="text" class="form-control border" rows="4" required v-model="notes"></textarea>
						</div>									
					</div>
					<div class="col-lg-12">
						<div class="form-group mb-1">
							<label class="form-label mb-0">Person to Notify</label>
							<select class="form-control border" id="person_to_notify" v-model="person_to_notify">
								<option :value="emp.id" v-for="emp in listemployees" :key="emp.id">{{ emp.name }}</option>
							</select>
						</div>									
					</div>
				</div>
				<hr class="border-dashed">
				<div class="pt-2 mb-2 flex justify-end">
					<button @click="addReminder()" class="btn btn-sm btn-primary btn-rounded w-32">Submit</button>
				</div>
			</div>
		</div>

		<div class="modal p-0" :class="{ show:showEditModal }">
			<div @click="closeEditModal" class="w-full h-full fixed"></div>
			<div class="modal__content w-5/12 p-4 mt-5">
				<div class="row">
					<div class="col-lg-12 flex justify-between">
						<p class="mb-0 font-bold">Edit Reminder</p>
						<a href="#" class="text-gray-600" @click="closeEditModal">
							<XMarkIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"></XMarkIcon>
						</a>
					</div>
				</div>
				<hr>
				<div class="row">
					<div class="col-lg-12">
						<div class="form-group mb-1">
							<label class="form-label mb-0">Date</label>
							<input type="date" class="form-control border" v-model="form.reminder_date">
						</div>									
					</div>
					<div class="col-lg-12">
						<div class="form-group mb-1">
							<label class="form-label mb-0">Title</label>
							<input type="text" class="form-control border" v-model="form.title">
						</div>									
					</div>
					<div class="col-lg-12">
						<div class="form-group mb-1">
							<label class="form-label mb-0">Notes</label>
							<textarea type="text" class="form-control border" rows="4" v-model="form.notes"></textarea>
						</div>									
					</div>
					<div class="col-lg-12">
						<div class="form-group mb-1">
							<label class="form-label mb-0">Person to Notify</label>
							<select class="form-control border" id="person_to_notify" v-model="form.person_to_notify_id">
								<option  :value="emp.id" v-for="emp in listemployees" :key="emp.id">{{ emp.name }}</option>
							</select>
						</div>									
					</div>
				</div>
				<hr class="border-dashed">
				<div class="pt-2 mb-2 flex justify-end">
					<button class="btn btn-sm btn-primary btn-rounded w-32" @click="EditReminder(form.id)">Update</button>
				</div>
			</div>
		</div>
    </navigation>
</template>
