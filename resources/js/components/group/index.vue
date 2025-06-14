<script setup>
	import { onMounted, ref } from "vue"
	import { useRouter } from "vue-router" 
	import navigation from '@/layouts/navigation.vue';
	import { PencilSquareIcon, TrashIcon, PlusIcon, MagnifyingGlassIcon, ChevronLeftIcon, ChevronRightIcon, ArrowUturnLeftIcon, Bars3Icon } from '@heroicons/vue/24/solid'

	const router = useRouter() 
	let groups = ref([])
	let searchGroup=ref([]);

	onMounted(async () => {
		getGroup()
	})

	const props = defineProps({
		id:{
			type:String,
			default:''
		}
	})

	const getGroup = async (page = 1) => {
		const response = await fetch(`/api/get_all_group?page=${page}&filter=${searchGroup.value}`);
		groups.value = await response.json();
	}

	const search = async () => {
		let response = await fetch('/api/search_group?filter='+searchGroup.value);
		groups.value = await response.json();
	}

	const onEdit = (id) =>{
		router.push('/group/edit/'+id)
	}
</script>

<template>
    <navigation>
		<div class="container-fluid">
			<div class="card mb-3">	
				<div class="flex justify-between content-center">
					<div class="flex justify-start space-x-3 ">
						<div class="">
							<a onclick="history.back()" class="btn btn-secondary btn-xs btn-rounded text-white">
								<ArrowUturnLeftIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></ArrowUturnLeftIcon>
							</a>
						</div>
						<div>
							<h6 class="m-0 pt-1 font-bold uppercase">Group</h6>
						</div>
					</div>	
					<div class="pt-1">	
						<nav aria-label="breadcrumb" role="navigation">
							<ol class="breadcrumb adminx-page-breadcrumb">
								<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
								<li class="breadcrumb-item active" aria-current="page">Group</li>
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
										<input type="text" class="form-control border-0" id="search" placeholder="Type to search..." @keyup="getGroup()" v-model="searchGroup">
									</div>
								</div>
								<a href="/group/new" class="btn btn-sm btn-primary btn-rounded">
									<div class="flex justify-between space-x-2" >
										<PlusIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"></PlusIcon>
										<span>Add New</span>
									</div>
								</a>
							</div>
							<p class="text-danger" v-if="error">{{ error }}</p>
							<table class="table table-bordsered table-hover mb-0">
								<thead>
									<tr>
										<th scope="col">Group</th>
										<th class="p-1" width="1" align="center">
											<Bars3Icon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"></Bars3Icon>
										</th>
									</tr>
								</thead>
								<tbody>
									
									<tr v-for="item in groups.data" :key="item.id">
										<td>{{ item.group_name }}</td>
										<td class="p-1">
											<button @click="onEdit(item.id)" class="btn btn-xs btn-info btn-rounded text-white">
												<PencilSquareIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></PencilSquareIcon>
											</button>
										</td>
									</tr>
									
								</tbody>
							</table>
							<div class="flex justify-end p-2 border-t">
								<nav aria-label="Page navigation example">
									<TailwindPagination
										:data="groups"
										:limit="1"
										@pagination-change-page="getGroup"
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
