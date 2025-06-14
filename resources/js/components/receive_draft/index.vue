<script setup>
	import navigation from '@/layouts/navigation.vue';
	import { EyeIcon, TrashIcon, PlusIcon, MagnifyingGlassIcon, Bars3Icon, ArrowUturnLeftIcon, BarsArrowUpIcon } from '@heroicons/vue/24/solid'
	import axios from "axios";
	import{ onMounted, ref } from "vue"
    import { useRouter } from "vue-router"
    const router = useRouter()
	let list=ref([])
	let searchDraft=ref([]);

	onMounted(async () => {
		draftList()
	})

	const draftList = async (page = 1) => {
		const response = await fetch(`/api/get_drafts?page=${page}&filter=${searchDraft.value}`);
		list.value = await response.json();
	}

	const search = async () => {
		let response = await fetch('/api/search_drafts?filter='+searchDraft.value);
		list.value = await response.json();
	}

	const cancelDraft= (id) =>{

		if(confirm("Are you sure you want to cancel this draft?")){
		 	axios.get("/api/cancel_draft/"+id).then(response => {
				location.reload()
			})
		}
	}

	const showDraft= (id) =>{

		axios.get("/api/get_max_detail/"+id).then(response => {
			router.push('/receive/new_second/'+id+'/'+response.data)
		})
		
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
							<a onclick="history.back()" class="btn btn-secondary btn-xs text-white btn-rounded">
								<ArrowUturnLeftIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></ArrowUturnLeftIcon>
							</a>
						</div>
						<div>
							<h6 class="m-0 pt-1 font-bold uppercase">Receive <span class="text-yellow-500">Drafts</span></h6>
						</div>
					</div>	
					<div class="pt-1">	
						<nav aria-label="breadcrumb" role="navigation">
							<ol class="breadcrumb adminx-page-breadcrumb">
								<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
								<li class="breadcrumb-item"><a href="/receive">Receive</a></li>
								<li class="breadcrumb-item active" aria-current="page">Drafts</li>
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
										<input type="text" class="form-control border-0" id="search" placeholder="Type to search..." @keyup="draftList()" v-model="searchDraft">
									</div>
								</div>
							
							</div>
							<table class="table table-actions table-borderesd table-hover mb-0">
								<thead>
									<tr>
										<th scope="col" width="30%">MRecF #</th>
										<th scope="col" width="15%">Receive Date</th>
										<th scope="col" width="20%">DR No.</th>
										<th scope="col" width="15%">PO No.</th>
										<th scope="col" width="15%">SI No.</th>
										<th scope="col" width="1%" align="center" class="pr-2">
											<div class="flex justify-center">
												<Bars3Icon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"></Bars3Icon>
											</div>
										</th>
									</tr>
								</thead>
								<tbody>
									<tr v-for="li in list.data">


										<td>{{ li.mrecf_no }}</td>
										<td>{{ li.receive_date }}</td>
										<td>{{ li.dr_no }}</td>
										<td>{{ li.po_no }}</td>
										<td>{{ li.si_no }}</td>
										<td class="pl-2 pr-2 font-bold">
                                            <div class="space-x-1 flex justify-center">
                                                <a @click="showDraft(li.id)" class="text-white btn btn-xs bg-yellow-500 btn-rounded">
                                                    <EyeIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></EyeIcon>
                                                </a>
												<a @click="cancelDraft(li.id)" class="text-white btn btn-xs bg-red-500 btn-rounded">
                                                    <TrashIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></TrashIcon>
                                                </a>
                                            </div>
										</td>
									</tr>
								</tbody>
							</table>
							<div class="flex justify-end p-2 border-t">
								<nav aria-label="Page navigation example">
									<TailwindPagination
											:data="list"
											@pagination-change-page="draftList"
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
