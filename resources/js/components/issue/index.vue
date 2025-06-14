<script setup>
import navigation from '@/layouts/navigation.vue';
import { EyeIcon, Bars3Icon, PlusIcon, MagnifyingGlassIcon, ArrowUturnLeftIcon } from '@heroicons/vue/24/solid'
import {onMounted, ref} from "vue";
import { useRouter } from "vue-router";

    const router = useRouter()
	let issuelist=ref([]);
	let searchIssue=ref([]);


	onMounted(async () => {
        getIssuance()
    })

    const getIssuance = async (page = 1) => {
		// const response = await axios.get(`/api/get_all_issuance?page=${page}&filter=${searchIssue.value}`);
		// issuelist.value = response.data.issuance
		const response = await fetch(`/api/get_all_issuance?page=${page}&filter=${searchIssue.value}`);
		issuelist.value = await response.json();
		
	}

	const showTransaction = (id) => {
		router.push('/issue/show/'+id)
	}

	// const search = async () => {
	// 	let response = await fetch('/api/search_category?filter='+searchCategory.value);
		
	// 	categories.value = await response.json();
	// }

	// const onEdit = (id) =>{
	// 	router.push('/category/edit/'+id)
	// }
	// const onEditSubcat = (id) =>{
	// 	router.push('/sub_category/edit/'+id)
	// }
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
									<ArrowUturnLeftIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></ArrowUturnLeftIcon>
								</a>
							</div>
							<div>
								<h6 class="m-0 pt-1 font-bold uppercase">Issue</h6>
							</div>
						</div>	
						<div class="pt-1">	
							<nav aria-label="breadcrumb" role="navigation">
								<ol class="breadcrumb adminx-page-breadcrumb">
									<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
									<li class="breadcrumb-item active" aria-current="page">Issue</li>
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
											<input type="text" class="form-control border-0" id="search" @keyup="getIssuance()" v-model="searchIssue" placeholder="Type to search...">
										</div>
									</div>
									<a href="/issue/new" class="btn btn-sm btn-primary btn-rounded">
										<div class="flex justify-between space-x-2" >
											<PlusIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"></PlusIcon>
											<span>Add New</span>
										</div>
									</a>
								</div>
								<table class="table table-actions table-bordersed table-hover mb-0">
									<thead>
										<tr>
											<th scope="col" width="10%">Date</th>
											<th scope="col" width="10%">Time</th>
											<th scope="col" width="20%">MIF No.</th>
											<th scope="col" width="20%">MReqF No.</th>
											<th scope="col" width="50%">Remarks</th>
											<th scope="col" width="1%" class="p-1">
												<div class="flex justify-center">
													<Bars3Icon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"></Bars3Icon>
												</div>
											</th>
										</tr>
									</thead>
									<tbody>
										<tr v-for="is in issuelist.data">
											<td>{{ is.issuance_date }}</td>
											<td>{{ is.issuance_time }}</td>
											<td>{{ is.mif_no }}</td>
											<td>{{ is.mreqf_no }}</td>
											<td>{{ is.remarks }}</td>
											<td class="p-1 ">
												<div class="flex justify-center">
													<a @click="showTransaction(is.id)" class="btn btn-xs btn-warning text-white btn-rounded">
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
											:data="issuelist"
											:limit="1"
											@pagination-change-page="getIssuance"
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
