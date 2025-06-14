<script setup>
	import navigation from '@/layouts/navigation.vue';
	import { ExclamationCircleIcon, ArrowUturnLeftIcon,CheckCircleIcon } from '@heroicons/vue/24/solid'
	import{ onMounted, ref } from "vue"
    import { useRouter } from "vue-router"



	// const authStore = useAuthStore();
	// if (authStore.user) {
	// 	router.push('/');
	// }
    const router = useRouter()
	let form=ref([])
	let error =ref([])
	let success = ref('')
	let draftcount = ref('')

	onMounted(async () => {
		receiveheadform()
		getDraftCount()
	})

	const receiveheadform = async () => {
		let response = await axios.get("/api/create_receive_head");
		form.value = response.data;
	}

	const getDraftCount = async () => {
		let response = await axios.get("/api/get_draft_count");
		draftcount.value = response.data;
		
	}

	const onSave = () => {

	
    
			if(confirm("Are you sure you want to save this transaction?")){
				const formData= new FormData()
				formData.append('mrecf_no', form.value.mrecf_no)
				formData.append('waybill_no', form.value.waybill_no)
				formData.append('receive_date', form.value.receive_date)
				formData.append('dr_no', form.value.dr_no)
				formData.append('po_no', form.value.po_no)
				formData.append('si_or', form.value.si_or)
				formData.append('pcf', form.value.pcf)
				formData.append('user_id', form.value.user_id)
				
				axios.post("/api/add_receive_head",formData).then(function (response) {
					
						error.value=[]
						success.value='Successfully saved!'
						router.push('/receive/new_second/'+response.data+'/1')
						// document.getElementById("success").style.display="block"
						// setTimeout(() => {
						// 	document.getElementById("success").style.display="none"
						// }, 4000);
				}, function (err) {
					error.value=[]
					document.getElementById("error").style.display="block"
					if (err.response.data.errors.mrecf_no) {
						error.value.push(err.response.data.errors.mrecf_no[0])
					}
					if (err.response.data.errors.receive_date) {
						error.value.push(err.response.data.errors.receive_date[0])
					}
					if (err.response.data.errors.dr_no) {
						error.value.push(err.response.data.errors.dr_no[0])
					}
					if (err.response.data.errors.pcf) {
						error.value.push(err.response.data.errors.pcf[0])
					}
					setTimeout(() => {
						document.getElementById("error").style.display="none"
					}, 4000);
				});
			}
		
		
	}
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
							<a href="/receive" class="btn btn-secondary btn-xs btn-rounded">
								<ArrowUturnLeftIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></ArrowUturnLeftIcon>
							</a>
						</div>
						<div class="flex justify-between ">
							<h6 class="m-0 pt-1 font-bold uppercase">Receive</h6>
							<!-- <h6 class="m-0 uppercase pt-1 mr-1">add new</h6> -->
						</div>
					</div>	
					<div class="pt-1">	
						<nav aria-label="breadcrumb" role="navigation">
							<ol class="breadcrumb adminx-page-breadcrumb">
								<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
								<li class="breadcrumb-item"><a href="/receive">Receive</a></li>
								<li class="breadcrumb-item active" aria-current="page">Add New Receive</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>	

			<div class="row">
				<div class="col-md-12 col-lg-12 ">
					<div class="card card-main-bg">
						<div class="alert alert-warning2 my-2 show-animate border-0 shadow-sm" v-if="draftcount == 1">
							<div class="flex justify-start space-x-2">
								<div class="text-yellow-600">
									<ExclamationCircleIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"></ExclamationCircleIcon>
								</div> 
								<div class="text-yellow-600">You have unsaved Receive Transaction/s. Click <a href="/receive_draft" class="underline">here</a> to view list.</div>
							</div>
						</div>
						<div class="p-2 pt-3">
							<div class="row">
								<div class="col-lg-12">
									<table class="w-full">
										<tr>
											<td class="px-1 " width="20%">
												<div class="flex justify-start ">
													<span class="text-xs text-gray-500 leading-none">MRIF NO.</span>
												</div>
											</td>
											<td class="px-1 " width="10%">
												<div class="flex justify-start" >
													<span class="text-xs text-gray-500 leading-none">DATE</span>
												</div>
											</td>
											<td class="px-1 "  width="18%">
												<div class="flex justify-start">
													<span class="text-xs text-gray-500 leading-none">DR NUMBER</span>
												</div>
											</td>
											<td class="px-1 " width="18%">
												<div class="flex justify-start" >
													<span class="text-xs text-gray-500 leading-none">PO NUMBER</span>
												</div>
											</td>
											<td class="px-1 " width="18%">
												<div class="flex justify-start" >
													<span class="text-xs text-gray-500 leading-none">SI/OR NUMBER</span>
												</div>
											</td>
											<td class="px-1 " width="20%">
												<div class="flex justify-start ">
													<span class="text-xs text-gray-500 leading-none">WAYBILL NO.</span>
												</div>
											</td>
											<td class="px-1 " width="5%">
												<div class="flex justify-end ">
													<span class="text-xs text-gray-500 leading-none">PCF</span>
												</div>
											</td>
										</tr>
										<tr>
											<td class="p-1 pt-0">
												<span class="text-lg uppercase font-bold text-gray-700 w-full leading-none leading-none">
													<input type="text" class="form-control border" disabled v-model="form.mrecf_no">
												</span>
											</td>
											<td class="p-1 pt-0">
												<div class="flex justify-start">
													<span class="text-base uppercase text-gray-700 w-full leading-none">
														<input type="date" class="form-control border" disabled v-model="form.receive_date">
													</span>
												</div>
											</td>
											<td class="p-1 pt-0">
												<div class="flex justify-start">
													<span class="text-base uppercase text-gray-700 w-full leading-none">
														<input type="text" class="form-control border " v-model="form.dr_no">
													</span>
												</div>
											</td>
											<td class="p-1 pt-0">
												<div class="flex justify-start">
													<span class="text-base uppercase text-gray-700 w-full leading-none">
														<input type="text" class="form-control border " v-model="form.po_no">
													</span>
												</div>
											</td>
											<td class="p-1 pt-0">
												<div class="flex justify-start">
													<span class="text-base uppercase text-gray-700 w-full leading-none">
														<input type="text" class="form-control border " v-model="form.si_or">
													</span>
												</div>
											</td>
											<td class="p-1 pt-0">
												<div class="flex justify-start">
													<span class="text-base uppercase text-gray-700 w-full leading-none">
														<input type="text" class="form-control border" v-model="form.waybill_no">
													</span>
												</div>
											</td>
											<td class="p-1 pt-0">
												<span class="flex justify-end text-green-600">
													<input type="checkbox" v-model='form.pcf' true-value="1" false-value="0">
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
								<button @click="onSave()" class="btn btn-sm btn-primary btn-rounded w-32">Next</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
    </navigation>
</template>
