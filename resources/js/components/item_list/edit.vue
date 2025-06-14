<script setup>
import navigation from '@/layouts/navigation.vue';
import {  EyeIcon,TrashIcon, PlusIcon, MagnifyingGlassIcon, CheckCircleIcon, ExclamationCircleIcon, ChevronRightIcon, ArrowUturnLeftIcon } from '@heroicons/vue/24/solid'
import { PhotoIcon, } from '@heroicons/vue/24/outline'
import { onMounted,ref,watch } from 'vue';
import { useRouter } from "vue-router";
const router = useRouter();
const choice = ref(false);
let form = ref({ id:'' })
let error = ref([]);
let error_items = ref([]);
let error_image = ref('');
let success = ref('')
let listitems=ref([]);
let listsubcategory=ref([]);
let category=ref([]);
let subcategory_id=ref([]);
let listlocation=ref([]);
let location=ref([]);
let listwarehouse=ref([]);
let warehouse=ref([]);
let listrack=ref([]);
let rack=ref([]);
let listgroup=ref([]);
let group=ref([]);
let listsupplier=ref([]);
let supplier=ref([]);
let liststatus=ref([]);
let composite_list=ref([]);
let variant_list=ref([]);
let novariant_list=ref([]);
let imageFile1=ref("");
let imageFile2=ref("");
let imageFile3=ref("");
let imageUrl1=ref("");
let imageUrl2=ref("");
let imageUrl3=ref("");
let colors=ref([]);
let sizes=ref([]);
let uom=ref([]);
let brand=ref([]);
let item_id=ref("");
let variant=ref([]);
let compositeqty=ref([]);
let totalcompqty=ref([]);
let begbal_checker=ref(0);
let copies=ref(1);
let checker=ref(0);
let begbal=ref(0);
let composite_qty=ref(0);
let currency=ref([]);
//let pr_no=ref('');
const props = defineProps({
	id:{
		type:String,
		default:''
	}
})
onMounted(async () => {
	getEditItems()
	getSubCategory()
	getLocation()
	getWarehouse()
	getRack()
	getGroup()
	getItem()
	getSupplier()
	getItemstatus()
})

const getEditItems = async () => {
	let response = await axios.get(`/api/edit_items/${props.id}`);
	form.value=response.data.items
	begbal_checker.value=response.data.begbal_checker
	category.value=response.data.category_name
	composite_list.value=response.data.composite
	variant_list.value=response.data.variants
	novariant_list.value=response.data.novariants
	checker.value=response.data.checker
	begbal.value=response.data.begbal
	composite_qty.value=response.data.composite_qty
	currency.value=response.data.currency
	// if(form.value.begbal!=0){
	// 	document.getElementById('beggining_balance').readOnly = true;
	// }else{
	// 	document.getElementById('beggining_balance').readOnly = false;
	// }
	//lastBox()
	for(var i = 0; i < composite_list.value.length;i++){
		loadComposite(i)
	}
}

const compositeQty = async (index,variant_id,item_id) => {
	for (var i = 0; i < variant_list.value.length; i++) {
		if(index==i){
			let response = await axios.get('/api/search_compositeqty/'+variant_id+'/'+item_id);
			if(response.data!=0){
				var compqty=parseFloat(response.data)
			}else{
				var compqty=0;
			}
			compositeqty.value[i] = compqty;
			totalcompqty.value[i] = parseFloat(variant_list.value[i].quantity) + compqty;
		}
	}
}

const lastBox = () => {
	const no_of_variant_list = variant_list.value.length  
	let lastBox = 0;
	for (let i = 0; i < no_of_variant_list; i++) {
		if(variant_list.value[i].supplier_id!=0 && variant_list.value[i].brand!='' && variant_list.value[i].quantity!=0 && variant_list.value[i].uom!='' && variant_list.value[i].item_status_id!='' && variant_list.value[i].receive_flag==0){
			lastBox++;
		}
	}
	return lastBox;
}

const getItem = async () => {
	//await new Promise(resolve => setTimeout(resolve, 5000));
	let response = await axios.get("/api/item_list_composite");
	listitems.value=response.data.items
}

const getSubCategory = async () => {
	//await new Promise(resolve => setTimeout(resolve, 5000));
	let response = await axios.get("/api/subcategory_list");
	listsubcategory.value=response.data.subcategory
}

const getLocation = async () => {
	//await new Promise(resolve => setTimeout(resolve, 5000));
	let response = await axios.get("/api/location_list");
	listlocation.value=response.data.location
	location.value=form.location_id
}

const getWarehouse = async () => {
	//await new Promise(resolve => setTimeout(resolve, 5000));
	let response = await axios.get("/api/warehouse_list");
	listwarehouse.value=response.data.warehouse
}

const getRack = async () => {
	//await new Promise(resolve => setTimeout(resolve, 5000));
	let response = await axios.get("/api/rack_list");
	listrack.value=response.data.rack
}

const getGroup = async () => {
	//await new Promise(resolve => setTimeout(resolve, 5000));
	let response = await axios.get("/api/group_list");
	listgroup.value=response.data.group
}

const getSupplier = async () => {
	//await new Promise(resolve => setTimeout(resolve, 5000));
	let response = await axios.get("/api/suppliers_list");
	listsupplier.value=response.data.suppliers
}

const getItemstatus = async () => {
	//await new Promise(resolve => setTimeout(resolve, 5000));
	let response = await axios.get("/api/itemstatus_list");
	liststatus.value=response.data.item_status
}

const chooseSubcat = async () => {
	var subcat = document.getElementById("subcategory").value;
	let response = await axios.get("/api/choose_subcat/"+subcat);
	category.value=response.data.category_name
	form.value.item_category_id=response.data.category_id
	form.value.pn_no=response.data.pn_no
}

const selectPass = (trigger,id,name) => {
	const select1 = document.getElementById(trigger);
	const selectedIndex1 = select1.selectedIndex;
	const selectedValue1 = select1.value;
	const selectedText1 = select1.options[selectedIndex1].text; 
	document.getElementById(id).innerHTML=selectedValue1;
	document.getElementById(name).innerHTML=selectedText1;
}

//Image 1
const upload_image1 = (event) => {
	let file = event.target.files[0];
	if(event.target.files.length===0){
		imageFile1.value='';
		imageUrl1.value='';
		return;
	}else if(file['size'] < 2111775){
		imageFile1.value = event.target.files[0];
		error_image.value=''
	}else{
		error_image.value='File size cannot be bigger than 2 MB'
	}
}
watch(imageFile1, (imageFile1) => {
	if(!(imageFile1 instanceof File)){
		return;
	}
	let fileReader1 = new FileReader();
	fileReader1.readAsDataURL(imageFile1)
	fileReader1.addEventListener("load", () => {
		imageUrl1.value=fileReader1.result
		document.getElementById('img1').src = fileReader1.result;
	})
})
//Image 1

//Image 2
const upload_image2 = (event) => {
	let file = event.target.files[0];
	if(event.target.files.length===0){
		imageFile2.value='';
		imageUrl2.value='';
		return;
	}else if(file['size'] < 2111775){
		imageFile2.value = event.target.files[0];
		error_image.value=''
	}else{
		error_image.value='File size cannot be bigger than 2 MB'
	}
}
watch(imageFile2, (imageFile2) => {
	if(!(imageFile2 instanceof File)){
		return;
	}
	let fileReader2 = new FileReader();
	fileReader2.readAsDataURL(imageFile2)
	fileReader2.addEventListener("load", () => {
		imageUrl2.value=fileReader2.result
		document.getElementById('img2').src = fileReader2.result;
	})
})
//Image 2

//Image 3
const upload_image3 = (event) => {
	let file = event.target.files[0];
	if(event.target.files.length===0){
		imageFile3.value='';
		imageUrl3.value='';
		return;
	}else if(file['size'] < 2111775){
		imageFile3.value = event.target.files[0];
		error_image.value=''
	}else{
		error_image.value='File size cannot be bigger than 2 MB'
	}
}
watch(imageFile3, (imageFile3) => {
	if(!(imageFile3 instanceof File)){
		return;
	}
	let fileReader3 = new FileReader();
	fileReader3.readAsDataURL(imageFile3)
	fileReader3.addEventListener("load", () => {
		imageUrl3.value=fileReader3.result
		document.getElementById('img3').src = fileReader3.result;
	})
})
//Image 3

const onEdit = (id) => {
	var location_id = document.getElementById("location_id").innerHTML;
	var location_description = document.getElementById("location_name").innerHTML;
	var warehouse_id = document.getElementById("warehouse_id").innerHTML;
	var warehouse_description = document.getElementById("warehouse_name").innerHTML;
	var rack_id = document.getElementById("rack_id").innerHTML;
	var rack_description = document.getElementById("rack_name").innerHTML;
	var group_id = document.getElementById("group_id").innerHTML;
	var group_description = document.getElementById("group_name").innerHTML;
	const formData=new FormData()
	// formData.append('composite',JSON.stringify(composite_list.value))
	// formData.append('variant',JSON.stringify(variant_list.value))
	// formData.append('novariant',JSON.stringify(novariant_list.value))
	formData.append('composite',JSON.stringify(composite_list.value))
	const no_of_composite = composite_list.value.length 
	error_items.value=[]
	for(var x=0;x<no_of_composite;x++){
		var inc=x+1;
		var copy = parseInt(copies.value) + 1
		var multiply_qty= copy * parseFloat(composite_list.value[x].quantity)
		//var multiply_qty= parseInt(form.value.begbal) * parseFloat(composite_list.value[x].quantity)
		if(multiply_qty > composite_list.value[x].checker_qty){ 
			error_items.value.push('Composite quantity is greater than warehouse stocks quantity (row '+inc+')')
		}
		if(composite_list.value[x].compose_item_id == ''){
			error_items.value.push('Item Description row '+inc+' must not be empty.')
		}
		if(composite_list.value[x].quantity == '' || composite_list.value[x].quantity == 0){
			error_items.value.push('Quantity row '+inc+' must not be empty.')
		}
	}
	formData.append('variant',JSON.stringify(variant_list.value))
	const no_of_variant = variant_list.value.length 
	for(var y=0;y<no_of_variant;y++){
		var inc=y+1;
		if(variant_list.value[y].supplier_id == ''){
			error_items.value.push('Supplier row '+inc+' must not be empty.')
		}
		// if(variant_list.value[y].brand == ''){
		// 	error_items.value.push('Brand row '+inc+' must not be empty.')
		// }
		if(variant_list.value[y].quantity == '' && variant_list.value[y].quantity == 0 && variant_list.value[y].receive_flag==0){
			error_items.value.push('Quantity row '+inc+' must not be empty.')
		}
		if(variant_list.value[y].uom == ''){
			error_items.value.push('UOM row '+inc+' must not be empty.')
		}
		if(variant_list.value[y].item_status_id == ''){
			error_items.value.push('Item Status row '+inc+' must not be empty.')
		}
		// if(variant_list.value[y].selling_price == ''){
		// 	error_items.value.push('Selling Price row '+inc+' must not be empty.')
		// }
	}
	// formData.append('novariant',JSON.stringify(novariant_list.value))
	// const no_of_novariant = novariant_list.value.length 
	// for(var y=0;y<no_of_novariant;y++){
	// 	var inc=y+1;
	// 	if(novariant_list.value[y].unit_cost == '' && composite_list.value.length<0 && variant_list.value.length<0){
	// 		error_items.value.push('Unit Cost row '+inc+' must not be empty.')
	// 	}
	// 	if(novariant_list.value[y].selling_price == '' && composite_list.value.length<0 && variant_list.value.length<0){
	// 		error_items.value.push('Selling Price row '+inc+' must not be empty.')
	// 	}
	// }
	formData.append('item_sub_category_id',form.value.item_sub_category_id)
	formData.append('item_category_id',form.value.item_category_id)
	formData.append('item_description',form.value.item_description ?? '')
	formData.append('pn_no',form.value.pn_no)
	formData.append('moq',form.value.moq ?? 0)
	formData.append('begbal',begbal.value ?? 0)
	// formData.append('begbal',form.value.begbal)
	formData.append('image1',imageFile1.value)
	formData.append('image2',imageFile2.value)
	formData.append('image3',imageFile3.value)
	formData.append('location_id',location_id)
	formData.append('location_description',location_description ?? '')
	formData.append('warehouse_id',warehouse_id)
	formData.append('warehouse_description',warehouse_description ?? '')
	formData.append('rack_id',rack_id)
	formData.append('rack_description',rack_description ?? '')
	formData.append('group_id',group_id)
	formData.append('group_description',group_description ?? '')
	formData.append('error_items',error_items.value.length)
	formData.append('copies',copies.value ?? 0)
	formData.append('composite_cost',form.value.composite_cost ?? 0)
	// formData.append('copies',form.value.copy_qty ?? 0)
	axios.post(`/api/update_items/`+id,formData).then(function (response) {
		if(error_items.value.length==0){

			//console.log(response)
			success.value='Successfully updated!'
			error.value=[]
			document.getElementById("success").style.display="block"
			document.getElementById("error").style.display="none"
			getEditItems()
			setTimeout(() => {
				document.getElementById("success").style.display="none"
			}, 4000);
		}
	}).catch(function(err){
		success.value=''
		error.value=[]
		if (err.response.data.errors.item_sub_category_id) {
			error.value.push(err.response.data.errors.item_sub_category_id[0])
			document.getElementById("error").style.display="block"
		}
		if (err.response.data.errors.item_category_id) {
			error.value.push(err.response.data.errors.item_category_id[0])
			document.getElementById("error").style.display="block"
		}
		if (err.response.data.errors.pn_no) {
			error.value.push(err.response.data.errors.pn_no[0])
			document.getElementById("error").style.display="block"
		}
		if (err.response.data.errors.item_description) {
			error.value.push(err.response.data.errors.item_description[0])
			document.getElementById("error").style.display="block"
		}
		if (err.response.data.errors.moq) {
			error.value.push(err.response.data.errors.moq[0])
			document.getElementById("error").style.display="block"
		}
		document.getElementById("success").style.display="none"
		setTimeout(() => {
			document.getElementById("error").style.display="none"
		}, 3000);
	});
}

const onSaveDraft = () => {
	var location_id = document.getElementById("location_id").value;
	var location_description = document.getElementById("location_name").value;
	var warehouse_id = document.getElementById("warehouse_id").value;
	var warehouse_description = document.getElementById("warehouse_name").value;
	var rack_id = document.getElementById("rack_id").value;
	var rack_description = document.getElementById("rack_name").value;
	var group_id = document.getElementById("group_id").value;
	var group_description = document.getElementById("group_name").value;
	const formData=new FormData()
	formData.append('composite',JSON.stringify(composite_list.value))
	// const no_of_composite = composite_list.value.length 
	// error_items.value=[]
	// for(var x=0;x<no_of_composite;x++){
	// 	var inc=x+1;
	// 	if(composite_list.value[x].item_description == ''){
	// 		error_items.value.push('Item Description row '+inc+' must not be empty.')
	// 	}
	// 	if(composite_list.value[x].quantity == ''){
	// 		error_items.value.push('Quantity row '+inc+' must not be empty.')
	// 	}
	// }
	formData.append('variant',JSON.stringify(variant_list.value))
	// const no_of_variant = variant_list.value.length 
	// for(var y=0;y<no_of_variant;y++){
	// 	var inc=y+1;
	// 	if(variant_list.value[y].supplier == ''){
	// 		error_items.value.push('Supplier row '+inc+' must not be empty.')
	// 	}
	// 	if(variant_list.value[y].brand == ''){
	// 		error_items.value.push('Brand row '+inc+' must not be empty.')
	// 	}
	// 	if(variant_list.value[y].quantity == ''){
	// 		error_items.value.push('Quantity row '+inc+' must not be empty.')
	// 	}
	// 	if(variant_list.value[y].unit_cost == ''){
	// 		error_items.value.push('Unit Cost row '+inc+' must not be empty.')
	// 	}
	// 	if(variant_list.value[y].selling_price == ''){
	// 		error_items.value.push('Selling Price row '+inc+' must not be empty.')
	// 	}
	// }
	//formData.append('novariant',JSON.stringify(novariant_list.value))
	// const no_of_novariant = novariant_list.value.length 
	// for(var y=0;y<no_of_novariant;y++){
	// 	var inc=y+1;
	// 	if(novariant_list.value[y].unit_cost == ''){
	// 		error_items.value.push('Unit Cost row '+inc+' must not be empty.')
	// 	}
	// 	if(novariant_list.value[y].selling_price == ''){
	// 		error_items.value.push('Selling Price row '+inc+' must not be empty.')
	// 	}
	// }
	if(form.value.item_sub_category_id!=undefined){
		formData.append('item_sub_category_id',form.value.item_sub_category_id)
	}else{
		formData.append('item_sub_category_id','0')
	}
	if(form.value.item_category_id!=undefined){
		formData.append('item_category_id',form.value.item_category_id)
	}else{
		formData.append('item_category_id','0')
	}
	formData.append('item_description',form.value.item_description)
	formData.append('pn_no',form.value.pn_no)
	formData.append('moq',form.value.moq ?? 0)
	// formData.append('begbal',form.value.begbal ?? 0)
	formData.append('begbal',begbal.value ?? 0)
	formData.append('image1',imageFile1.value)
	formData.append('image2',imageFile2.value)
	formData.append('image3',imageFile3.value)
	formData.append('location_id',location_id)
	formData.append('location_description',location_description ?? '')
	formData.append('warehouse_id',warehouse_id)
	formData.append('warehouse_description',warehouse_description ?? '')
	formData.append('rack_id',rack_id)
	formData.append('rack_description',rack_description ?? '')
	formData.append('group_id',group_id)
	formData.append('group_description',group_description ?? '')
	formData.append('error_items',error_items.value.length)
	formData.append('item_id',form.value.id)
	formData.append('copies',copies.value ?? 0)
	formData.append('composite_cost',form.value.composite_cost ?? 0)
	axios.post("/api/add_items_draft",formData).then(function (response) {
		if(error_items.value.length==0){
			error.value=[]
			success.value='Successfully saved as draft!'
			if(response.data.item_id_value==null){
				item_id.value=response.data.item_id
			}else{
				item_id.value=response.data.item_id_value
			}
			document.getElementById("success").style.display="block"
			document.getElementById("error").style.display="none"
			setTimeout(() => {
				document.getElementById("success").style.display="none"
			}, 4000);

			// form = ref([])
			// choice.value = 'novariant'
			// location.value=[]
			// warehouse.value=[]
			// rack.value=[]
			// group.value=[]
			// category.value=[]
			// composite_list.value=[]
			// variant_list.value=[]
			// novariant_list.value=[]
			// const novariants = {
			// 	serial_no:"",
			// 	expiration:"",
			// 	bar_code:"",
			// 	unit_cost:"",
			// 	selling_price:"",
			// 	item_status:"",
			// }
			// novariant_list.value.push(novariants)
		}
	}).catch(function(err){
		success.value=''
		error.value=[]
		if (err.response.data.errors.item_sub_category_id) {
			error.value.push(err.response.data.errors.item_sub_category_id[0])
			document.getElementById("error").style.display="block"
		}
		if (err.response.data.errors.item_category_id) {
			error.value.push(err.response.data.errors.item_category_id[0])
			document.getElementById("error").style.display="block"
		}
		if (err.response.data.errors.pn_no) {
			error.value.push(err.response.data.errors.pn_no[0])
			document.getElementById("error").style.display="block"
		}
		if (err.response.data.errors.item_description) {
			error.value.push(err.response.data.errors.item_description[0])
			document.getElementById("error").style.display="block"
		}
		if (err.response.data.errors.moq) {
			error.value.push(err.response.data.errors.moq[0])
			document.getElementById("error").style.display="block"
		}
		document.getElementById("success").style.display="none"
		setTimeout(() => {
			document.getElementById("error").style.display="none"
		}, 3000);
	});
}

const addRowComposite= () => {
	const composites = {
		id:0,
		item_id:form.value.id,
		variant_id:"",
		compose_item_id:"",
		quantity:"",
		checker_qty:"",
		pr_no:"",
	}
	composite_list.value.push(composites)
	if(composite_list.value.length>0){
		const variant = document.getElementById("variant");
		const begbal_disp = document.getElementById("begbal");
		//const novariant = document.getElementById("novariant");
		variant.disabled = true;
		begbal_disp.disabled = true;
		// form.value.begbal = 1;
		begbal.value = 1;
		document.getElementById("makecopies").style.display="block"
		//novariant.disabled = true;
	}
}
const removeComposite = (index) => {
	composite_list.value.splice(index,1)
	if(composite_list.value.length==0){
		const variant = document.getElementById("variant");
		//const novariant = document.getElementById("novariant");
		variant.disabled = false;
		//novariant.disabled = false;
	}
}

// const deleteComposite = (id,clength,itemid) => {
// 	var confirmation = confirm("Do you want to delete this composite?");
// 	if (confirmation == true) {
// 		axios.get(`/api/delete_composite/`+id+`/`+clength+`/`+itemid).then(function () {
// 			success.value='Successfully deleted!'
// 			error.value=[]
// 			getEditItems()
// 		}).catch(function(err){
// 			success.value=''
// 			error.value.push('Error! Try again.')
// 		});
// 	}
// }

const loadComposite = async (index) => {
	for (var i = 0; i < composite_list.value.length; i++) {
		if(index==i){
			// let response = await fetch('/api/search_variant?filter='+composite_list.value[i].compose_item_id);
			// variant.value[i] = await response.json();
			let response = await axios.get('/api/search_variant?filter='+composite_list.value[i].compose_item_id);
			variant.value[i] = response.data.items;
		}
	}
}
const deleteComposite = (id,clength,composeitemid,composite_qty,variant,index,item_id) => {
	var confirmation = confirm("Do you want to delete this composite?");
	if (confirmation == true) {
		axios.get(`/api/delete_composite/`+id+`/`+clength+`/`+composeitemid+`/`+composite_qty+`/`+variant+`/`+index+`/`+item_id).then(function (response) {
			success.value='Successfully deleted!'
			error.value=[]
			document.getElementById("success").style.display="block"
			document.getElementById("error").style.display="none"
			setTimeout(() => {
				document.getElementById("success").style.display="none"
			}, 4000);
			composite_list.value.splice(index,1)
			for(var i = 0;i<clength;i++){
				loadComposite(i)
				// composite_list.value[i].id = response.data.composite[i].id;
				// composite_list.value[i].item_description = response.data.composite[i].compose_item_id;
				// composite_list.value[i].variant = response.data.composite[i].variant_id;
				// composite_list.value[i].quantity = response.data.composite[i].quantity;
			}
			
		}).catch(function(err){
			success.value=''
			error.value.push('Error! Try again.')
			document.getElementById("success").style.display="none"
			setTimeout(() => {
				document.getElementById("error").style.display="none"
			}, 3000);
		});
	}
}

const addRowVariant= () => {
	const variants = {
		id:0,
		item_id:form.value.id,
		supplier_id:"",
		catalog_no:"",
		brand:"",
		serial_no:"",
		expiration:"",
		barcode:"",
		quantity:"",
		uom:"",
		color:"",
		size:"",
		composite_quantity:0,
		unit_cost:0,
		currency:"",
		average_cost:0,
		// selling_price:"",
		item_status_id:"",
	}
	variant_list.value.push(variants)
	if(variant_list.value.length>0){
		const composite = document.getElementById("composite");
		//const novariant = document.getElementById("novariant");
		composite.disabled = true;
		//novariant.disabled = true;
	}
}
const removeVariant = (index) => {
	variant_list.value.splice(index,1)
	if(variant_list.value.length==0){
		const composite = document.getElementById("composite");
		//const novariant = document.getElementById("novariant");
		composite.disabled = false;
		//novariant.disabled = false;
	}
}

const deleteVariant = (id,vlength,itemid,quantity) => {
	var confirmation = confirm("Do you want to delete this variant?");
	if (confirmation == true) {
		axios.get(`/api/delete_variant/`+id+`/`+vlength+`/`+itemid+`/`+quantity).then(function () {
			success.value='Successfully deleted!'
			error.value=[]
			getEditItems()
			setTimeout(() => {
				document.getElementById("success").style.display="none"
			}, 4000);
		}).catch(function(err){
			success.value=''
			error.value.push('Error! Try again.')
		});
	}
}

const addRowNovariant= () => {
	const novariants = {
		id:0,
		item_id:form.value.id,
		serial_no:"",
		expiration:"",
		barcode:"",
		quantity:"",
		unit_cost:"",
		selling_price:"",
		item_status_id:"",
	}
	novariant_list.value.push(novariants)
	if(novariant_list.value.length>0){
		const composite = document.getElementById("composite");
		const variant = document.getElementById("variant");
		composite.disabled = true;
		variant.disabled = true;
	}
}
const removeNovariant = (index) => {
	novariant_list.value.splice(index,1)
	if(novariant_list.value.length==0){
		const composite = document.getElementById("composite");
		const variant = document.getElementById("variant");
		composite.disabled = false;
		variant.disabled = false;
	}
}

const deleteNoVariant = (id) => {
	var confirmation = confirm("Do you want to delete this variant?");
	if (confirmation == true) {
		axios.get(`/api/delete_novariant/`+id).then(function () {
			success.value='Successfully deleted!'
			error.value=[]
			getEditItems()
		}).catch(function(err){
			success.value=''
			error.value.push('Error! Try again.')
		});
	}
}

const begbal_input = () => {
	var begbal = document.getElementById('beggining_balance').value;
	if(begbal!=0){
		document.getElementById('beggining_balance').readOnly = true;
	}else{
		document.getElementById('beggining_balance').readOnly = false;
	}
}

const autosuggestColor = async (index) => {
	for (var i = 0; i < variant_list.value.length; i++) {
		if(index==i){
			let response = await fetch('/api/search_colors?filter='+variant_list.value[i].color);
			colors.value = await response.json();
		}
	} 
}

const autosuggestSize = async (index) => {
	for (var i = 0; i < variant_list.value.length; i++) {
		if(index==i){
			let response = await fetch('/api/search_size?filter='+variant_list.value[i].size);
			sizes.value = await response.json();
		}
	}
}

const autosuggestUom = async (index) => {
	for (var i = 0; i < variant_list.value.length; i++) {
		if(index==i){
			let response = await fetch('/api/search_uom?filter='+variant_list.value[i].uom);
			uom.value = await response.json();
		}
	}
}

const autosuggestBrand = async (index) => {
	for (var i = 0; i < variant_list.value.length; i++) {
		if(index==i){
			let response = await fetch('/api/search_brand?filter='+variant_list.value[i].brand);
			brand.value = await response.json();
		}
	}
}

const checkVariant = async (index) => {
	for (var i = 0; i < composite_list.value.length; i++) {
		if(index==i){
			// let response = await fetch('/api/search_variant?filter='+composite_list.value[i].compose_item_id);
			// variant.value[i] = await response.json();
			// composite_list.value[i].quantity = '';
			let response = await axios.get('/api/search_variant?filter='+composite_list.value[i].compose_item_id);
			variant.value[i] = response.data.items;
			composite_list.value[i].pr_no='';
			if(response.data.begbal==0){
				composite_list.value[i].quantity = '';
				composite_list.value[i].checker_qty = '';
			}else{
				composite_list.value[i].quantity = response.data.begbal;
				composite_list.value[i].checker_qty = response.data.begbal;
			}
		}
	}
}

const checkVariantqty = async (index,variant_id,item_id) => {
	for (var i = 0; i < composite_list.value.length; i++) {
		if(index==i){
			//let response = await axios.get('/api/search_variantqty/'+composite_list.value[i].item_description);
			let response = await axios.get('/api/search_variantqty/'+variant_id+'/'+item_id);
			composite_list.value[i].quantity = response.data.quantity;
			composite_list.value[i].checker_qty = response.data.quantity;
			composite_list.value[i].pr_no = response.data.pr_no;
		}
	}
}

const checkQty = (index) => {
	totalcompqty.value[index] = parseFloat(variant_list.value[index].quantity) + parseFloat(compositeqty.value[index]);
}

const checkMaxqty = async (index) => {
	for (var i = 0; i < composite_list.value.length; i++) {
		if(index==i){
			var s = index+1;
			//const save = document.getElementById("save");
			// alert(composite_list.value[i].quantity)
			//const savedraft = document.getElementById("savedraft");
			if(composite_list.value[i].quantity > composite_list.value[i].checker_qty){
				error.value.push('Input quantity is greater than your variant quantity  (row '+s+')')
				document.getElementById("error").style.display="block"
				//save.disabled = true;
				//savedraft.disabled = true;
				setTimeout(() => {
					document.getElementById("error").style.display="none"
					error.value=[]
				}, 4000);
			}else{
				error.value=[]
				setTimeout(() => {
					document.getElementById("error").style.display="none"
				}, 4000);
				//save.disabled = false;
				//savedraft.disabled = false;
			}
		}
	}
}

const stockcardRedirect= (variant_id,item_id,supplier_id,catalog_no,brand) => {
	if(variant_id!=''){
		var variant=variant_id
	}else{
		var variant=0
	}

	if(item_id!=''){
		var item=item_id
	}else{
		var item=0
	}

	if(supplier_id!=''){
		var supplier=supplier_id
	}else{
		var supplier=0
	}

	if(catalog_no!=''){
		var catalog=catalog_no
	}else{
		var catalog='null'
	}
	if(brand!=''){
		var brand_id=brand
	}else{
		var brand_id='null'
	}
	router.push('/reports/stockcard_item/'+variant+'/'+item+'/'+supplier+'/'+catalog+'/'+brand_id+'/0')
}
</script>

<template>
	<div>
		<div class="col-lg-4 offset-lg-4">
			<div class="flex content-center">
				<div class="hide-animates" v-if="success" id="success">
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
				<div class="hide-animates"  v-if="error.length > 0" id="error">
					<div class="alert alert-danger alert-top my-2" >
						<div class="flex justify-start space-x-2">
							<div>
								<ExclamationCircleIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12"></ExclamationCircleIcon>
							</div> 
							<div class="py-1">
								<h6 class="font-bold m-0">Error!</h6>
								<p class="text-sm m-0 text-gray-400"  v-for="err in error"> {{ err }}</p>
							</div>
						</div>
					</div>
				</div>
				<div v-else id="error"></div>
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
							<a href="/item_list" class="btn btn-secondary btn-xs btn-rounded">
								<ArrowUturnLeftIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></ArrowUturnLeftIcon>
							</a>
						</div>
						<div>
							<h6 class="m-0 pt-1 font-bold uppercase">Item List</h6>
						</div>
					</div>	
					<div class="pt-1">	
						<nav aria-label="breadcrumb" role="navigation">
							<ol class="breadcrumb adminx-page-breadcrumb">
								<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
								<li class="breadcrumb-item"><a href="/item_list">Item List</a></li>
								<li class="breadcrumb-item active" aria-current="page" v-if="form.draft==0">Update Item</li>
								<li class="breadcrumb-item active" aria-current="page" v-else>Draft Item</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>	

			<div class="row">
				<div class="col-md-12 col-lg-12 ">
					<div class="card card-main-bg">
						<div class="p-2 pt-3 px-4">
							<div class="row">
								<div class="col-lg-12 px-1">
									<div class="flex justify-start mb-3">
										<div class="bg-blue-500 px-2 text-white space-x-1 py-1 rounded">
											<span class="text-sm font-bold">Running Balance :</span>
											<span class="font-bold">{{ form.running_balance }}</span>	
										</div>
									</div>
								</div> 
							</div>
							<div class="row">
								<div class="col-lg-6 px-1">
									<div class="form-group">
										<div class="flex justify-start ">
											<span class="text-xs text-gray-500 leading-none uppercase">Sub Category</span>
										</div>
										<select name="subcategory" id="subcategory" class="form-control border my-1" v-model="form.item_sub_category_id" @change="chooseSubcat()">
											<option :value="subcategory.id" v-for="subcategory in listsubcategory" :key="subcategory.id">{{ subcategory.subcat_name }}</option>
										</select>
									</div>										
								</div>
								<div class="col-lg-3 px-1">
									<div class="form-group">
										<div class="flex justify-start ">
											<span class="text-xs text-gray-500 leading-none uppercase">Category</span>
										</div>
										<input type="text" id="category" class="form-control border my-1" v-model="category" placeholder="Category" readonly>
										<input type="hidden" id="category_id" v-model="form.item_category_id" class="form-control border" placeholder="Category">
									</div>										
								</div>
								<div class="col-lg-3 px-1">
									<div class="form-group">
										<div class="flex justify-start ">
											<span class="text-xs text-gray-500 leading-none uppercase">PN No.</span>
										</div>
										<input type="text" id="pn_no" v-model="form.pn_no" class="form-control border my-1" placeholder="PN No.">
									</div>										
								</div>
							</div>
							<div class="row">
								<div class="col-lg-6 px-1">
									<div class="form-group">
										<div class="flex justify-start ">
											<span class="text-xs text-gray-500 leading-none uppercase">Item Description</span>
										</div>
										<input type="text" class="form-control border my-1" v-model="form.item_description" placeholder="Item Description">
									</div>										
								</div>
								<div class="col-lg-3 px-1">
									<div class="form-group">
										<div class="flex justify-start ">
											<span class="text-xs text-gray-500 leading-none uppercase">Location</span>
										</div>
										<select name="location" id="location" class="form-control border my-1" v-model="form.location_id" @change="selectPass('location','location_id','location_name')">
											<option :value="loc.id" v-for="loc in listlocation" :key="loc.id">{{ loc.location_name }}</option>
										</select>
										<span hidden id="location_id">{{form.location_id}}</span>
										<span hidden id="location_name">{{form.location_description}}</span>
									</div>											
								</div>
								<div class="col-lg-3 px-1">
									<div class="form-group">
										<div class="flex justify-start ">
											<span class="text-xs text-gray-500 leading-none uppercase">Warehouse</span>
										</div>
										<select name="warehouse" id="warehouse" class="form-control border my-1" v-model="form.warehouse_id" @change="selectPass('warehouse','warehouse_id','warehouse_name')">
											<option :value="wareh.id" v-for="wareh in listwarehouse" :key="wareh.id">{{ wareh.warehouse_name }}</option>
										</select>
										<span hidden id="warehouse_id">{{form.warehouse_id}}</span>
										<span hidden id="warehouse_name">{{form.warehouse_description}}</span>
									</div>										
								</div>
							</div>
							<div class="row">
								<div class="col-lg-3 px-1">
									<div class="form-group">
										<div class="flex justify-start ">
											<span class="text-xs text-gray-500 leading-none uppercase">Rack</span>
										</div>
										<select name="rack" id="rack" class="form-control border my-1" v-model="form.rack_id" @change="selectPass('rack','rack_id','rack_name')">
											<option :value="rck.id" v-for="rck in listrack" :key="rck.id">{{ rck.rack_name }}</option>
										</select>
										<span hidden id="rack_id">{{form.rack_id}}</span>
										<span hidden id="rack_name">{{form.rack_description}}</span>
									</div>										
								</div>
								<div class="col-lg-3 px-1">
									<div class="form-group">
										<div class="flex justify-start ">
											<span class="text-xs text-gray-500 leading-none uppercase">Group</span>
										</div>
										<select name="group" id="group" class="form-control border my-1" v-model="form.group_id" @change="selectPass('group','group_id','group_name')">
											<option :value="grp.id" v-for="grp in listgroup" :key="grp.id">{{ grp.group_name }}</option>
										</select>
										<span hidden id="group_id">{{form.group_id}}</span>
										<span hidden id="group_name">{{form.group_description}}</span>
									</div>										
								</div>
								<div class="col-lg-3 px-1">
									<div class="form-group">
										<div class="flex justify-start ">
											<span class="text-xs text-gray-500 leading-none uppercase">Minimum Order Qty</span>
										</div>
										<input type="text" class="form-control border my-1" v-model="form.moq" placeholder="Minimum Order Qty">
									</div>										
								</div>
								<div class="col-lg-3 px-1">
									<div class="form-group">
										<div class="flex justify-start ">
											<span class="text-xs text-gray-500 leading-none uppercase">Beginning Balance</span>
										</div>
										<!-- <input type="text" class="form-control border my-1" id="beggining_balance" v-model="form.begbal" placeholder="Beginning Balance" :disabled="begbal_checker!=0" v-if="form.draft==0">
										<input type="text" :disabled="composite_list.length!=0" class="form-control border my-1" id="beggining_balance" v-model="form.begbal" placeholder="Beginning Balance"  v-else> -->
										<input type="text" class="form-control border my-1" id="beggining_balance" v-model="begbal" placeholder="Beginning Balance" :disabled="begbal_checker!=0" v-if="form.draft==0">
										<input type="text" :disabled="composite_list.length!=0" class="form-control border my-1" id="beggining_balance" v-model="begbal" placeholder="Beginning Balance"  v-else>
										<!-- <input type="text" class="form-control border" v-model="form.begbal" placeholder="Beginning Balance" v-if="form.begbal==0">
										<input type="text" class="form-control border" v-model="form.begbal" placeholder="Beginning Balance" v-else readonly> -->
									</div>										
								</div>
								<!-- <div class="col-lg-2 px-1">
									<div class="form-group">
										<div class="flex justify-start ">
											<span class="text-xs text-gray-500 leading-none uppercase font-bold">Running Balance</span>
										</div>
										<input type="text" class="form-control border my-1 !bg-blue-500 text-white font-bold" value="999" disabled >
									</div>										
								</div> -->
							</div>
							<div class="row" id="makecopies" v-if="form.draft==1 && composite_list.length!=0">
								<div class="col-lg-3 px-1">
									<div class="form-group">
										<div class="flex justify-start ">
											<span class="text-xs text-gray-500 leading-none uppercase">Make Copies</span>
										</div>
										<input type="text" class="form-control border my-1" v-model="form.copy_qty" placeholder="Make Copies">
									</div>										
								</div>
							</div>
							<div id="makecopies" v-else-if="form.draft==0 && composite_list.length!=0">
								<div class="row">
									<div class="col-lg-3 px-1">
										<div class="form-group">
											<div class="flex justify-start ">
												<span class="text-xs text-gray-500 leading-none uppercase">Composite Cost</span>
											</div>
											<input type="text" class="form-control border my-1" v-model="form.composite_cost" placeholder="Composite Cost">
										</div>										
									</div>
								</div>
							</div>
							<div style="display: none;" id="makecopies" v-else></div>
							<div v-if="choice === 'composite' || composite_list.length!=0">
								<p class="text-danger" v-for="erritm in error_items" v-if="error_items.length > 0">{{ erritm }}</p>
								<div class="flex justify-start space-x-1 mt-2">
									<button id='composite' class="bg-gray-400 text-white btn-sm w-32 rounded-t-lg focus:outline-none" v-on:click="choice = 'composite'">Composite</button>
									<button  :disabled="form.composite_flag==1 || composite_list.length!=0" id='variant' class="bg-gray-300 text-white btn-sm w-32 rounded-t-lg focus:outline-none" v-on:click="choice = 'variant'">Variant</button>
									<!-- <button  :disabled="form.composite_flag==1 || composite_list.length!=0" id='novariant' class="bg-gray-300 text-white btn-sm w-32 rounded-t-lg focus:outline-none" v-on:click="choice = 'novariant'">No Variant</button> -->
								</div>
								<div class="row ">
									<div class="col-lg-12 px-1">
										<div class="border-gray-400 border-t-2">
											<table class="table table-bordered mb-0">
												<thead>
													<tr>
														<th class="font-xxs">Item Description</th>
														<th class="font-xxs">Variant</th>
														<th class="font-xxs">Quantity</th>
														<th class="p-1 font-xxs" align="center" width="1%">
															<div class="space-x-1 flex justify-center">
																<a href="#" @click="addRowComposite" class="btn btn-xs btn-primary btn-rounded">
																	<PlusIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></PlusIcon>
																</a>
															</div>
														</th>
													</tr>
												</thead>
												<tbody>
													<tr v-for="(c,index) in composite_list">
														<td class="p-0 text-xs">
															<select name="items" id="items" class="form-control border" v-model="c.compose_item_id" @change="checkVariant(index)" :disabled="c.id!=0">
																<option :value="item.id" v-for="item in listitems" :key="item.id">{{ item.item_description }}</option>
															</select>
														</td>
														<td class="p-0 text-xs">
															<select name="variant_comp" id="variant_comp" class="form-control border" v-model="c.variant_id" @change="checkVariantqty(index,c.variant_id,c.compose_item_id)" :disabled="c.id!=0">
																<option :value="v.id" v-for="v in variant[index]" :key="v.id">{{ v.supplier_name }}, {{ v.brand }}</option>
															</select>
															<input type="hidden" v-model="c.pr_no">
														</td>
														<td class="p-0 text-xs">
															<textarea type="text" rows="2" v-model="c.quantity" class="p-1 m-0 w-full leading-none block " :disabled="c.id!=0" @blur="checkMaxqty(index)"></textarea>
															<input type="hidden" v-model="c.checker_qty">
														</td>
														<td class="p-1  font-bold">
															<div class="p-0 space-x-1 flex justify-center">
																<button v-if="c.id" href="#" @click="deleteComposite(c.id,composite_list.length,c.compose_item_id,c.quantity,c.variant_id,index,c.item_id)" class="text-white btn btn-xs btn-danger btn-rounded" :disabled="c.quantity==0 || checker[index]==1">
																	<TrashIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></TrashIcon>
																</button>
																<button v-else href="#" @click="removeComposite(index)" class="text-white btn btn-xs btn-danger btn-rounded">
																	<TrashIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></TrashIcon>
																</button>
																<input v-model="c.id" type="hidden"/>
															</div>
														</td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>

							<div v-else-if="choice === 'variant' || variant_list.length!=0">
								<p class="text-danger" v-for="erritm in error_items" v-if="error_items.length > 0">{{ erritm }}</p>
								<div class="flex justify-start space-x-1 mt-2">
									<button :disabled="form.variant_flag==1 || variant_list.length!=0" id='composite' class="bg-gray-300 text-white btn-sm w-32 rounded-t-lg focus:outline-none" v-on:click="choice = 'composite'">Composite</button>
									<button id='variant' class="bg-gray-400 text-white btn-sm w-32 rounded-t-lg focus:outline-none" v-on:click="choice = 'variant'">Variant</button>
									<!-- <button :disabled="form.variant_flag==1 || variant_list.length!=0" id='novariant' class="bg-gray-300 text-white btn-sm w-32 rounded-t-lg focus:outline-none" v-on:click="choice = 'novariant'">No Variant</button> -->
								</div>
								<div class="row">
									<div class="col-lg-12 px-1">
										<div class="border-gray-400 border-t-2">
											<table class="table table-bordered mb-0">
												<thead>
													<tr>
														<th class="font-xxs" width="20%">Supplier</th>
														<th class="font-xxs">Catalog No</th>
														<th class="font-xxs">Brand</th>
														<th class="font-xxs">Serial No</th>
														<th class="font-xxs" width="5%">Expiration</th>
														<th class="font-xxs">Barcode</th>
														<th class="font-xxs" width="5%">Avail Qty</th>
														<th class="font-xxs" width="5%">Composite Qty</th>
														<th class="font-xxs" width="5%">Total Qty</th>
														<th class="font-xxs" width="5%">Unit Cost</th>
														<th class="font-xxs" width="5%">Currency</th>
														<th class="font-xxs" width="5%">Total Cost</th>
														<th class="font-xxs" width="5%">UOM</th>
														<th class="font-xxs" width="5%">Color</th>
														<th class="font-xxs" width="5%">Size</th>
														<!-- <th class="font-xxs" width="5%">Selling Price</th> -->
														<th class="font-xxs" width="10%">Item Status</th>
														<th class="font-xxs p-1 font-xxs" align="center" width="1%">
															<div class="space-x-1 flex justify-center">
																<a href="#" @click="addRowVariant" class="btn btn-xs btn-primary btn-rounded">
																	<PlusIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></PlusIcon>
																</a>
															</div>
														</th>
													</tr>
												</thead>
												<tbody>
													<tr v-for="(v,index) in variant_list">
														<td class="p-0 text-xs">
															<select name="supplier" id="supplier_id{{index}}" class="form-control border-none !text-xs py-1 supplier" v-model="v.supplier_id" :disabled="v.receive_flag==1 || v.composite_flag==1 || checker[index]==1">
																<option :value="sup.id" v-for="sup in listsupplier" :key="sup.id">{{ sup.supplier_name }}</option>
															</select>
														</td>
														<td class="p-0 text-xs"><textarea type="text" rows="2" class="p-1 m-0 w-full leading-none block catalog_no" v-model="v.catalog_no" id="catalog_no" :disabled="v.receive_flag==1 || v.composite_flag==1 || checker[index]==1"></textarea></td>
														<td class="p-0 text-xs">
															<!-- <textarea type="text" rows="2" class="p-1 m-0 w-full leading-none block " v-model="v.brand" :disabled="v.receive_flag==1 || v.composite_flag==1 || checker[index]==1"></textarea> -->
															<input type="text" rows="2" class="p-1 m-0 w-full leading-none block" @keyup="autosuggestBrand(index)" v-model="v.brand" list="brand_list" :disabled="v.receive_flag==1 || v.composite_flag==1 || checker[index]==1">
															<datalist id="brand_list">
																<option :value="b.brand" v-for="b in brand" :key="b.brand"></option>
															</datalist>
														</td>
														<td class="p-0 text-xs"><textarea type="text" rows="2" class="p-1 m-0 w-full leading-none block " v-model="v.serial_no" :disabled="v.receive_flag==1 || v.composite_flag==1 || checker[index]==1"></textarea></td>
														<td class="p-0 text-xs"><input type="date" rows="2" class="p-1 m-0 w-full leading-none block " v-model="v.expiration" :disabled="v.receive_flag==1 || v.composite_flag==1 || checker[index]==1" /></td>
														<td class="p-0 text-xs"><textarea type="text" rows="2" class="p-1 m-0 w-full leading-none block " v-model="v.barcode" :disabled="v.receive_flag==1 || v.composite_flag==1 || checker[index]==1"></textarea></td>
														<td class="p-0 text-xs"><textarea type="text" rows="2" class="p-1 m-0 w-full leading-none block " @blur="checkQty(index)" v-model="v.quantity" :disabled="v.receive_flag==1 || v.composite_flag==1 || checker[index]==1"></textarea></td>
														<td class="p-0 text-xs">
															<!-- <span hidden>{{ compositeQty(index,v.id,form.id) }}</span> -->
															<textarea type="number" rows="2" class="p-1 m-0 w-full leading-none block " v-model="composite_qty[index]" readonly v-if="v.id!=0"></textarea>
															<textarea type="number" rows="2" class="p-1 m-0 w-full leading-none block " value="0" readonly v-else></textarea>
														</td>
														<td class="p-0 text-xs">
															<textarea type="text" rows="2" class="p-1 m-0 w-full leading-none block " readonly v-if="v.id!=0">{{ v.quantity + composite_qty[index] }}</textarea>
															<textarea type="text" rows="2" class="p-1 m-0 w-full leading-none block " readonly v-else>{{ v.quantity }}</textarea>
														</td>
														<!-- <td class="p-0 text-xs"><textarea type="text" rows="2" class="p-1 m-0 w-full leading-none block " v-model="totalcompqty[index]" readonly></textarea></td> -->
														<td class="p-0 text-xs" v-if="v.receive_flag==1 || v.composite_flag==1 || checker[index]==1"><textarea type="text" rows="2" class="p-1 m-0 w-full leading-none block "  :disabled="v.receive_flag==1 || v.composite_flag==1 || checker[index]==1">{{ v.unit_cost + v.shipping_cost }}</textarea></td>
														<td class="p-0 text-xs" v-else><textarea type="text" rows="2" class="p-1 m-0 w-full leading-none block "  v-model="v.average_cost" :disabled="v.receive_flag==1 || v.composite_flag==1 || checker[index]==1"></textarea></td>
														<td class="p-0 text-xs">
															<select class="p-1 m-0 leading-none w-36 block text-xs whitespace-nowrap" v-model="v.currency">
																<option value="" disabled selected>Select Currency</option>
																<option v-for="cur in currency" v-bind:key="cur" v-bind:value="cur">{{  cur }}</option>
															</select>	
														</td>
														<td class="p-0 text-xs">
															<textarea v-if="v.receive_flag!=1 || v.composite_flag==1 || checker[index]==1" rows="2" class="p-1 m-0 w-full leading-none block "  :value="v.quantity * v.average_cost" readonly></textarea>
															<textarea v-else rows="2" class="p-1 m-0 w-full leading-none block "  :value="v.quantity * (v.unit_cost + v.shipping_cost)" readonly></textarea>
														</td>
														<td class="p-0 text-xs">
															<!-- <textarea type="text" rows="2" class="p-1 m-0 w-full leading-none block " v-model="variant.uom"></textarea> -->
															<input type="text" rows="2" class="p-1 m-0 w-full leading-none block" @keyup="autosuggestUom(index)" v-model="v.uom" list="uom_list" :disabled="v.receive_flag==1 || v.composite_flag==1 || checker[index]==1">
															<datalist id="uom_list">
																<option :value="u.uom" v-for="u in uom" :key="u.uom"></option>
															</datalist>
														</td>
														<td class="p-0 text-xs">
															<input type="text" rows="2" class="p-1 m-0 w-full leading-none block" @keyup="autosuggestColor(index)" v-model="v.color" list="colors_list" :disabled="v.receive_flag==1 || v.composite_flag==1 || checker[index]==1">
															<datalist id="colors_list">
																<option :value="c.color" v-for="c in colors" :key="c.color"></option>
															</datalist>
														</td>
														<td class="p-0 text-xs">
															<input type="text" rows="2" class="p-1 m-0 w-full leading-none block" @keyup="autosuggestSize(index)" v-model="v.size" list="sizes_list" :disabled="v.receive_flag==1 || v.composite_flag==1 || checker[index]==1">
															<datalist id="sizes_list">
																<option :value="s.size" v-for="s in sizes" :key="s.size"></option>
															</datalist>
														</td>
														<!-- <td class="p-0 text-xs"><textarea type="text" rows="2" class="p-1 m-0 w-full leading-none block " v-model="v.uom" :disabled="v.receive_flag==1 || v.composite_flag==1 || checker[index]==1"></textarea></td> -->
														<!-- <td class="p-0 text-xs"><textarea type="text" rows="2" class="p-1 m-0 w-full leading-none block " v-model="v.selling_price"></textarea></td> -->
														<td class="p-0 text-xs">
															<select name="item_status" id="item_status" class="form-control border-none !text-xs py-1" v-model="v.item_status_id" :disabled="v.receive_flag==1 || v.composite_flag==1 || checker[index]==1">
																<option :value="itemstatus.id" v-for="itemstatus in liststatus" :key="itemstatus.id">{{ itemstatus.status }}</option>
															</select>
														</td>
														<td class="p-1  font-bold">
															<div class="p-0 space-x-1 flex justify-center">
																<button v-if="v.id" @click="deleteVariant(v.id,variant_list.length,v.item_id,v.quantity)" class="text-white btn btn-xs btn-danger btn-rounded" :disabled="v.receive_flag==1 || v.composite_flag==1 || checker[index]==1">
																	<TrashIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></TrashIcon>
																</button>
																<a v-else href="#" @click="removeVariant(index)" class="text-white btn btn-xs btn-danger btn-rounded">
																	<TrashIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></TrashIcon>
																</a>
																<button @click="stockcardRedirect(v.id,v.item_id,v.supplier_id,v.catalog_no,v.brand,'0')" class="text-white btn btn-xs btn-warning btn-rounded">
																	<EyeIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></EyeIcon>
																</button>
																<input v-model="v.id" type="hidden"/>
															</div>
														</td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>

							<!-- <div v-else-if="choice === 'novariant' || novariant_list.length!=0">
								<p class="text-danger" v-for="erritm in error_items" v-if="error_items.length > 0">{{ erritm }}</p>
								<div class="flex justify-start space-x-1 mt-2">
									<button :disabled="form.novariant_flag==1 || novariant_list.length!=0" id='composite' class="bg-gray-300 text-white btn-sm w-32 rounded-t-lg focus:outline-none" v-on:click="choice = 'composite'">Composite</button>
									<button :disabled="form.novariant_flag==1 || novariant_list.length!=0" id='variant' class="bg-gray-300 text-white btn-sm w-32 rounded-t-lg focus:outline-none" v-on:click="choice = 'variant'">Variant</button>
									<button id='novariant' class="bg-gray-400 text-white btn-sm w-32 rounded-t-lg focus:outline-none" v-on:click="choice = 'novariant'">No Variant</button>
								</div>
								<div class="row">
									<div class="col-lg-12 px-1">
										<div class="border-gray-400 border-t-2">
											<table class="table table-bordered mb-0">
												<thead>
													<tr>
														<th class="font-xxs" width="5%">Unit Cost</th>
														<th class="font-xxs" width="5%">Selling Price</th>
														<th class="font-xxs" width="5%">Expiration</th>
														<th class="font-xxs" width="10%">Barcode</th>
														<th class="font-xxs" width="10%">Serial No</th> 
														<th class="font-xxs" width="10%">Item Status</th>
														<th class="font-xxs p-1 font-xxs" align="center" width="1%">
															<div class="space-x-1 flex justify-center">
																<a href="#" @click="addRowNovariant" class="btn btn-xs btn-primary btn-rounded">
																	<PlusIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></PlusIcon>
																</a>
															</div>
														</th>
													</tr>
												</thead>
												<tbody>
													<tr v-for="(nv,index) in novariant_list">
														<td class="p-0 text-xs"><textarea type="text" rows="2" class="p-1 m-0 w-full leading-none block " v-model="nv.unit_cost"></textarea></td>
														<td class="p-0 text-xs"><textarea type="text" rows="2" class="p-1 m-0 w-full leading-none block " v-model="nv.selling_price"></textarea></td>
														<td class="p-0 text-xs"><input type="date" rows="2" class="p-1 m-0 w-full leading-none block " v-model="nv.expiration" /></td>
														<td class="p-0 text-xs"><textarea type="text" rows="2" class="p-1 m-0 w-full leading-none block " v-model="nv.barcode"></textarea></td>
														<td class="p-0 text-xs"><textarea type="text" rows="2" class="p-1 m-0 w-full leading-none block " v-model="nv.serial_no"></textarea></td>
														<td class="p-0 text-xs">
															<select name="item_status" id="item_status" class="form-control border" v-model="nv.item_status_id">
																<option :value="itemstatus.id" v-for="itemstatus in liststatus" :key="itemstatus.id">{{ itemstatus.status }}</option>
															</select>
														</td>
														<td class="p-1  font-bold">
															<div class="p-0 space-x-1 flex justify-center">
																<a v-if="nv.id" href="#" @click="deleteNoVariant(nv.id)" class="text-white btn btn-xs btn-danger btn-rounded">
																	<TrashIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></TrashIcon>
																</a>
																<a v-else href="#" @click="removeNovariant(index)" class="text-white btn btn-xs btn-danger btn-rounded">
																	<TrashIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></TrashIcon>
																</a>
																<input v-model="nv.id" type="hidden"/>
															</div>
														</td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div> -->

							<div v-else>
								<div class="flex justify-start space-x-1 mt-2">
									<!-- <button class="bg-gray-300 text-white btn-sm w-32 rounded-t-lg focus:outline-none" v-on:click="choice = 'composite'">Composite</button>
									<button class="bg-gray-300 text-white btn-sm w-32 rounded-t-lg focus:outline-none" v-on:click="choice = 'variant'">Variant</button>
									<button class="bg-gray-300 text-white btn-sm w-32 rounded-t-lg focus:outline-none" v-on:click="choice = 'novariant'">No Variant</button> -->
									<button id='composite' class="bg-gray-300 text-white btn-sm w-32 rounded-t-lg focus:outline-none" v-on:click="choice = 'composite'">Composite</button>
									<button id='variant' class="bg-gray-300 text-white btn-sm w-32 rounded-t-lg focus:outline-none" v-on:click="choice = 'variant'">Variant</button>
									<!-- <button id='novariant' class="bg-gray-400 text-white btn-sm w-32 rounded-t-lg focus:outline-none" v-on:click="choice = 'novariant'">No Variant</button> -->
								</div>
							</div>
							
							<div class="row  mt-3">
								<div class="col-lg-12 px-1">
									<div class="mt-2 mb-2  border-b">
										<div class="flex justify-start space-x-2">
											<PhotoIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"></PhotoIcon>
											<h6>Upload Images</h6>	
										</div>
										<p class="text-danger" v-if='error_image'>{{ error_image }}</p>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-lg-4 px-1">
									<div class="form-group">
										<div class="flex justify-start ">
											<span class="text-xs text-gray-500 leading-none uppercase">Image 1</span>
										</div>
										<input type="file" accept="image/*" id="image1" @change="upload_image1" class="form-control border my-1">
										<div class="avatar img-fluid img-circle" style="margin-top:10px">
											<img :src="'/images/'+form.image1" id="img1" v-if="form.image1!=null"/>
											<img :src="'/images/default/default-img.jpg'" id="img1" v-else/>
										</div>
									</div>										
								</div>	
								<div class="col-lg-4 px-1">
									<div class="form-group">
										<div class="flex justify-start ">
											<span class="text-xs text-gray-500 leading-none uppercase">Image 2</span>
										</div>
										<input type="file" accept="image/*" id="image1" @change="upload_image2" class="form-control border my-1">
										<div class="avatar img-fluid img-circle" style="margin-top:10px">
											<img :src="'/images/'+form.image2" id="img2" v-if="form.image2!=null"/>
											<img :src="'/images/default/default-img.jpg'" id="img2" v-else/>
										</div>
									</div>										
								</div>	
								<div class="col-lg-4 px-1">
									<div class="form-group">
										<div class="flex justify-start ">
											<span class="text-xs text-gray-500 leading-none uppercase">Image 3</span>
										</div>
										<input type="file" accept="image/*" id="image1" @change="upload_image3" class="form-control border my-1">
										<div class="avatar img-fluid img-circle" style="margin-top:10px">
											<img :src="'/images/'+form.image3" id="img3" v-if="form.image3!=null"/>
											<img :src="'/images/default/default-img.jpg'" id="img3" v-else/>
										</div>
									</div>										
								</div>							
							</div>
							<!-- <div class="mt-2 mb-2 mt-4 border-b"></div>s -->
							<!-- <span hidden>{{ form.variant_flag==1 ? choice='variant' : form.composite_flag== 1 ? choice='composite' : form.novariant_flag==1 ? choice='novariant' : choice='false' }}</span> -->
							
							<div class="row">
								<div class="col-lg-12 px-1">
									<div class="pt-4 mt-4 border-dashed border-t flex justify-end" v-if="form.draft==0">
										<button @click="onEdit(form.id)" class="btn btn-sm btn-info  w-60" id="save">Update</button>
									</div>
									<div class="pt-3 mt-3 border-t border-dashed flex justify-end space-x-2" v-else>
										<!-- <input v-model="item_id" type="hidden"> -->
										<input v-model="form.id" type="hidden">
										<button @click="onSaveDraft()" class="btn btn-sm btn-warning btn- w-32 text-white" id="savedraft">Save As Draft</button>
										<button @click="onEdit(form.id)" class="btn btn-sm btn-primary btn- w-60" id="save">Save</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
    </navigation>
</template>
