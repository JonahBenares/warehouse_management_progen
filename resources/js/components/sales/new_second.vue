<script setup>
import navigation from '@/layouts/navigation.vue';
import { XMarkIcon, ArrowUturnLeftIcon, PlusIcon, Bars3Icon} from '@heroicons/vue/24/solid'
import {onMounted, ref} from "vue";
import { useRouter } from "vue-router";
const router = useRouter();

let form = ref({
        id:''
    })

let form_receive=ref([]);
const showModal = ref(false)
const hideModal = ref(true)
const showPreview = ref(false)
const hidePreview = ref(true)
const openPreview = () => {
		showPreview.value = !showPreview.value
		GetRequestHead()
	}
	const closePreview = () => {
		showPreview.value = !hidePreview.value
	}
let error = ref([])
let success = ref('')

const props = defineProps({
        id:{
            type:String,
            default:''
        }
    })

onMounted(async () => {
	GetRequestHead()
})

</script>

<template>
    <navigation>
        <div class="container-fluid">
			<!-- BreadCrumb -->
			<div class="card mb-3">	
				<div class="flex justify-between content-center">
					<div class="flex justify-start space-x-3 ">
						<div class="">
							<a href="/sales/new" class="btn btn-secondary btn-xs btn-rounded">
								<ArrowUturnLeftIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></ArrowUturnLeftIcon>
							</a>
						</div>
						<div>
							<h6 class="m-0 pt-1 font-bold uppercase">Sales</h6>
						</div>
					</div>
					<div class="pt-1">	
						<nav aria-label="breadcrumb" role="navigation">
							<ol class="breadcrumb adminx-page-breadcrumb">
								<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
								<li class="breadcrumb-item"><a href="/sales">Sales</a></li>
								<li class="breadcrumb-item active" aria-current="page">Add New Sales</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>

			<div class="row mb-3">
				<div class="col-md-12 col-lg-12">
					<div class="card card-main-bg">
						<div class="py-4 px-2">
							<table class="w-full table-bordeSred">
								<tr>
									<td class="" width="50%">
										<div class="flex justify-start">
											<span class="text-lg uppercase font-bold text-gray-600 w-full leading-none">
												PROBCD-20225-9231
											</span>
										</div>
										<div class="flex justify-start ">
											<span class="text-xs text-gray-500 leading-none pt-1">DR NO.</span>
										</div>
									</td>
									<td class="" width="25%">
										<div class="flex justify-start">
											<span class="text-md uppercase font-bold text-gray-600 w-full leading-none">
												sample
											</span>
										</div>
										<div class="flex justify-start" >
											<span class="text-xs text-gray-500 leading-none pt-1">DATE</span>
										</div>
									</td>
									<td class="" width="25%" colspan="2">
										<div class="flex justify-start">
											<span class="text-md uppercase font-bold text-gray-600 w-full leading-none">
												sample
											</span>
										</div>
										<div class="flex justify-start" >
											<span class="text-xs text-gray-500 leading-none pt-1">SOURCE PR NUMBER</span>
										</div>
									</td>
								</tr>
								<tr>
									<td colspan="6" class="py-1"></td>
								</tr>
								<tr>
									<td class="" colspan="">
										<div class="flex justify-start">
											<span class="text-md uppercase font-bold text-gray-600 w-full leading-none">
												CENPRI POWER
											</span>
										</div>
										<div class="flex justify-start" >
											<span class="text-xs text-gray-500 leading-none pt-1">BUYER</span>
										</div>
									</td>
									<td class="" colspan="">
										<div class="flex justify-start">
											<span class="text-md uppercase font-bold text-gray-600 w-full leading-none">
												CENPRI POWER
											</span>
										</div>
										<div class="flex justify-start" >
											<span class="text-xs text-gray-500 leading-none pt-1">CONTACT PERSON</span>
										</div>
									</td>
									<td class="" colspan="2">
										<div class="flex justify-start">
											<span class="text-md uppercase font-bold text-gray-600 w-full leading-none">
												sample
											</span>
										</div>
										<div class="flex justify-start" >
											<span class="text-xs text-gray-500 leading-none pt-1">PGC PR/PO NUMBER</span>
										</div>
									</td>
								</tr>
								<tr>
									<td colspan="6" class="py-1"></td>
								</tr>
								<tr>
									<td class="" colspan="">
										<div class="flex justify-start">
											<span class="text-md uppercase font-bold text-gray-600 w-full leading-none">
												BACOLOD CITY
											</span>
										</div>
										<div class="flex justify-start" >
											<span class="text-xs text-gray-500 leading-none pt-1">ADDRESS</span>
										</div>
									</td>
									<td class="" colspan="">
										<div class="flex justify-start">
											<span class="text-md uppercase font-bold text-gray-600 w-full leading-none">
												BACOLOD CITY
											</span>
										</div>
										<div class="flex justify-start" >
											<span class="text-xs text-gray-500 leading-none pt-1">CONTACT NUMBER</span>
										</div>
									</td>
									<td class="">
										<div class="flex justify-start">
											<span class="text-md uppercase font-bold text-gray-600 w-full leading-none">
												sample
											</span>
										</div>
										<div class="flex justify-start" >
											<span class="text-xs text-gray-500 leading-none pt-1">PR DATE</span>
										</div>
									</td>
									<td class="">
										<div class="flex justify-start">
											<span class="text-md uppercase font-bold text-gray-600 w-full leading-none">
												sample
											</span>
										</div>
										<div class="flex justify-start" >
											<span class="text-xs text-gray-500 leading-none pt-1">VAT</span>
										</div>
									</td>
								</tr>
								<tr>
									<td colspan="6" class="py-1"></td>
								</tr>
							</table>
							
							<div class="mt-2">	
								<table class="table table-bordered">
									<thead>
										<tr>
											<th class="text-sm" width="1%">#</th>
											<th class="text-sm" width="25%">Item Description</th>
											<th class="text-sm" width="25%">Cross Reference</th>
											<th class="text-sm" width="12%">Serial No</th>
											<th class="text-sm" width="7%">Qty</th>
											<th class="text-sm" width="7%">Selling Price</th>
											<th class="text-sm" width="7%">Discount</th>
											<th class="text-sm" width="7%">Shipping Fee</th>
											<th class="text-sm" width="7%">Total Cost</th>
											<th class="text-sm" width="2%" align="center">
												<Bars3Icon class="size-5"></Bars3Icon>
											</th>
										</tr>
									</thead>
									<tbody>
										<tr >
											<td class="text-sm p-1 text-center">#</td>
											<td class="text-sm p-0">
												<select name="" id="" class="w-full bg-yellow-100 p-1 h-full">
													<option value="">Select Item</option>
												</select>
											</td>
											<td class="text-sm p-0">
												<select name="" id="" class="w-full bg-yellow-100 p-1 h-full">
													<option value="">Select Item</option>
												</select>
											</td>
											<td class="text-sm p-0">
												<input type="text" class="w-full bg-yellow-100 p-1 h-full"/>
											</td>
											<td class="text-sm p-0">
												<input type="number" class="w-full bg-yellow-100 p-1 h-full"/>
											</td>
											<td class="text-sm p-0">
												<input type="text" class="w-full bg-yellow-100 p-1 h-full text-right"/>
											</td>
											<td class="text-sm p-0">
												<input type="text" class="w-full bg-yellow-100 p-1 h-full text-right"/>
											</td>
											<td class="text-sm p-0">
												<input type="text" class="w-full bg-yellow-100 p-1 h-full text-right"/>
											</td>
											<td class="text-sm p-0">
												<input type="text" class="w-full bg-yellow-100 p-1 h-full text-right"/>
											</td>
											<td class="text-xs p-1" align="center">
												<button class="bg-blue-500 p-1 text-white rounded-xl">
													<PlusIcon class="size-3"></PlusIcon>
												</button>
											</td>
										</tr>
										<tr >
											<td class="text-sm p-1 text-center">1</td>
											<td class="text-sm p-1">sample</td>
											<td class="text-sm p-1">sample</td>
											<td class="text-sm p-1">sample</td>
											<td class="text-sm p-1">sample</td>
											<td class="text-sm p-1 text-right">10018.22</td>
											<td class="text-sm p-1 text-right">10018.22</td>
											<td class="text-sm p-1 text-right">10018.22</td>
											<td class="text-sm p-1 text-right">10018.22</td>
											<td class="text-xs p-1" align="center">
												<button class="bg-red-500 p-1 text-white rounded-xl">
													<XMarkIcon class="size-3"></XMarkIcon>
												</button>
											</td>
										</tr>
									</tbody>
								</table>
							</div>	
							<hr class="border-dashed m-2">	
							<div class="row">
								<div class="col-lg-12"> 
									<div class="mb-0 mt-1 flex justify-end space-x-10">
										<!-- <button  @click="openPreview()" class="btn btn-sm py-1.5 btn-success w-32">Preview</button> -->
										<div class="flex justify-between space-x-5">
											<div class="flex justify-between space-x-1">
												<button @click="cancelTransaction(form.id)" class="btn btn-sm btn-danger" >Cancel Transaction</button>
												<!-- <button @click="saveDraft(form.id)" type="submit" class="btn btn-sm py-1.5 bg-orange-400 hover:bg-orange-600 w-32 text-white">Save as Draft</button> -->
												<button @click="SaveNewRequest(form.id,'save')" id = "SubmitButton" type="submit" class="btn btn-sm py-1.5 bg-blue-500 text-white hover:bg-blue-600 w-60">Save & Print</button>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
			<div class="modal pt-4 px-3" :class="{ show:showPreview }">
				<div></div>
				<div class="modal__content w-full">
					<div class="row mb-4">
						<div class="col-lg-12 flex justify-end">
							<a href="#" class="text-gray-600" @click="closePreview">
								<XMarkIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"></XMarkIcon>
							</a>
						</div>
					</div>
					<div class="p- pt-2">
							<div class="row">
								<div class="col-lg-3">
									<table class="w-full border border-r-0">
										<tr>
											<td class="px-1" width="25%">
												<div class="flex justify-start" >
													<span class="text-xs text-gray-500 leading-none">MREQF NO.</span>
												</div>
											</td>
										</tr>
										<tr>
											<td class="p-1 pt-0  border-b leading-none" >
												<span class="text-base font-bold text-gray-600 w-full leading-none">
													sample
												</span>
											</td>
										</tr>
										<tr>
											<td class="px-1 leading-none">
												<span class="text-xs text-gray-500 leading-none">DATE/TIME</span>
											</td>
										</tr>
										<tr>
											<td class="p-1 pt-0 border-b leading-none">
												<span class="text-base font-bold text-gray-600 w-full leading-none" >sample - sample
												</span>
											</td>
										</tr>
										<tr>
											<td class="px-1 leading-none" colspan="2">
												<span class="text-xs text-gray-500 leading-none">REQUEST TYPE</span>
											</td>
										</tr>
										<tr>
											<td class="p-1 pt-0 leading-none" colspan="2">
												<span class="text-base font-bold text-gray-600 w-full leading-none">
													sample
												</span>
											</td>
										</tr>
									</table>
								</div>
								<div class="col-lg-9 pl-0">
									<table class="w-full border">
										<tr>
											<td class="px-1 border-r" width="25%">
												<div class="flex justify-start" >
													<span class="text-xs text-gray-500 leading-none">JO/PR NUMBER</span>
												</div>
											</td>
											<td class="px-1 border-r" width="25%">
												<div class="flex justify-start" >
													<span class="text-xs text-gray-500 leading-none">DEPARTMENT</span>
												</div>
											</td>
											<td class="px-1 border-r" width="25%">
												<div class="flex justify-start" >
													<span class="text-xs text-gray-500 leading-none">END-USE</span>
												</div>
											</td>
										</tr>
										<tr>
											<td class="p-1 pt-0 border-r border-b leading-none">
												<span class="text-base font-bold text-gray-600 w-full leading-none">
													sample
												</span>
											</td>
											<td class="p-1 pt-0 border-r border-b leading-none">
												<span class="text-base font-bold text-gray-600 w-full leading-none">
													sample
												</span>
											</td>
											<td class="p-1 pt-0 border-r border-b leading-none">
												<span class="text-base font-bold text-gray-600 w-full leading-none">
													sample
												</span>
											</td>
										</tr>
										<tr>
											<td class="px-1 border-r leading-none" colspan="3">
												<span class="text-xs text-gray-500 leading-none">PURPOSE</span>
											</td>
										</tr>
										<tr>
											<td class="p-1 pt-0 border-r border-b leading-none" colspan="3">
												<span class="text-base font-bold text-gray-600 w-full leading-none">
													sample
												</span>
											</td>
										</tr>
										<tr>
											<td class="px-1 border-r leading-none" colspan="3">
												<span class="text-xs text-gray-500 leading-none">REMARKS</span>
											</td>
										</tr>
										<tr>
											<td class="p-1 pt-0 border-r leading-none" colspan="3">
												<span class="text-base font-bold text-gray-600 w-full leading-none">
													sample
												</span>
											</td>
										</tr>
									</table>
								</div>
							</div>
						</div> 
				</div>
			</div>
		</Transition>

		
    </navigation>
</template>
