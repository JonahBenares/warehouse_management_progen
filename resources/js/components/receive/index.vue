<script setup>
import{ onMounted, ref } from "vue"
	import navigation from '@/layouts/navigation.vue';
	import { EyeIcon, TrashIcon, PlusIcon, MagnifyingGlassIcon, Bars3Icon, ChevronRightIcon, ArrowUturnLeftIcon, BarsArrowUpIcon, ClipboardDocumentIcon } from '@heroicons/vue/24/solid'
	import { useRouter } from "vue-router"
    const router = useRouter()

	let head = ref([])
	let searchRecieve=ref([]);
	onMounted(async () =>{
        getAllReceive()
    })

	const getAllReceive = async (page = 1) => {
		const response = await fetch(`/api/get_all_receive?page=${page}&filter=${searchRecieve.value}`);
		head.value = await response.json();

		//  let response = await axios.get(`/api/get_all_receive`)
		//  head.value = response.data
	
		
	}

	const showTransaction = (id) => {
		router.push('/receive/new_third/'+id)
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
						<!-- <div class="border-r"></div>	 -->
						<div>
							<h6 class="m-0 pt-1 font-bold uppercase">Receive</h6>
						</div>
					</div>	
					<div class="pt-1">	
						<nav aria-label="breadcrumb" role="navigation">
							<ol class="breadcrumb adminx-page-breadcrumb">
								<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
								<li class="breadcrumb-item active" aria-current="page">Receive</li>
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
										<input type="text" class="form-control border-0" id="search" placeholder="Type to search..." @keyup="getAllReceive()" v-model="searchRecieve">
									</div>
								</div>
								<div class="flex justify-between space-x-1">
									<a href="/receive_draft" class="btn btn-sm bg-orange-400 text-white hover:bg-orange-500 btn-rounded">
										<div class="flex justify-between space-x-1" >
											<ClipboardDocumentIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"></ClipboardDocumentIcon>
											<span>Drafts</span>
										</div>
									</a>
									<!-- <a href="export" class="btn btn-sm btn-success btn-rounded">
										<div class="flex justify-between space-x-1" >
											<BarsArrowUpIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"></BarsArrowUpIcon>
											<span>Export</span>
										</div>
									</a> -->
									<a href="/receive/new" class="btn btn-sm btn-primary btn-rounded">
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
										<th scope="col" width="20%">MrecF No.</th>
										<th scope="col" width="14%">Receive Date</th>
										<th scope="col" width="12%">DR No.</th>
										<th scope="col" width="12%">PO No.</th>
										<th scope="col" width="12%">SI No.</th>
										<th scope="col" width="12%">Waybill No.</th>
										<th scope="col" width="20%">PR No/s</th>
										<th scope="col" width="5%" class="text-center">Status</th>
										<th scope="col" width="1%" align="center" class="pr-2">
											<div class="flex justify-center">
												<Bars3Icon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"></Bars3Icon>
											</div>
										</th>
									</tr>
								</thead>
								<tbody>
									<tr v-for="h in head.data">
										<td>{{ h.mrecf_no }}</td>
										<td>{{ h.receive_date }}</td>
										<td>{{ h.dr_no }}</td>
										<td>{{ h.po_no }}</td>
										<td>{{ h.si_or }}</td>
										<td>{{ h.waybill_no }}</td>
										<td>
											<span class="text-white btn btn-xs py-0 !text-xs bg-gray-400 rounded mr-1" v-for="pr in h.receive_details ">{{ pr.pr_no + ' ' }}</span>
										</td>
										<td align="center">
											<span class="badge badge-pill badge-success" v-if="h.closed === 0"> Open</span>
											<span class="badge badge-pill badge-danger" v-else>Close</span>
										</td>
										<td class="pl-2 pr-2 font-bold">
                                            <div class="space-x-1 flex justify-center">
                                                <a @click="showTransaction(h.id)" class="text-white btn btn-xs bg-yellow-500 btn-rounded">
                                                    <EyeIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></EyeIcon>
                                                </a>
                                            </div>
										</td>
									</tr>
									
								</tbody>
							</table>
							<div class="flex justify-end p-2 border-t">
								<nav aria-label="Page navigation example">
									<TailwindPagination
										:data="head"
										:limit="1"
										@pagination-change-page="getAllReceive"
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
