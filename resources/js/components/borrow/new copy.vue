<script setup>
import navigation from '@/layouts/navigation.vue';
import { PencilSquareIcon, TrashIcon, PlusIcon, MagnifyingGlassIcon, ChevronLeftIcon, ChevronRightIcon, ArrowUturnLeftIcon } from '@heroicons/vue/24/solid'
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
								<li class="breadcrumb-item"><a href="/request">Borrow</a></li>
								<li class="breadcrumb-item active" aria-current="page">Add New Borrow</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>	

			<div class="row">
				<div class="col-md-12 col-lg-12">
					<div class="card card-main-bg">
						<div class="p-2 pt-3">
							<table class="w-full">
								<tr>
									<td class="px-1 " width="20%">
										<div class="flex justify-start ">
											<span class="text-xs text-gray-500 leading-none">MREQF NO.</span>
										</div>
										<span class="text-lg uppercase font-bold text-gray-700 w-full leading-none">
											<input type="text" class="form-control border my-1" disabled  value="1231">
										</span>
									</td>
									
									<td class="px-1 " width="20%">
										<div class="flex justify-start" >
											<span class="text-xs text-gray-500 leading-none">REQUEST TYPE</span>
										</div>
										<span class="text-lg uppercase text-gray-700 w-full leading-none">
											<select class="form-control border my-1" ></select>
										</span>
									</td>
									<td class="px-1 " width="20%">
										<div class="flex justify-start" >
											<span class="text-xs text-gray-500 leading-none">BORROWED BY</span>
										</div>
										<span class="text-lg uppercase text-gray-700 w-full leading-none">
											<select class="form-control border my-1" ></select>
										</span>
									</td>
									<td class="px-1 " width="20%">
										<div class="flex justify-start" >
											<span class="text-xs text-gray-500 leading-none">BORROWED FROM</span>
										</div>
										<span class="text-lg uppercase text-gray-700 w-full leading-none">
											<select class="form-control border my-1" ></select>
										</span>
									</td>
									<td class="px-1 " width="10%">
										<div class="flex justify-start" >
											<span class="text-xs text-gray-500 leading-none">DATE</span>
										</div>
										<span class="text-lg uppercase text-gray-700 w-full leading-none">
											<input type="date" class="form-control border my-1" >
										</span>
									</td>
									<td class="px-1 "  width="10%">
										<div class="flex justify-start">
											<span class="text-xs text-gray-500 leading-none">TIME</span>
										</div>
										<span class="text-lg uppercase text-gray-700 w-full leading-none">
											<input type="time" class="form-control border my-1 py-1" >
										</span>
									</td>
								</tr>
								<tr>
									<td colspan="6" class="py-2"></td>
								</tr>
								<tr>
									<td class="px-1 " colspan="1">
										<div class="flex justify-start" >
											<span class="text-xs text-gray-500 leading-none">DEPARTMENT</span>
										</div>
										<span class="text-lg uppercase text-gray-700 w-full leading-none">
											<select class="form-control border my-1" ></select>
										</span>
									</td>
									<td class="px-1 " colspan="">
										<div class="flex justify-start" >
											<span class="text-xs text-gray-500 leading-none">ENDUSE</span>
										</div>
										<span class="text-lg uppercase text-gray-700 w-full leading-none">
											<select class="form-control border my-1" ></select>
										</span>
									</td>
									<td class="px-1 " colspan="2">
										<div class="flex justify-start" >
											<span class="text-xs text-gray-500 leading-none">PURPOSE</span>
										</div>
										<span class="text-lg uppercase text-gray-700 w-full leading-none">
											<select class="form-control border my-1" ></select>
										</span>
									</td>
									<td class="px-1 " colspan="2">
										<div class="flex justify-start" >
											<span class="text-xs text-gray-500 leading-none">REMARKS</span>
										</div>
										<span class="text-lg uppercase text-gray-700 w-full leading-none">
											<input type="Text" class="form-control border my-1" >
										</span>
									</td>
								</tr>
							</table>
						</div>
						<hr class="border-dashed m-2">	
						<div class="px-2 pt-2 mb-2 mt-1 flex justify-end space-x-10">
							<div class="flex justify-between space-x-1">
								<a href="/borrow/new_second" class="btn btn-sm btn-primary btn-rounded w-32">Next</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
    </navigation>
</template>
