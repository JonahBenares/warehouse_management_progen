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
        getPurpose()
    })

	const getPurpose = async () => {
        let response = await axios.get(`/api/edit_purpose/${props.id}`)
       form.value = response.data.purposes
    }

	const onEdit = (id) => {

	const formData= new FormData()
	formData.append('purpose_name', form.value.purpose_name)
		axios.post(`/api/update_purpose/${form.value.id}`,formData).then(function () {
			success.value='You have successfully updated the data!'
			document.getElementById("success").style.display="block"
			document.getElementById("error").style.display="none"
			setTimeout(() => {
				document.getElementById("success").style.display="none"
			}, 4000);
			
		}, function (err) {
			error.value = err.response.data.message;
			document.getElementById("error").style.display="block"
			document.getElementById("success").style.display="none"
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
				<div v-else id="success"></div>
				<div class="hide-animate" v-if="error" id="error">
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
							<h6 class="m-0 pt-1 font-bold uppercase">Purpose</h6>
						</div>
					</div>	
					<div class="pt-1">	
						<nav aria-label="breadcrumb" role="navigation">
							<ol class="breadcrumb adminx-page-breadcrumb">
								<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
								<li class="breadcrumb-item"><a href="/purpose">Purpose</a></li>
								<li class="breadcrumb-item active" aria-current="page">Update Purpose</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>	

			<div class="row">
				<div class="col-md-6 col-lg-6 ">
					<div class="card">
						<div class="mt-2 mb-2 border-b">
							<!-- <p class="text-success" v-if="success">{{ success }}</p>
							<p class="text-danger" v-else>{{ error }}</p> -->
							<h6>Update Purpose</h6>	
						</div>
						<div class="row">
							<div class="col-lg-12">
								<div class="form-group">
									<label class="form-label mb-0">Purpose</label>
									<input type="text" class="form-control border"  v-model="form.purpose_name">
								</div>										
							</div>
						</div>
						<div class="pt-2 mb-2 flex justify-end">
							<button type="submit" class="btn btn-sm btn-info btn-rounded w-32" @click="onEdit(form.id)">Update</button>
						</div>
					</div>
				</div>
			</div>
		</div>
    </navigation>
</template>
