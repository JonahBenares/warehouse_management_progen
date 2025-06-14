<script setup>
	import navigation from '@/layouts/navigation.vue';
	import { ArrowUturnLeftIcon, Bars3Icon, XMarkIcon } from '@heroicons/vue/24/solid'
    import { PhotoIcon, } from '@heroicons/vue/24/outline'
	import{ onMounted, ref, watch } from "vue"
    import { routerViewLocationKey, useRouter } from "vue-router"

    const router = useRouter()
    let error_image = ref('');
    let items = ref([])
    let item_description = ref('');
    let quantity = ref('');
    let uom = ref('');
    let type = ref('');
    let remarks = ref('');
    let display_flag = ref('');
    let error =ref([])
    let success = ref('')
    let imageFile=ref('');
    let imageUrl=ref([]);
    let imageDisplay=ref([]);

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
            if(items.value.length > 0){
                document.getElementById("SubmitButton").style.display = "block";
            }else{
                document.getElementById("SubmitButton").style.display = "none";
            }
    }

    const upload_image = (event) => {

        let file = event.target.files[0];
        if(event.target.files.length===0){
            imageFile.value='';
            imageUrl.value='';
            return;
        }else if(file['size'] < 2111775){
            imageFile.value = event.target.files[0];
            error_image.value=''
        }else{
            error_image.value='File size can not be bigger than 2 MB'
            imageUrl.value='';
        }
    }   

    // watch(imageFile, (imageFile) => {
    //     if(!(imageFile instanceof File)){
    //         return;
    //     }
    //             let fileReader = new FileReader();
    //             fileReader.readAsDataURL(imageFile)
    //             fileReader.addEventListener("load", () => {
    //             imageUrl.value=fileReader.result 
    //         })
    //     })

    const addItem = () => {
    if(item_description.value == ''){
        alert('Item description must not be empty');
    }else if(quantity.value == ''){
        alert('Quantity must not be empty');
    }else if(uom.value == ''){
        alert('Uom must not be empty');
    }else if(uom.value == ''){
        alert('Uom must not be empty');
    }else if(type.value == ''){
        alert('Type must not be empty');
    }else{
            const formData=new FormData()
			formData.append('head_id', props.id)
			formData.append('item_description', item_description.value)
			formData.append('quantity', quantity.value)
			formData.append('uom', uom.value)
			formData.append('type', type.value)
			formData.append('remarks', remarks.value)
			formData.append('display_flag', display_flag.value)
			formData.append('imagename', imageFile.value)
            // axios.post("/api/insert_item/",formData).then(function () {
            axios.post("/api/insert_item",formData,{
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }).then(function () {
                // for(var i=0;i<items.value.length;i++){
                // itemid.value[i] = response.data
                // }
                GetGatepassHead()
                item_description.value=''
                quantity.value=''
                uom.value=''
                type.value=''
                remarks.value=''
                display_flag.value=''
                imageFile.value = ''
                document.getElementById("imageLoc").value = ''
            });
        }
        // if(display_flag.value == 1){
        //     var flag = 'Yes'
        // }else{
        //     var flag = 'No'
        // }
        // var item_id = itemid.value

        // items.value.push({
        //     item_desc:item_description.value,
        //     qty:quantity.value,
        //     uom:uom.value,
        //     type:type.value,
        //     remarks:remarks.value,
        //     display_flag:flag,
        //     item_id:itemid,
        //     // imageFilename:imageFile.value,
        //     // imageDisplay:imageUrl.value
        //     }
        // );
            
       
        // item_description.value=''
        // quantity.value=''
        // uom.value=''
        // type.value=''
        // remarks.value=''
        // display_flag.value=''
        // imageFile.value = ''
        // document.getElementById("imageLoc").value = ''
        // document.getElementById("SubmitButton").style.display = "block";
    }

    const removeItem= (id) =>{
		if(confirm("Do you really want to delete this row?")){
			// items.value.splice(index,1)
            axios.get(`/api/remove_item/${id}`).then(function () {
                GetGatepassHead()
            });
		}
	}
    

    const SaveNewGatepass = () => {
        if(confirm("Are you sure you want to save this Gatepass?")){
            // const formItems= new FormData()
            // formItems.append('gatepass_items', JSON.stringify(items.value))
                axios.post(`/api/save_gatepass_items/${props.id}`).then(function (response) {
                    // console.log(response.data)
                    error.value=[]
                    success.value='Successfully saved!'
                    router.push(`/gatepass/print/${props.id}`)
                    }).catch(function(err){
                        success.value=''
                    });
        }
    }

    const cancelTransaction = (id) => {
			if(confirm("Are you sure you want to cancel transaction?")){
				
				axios.get(`/api/cancel_gatepass/${id}`).then(function () {
					router.push('/gatepass')
				});
			}
		}

// watch(imageFile, (imageFile) => {
// 	if(!(imageFile instanceof File)){
// 		return;
// 	}
//     let fileReader = new FileReader();
//     fileReader.readAsDataURL(imageFile)
//     fileReader.addEventListener("load", () => {
//         imageUrl.value=fileReader.result 
//     })

watch(imageFile, (imageFile) => {
	if(!(imageFile instanceof File)){
		return;
	}
    let fileReader = new FileReader();
    fileReader.readAsDataURL(imageFile)
    fileReader.addEventListener("load", () => {
        imageUrl.value=fileReader.result 
    })

    // var image_class=document.getElementsByClassName('imagedisplay');
    // var image_class=document.getElementsByClassName('image_display');
    // for(var i=0;i<image_class.length;i++){
    //     var fileReaders = new FileReader();
    //     fileReaders.readAsDataURL(imageFile)
    //     fileReaders.addEventListener("load", () => {
    //         imageDisplay.value[i]=fileReaders.result;
    //     })
    // }
})
    //         imageDisplay.value[i]=fileReaders.result;  
    //     })
    // }
// })
	
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
                                            <tr>
                                                <td colspan="3" class="pt-3"></td>
                                            </tr>
                                            <tr>
                                                <td class="" >
                                                    <div class="flex justify-start">
                                                        <span class="text-sm uppercase font-bold text-gray-600 w-full leading-none">
                                                            {{ form.remarks }}
                                                        </span>
                                                    </div>
                                                    <div class="flex justify-start" >
                                                        <span class="text-xs text-gray-500 leading-none uppercase">Remarks</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
								</div>
							</div>
                            <br>
                            <div class="row px-4">
                                <div class="col-lg-5 pr-0 pl-2">
                                    <div class="">
                                        <span class="text-xs">Item Description</span>
                                        <!-- <select type="" class="form-control border w-full py-2">
                                            <option value=""></option>
                                        </select> -->
                                        <input type="text" class="form-control border w-full" v-model="item_description">
                                    </div>
                                </div>
                                <div class="col-lg-2 pr-0 pl-2">
                                    <div class="">
                                        <span class="text-xs">QTY</span>
                                        <input type="text" class="form-control border w-full" v-model="quantity" min="0" onkeypress="return (event.charCode !=8 && event.charCode ==0 || (event.charCode >= 48 && event.charCode <= 57))">
                                    </div>
                                </div>
                                <div class="col-lg-2 pr-0 pl-2">
                                    <div class="">
                                        <span class="text-xs">UOM</span>
                                        <input type="text" class="form-control border w-full" v-model="uom">
                                    </div>
                                </div>
                                <div class="col-lg-3 pr-0 pl-2">
                                    <div class="">
                                        <span class="text-xs">Type</span>
                                        <select class="form-control border w-full py-2" v-model="type">
                                            <option value="Returnable">Returnable</option>
                                            <option value="Non-Returnable">Non-Returnable</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row px-4">
                                <div class="col-lg-5 pr-0 pl-2">
                                    <div class="">
                                        <span class="text-xs">Remarks</span>
                                        <input type="text" class="form-control border w-full py-2" v-model="remarks">
                                    </div>
                                </div>
                                <div class="col-lg-4 pr-0 pl-2">
                                    <div class="">
                                        <span class="text-xs">Image</span>
                                        <input type="file" accept="image/*" id="imageLoc" @change="upload_image" class="form-control border w-full pt-2 pb-1">
                                        <!-- <img :src="imageUrl" /> -->
                                        <!-- <div class="avatar img-fluid img-circle" style="margin-top:10px">
											<img :src="imageUrl"/>
										</div> -->
                                        <!-- <a href="" class="text-xs">asdasd</a> -->
                                    </div>
                                </div>
                                <div class="col-lg-2 pr-0 pl-2">
                                    <div class="flex justify-center mt-8 space-x-2">
                                        <span class="text-xs">Display Image in Print</span>
                                        <div class="w-5">
                                            <input class="form-control border  py-3" type="checkbox" v-model='display_flag' true-value="1" false-value="0">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-lg-1 pr-0 pl-2">
                                    <div class="">
                                        <span class="text-xs"><br></span>
                                        <button class="btn btn-sm bg-yellow-500 w-full text-white" id="additem_" @click="addItem()">Add Item</button>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row px-6">
                                <table class="table table-actions table-bordered table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th scope="col" width="30%">Item Description</th>
                                            <th scope="col" width="5%">QTY</th>
                                            <th scope="col" width="5%">UOM</th>
                                            <th scope="col" width="15%">Type</th>
                                            <th scope="col" width="22%">Remarks</th>
                                            <th scope="col" width="10%">Photo</th>
                                            <th scope="col" width="15%" class="font-xxs">Display Photo in Print</th>
                                            <th scope="col" width="1%" align="center" class="pr-2">
                                                <div class="flex justify-center">
                                                    <Bars3Icon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"></Bars3Icon>
                                                </div>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="i in items">
                                            <td>{{ i.item_description }}</td>
                                            <td>{{ parseFloat(i.quantity).toFixed(2) }}</td>
                                            <td>{{ i.uom }}</td>
                                            <td>{{ i.type }}</td>
                                            <td class="p-0">
                                                <span class="p-1">{{ i.remarks }}</span>
                                                <!-- <textarea name="" id="" class="form-control" rows="1"></textarea> -->
                                            </td>
                                            <!-- <input type="text" class="form-control border w-full" v-model="i.imageFilename"> -->
                                            <td>
                                                <div class="flex justify-center">
                                                    <!-- <img class="w-10 bg-green-100" :id="'imagedisplay'+index" :src="imageDisplay[index]"/> -->
                                                    <!-- <img class="w-10 bg-green-100" id="imagedisplay" :src="imageDisplay"/> -->
                                                    <img :src="'/gatepass_items/'+i.image" id="img" v-if="i.image!=null"/>
                                                </div>
                                            </td>
                                            <td>{{ (i.display_flag == 1) ? 'Yes' : 'No' }}</td>
                                            <td align="center" class="px-0">
                                                <!-- <input type="text" class="image_display" :value="index">  -->
                                                <div class="flex justify-center w-full">
                                                    <button class="btn btn-xs btn-rounded btn-danger text-xs p-1" @click="removeItem(i.id)">
                                                        <XMarkIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"></XMarkIcon> 
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
						</div> 
						<div class="pt-3 mb-2 mt-2 border-t flex justify-end space-x-10">
							<div class="flex justify-between space-x-1">
                                <button @click="cancelTransaction(form.id)"  class="btn btn-sm btn-danger" >Cancel Transaction</button>
								<a @click="SaveNewGatepass()" style="display:none" id = "SubmitButton" type="submit" class="btn btn-sm btn-primary  text-white w-32" disabled>Save & Print</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
    </navigation>
</template>
