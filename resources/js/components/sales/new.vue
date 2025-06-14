<script setup>
	import navigation from '@/layouts/navigation.vue';
	import { CheckCircleIcon, ExclamationCircleIcon, ArrowUturnLeftIcon } from '@heroicons/vue/24/solid'
	import { onMounted, ref, nextTick } from "vue";
	import { useRouter } from "vue-router";

	const router = useRouter();
	let form = ref([]);
	let error = ref([]);
	let success = ref('');

	// Make checkboxes behave like radio buttons
	onMounted(() => {
	nextTick(() => {
		const checkboxes = document.querySelectorAll('.vat-checkbox');
		checkboxes.forEach(checkbox => {
		checkbox.addEventListener('change', function () {
			if (this.checked) {
			checkboxes.forEach(cb => {
				if (cb !== this) cb.checked = false;
			});
			}
		});
		});
	});
	});
</script>


<template>
	<div class="col-lg-4 offset-lg-4">
		<div class="flex content-center">
			<div class="hide-animate" v-if="success" id="success">
				<div class="alert alert-success alert-top my-2">
					<div class="flex justify-start space-x-1">
						<div>
							<CheckCircleIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12"></CheckCircleIcon>
						</div> 
						<div class="py-1">
							<h6 class="font-bold m-0">Success!</h6>
							<p class="text-sm m-0 text-gray-400"> {{ success }}</p>
						</div>
					</div>
				</div>
			</div>
			<div v-else id="success"></div>
			<div class="hide-animate" v-if="error.length > 0" id="error">
				<div class="alert alert-danger alert-top my-2" >
					<div class="flex justify-start space-x-2">
						<div>
							<ExclamationCircleIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12"></ExclamationCircleIcon>
						</div> 
						<div class="py-1">
							<h6 class="font-bold m-0">Error!</h6>
							<p class="text-sm m-0 text-gray-400" v-for="err in error"> {{ err }}</p>
						</div>
					</div>
				</div>
			</div>
			<div v-else id="error"></div>
		</div>
	</div>
    <navigation>
        <div class="container-fluid">
			<!-- BreadCrumb -->
			<div class="card mb-3">	
				<div class="flex justify-between content-center">
					<div class="flex justify-start space-x-3 ">
						<div class="">
							<a href="/request" class="btn btn-secondary btn-xs btn-rounded">
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

			<div class="row">
				<div class="col-md-12 col-lg-12">
					<div class="card card-main-bg">
						<div class="pt-3 p-2">
							<div class="row">
								<div class="col-lg-12">
									<table class="w-full table-borderded">
										<tr>
											<td width="25%" class="px-1">
												<div class="flex justify-start ">
													<span class="text-xs text-gray-500 leading-none">DATE</span>
												</div>
												<span class="text-lg uppercase text-gray-700 w-full leading-none">
													<input type="text" class="form-control border my-1" disabled>
												</span>
											</td>
											<td width="25%" class="px-1">
												<div class="flex justify-start ">
													<span class="text-xs text-gray-500 leading-none">PR/PO DATE</span>
												</div>
												<span class="text-lg uppercase text-gray-700 w-full leading-none">
													<input type="date" class="form-control border my-1">
												</span>
											</td>
											<td width="25%" class="px-1">
												<div class="flex justify-start ">
													<span class="text-xs text-gray-500 leading-none">SOURCE PR/PO NO. </span>
												</div>
												<span class="text-lg uppercase text-gray-700 w-full leading-none">
													<select name="request_type" id="request_type" class="form-control border my-1" v-model="request_type" @change="DisableInput()">
														<option value="Sample PR/PO">Sample PR/PO</option>
														<option value="Sample PR/PO1">Sample PR/PO1</option>
														<option value="Sample PR/PO2">Sample PR/PO2</option>
													</select>
												</span>
											</td>
											<td width="25%" class="px-1">
												<div class="flex justify-start ">
													<span class="text-xs text-gray-500 leading-none">PGC PR/PO NO.</span>
												</div>
												<span class="text-lg uppercase text-gray-700 w-full leading-none">
													<select name="request_type" id="request_type" class="form-control border my-1" v-model="request_type" @change="DisableInput()">
														<option value="With PR">With PR</option>
														<option value="WH STOCKS">WH STOCKS</option>
													</select>
												</span>
											</td>
											
										</tr>
										<tr>
											<td class="px-1" colspan="2">
												<div class="flex justify-start" >
													<span class="text-xs text-gray-500 leading-none">BUYER</span>
												</div>
												<span class="text-base  text-gray-600 w-full leading-none">
													<select name="pr_no" id="pr_no" class="form-control border  my-1" v-model="pr_no" @change="chooseprno()"> 
														<option :value="pr.pr_no" v-for="pr in prnolist" :key="pr.id">{{ pr.pr_no }}</option>
													</select>
												</span>
											</td>
											<td class="px-1"  colspan="2">
												<div class="flex justify-start" >
													<span class="text-xs text-gray-500 leading-none">ADDRESS</span>
												</div>
												<span class="text-base  text-gray-600 w-full leading-none">
													<input name="" id="" class="form-control border  my-1"/>
												</span>
											</td>
										</tr>
										<tr>
											<td class="px-1">
												<div class="flex justify-start">
													<span class="text-xs text-gray-500 leading-none">CONTACT PERSON</span>
												</div>
												<span class="text-base text-gray-600 w-full leading-none">
													<input name="contact_person" id="contact_person" class="form-control border my-1 w-full px-2 py-1 rounded" />
												</span>
											</td>
											<td class="px-1">
												<div class="flex justify-start">
													<span class="text-xs text-gray-500 leading-none">CONTACT NO.</span>
												</div>
												<span class="text-base text-gray-600 w-full leading-none">
													<input name="contact_no" id="contact_no" class="form-control border my-1 w-full px-2 py-1 rounded" />
												</span>
											</td>
											<td colspan="2" class="px-1 align-top pt-4">
												<div class="flex items-center space-x-6">
													<label class="inline-flex items-center text-sm text-gray-500">
														<input 
															type="checkbox" 
															name="vat_status" 
															value="vat" 
															class="vat-checkbox form-checkbox text-blue-600 h-6 w-6 border-blue-500 focus:ring-blue-500"
														/>
														<span class="ml-1 text-base">VAT</span>
													</label>
													<label class="inline-flex items-center text-sm text-gray-500">
														<input 
															type="checkbox" 
															name="vat_status" 
															value="non-vat" 
															class="vat-checkbox form-checkbox text-blue-600 h-6 w-6 border-blue-500 focus:ring-blue-500"
														/>
														<span class="ml-1 text-base">Non-VAT</span>
													</label>
												</div>
											</td>
										</tr>
									</table>
								</div>
							</div>
						</div>
					
						<hr class="border-dashed m-2">	
						<div class="mb-2 mt-2 flex justify-end space-x-10">
							<div class="flex justify-between space-x-1">
								<a href="/sales/new_second/" class="btn btn-sm hover:bg-blue-600 bg-blue-500 text-white w-60">Next</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
    </navigation>
</template>
