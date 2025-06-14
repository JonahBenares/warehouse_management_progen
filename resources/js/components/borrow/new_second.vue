<script setup>
import navigation from '@/layouts/navigation.vue';
import { PencilSquareIcon, TrashIcon, PlusIcon, Bars3Icon, ChevronLeftIcon, ChevronRightIcon, ArrowUturnLeftIcon } from '@heroicons/vue/24/solid'
import axios from 'axios';
import {onMounted, ref} from "vue";
import { useRouter } from "vue-router";
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
												MREQ-1983884-29938
											</span>
										</div>
										<div class="flex justify-start ">
											<span class="text-xs text-gray-500 leading-none pt-1">MRF NO.</span>
										</div>
									</td>
									<td class="" width="15%">
										<div class="flex justify-start">
											<span class="text-md uppercase font-bold text-gray-600 w-full leading-none">
												Request Type
											</span>
										</div>
										<div class="flex justify-start" >
											<span class="text-xs text-gray-500 leading-none pt-1">REQUEST TYPE</span>
										</div>
									</td>
									<td width="11%"></td>
									<td width="12%"></td>
									<td width="20%"></td>
									<td class="" width="10%">
										<div class="flex justify-end">
											<span class="text-md uppercase font-bold text-gray-600 w-full leading-none text-right">
												10-01-2024
											</span>
										</div>
										<div class="flex justify-end" >
											<span class="text-xs text-gray-500 leading-none pt-1">DATE</span>
										</div>
									</td>
									<td class=""  width="7%">
										<div class="flex justify-end">
											<span class="text-md uppercase font-bold text-gray-600 w-full leading-none text-right">
												10:50 am
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
									<td class="" >
										<div class="flex justify-start">
											<span class="text-md uppercase font-bold text-gray-600 w-full leading-none">
												PR2888-8832-1011
											</span>
										</div>
										<div class="flex justify-start" >
											<span class="text-xs text-gray-500 leading-none pt-1">BORROWED BY</span>
										</div>
									</td>
									<td class="">
										<div class="flex justify-start">
											<span class="text-md uppercase font-bold text-gray-600 w-full leading-none">
												PR2888-8832-1011
											</span>
										</div>
										<div class="flex justify-start" >
											<span class="text-xs text-gray-500 leading-none pt-1">BORROWED FROM</span>
										</div>
									</td>
									<td class="" colspan="2">
										<div class="flex justify-start">
											<span class="text-sm  font-bold text-gray-600 w-full leading-none">
												Special Proj/Facilities Imp
											</span>
										</div>
										<div class="flex justify-start ">
											<span class="text-xs text-gray-500 leading-none pt-1">DEPARTMENT</span>
										</div>
									</td>
									<td class="" colspan="4">
										<div class="flex justify-start">
											<span class="text-sm  font-bold text-gray-600 w-full leading-none">
												Lube Oil Pre-Heater Controller at  DG Unit 2 & 3 Control Circuit
											</span>
										</div>
										<div class="flex justify-start ">
											<span class="text-xs text-gray-500 leading-none pt-1">ENDUSE</span>
										</div>
									</td>
									<!-- <td class="" colspan="2">
										<div class="flex justify-start" >
											<span class="text-xs text-gray-500 leading-none">PURPOSE</span>
										</div>
										<span class="text-lg uppercase text-gray-700 w-full leading-none">
											<input type="Text" class="form-control border my-1" >
										</span>
									</td> -->
								</tr>
								<tr>
									<td colspan="6" class="py-1"></td>
								</tr>
								<tr>
									<td class="" colspan="4">
										<div class="flex justify-start">
											<span class="text-sm font-bold text-gray-600 w-full leading-none">
												Materials for Modification of Boiler Shell for Replacement of Smokestack Pedestal Shell and Dismantling and Installation of Horizontal Exhaust Main Header Spool
											</span>
										</div>
										<div class="flex justify-start" >
											<span class="text-xs text-gray-500 leading-none pt-1">PURPOSE</span>
										</div>
									</td>
									<td class="" colspan="4">
										<div class="flex justify-start">
											<span class="text-sm font-bold text-gray-600 w-full leading-none">
												Materials for Modification of Boiler Shell for Replacement of Smokestack
											</span>
										</div>
										<div class="flex justify-start" >
											<span class="text-xs text-gray-500 leading-none pt-1">REMARKS</span>
										</div>
									</td>
								</tr>
							</table>
						</div>
						<div class="px-2">	
							<table class="table table-bordered">
								<thead>
									<tr>
										<th width="7%">Avail Qty</th>
										<th width="7%">Qty</th>
										<th width="40%">Item Decription</th>
										<th width="40%">Variant</th>
										<th class="p-1" width="1%">
											<a href="" class="btn btn-xs btn-primary btn-rounded">
												<PlusIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></PlusIcon>
											</a>
										</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td><input type="text" class="w-full text-right px-2" placeholder="00"></td>
										<td><input type="text" class="w-full text-right px-2" placeholder="00"></td>
										<td>
											<select name="" id="" class="w-full">
												<option value="">Sample</option>
												<option value="">Sample</option>
												<option value="">Sample</option>
											</select>
										</td>
										<td>
											<select name="" id="" class="w-full">
												<option value="">Sample</option>
												<option value="">Sample</option>
												<option value="">Sample</option>
											</select>
										</td>
										<td class="p-1">
											<a href="" class="btn btn-xs btn-danger btn-rounded">
												<TrashIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></TrashIcon>
											</a>
										</td>
									</tr>
									<tr>
										<td><input type="text" class="w-full text-right px-2" placeholder="00"></td>
										<td><input type="text" class="w-full text-right px-2" placeholder="00"></td>
										<td>
											<select name="" id="" class="w-full">
												<option value="">Sample</option>
												<option value="">Sample</option>
												<option value="">Sample</option>
											</select>
										</td>
										<td>
											<select name="" id="" class="w-full">
												<option value="">Sample</option>
												<option value="">Sample</option>
												<option value="">Sample</option>
											</select>
										</td>
										<td class="p-1">
											<a href="" class="btn btn-xs btn-danger btn-rounded">
												<TrashIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></TrashIcon>
											</a>
										</td>
									</tr>
									<tr>
										<td><input type="text" class="w-full text-right px-2" placeholder="00"></td>
										<td><input type="text" class="w-full text-right px-2" placeholder="00"></td>
										<td>
											<select name="" id="" class="w-full">
												<option value="">Sample</option>
												<option value="">Sample</option>
												<option value="">Sample</option>
											</select>
										</td>
										<td>
											<select name="" id="" class="w-full">
												<option value="">Sample</option>
												<option value="">Sample</option>
												<option value="">Sample</option>
											</select>
										</td>
										<td class="p-1">
											<a href="" class="btn btn-xs btn-danger btn-rounded">
												<TrashIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></TrashIcon>
											</a>
										</td>
									</tr>
									
								</tbody>
							</table>
						</div>
						<hr class="border-dashed m-2">	
						<div class="mb-2 mt-2 flex justify-end space-x-10">
							<div class="flex justify-between space-x-1">
								<a href="/" type="submit" class="btn btn-sm hover:bg-red-600 bg-red-500 text-white">Cancel Transaction</a>
								<a href="/borrow/print" type="submit" class="btn btn-sm hover:bg-blue-600 bg-blue-500 text-white w-60">Save and Print</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
    </navigation>
</template>


