<script setup>
	import navigation from '@/layouts/navigation.vue';
	import { CheckCircleIcon, ExclamationCircleIcon, ArrowUturnLeftIcon } from '@heroicons/vue/24/solid'
	import { onMounted, ref } from "vue"
    import { useRouter } from "vue-router"
    const router = useRouter()

    let form = ref({
        id:''
    })
	let error = ref('')
	let success = ref('')

	const props = defineProps({
        id:{
            type:String,
            default:''
        }
    })

	onMounted(async () =>{
        getSubcat()
    })

    const getSubcat = async () => {
        let response = await axios.get(`/api/edit_subcat/${props.id}`)
        form.value = response.data.subcategories
    }

	const onEdit = (id) => {

		const formData= new FormData()
		formData.append('subcat_code', form.value.subcat_code)
		formData.append('subcat_prefix', form.value.subcat_prefix)
		formData.append('subcat_name', form.value.subcat_name)
		axios.post(`/api/update_subcat/${form.value.id}`,formData).then(function () {
			// error.value=[]
			success.value='You have successfully updated data!'
			// setInterval(function(){ alert("Hello world"); }, 3000);
			document.getElementById("success").style.display="block"
			document.getElementById("error").style.display="none"
			setTimeout(() => {
				document.getElementById("success").style.display="none"
			}, 4000);
		}, function (err) {
			error.value=[]
			success.value=''
			error.value = err.response.data.message;
			document.getElementById("error").style.display="block"
			// document.getElementById("success").style.display="none"
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
					<div class="alert alert-info alert-top my-2">
						<div class="flex justify-start space-x-1">
							<div>
								<CheckCircleIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12"></CheckCircleIcon>
							</div> 
							<div class="py-1">
								<h6 class="font-bold m-0">Updated!</h6>
								<p class="text-sm m-0 text-gray-400"> {{ success }}</p>
							</div>
						</div>
					</div>
				</div>
				<div class="hide-animate"  v-if="error.length > 0" id="error">
					<div class="alert alert-danger alert-top my-2" >
						<div class="flex justify-start space-x-2">
							<div>
								<ExclamationCircleIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12"></ExclamationCircleIcon>
							</div> 
							<div class="py-1">
								<h6 class="font-bold m-0">Error!</h6>
								<p class="text-sm m-0 text-gray-400"> {{ error }}</p>
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
							<h6 class="m-0 pt-1 font-bold uppercase">Sub Category</h6>
						</div>
					</div>	
					<div class="pt-1">	
						<nav aria-label="breadcrumb" role="navigation">
							<ol class="breadcrumb adminx-page-breadcrumb">
								<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
								<li class="breadcrumb-item"><a href="/category">Item Category</a></li>
								<li class="breadcrumb-item active" aria-current="page">Update Sub Category</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>	

			<div class="row">
				<div class="col-md-12 col-lg-12 ">
					<div class="card">
						<div class="mt-2 mb-2 border-b">
							<h6>Update <span class="">Sub Category</span></h6>	
						</div>
						<div class="row">
							<div class="col-lg-3">
								<div class="form-group">
									<label class="form-label mb-0">Code</label>
									<input type="text" class="form-control border" v-model="form.subcat_code">
								</div>										
							</div>
							<div class="col-lg-6">
								<div class="form-group">
									<label class="form-label mb-0">Sub Category</label>
									<input type="text" class="form-control border" v-model="form.subcat_name">
								</div>										
							</div>
							<div class="col-lg-3">
								<div class="form-group">
									<label class="form-label mb-0">Prefix</label>
									<input type="text" class="form-control border" v-model="form.subcat_prefix">
								</div>										
							</div>
						</div>
						<div class="pt-3 mb-2 mt-2 border-t flex justify-end">
							<button @click="onEdit(form.id)"  type="submit" class="btn btn-sm btn-primary btn-rounded w-32">Update</button>
						</div>
					</div>
				</div>
			</div>
		</div>
    </navigation>
</template>
