<script setup>
	import navigation from '@/layouts/navigation.vue';
	import { CheckCircleIcon, ExclamationCircleIcon, ArrowUturnLeftIcon } from '@heroicons/vue/24/solid'
	import axios from 'axios';
	import{ onMounted, ref, watch } from "vue"
    import { useRouter } from "vue-router"
    const router = useRouter()
	let form=ref([])
	let error = ref('')
	let error_begbal = ref('')
	let error_inventory = ref('')
	let error_currentinventory = ref('')
	let success = ref('')
	let begbalFile=ref("");
	let begbalUrl=ref("");
	let inventoryFile=ref("");
	let inventoryUrl=ref("");
	let currentinventoryFile=ref("");
	let currentinventoryUrl=ref("");
	let check_inv=ref(true)
	let check_begbal=ref(true)
	let check_curinv=ref(true)
	onMounted(async () => {
	})

	const upload_begbal = (event) => {
		const btn_begbal = document.getElementById("btn_begbal");
		btn_begbal.disabled = false;
		let file = event.target.files[0];
		if(event.target.files.length===0){
			begbalFile.value='';
			begbalUrl.value='';
			return;
		}else if(file['size'] < 2111775){
			begbalFile.value = event.target.files[0];
			error_begbal.value=''
		}else{
			begbalUrl.value='';
			error_begbal.value='File size can not be bigger than 2 MB'
			document.getElementById("error_begbal").style.display="block"
			setTimeout(() => {
				document.getElementById("error_begbal").style.display="none"
			}, 4000);
		}
	}
	
	watch(begbalFile, (begbalFile) => {
		if(!(begbalFile instanceof File)){
			return;
		}
		let fileReader = new FileReader();
		fileReader.readAsDataURL(begbalFile)
		fileReader.addEventListener("load", () => {
			begbalUrl.value=fileReader.result
		})
	})

	const onSave = () => {
            const formData= new FormData()
			formData.append('upload_begbal',begbalFile.value)
			axios.post("/api/add_begbal",formData).then(function (response) {
				//console.log(response.data);
				success.value='You have successfully imported new begbal data!'
				begbalFile.value=''
				document.getElementById("success").style.display="block"
				document.getElementById("error").style.display="none"
				const btn_begbal = document.getElementById("btn_begbal");
				btn_begbal.disabled = true;
				setTimeout(() => {
					document.getElementById("success").style.display="none"
					btn_begbal.disabled = false;
					document.getElementById("upload_begbal").value='';
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

	const upload_inventory = (event) => {
		const btn_inv = document.getElementById("btn_inv");
		btn_inv.disabled = false;
		let file = event.target.files[0];
		if(event.target.files.length===0){
			inventoryFile.value='';
			inventoryUrl.value='';
			return;
		}else if(file['size'] < 2111775){
			inventoryFile.value = event.target.files[0];
			error_inventory.value=''
		}else{
			inventoryUrl.value='';
			error_inventory.value='File size can not be bigger than 2 MB'
			document.getElementById("error_inventory").style.display="block"
			setTimeout(() => {
				document.getElementById("error_inventory").style.display="none"
			}, 4000);
		}
	}
	
	watch(inventoryFile, (inventoryFile) => {
		if(!(inventoryFile instanceof File)){
			return;
		}
		let fileReader = new FileReader();
		fileReader.readAsDataURL(inventoryFile)
		fileReader.addEventListener("load", () => {
			inventoryUrl.value=fileReader.result
		})
	})

	const onSaveInv = () => {
            const formData= new FormData()
			formData.append('upload_inventory',inventoryFile.value)
			axios.post("/api/add_inventory",formData).then(function (response) {
				//console.log(response.data);
				success.value='You have successfully imported new inventory data!'
				inventoryFile.value=''
				document.getElementById("success").style.display="block"
				document.getElementById("error").style.display="none"
				const btn_inv = document.getElementById("btn_inv");
				btn_inv.disabled = true;
				setTimeout(() => {
					document.getElementById("success").style.display="none"
					btn_inv.disabled = false;
					document.getElementById("upload_inventory").value='';
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

	const upload_currentinventory = (event) => {
		const btn_current = document.getElementById("btn_current");
		btn_current.disabled = false;
		let file = event.target.files[0];
		if(event.target.files.length===0){
			currentinventoryFile.value='';
			currentinventoryUrl.value='';
			return;
		}else if(file['size'] < 2111775){
			currentinventoryFile.value = event.target.files[0];
			error_currentinventory.value=''
		}else{
			currentinventoryUrl.value='';
			error_currentinventory.value='File size can not be bigger than 2 MB'
			document.getElementById("error_currentinventory").style.display="block"
			setTimeout(() => {
				document.getElementById("error_currentinventory").style.display="none"
			}, 4000);
		}
	}
	
	watch(currentinventoryFile, (currentinventoryFile) => {
		if(!(currentinventoryFile instanceof File)){
			return;
		}
		let fileReader = new FileReader();
		fileReader.readAsDataURL(currentinventoryFile)
		fileReader.addEventListener("load", () => {
			currentinventoryUrl.value=fileReader.result
		})
	})

	const onSaveCurrent = () => {
            const formData= new FormData()
			formData.append('upload_currentinventory',currentinventoryFile.value)
			axios.post("/api/add_currentinventory",formData).then(function (response) {
				//console.log(response.data);
				success.value='You have successfully imported new inventory data!'
				currentinventoryFile.value=''
				document.getElementById("success").style.display="block"
				document.getElementById("error").style.display="none"
				const btn_current = document.getElementById("btn_current");
				btn_current.disabled = true;
				setTimeout(() => {
					document.getElementById("success").style.display="none"
					btn_current.disabled = false;
					document.getElementById("upload_currentinventory").value='';
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
				<div class="hide-animate" v-if="error_begbal" id="error_begbal">
					<div class="alert alert-danger alert-top my-2" >
						<div class="flex justify-start space-x-2">
							<div>
								<ExclamationCircleIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12"></ExclamationCircleIcon>
							</div> 
							<div class="py-1">
								<h6 class="font-bold m-0">Error!</h6>
								<p class="text-sm m-0 text-gray-400"> {{ error_begbal }}</p>
							</div>
						</div>
					</div>
				</div>
				<div v-else id="error_begbal"></div>
				<div class="hide-animate" v-if="error_inventory" id="error_inventory">
					<div class="alert alert-danger alert-top my-2" >
						<div class="flex justify-start space-x-2">
							<div>
								<ExclamationCircleIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12"></ExclamationCircleIcon>
							</div> 
							<div class="py-1">
								<h6 class="font-bold m-0">Error!</h6>
								<p class="text-sm m-0 text-gray-400"> {{ error_inventory }}</p>
							</div>
						</div>
					</div>
				</div>
				<div v-else id="error_currentinventory"></div>
				<div class="hide-animate" v-if="error_currentinventory" id="error_currentinventory">
					<div class="alert alert-danger alert-top my-2" >
						<div class="flex justify-start space-x-2">
							<div>
								<ExclamationCircleIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12"></ExclamationCircleIcon>
							</div> 
							<div class="py-1">
								<h6 class="font-bold m-0">Error!</h6>
								<p class="text-sm m-0 text-gray-400"> {{ error_currentinventory }}</p>
							</div>
						</div>
					</div>
				</div>
				<div v-else id="error_currentinventory"></div>
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
							<h6 class="m-0 pt-1 font-bold uppercase">Import Items</h6>
						</div>
					</div>	
					<div class="pt-1">	
						<nav aria-label="breadcrumb" role="navigation">
							<ol class="breadcrumb adminx-page-breadcrumb">
								<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
								<li class="breadcrumb-item"><a href="/import_items">Import Items</a></li>
								<li class="breadcrumb-item active" aria-current="page">Upload Excel Files </li>
							</ol>
						</nav>
					</div>
				</div>
			</div>	

			<div class="row">
				<div class="col-md-12 col-lg-12">
					<div class="card">
						<div class="mt-2 mb-2 border-b">
							<h6>Upload Excel Files:</h6>	
						</div>
						<div class="row">
							<div class="col-lg-4">
								<div class="form-group">
									<label class="form-label mb-0">Item Inventory</label>
								</div>										
							</div>
                            <div class="col-lg-3">
								<div class="form-group">
									<a href="/export-inventory" >Download Inventory Format</a>
								</div>										
							</div>
                            <div class="col-lg-3">
								<div class="form-group">
									<input type="file" id="upload_inventory" name="upload_inventory" @change="upload_inventory">
								</div>										
							</div>
                            <div class="col-lg-2">
								<div class="form-group">
									<button @click="onSaveInv()" :disabled="check_inv" id="btn_inv" class="btn btn-sm btn-primary btn-rounded w-32">Upload</button>
								</div>										
							</div>
						</div>
                        <div class="row">
							<div class="col-lg-4">
								<div class="form-group">
									<label class="form-label mb-0">Beginning Balance</label>
								</div>										
							</div>
                            <div class="col-lg-3">
								<div class="form-group">
									<!-- <a href="#" @click="tableToExcel('testTable', 'W3C Example Table')">Download Begbal Format</a> -->
									<a href="/export-excel">Download Begbal Format</a>
									<!-- <a href="#" @click="tableToExcel1s()">Download Begbal Format</a> -->
								</div>										
							</div>
                            <div class="col-lg-3">
								<div class="form-group">
									<input type="file" name="upload_begbal" id="upload_begbal" @change="upload_begbal">
								</div>										
							</div>
                            <div class="col-lg-2">
								<div class="form-group">
									<button @click="onSave()" :disabled="check_begbal" id="btn_begbal" class="btn btn-sm btn-primary btn-rounded w-32">Upload</button>
								</div>										
							</div>
						</div>
                        <div class="row">
							<div class="col-lg-4">
								<div class="form-group">
									<label class="form-label mb-0">Update Item Info</label>
								</div>										
							</div>
                            <div class="col-lg-3">
								<div class="form-group">
									<a href="/export-currentinventory">Download Current Inventory Format</a>
								</div>										
							</div>
                            <div class="col-lg-3">
								<div class="form-group">
									<input type="file" id="upload_currentinventory" name="upload_currentinventory" @change="upload_currentinventory">
								</div>										
							</div>
                            <div class="col-lg-2">
								<div class="form-group">
									<button @click="onSaveCurrent()" :disabled="check_curinv" id="btn_current" class="btn btn-sm btn-primary btn-rounded w-32">Upload</button>
								</div>										
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
    </navigation>
</template>
