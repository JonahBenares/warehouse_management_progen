<script setup>
	import navigation from '@/layouts/navigation.vue';
	import { PencilSquareIcon, TrashIcon, PlusIcon, MagnifyingGlassIcon, Bars3Icon, ArrowUturnLeftIcon } from '@heroicons/vue/24/solid'
	import { onMounted, ref } from "vue"
	import { useRouter } from "vue-router";

    const router = useRouter()
	let categories=ref([]);
	let searchCategory=ref([]);


	onMounted(async () => {
        getCategory()
    })

    const getCategory = async (page = 1) => {
		const response = await fetch(`/api/get_all_category?page=${page}&filter=${searchCategory.value}`);
		categories.value = await response.json();
	}

	const search = async () => {
		let response = await fetch('/api/search_category?filter='+searchCategory.value);
		categories.value = await response.json();
	}

	const onEdit = (id) =>{
		router.push('/category/edit/'+id)
	}
	const onEditSubcat = (id) =>{
		router.push('/sub_category/edit/'+id)
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
							<h6 class="m-0 pt-1 font-bold uppercase">Item Category</h6>
						</div>
					</div>	
					<div class="pt-1">	
						<nav aria-label="breadcrumb" role="navigation">
							<ol class="breadcrumb adminx-page-breadcrumb">
								<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
								<li class="breadcrumb-item active" aria-current="page">Item Category</li>
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
										<input type="text" class="form-control border-0" id="search" placeholder="Type to search..." @keyup="getCategory()" v-model="searchCategory">
									</div>
								</div>
								<a href="/category/new" class="btn btn-sm btn-primary btn-rounded">
									<div class="flex justify-between space-x-2" >
										<PlusIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"></PlusIcon>
										<span>Add New</span>
									</div>
								</a>
							</div>
							<table class="table table-bordedred table-hover mb-0">
								<thead>
									<tr>
										<th scope="col" width="15%">Code</th>
										<th scope="col" width="65%">Item Category</th>
										<th scope="col" width="20%">Prefix</th>
										<th align="center" class="p-0 w-0"></th>
									</tr>
								</thead>
								<tbody v-for="cat in categories.data">
									<tr class="bg-orange-100">
										<td class="py-1 px-2 text-gray-600 font-bold uppercase">{{ cat.category_code }}</td>
										<td class="py-1 px-2 text-gray-600 font-bold capitalize">{{ cat.category_name }}</td>
										<td class="py-1 px-2 text-gray-600 font-bold">{{ cat.prefix }}</td>
										<td class="p-0 w-0">
                                            <div class="flex justify-end right-10">
												<span class="menu bg-white absolute">
													<span class="submenu flex justify-center space-x-1">
														<button @click="onEdit(cat.id)" class="btn btn-xs btn-primary btn-rounded">
															<PencilSquareIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></PencilSquareIcon>
														</button>
													</span>
												</span>
                                            </div>
										</td>
									</tr>
									<tr v-for="subcat  in cat.sub_categories">
										<td class="px-2 text-xs py-1 uppercase">{{ subcat.subcat_code }}</td>
										<td class="px-2 text-sm py-1 capitalize">{{ subcat.subcat_name }}</td>
										<td class="px-2 text-sm py-1">{{ subcat.subcat_prefix }}</td>
										<td class="p-0 w-0">
											<div class="flex justify-end right-10">
												<span class="menu bg-white absolute ">
													<span class="submenu flex justify-center space-x-1">
														<button @click="onEditSubcat(subcat.id)" class="btn btn-xs btn-primary btn-rounded">
															<PencilSquareIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></PencilSquareIcon>
														</button>
													</span>
												</span>
                                            </div>
										</td>
									</tr>
								</tbody>
							</table>
							<div class="flex justify-end p-2 border-t">
								<nav aria-label="Page navigation example">
									<TailwindPagination
										:data="categories"
										@pagination-change-page="getCategory"
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
