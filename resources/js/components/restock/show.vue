''<script setup>
	import navigation from '@/layouts/navigation.vue';
	import { PencilSquareIcon, TrashIcon, MinusIcon, PlusIcon, MagnifyingGlassIcon, ChevronLeftIcon, ChevronRightIcon, ArrowUturnLeftIcon } from '@heroicons/vue/24/solid'
	import{ onMounted, ref } from "vue"
    import { useRouter } from "vue-router"
    const router = useRouter()
	let head = ref([]);
	let purpose = ref('');
	let enduse = ref('');
	let department = ref('');
	let details = ref([]);
	const props = defineProps({
        id:{
            type:String,
            default:''
        }
    })
	onMounted(async () => {
		restockShowform()
	})

	const restockShowform = async () => {
		let response = await axios.get(`/api/getshow_details/${props.id}`)
		head.value = response.data.head;
		details.value = response.data.details;
		purpose.value = response.data.purpose
        enduse.value = response.data.enduser
        department.value = response.data.department
	}

	const printForm = () => {
		router.push(`/restock/print/${props.id}`)
	}
</script>

<template>
    <navigation>
        <div class="container-fluid">
			<div class="card mb-3">	
				<div class="flex justify-between content-center">
					<div class="flex justify-start space-x-3 ">
						<div class="">
							<a href="/restock" class="btn btn-secondary btn-xs btn-rounded">
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
								<li class="breadcrumb-item"><a href="/restock">Restock</a></li>
								<li class="breadcrumb-item active" aria-current="page">Add New Restock</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>	

			<!-- <div class="card mt-2 mb-3">
				<div class="row">
					<div class="col-md-12 col-lg-12 ">
						<div class="row">
							<div class="col-lg-4">
								<div class="form-group">
									<label class="form-label mb-0">Purpose</label>
									<div class="flex justify-start">
										<span class="text-lg uppercase font-bold text-gray-700 w-full leading-none">
											MrecF-2012-09-0008
										</span>
									</div>
								</div>										
							</div>
							<div class="col-lg-4">
								<div class="form-group">
									<label class="form-label mb-0">Source PR Number</label>
									<div class="flex justify-start">
										<span class="text-lg uppercase font-bold text-gray-700 w-full leading-none">
											MrecF-2012-09-0008
										</span>
									</div>
								</div>										
							</div>
							<div class="col-lg-4">
								<div class="form-group">
									<label class="form-label mb-0">Destination</label>
									<div class="flex justify-start">
										<span class="text-lg uppercase font-bold text-gray-700 w-full leading-none">
											MrecF-2012-09-0008
										</span>
									</div>
								</div>										
							</div>
						</div>
					</div>
				</div>
			</div> -->

			<div class="row mb-3">
				<div class="col-md-12 col-lg-12">
					<div class="card card-main-bg">
						<div class="py-4 px-2">
							<table class="w-full table-borsdered">
								<!-- <tr v-for="h in head.data"> -->
								<tr>
									<td class="" width="20%">
										<div class="flex justify-start">
											<span class="text-lg uppercase font-bold text-gray-600 w-full leading-none">
												{{ head.mrs_no }}
											</span>
										</div>
										<div class="flex justify-start ">
											<span class="text-xs text-gray-500 leading-none pt-1 uppercase ">MRS NO</span>
										</div>
									</td>
									<td class="" width="20%">
										<div class="flex justify-start">
											<span class="text-lg uppercase font-bold text-gray-600 w-full leading-none">
												{{ head.source_pr }}
											</span>
										</div>
										<div class="flex justify-start ">
											<span class="text-xs text-gray-500 leading-none pt-1 uppercase ">Source PR Number</span>
										</div>
									</td>
									<td class="" width="20%">
										<div class="flex justify-start">
											<span class="text-md uppercase font-bold text-gray-600 w-full leading-none">
												{{ head.destination }}
											</span>
										</div>
										<div class="flex justify-start" >
											<span class="text-xs text-gray-500 leading-none pt-1 uppercase">Destination</span>
										</div>
									</td>
									<td width="11%"></td>
									<td width="12%"></td>
									<td class="" width="10%">
										<div class="flex justify-end">
											<span class="text-md uppercase font-bold text-gray-600 w-full leading-none text-right">
												{{ head.date }}
											</span>
										</div>
										<div class="flex justify-end" >
											<span class="text-xs text-gray-500 leading-none pt-1">DATE</span>
										</div>
									</td>
									<td class=""  width="8%">
										<div class="flex justify-end">
											<span class="text-md uppercase font-bold text-gray-600 w-full leading-none text-right">
												{{ head.time }}
											</span>
										</div>
										<div class="flex justify-end" >
											<span class="text-xs text-gray-500 leading-none pt-1 text-right">TIME</span>
										</div>
									</td>
								</tr>
								<tr>
									<td colspan="6" class="py-1"></td>
								</tr>
								<tr>
									<td class="" colspan="8">
										<div class="flex justify-start">
											<span class="text-sm font-bold text-gray-600 w-full leading-none">
												{{ head.purpose }}
											</span>
										</div>
										<div class="flex justify-start" >
											<span class="text-xs text-gray-500 leading-none pt-1">PURPOSE</span>
										</div>
									</td>
								</tr>
								<tr>
									<td colspan="6" class="py-1"></td>
								</tr>
								<tr>
									<td class="" width="20%">
										<div class="flex justify-start">
											<span class="text-sm font-bold text-gray-600 w-full leading-none">
												{{ head.department }}
											</span>
										</div>
										<div class="flex justify-start ">
											<span class="text-xs text-gray-500 leading-none pt-1 uppercase">Department</span>
										</div>
									</td>
									<td class="" width="20%" colspan="4">
										<div class="flex justify-start">
											<span class="text-sm font-bold text-gray-600 w-full leading-none">
												{{ head.enduse }}
											</span>
										</div>
										<div class="flex justify-start" >
											<span class="text-xs text-gray-500 leading-none pt-1 uppercase">End-use</span>
										</div>
									</td>
									<td width="10%"></td>
									<td width="10%"></td>
								</tr>
							</table>
						</div>
						<div class="px-2">	
							<table class="table table-actions table-bordered table-hover">
								<thead>
									<tr>
										<th class="font-xxs" width="5%">#</th>
										<th class="font-xxs" width="4%">Qty</th>
										<th class="font-xxs" width="15%">Supplier</th>
										<th class="font-xxs" width="20%">Item Description</th>
										<th class="font-xxs" width="">Brand</th>
										<th class="font-xxs" width="">Cat No.</th>
										<th class="font-xxs" width="">Serial No</th>
										<th class="font-xxs" width="">Uom</th>
										<th class="font-xxs" width="">Color</th>
										<th class="font-xxs" width="">Size</th>
										<th class="font-xxs" width="">Item Status</th>
										<th class="font-xxs" width="">Reason</th>
										<th class="font-xxs" width="">Remarks</th>
									</tr>
								</thead>
								<tbody>
									<tr v-for="(d,index) in details">
										<td class="text-xs">{{ index + 1 }}</td>
										<td class="text-xs">{{ d.quantity }}</td>
										<td class="text-xs">{{ d.supplier_name }}</td>
										<td class="text-xs">{{ d.item_description }}</td>
										<td class="text-xs">{{ d.brand }}</td>
										<td class="text-xs">{{ d.catalog_no }}</td>
										<td class="text-xs">{{ d.serial_no }}</td>
										<td class="text-xs">{{ d.uom }}</td>
										<td class="text-xs">{{ d.color }}</td>
										<td class="text-xs">{{ d.size }}</td>
										<td class="text-xs">{{ d.item_status }}</td>
										<td class="text-xs">{{ d.reason }}</td>
										<td class="text-xs">{{ d.remarks }}</td>
									</tr>
								</tbody>
							</table>
						</div>
						<hr class="border-dashed m-2">	
						<div class="mb-2 mt-2 flex justify-end space-x-10">
							<div class="flex justify-between space-x-1">
								<button @click="printForm()"  class="btn btn-sm hover:bg-blue-600 bg-blue-500 text-white w-60">Print</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
    </navigation>

	 
</template>
