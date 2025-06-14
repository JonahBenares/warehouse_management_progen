<script setup>
import{ onMounted, ref } from "vue"
	import navigation from '@/layouts/navigation.vue';
	import { EyeIcon, DocumentIcon, PlusIcon, MagnifyingGlassIcon, Bars3Icon, ArrowUturnLeftIcon, PencilSquareIcon } from '@heroicons/vue/24/solid'
	import { useRouter } from "vue-router"
    const router = useRouter()
	let searchAllGatepass=ref([]);
	let allgatepass=ref([]);
	let ret_dets=ref([]);
	let all_items = ref([])
    let head_id=ref('');
    let item_id=ref('');
    let date_return=ref('');
    let return_qty=ref('');
	let remaining_qty=ref('');
    let return_remarks=ref('');
    let error = ref([])
	let success = ref('')
    const viewHistoryModal = ref(false)
    const dateReturnModal = ref(false)
	const hideModal = ref(true)

	onMounted(async () => {
			getAllGatepass()
		})

	// const getAllGatepass = async (page = 1) => {
	// 	const response = await fetch(`/api/get_all_gatepass?page=${page}&filter=${searchAllGatepass.value}`);
	// 	allgatepass.value = await response.json();
	// }

	const getAllGatepass = async () => {
		// let response = await axios.get(`/api/get_all_gatepass?page=${page}&filter=${searchAllGatepass.value}`);
		let response = await axios.get(`/api/get_all_gatepass`)
		allgatepass.value=response.data
		// all_items.value=response.data.all_items
	}

	const showGatepass = (id) => {
		router.push('/gatepass/show/'+id)
	}

	const EditGatepass = (id) => {
		router.push('/gatepass/new_second/'+id)
	}

	const SaveReturnDetails = () => {
		if(confirm("Are you sure you want to proceed?")){
			const formData=new FormData()
			const btn = document.getElementById("SubmitButton");
			
			formData.append('head_id', head_id.value)
			formData.append('item_id', item_id.value)
			formData.append('date_return', date_return.value)
			formData.append('return_qty', return_qty.value)
			formData.append('return_remarks', return_remarks.value)

				axios.post("/api/add_return_details",formData).then(function (response) {
					// btn.disabled = false;
					error.value=[]
					success.value='Successfully saved!'
					// router.push('/gatepass/new_second/'+response.data)
                    closeModal()
                    getAllGatepass()
					item_id.value = '';
					date_return.value = '';
					return_qty.value = '';
					remaining_qty.value = '';
					return_remarks.value = '';
				});
		}
    }


	const QtyLimit = () => {
	const returnqty = return_qty.value;
	const remainingqty = remaining_qty.value;
	const btn = document.getElementById("SaveReturn");
	if(returnqty <= remainingqty){
			btn.disabled = false;
		}else{
			alert('Return quantity not equal to issued quantity');
			btn.disabled = true;
		}
	}

	const ItemDets = async () => {
        let response = await axios.get(`/api/item_details/`+item_id.value)
        return_qty.value=response.data.quantity
        remaining_qty.value=response.data.quantity
	}


	const AddReturn = async (headid) => {
		let response = await axios.get(`/api/get_returnable_items/${headid}`)
		all_items.value=response.data.all_items

        head_id.value = headid;
        // item_id.value = itemid;	
		viewHistoryModal.value = !viewHistoryModal.value
	}
    const openReturnedHistory = async (headid) => {
        let response = await axios.get(`/api/returned_details/${headid}`)
        ret_dets.value=response.data
		dateReturnModal.value = !dateReturnModal.value
	}
	const closeModal = () => {
		item_id.value = '';
		date_return.value = '';
		return_qty.value = '';
		remaining_qty.value = '';
		return_remarks.value = '';
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
							<h6 class="m-0 pt-1 font-bold uppercase">Material Gatepass (Overall)</h6>
						</div>
					</div>	
					<div class="pt-1">	
						<nav aria-label="breadcrumb" role="navigation">
							<ol class="breadcrumb adminx-page-breadcrumb">
								<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
								<li class="breadcrumb-item active" aria-current="page">Material Gatepass (Overall)</li>
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
										<input type="text" class="form-control border-0" id="search" placeholder="Type to search..." @keyup="getAllGatepass()" v-model="searchAllGatepass">
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
										<th scope="col" width="12%">Status</th>
										<th scope="col" width="1%" align="center" class="pr-2">
											<div class="flex justify-center">
												<Bars3Icon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"></Bars3Icon>
											</div>
										</th>
									</tr>
								</thead>
								<tbody>
									<tr v-for="agp in allgatepass">
										<td>{{ agp.date_issued }}</td>
										<td>{{ agp.item_desc }}</td>
										<td>{{ agp.uom }}</td>
										<td>{{ agp.quantity }}</td>
										<td>{{ agp.type }}</td>
										<td>{{ agp.remarks }}</td>
										<td>{{ agp.gatepass_no }}</td>
										<td>{{ agp.destination }}</td>
										<td>{{ agp.status }}</td>
										<input type="hidden" id="quantity_" class="form-control border py-2" v-model="agp.quantity">
										<input type="hidden" id="totalreturnedqty" class="form-control border py-2" v-model="agp.returned_qty">
										<td class="pl-3 pr-2 font-bold">
                                            <div class="space-x-1 flex justify-center">
                                                <a @click="showGatepass(agp.head_id)" class="text-white btn btn-xs bg-yellow-500 btn-rounded" v-if="agp.saved == '1'">
                                                    <EyeIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></EyeIcon>
                                                </a>
												<a @click="EditGatepass(agp.head_id)" class="text-white btn btn-xs bg-blue-500 btn-rounded" v-if="agp.saved == '0'">
                                                    <PencilSquareIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></PencilSquareIcon>
                                                </a>
												<a  @click="AddReturn(agp.head_id,agp.item_id)" class="text-white btn btn-xs bg-green-500 btn-rounded" v-if="agp.count_ret != '0' && agp.status == 'Incomplete'">
                                                    <PlusIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></PlusIcon>
                                                </a>
                                                <a @click="openReturnedHistory(agp.head_id)" class="text-white btn btn-xs bg-orange-500 btn-rounded" v-if="agp.count_hist != '0'">
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
										:data="allgatepass"
										:limit="1"
										@pagination-change-page="getAllGatepass"
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
        <div class="modal pt-4 px-3" :class="{ show:viewHistoryModal }">
            <div @click="closeModal" class="w-full h-full fixed"></div>
            <div class="modal__content w-4/12 !mt-20">
                <div class="modal_s_items">
                    <div class=" ">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="border-b mb-3">
                                    <h6 class=" font-bold uppercase">Date Returned</h6>
                                </div>
                                <div class="">
									<div class="form-group w-full">
									<label class="form-label mb-0">Item Description</label>
									<select class="form-control border w-full py-2" v-model="item_id" @change="ItemDets()">
										<option v-for="at in all_items" v-bind:key="at.id" v-bind:value="at.id">{{ at.item_description}}</option>
									</select>
									</div>
                                    <div class="form-group w-full">
                                        <label class="form-label mb-0">Date Returned</label>
                                        <input type="date" class="form-control border" v-model="date_return">
                                    </div>
                                    <div class="form-group w-full">
                                        <label class="form-label mb-0">Qty</label>
                                        <input type="text" class="form-control border py-2" v-model="return_qty" @change="QtyLimit()" min="0" onkeypress="return (event.charCode !=8 && event.charCode ==0 || (event.charCode >= 48 && event.charCode <= 57))">
										<input type="hidden" class="form-control border" v-model="remaining_qty">
									</div>
                                    <div class="form-group w-full">
                                        <label class="form-label mb-0">Remarks</label>
                                        <textarea class="form-control border py-2" v-model="return_remarks"></textarea>
                                    </div>
                                    <input type="hidden" class="form-control border py-2" v-model="head_id">
                                    <input type="hidden" class="form-control border py-2" v-model="item_id">
                                </div>
                                <div class="pt-2 mb-2 flex justify-end">
                                    <button @click="SaveReturnDetails()" id="SaveReturn" class="btn btn-sm btn-primary btn-rounded w-32">Submit</button>
                                </div>
                            </div>
                        </div>
                    </div>		
                </div> 
            </div>
        </div>
    </Transition>
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
