<script setup>
	import { onMounted, ref } from "vue"
	import { useRouter } from "vue-router" 
	import navigation from '@/layouts/navigation.vue';
	import { PencilSquareIcon, Bars3Icon, PlusIcon, MinusCircleIcon, FunnelIcon, ArrowUturnLeftIcon } from '@heroicons/vue/24/solid'

	const router = useRouter() 
	let items = ref([])
	// let pr = ref([])
	// let pritems = ref([])
	 let form=ref([]);
	 let reports=ref([]);

	onMounted(async () => {
		getItems()
		//getPR()
	})

	const getItems = async () => {
		let response = await axios.get("/api/item_variant_list");
		items.value=response.data.items
	}

	// const getVariant = async () => {
	// 	let response = await axios.get("/api/variant_list");
	// 	pr.value=response.data.pr
	// }

	const filter = () => {
		const formDetails = new FormData()
		formDetails.append('item', form.value.item_name)
		
		axios.post(`/api/filter_item`, formDetails).then(function (response) {
				reports.value = response.data;
				document.getElementById("showExport").style.display="block"
			
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
						<div class="">
							<a onclick="history.back()" class="btn btn-secondary btn-xs btn-rounded text-white">
								<ArrowUturnLeftIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></ArrowUturnLeftIcon>
							</a>
						</div>
						<div>
							<h6 class="m-0 pt-1 font-bold uppercase">Variants Report</h6>
						</div>
					</div>	
					<div class="pt-1">	
						<nav aria-label="breadcrumb" role="navigation">
							<ol class="breadcrumb adminx-page-breadcrumb">
								<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
								<li class="breadcrumb-item active" aria-current="page">Reports</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>	

			<div class="row">
				<div class="col-md-12 col-lg-12 ">
					<div class="card">
						<div class="table-responsive-md">
							<div class="flex justify-start pb-2 my-2 space-x-2">
								<div class="w-64">
									<select type="text" class="form-control border" v-model="form.item_name">
                                        <option value="">Select Item</option>
										<option v-for="i in items" :value="i.id" :key="i.id">{{ i.item_description }}</option>
                                    </select>
								</div>
                              
								<button class="btn btn-sm btn-success"  @click="filter()">
									<div class="flex justify-between space-x-2" >
										<FunnelIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"></FunnelIcon>
										<span>Filter</span>
									</div>
								</button>
							</div>
							<hr class="border-dashed">
                            <div class="row" style="display: none;" id="showExport">
                                <div class="col-lg-12 px-1">
                                    <div class="flex justify-end mb-3">
                                        <a :href="'/export-variants/'+((form.item_name!='') ? form.item_name : '0')" class="btn btn-sm btn-success">
                                            <div class="flex justify-between space-x-2" >
                                                <span>Export to Excel</span>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
							<table class="table table-bordered table-hover mb-0">
								<thead>
									<tr>
										<th class="font-xxs align-bottom" width="20%">Item</th>
										<th class="font-xxs align-bottom" width="20%">Variant</th>
										
										<th class="font-xxs align-top text-center">
											WStock Qty
										</th>
										<th class="font-xxs align-top text-center">
											Composite Qty 
										</th>
										<th class="font-xxs align-top text-center">Receive Qty</th>
										<th class="font-xxs align-top text-center">Issuance Qty</th>
										<th class="font-xxs align-top text-center">
											Restock Qty <span class="text-sm leading-none text-green-500 font-bold ml-1">+</span>
										</th>
										<th class="font-xxs align-top text-center">Transfer
												<span class="text-sm leading-none text-red-500 font-bold ml-1">-</span>
											</th>
											<th class="font-xxs align-top text-center">Damage Qty</th>
											<th class="font-xxs align-top text-center">Borrowed Qty
												<span class="text-sm leading-none text-red-500 font-bold ml-1">-</span>
											</th>
											<th class="font-xxs align-top text-center">Reimbursed Qty
												<span class="text-sm leading-none text-green-500 font-bold ml-1">+</span>
											</th>
											<th class="font-xxs align-top text-center">Lent Qty
												<span class="text-sm leading-none text-green-500 font-bold ml-1">+</span>
											</th>
											<th class="font-xxs align-top text-center">Replenished Qty
												<span class="text-sm leading-none text-red-500 font-bold ml-1">-</span>
											</th>
											<th class="font-xxs align-top text-center">Backorder Qty</th>
											<th class="font-xxs align-top text-center !bg-yellow-100">Balance</th>
									</tr>
								</thead>
							
								<tbody>
									<tr v-for="r in reports">
										<td class="text-xs">{{ r.item }}</td>
										<td class="text-xs"><span class="text-white btn btn-xs py-0 !text-xs bg-gray-400 rounded mr-1" v-if="r.variant.supplier_name != ''">{{ r.variant.supplier_name }}</span>
                                                <span class="text-white btn btn-xs py-0 !text-xs bg-gray-400 rounded mr-1" v-if="r.variant.uom != ''">{{ r.variant.uom }}</span>
                                                <span class="text-white btn btn-xs py-0 !text-xs bg-gray-400 rounded mr-1" v-if="r.variant.brand != ''">{{ r.variant.brand }}</span>
                                                <span class="text-white btn btn-xs py-0 !text-xs bg-gray-400 rounded mr-1" v-if="r.variant.color != ''">{{ r.variant.color }}</span>
                                                <span class="text-white btn btn-xs py-0 !text-xs bg-gray-400 rounded mr-1" v-if="r.variant.size != ''">{{ r.variant.size }}</span>
												<span class="text-white btn btn-xs py-0 !text-xs bg-gray-400 rounded mr-1" v-if="r.item_status != ''">{{ r.item_status }}</span>
												<span class="text-white btn btn-xs py-0 !text-xs bg-gray-400 rounded mr-1" v-if="r.variant.average_cost != '' && r.variant!='Beginning Balance'">{{ r.variant.average_cost+" "+r.variant.currency }}</span></td>
										<td class="text-xs text-center">{{ r.begbal }}</td>
										<td class="text-xs border font-bold text-center">{{ r.composite_qty }}</td>
										<td class="text-xs border font-bold text-center">{{ r.receive_qty }}</td>
										<td class="text-xs border font-bold text-center">{{ r.issuance_qty }}</td>
										<td class="text-xs border font-bold text-center">{{ r.restock_qty }}</td>
										<td class="text-xs border font-bold text-center">{{ r.transfer_qty }}</td>
										<td class="text-xs border font-bold text-center">{{ r.damage_qty }}</td>
										<td class="text-xs border font-bold text-center">{{ r.borrow_deduct }}</td>
										<td class="text-xs border font-bold text-center">{{ r.replenish_add }}</td>
										<td class="text-xs border font-bold text-center">{{ r.borrow_add }}</td>
										<td class="text-xs border font-bold text-center">{{ r.replenish_deduct }}</td>
										<td class="text-xs border font-bold text-center">{{ r.backorder_qty }}</td>
										<td class="text-xs border font-bold text-center font-bold !bg-yellow-100">{{ r.balance }}</td>
									</tr>
								</tbody>
							</table>
							<div class="flex justify-end p-2 border-t">
								<nav aria-label="Page navigation example">
									
								</nav>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
    </navigation>
</template>
