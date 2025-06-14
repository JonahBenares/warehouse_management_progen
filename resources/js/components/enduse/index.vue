<script setup>
import navigation from '@/layouts/navigation.vue';
import { PencilSquareIcon, Bars3Icon, PlusIcon, MagnifyingGlassIcon, ArrowUturnLeftIcon, BarsArrowUpIcon } from '@heroicons/vue/24/solid'
import axios from "axios";
import {onMounted, ref} from "vue";
import { useRouter } from "vue-router";

const router = useRouter();
let error = ref('')
let enduses=ref([]);
let searchEnduse=ref([]);

onMounted(async () => {
	getEnduse()
})

const props = defineProps({
        id:{
            type:String,
            default:''
        }
    })

const getEnduse = async (page = 1) => {
	const response = await fetch(`/api/get_all_enduse?page=${page}&filter=${searchEnduse.value}`);
    enduses.value = await response.json();
}

const search = async () => {
	let response = await fetch('/api/search_enduse?filter='+searchEnduse.value);
    enduses.value = await response.json();
}

const onEdit = (id) =>{
	router.push('/enduse/edit/'+id)
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
							<h6 class="m-0 pt-1 font-bold uppercase">End-Use</h6>
						</div>
					</div>	
					<div class="pt-1">	
						<nav aria-label="breadcrumb" role="navigation">
							<ol class="breadcrumb adminx-page-breadcrumb">
								<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
								<li class="breadcrumb-item active" aria-current="page">End-Use</li>
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
										<input type="text" class="form-control border-0" id="search" placeholder="Type to search..." @keyup="getEnduse()" v-model="searchEnduse">
									</div>
								</div>
								<div class="flex justify-between space-x-1">
									<a href="/export-enduse" class="btn btn-sm btn-success btn-rounded">
										<div class="flex justify-between space-x-2" >
											<BarsArrowUpIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"></BarsArrowUpIcon>
											<span>Export</span>
										</div>
									</a>
									<a href="/enduse/new" class="btn btn-sm btn-primary btn-rounded">
										<div class="flex justify-between space-x-2" >
											<PlusIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"></PlusIcon>
											<span>Add New</span>
										</div>
									</a>
								</div>
							</div>
							<table class="table table-actions table-hover mb-0">
								<thead>
									<tr>
										<th scope="col">End-Use</th>
										<th class="p-1" width="1" align="center">
											<Bars3Icon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"></Bars3Icon>
										</th>
									</tr>
								</thead>
								<tbody>
									<tr v-for="eu in enduses.data" :key="eu.id">
										<td>{{ eu.enduse_name }}</td>
										<td class="p-1">
											<button @click="onEdit(eu.id)" class="btn btn-xs btn-info btn-rounded">
												<PencilSquareIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></PencilSquareIcon>
											</button>
										</td>
									</tr>
								</tbody>
							</table>
							<div class="flex justify-end p-2 border-t">
								<nav aria-label="Page navigation example">
									<TailwindPagination
										:data="enduses"
										:limit="1"
										@pagination-change-page="getEnduse"
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
