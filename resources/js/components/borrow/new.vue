<script setup>
import navigation from '@/layouts/navigation.vue';
import { PencilSquareIcon, TrashIcon, PlusIcon, XMarkIcon, Bars3Icon, ArrowUturnLeftIcon } from '@heroicons/vue/24/solid'
import axios from 'axios';
import {onMounted, ref} from "vue";
import { useRouter } from "vue-router";

const router = useRouter()
let rows = ref([])
let form=ref([])
let listemployees = ref([])
let ItemList = ref([])
let PRList = ref([])
let departments = ref([])
let enduses = ref([])
let purposes = ref([])
let users = ref([])
let borrow_rem=ref([]);
let description = ref('');
let pr_no = ref('');
let borrowers_name = ref('');
let borrowed_by = ref('');
let borrowed_qty = ref(0);
let department = ref('');
let purpose = ref('');
let enduse = ref('');
let remarks = ref('');
let error =ref([])
let success = ref('')

	onMounted(async () => {
			borrowheadform()
			getUsers()
		})

	const borrowheadform = async () => {
		let response = await axios.get("/api/create_borrow_head");
		form.value = response.data.formData;
		ItemList.value = response.data.BorrowItems;
		departments.value = response.data.department
		enduses.value = response.data.enduse
		purposes.value = response.data.purpose
		users.value = response.data.user
	}

	const getUsers = async () => {
		let response = await axios.get("/api/employee_list");
		listemployees.value=response.data.users
		
	}

	const CheckPr = async () => {
			let desc = description.value
			const d= desc.split("_")
			let itemid= d[0]

			let response = await axios.get('/api/search_availablepr/'+itemid);
			PRList.value = response.data.varaints;
			document.getElementById("prno_").disabled = false;
			borrowed_qty.value=0
			document.getElementById("borrowedq_").disabled = true;
			document.getElementById("borrowedby_").disabled = true;
			document.getElementById("department_").disabled = true;
			document.getElementById("enduse_").disabled = true;
			document.getElementById("purpose_").disabled = true;
			document.getElementById("remarks_").disabled = true;
			document.getElementById("addborrow_").disabled = true;
	}

	const EnableItemList = () => {
		let b_name = borrowers_name.value
		if(b_name != ''){
			document.getElementById("itemdesc_").disabled = false;
			// document.getElementById("SubmitButton").disabled = false;
			borrowed_qty.value = 0;
		}else{
			document.getElementById("itemdesc_").disabled = true;
			// document.getElementById("SubmitButton").disabled = true;
		}
	}

	const DisplayQty = () => {
		let desc = description.value
		const de= desc.split("_")
		let item_id= de[0]

		let bal = pr_no.value
		const b= bal.split("_")
		let pr= b[1]
		let quantity= b[3]

			if(pr_no != ''){
				borrowed_qty.value = quantity
				document.getElementById("borrowedq_").disabled = false;
				document.getElementById("borrowedby_").disabled = false;
				document.getElementById("department_").disabled = false;
				document.getElementById("enduse_").disabled = false;
				document.getElementById("purpose_").disabled = false;
				document.getElementById("remarks_").disabled = false;
				document.getElementById("addborrow_").disabled = false;
			}else{
				document.getElementById("borrowedq_").disabled = true;
				document.getElementById("borrowedby_").disabled = true;
				document.getElementById("department_").disabled = true;
				document.getElementById("enduse_").disabled = true;
				document.getElementById("purpose_").disabled = true;
				document.getElementById("remarks_").disabled = true;
				document.getElementById("addborrow_").disabled = true;
			}

		for (var i = 0; i < rows.value.length; i++) {
			if(rows.value[i].itemid == item_id && rows.value[i].prno == pr){
				alert('PR No is already added!');
				document.getElementById("addborrow_").disabled = true;
			}
		}

	}

	const addBorrow= () => {
			let desc = description.value
			const de= desc.split("_")
			let itemid= de[0]
			let item_name= de[1]

			let pr = pr_no.value
			const p= pr.split("_")
			let prno= p[1]
			let variantid= p[2]
			let bal= p[3]

			let dept = department.value
			const dn= dept.split("_")
			let dept_id= dn[0]
			let dept_name= dn[1]

			let pur = purpose.value
			const pn= pur.split("_")
			let pur_id= pn[0]
			let pur_name= pn[1]

			let end = enduse.value
			const en= end.split("_")
			let end_id= en[0]
			let end_name= en[1]

			if(borrowed_qty.value == 0){
				alert('Quantity must not be empty');
			}else if(department.value == ''){
				alert('Department must not be empty');
			}else if(purpose.value == ''){
				alert('Purpose must not be empty');
			}else if(enduse.value == ''){
				alert('Enduse must not be empty');
			}else{
				rows.value.push({
					itemid:itemid,
					item_desc :item_name,
					prno :prno,
					variantid :variantid,
					avail_qty :bal,
					borrowed_qty :borrowed_qty.value,
					borrowed_by :borrowed_by.value,
					department :dept_name,
					dept_id :dept_id,
					purpose :pur_name,
					pur_id :pur_id,
					enduse :end_name,
					end_id :end_id,
					remarks :remarks.value,
					}
				);
				description.value=[]
				pr_no.value=[]
				borrowed_qty.value=0
				borrowed_by.value=''
				department.value=[]
				purpose.value=[]
				enduse.value=[]
				remarks.value=''
				document.getElementById("prno_").disabled = true;
				document.getElementById("borrowedq_").disabled = true;
				document.getElementById("borrowedby_").disabled = true;
				document.getElementById("department_").disabled = true;
				document.getElementById("enduse_").disabled = true;
				document.getElementById("purpose_").disabled = true;
				document.getElementById("remarks_").disabled = true;
				document.getElementById("addborrow_").disabled = true;
				document.getElementById("SubmitButton").style.display = "block";
			}
			
		}

	const removeItem= (index) =>{
		if(confirm("Do you really want to delete this row?")){
			rows.value.splice(index,1)
			if(index > 0){
				document.getElementById("SubmitButton").style.display = "block";
			}else{
				document.getElementById("SubmitButton").style.display = "none";
			}
		}
	}

	// const SaveNewBorrow = () => {
	// 	if(confirm("Are you sure you want to save this Borrow? It will automatically create a request transaction")){
	// 		saveTransaction()				
	// 	}
	// }

		const SaveNewBorrow = () => {
			//if(rows.value.length>=1){
			if(confirm("Are you sure you want to save this Borrow? It will automatically create a request transaction")){
				const formItems= new FormData()
					formItems.append('mbr_no', form.value.mbr_no)
					formItems.append('borrow_date', form.value.borrow_date)
					formItems.append('borrow_time', form.value.borrow_time)
					formItems.append('borrowers_name', borrowers_name.value)
						for(var i=0;i<rows.value.length;i++){
						var pr=document.getElementsByClassName("remarks_pr")[i].value;
						var r_by=document.getElementsByClassName("remarks_by")[i].value;
						var dept_by=document.getElementsByClassName("remarks_dept")[i].value;
						var end_by=document.getElementsByClassName("remarks_end")[i].value;
						var pur_by=document.getElementsByClassName("remarks_pur")[i].value;
								const borrow_remarks = {
									pr:pr,
									r_by:r_by,
									dept_by:dept_by,
									end_by:end_by,
									pur_by:pur_by,
								}
									borrow_rem.value.push(borrow_remarks)
						}
					// console.log(borrow_rem);
					formItems.append('head_remarks', JSON.stringify(borrow_rem.value))
					formItems.append('borrow_items', JSON.stringify(rows.value))
					axios.post(`/api/save_borrow`, formItems).then(function (response) {
						// console.log(response.data)
						error.value=[]
						success.value='Successfully saved!'
						router.push('/borrow/print/'+response.data)
						}).catch(function(err){
							success.value=''
						});
			}
		}

		const BorrowQtyLimit = async () => {
			let pr = pr_no.value
			const p = pr.split("_")
			let quantity= p[3]
				if(parseFloat(quantity) >= parseFloat(borrowed_qty.value)){
					document.getElementById("addborrow_").disabled = false;
				}else{
					alert('Borrow quantity not equal to warehouse quantity');
					document.getElementById("addborrow_").disabled = true;
				}
		}
</script>

<template>
    <navigation>
        <div class="container-fluid">
			<!-- BreadCrumb -->
			<div class="card mb-3">	
				<div class="flex justify-between content-center">
					<div class="flex justify-start space-x-3 ">
						<div class="">
							<a href="/dashboard" class="btn btn-secondary btn-xs btn-rounded">
								<ArrowUturnLeftIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></ArrowUturnLeftIcon>
							</a>
						</div>
						<div>
							<h6 class="m-0 pt-1 font-bold uppercase">Borrow</h6>
						</div>
					</div>	
					<div class="pt-1">	
						<nav aria-label="breadcrumb" role="navigation">
							<ol class="breadcrumb adminx-page-breadcrumb">
								<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
								<li class="breadcrumb-item"><a href="/borrow">Borrow</a></li>
								<li class="breadcrumb-item active" aria-current="page">Add New Borrow</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>	

			<div class="row">
				<div class="col-md-12 col-lg-12">
					<div class="card card-main-bg">
						<div class="p-2 pt-3">
							<table class="w-full">
								<tr>
									<td class="px-1 " width="20%">
										<div class="flex justify-start ">
											<span class="text-xs text-gray-500 leading-none">MBR NO.</span>
										</div>
										<span class="text-lg uppercase font-bold text-gray-700 w-full leading-none">
											<input type="text" class="form-control border my-1" disabled  v-model="form.mbr_no">
										</span>
									</td>
									<td class="px-1 " width="20%">
										<div class="flex justify-start" >
											<span class="text-xs text-gray-500 leading-none">BORROWER'S NAME</span>
										</div>
										<span class="text-lg uppercase text-gray-700 w-full leading-none">
											<select class="form-control border" id="borrowers_name" v-model="borrowers_name" @change="EnableItemList()">
												<option :value="emp.id" v-for="emp in listemployees" :key="emp.id">{{ emp.name }}</option>
											</select>
										</span>
									</td>
									<td class="px-1 " width="5%">
										<div class="flex justify-start" >
											<span class="text-xs text-gray-500 leading-none">DATE</span>
										</div>
										<span class="text-lg uppercase text-gray-700 w-full leading-none">
											<input type="date" class="form-control border my-1" v-model="form.borrow_date">
										</span>
									</td>
									<td class="px-1" width="5%">
										<div class="flex justify-start">
											<span class="text-xs text-gray-500 leading-none">TIME</span>
										</div>
										<span class="text-lg uppercase text-gray-700 w-full leading-none">
											<input type="time" class="form-control border my-1 py-1" v-model="form.borrow_time">
										</span>
									</td>
								</tr>
							</table>
							<hr class="my-4 border-dashed mx-1">	
							<div class="row px-1">
								<div class="col-lg-12 ">
									<div class="text-lg uppercase text-gray-700 w-full leading-none flex justify-between space-x-1 mb-3">
										<span class="w-4/12">
											<label for="" class="form-label">Item Name</label>
												<select class="form-control border" id="itemdesc_" v-model="description" @change="CheckPr()" disabled>
													<option :value="it.id+'_'+ it.item_description" v-for="it in ItemList" :key="it.id">{{ it.item_description }}</option>
												</select>
										</span>
										<span class="w-3/12">
											<label for="" class="form-label">PR Number</label>
											<select class="form-control border" id="prno_" v-model="pr_no" @change="DisplayQty()" disabled>
												<option :value="p.id+'_'+p.pr_no+'_'+p.variant_id+'_'+p.quantity" v-for="p in PRList" :key="p.id">{{ p.pr_no }} - Available Qty: {{ p.quantity }} {{ p.variant_data }}</option>
											</select>
										</span>
										<span class="w-2/12">
											<label for="" class="form-label w-full">Borrow Qty</label>
											<input class="form-control border" type="number" id="borrowedq_" v-model="borrowed_qty" min="0" onkeypress="return (event.charCode !=8 && event.charCode ==0 || (event.charCode >= 48 && event.charCode <= 57))" @change="BorrowQtyLimit()" disabled>
										</span>
										<span class="w-3/12">
											<label for="" class="form-label">Borrower's PR</label>
											<input class="form-control border" id="borrowedby_" v-model="borrowed_by" oninput="this.value = this.value.toUpperCase()" disabled>
										</span>
									</div>
									<div class="text-lg uppercase text-gray-700 w-full leading-none flex justify-between space-x-1 mb-3">
										<span class="w-3/12">
											<label for="" class="form-label">Department</label>
											<select class="form-control border" id="department_" v-model="department" disabled>
												<option v-for="dept in departments" v-bind:key="dept.id"  :value="dept.id+'_'+dept.department_name ">{{  dept.department_name }}</option>
											</select>
										</span>
										<span class="w-4/12">
											<label for="" class="form-label">Enduse</label>
											<select class="form-control border" id="enduse_" v-model="enduse" disabled>
												<option v-for="end in enduses" v-bind:key="end.id" :value="end.id+'_'+end.enduse_name">{{  end.enduse_name }}</option>
											</select>
										</span>
										<span class="w-4/12">
											<label for="" class="form-label">Purpose</label>
											<select class="form-control border" id="purpose_" v-model="purpose" disabled>
												<option v-for="purp in purposes" v-bind:key="purp.id" :value=" purp.id+'_'+purp.purpose_name ">{{  purp.purpose_name }}</option>
											</select>
										</span>
										<span class="w-2/12">
											<label for="" class="form-label">Remarks</label>
											<input class="form-control border" id="remarks_" v-model="remarks" disabled >
										</span>
										<span class="pt-4">
											<button class="btn btn-primary btn-sm" id="addborrow_" @click="addBorrow()" disabled>
												<PlusIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"></PlusIcon>
											</button>
										</span>
									</div>
								</div>
							</div>
							<div class="row px-1">
								<div class="col-lg-12">
									<table class="table table-bordered">
										<thead>
											<tr>
												<th class="font-xxs">Item Description</th>
												<th class="font-xxs">Borrow From</th>
												<th class="font-xxs" width="6%">Avail Qty</th>
												<th class="font-xxs" width="7%">Borrow Qty</th>
												<th class="font-xxs" width="13%">Borrow By</th>
												<th class="font-xxs">Department</th>
												<th class="font-xxs">Enduse</th>
												<th class="font-xxs">Purpose</th>
												<th class="font-xxs" width="15%">Remarks</th>
												<th class="font-xxs" width="1%">
													<Bars3Icon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"></Bars3Icon >
												</th>
											</tr>
										</thead>
										<tr v-for="(r, index) in rows">
											<td class="text-xs">{{ r.item_desc }}</td>
											<td class="text-xs">
												{{ r.prno }}
												<input type="hidden" class="remarks_pr" v-model="r.prno" :id="'pr'+index">
											</td>
											<td class="text-xs">{{ r.avail_qty }}</td>
											<td class="text-xs">{{ r.borrowed_qty }}</td>
											<td class="text-xs">
												{{ r.borrowed_by }}
												<input type="hidden" class="remarks_by" v-model="r.borrowed_by" :id="'borrowedby'+index">
											</td>
											<td class="text-xs">{{ r.department }}</td>
											<td class="text-xs">{{ r.enduse }}</td>
											<td class="text-xs">{{ r.purpose }}</td>
											<td class="text-xs">{{ r.remarks }}</td>
											<input type="hidden" class="p-1 m-0 w-full leading-none" v-model="r.itemid"/>
											<input type="hidden" class="p-1 m-0 w-full leading-none" v-model="r.variantid"/>
											<input type="hidden" class="p-1 m-0 w-full leading-none" v-model="r.dept_id"/>
											<input type="hidden" class="remarks_dept" v-model="r.dept_id" :id="'remarksdept'+index">
											<input type="hidden" class="p-1 m-0 w-full leading-none" v-model="r.end_id"/>
											<input type="hidden" class="remarks_end" v-model="r.end_id" :id="'remarksend'+index">
											<input type="hidden" class="p-1 m-0 w-full leading-none" v-model="r.pur_id"/>
											<input type="hidden" class="remarks_pur" v-model="r.pur_id" :id="'remarkspur'+index">
											<td class="text-xs" align="center">
												<button class="btn btn-danger btn-xs p-1"  @click="removeItem(index)">
													<XMarkIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></XMarkIcon >
												</button>
											</td>
										</tr>
									</table>
								</div>
							</div>
						</div>
						<div class="px-2 pt-2 mb-2 mt-1 flex justify-end space-x-10">
							<div class="flex justify-between space-x-1">
								<a @click="SaveNewBorrow()" id = "SubmitButton" type="submit" style="display:none" class="btn btn-sm py-1.5 bg-blue-500 text-white hover:bg-blue-600 w-60" disabled>Save & Print</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
    </navigation>
</template>
