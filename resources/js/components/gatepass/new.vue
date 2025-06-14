<script setup>
	import navigation from '@/layouts/navigation.vue';
	import { ExclamationCircleIcon, ArrowUturnLeftIcon,CheckCircleIcon } from '@heroicons/vue/24/solid'
	import{ onMounted, ref } from "vue"
    import { useRouter } from "vue-router"

    const router = useRouter()
	let form=ref([]);
	let prepared_by = ref([])
	let approved_by = ref([])
	let noted_by = ref([])
	let error = ref([])
	let success = ref('')

	onMounted(async () => {
		GatepassHeadForm()
	})

	const GatepassHeadForm = async () => {
	let response = await axios.get("/api/create_gatepass_head");
	form.value = response.data.formData;
	prepared_by.value = response.data.prepared_by;
	approved_by.value = response.data.approved_by;
	noted_by.value = response.data.noted_by;
	}

	const SaveGatepassHead = () => {
		if(confirm("Are you sure you want to proceed?")){
			const formData=new FormData()
			const btn = document.getElementById("SubmitButton");
			
			formData.append('gatepass_no', form.value.gatepass_no)
			formData.append('to_company', form.value.to_company)
			formData.append('issue_date', form.value.issue_date)
			formData.append('destination', form.value.destination)
			formData.append('vehicle_no', form.value.vehicle_no)
			formData.append('prepared_by_id', form.value.prepared_by_id)
			formData.append('noted_by_id', form.value.noted_by_id)
			formData.append('approved_by_id', form.value.approved_by_id)
			formData.append('remarks', form.value.remarks)
			formData.append('user_id', form.value.user_id)

				axios.post("/api/add_gatepass_head",formData).then(function (response) {
					btn.disabled = false;
					error.value=[]
					success.value='Successfully saved!'
					router.push('/gatepass/new_second/'+response.data)
				}, function (err){
					error.value=[]
						document.getElementById("error").style.display="block"
						if (err.response.data.errors.request_type) {
							error.value.push(err.response.data.errors.request_type[0])
						}
						setTimeout(() => {
							document.getElementById("error").style.display="none"
						}, 4000);
				});
		}
	}
</script>

<template>
	<!-- <div class="col-lg-4 offset-lg-4">
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
	</div> -->
    <navigation>
        <div class="container-fluid">
			<!-- BreadCrumb -->
			<div class="card mb-3">	
				<div class="flex justify-between content-center">
					<div class="flex justify-start space-x-3 ">
						<div class="">
							<a href="/gatepass" class="btn btn-secondary btn-xs btn-rounded">
								<ArrowUturnLeftIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></ArrowUturnLeftIcon>
							</a>
						</div>
						<div class="flex justify-between ">
							<h6 class="m-0 pt-1 font-bold uppercase">Material Gatepass</h6>
							<!-- <h6 class="m-0 uppercase pt-1 mr-1">add new</h6> -->
						</div>
					</div>	
					<div class="pt-1">	
						<nav aria-label="breadcrumb" role="navigation">
							<ol class="breadcrumb adminx-page-breadcrumb">
								<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
								<li class="breadcrumb-item"><a href="/gatepass">Material Gatepass</a></li>
								<li class="breadcrumb-item active" aria-current="page">Add New Gatepass</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>	

			<div class="row">
				<div class="col-md-12 col-lg-12 ">
					<div class="card card-main-bg">
						<!-- <div class="alert alert-warning2 my-2 show-animate border-0 shadow-sm" v-if="draftcount == 1">
							<div class="flex justify-start space-x-2">
								<div class="text-yellow-600">
									<ExclamationCircleIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"></ExclamationCircleIcon>
								</div> 
								<div class="text-yellow-600">You have unsaved Receive Transaction/s. Click <a href="/receive_draft" class="underline">here</a> to view list.</div>
							</div>
						</div> -->
						<div class="p-2 pt-3">
							<div class="row">
								<div class="col-lg-12">
									<table class="w-full table-bordes\red">
										<tr>
											<td class="px-1 " width="20%">
												<div class="flex justify-start ">
													<span class="text-xs text-gray-500 leading-none">MGP NO.</span>
												</div>
											</td>
											<td class="px-1 " width="10%">
												<div class="flex justify-start" >
													<span class="text-xs text-gray-500 leading-none">TO COMPANY</span>
												</div>
											</td>
										</tr>
										<tr>
											<td class="p-1 pt-0">
												<span class="text-lg uppercase font-bold text-gray-700 w-full leading-none leading-none">
													<input type="text" class="form-control border" v-model="form.gatepass_no" disabled>
												</span>
											</td>
											<td class="p-1 pt-0" colspan="2">
												<!-- <div class="flex justify-start">
													<span class="text-base uppercase text-gray-700 w-full leading-none">
														<select class="form-control border w-full">
                                                            <option value=""></option>
                                                        </select>
													</span>
												</div> -->
												<input type="text" class="form-control border" v-model="form.to_company">
											</td>
										</tr>
                                        <tr>
                                            <td colspan="3" class="pt-2"></td>
                                        </tr>
                                        <tr>
                                            <td class="px-1 " >
												<div class="flex justify-start" >
													<span class="text-xs text-gray-500 leading-none uppercase">Date Issued</span>
												</div>
											</td>
											<td class="px-1 "  width="30%">
												<div class="flex justify-start">
													<span class="text-xs text-gray-500 leading-none uppercase">Destination</span>
												</div>
											</td>
											<td class="px-1 " width="30%">
												<div class="flex justify-start" >
													<span class="text-xs text-gray-500 leading-none uppercase">Vehicle No.</span>
												</div>
											</td>
										</tr>
                                        <tr>
                                            <td class="p-1 pt-0">
												<div class="flex justify-start">
													<span class="text-base uppercase text-gray-700 w-full leading-none">
														<input type="date" class="form-control border w-full" v-model='form.issue_date'>
													</span>
												</div>
											</td>
											<td class="p-1 pt-0">
												<!-- <div class="flex justify-start">
													<span class="text-base uppercase text-gray-700 w-full leading-none">
														<select  class="form-control border w-full">
                                                            <option value=""></option>
                                                        </select>
													</span>
												</div> -->
												<input type="text" class="form-control border" v-model="form.destination">
											</td>
											<td class="p-1 pt-0">
												<!-- <div class="flex justify-start">
													<span class="text-base uppercase text-gray-700 w-full leading-none">
														<select class="form-control border w-full">
                                                            <option value=""></option>
                                                        </select>
													</span>
												</div> -->
												<input type="text" class="form-control border" v-model="form.vehicle_no">
											</td>
										</tr>
										<tr>
                                            <td colspan="3" class="pt-2"></td>
                                        </tr>
										<tr>
                                            <td class="px-1 " >
												<div class="flex justify-start" >
													<span class="text-xs text-gray-500 leading-none uppercase">Prepared By</span>
												</div>
											</td>
											<td class="px-1 "  width="30%">
												<div class="flex justify-start">
													<span class="text-xs text-gray-500 leading-none uppercase">Noted By</span>
												</div>
											</td>
											<td class="px-1 " width="30%">
												<div class="flex justify-start" >
													<span class="text-xs text-gray-500 leading-none uppercase">Approved By</span>
												</div>
											</td>
										</tr>
                                        <tr>
                                            <td class="p-1 pt-0">
												<div class="flex justify-start">
													<span class="text-base uppercase text-gray-700 w-full leading-none">
														<select class="form-control border w-full" v-model="form.prepared_by_id">
															<option v-for="pb in prepared_by" v-bind:key="pb.id" v-bind:value="pb.id +'_'+ pb.name +'_'+ pb.position">{{  pb.name }}</option>
														</select>
													</span>
												</div>
											</td>
											<td class="p-1 pt-0">
												<div class="flex justify-start">
													<span class="text-base uppercase text-gray-700 w-full leading-none">
														<select  class="form-control border w-full" v-model="form.noted_by_id">
                                                            <option v-for="nb in noted_by" v-bind:key="nb.id" v-bind:value="nb.id +'_'+ nb.name +'_'+ nb.position">{{  nb.name }}</option>
                                                        </select>
													</span>
												</div>
											</td>
											<td class="p-1 pt-0">
												<div class="flex justify-start">
													<span class="text-base uppercase text-gray-700 w-full leading-none">
														<select class="form-control border w-full" v-model="form.approved_by_id">
                                                            <option v-for="ab in approved_by" v-bind:key="ab.id" v-bind:value="ab.id +'_'+ ab.name +'_'+ ab.position">{{  ab.name }}</option>
                                                        </select>
													</span>
												</div>
											</td>
										</tr>
										<tr>
                                            <td colspan="3" class="pt-2"></td>
                                        </tr>
										<tr>
											<td class="px-1 ">
												<div class="flex justify-start ">
													<span class="text-xs text-gray-500 leading-none uppercase">Remarks</span>
												</div>
											</td>
										</tr>
										<tr>
											<td class="px-1 " colspan="3">
												<span class="text-lg uppercase text-gray-700 w-full leading-none leading-none">
													<input type="text" class="form-control border" v-model="form.remarks">
												</span>
											</td>
										</tr>
										<input type="hidden" class="form-control border " v-model ='form.user_id'>
									</table>
								</div>
							</div>
						</div> 
						<div class="pt-3 mb-2 mt-2 border-t flex justify-end space-x-10">
							<div class="flex justify-between space-x-1">
								<!-- <a href="/gatepass/new_second" class="btn btn-sm btn-primary btn-rounded w-32">Next</a> -->
								<button @click="SaveGatepassHead()" id = "SubmitButton" class="btn btn-sm hover:bg-blue-600 bg-blue-500 text-white w-60">Next</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
    </navigation>
</template>
