<script setup>
import{ onMounted, ref } from "vue"
	import navigation from '@/layouts/navigation.vue';
	import { EyeIcon, DocumentIcon, PlusIcon, MagnifyingGlassIcon, Bars3Icon, ChevronRightIcon, ArrowUturnLeftIcon, PencilSquareIcon } from '@heroicons/vue/24/solid'
	import { useRouter } from "vue-router"
    const router = useRouter()
	let searchCompletedGatepass=ref([]);
	let completedgatepass=ref([]);
	let ret_dets=ref([]);
    let head_id=ref('');
    let item_id=ref('');
	let error = ref([])
	let success = ref('')
    const viewHistoryModal = ref(false)
    const dateReturnModal = ref(false)
	const hideModal = ref(true)

	onMounted(async () => {
			getCompletedGatepass()
		})

	const getCompletedGatepass = async (page = 1) => {
		// let response = await axios.get(`/api/get_completed_gatepass?page=${page}`);
		let response = await axios.get(`/api/get_completed_gatepass`);
		completedgatepass.value=response.data
	}

	const showGatepass = (id) => {
		router.push('/gatepass/show/'+id)
	}

	const EditGatepass = (id) => {
		router.push('/gatepass/new_second/'+id)
	}

	const AddReturn = (headid,itemid) => {
        head_id.value = headid;
        item_id.value = itemid;	
		viewHistoryModal.value = !viewHistoryModal.value
	}

	const openReturnedHistory = async (headid,itemid) => {
        let response = await axios.get(`/api/returned_details/${headid}`)
        ret_dets.value=response.data
		dateReturnModal.value = !dateReturnModal.value
	}

	const closeModal = () => {
		viewHistoryModal.value = !hideModal.value
        dateReturnModal.value = !hideModal.value
	}

</script>

<template>
    <navigation>
		<div class="container-fluid">
			<!-- BreadCrumb -->
			<div class="card mb-3">	
				<div class="flex justify-between content-center">
					<div class="flex justify-start space-x-3 ">
						<div class=" ">
							<a href="/dashboard" class="btn btn-secondary btn-xs btn-rounded">
								<ArrowUturnLeftIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></ArrowUturnLeftIcon>
							</a>
						</div>
						<div>
							<h6 class="m-0 pt-1 font-bold uppercase">Material Gatepass (COMPLETED)</h6>
						</div>
					</div>	
					<div class="pt-1">	
						<nav aria-label="breadcrumb" role="navigation">
							<ol class="breadcrumb adminx-page-breadcrumb">
								<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
								<li class="breadcrumb-item active" aria-current="page">Material Gatepass (Completed)</li>
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
										<input type="text" class="form-control border-0" id="search" placeholder="Type to search..." @keyup="getCompletedGatepass()" v-model="searchCompletedGatepass">
									</div>
								</div>
								<div class="flex justify-between space-x-1">
									<a href="/gatepass/new" class="btn btn-sm btn-primary btn-rounded">
										<div class="flex justify-between space-x-1" >
											<PlusIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"></PlusIcon>
											<span>Add New</span>
										</div>
									</a>
								</div>
							</div>
							<table class="table table-actions table-bordesred table-hover mb-0">
								<thead>
									<tr>
										<th scope="col" width="20%">Date Issued</th>
										<th scope="col" width="20%">Item Description</th>
										<th scope="col" width="5%">U/M</th>
										<th scope="col" width="5%">QTY</th>
										<th scope="col" width="10%">Type</th>
										<th scope="col" width="15%">Remarks</th>
										<th scope="col" width="15%">MGP No.</th>
										<th scope="col" width="15%">Destination</th>
										<th scope="col" width="1%" align="center" class="pr-2">
											<div class="flex justify-center">
												<Bars3Icon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"></Bars3Icon>
											</div>
										</th>
									</tr>
								</thead>
								<tbody>
									<tr v-for="cgp in completedgatepass">
										<td>{{ cgp.date_issued }}</td>
										<td>{{ cgp.item_desc }}</td>
										<td>{{ cgp.uom }}</td>
										<td>{{ cgp.quantity }}</td>
										<td>{{ cgp.type }}</td>
										<td>{{ cgp.remarks }}</td>
										<td>{{ cgp.gatepass_no }}</td>
										<td>{{ cgp.destination }}</td>
										<td>
                                            <div class="flex justify-center space-x-1">
												<a @click="showGatepass(cgp.head_id)" class="text-white btn btn-xs bg-yellow-500 btn-rounded" v-if="cgp.saved == '1'">
                                                    <EyeIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></EyeIcon>
                                                </a>
												<a @click="EditGatepass(cgp.head_id)" class="text-white btn btn-xs bg-blue-500 btn-rounded" v-if="cgp.saved == '0'">
                                                    <PencilSquareIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></PencilSquareIcon>
                                                </a>
                                                <a @click="openReturnedHistory(cgp.head_id)" class="text-white btn btn-xs bg-orange-500 btn-rounded" v-if="cgp.count_hist != '0'">
                                                    <DocumentIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></DocumentIcon>
                                                </a>
                                            </div>
                                        </td>
									</tr>
								</tbody>
							</table>
							<div class="flex justify-end p-2 border-t">
								<nav aria-label="Page navigation example">
									<TailwindPagination
										:data="completedgatepass"
										:limit="1"
										@pagination-change-page="getCompletedGatepass"
									/>
								</nav>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
    </navigation>
	<Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
        <div class="modal pt-4 px-3" :class="{ show:dateReturnModal }">
            <div @click="closeModal" class="w-full h-full fixed"></div>
            <div class="modal__content w-4/12 !mt-20">
                <div class="modal_s_items">
                    <div class=" ">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="border-b mb-3">
                                    <h6 class=" font-bold uppercase">History</h6>
                                </div>
                                <div class="">
                                    <table class="w-full table-bordered">
                                        <thead>
                                            <tr>
												<td class="px-2">Item Description</td>
                                                <td class="px-2">Date Returned</td>
                                                <td class="px-2">Qty</td>
                                                <td class="px-2">Remarks</td>
                                            </tr>
                                        </thead>
                                        <tbody v-for="rd in ret_dets">
                                            <tr>
												<td><b>{{ rd.item_desc }}</b></td>
                                                <td>{{ rd.date_returned }}<br></td>
                                                <td>{{ parseFloat(rd.returned_qty).toFixed(2) }}</td>
                                                <td>{{ rd.remarks }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- <div class="pt-2 mb-2 flex justify-end">
                                    <button class="btn btn-sm btn-primary btn-rounded w-32">Submit</button>
                                </div> -->
                            </div>
                        </div>
                    </div>		
                </div> 
            </div>
        </div>
    </Transition>
</template>
