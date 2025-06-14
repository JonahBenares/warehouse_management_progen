<script setup>
import navigation from '@/layouts/navigation.vue';
import { PencilSquareIcon, EyeIcon, Bars3Icon, PlusIcon, MagnifyingGlassIcon, ArrowUturnLeftIcon } from '@heroicons/vue/24/solid'
import axios from "axios";
import {onMounted, ref} from "vue";
import { useRouter } from "vue-router";
const router = useRouter()
let restockitems=ref([]);
let searchRestock=ref([]);
onMounted(async () => {
	getRestocks()
})
const getRestocks = async (page = 1) => {
	let response = await fetch(`/api/get_all_restocks?page=${page}&filter=${searchRestock.value}`);
	restockitems.value= await response.json();
}

const showTransaction = (id) => {
	router.push('/restock/show/'+id)
}

const editRestock = (id,source_pr,destination) => {
	if(source_pr == 'WH STOCKS'){
		router.push('/restock/new_second_whs/'+id+'/'+source_pr)
	}else{
		router.push('/restock/new_second/'+id+'/'+source_pr)
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
									<ArrowUturnLeftIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></ArrowUturnLeftIcon>
								</a>
							</div>
							<div>
								<h6 class="m-0 pt-1 font-bold uppercase">Restock</h6>
							</div>
						</div>	
						<div class="pt-1">	
							<nav aria-label="breadcrumb" role="navigation">
								<ol class="breadcrumb adminx-page-breadcrumb">
									<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
									<li class="breadcrumb-item active" aria-current="page">Restock</li>
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
											<input type="text" class="form-control border-0" id="search" placeholder="Type to search..." @keyup="getRestocks()" v-model="searchRestock">
										</div>
									</div>
									<a href="/restock/new" class="btn btn-sm btn-primary btn-rounded">
										<div class="flex justify-between space-x-2" >
											<PlusIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"></PlusIcon>
											<span>Add New</span>
										</div>
									</a>
								</div>
								<table class="table table-actions table-borsdered table-hover mb-0">
									<thead>
										<tr>
											<th scope="col" width="8%">Date</th>
											<th scope="col" width="7%">Time</th>
											<th scope="col" width="15%">MRS NO.</th>
											<th scope="col" width="15%">Source PR</th>
											<th scope="col" width="15%">Destination</th>
											<th scope="col" width="40%">Purpose</th>
											<th scope="col" width="1%" class="p-1">
												<div class="flex justify-center">
													<Bars3Icon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"></Bars3Icon>
												</div>
											</th>
										</tr>
									</thead>
									<tbody>
										<tr v-for="rs in restockitems.data">
											<td>{{ rs.date }}</td>
											<td>{{ rs.time }}</td>
											<td>{{ rs.mrs_no }}</td>
											<td>{{ rs.source_pr}}</td>
											<td>{{ rs.destination }}</td>
											<td>{{ rs.purpose}}</td>
											<td class="p-1 ">
												<div class="flex justify-center" v-if="rs.saved==1">
													<a @click="showTransaction(rs.id)" class="btn btn-xs btn-warning text-white btn-rounded">
														<EyeIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></EyeIcon>
													</a>
												</div>
												<div class="flex justify-center" v-else>
													<a @click="editRestock(rs.id,rs.source_pr,rs.destination)" class="btn btn-xs btn-info text-white btn-rounded">
														<PencilSquareIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></PencilSquareIcon>
													</a>
												</div>
											</td>
										</tr>
									</tbody>
								</table>
								<div class="flex justify-end p-2 border-t">
									<nav aria-label="Page navigation example">
										<TailwindPagination
											:data="restockitems"
											:limit="1"
											@pagination-change-page="getRestocks"
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
