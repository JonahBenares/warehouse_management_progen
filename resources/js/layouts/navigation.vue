<script setup >
	import { onMounted, ref } from 'vue';
	import { useRouter } from "vue-router";
	import { ShoppingBagIcon, HomeIcon, ArchiveBoxArrowDownIcon, Square2StackIcon, DocumentChartBarIcon, ArrowPathRoundedSquareIcon, FolderIcon, TicketIcon, Square3Stack3DIcon, ChevronRightIcon, InboxArrowDownIcon, ClipboardDocumentIcon, ArrowUpOnSquareStackIcon, BellIcon, RectangleGroupIcon } from '@heroicons/vue/24/outline';
	import { Cog8ToothIcon} from '@heroicons/vue/24/solid'
	const router = useRouter();
	const masterfileDrop = ref(false);
	const receiveDrop = ref(false);
	const uatDrop = ref(false);
	const requestDrop = ref(false);
	const issueDrop = ref(false);
	const restockDrop = ref(false);
	const salesDrop = ref(false);
	const borrowDrop = ref(false);
	const backorderDrop = ref(false);
	const gatepassDrop = ref(false);
	const reportsDrop = ref(false);
	const transactionDrop = ref(false);
	const dropdown = ref(false);
	let credentials=ref([])
	let url='/'

	const loading = ref(false);
	const company = ref({});


	onMounted(async () => {
		getDashboard();
		const response = await fetch('/api/constants');
		const data = await response.json();
		company.value = data.company;
	});

	const logout = async () => {
		loading.value = true;
		setTimeout(() => {
			localStorage.removeItem('token');
			router.push('/');
			loading.value = false;
		}, 1500);
	};

	const getDashboard = async () => {
		const response = await fetch(`/api/dashboard`);
		credentials.value = await response.json();
		if (!credentials.value.name) {
			alert('You have been logged out due to inactivity.');
			router.push('/');
		}
	};
</script>
<style>
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.5s;
}
.fade-enter, .fade-leave-to {
    opacity: 0;
}
.loader {
    border: 4px solid rgba(0, 0, 0, 0.1);
    border-left-color: #000;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
<template>
	
    <div class="adminx-container">
      	<nav id="print_nav" class="navbar navbar-expand justify-content-between fixed-top">
			<a class="navbar-brand mb-0 h1 d-none d-md-block" href="index.html">
				<img :src="company.logo" class="navbar-brand-image d-inline-block align-top mr-2" alt="">
				<span class="font-bold mx-2">{{ company.title }}</span>
				<span class="text-gray-500 text-base">Warehouse Management System	</span>				
			</a>
			<div class="d-flex flex-1 d-block d-md-none">
				<a href="#" class="sidebar-toggle ml-3">
					<i data-feather="menu"></i>
				</a>
			</div>
			<ul class="navbar-nav d-flex justify-content-end mr-2">
				<li class="nav-item dropdown d-flex align-items-center mr-2 ">
					<a class="nav-link nav-link-notifications" @click="dropdown = !dropdown" href="#"  aria-expanded="false">
						<Cog8ToothIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"></Cog8ToothIcon>
						<!-- <span class="nav-link-notification-number">3</span> -->
					</a>
					<Transition
						enter-active-class="transition ease-out duration-200"
						enter-from-class="opacity-0 scale-95"
						enter-to-class="opacity-100 scale-100"
						leave-active-class="transition ease-in duration-75"
						leave-from-class="opacity-100 scale-100"
						leave-to-class="opacity-0 scale-95"
					>
					<div class="dropdown-menu dropdown-menu-right" v-show="dropdown">
						<a class="dropdown-item" href="/change_password/">Change password</a>
						<div class="dropdown-divider"></div>
						<a class="dropdown-item text-danger" href="#" @click="logout" >Sign out</a>
					</div>
					</Transition>
				</li>
			</ul>
      	</nav>
		
      	<div id="print_side" class="adminx-sidebar expand-hover push">
			<ul class="sidebar-nav">
				<li class="sidebar-nav-item">
					<a href="/dashboard" class="sidebar-nav-link active">
						<span class="sidebar-nav-icon">
							<HomeIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" />
						</span>
						<span class="sidebar-nav-name">
							Dashboard
						</span>
						<span class="sidebar-nav-end">

						</span>
					</a>
				</li>
				<li class="sidebar-nav-item">
					<a href="/reminder" class="sidebar-nav-link">
						<span class="sidebar-nav-icon">
							<BellIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" />
						</span>
						<span class="sidebar-nav-name">
							Reminder
						</span>
						<span class="sidebar-nav-end">

						</span>
					</a>
				</li>
				<li class="sidebar-nav-item">
					<a href="/item_list" class="sidebar-nav-link">
						<span class="sidebar-nav-icon">
							<RectangleGroupIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" />
						</span>
						<span class="sidebar-nav-name">
							Item List
						</span>
						<span class="sidebar-nav-end">

						</span>
					</a>
				</li>
				<li class="sidebar-nav-item">
					<a class="sidebar-nav-link collapsed" @click="masterfileDrop = !masterfileDrop" href="#"  aria-expanded="false" >
						<span class="sidebar-nav-icon">
							<Square3Stack3DIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"/>
						</span>
						<span class="sidebar-nav-name">
							Masterfile
						</span>
						<span class="sidebar-nav-end">
							<ChevronRightIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" />
						</span>
					</a>
					<Transition
						enter-active-class="transition ease-out duration-250"
						enter-from-class="opacity-0 h-1/2"
						enter-to-class="opacity-100 h-full"
						leave-active-class="transition ease-in duration-100"
						leave-from-class="opacity-100 h-full"
						leave-to-class="opacity-0 h-1/2"
					>
						<ul class="sidebar-sub-nav" v-show="masterfileDrop">
							<li class="sidebar-nav-item">
								<a href="/category" class="sidebar-nav-link">
									<span class="sidebar-nav-abbr text-gray-300">CT</span>
									<span class="sidebar-nav-name">
										Category
									</span>
								</a>
							</li>
							<li class="sidebar-nav-item">
								<a href="/group" class="sidebar-nav-link">
									<span class="sidebar-nav-abbr text-gray-300">GP</span>
									<span class="sidebar-nav-name">
										Group
									</span>
								</a>
							</li>
							<!-- <li class="sidebar-nav-item">
								<a href="/uom" class="sidebar-nav-link">
									<span class="sidebar-nav-abbr"></span>
									<span class="sidebar-nav-name">
										Unit of Measurement
									</span>
								</a>
							</li> -->
							<li class="sidebar-nav-item">
								<a href="/supplier" class="sidebar-nav-link">
									<span class="sidebar-nav-abbr text-gray-300">SP</span>
									<span class="sidebar-nav-name">
										Supplier
									</span>
								</a>
							</li>

							<li class="sidebar-nav-item">
								<a href="/department" class="sidebar-nav-link">
									<span class="sidebar-nav-abbr text-gray-300">DP</span>
									<span class="sidebar-nav-name">
										Department
									</span>
								</a>
							</li>

							<li class="sidebar-nav-item">
								<a href="/purpose" class="sidebar-nav-link">
									<span class="sidebar-nav-abbr text-gray-300">PP</span>
									<span class="sidebar-nav-name">
										Purpose
									</span>
								</a>
							</li>

							<li class="sidebar-nav-item">
								<a href="/enduse" class="sidebar-nav-link">
									<span class="sidebar-nav-abbr text-gray-300">EU</span>
									<span class="sidebar-nav-name">
										End Use
									</span>
								</a>
							</li>

							<li class="sidebar-nav-item">
								<a href="/warehouse" class="sidebar-nav-link">
									<span class="sidebar-nav-abbr text-gray-300">WH</span>
									<span class="sidebar-nav-name">
										Warehouse
									</span>
								</a>
							</li>

							<li class="sidebar-nav-item">
								<a href="/location" class="sidebar-nav-link">
									<span class="sidebar-nav-abbr text-gray-300">LO</span>
									<span class="sidebar-nav-name">
										Location
									</span>
								</a>
							</li>

							<li class="sidebar-nav-item">
								<a href="/rack" class="sidebar-nav-link">
									<span class="sidebar-nav-abbr text-gray-300">RC</span>
									<span class="sidebar-nav-name">
										Rack
									</span>
								</a>
							</li>
							
							<li class="sidebar-nav-item">
								<a href="/employees" class="sidebar-nav-link">
									<span class="sidebar-nav-abbr text-gray-300">EM</span>
									<span class="sidebar-nav-name">
										Employees
									</span>
								</a>
							</li>


							<li class="sidebar-nav-item">
								<a href="/item_status" class="sidebar-nav-link">
									<span class="sidebar-nav-abbr text-gray-300">IS</span>
									<span class="sidebar-nav-name">
										Item Status
									</span>
								</a>
							</li>

							<li class="sidebar-nav-item">
								<a href="/restock_reason" class="sidebar-nav-link">
									<span class="sidebar-nav-abbr text-gray-300">RR</span>
									<span class="sidebar-nav-name">
										Restock Reason
									</span>
								</a>
							</li>
							
							<!-- <li class="sidebar-nav-item">
								<a href="/import_items" class="sidebar-nav-link">
									<span class="sidebar-nav-abbr text-gray-300">II</span>
									<span class="sidebar-nav-name">
										Import Items
									</span>
								</a>
							</li> -->

						</ul>
					</Transition>
				</li>
				<li class="sidebar-nav-item">
					<a href="#" class="sidebar-nav-link" @click="receiveDrop = !receiveDrop"  aria-expanded="false">
						<span class="sidebar-nav-icon">
							<InboxArrowDownIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" />
						</span>
						<span class="sidebar-nav-name">
							Receive
						</span>
						<span class="sidebar-nav-end">
							<ChevronRightIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" />
						</span>
					</a>
					<Transition
						enter-active-class="transition ease-out duration-250"
						enter-from-class="opacity-0 h-1/2"
						enter-to-class="opacity-100 h-full"
						leave-active-class="transition ease-in duration-100"
						leave-from-class="opacity-100 h-full"
						leave-to-class="opacity-0 h-1/2"
					>
						<ul class="sidebar-sub-nav" v-show="receiveDrop">
							<li class="sidebar-nav-item">
								<a href="/receive/new" class="sidebar-nav-link">
									<span class="sidebar-nav-abbr text-gray-300">AN</span>
									<span class="sidebar-nav-name">
										Add New
									</span>
								</a>
							</li>
							<li class="sidebar-nav-item">
								<a href="/receive" class="sidebar-nav-link">
									<span class="sidebar-nav-abbr text-gray-300">SL</span>
									<span class="sidebar-nav-name">
										Show List
									</span>
								</a>
							</li>
						</ul>
					</Transition>
				</li>
				<li class="sidebar-nav-item">
					<a href="#" class="sidebar-nav-link" @click="uatDrop = !uatDrop"  aria-expanded="false">
						<span class="sidebar-nav-icon">
							<FolderIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" />
						</span>
						<span class="sidebar-nav-name">
							User Acceptance
						</span>
						<span class="sidebar-nav-end">
							<ChevronRightIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" />
						</span>
					</a>
					<Transition
						enter-active-class="transition ease-out duration-250"
						enter-from-class="opacity-0 h-1/2"
						enter-to-class="opacity-100 h-full"
						leave-active-class="transition ease-in duration-100"
						leave-from-class="opacity-100 h-full"
						leave-to-class="opacity-0 h-1/2"
					>
						<ul class="sidebar-sub-nav" v-show="uatDrop">
							<li class="sidebar-nav-item">
								<a href="/user_acceptance/pending" class="sidebar-nav-link">
									<span class="sidebar-nav-abbr text-gray-300">PE</span>
									<span class="sidebar-nav-name">
										Pending
									</span>
								</a>
							</li>
							<li class="sidebar-nav-item">
								<a href="/user_acceptance/accepted" class="sidebar-nav-link">
									<span class="sidebar-nav-abbr text-gray-300">CO</span>
									<span class="sidebar-nav-name">
										Completed
									</span>
								</a>
							</li>
						</ul>
					</Transition>
				</li>
				<li class="sidebar-nav-item">
					<a href="#" class="sidebar-nav-link" @click="requestDrop = !requestDrop"  aria-expanded="false">
						<span class="sidebar-nav-icon">
							<ClipboardDocumentIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" />
						</span>
						<span class="sidebar-nav-name">
							Request
						</span>
						<span class="sidebar-nav-end">
							<ChevronRightIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" />
						</span>
					</a>
					<Transition
						enter-active-class="transition ease-out duration-250"
						enter-from-class="opacity-0 h-1/2"
						enter-to-class="opacity-100 h-full"
						leave-active-class="transition ease-in duration-100"
						leave-from-class="opacity-100 h-full"
						leave-to-class="opacity-0 h-1/2"
					>
						<ul class="sidebar-sub-nav" v-show="requestDrop">
							<li class="sidebar-nav-item">
								<a href="/request/new" class="sidebar-nav-link">
									<span class="sidebar-nav-abbr text-gray-300">AN</span>
									<span class="sidebar-nav-name">
										Add New
									</span>
								</a>
							</li>
							<li class="sidebar-nav-item">
								<a href="/request" class="sidebar-nav-link">
									<span class="sidebar-nav-abbr text-gray-300">SL</span>
									<span class="sidebar-nav-name">
										Show List
									</span>
								</a>
							</li>
						</ul>
					</Transition>
				</li>
				<li class="sidebar-nav-item">
					<a href="#" class="sidebar-nav-link" @click="issueDrop = !issueDrop"  aria-expanded="false">
						<span class="sidebar-nav-icon">
							<ArrowUpOnSquareStackIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" />
						</span>
						<span class="sidebar-nav-name">
							Issue
						</span>
						<span class="sidebar-nav-end">
							<ChevronRightIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" />
						</span>
					</a>
					<Transition
						enter-active-class="transition ease-out duration-250"
						enter-from-class="opacity-0 h-1/2"
						enter-to-class="opacity-100 h-full"
						leave-active-class="transition ease-in duration-100"
						leave-from-class="opacity-100 h-full"
						leave-to-class="opacity-0 h-1/2"
					>
						<ul class="sidebar-sub-nav" v-show="issueDrop">
							<li class="sidebar-nav-item">
								<a href="/issue/new" class="sidebar-nav-link">
									<span class="sidebar-nav-abbr text-gray-300">AN</span>
									<span class="sidebar-nav-name">
										Add New
									</span>
								</a>
							</li>
							<li class="sidebar-nav-item">
								<a href="/issue" class="sidebar-nav-link">
									<span class="sidebar-nav-abbr text-gray-300">SL</span>
									<span class="sidebar-nav-name">
										Show List
									</span>
								</a>
							</li>
						</ul>
					</Transition>
				</li>
				<li class="sidebar-nav-item">
					<a href="#" class="sidebar-nav-link" @click="restockDrop = !restockDrop"  aria-expanded="false">
						<span class="sidebar-nav-icon">
							<ArchiveBoxArrowDownIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" />
						</span>
						<span class="sidebar-nav-name">
							Restock
						</span>
						<span class="sidebar-nav-end">
							<ChevronRightIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" />
						</span>
					</a>
					<Transition
						enter-active-class="transition ease-out duration-250"
						enter-from-class="opacity-0 h-1/2"
						enter-to-class="opacity-100 h-full"
						leave-active-class="transition ease-in duration-100"
						leave-from-class="opacity-100 h-full"
						leave-to-class="opacity-0 h-1/2"
					>
						<ul class="sidebar-sub-nav" v-show="restockDrop">
							<li class="sidebar-nav-item">
								<a href="/restock/new" class="sidebar-nav-link">
									<span class="sidebar-nav-abbr text-gray-300">AN</span>
									<span class="sidebar-nav-name">
										Add New
									</span>
								</a>
							</li>
							<li class="sidebar-nav-item">
								<a href="/restock" class="sidebar-nav-link">
									<span class="sidebar-nav-abbr text-gray-300">SL</span>
									<span class="sidebar-nav-name">
										Show List
									</span>
								</a>
							</li>
						</ul>
					</Transition>
				</li>
				<li class="sidebar-nav-item">
					<a href="#" class="sidebar-nav-link" @click="salesDrop = !salesDrop"  aria-expanded="false">
						<span class="sidebar-nav-icon">
							<ShoppingBagIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" />
						</span>
						<span class="sidebar-nav-name">
							Sales
						</span>
						<span class="sidebar-nav-end">
							<ChevronRightIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" />
						</span>
					</a>
					<Transition
						enter-active-class="transition ease-out duration-250"
						enter-from-class="opacity-0 h-1/2"
						enter-to-class="opacity-100 h-full"
						leave-active-class="transition ease-in duration-100"
						leave-from-class="opacity-100 h-full"
						leave-to-class="opacity-0 h-1/2"
					>
						<ul class="sidebar-sub-nav" v-show="salesDrop">
							<li class="sidebar-nav-item">
								<a href="/sales/new" class="sidebar-nav-link">
									<span class="sidebar-nav-abbr text-gray-300">AN</span>
									<span class="sidebar-nav-name">
										Add New
									</span>
								</a>
							</li>
							<li class="sidebar-nav-item">
								<a href="/sales" class="sidebar-nav-link">
									<span class="sidebar-nav-abbr text-gray-300">SL</span>
									<span class="sidebar-nav-name">
										Show List
									</span>
								</a>
							</li>
						</ul>
					</Transition>
				</li>
				<li class="sidebar-nav-item">
					<a href="#" class="sidebar-nav-link" @click="borrowDrop = !borrowDrop"  aria-expanded="false">
						<span class="sidebar-nav-icon">
							<Square2StackIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" />
						</span>
						<span class="sidebar-nav-name">
							Borrow
						</span>
						<span class="sidebar-nav-end">
							<ChevronRightIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" />
						</span>
					</a>
					<Transition
						enter-active-class="transition ease-out duration-250"
						enter-from-class="opacity-0 h-1/2"
						enter-to-class="opacity-100 h-full"
						leave-active-class="transition ease-in duration-100"
						leave-from-class="opacity-100 h-full"
						leave-to-class="opacity-0 h-1/2"
					>
						<ul class="sidebar-sub-nav" v-show="borrowDrop">
							<li class="sidebar-nav-item">
								<a href="/borrow/new" class="sidebar-nav-link">
									<span class="sidebar-nav-abbr text-gray-300">AN</span>
									<span class="sidebar-nav-name">
										Add New
									</span>
								</a>
							</li>
							<li class="sidebar-nav-item">
								<a href="/borrow" class="sidebar-nav-link">
									<span class="sidebar-nav-abbr text-gray-300">SL</span>
									<span class="sidebar-nav-name">
										Show List
									</span>
								</a>
							</li>
						</ul>
					</Transition>
				</li>
				<li class="sidebar-nav-item">
					<a href="#" class="sidebar-nav-link" @click="backorderDrop = !backorderDrop"  aria-expanded="false">
						<span class="sidebar-nav-icon">
							<ArrowPathRoundedSquareIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" />
						</span>
						<span class="sidebar-nav-name">
							Back Order
						</span>
						<span class="sidebar-nav-end">
							<ChevronRightIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" />
						</span>
					</a>
					<Transition
						enter-active-class="transition ease-out duration-250"
						enter-from-class="opacity-0 h-1/2"
						enter-to-class="opacity-100 h-full"
						leave-active-class="transition ease-in duration-100"
						leave-from-class="opacity-100 h-full"
						leave-to-class="opacity-0 h-1/2"
					>
						<ul class="sidebar-sub-nav" v-show="backorderDrop">
							<li class="sidebar-nav-item">
								<a href="/back_order/new/0" class="sidebar-nav-link">
									<span class="sidebar-nav-abbr text-gray-300">AN</span>
									<span class="sidebar-nav-name">
										Add New
									</span>
								</a>
							</li>
							<li class="sidebar-nav-item">
								<a href="/back_order" class="sidebar-nav-link">
									<span class="sidebar-nav-abbr text-gray-300">SL</span>
									<span class="sidebar-nav-name">
										Show List
									</span>
								</a>
							</li>
						</ul>
					</Transition>
				</li>
				<li class="sidebar-nav-item">
					<a href="#" class="sidebar-nav-link" @click="gatepassDrop = !gatepassDrop"  aria-expanded="false">
						<span class="sidebar-nav-icon">
							<TicketIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" />
						</span>
						<span class="sidebar-nav-name">
							Gatepass
						</span>
						<span class="sidebar-nav-end">
							<ChevronRightIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" />
						</span>
					</a>
					<Transition
						enter-active-class="transition ease-out duration-250"
						enter-from-class="opacity-0 h-1/2"
						enter-to-class="opacity-100 h-full"
						leave-active-class="transition ease-in duration-100"
						leave-from-class="opacity-100 h-full"
						leave-to-class="opacity-0 h-1/2"
					>
						<ul class="sidebar-sub-nav" v-show="gatepassDrop">
							<li class="sidebar-nav-item">
								<a href="/gatepass/new" class="sidebar-nav-link">
									<span class="sidebar-nav-abbr text-gray-300">AN</span>
									<span class="sidebar-nav-name">
										Add New
									</span>
								</a>
							</li>
							<li class="sidebar-nav-item">
								<a href="/gatepass" class="sidebar-nav-link">
									<span class="sidebar-nav-abbr text-gray-300">SL</span>
									<span class="sidebar-nav-name">
										Show List (Overall)
									</span>
								</a>
							</li>
							<!-- <li class="sidebar-nav-item">
								<a href="/gatepass/overall" class="sidebar-nav-link">
									<span class="sidebar-nav-abbr text-gray-300">SL</span>
									<span class="sidebar-nav-name">
										Overall
									</span>
								</a>
							</li> -->
							<li class="sidebar-nav-item">
								<a href="/gatepass/completed" class="sidebar-nav-link">
									<span class="sidebar-nav-abbr text-gray-300">SL</span>
									<span class="sidebar-nav-name">
										Completed
									</span>
								</a>
							</li>
							<li class="sidebar-nav-item">
								<a href="/gatepass/incomplete" class="sidebar-nav-link">
									<span class="sidebar-nav-abbr text-gray-300">SL</span>
									<span class="sidebar-nav-name">
										Incomplete
									</span>
								</a>
							</li>

						</ul>
					</Transition>
				</li>
				<li class="sidebar-nav-item">
					<a href="#" class="sidebar-nav-link" @click="reportsDrop = !reportsDrop"  aria-expanded="false">
						<span class="sidebar-nav-icon">
							<DocumentChartBarIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" />
						</span>
						<span class="sidebar-nav-name">
							Reports
						</span>
						<span class="sidebar-nav-end">
							<ChevronRightIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" />
						</span>
					</a>
					<Transition
						enter-active-class="transition ease-out duration-250"
						enter-from-class="opacity-0 h-1/2"
						enter-to-class="opacity-100 h-full"
						leave-active-class="transition ease-in duration-100"
						leave-from-class="opacity-100 h-full"
						leave-to-class="opacity-0 h-1/2"
					>
						<ul class="sidebar-sub-nav" v-show="reportsDrop">
							<li class="sidebar-nav-item">
								<a href="/reports/pr_overall" class="sidebar-nav-link">
									<span class="sidebar-nav-abbr text-gray-300">PR</span>
									<span class="sidebar-nav-name">
										PR Overall Report
									</span>
								</a>
							</li>
							<li class="sidebar-nav-item">
								<a href="/reports/variants" class="sidebar-nav-link">
									<span class="sidebar-nav-abbr text-gray-300">VR</span>
									<span class="sidebar-nav-name">
										Variants Report
									</span>
								</a>
							</li>
							<li class="sidebar-nav-item">
								<a href="/reports/pr_variants" class="sidebar-nav-link">
									<span class="sidebar-nav-abbr text-gray-300">PV</span>
									<span class="sidebar-nav-name">
										PR w/ Variants Report
									</span>
								</a>
							</li>
							<li class="sidebar-nav-item">
								<a href="/reports/stockcard" class="sidebar-nav-link">
									<span class="sidebar-nav-abbr text-gray-300">SC</span>
									<span class="sidebar-nav-name">
										Stockcard
									</span>
								</a>
							</li>
							<li class="sidebar-nav-item">
								<a href="#" class="sidebar-nav-link" @click="transactionDrop = !transactionDrop"  aria-expanded="false">
									<span class="sidebar-nav-abbr text-gray-300">TR</span>
									<span class="sidebar-nav-name">
										Transaction Reports
									</span>
									<span class="sidebar-nav-end">
										<ChevronRightIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" />
									</span>
								</a>
								<Transition
									enter-active-class="transition ease-out duration-250"
									enter-from-class="opacity-0 h-1/2"
									enter-to-class="opacity-100 h-full"
									leave-active-class="transition ease-in duration-100"
									leave-from-class="opacity-100 h-full"
									leave-to-class="opacity-0 h-1/2"
								>
									<ul class="sidebar-sub-nav p-0 pl-3" v-show="transactionDrop">
										<li class="sidebar-nav-item">
											<a href="/reports/tr_receive" class="sidebar-nav-link">
												<span class="sidebar-nav-abbr"></span>
												<span class="sidebar-nav-name">
													Receive
												</span>
											</a>
										</li>
										<li class="sidebar-nav-item">
											<a href="/reports/tr_issued" class="sidebar-nav-link">
												<span class="sidebar-nav-abbr"></span>
												<span class="sidebar-nav-name">
													Issued
												</span>
											</a>
										</li>
										<li class="sidebar-nav-item">
											<a href="/reports/tr_restock" class="sidebar-nav-link">
												<span class="sidebar-nav-abbr"></span>
												<span class="sidebar-nav-name">
													Restock
												</span>
											</a>
										</li>
										<li class="sidebar-nav-item">
											<a href="/reports/tr_borrow" class="sidebar-nav-link">
												<span class="sidebar-nav-abbr"></span>
												<span class="sidebar-nav-name">
													Borrow
												</span>
											</a>
										</li>
										<li class="sidebar-nav-item">
											<a href="/reports/tr_backorder" class="sidebar-nav-link">
												<span class="sidebar-nav-abbr"></span>
												<span class="sidebar-nav-name">
													Back Order
												</span>
											</a>
										</li>
									</ul>
								</Transition>
							</li>
						</ul>
					</Transition>
				</li>
			</ul>
    	</div>
		<div class="adminx-content">
			<div class="adminx-main-content h-full">
				<slot/>
			</div>
		</div>
    </div>
	<Transition name="fade">
        <div v-if="loading" class="fixed inset-0 flex items-center justify-center bg-gray-100 z-[9999]">
            <div class="flex flex-col items-center">
                <div class="loader mb-2"></div>
                <div class="text-lg font-semibold animate-pulse">Logging out...</div>
            </div>
        </div>
    </Transition>
</template>
