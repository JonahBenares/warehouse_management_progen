<script setup>
	import navigation from '@/layouts/navigation.vue';
	import { CheckCircleIcon,TrashIcon, ExclamationCircleIcon, PlusIcon,XMarkIcon,  ArrowUturnLeftIcon } from '@heroicons/vue/24/solid'
	import { onMounted, ref } from "vue"
    import { useRouter } from "vue-router"
    const router = useRouter()

	let form = ref({
        id:''
    })
	let subcat = ref([])
	let error = ref([])
	let error_modal = ref([])
	let success = ref('')
	let listSubCat = ref([])
	const showModal = ref(false)
    const hideModal = ref(true)
	const removeBttn = ref(true)

	const props = defineProps({
        id:{
            type:String,
            default:''
        }
    })

	onMounted(async () =>{
        getCategory()
    })

	const getCategory = async () => {
        let response = await axios.get(`/api/edit_category/${props.id}`)
        form.value = response.data.categories

    }

	const openModel = () => {
        showModal.value = !showModal.value
    }

    const closeModal = () => {
        showModal.value = !hideModal.value
    }

	const addSubCat = (subcat) =>{
		if(subcat.subcat_code!=undefined && subcat.subcat_name!=undefined && subcat.subcat_prefix!=undefined){
			const subcat_added = {
				subcat_code : subcat.subcat_code,
				subcat_name : subcat.subcat_name,
				subcat_prefix : subcat.subcat_prefix,
			}
			listSubCat.value.push(subcat_added)
			subcat.subcat_code = ""
			subcat.subcat_name = ""
			subcat.subcat_prefix = ""
			closeModal();
		}else{
			document.getElementById("error_modal").style.display="block"
			success.value=''
			error_modal.value=[]
			var err = ['The subcat code field is required.','The subcat prefix field is required.','The sub category name field is required.']
			if (subcat.subcat_code==undefined) {
				error_modal.value.push(err[0])
			}
			if (subcat.subcat_prefix==undefined) {
				error_modal.value.push(err[1])
			}
			if (subcat.subcat_name==undefined) {
				error_modal.value.push(err[2])
			}
			setTimeout(() => {
				document.getElementById("error_modal").style.display="none"
			}, 4000);
		}
	}

		const removeItem = (i) =>{
        listSubCat.value.splice(i,1)
		
    }
	const onEdit = (id) => {
		
		const formData= new FormData()

		formData.append('subcategories', JSON.stringify(listSubCat.value))
		formData.append('category_code', form.value.category_code)
		formData.append('prefix', form.value.prefix)
		formData.append('category_name', form.value.category_name)
		
		//console.log(formData);
		axios.post(`/api/update_category/${form.value.id}`,formData).then(function () {
			// error.value=[]
			success.value='You have successfully updated the data!'
			removeBttn.value=!removeBttn
			document.getElementById("success").style.display="block"
			setTimeout(() => {
				document.getElementById("success").style.display="none"
			}, 4000);
		}, function (err) {
			error.value=[]
			success.value=''
			document.getElementById("error").style.display="block"
			if (err.response.data.errors.category_code) {
				error.value.push(err.response.data.errors.category_code[0])
			}
			if (err.response.data.errors.category_name) {
				error.value.push(err.response.data.errors.category_name[0])
			}
			if (err.response.data.errors.prefix) {
				error.value.push(err.response.data.errors.prefix[0])
			}
			setTimeout(() => {
				document.getElementById("error").style.display="none"
			}, 4000);
		});
	}
</script>
<style>
  .invisible {
	visibility: hidden;
  }
</style>
<template>
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
			<div class="hide-animate"  v-if="error_modal.length > 0" id="error_modal">
				<div class="alert alert-danger alert-top my-2" >
					<div class="flex justify-start space-x-2">
						<div>
							<ExclamationCircleIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12"></ExclamationCircleIcon>
						</div> 
						<div class="py-1">
							<h6 class="font-bold m-0">Error!</h6>
							<p class="text-sm m-0 text-gray-400" v-for="errs in error_modal"> {{ errs }}</p>
						</div>
					</div>
				</div>
			</div>
			<div v-else id="error_modal"></div>
		</div>
	</div>
    <navigation>
        <div class="container-fluid" id="top" >
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
							<h6 class="m-0 pt-1 font-bold uppercase">Item Category</h6>
						</div>
					</div>	
					<div class="pt-1">	
						<nav aria-label="breadcrumb" role="navigation">
							<ol class="breadcrumb adminx-page-breadcrumb">
								<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
								<li class="breadcrumb-item"><a href="/category">Item Category</a></li>
								<li class="breadcrumb-item active" aria-current="page">Update Item Category</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>	

			<div class="row">
				<div class="col-md-12 col-lg-12 ">
					<div class="card">
						<div class="mt-2 mb-2 border-b">
							<h6>Update <span>Item Category</span></h6>	
						</div>
						<!-- <Transition
							enter-active-class="transition ease-out duration-250"
							enter-from-class="opacity-0 h-1/2"
							enter-to-class="opacity-100 h-full"
							leave-active-class="transition ease-in duration-100"
							leave-from-class="opacity-100 h-full"
							leave-to-class="opacity-0 h-1/2"
						>
						<div class="alert alert-success my-2 show-animate" v-if="success">
							<div class="flex justify-start space-x-2">
								<div>
									<CheckCircleIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"></CheckCircleIcon>
								</div> 
								<div> {{ success }}</div>
							</div>
						</div>
						</Transition>
						<div class="alert alert-danger my-2 show-animate" v-for="err in error" v-if="error.length > 0">
							<div class="flex justify-start space-x-2">
								<div>
									<ExclamationCircleIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"></ExclamationCircleIcon>
								</div> 
								<div> {{ err }} </div>
							</div>
						</div> -->
						<div class="row">
							<div class="col-lg-3">
								<div class="form-group">
									<label class="form-label mb-0">Code</label>
									<input type="text" class="form-control border" placeholder="Code" v-model="form.category_code">
								</div>										
							</div>
							<div class="col-lg-6">
								<div class="form-group">
									<label class="form-label mb-0">Item Category</label>
									<input type="text" class="form-control border" placeholder="Item Category" v-model="form.category_name">
								</div>										
							</div>
							<div class="col-lg-3">
								<div class="form-group">
									<label class="form-label mb-0">Prefix</label>
									<input type="text" class="form-control border" placeholder="Prefix" v-model="form.prefix">
								</div>										
							</div>
						</div>
						<table class="table table-bordsered table-hover mb-0 border">
							<thead>
								<tr>
									<th scope="col">SubCat Code</th>
									<th scope="col">SubCat Name</th>
									<th scope="col">SubCat Prefix</th>
									<th scope="col" class="p-1" width="1%">
										<button  @click="openModel()" class="btn btn-xs btn-primary btn-rounded btn__open--modal">
											<PlusIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></PlusIcon>
										</button>
									</th>
								</tr>
							</thead>
							<tbody>
								<tr  v-for="(sc, x) in form.sub_categories">
									<td>{{ sc.subcat_code }}</td>
									<td>{{ sc.subcat_name }}</td>
									<td>{{ sc.subcat_prefix }}</td>
									<td class="space-x-1">
										
									</td>
								</tr>
								<tr  v-for="(sub, i) in listSubCat">
									<td>{{ sub.subcat_code }}</td>
									<td>{{ sub.subcat_name }}</td>
									<td>{{ sub.subcat_prefix }}</td>
									<td class="p-1">
										<button   :class="{ invisible: !removeBttn }" class="btn btn-xs btn-danger btn-rounded " @click="removeItem(i)">
											<TrashIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></TrashIcon>
										</button>
									</td>
								</tr>
							</tbody>
						</table>
						<div class="pt-3 mb-2 flex justify-end space-x-10 ">
							<div class="flex justify-between space-x-1">
								<button class="btn btn-xs btn-primary px-5 btn-rounded btn-block" href="#top"  @click="onEdit(form.id)">Update
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		 <!--==================== add modal items ====================-->

		 <div class="modal main__modal " :class="{ show:showModal }">
			<div class="absolute w-full h-full top-0" @click="closeModal"></div>
            <div class="modal__content  w-5/12">
				<div class="border-b pb-2 mb-2">
					<span class="modal__close btn__close--modal pt-2" @click="closeModal">
						<XMarkIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mt-1"></XMarkIcon>
					</span>
					<h6 class="modal__title m-0">Add Sub Category</h6>
				</div>
                <div class="">
					<div class="row">
						<div class="col-lg-6">
							<div class="form-group">
								<label class="form-label mb-0">Code</label>
								<input type="text" class="form-control border" required v-model="subcat.subcat_code">
							</div>										
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label class="form-label mb-0">Prefix</label>
								<input type="text" class="form-control border" required v-model="subcat.subcat_prefix">
							</div>										
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12">
							<div class="form-group">
								<label class="form-label mb-0">Item Category</label>
								<input type="text" class="form-control border" required v-model="subcat.subcat_name">
							</div>										
						</div>
					</div>
                </div>
                <div class="border-t flex justify-end pt-3 mt-2">
                    <button class="btn btn-danger btn-sm mr-2 btn__close--modal" @click="closeModal">
                        Cancel
                    </button>
                    <button class="btn btn-primary btn-sm px-4" @click="addSubCat(subcat)">Save</button>
                </div>
            </div>
        </div>
		 <!-- <div class="modal main__modal " :class="{ show:showModal }">
            <div class="modal__content">
                <span class="modal__close btn__close--modal" @click="closeModal">×</span>
                <h3 class="modal__title">Add Sub Category</h3>
                <hr><br>
                <div class="modal__items">
					
							<div class="col-lg-3">
								<div class="form-group">
									<input type="text" class="form-control border" placeholder="Code" v-model="subcat.subcat_code">
								</div>										
							</div>
							<div class="col-lg-6">
								<div class="form-group">
									<input type="text" class="form-control border" placeholder="Item Category" v-model="subcat.subcat_name">
								</div>										
							</div>
							<div class="col-lg-3">
								<div class="form-group">
									<input type="text" class="form-control border" placeholder="Prefix" v-model="subcat.subcat_prefix">
								</div>										
							</div>
						
                </div>
                <br><hr>
                <div class="model__footer">
                    <button class="btn btn-light mr-2 btn__close--modal" @click="closeModal">
                        Cancel
                    </button>
                    <button class="btn btn-light" @click="addSubCat(subcat)">Save</button> 
					
                </div>
            </div>
        </div> -->

    </navigation>
</template>
