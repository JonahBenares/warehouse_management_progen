<script setup>
import navigation from '@/layouts/navigation.vue';
import { ArrowUturnLeftIcon } from '@heroicons/vue/24/solid'
import axios from 'axios';
import {onMounted, ref} from "vue";
import { useRouter } from "vue-router";
const router = useRouter();

let form = ref({
        id:''
    })
let details=ref([]);

const props = defineProps({
        id:{
            type:String,
            default:''
        }
    })

onMounted(async () => {
	GetBorrowHead()
	GetBorrowDetails()
})


const GetBorrowHead = async () => {
			let response = await axios.get(`/api/get_borrow_head/${props.id}`)
			form.value=response.data.head
		}

		const GetBorrowDetails = async () => {
			let response = await axios.get(`/api/get_borrow_details/${props.id}`)
			details.value=response.data.details
		}

		const PrintBorrow = (id) => {
		router.push('/borrow/print/'+id)
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
							<a href="/borrow" class="btn btn-secondary btn-xs btn-rounded">
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
								<li class="breadcrumb-item"><a href="/borrow">Borrow</a></li>
								<li class="breadcrumb-item active" aria-current="page">Add New Borrow</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>	

			<div class="row mb-3">
				<div class="col-md-12 col-lg-12">
					<div class="card card-main-bg">
						<div class="py-4 px-2">
							<table class="w-full table-bordsered">
								<tr>
									<td class="" width="18%">
										<div class="flex justify-start">
											<span class="text-lg uppercase font-bold text-gray-600 w-full leading-none">
												{{ form.mbr_no}}
											</span>
										</div>
										<div class="flex justify-start ">
											<span class="text-xs text-gray-500 leading-none pt-1">MRF NO.</span>
										</div>
									</td>
									<td class="" width="15%">
										<div class="flex justify-start">
											<span class="text-md uppercase font-bold text-gray-600 w-full leading-none">
												{{ form.borrowed_by_user_name}}
											</span>
										</div>
										<div class="flex justify-start" >
											<span class="text-xs text-gray-500 leading-none pt-1">BORROWER'S NAME</span>
										</div>
									</td>
									<td width="11%"></td>
									<td width="12%"></td>
									<td width="20%"></td>
									<td class="" width="10%">
										<div class="flex justify-end">
											<span class="text-md uppercase font-bold text-gray-600 w-full leading-none text-right">
												{{ form.borrow_date}}
											</span>
										</div>
										<div class="flex justify-end" >
											<span class="text-xs text-gray-500 leading-none pt-1">DATE</span>
										</div>
									</td>
									<td class=""  width="7%">
										<div class="flex justify-end">
											<span class="text-md uppercase font-bold text-gray-600 w-full leading-none text-right">
												{{ form.borrow_time}}
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
							</table>
						</div>
						<div class="px-2">	
							<table class="table table-bordered">
								<thead>
									<tr>
										<th class="font-xxs">Item Description</th>
										<th class="font-xxs">Borrow From</th>
										<th class="font-xxs" width="6%">Avail Qty</th>
										<th class="font-xxs" width="7%">Borrow Qty</th>
										<th class="font-xxs" width="13%">Borrow By</th>
										<th class="font-xxs">Department</th>
										<th class="font-xxs">Enduse</th>
										<th class="font-xxs">Purpose</th>
										<th class="font-xxs" width="15%">Remarks</th>
									</tr>
								</thead>
								<tbody>
									<tr v-for="det in details">
											<td class="text-xs">{{ det.item_description }}</td>
											<td class="text-xs">{{ det.borrowed_from }}</td>
											<td class="text-xs">{{ det.avail_qty }}</td>
											<td class="text-xs">{{ det.quantity }}</td>
											<td class="text-xs">{{ det.borrowed_by }}</td>
											<td class="text-xs">{{ det.department }}</td>
											<td class="text-xs">{{ det.enduse }}</td>
											<td class="text-xs">{{ det.purpose }}</td>
											<td class="text-xs">{{ det.remarks_item }}</td>
										</tr>
								</tbody>
							</table>
						</div>
						<hr class="border-dashed m-2">	
						<div class="mb-2 mt-2 flex justify-end space-x-10">
							<div class="flex justify-between space-x-1">
								<a @click="PrintBorrow(form.id)" class="btn btn-sm hover:bg-blue-600 bg-blue-500 text-white w-60">Print</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
    </navigation>
</template>


