<script setup>
import{ onMounted, ref } from "vue"
	import navigation from '@/layouts/navigation.vue';
	import { EyeIcon, XMarkIcon, CheckIcon, MagnifyingGlassIcon, Bars3Icon, ChevronRightIcon, ArrowUturnLeftIcon, BarsArrowUpIcon, ClipboardDocumentIcon } from '@heroicons/vue/24/solid'
	import DataTable from 'datatables.net-vue3';
	import DataTablesCore from 'datatables.net-bs5';
	import 'datatables.net-responsive';
	import 'datatables.net-select';
	import 'datatables.net-buttons';
	import 'datatables.net-buttons/js/buttons.html5';
	import 'datatables.net-buttons/js/buttons.print.js';
	import jszip from 'jszip';
	import $ from 'jquery'
    import moment from 'moment'
	DataTablesCore.Buttons.jszip(jszip);
	DataTable.use(DataTablesCore);
	import { useRouter } from "vue-router"
    const router = useRouter()
	let head = ref([])
	let searchRecieve=ref([]);
	const options = {
		dom: "<'row'<'col-sm-6 col-lg-6 mb-2'B><'col-sm-4 col-gl-4 offset-lg-2 offset-sm-2 mb-2'f>>"+"<'row'<'col-sm-12 mb-2'tr>>"+"<'row'<'col-sm-6 mb-2'i><'col-sm-6 mb-2'p>>",
		select: true,
		order:[0, 'desc'],	
		lengthMenu: [
			[10, 25, 50, -1],
			['10 rows', '25 rows', '50 rows', 'Show all']
		],
		buttons: [
			{
				extend: 'copy',
                title: 'Backorder User Acceptance (Completed)',
				exportOptions: {
					columns: [ 0, 1, 2, 3, 4, 5, 6],
					orthogonal: 'export',
				}
			},
			{
				extend: 'excel',
                title: 'Backorder User Acceptance (Completed)',
                exportOptions: {
					columns: [ 0, 1, 2, 3, 4, 5, 6],
					orthogonal: 'export',
                    format: {
                        body: function (data, row, column, node) {
                            if (column === 0){
                               return moment.utc(data).format('MMMM DD, YYYY');
                            }else if(column === 6){
								data = data.replace(/&gt;/g, '>')
                                   .replace(/&lt;/g, '<')
                                   .replace(/&amp;/g, '&')
                                   .replace(/&quot;/g, '"')
                                   .replace(/&#163;/g, '£')
                                   .replace(/&#39;/g, '\'')
                                   .replace(/&#10;/g, '\n');
								//replace html tags with one space
								data = data.replace(/<[^>]*>/g, ' ');
								//replace multiple spaces and tabs etc with one space
								return data.replace(/\s\s+/g, ' ');
							}else{
                                return data;
                            }
                        }
                    }
				},
                createEmptyCells: true,
                customize: function(xlsx) {
                    var sheet = xlsx.xl.worksheets['sheet1.xml'];
                    var clRow = $('row', sheet);
                    clRow[0].children[0].remove(); // clear header cell
                    $( 'row c', sheet ).attr( 's', '25' );
                }
			},
			{
				extend: 'print',
                title: 'Backorder User Acceptance (Completed)',
				exportOptions: {
					columns: [ 0, 1, 2, 3, 4, 5, 6],
					orthogonal: 'export',
				}
			},
			{
				extend: 'pageLength'
			}
		]
	};
	onMounted(async () =>{
        getAllReceive()
    })

	const getAllReceive = async (page = 1) => {
		// const response = await fetch(`/api/get_all_useracceptance_accepted?page=${page}&filter=${searchRecieve.value}`);
		// head.value = await response.json();
		let response = await axios.get(`/api/get_all_useracceptance_completed?page=${page}`);
		head.value=response.data.backorderarray
	}

    const acceptModal = ref(false)
    const rejectModal = ref(false)
	const hideModal = ref(true)
	const openAccept = () => {
		acceptModal.value = !acceptModal.value
	}
    const openReject = () => {
		rejectModal.value = !rejectModal.value
	}
	const closeModal = () => {
		acceptModal.value = !hideModal.value
        rejectModal.value = !hideModal.value
	}

	const showTransaction = (id) => {
		router.push('/user_acceptance/backorder_view/'+id)
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
						<div>
							<h6 class="m-0 pt-1 font-bold uppercase">Backorder User Acceptance <span class="text-sm text-green-600">(Completed)</span></h6>
						</div>
					</div>	
					<div class="pt-1">	
						<nav aria-label="breadcrumb" role="navigation">
							<ol class="breadcrumb adminx-page-breadcrumb">
								<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
								<li class="breadcrumb-item active" aria-current="page">Backorder User Acceptance (Completed)</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>	

			<div class="row">
				<div class="col-md-12 col-lg-12 ">
					<div class="card">
						<div class="table-responsive-md">
							<!-- <div class="flex justify-between pb-2 mt-2 mb-2">
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
									<a href="export" class="btn btn-sm btn-success btn-rounded">
										<div class="flex justify-between space-x-1" >
											<BarsArrowUpIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"></BarsArrowUpIcon>
											<span>Export</span>
										</div>
									</a>
								</div>
							</div> -->
							<!-- <table class="table table-actions table-bordesred table-hover mb-0"> -->
							<DataTable :data="head"  :options="options" class="table table-actions table-bordesred table-hover mb-0" width="100%">
								<thead>
									<tr>
										<th scope="col" width="20%">MrecF No.</th>
										<th scope="col" width="14%">Receive Date</th>
										<th scope="col" width="12%">DR No.</th>
										<th scope="col" width="12%">PO No.</th>
										<th scope="col" width="12%">SI No.</th>
										<th scope="col" width="12%">Waybill No.</th>
										<th scope="col" width="20%">PR No/s</th>
										<th scope="col" width="1%" align="center" class="pr-2">
											<div class="flex justify-center">
												<Bars3Icon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"></Bars3Icon>
											</div>
										</th>
									</tr>
								</thead>
								<template #column-6="props">
									<span class="text-white btn btn-xs py-0 !text-xs bg-gray-400 rounded mr-1" v-for="pr in props.rowData.backorder_details">{{ pr.pr_no + ' ' }}</span>
								</template>
								<template #column-7="props">
									<a @click="showTransaction(props.rowData.id)" class="text-white btn btn-xs bg-yellow-500 btn-rounded">
										<EyeIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></EyeIcon>
									</a>
								</template>
							</DataTable>
								<!-- <tbody>
									<tr v-for="h in head.data">
										<td>{{ h.mrecf_no }}</td>
										<td>{{ h.receive_date }}</td>
										<td>{{ h.dr_no }}</td>
										<td>{{ h.po_no }}</td>
										<td>{{ h.si_or }}</td>
                                        <td align="center">
                                            <span class="badge badge-pill badge-success" v-if="h.closed === 0"> Open</span>
                                            <span class="badge badge-pill badge-danger" v-else>Close</span>
                                        </td>
										<td>
											<span class="text-white btn btn-xs py-0 !text-xs bg-gray-400 rounded mr-1" v-for="pr in h.receive_details ">{{ pr.pr_no + ' ' }}</span>
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
							</div> -->
						</div>
					</div>
				</div>
			</div>
		</div>
    </navigation>
</template>
