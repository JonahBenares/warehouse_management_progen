<script setup>
	import navigation from '@/layouts/navigation.vue';
	import { PencilSquareIcon, TrashIcon, PlusIcon, MagnifyingGlassIcon, ChevronLeftIcon, ChevronRightIcon, ArrowUturnLeftIcon } from '@heroicons/vue/24/solid'
	import { onMounted, ref } from "vue"
	import { useRouter } from "vue-router";

    const router = useRouter()
	let suppliers=ref([]);
	let searchSupplier=ref([]);

	onMounted(async () => {
        getSupplier()
    })

    const getSupplier = async (page = 1) => {
		const response = await fetch(`/api/get_all_supplier?page=${page}&filter=${searchSupplier.value}`);
		suppliers.value = await response.json();
	}

	const search = async () => {
		let response = await fetch('/api/search_supplier?filter='+searchSupplier.value);
		suppliers.value = await response.json();
	}

	const onEdit = (id) =>{
		router.push('/supplier/edit/'+id)
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
							<a onclick="history.back()" class="btn btn-secondary btn-xs btn-rounded text-white">
								<ArrowUturnLeftIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></ArrowUturnLeftIcon>
							</a>
						</div>
						<div>
							<h6 class="m-0 pt-1 font-bold uppercase">Supplier</h6>
						</div>
					</div>	
					<div class="pt-1">	
						<nav aria-label="breadcrumb" role="navigation">
							<ol class="breadcrumb adminx-page-breadcrumb">
								<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
								<li class="breadcrumb-item active" aria-current="page">Supplier</li>
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
										<input type="text" class="form-control border-0" id="search" placeholder="Type to search..."  @keyup="getSupplier()" v-model="searchSupplier">
									</div>
								</div>
								<a href="/supplier/new" class="btn btn-sm btn-primary btn-rounded">
									<div class="flex justify-between space-x-2" >
										<PlusIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"></PlusIcon>
										<span>Add New</span>
									</div>
								</a>
							</div>
							<table class="table table-actions table-hover table-hover mb-0">
								<thead>
									<tr>
										<th scope="col" width="5%">Status</th>
										<th scope="col" width="20%">Supplier Name</th>
										<th scope="col" width="35%">Address</th>
										<th scope="col" width="15%">Email</th>
										<th scope="col" width="12%">Contact Number</th>
										<th scope="col" width="15%">Contact Person</th>
										<th class="p-0"></th>
									</tr>
								</thead>
								<tbody>
									<tr v-for="sup in suppliers.data">
										<td align="center">
											<span class="badge badge-pill badge-success" v-if="sup.is_active === 1">Active</span>
											<span class="badge badge-pill badge-danger" v-else>Inactive</span>
										</td>
										<td>{{ sup.supplier_name }}</td>
										<td>{{ sup.address }}</td>
										<td>{{ sup.email }}</td>
										<td>{{ sup.contact_number }}</td>
										<td>{{ sup.contact_person }}</td>
										<td class="p-0 w-0">
											<div class="flex justify-end right-10">
												<span class="menu bg-white absolute">
													<span class="submenu flex justify-center space-x-1">
														<button @click="onEdit(sup.id)" class="btn btn-xs btn-info btn-rounded  text-white">
															<PencilSquareIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></PencilSquareIcon>
														</button>
													</span>
												</span>
											</div>
										</td>
										<!-- <td class="space-x-1">
											
										</td> -->
									</tr>
									
								</tbody>
							</table>
							<div class="flex justify-end p-2 border-t">
									<nav aria-label="Page navigation example">
										<TailwindPagination
											:data="suppliers"
											:limit="1"
											@pagination-change-page="getSupplier"
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
