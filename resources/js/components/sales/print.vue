<script setup>
	import{ onMounted, ref } from "vue"
	import navigation from '@/layouts/navigation.vue';
	import printheader from '@/layouts/header.vue';
	import { CheckCircleIcon, TrashIcon, PlusIcon, XMarkIcon, PencilSquareIcon, ArrowUturnLeftIcon, ExclamationTriangleIcon, CheckIcon  } from '@heroicons/vue/24/solid'
	import { useRouter } from "vue-router"
</script>

<style>
	@media print {
		.no-btn  {
			display: none;
		}
			#printme  {
			display: block;
		}
	}
	</style>

<template>
    <navigation>
        <div class="container-fluid">
			<div class="card mb-3 no-btn">	
				<div class="flex justify-between content-center">
					<div class="flex justify-start space-x-3 ">
						<div class="no-btn">
							<a href="javascript: history.go(-1)" class="btn btn-secondary btn-xs btn-rounded">
								<ArrowUturnLeftIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></ArrowUturnLeftIcon>
							</a>
						</div>
						<div>
							<h6 class="m-0 pt-1 font-bold uppercase">Sales</h6>
						</div>
					</div>	
					<div class="pt-1 no-btn">	
						<nav aria-label="breadcrumb" role="navigation">
							<ol class="breadcrumb adminx-page-breadcrumb">
								<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
								<li class="breadcrumb-item"><a href="/sales">Sales</a></li>
								<li class="breadcrumb-item active" aria-current="page">Print Delivery</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-lg-12 no-btn">
					<div class="flex justify-center mb-3">
						<button class="btn btn-sm btn-success" @click="printDiv('printme')"> Print Report</button>
					</div>
				</div>
			</div>
		
			<div class="row">
				<div class="col-lg-12">
					<div class="flex justify-center">
						<page size="A4" id="printme" class="p-2">
							<div >
								<printheader>
									DELIVERY RECEIPT
								</printheader>
								<table class="w-full table-bordsered">
									<tr>
										<td class="leading-tight text-sm font-bold" width="11%">DR NO.</td>
										<td class="leading-tight text-sm font-bold" width="30%" ><span class="pr-2">:</span></td>
										<td class="leading-tight text-sm font-bold" width="11%">DATE</td>
										<td class="leading-tight text-sm font-bold" width="30%" ><span class="pr-2">:</span></td>
									</tr>
									<tr>
										<td class="leading-tight text-sm">BUYER</td>
										<td class="leading-tight text-sm" colspan="3"><span class="pr-2">:</span></td>
									</tr>
									<tr>
										<td class="leading-tight text-sm">ADDRESS</td>
										<td class="leading-tight text-sm" colspan="3"><span class="pr-2">:</span></td>
									</tr>
									<tr>
										<td class="leading-tight text-sm">CONTACT PERSON</td>
										<td class="leading-tight text-sm" ><span class="pr-2">:</span></td>
										<td class="leading-tight text-sm">CONTACT NUMBER</td>
										<td class="leading-tight text-sm" ><span class="pr-2">:</span></td>
									</tr>
									<tr>
										<td class="leading-tight text-sm">PGC PR/PO NO.</td>
										<td class="leading-tight text-sm" ><span class="pr-2">:</span></td>
										<td class="leading-tight text-sm">PR/PO DATE</td>
										<td class="leading-tight text-sm" >
											<div class="flex justify-between">
												<div><span class="pr-2">:</span><span>asd</span></div>
												<div class="pr-10"><span class="pr-2">VAT:</span><span>Vatable</span></div>
											</div>
										</td>
									</tr>
								</table>
								<table width="100%" class="table-bordered mt-2">
									<tr>
										<td class="leading-tight text-sm" width="2%" align="center">#</td>
										<td class="leading-tight text-sm" width="10%" align="center">Part No.</td>
										<td class="leading-tight text-sm" width="30%" align="center">Item Description</td>  
										<td class="leading-tight text-sm" width="10%" align="center">Serial No.</td>
										<td class="leading-tight text-sm" width="5%" align="center">Qty</td>
										<td class="leading-tight text-sm" width="5%" align="center">UOM</td>
										<td class="leading-tight text-sm" width="10%" align="center">Selling Price</td>
										<td class="leading-tight text-sm" width="10%" align="center">Discount</td>
										<td class="leading-tight text-sm" width="10%" align="center">Shipping Fee</td>
										<td class="leading-tight text-sm" width="10%" align="center">Total Price</td>
									</tr>
									<tr>
										<td class="leading-tight text-sm align-top px-1" align="center">1</td>
										<td class="leading-tight text-sm align-top px-1" align="center">232</td>
										<td class="leading-tight text-sm align-top px-1" align="left">sample</td>
										<td class="leading-tight text-sm align-top px-1" align="center">asd</td>
										<td class="leading-tight text-sm align-top px-1" align="center">23</td>
										<td class="leading-tight text-sm align-top px-1" align="center">kg</td>
										<td class="leading-tight text-sm align-top px-1" align="center">1005</td>
										<td class="leading-tight text-sm align-top px-1" align="center">21</td>
										<td class="leading-tight text-sm align-top px-1" align="center">232</td>
										<td class="leading-tight text-sm align-top px-1" align="center">123</td>
									</tr>
								</table>
								<table class="w-full mt-2">
									<tr>
										<td class="text-sm" >Remarks:</td>
									</tr>
									<tr>
										<td class="text-sm border-b border-gray-200"><br> </td>
									</tr>
								</table>
								<table class="w-full mt-2">
									<tr>
										<td class="text-sm" width="8%">Shipped Via:</td>
										<td class="text-sm border-b border-gray-200" width="40%"> <br></td>
										<td class="text-sm" width="8%" align="right">Waybill No:</td>
										<td class="text-sm border-b border-gray-200" width="40%"><br></td>
									</tr>
									
								</table>
								<table class="w-full mt-2">
									<tr>
										<td class="text-sm" width="30%">Prepared By:</td>
										<td width="5%"></td>                    
										<td class="text-sm" width="30%">Released by:</td>
										<td width="5%"></td>
										<td class="text-sm" width="30%">Verified by:</td>
									</tr>
									<tr>
										<td class="border-b border-gray-200 p-0">
											<input  class="text-sm w-full text-center" name="" readonly >
										</td>     
										<td></td>
										<td class="border-b border-gray-200 p-0">
											<select class="text-sm w-full text-center appearance-none" id="">
												<option></option>
											</select>
											<!-- <input id="" class="text-sm w-full text-center" style="pointer-events:none"> -->
										</td>
										<td></td>
										<td class="border-b border-gray-200 p-0">
											<select class="text-sm w-full text-center appearance-none" id="">
												<option></option>
											</select>
											<!-- <input id="" class="text-sm w-full text-center" style="pointer-events:none"> -->
										</td>           
									</tr>
									<tr>
										<td>
											<input class="text-sm w-full text-center" style="pointer-events:none">
										</td>  
										<td></td>
										<!-- <td class="text-sm text-center">End-User/Requester</td> -->
										<td class="text-sm text-center">
											<input class="text-sm w-full text-center" style="pointer-events:none">
										</td>
										<td></td>
										<td>
											<input class="text-sm w-full text-center" style="pointer-events:none">
										</td>
									</tr>
								</table>
								
								<table class="w-full mt-2">
									<tr>
										<td width="10%"></td>
										<td class="text-sm" width="35%">Noted by:</td>
										<td width="10%"></td>
										<td class="text-sm" width="35%">Received the items above in good condition:</td>
										<td width="10%"></td>
									</tr>
									<tr>
										<td></td>
										<td class="border-b border-gray-200">
											<select class="text-sm w-full text-center appearance-none" id="">
												<option></option>
											</select>
											<!-- <input id="" class="text-sm w-full text-center" style="pointer-events:none"> -->
										</td>
										<td></td>
										<td class="border-b border-gray-200">
											<select class="text-sm w-full text-center appearance-none" id="">
												<option></option>
											</select>
											<!-- <input id="" class="text-sm w-full text-center" style="pointer-events:none"> -->
										</td>
										<td></td>                
									</tr>
									<tr>
										<td></td>
										<td>
											<input class="text-sm w-full text-center" style="pointer-events:none">
										</td>
										<td></td>
										<td class="text-sm text-center">
											Signature over printed name <br>
											<span class="justify-start flex w-full">Date/Time:</span>
										</td>
										<td></td>                
									</tr>
								</table>
							</div>
						</page>
					</div>
				</div>
			</div>
		</div>

    </navigation>
</template>
