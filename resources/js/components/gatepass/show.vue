<script setup>
	import navigation from '@/layouts/navigation.vue';
	import { ArrowUturnLeftIcon, Bars3Icon, XMarkIcon } from '@heroicons/vue/24/solid'
	import{ onMounted, ref, watch } from "vue"
    import { useRouter } from "vue-router"

    const router = useRouter()
    let error_image = ref('');
    let items = ref([])
    let error =ref([])
    let success = ref('')

    let form = ref({
        id:''
    })

    const props = defineProps({
        id:{
            type:String,
            default:''
        }
    })

    onMounted(async () => {
	    GetGatepassHead()
    })

    const GetGatepassHead = async () => {
        let response = await axios.get(`/api/gatepass_details/${props.id}`)
        form.value=response.data.gp_head
        items.value=response.data.gp_items
    }

    const UpdateRemarksHead = () => {
            const formData=new FormData()
            var remarks_head = document.getElementById("head_remarks").value;
			formData.append('head_id', form.value.id)
			formData.append('remarks_head', remarks_head)
            axios.post("/api/update_remarks_head/",formData).then(function (response) {
                GetGatepassHead()
            });
    }

    const PrintGatepass = (id) => {
		router.push('/gatepass/print/'+id)
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
									<div class="px-3">
                                        <table class="w-full table-bordesred mt-3 mb-3">
                                            <tr>
                                                <td class="pr-1" width="20%">
                                                    <div class="flex justify-start">
                                                        <span class="text-lg uppercase font-bold text-gray-600 w-full leading-none">
                                                           {{ form.gatepass_no }}
                                                        </span>
                                                    </div>
                                                    <div class="flex justify-start ">
                                                        <span class="text-xs text-gray-500 leading-none pt-1">MGP NO.</span>
                                                    </div>
                                                </td>
                                                <td class="px-1" width="10%" colspan="2">
                                                    <div class="flex justify-start">
                                                        <span class="text-lg uppercase font-bold text-gray-600 leading-none">{{ form.to_company }}</span>
                                                    </div>
                                                    <div class="flex justify-start" >
                                                        <span class="text-xs text-gray-500 leading-none pt-1">COMPANY</span>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" class="pt-3"></td>
                                            </tr>
                                            <tr>
                                                <td class="" >
                                                    <div class="flex justify-start">
                                                        <span class="text-sm uppercase font-bold text-gray-600 w-full leading-none">
                                                            {{ form.date_issued }}
                                                        </span>
                                                    </div>
                                                    <div class="flex justify-start" >
                                                        <span class="text-xs text-gray-500 leading-none uppercase">Date Issued</span>
                                                    </div>
                                                </td>
                                                <td class="px-1 "  width="30%">
                                                    <div class="flex justify-start">
                                                        <span class="text-sm uppercase font-bold text-gray-600 w-full leading-none">
                                                            {{ form.destination }}
                                                        </span>
                                                    </div>
                                                    <div class="flex justify-start">
                                                        <span class="text-xs text-gray-500 leading-none uppercase">Destination</span>
                                                    </div>
                                                </td>
                                                <td class="px-1 " width="30%">
                                                    <div class="flex justify-start">
                                                        <span class="text-sm uppercase font-bold text-gray-600 w-full leading-none">
                                                            {{ form.vehicle_no }}
                                                        </span>
                                                    </div>
                                                    <div class="flex justify-start" >
                                                        <span class="text-xs text-gray-500 leading-none uppercase">Vehicle No.</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="px-3">
                                        <span class="text-xs text-gray-500 leading-none pt-1">REMARKS</span>
                                        <textarea id="head_remarks" class="form-control border" cols="30" rows="2" @change="UpdateRemarksHead()">{{ form.remarks }}</textarea>
                                    </div>
								</div>
							</div>
                            <br>
                            <div class="row px-6">
                                <table class="table table-actions table-bordesred table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th scope="col" width="30%">Item Description</th>
                                            <th scope="col" width="10%">QTY</th>
                                            <th scope="col" width="10%">UOM</th>
                                            <th scope="col" width="15%">Type</th>
                                            <th scope="col" width="30%">Remarks</th>
                                            <th scope="col" width="30%">Photo</th>
                                            <th scope="col" width="30%">Display Image in Print</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="i in items">
                                            <td>{{ i.item_description }}</td>
                                            <td>{{ parseFloat(i.quantity).toFixed(2) }}</td>
                                            <td>{{ i.uom }}</td>
                                            <td>{{ i.type }}</td>
                                            <td>{{ i.remarks }}</td>
                                            <td>
                                                <img :src="'/gatepass_items/'+i.image" v-if="i.image!=null"/>
                                            </td>
                                            <td>{{ (i.display_flag == '1') ? 'Yes' : 'No' }}</td>
                                            <!-- <input type="text" class="form-control border w-full" v-model="i.imagefile"> -->
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
						</div> 
						<div class="pt-3 mb-2 mt-2 border-t flex justify-end space-x-10">
							<div class="flex justify-between space-x-1">
								<a @click="PrintGatepass(form.id)" class="btn btn-sm hover:bg-blue-600 bg-blue-500 text-white w-60">Print</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
    </navigation>
</template>
