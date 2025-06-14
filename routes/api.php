<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\RackController;
use App\Http\Controllers\EmployeesController;
use App\Http\Controllers\DropdownController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\UomController;
use App\Http\Controllers\EnduseController;
use App\Http\Controllers\ItemCategoryController;
use App\Http\Controllers\ItemSubCategoryController;
use App\Http\Controllers\ItemStatusController;
use App\Http\Controllers\RestockReasonController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\PurposeController;
use App\Http\Controllers\ReceiveController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\IssuanceController;
use App\Http\Controllers\RestockController;
use App\Http\Controllers\BackorderController;
use App\Http\Controllers\BorrowController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ImportItemsController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\reminderController;
use App\Http\Controllers\GatepassController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::get('/constants', function () {
    return response()->json(config('constants'));
});
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/login_form', [AuthController::class,'login_form']);
Route::post('/login_process', [AuthController::class,'login_process']);
Route::get('/dashboard', [AuthController::class,'dashboard']);
Route::get('/backorder_list', [AuthController::class,'get_backorder_list']);
Route::get('/change_password', [AuthController::class,'change_password']);
Route::get('/create_credential',[AuthController::class,'create_credential']);
Route::post('/edit_password',[AuthController::class,'edit_password']);
Route::post('/add_reminder',[AuthController::class,'add_reminder']);
Route::get('/reminder_list', [AuthController::class,'get_reminder_list']);
Route::post('/done_reminder/{id}',[AuthController::class,'done_reminder']);
Route::get('/edit_reminder/{id}',[AuthController::class,'edit_reminder']);
Route::post('/update_reminder/{id}',[AuthController::class,'update_reminder']);
Route::get('/get_all_warehouse', [WarehouseController::class,'get_all_warehouse']);
Route::get('/search_warehouse',[WarehouseController::class,'search_warehouse']);
Route::get('/create_warehouse',[WarehouseController::class,'create_warehouse']);
Route::post('/add_warehouse',[WarehouseController::class,'add_warehouse']);
Route::get('/edit_warehouse/{id}',[WarehouseController::class,'edit_warehouse']);
Route::post('/update_warehouse/{id}',[WarehouseController::class,'update_warehouse']);
Route::get('/get_all_rack', [RackController::class,'get_all_rack']);
Route::get('/search_rack',[RackController::class,'search_rack']);
Route::get('/create_rack',[RackController::class,'create_rack']);
Route::post('/add_rack',[RackController::class,'add_rack']);
Route::get('/edit_rack/{id}',[RackController::class,'edit_rack']);
Route::post('/update_rack/{id}',[RackController::class,'update_rack']);
Route::get('/get_all_employees', [EmployeesController::class,'get_all_employees']);
Route::get('/search_employees',[EmployeesController::class,'search_employees']);
Route::get('/create_employees',[EmployeesController::class,'create_employees']);
Route::post('/add_employees',[EmployeesController::class,'add_employees']);
Route::get('/edit_employees/{id}',[EmployeesController::class,'edit_employees']);
Route::post('/update_employees/{id}',[EmployeesController::class,'update_employees']);
Route::get('/department_list',[DropdownController::class,'all_department']);
Route::get('/enduse_list',[DropdownController::class,'all_enduse']);
Route::get('/purpose_list',[DropdownController::class,'all_purpose']);

Route::get('/get_all_group', [GroupController::class,'get_all_group']);
Route::get('/search_group',[GroupController::class,'search_group']);
Route::get('/create_group',[GroupController::class,'create_group']);
Route::post('/add_group',[GroupController::class,'add_group']);
Route::get('/edit_group/{id}',[GroupController::class,'edit_group']);
Route::post('/update_group/{id}',[GroupController::class,'update_group']);

Route::get('/get_all_department', [DepartmentController::class,'get_all_department']);
Route::get('/search_department',[DepartmentController::class,'search_department']);
Route::get('/create_department',[DepartmentController::class,'create_department']);
Route::post('/add_department',[DepartmentController::class,'add_department']);
Route::get('/edit_department/{id}',[DepartmentController::class,'edit_department']);
Route::post('/update_department/{id}',[DepartmentController::class,'update_department']);

Route::get('/get_all_uom', [UomController::class,'get_all_uom']);
Route::get('/search_uom',[UomController::class,'search_uom']);
Route::get('/create_uom',[UomController::class,'create_uom']);
Route::post('/add_uom',[UomController::class,'add_uom']);
Route::get('/edit_uom/{id}',[UomController::class,'edit_uom']);
Route::post('/update_uom/{id}',[UomController::class,'update_uom']);

Route::get('/get_all_enduse', [EnduseController::class,'get_all_enduse']);
Route::get('/search_enduse',[EnduseController::class,'search_enduse']);
Route::get('/create_enduse',[EnduseController::class,'create_enduse']);
Route::post('/add_enduse',[EnduseController::class,'add_enduse']);
Route::get('/edit_enduse/{id}',[EnduseController::class,'edit_enduse']);
Route::post('/update_enduse/{id}',[EnduseController::class,'update_enduse']);

Route::get('/get_all_category', [ItemCategoryController::class,'get_all_category']);
Route::get('/search_category',[ItemCategoryController::class,'search_category']);
Route::get('/create_category',[ItemCategoryController::class,'create_category']);
Route::post('/add_category',[ItemCategoryController::class,'add_category']);
Route::get('/edit_category/{id}',[ItemCategoryController::class,'edit_category']);
Route::post('/update_category/{id}',[ItemCategoryController::class,'update_category']);

Route::get('/edit_subcat/{id}',[ItemSubCategoryController::class,'edit_subcat']);
Route::post('/update_subcat/{id}',[ItemSubCategoryController::class,'update_subcat']);

Route::get('/get_all_status', [ItemStatusController::class,'get_all_status']);
Route::get('/search_status',[ItemStatusController::class,'search_status']);
Route::get('/create_status',[ItemStatusController::class,'create_status']);
Route::post('/add_status',[ItemStatusController::class,'add_status']);
Route::get('/edit_status/{id}',[ItemStatusController::class,'edit_status']);
Route::post('/update_status/{id}',[ItemStatusController::class,'update_status']);

Route::get('/get_all_restockreason', [RestockReasonController::class,'get_all_restockreason']);
Route::get('/search_restockreason',[RestockReasonController::class,'search_restockreason']);
Route::get('/create_restockreason',[RestockReasonController::class,'create_restockreason']);
Route::post('/add_restockreason',[RestockReasonController::class,'add_restockreason']);
Route::get('/edit_restockreason/{id}',[RestockReasonController::class,'edit_restockreason']);
Route::post('/update_restockreason/{id}',[RestockReasonController::class,'update_restockreason']);
Route::get('/get_all_supplier', [SupplierController::class,'get_all_supplier']);
Route::get('/search_supplier',[SupplierController::class,'search_supplier']);
Route::get('/create_supplier',[SupplierController::class,'create_supplier']);
Route::post('/add_supplier',[SupplierController::class,'add_supplier']);
Route::get('/edit_supplier/{id}',[SupplierController::class,'edit_supplier']);
Route::post('/update_supplier/{id}',[SupplierController::class,'update_supplier']);

Route::get('/get_all_location', [LocationController::class,'get_all_location']);
Route::get('/search_location',[LocationController::class,'search_location']);
Route::get('/create_location',[LocationController::class,'create_location']);
Route::post('/add_location',[LocationController::class,'add_location']);
Route::get('/edit_location/{id}',[LocationController::class,'edit_location']);
Route::post('/update_location/{id}',[LocationController::class,'update_location']);

Route::get('/get_all_purpose', [PurposeController::class,'get_all_purpose']);
Route::get('/search_purpose',[PurposeController::class,'search_purpose']);
Route::get('/create_purpose',[PurposeController::class,'create_purpose']);
Route::post('/add_purpose',[PurposeController::class,'add_purpose']);
Route::get('/edit_purpose/{id}',[PurposeController::class,'edit_purpose']);
Route::post('/update_purpose/{id}',[PurposeController::class,'update_purpose']);

Route::get('/get_all_request', [RequestController::class,'get_all_request']);
Route::get('/search_request', [RequestController::class,'search_request']);
Route::get('/create_head',[RequestController::class,'create_head']);
Route::post('/add_head',[RequestController::class,'add_head']);
Route::get('/create_details/{id}',[RequestController::class,'create_details']);
Route::get('/create_details_wh/{id}',[RequestController::class,'create_details_wh']);
Route::get('/choose_prno/{prno}',[RequestController::class,'choose_prno']);
Route::post('/save_request/{id}',[RequestController::class,'save_request']);
Route::post('/save_request_wh/{id}',[RequestController::class,'save_request_wh']);
Route::get('/get_request_head/{id}',[RequestController::class,'get_request_head']);
Route::get('/get_request_items/{id}',[RequestController::class,'get_request_items']);
Route::get('/cancel_transaction_request/{id}', [RequestController::class,'cancel_transaction']);
Route::get('/choose_item/{item_id}',[RequestController::class,'choose_item']);
Route::get('/wh_item_list',[DropdownController::class,'wh_items']);
Route::get('/get_request_position/{id}',[RequestController::class,'get_request_position']);
Route::post('/add_request_signatory',[RequestController::class,'add_request_signatory']);
Route::get('/search_wh_variant',[RequestController::class,'search_wh_variant']);
Route::get('/search_whvariantqty/{variant_id}/{var_itemid}/{item_id}',[RequestController::class,'search_whvariantqty']);

Route::get('/create_receive_head', [ReceiveController::class,'create_receive_head']);
Route::post('/add_receive_head',[ReceiveController::class,'add_receive_head']);
Route::get('/get_receive_head/{id}',[ReceiveController::class,'get_receive_head']);
Route::get('/get_draft_count',[ReceiveController::class,'get_draft_count']);
Route::get('/get_PR_replenish/{id}/{pr}',[ReceiveController::class,'get_PR_replenish']);

Route::get('/create_receive_details/{id}/{detail_id}', [ReceiveController::class,'create_receive_details']);
Route::post('/save_draft_details/{id}',[ReceiveController::class,'save_draft_details']);
Route::get('/create_receive_items/{id}/{detail_id}', [ReceiveController::class,'create_receive_items']);
Route::get('/get_drafts', [ReceiveController::class,'get_drafts']);
Route::get('/search_drafts',[ReceiveController::class,'search_drafts']);
Route::get('/cancel_draft/{id}',[ReceiveController::class,'cancel_draft']);
Route::get('/get_max_detail/{id}',[ReceiveController::class,'get_max_detail']);
Route::post('/save_receive/{id}',[ReceiveController::class,'save_receive']);
Route::get('/delete_receive_item/{id}', [ReceiveController::class,'delete_receive_item']);
Route::get('/cancel_pr/{id}/{detail_id}', [ReceiveController::class,'cancel_pr']);
Route::get('/navigate/{id}/{detail_id}/{path}', [ReceiveController::class,'navigate']);
Route::get('/get_latest_detail_no/{id}',[ReceiveController::class,'get_latest_detail_no']);
Route::get('/get_receive_details/{id}', [ReceiveController::class,'get_receive_details']);
Route::post('/edit_receive_head/{id}',[ReceiveController::class,'edit_receive_head']);
Route::get('/close_transaction/{id}', [ReceiveController::class,'close_transaction']);
Route::get('/get_all_receive', [ReceiveController::class,'get_all_receive']);
Route::get('/cancel_transaction/{id}', [ReceiveController::class,'cancel_transaction']);
Route::get('/employee_list',[DropdownController::class,'all_users']);
Route::get('/get_all_position/{id}',[ReceiveController::class,'get_all_position']);
Route::post('/add_signatory_receive',[ReceiveController::class,'add_signatory']);
Route::get('/get_pr_existing/{pr}',[ReceiveController::class,'get_pr_existing']);
Route::get('/get_printables/{id}', [ReceiveController::class,'get_printables']);
Route::post('/password_checker',[ReceiveController::class,'password_checker']);
Route::post('/override_update/{id}',[ReceiveController::class,'override_update']);

Route::get('/supplier_list', [DropdownController::class,'all_supplier']);
Route::get('/brand_list', [DropdownController::class,'all_brand']);
Route::get('/pr_list', [DropdownController::class,'all_pr']);
Route::get('/pr_request_list', [DropdownController::class,'all_pr_request']);
Route::get('/pr_restock_list', [DropdownController::class,'all_pr_restock']);
Route::get('/status_list', [DropdownController::class,'all_status']);
Route::get('/catalog_list', [DropdownController::class,'all_catalog']);

Route::get('/get_all_items', [ItemController::class,'get_all_items']);
Route::get('/search_items',[ItemController::class,'search_items']);
Route::get('/search_colors',[ItemController::class,'search_colors']);
Route::get('/search_size',[ItemController::class,'search_size']);
Route::get('/search_uom',[ItemController::class,'search_uom']);
Route::get('/search_brand',[ItemController::class,'search_brand']);
Route::get('/search_variant',[ItemController::class,'search_variant']);
Route::get('/search_variantqty/{variant_id}/{item_id}',[ItemController::class,'search_variantqty']);
Route::get('/search_compositeqty/{variant_id}/{item_id}',[ItemController::class,'search_compositeqty']);
Route::get('/create_items',[ItemController::class,'create_items']);
Route::post('/add_items',[ItemController::class,'add_items']);
Route::post('/add_items_draft',[ItemController::class,'add_items_draft']);
Route::get('/edit_items/{id}',[ItemController::class,'edit_items']);
Route::get('/delete_composite/{id}/{clength}/{composeitemid}/{composite_qty}/{variant}/{index}/{item_id}',[ItemController::class,'delete_composite']);
Route::get('/check_composite/{id}',[ItemController::class,'check_composite']);
Route::get('/delete_variant/{id}/{vlength}/{itemid}/{quantity}',[ItemController::class,'delete_variant']);
Route::get('/delete_novariant/{id}',[ItemController::class,'delete_novariant']);
Route::post('/update_items/{id}',[ItemController::class,'update_items']);
Route::post('/update_composite_duplicate',[ItemController::class,'update_composite_duplicate']);
Route::post('/inventory_balance/{itemid}',[ItemController::class,'inventory_balance']);
Route::get('/choose_subcat/{subcat}',[ItemController::class,'choose_subcat']);
Route::get('/get_novariant/{item_id}',[ItemController::class,'get_novariant']);
Route::get('/category_list',[DropdownController::class,'all_category']);
Route::get('/get_subcategories/{id}',[DropdownController::class,'get_subcategories']);
Route::get('/subcategory_list',[DropdownController::class,'all_subcategory']);
Route::get('/location_list',[DropdownController::class,'all_location']);
Route::get('/warehouse_list',[DropdownController::class,'all_warehouse']);
Route::get('/rack_list',[DropdownController::class,'all_rack']);
Route::get('/group_list',[DropdownController::class,'all_group']);
Route::get('/uom_list',[DropdownController::class,'all_uom']);
Route::get('/item_list',[DropdownController::class,'all_item']);
Route::get('/item_variant_list',[DropdownController::class,'item_variant_list']);
Route::get('/item_list_composite',[DropdownController::class,'all_item_composite']);
Route::get('/suppliers_list',[DropdownController::class,'all_suppliers']);
Route::get('/itemstatus_list',[DropdownController::class,'all_itemstatus']);
Route::get('/export-begbal',[ImportItemsController::class,'exportBegbal']);
Route::get('/begbal_format', [ImportItemsController::class,'begbal_format']);
Route::post('/add_begbal',[ImportItemsController::class,'add_begbal']);
Route::get('/inventory_format', [ImportItemsController::class,'inventory_format']);
Route::post('/add_inventory',[ImportItemsController::class,'add_inventory']);
Route::post('/add_currentinventory',[ImportItemsController::class,'add_currentinventory']);

Route::get('/create_issuance_head', [IssuanceController::class,'create_issuance_head']);
Route::get('/get_request_data/{mreqf_no}', [IssuanceController::class,'get_request_data']);
Route::get('/get_all_issuance', [IssuanceController::class,'all_issuance']);
Route::get('/get_issuance_head/{id}',[IssuanceController::class,'get_issuance_head']);
Route::post('/add_signatory',[IssuanceController::class,'add_signatory']);
Route::post('/save_issuance',[IssuanceController::class,'save_issuance']);
Route::post('/add_signatory_gp',[IssuanceController::class,'add_signatory_gp']);

Route::get('/get_all_restocks', [RestockController::class,'get_all_restocks']);
Route::get('/create_restock_head', [RestockController::class,'create_restock_head']);
Route::get('/get_pr_details/{pr_no}', [RestockController::class,'get_pr_details']);
Route::post('/add_restock_head',[RestockController::class,'add_restock_head']);
Route::get('/get_restock_details/{id}/{source_pr}',[RestockController::class,'get_restock_details']);
Route::get('/all_restockreason',[DropdownController::class,'all_restockreason']);
Route::post('/save_restock',[RestockController::class,'save_restock']);
Route::post('/save_restock_wh',[RestockController::class,'save_restock_wh']);
Route::get('/cancel_transaction_restock/{id}',[RestockController::class,'cancel_transaction_restock']);
Route::get('/getshow_details/{id}', [RestockController::class,'getshow_details']);
Route::post('/add_signatory_restock',[RestockController::class,'add_signatory']);

Route::post('/filter_pr_overall', [ReportController::class,'filter_pr_overall']);
Route::post('/filter_item', [ReportController::class,'filter_item']);
Route::get('/get_all_backorder', [BackorderController::class,'get_all_backorder']);
Route::get('/po_list', [DropdownController::class,'all_po']);
Route::get('/get_receive_data/{pono}', [BackorderController::class,'get_receive_data']);
Route::get('/get_backorder_data/{id}', [BackorderController::class,'get_backorder_data']);
Route::post('/save_backorder',[BackorderController::class,'save_backorder']);
Route::get('/get_backorder_head/{id}',[BackorderController::class,'get_backorder_head']);
Route::get('/get_backorder_details/{id}', [BackorderController::class,'get_backorder_details']);
Route::post('/add_signatory_backorder',[BackorderController::class,'add_bo_signatory']);

Route::post('/filter_pr_overall', [ReportController::class,'filter_pr_overall']);
Route::post('/all_receive_transactions', [ReportController::class,'all_receive_transactions']);
Route::post('/filter_pr_variants', [ReportController::class,'filter_pr_variants']);

Route::get('/get_all_borrowed', [BorrowController::class,'get_all_borrowed']);
Route::get('/create_borrow_head', [BorrowController::class,'create_borrow_head']);
Route::get('/search_availablepr/{itemid}',[BorrowController::class,'search_availablepr']);
Route::post('/save_borrow',[BorrowController::class,'save_borrow']);
Route::get('/get_borrow_head/{id}',[BorrowController::class,'get_borrow_head']);
Route::get('/get_borrow_position/{id}',[BorrowController::class,'get_borrow_position']);
Route::get('/get_borrow_details/{id}',[BorrowController::class,'get_borrow_details']);
Route::get('/get_print_details/{id}',[BorrowController::class,'get_print_details']);
Route::post('/add_borrow_signatory',[BorrowController::class,'add_borrow_signatory']);

Route::post('/all_issued_transactions', [ReportController::class,'all_issued_transactions']);
Route::post('/all_restock_transactions', [ReportController::class,'all_restock_transactions']);
Route::post('/all_backorder_transactions', [ReportController::class,'all_backorder_transactions']);
Route::post('/all_borrowed_transactions', [ReportController::class,'all_borrowed_transactions']);
Route::post('/all_stockcard_transactions', [ReportController::class,'all_stockcard_transactions']);

Route::get('/get_all_reminders', [ReminderController::class,'reminderlist']);
Route::get('/create_reminder',[ReminderController::class,'create_reminder']);
Route::post('/addreminder',[ReminderController::class,'addreminder']);
Route::get('/editreminder/{id}',[ReminderController::class,'editreminder']);
Route::post('/updatereminder/{id}',[ReminderController::class,'updatereminder']);
Route::get('/delete_reminder/{id}', [ReminderController::class,'delete_reminder']);


Route::get('/receiveemp_list',[DropdownController::class,'receiveemp_list']);
Route::get('/notedemp_list',[DropdownController::class,'notedemp_list']);
Route::get('/acknowledgeemp_list',[DropdownController::class,'acknowledgeemp_list']);
Route::get('/approvedemp_list',[DropdownController::class,'approvedemp_list']);
Route::get('/requestedemp_list',[DropdownController::class,'requestedemp_list']);
Route::get('/releasedemp_list',[DropdownController::class,'releasedemp_list']);
Route::get('/reviewedemp_list',[DropdownController::class,'reviewedemp_list']);
Route::get('/deliveredemp_list',[DropdownController::class,'deliveredemp_list']);
Route::get('/inspectedemp_list',[DropdownController::class,'inspectedemp_list']);
Route::get('/returnedemp_list',[DropdownController::class,'returnedemp_list']);
Route::get('/recommendemp_list',[DropdownController::class,'recommendemp_list']);
Route::get('/acceptedemp_list',[DropdownController::class,'acceptedemp_list']);
Route::get('/rejectedemp_list',[DropdownController::class,'rejectedemp_list']);
Route::get('/get_itemname/{id}',[DropdownController::class,'get_itemname']);

Route::get('/stockcard_item/{variant_id}/{item_id}/{supplier_id}/{catalog_no}/{brand}/{department_id}', [ReportController::class,'stockcard_item']);
Route::get('/get_all_useracceptance', [ReceiveController::class,'get_all_useracceptance']);
Route::get('/get_all_useracceptance_dashboard', [ReceiveController::class,'get_all_useracceptance_dashboard']);
Route::get('/get_all_useracceptance_accepted', [ReceiveController::class,'get_all_useracceptance_accepted']);
Route::get('/get_all_useracceptance_rejected', [ReceiveController::class,'get_all_useracceptance_rejected']);
Route::post('/save_accepted',[ReceiveController::class,'save_accepted']);
Route::post('/save_rejected',[ReceiveController::class,'save_rejected']);
Route::post('/save_newaccepted',[ReceiveController::class,'save_newaccepted']);
Route::get('/get_all_backuseracceptance', [BackOrderController::class,'get_all_backuseracceptance']);
Route::get('/get_all_useracceptance_completed', [BackOrderController::class,'get_all_useracceptance_completed']);
Route::post('/save_backaccepted',[BackOrderController::class,'save_backaccepted']);
Route::post('/save_backrejected',[BackOrderController::class,'save_backrejected']);
Route::post('/save_newbackaccepted',[BackOrderController::class,'save_newbackaccepted']);
Route::get('/moq_display_dashboard', [ItemController::class,'moq_display_dashboard']);

Route::get('/create_gatepass_head',[GatepassController::class,'create_gatepass_head']);
Route::post('/add_gatepass_head',[GatepassController::class,'add_gatepass_head']);
Route::get('/gatepass_details/{id}',[GatepassController::class,'gatepass_details']);
Route::post('/save_gatepass_items/{id}',[GatepassController::class,'save_gatepass_items']);
Route::post('/save_gatepass_items_try/{id}',[GatepassController::class,'save_gatepass_items_try']);
Route::get('/get_all_gatepass', [GatepassController::class,'get_all_gatepass']);
Route::get('/get_completed_gatepass', [GatepassController::class,'get_completed_gatepass']);
Route::get('/get_incomplete_gatepass', [GatepassController::class,'get_incomplete_gatepass']);
Route::post('/add_signatory_gatepass',[GatepassController::class,'add_signatory']);
Route::post('/add_return_details',[GatepassController::class,'add_return_details']);
Route::get('/returned_details/{headid}',[GatepassController::class,'returned_details']);
Route::get('/cancel_gatepass/{id}', [GatepassController::class,'cancel_gatepass']);
Route::post('/update_remarks_head',[GatepassController::class,'update_remarks_head']);
Route::post('/insert_item',[GatepassController::class,'insert_item']);
Route::get('/remove_item/{id}', [GatepassController::class,'remove_item']);
Route::get('/item_details/{id}',[GatepassController::class,'item_details']);
Route::get('/get_returnable_items/{headid}',[GatepassController::class,'get_returnable_items']);
