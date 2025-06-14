<script setup>
import navigation from '@/layouts/navigation.vue';
import { PencilSquareIcon, EyeIcon, TrashIcon, PlusIcon, MagnifyingGlassIcon, Bars3Icon, ChevronRightIcon, ArrowUturnLeftIcon, BarsArrowUpIcon, ClipboardDocumentIcon } from '@heroicons/vue/24/solid'
import {onMounted, ref} from "vue";
import { useRouter } from "vue-router";

const router = useRouter()
let searchRequest=ref([]);
let requests=ref([]);

		onMounted(async () => {
			getRequest()
		})

		const getRequest = async (page = 1) => {
			const response = await fetch(`/api/get_all_request?page=${page}&filter=${searchRequest.value}`);
			requests.value = await response.json();
		}

		const showTransaction = (id) => {
		router.push('/request/show/'+id)
		}

		const UpdateTransaction = (id, prno) => {
			if(prno == 'WH STOCKS'){
				router.push('/request/new_second_wh/'+id)
			}else{
				router.push('/request/new_second/'+id)
			}
			
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
							<div>
								<h6 class="m-0 pt-1 font-bold uppercase">Request</h6>
							</div>
						</div>	
						<div class="pt-1">	
							<nav aria-label="breadcrumb" role="navigation">
								<ol class="breadcrumb adminx-page-breadcrumb">
									<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
									<li class="breadcrumb-item active" aria-current="page">Request</li>
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
											<input type="text" class="form-control border-0" id="search" placeholder="Type to search..." @keyup="getRequest()" v-model="searchRequest">
										</div>
									</div>
									<a href="/request/new" class="btn btn-sm btn-primary btn-rounded">
										<div class="flex justify-between space-x-2" >
											<PlusIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"></PlusIcon>
											<span>Add New</span>
										</div>
									</a>
								</div>
								<table class="table table-actions table-hover mb-0">
									<thead>
										<tr>
											<th scope="col" width="5%" class="text-center">#</th>
											<th scope="col" width="10%">Date</th>
											<th scope="col" width="15%">MReqF No.</th>
											<th scope="col" width="12%">PR/JO No.</th>
											<th scope="col" width="18%">Department</th>
											<th scope="col" width="25%">Purpose</th>
											<th scope="col" width="15%">End Use</th>
											<th scope="col" width="5%" class="text-center">Status</th>
											<th scope="col" width="1%">
												<div class="flex justify-center">
													<Bars3Icon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"></Bars3Icon>
												</div>
											</th>
										</tr>
									</thead>
									<tbody v-for="(req, r) in requests.data">
										<tr>
											<td align="center">{{ r + 1 }}</td>
											<td>{{ req.request_date }}</td>
											<td>{{ req.mreqf_no }}</td>
											<td>{{ req.pr_no }}</td>
											<td>{{ req.department_name }}</td>
											<td>{{ req.purpose_name }}</td>
											<td>{{ req.enduse_name }}</td>
											<td align="center">
											<span class="badge badge-pill badge-success" v-if="req.close === 0"> Open</span>
											<span class="badge badge-pill badge-danger" v-else>Close</span>
											</td>
											<td class="space-x-1">
												<a v-if="req.saved == 1" @click="showTransaction(req.id)" class="text-white btn btn-xs bg-yellow-500 btn-rounded">
                                                    <EyeIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></EyeIcon>
                                                </a>
												<a v-if="req.saved == 0" @click="UpdateTransaction(req.id, req.pr_no)" class="text-white btn btn-xs bg-blue-500 btn-rounded">
                                                    <PencilSquareIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></PencilSquareIcon>
                                                </a>
											</td>
										</tr>
									</tbody>
								</table>
								<div class="flex justify-end p-2 border-t">
									<nav aria-label="Page navigation example">
										<TailwindPagination
											:data="requests"
											:limit="1"
											@pagination-change-page="getRequest"
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
