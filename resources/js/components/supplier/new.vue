<script setup>
	import navigation from '@/layouts/navigation.vue';
	import { CheckCircleIcon, ExclamationCircleIcon, ArrowUturnLeftIcon } from '@heroicons/vue/24/solid'
	import{ onMounted, ref } from "vue"
    import { useRouter } from "vue-router"
    const router = useRouter()

	let form = ref([])
	let error=ref([])
	let success = ref('')

	onMounted(async () => {
		supplierform()
	})

	const supplierform = async () => {
		let response = await axios.get("/api/create_supplier");
		form.value = response.data;
	}

	const onSave = () => {
    
		const formData= new FormData()
		formData.append('supplier_name', form.value.supplier_name)
		formData.append('address', form.value.address)
		formData.append('email', form.value.email)
		formData.append('contact_person', form.value.contact_person)
		formData.append('contact_number', form.value.contact_number)
		formData.append('terms', form.value.terms)
		let is_active=document.getElementById('is_active');
			if(is_active.checked){
				var active=1
				
			}else{
				var active=0
				
			}
			formData.append('is_active',active)

		axios.post("/api/add_supplier",formData).then(function () {
			success.value='You have successfully added new supplier!'
			form.value=ref([])
			error.value=[]
			document.getElementById("success").style.display="block"
			setTimeout(() => {
				document.getElementById("success").style.display="none"
			}, 4000);
		}, function (err) {
			error.value=[]
			document.getElementById("error").style.display="block"
			if (err.response.data.errors.supplier_name) {
				error.value.push(err.response.data.errors.supplier_name[0])
			}
			if (err.response.data.errors.email) {
				error.value.push(err.response.data.errors.email[0])
			}
			if (err.response.data.errors.is_active) {
				error.value.push(err.response.data.errors.is_active[0])
			}
			setTimeout(() => {
				document.getElementById("error").style.display="none"
			}, 4000);
			
		});

	}

</script>

<template>
	<div>
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
				<div class="hide-animate"  v-if="error.length > 0" id="error">
					<div class="alert alert-danger alert-top my-2" >
						<div class="flex justify-start space-x-2">
							<div>
								<ExclamationCircleIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12"></ExclamationCircleIcon>
							</div> 
							<div class="py-1">
								<h6 class="font-bold m-0">Error!</h6>
								<p class="text-sm m-0 text-gray-400"  v-for="err in error"> {{ err }}</p>
							</div>
						</div>
					</div>
				</div>
				<div v-else id="error"></div>
			</div>
		</div>	
	</div>
    <navigation>
        <div class="container-fluid">
				<!-- BreadCrumb -->
				<div class="card mb-3">	
					<div class="flex justify-between content-center">
						<div class="flex justify-start space-x-3 ">
							<div class="">
								<a onclick="history.back()" class="btn btn-secondary btn-xs btn-rounded text-white">
									<ArrowUturnLeftIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></ArrowUturnLeftIcon>
								</a>
							</div>
							<div>
								<h6 class="m-0 pt-1 font-bold uppercase">Supplier</h6>
							</div>
						</div>	
						<div class="pt-1">	
							<nav aria-label="breadcrumb" role="navigation">
								<ol class="breadcrumb adminx-page-breadcrumb">
									<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
									<li class="breadcrumb-item"><a href="/supplier">Supplier</a></li>
									<li class="breadcrumb-item active" aria-current="page">Add New Supplier</li>
								</ol>
							</nav>
						</div>
					</div>
				</div>	

				<div class="row">
					<div class="col-md-12 col-lg-12 ">
						<div class="card">
							<div class="mt-2 mb-2 border-b">
								<!-- <p class="text-success" v-if="success">{{ success }}</p>
							<p class="text-danger" v-for="err in error" v-if="error.length > 0">{{ err }}</p> -->
								<h6>Add New Supplier</h6>	
							</div>
							<div class="row">
								<div class="col-lg-6">
									<div class="form-group">
										<label class="form-label m-0">Supplier Name</label>
										<textarea class="form-control border" rows="2" v-model='form.supplier_name'></textarea>
									</div>										
								</div>
								<div class="col-lg-6">
									<div class="form-group">
										<label class="form-label m-0">Address</label>
										<textarea class="form-control border" rows="2" v-model='form.address'></textarea>
									</div>										
								</div>
							</div>
							<div class="row">
								<div class="col-lg-6">
									<div class="form-group">
										<label class="form-label m-0">Email</label>
										<input type="email" class="form-control border" v-model='form.email'>
									</div>										
								</div>
								<div class="col-lg-3">
									<div class="form-group">
										<label class="form-label m-0">Contact Number</label>
										<input type="text" class="form-control border" v-model='form.contact_number'>
									</div>										
								</div>
								<div class="col-lg-3">
									<div class="form-group">
										<label class="form-label m-0">Contact Person</label>
										<input type="text" class="form-control border" v-model='form.contact_person'>
									</div>										
								</div>
							</div>
							<div class="row">
								<div class="col-lg-9">
									<div class="form-group">
										<label class="form-label m-0">Terms</label>
										<textarea class="form-control border" rows="2" v-model='form.terms'></textarea>
									</div>										
								</div>
								<div class="col-lg-3">
									<div class="form-group">
										<label class="form-label m-0">Status</label>
										<div class="form-check mt-2">
											<label class="form-check-label">
											<input class="form-check-input" type="checkbox" id='is_active' v-model='form.is_active'> Active
											</label>
										</div>
									</div>										
								</div>
							</div>
							<div class="pt-2 mb-2 flex justify-end border-t mt-2">
								<button @click="onSave()"  class="btn btn-sm btn-primary btn-rounded w-32">Submit</button>
							</div>
						</div>
					</div>
				</div>
			</div>
    </navigation>
</template>
