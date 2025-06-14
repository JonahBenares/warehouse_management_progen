<script setup>
	import navigation from '@/layouts/navigation.vue';
	import { PencilSquareIcon, TrashIcon, PlusIcon, MagnifyingGlassIcon, ChevronLeftIcon, ChevronRightIcon, ArrowUturnLeftIcon } from '@heroicons/vue/24/solid'
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
        getUom()
    })

    const getUom = async () => {
        let response = await axios.get(`/api/edit_uom/${props.id}`)
       form.value = response.data.uoms
    }

		const onEdit = (id) => {

		const formData= new FormData()
		formData.append('unit_name', form.value.unit_name)
		axios.post(`/api/update_uom/${form.value.id}`,formData).then(function () {
				success.value='Successfully saved!'
				
			}, function (err) {
				error.value = err.response.data.message;
			});
		
	}


</script>

<template>
    <navigation>
        <div class="container-fluid">
			<!-- BreadCrumb -->
			<div class="card mb-3">	
				<div class="flex justify-between content-center">
					<div class="flex justify-start space-x-3 ">
						<div class="pt-1 ">
							<a href="/uom" class="btn btn-secondary btn-xs btn-rounded">
								<ArrowUturnLeftIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"></ArrowUturnLeftIcon>
							</a>
						</div>
						<!-- <div class="border-r"></div>	 -->
						<div>
							<h4 class="m-0">Unit of Measurement</h4>
						</div>
					</div>	
					<div class="pt-1">	
						<nav aria-label="breadcrumb" role="navigation">
							<ol class="breadcrumb adminx-page-breadcrumb">
								<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
								<li class="breadcrumb-item"><a href="/uom">Unit of Measurement</a></li>
								<li class="breadcrumb-item active" aria-current="page">Update UOM</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>	

			<div class="row">
				<div class="col-md-12 col-lg-12 ">
					<div class="card">
						<div class="mt-2 mb-2 border-b">
							<p class="text-success" v-if="success">{{ success }}</p>
							<p class="text-danger" v-else>{{ error }}</p>
							<h6>Update UOM</h6>	
						</div>
						<div class="row">
							<div class="col-lg-6">
								<div class="form-group">
									<label class="form-label">Unit of Measurement</label>
									<input type="text" class="form-control border" placeholder="Unit of Measurement" v-model="form.unit_name">
								</div>										
							</div>
						</div>
						<div class="pt-2 mb-2">
							<button type="submit" class="btn btn-sm btn-success btn-rounded w-32" @click="onEdit(form.id)">Update</button>
						</div>
					</div>
				</div>

			</div>


		</div>
    </navigation>
</template>
