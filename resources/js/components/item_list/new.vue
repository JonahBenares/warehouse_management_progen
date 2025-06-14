<script setup>
import navigation from '@/layouts/navigation.vue';
import {  TrashIcon, PlusIcon, XMarkIcon,CheckCircleIcon, ExclamationCircleIcon, ArrowUturnLeftIcon } from '@heroicons/vue/24/solid'
import { PhotoIcon, } from '@heroicons/vue/24/outline'
import axios from 'axios';
import {onMounted, ref, watch} from "vue";
import { useRouter } from "vue-router"
const router = useRouter()
const choice = ref('variant');
let form=ref([]);
let error = ref([]);
let error_image = ref('');
let error_items = ref([]);
let success = ref('')
let listsubcategory=ref([]);
let category=ref([]);
let subcategory_id=ref([]);
let listitems=ref([]);
let listlocation=ref([]);
let location=ref([]);
let listwarehouse=ref([]);
let warehouse=ref([]);
let listrack=ref([]);
let rack=ref([]);
let listgroup=ref([]);
let listuom=ref([]);
let group=ref([]);
let composite_list=ref([]);
let variant_list=ref([]);
let novariant_list=ref([]);
let listsupplier=ref([]);
let supplier=ref([]);
let liststatus=ref([]);
let imageFile1=ref("");
let imageFile2=ref("");
let imageFile3=ref("");
let imageUrl1=ref("");
let imageUrl2=ref("");
let imageUrl3=ref("");
let item_id=ref("");
let draft=ref("0");
let searchColors=ref([]);
let colors=ref([]);
let searchSize=ref([]);
let sizes=ref([]);
let uom=ref([]);
let brand=ref([]);
let variant=ref([]);
let showItemname=ref(false);
let closeItemname=ref(true);
let copies=ref("0");
let composite_cost=ref("0");
let itemlist=ref([])
let currency=ref([])
onMounted(async () => {
	itemForm()
	getSubCategory()
	getLocation()
	getWarehouse()
	getRack()
	getGroup()
	getUom()
	getItem()
	getSupplier()
	getItemstatus()
	chooseSubcat()
	getNovariant()
})

const closeModal = () => {
	showItemname.value = !closeItemname.value
}
const itemForm = async () => {
	let response = await axios.get("/api/create_items");
	form.value = response.data;
}

const getNovariant = async () => {
	if(form.value.length!=0){
		if(item_id.value!=''){
			var item_id=item_id.value
		}else{
			var item_id=0
		}
	}
}

const getItem = async () => {
	//await new Promise(resolve => setTimeout(resolve, 5000));
	let response = await axios.get("/api/item_list_composite");
	listitems.value=response.data.items
	currency.value=response.data.currency
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

const getUom = async () => {
	//await new Promise(resolve => setTimeout(resolve, 5000));
	let response = await axios.get("/api/uom_list");
	listuom.value=response.data.uom
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

// const upload_avatar = (e) => {
// 	let file = e.target.files[0];
// 	let reader = new FileReader();  

// 	if(file['size'] < 2111775)
// 	{
// 		reader.onloadend = (file) => {
// 		//console.log('RESULT', reader.result)
// 			form.avatar = reader.result;
// 		}              
// 			reader.readAsDataURL(file);
// 	}else{
// 		alert('File size can not be bigger than 2 MB')
// 	}
// }
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
		error_image.value='File size can not be bigger than 2 MB'
		imageUrl1.value='';
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
		error_image.value='File size can not be bigger than 2 MB'
		imageUrl2.value='';
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
		error_image.value='File size can not be bigger than 2 MB'
		imageUrl3.value='';
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
	})
})
//Image 3
const updateCompositeDuplicate = () => {
	const formData=new FormData()
	formData.append('itemlist',JSON.stringify(itemlist.value))
	axios.post(`/api/update_composite_duplicate`,formData).then(function (response) {
		error.value=[]
		success.value='Successfully updated!'
		document.getElementById("success").style.display="block"
		document.getElementById("error").style.display="none"
		setTimeout(() => {
			closeModal()
			document.getElementById("success").style.display="none"
		}, 2000);
		getItem()
	});
}

const onSave = () => {
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
	const no_of_composite = composite_list.value.length 
	error_items.value=[]
	for(var x=0;x<no_of_composite;x++){
		//alert(composite_list.value[x].variant)
		var inc=x+1;
		// var multiply_qty= parseInt(form.value.begbal) * parseFloat(composite_list.value[x].quantity)
		var copy = parseInt(copies.value) + 1
		var multiply_qty= copy * parseFloat(composite_list.value[x].quantity)
		if(multiply_qty > composite_list.value[x].checker_qty){ 
			error_items.value.push('Composite quantity is greater than warehouse stocks quantity (row '+inc+')')
		}
		if(composite_list.value[x].compose_item_id == ''){
			error_items.value.push('Item Description row '+inc+' must not be empty.')
		}
		// if(composite_list.value[x].variant_id == '' || composite_list.value[x].quantity==0){
		// 	error_items.value.push('Variant row '+inc+' must not be empty.')
		// }
		if(composite_list.value[x].quantity == ''){
			error_items.value.push('Quantity row '+inc+' must not be empty.')
		}
	}
	formData.append('variant',JSON.stringify(variant_list.value))
	const no_of_variant = variant_list.value.length 
	for(var y=0;y<no_of_variant;y++){
		var inc=y+1;
		if(variant_list.value[y].supplier == ''){
			error_items.value.push('Supplier row '+inc+' must not be empty.')
		}
		// if(variant_list.value[y].brand == ''){
		// 	error_items.value.push('Brand row '+inc+' must not be empty.')
		// }
		if(variant_list.value[y].quantity == ''){
			error_items.value.push('Quantity row '+inc+' must not be empty.')
		}
		if(variant_list.value[y].uom == ''){
			error_items.value.push('UOM row '+inc+' must not be empty.')
		}
		if(variant_list.value[y].item_status_id == ''){
			error_items.value.push('Item Status row '+inc+' must not be empty.')
		}
	}
	// formData.append('novariant',JSON.stringify(novariant_list.value))
	// const no_of_novariant = novariant_list.value.length 
	// for(var y=0;y<no_of_novariant;y++){
	// 	var inc=y+1;
	// 	if(novariant_list.value[y].unit_cost == ''  && composite_list.value.length<0 && variant_list.value.length<0){
	// 		error_items.value.push('Unit Cost row '+inc+' must not be empty.')
	// 	}
	// 	if(novariant_list.value[y].selling_price == ''  && composite_list.value.length<0 && variant_list.value.length<0){
	// 		error_items.value.push('Selling Price row '+inc+' must not be empty.')
	// 	}
	// 	if(novariant_list.value[y].item_status == ''  && composite_list.value.length<0 && variant_list.value.length<0){
	// 		error_items.value.push('Item Status row '+inc+' must not be empty.')
	// 	}
	// }
	formData.append('item_sub_category_id',form.value.item_sub_category_id ?? '')
	formData.append('item_category_id',form.value.item_category_id ?? '')
	formData.append('item_description',form.value.item_description)
	formData.append('pn_no',form.value.pn_no)
	formData.append('moq',form.value.moq ?? 0)
	formData.append('begbal',form.value.begbal ?? 0)
	formData.append('image1',imageFile1.value)
	formData.append('image2',imageFile2.value)
	formData.append('image3',imageFile3.value)
	formData.append('location_id',location_id ?? 0)
	formData.append('location_description',location_description ?? '')
	formData.append('warehouse_id',warehouse_id ?? 0)
	formData.append('warehouse_description',warehouse_description ?? '')
	formData.append('rack_id',rack_id ?? 0)
	formData.append('rack_description',rack_description ?? '')
	formData.append('group_id',group_id ?? 0)
	formData.append('group_description',group_description ?? '')
	formData.append('error_items',error_items.value.length)
	formData.append('copies',copies.value)
	formData.append('composite_cost',composite_cost.value)
	formData.append('item_id',item_id.value ?? 0)
	//showItemname.value = ref(true);
	if(item_id.value==''){
		var redirect="/api/add_items";
	}else{
		var redirect=`/api/update_items/`+item_id.value;
	}

	axios.post(redirect,formData).then(function (response) {
		if(error_items.value.length==0){
			// console.log(response.data);
			if(copies.value>0){
				itemlist.value=response.data;
				showItemname.value = ref(true);
			}
			item_id.value='';
			draft.value=0;
			error.value=[]
			success.value='You have successfully added new item!'
			form = ref([])
			//choice.value = 'novariant'
			location.value=[]
			warehouse.value=[]
			rack.value=[]
			group.value=[]
			category.value=[]
			composite_list.value=[]
			variant_list.value=[]
			//novariant_list.value=[]
			imageUrl1.value=""
			imageUrl2.value=""
			imageUrl3.value=""
			copies.value=""
			form.value.begbal=""
			const composite = document.getElementById("composite");
			composite.disabled = false;
			const variant = document.getElementById("variant");
			variant.disabled = false;
			const begbal = document.getElementById("begbal");
			begbal.disabled = false;
			form.value.begbal = '';
			document.getElementById("makecopies").style.display="none"
			// const novariants = {
			// 	serial_no:"",
			// 	expiration:"",
			// 	bar_code:"",
			// 	unit_cost:"",
			// 	selling_price:"",
			// 	item_status:"",
			// }
			// novariant_list.value.push(novariants)
			document.getElementById("success").style.display="block"
			document.getElementById("error").style.display="none"
			setTimeout(() => {
				document.getElementById("success").style.display="none"
				//window.location.reload()
			}, 4000);
			getItem()
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
	const no_of_composite = composite_list.value.length 
	error_items.value=[]
	for(var x=0;x<no_of_composite;x++){
		var inc=x+1;
		//var multiply_qty= parseInt(form.value.begbal) * parseFloat(composite_list.value[x].quantity)
		var copy = parseInt(copies.value) + 1
		var multiply_qty= copy * parseFloat(composite_list.value[x].quantity)
		if(multiply_qty > composite_list.value[x].checker_qty){ 
			error_items.value.push('Composite quantity is greater than warehouse stocks quantity (row '+inc+')')
		}
		// if(composite_list.value[x].item_description == ''){
		// 	error_items.value.push('Item Description row '+inc+' must not be empty.')
		// }
		// if(composite_list.value[x].quantity == ''){
		// 	error_items.value.push('Quantity row '+inc+' must not be empty.')
		// }
	}
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
	formData.append('item_description',form.value.item_description ?? '')
	formData.append('pn_no',form.value.pn_no ?? '')
	formData.append('moq',form.value.moq ?? 0)
	formData.append('begbal',form.value.begbal ?? 0)
	formData.append('image1',imageFile1.value)
	formData.append('image2',imageFile2.value)
	formData.append('image3',imageFile3.value)
	formData.append('location_id',location_id ?? 0)
	formData.append('location_description',location_description ?? '')
	formData.append('warehouse_id',warehouse_id ?? 0)
	formData.append('warehouse_description',warehouse_description ?? '')
	formData.append('rack_id',rack_id ?? 0)
	formData.append('rack_description',rack_description ?? '')
	formData.append('group_id',group_id ?? 0)
	formData.append('group_description',group_description ?? '')
	formData.append('error_items',error_items.value.length)
	formData.append('item_id',item_id.value ?? 0)
	formData.append('copies',copies.value)
	formData.append('composite_cost',composite_cost.value)
	axios.post("/api/add_items_draft",formData).then(function (response) {
		if(error_items.value.length==0){
			draft.value=1;
			error.value=[]
			success.value='Successfully saved as draft!'
			if(response.data.item_id_value==null){
				item_id.value=response.data.item_id
			}else{
				item_id.value=response.data.item_id_value
			}
			for(var i = 0;i<composite_list.value.length;i++){
				composite_list.value[i].id = response.data.composite[i].id;
				composite_list.value[i].item_description = response.data.composite[i].compose_item_id;
				composite_list.value[i].variant = response.data.composite[i].variant_id;
				if(form.value.begbal==0){
					composite_list.value[i].quantity = response.data.composite[i].quantity;
				}else{
					composite_list.value[i].quantity = response.data.composite[i].quantity * form.value.begbal;
				}
				loadComposite(i)
			}

			for(var z=0;z<variant_list.value.length;z++){
				variant_list.value[z].id = response.data.variants[z].id;
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
		item_id:0,
		compose_item_id:"",
		variant_id:"",
		quantity:"",
		checker_qty:"",
		pr_no:"",
	}
	composite_list.value.push(composites)
	if(composite_list.value.length>0){
		
		const variant = document.getElementById("variant");
		const begbal = document.getElementById("begbal");
		//const novariant = document.getElementById("novariant");
		variant.disabled = true;
		begbal.disabled = true;
		form.value.begbal = 1;
		document.getElementById("makecopies").style.display="block"
		//novariant.disabled = true;
	}
}
const removeComposite = (index) => {
	composite_list.value.splice(index,1)
	if(composite_list.value.length==0){
		const variant = document.getElementById("variant");
		const begbal = document.getElementById("begbal");
		//const novariant = document.getElementById("novariant");
		variant.disabled = false;
		begbal.disabled = false;
		form.value.begbal = 0;
		document.getElementById("makecopies").style.display="none"
		//novariant.disabled = false;
	}
}

const addRowVariant= () => {
	const variants = {
		id:0,
		item_id:item_id.value,
		supplier_id:"",
		catalog_no:"",
		brand:"",
		serial_no:"",
		expiration:"",
		barcode:"",
		unit_cost:0,
		currency:"",
		quantity:"",
		composite_quantity:0,
		uom:"",
		color:"",
		size:"",
		item_status_id:"",
	}
	variant_list.value.push(variants)
	colors.value=[]
	sizes.value=[]
	brand.value=[]
	uom.value=[]
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

//NO VARIANT
// if(variant_list.value.length>0 || composite_list.value.length>0){
// 	novariant_list.value=[];
// }else{
// 	const novariants = {
// 		id:0,
// 		serial_no:"",
// 		expiration:"",
// 		bar_code:"",
// 		unit_cost:0,
// 		selling_price:0,
// 		item_status:0,
// 	}
// 	novariant_list.value.push(novariants)
// }
const addRowNovariant= () => {
	const novariants = {
		id:0,
		serial_no:"",
		expiration:"",
		bar_code:"",
		unit_cost:0,
		selling_price:0,
		item_status:0,
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
//NO VARIANT
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
			//let response = await fetch('/api/search_variant?filter='+composite_list.value[i].compose_item_id);
			//variant.value[i] = await response.json();
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

const checkMaxqty = async (index) => {
	for (var i = 0; i < composite_list.value.length; i++) {
		if(index==i){
			var s = index+1;
			const save = document.getElementById("save");
			const savedraft = document.getElementById("savedraft");
			if(composite_list.value[i].quantity > composite_list.value[i].checker_qty){
				error.value.push('Input quantity is greater than your variant quantity  (row '+s+')')
				document.getElementById("error").style.display="block"
				save.disabled = true;
				savedraft.disabled = true;
				setTimeout(() => {
					document.getElementById("error").style.display="none"
					error.value=[]
				}, 4000);
			}else{
				error.value=[]
				setTimeout(() => {
					document.getElementById("error").style.display="none"
				}, 4000);
				save.disabled = false;
				savedraft.disabled = false;
			}
		}
	}
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
								<li class="breadcrumb-item active" aria-current="page">Add New Item</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>	

			<div class="row">
				<div class="col-md-12 col-lg-12 ">
					<div class="card card-main-bg">
						<div class="p-2 pt-4 px-4">
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
										<select name="location" id="location" class="form-control border my-1" v-model="location">
											<option :value="loc" v-for="loc in listlocation" :key="loc.id">{{ loc.location_name }}</option>
										</select>
										<input type="hidden" id="location_id" :value="location.id">
										<input type="hidden" id="location_name" :value="location.location_name">
									</div>										
								</div>
								<div class="col-lg-3 px-1">
									<div class="form-group">
										<div class="flex justify-start ">
											<span class="text-xs text-gray-500 leading-none uppercase">Warehouse</span>
										</div>
										<select name="warehouse" id="warehouse" class="form-control border my-1" v-model="warehouse">
											<option :value="wareh" v-for="wareh in listwarehouse" :key="wareh.id">{{ wareh.warehouse_name }}</option>
										</select>
										<input type="hidden" id="warehouse_id" :value="warehouse.id">
										<input type="hidden" id="warehouse_name" :value="warehouse.warehouse_name">
									</div>										
								</div>
							</div>
							<div class="row">
								<div class="col-lg-3 px-1">
									<div class="form-group">
										<div class="flex justify-start ">
											<span class="text-xs text-gray-500 leading-none uppercase">Rack</span>
										</div>
										<select name="rack" id="rack" class="form-control border my-1" v-model="rack">
											<option :value="rck" v-for="rck in listrack" :key="rck.id">{{ rck.rack_name }}</option>
										</select>
										<input type="hidden" id="rack_id" :value="rack.id">
										<input type="hidden" id="rack_name" :value="rack.rack_name">
									</div>										
								</div>
								<div class="col-lg-3 px-1">
									<div class="form-group">
										<div class="flex justify-start ">
											<span class="text-xs text-gray-500 leading-none uppercase">Group</span>
										</div>
										<select name="group" id="group" class="form-control border my-1" v-model="group">
											<option :value="grp" v-for="grp in listgroup" :key="grp.id">{{ grp.group_name }}</option>
										</select>
										<input type="hidden" id="group_id" :value="group.id">
										<input type="hidden" id="group_name" :value="group.group_name">
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
										<input type="text" id="begbal" class="form-control border my-1" v-model="form.begbal" placeholder="Beginning Balance">
									</div>										
								</div>
							</div>
							<div  style="display: none;" id="makecopies">
								<div class="row">
									<div class="col-lg-3 px-1">
										<div class="form-group">
											<div class="flex justify-start ">
												<span class="text-xs text-gray-500 leading-none uppercase">Make Copies</span>
											</div>
											<input type="text" class="form-control border my-1" v-model="copies" placeholder="Make Copies">
										</div>										
									</div>
									<div class="col-lg-3 px-1">
										<div class="form-group">
											<div class="flex justify-start ">
												<span class="text-xs text-gray-500 leading-none uppercase">Composite Cost</span>
											</div>
											<input type="text" class="form-control border my-1" v-model="composite_cost" placeholder="Composite Cost">
										</div>										
									</div>
								</div>
							</div>
							
							<div v-if="choice === 'composite'">
								<div class="alert alert-warning2 my-2 show-animate border-0 shadow-sm" v-if="error_items.length > 0">
									<div class="flex justify-stsart space-x-2">
										<div class="text-yellow-600">
											<ExclamationCircleIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"></ExclamationCircleIcon>
										</div> 
										<div>
											<div class="text-yellow-600" v-for="erritm in error_items">{{ erritm }} </div>
										</div>
									</div>
								</div>
								<div class="flex justify-start space-x-1 mt-2">
									<button id="composite" class="bg-gray-400 text-white btn-sm w-32 rounded-t-lg focus:outline-none" v-on:click="choice = 'composite'">Composite</button>
									<button id="variant" class="bg-gray-300 text-white btn-sm w-32 rounded-t-lg focus:outline-none" v-on:click="choice = 'variant'">Variant</button>
									<!-- <button id="novariant" class="bg-gray-300 text-white btn-sm w-32 rounded-t-lg focus:outline-none" v-on:click="choice = 'novariant'">No Variant</button> -->
								</div>
								<div class="row ">
									<div class="col-lg-12 px-1">
										<div class="border-gray-400 border-t-2 shadow-sm">
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
													<tr v-for="(comp, index) in composite_list">
														<td class="p-0 text-xs">
															<input type="hidden" v-model="comp.id">
															<!-- <textarea type="text" v-model="comp.item_description" rows="2" class="p-1 m-0 w-full leading-none block "></textarea> -->
															<select name="items" id="items" class="form-control border" v-model="comp.compose_item_id" @change="checkVariant(index)" :disabled="draft==1 && comp.id!=0">
																<option :value="item.id" v-for="item in listitems" :key="item.id">{{ item.item_description }}</option>
															</select>
														</td>
														<td class="p-0 text-xs">
															<select name="variant_comp" id="variant_comp" class="form-control border" v-model="comp.variant_id" @change="checkVariantqty(index,comp.variant_id,comp.compose_item_id)" :disabled="draft==1 && comp.id!=0">
																<option :value="v.id" v-for="v in variant[index]" :key="v.id">{{ v.supplier_name }}, {{ v.brand }}</option>
															</select>
															<input type="hidden" v-model="comp.pr_no">
															<!-- <input type="text" rows="2" class="p-1 m-0 w-full leading-none block" v-model="comp.variant" list="variant_list">
															<datalist id="variant_list">
																<option :value="v.id" v-for="v in variant" :key="v.id"></option>
															</datalist> -->
														</td>
														<td class="p-0 text-xs">
															<textarea type="number" v-model="comp.quantity" rows="2" class="p-1 m-0 w-full leading-none block " placeholder="Quantity" :disabled="draft==1 && comp.id!=0" @blur="checkMaxqty(index)"></textarea>
															<input type="hidden" v-model="comp.checker_qty">
														</td>
														<td class="p-1  font-bold">
															<div class="p-0 space-x-1 flex justify-center" v-if="comp.id==0">
																<a @click="removeComposite(index)" class="text-white btn btn-xs btn-danger btn-rounded">
																	<TrashIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></TrashIcon>
																</a>
															</div>
															<div class="p-0 space-x-1 flex justify-center" v-else>
																<a @click="deleteComposite(comp.id,composite_list.length,comp.compose_item_id,comp.quantity,comp.variant_id,index,item_id)" class="text-white btn btn-xs btn-danger btn-rounded">
																	<TrashIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></TrashIcon>
																</a>
															</div>
														</td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>

							<div v-else-if="choice === 'variant'">
								<p class="text-danger" v-for="erritm in error_items" v-if="error_items.length > 0">{{ erritm }}</p>
								<div class="flex justify-start space-x-1 mt-2">
									<button id="composite" class="bg-gray-300 text-white btn-sm w-32 rounded-t-lg focus:outline-none" v-on:click="choice = 'composite'">Composite</button>
									<button id="variant" class="bg-gray-400 text-white btn-sm w-32 rounded-t-lg focus:outline-none" v-on:click="choice = 'variant'">Variant</button>
									<!-- <button id="variant" class="bg-gray-300 text-white btn-sm w-32 rounded-t-lg focus:outline-none" v-on:click="choice = 'variant'">No Variant</button> -->
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
														<th class="font-xxs" width="10%">Item Status</th>
														<th class="font-xxs p-1 font-xxs" align="center" width="1%">
															<div class="space-x-1 flex justify-center">
																<a href='#' @click="addRowVariant" class="btn btn-xs btn-primary btn-rounded">
																	<PlusIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></PlusIcon>
																</a>
															</div>
														</th>
													</tr>
												</thead>
												<tbody>
													<tr v-for="(variant,index) in variant_list">
														<td class="p-0 text-xs">
															<input type="hidden" v-model="variant.id">
															<select name="supplier" id="supplier" class="form-control border-none !text-xs py-1" v-model="variant.supplier_id">
																<option :value="sup.id" v-for="sup in listsupplier" :key="sup.id">{{ sup.supplier_name }}</option>
															</select>
														</td>
														<td class="p-0 text-xs"><textarea type="text" rows="2" class="p-1 m-0 w-full leading-none block " v-model="variant.catalog_no"></textarea></td>
														<td class="p-0 text-xs">
															<!-- <textarea type="text" rows="2" class="p-1 m-0 w-full leading-none block " v-model="variant.brand"></textarea> -->
															<input type="text" rows="2" class="p-1 m-0 w-full leading-none block" @keyup="autosuggestBrand(index)" v-model="variant.brand" list="brand_list">
															<datalist id="brand_list">
																<option :value="b.brand" v-for="b in brand" :key="b.brand"></option>
															</datalist>
														</td>
														<td class="p-0 text-xs"><textarea type="text" rows="2" class="p-1 m-0 w-full leading-none block " v-model="variant.serial_no"></textarea></td>
														<td class="p-0 text-xs"><input type="date" class="p-1 m-0 w-full leading-none block " v-model="variant.expiration" /></td>
														<td class="p-0 text-xs"><textarea type="text" rows="2" class="p-1 m-0 w-full leading-none block " v-model="variant.barcode"></textarea></td>
														<td class="p-0 text-xs"><textarea type="number" rows="2" class="p-1 m-0 w-full leading-none block " v-model="variant.quantity"></textarea></td>
														<td class="p-0 text-xs"><textarea type="number" rows="2" class="p-1 m-0 w-full leading-none block " v-model="variant.composite_quantity" readonly></textarea></td>
														<td class="p-0 text-xs"><textarea type="number" rows="2" class="p-1 m-0 w-full leading-none block " :value="variant.quantity+variant.composite_quantity" readonly></textarea></td>
														<td class="p-0 text-xs"><textarea type="number" v-model="variant.unit_cost" rows="2" class="p-1 m-0 w-full leading-none block " ></textarea></td>
														<td class="p-0 text-xs">
															<select class="p-1 m-0 leading-none w-36 block text-xs whitespace-nowrap" v-model="variant.currency">
																<option value="" disabled selected>Select Currency</option>
																<option v-for="cur in currency" v-bind:key="cur" v-bind:value="cur">{{  cur }}</option>
															</select>	
														</td>
														<td class="p-0 text-xs"><textarea type="number" rows="2" class="p-1 m-0 w-full leading-none block " :value="variant.quantity*variant.unit_cost" readonly></textarea></td>
														<td class="p-0 text-xs">
															<!-- <textarea type="text" rows="2" class="p-1 m-0 w-full leading-none block " v-model="variant.uom"></textarea> -->
															<input type="text" rows="2" class="p-1 m-0 w-full leading-none block" @keyup="autosuggestUom(index)" v-model="variant.uom" list="uom_list">
															<datalist id="uom_list">
																<option :value="u.uom" v-for="u in uom" :key="u.uom"></option>
															</datalist>
														</td>
														<td class="p-0 text-xs">
															<input type="text" rows="2" class="p-1 m-0 w-full leading-none block" @keyup="autosuggestColor(index)" v-model="variant.color" list="colors_list">
															<datalist id="colors_list">
																<option :value="c.color" v-for="c in colors" :key="c.color"></option>
															</datalist>
														</td>
														<td class="p-0 text-xs">
															<input type="text" rows="2" class="p-1 m-0 w-full leading-none block" @keyup="autosuggestSize(index)" v-model="variant.size" list="sizes_list">
															<datalist id="sizes_list">
																<option :value="s.size" v-for="s in sizes" :key="s.size"></option>
															</datalist>
														</td>
														<td class="p-0 text-xs">
															<select name="item_status" id="item_status" class="form-control border-none !text-xs py-1" v-model="variant.item_status_id">
																<option :value="itemstatus.id" v-for="itemstatus in liststatus" :key="itemstatus.id">{{ itemstatus.status }}</option>
															</select>
														</td>
														<td class="p-1  font-bold">
															<div class="p-0 space-x-1 flex justify-center">
																<a href='#' @click="removeVariant(index)" class="text-white btn btn-xs btn-danger btn-rounded">
																	<TrashIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></TrashIcon>
																</a>
															</div>
														</td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
							
							<!-- <div v-else-if="choice === 'variant'">
								<p class="text-danger" v-for="erritm in error_items" v-if="error_items.length > 0">{{ erritm }}</p>
								<div class="flex justify-start space-x-1 mt-2">
									<button id="composite" class="bg-gray-300 text-white btn-sm w-32 rounded-t-lg focus:outline-none" v-on:click="choice = 'composite'">Composite</button>
									<button id="variant" class="bg-gray-300 text-white btn-sm w-32 rounded-t-lg focus:outline-none" v-on:click="choice = 'variant'">Variant</button>
									<button id="variant" class="bg-gray-400 text-white btn-sm w-32 rounded-t-lg focus:outline-none" v-on:click="choice = 'variant'">No Variant</button>
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
														<th class="font-xxs" width="8%">Item Status</th>

														<th class="font-xxs p-1 font-xxs" align="center" width="1%">
															<div class="space-x-1 flex justify-center">
																<a href="#" @click="addRowvariant" class="btn btn-xs btn-primary btn-rounded">
																	<PlusIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></PlusIcon>
																</a>
															</div>
														</th>
													</tr>
												</thead>
												<tbody>
													<tr v-for="(novar, index) in variant_list">
														<td class="p-0 text-xs"><textarea type="text" rows="2" class="p-1 m-0 w-full leading-none block " v-model="novar.unit_cost"></textarea></td>
														<td class="p-0 text-xs"><textarea type="text" rows="2" class="p-1 m-0 w-full leading-none block " v-model="novar.selling_price"></textarea></td>
														<td class="p-0 text-xs"><input type="date" rows="2" class="p-1 m-0 w-full leading-none block " v-model="novar.expiration"/></td>
														<td class="p-0 text-xs"><textarea type="text" rows="2" class="p-1 m-0 w-full leading-none block " v-model="novar.bar_code"></textarea></td>
														<td class="p-0 text-xs"><textarea type="text" rows="2" class="p-1 m-0 w-full leading-none block " v-model="novar.serial_no"></textarea></td>
														<td class="p-0 text-xs">
															<select name="item_status" id="item_status" class="form-control border" v-model="novar.item_status">
																<option :value="itemstatus.id" v-for="itemstatus in liststatus" :key="itemstatus.id">{{ itemstatus.status }}</option>
															</select>	
														</td>
														<td class="p-1  font-bold">
															<div class="p-0 space-x-1 flex justify-center">
																<a href="#" @click="removevariant(index)" class="text-white btn btn-xs btn-danger btn-rounded">
																	<TrashIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></TrashIcon>
																</a>
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
									<button class="bg-gray-300 text-white btn-sm w-32 rounded-t-lg focus:outline-none" v-on:click="choice = 'composite'">Composite</button>
									<button class="bg-gray-300 text-white btn-sm w-32 rounded-t-lg focus:outline-none" v-on:click="choice = 'variant'">Variant</button>
									<!-- <button class="bg-gray-300 text-white btn-sm w-32 rounded-t-lg focus:outline-none" v-on:click="choice = 'variant'">No Variant</button> -->
								</div>
							</div>

							<div class="row mt-3">
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
							
							<div class="row  mt-2">
								<div class="col-lg-4 px-1">
									<div class="form-group">
										<div class="flex justify-start ">
											<span class="text-xs text-gray-500 leading-none uppercase">Image 1</span>
										</div>
										<input type="file" accept="image/*" id="image1" @change="upload_image1" class="form-control border my-1">
										<div class="avatar img-fluid img-circle" style="margin-top:10px">
											<img :src="imageUrl1"/>
										</div>
									</div>										
								</div>	
								<div class="col-lg-4 px-1">
									<div class="form-group">
										<div class="flex justify-start ">
											<span class="text-xs text-gray-500 leading-none uppercase">Image 2</span>
										</div>
										<input type="file" accept="image/*" id="image2" @change='upload_image2' class="form-control border my-1">
										<div class="avatar img-fluid img-circle" style="margin-top:10px">
											<img :src="imageUrl2"/>
										</div>
									</div>										
								</div>	
								<div class="col-lg-4 px-1">
									<div class="form-group">
										<div class="flex justify-start ">
											<span class="text-xs text-gray-500 leading-none uppercase">Image 3</span>
										</div>
										<input type="file" accept="image/*" id="image3" @change='upload_image3' class="form-control border my-1">
										<div class="avatar img-fluid img-circle" style="margin-top:10px">
											<img :src="imageUrl3"/>
										</div>
									</div>										
								</div>							
							</div>
							<!-- <div class="mt-2 mb-2 mt-2 border-b"></div> -->
							
							<div class="row">
								<div class="col-lg-12 px-1">
									<div class="pt-3 mt-3 border-t border-dashed flex justify-end space-x-2">
										<input v-model="item_id" type="hidden">
										<button  @click="onSaveDraft()" class="btn btn-sm btn-warning btn- w-32 text-white" id='savedraft'>Save As Draft</button>
										<button  @click="onSave()" class="btn btn-sm btn-primary btn- w-60" id='save'>Save</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

			</div>
		</div>
		<div class="modal pt-5 px-3" :class="{ show:showItemname }">
			<div @click="closeModal" class="w-full h-full fixed"></div>
			<div class="modal__content w-2/4 p-4 !mt-[100px] mb-5">
				<div class="row">
					<div class="col-lg-12 flex justify-between">
						<p class="mb-0 font-bold">Edit Duplicate Items</p>
						<a href="#" class="text-gray-600" @click="closeModal">
							<XMarkIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"></XMarkIcon>
						</a>
					</div>
				</div>
				<hr class="mt-0">
				<div class="row" >
					<div class="col-lg-12">
						<div class="flex justify-between py-1" v-for="(i,index) in itemlist">
							<span class="p-2 px-3 w-12 bg-gray-400 text-white">{{ index+1 }}</span>
							<input type="text" class="form-control border !rounded-l-none" v-model="i.item_description">
							<input type="hidden" v-model="i.id">
						</div>
						<div class="flex justify-end mt-2">
							<button class="btn btn-primary btn-sm" @click="updateCompositeDuplicate()">Update</button>
						</div>
					</div>
				</div>
			</div>
		</div>
    </navigation>
</template>
