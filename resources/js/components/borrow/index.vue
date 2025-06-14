<script setup>
import navigation from '@/layouts/navigation.vue';
import { EyeIcon, Bars3Icon, PlusIcon, MagnifyingGlassIcon, ArrowUturnLeftIcon } from '@heroicons/vue/24/solid'
import axios from "axios";
import {onMounted, ref} from "vue";
import { useRouter } from "vue-router";
const router = useRouter()
let searchBorrow=ref([]);
let borrowed=ref([]);

		onMounted(async () => {
			getBorrow()
		})

		const getBorrow = async (page = 1) => {
			const response = await fetch(`/api/get_all_borrowed?page=${page}&filter=${searchBorrow.value}`);
			borrowed.value = await response.json();
		}

		const showTransaction = (id) => {
		router.push('/borrow/show/'+id)
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
								<h6 class="m-0 pt-1 font-bold uppercase">Borrow</h6>
							</div>
						</div>	
						<div class="pt-1">	
							<nav aria-label="breadcrumb" role="navigation">
								<ol class="breadcrumb adminx-page-breadcrumb">
									<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
									<li class="breadcrumb-item active" aria-current="page">Borrow</li>
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
											<input type="text" class="form-control border-0" id="search" placeholder="Type to search..." @keyup="getBorrow()" v-model="searchBorrow">
										</div>
									</div>
									<a href="/borrow/new" class="btn btn-sm btn-primary btn-rounded">
										<div class="flex justify-between space-x-2" >
											<PlusIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"></PlusIcon>
											<span>Add New</span>
										</div>
									</a>
								</div>
								<table class="table table-actions table-hover mb-0">
									<thead>
										<tr>
											<th scope="col"  class="text-center">#</th>
											<th scope="col" >Date</th>
											<th scope="col" >Time</th>
											<th scope="col" >MBR No.</th>
											<th scope="col" >Borrower's Name</th>
											<!-- <th scope="col" width="%">Borrowed <span class="bg-orange-100 px-1">From</span> / <span class="bg-green-100 px-1">By</span></th> -->
											<th width="1%" align="center">
												<Bars3Icon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"></Bars3Icon>
											</th>
										</tr>
									</thead>
									<tbody>
										<tr v-for="(bo, x) in borrowed.data">
											<td align="center">{{ x + 1 }}</td>
											<td>{{ bo.borrow_date }}</td>
											<td>{{ bo.borrow_time }}</td>
											<td>{{ bo.mbr_no }}</td>
											<td>{{ bo.borrowed_by_user_name }}</td>
											<!-- <td class="pb-0">
												<div class="flex justify-start space-x-1 mb-1">
													<span class="bg-orange-100 px-1">{{ bo.borrow_details.borrowed_from }}</span>
													<span class="bg-green-100 px-1">{{ bo.borrow_details.borrowed_by }}</span>
												</div>
											</td> -->
											<td class="">
												<span class="flex justify-center space-x-1">
													<a @click="showTransaction(bo.id)" class="btn btn-xs btn-warning text-white btn-rounded">
														<EyeIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></EyeIcon>
													</a>
												</span>
											</td>
										</tr>
									</tbody>
								</table>
								<div class="flex justify-end p-2 border-t">
									<nav aria-label="Page navigation example">
										<TailwindPagination
											:data="borrowed"
											:limit="1"
											@pagination-change-page="getBorrow"
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
