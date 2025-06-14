<script setup >
import {  ref, onMounted } from 'vue';
import { useRouter } from "vue-router";
const company = ref({});

onMounted(async () => {
    const response = await fetch('/api/constants');
    const data = await response.json();
    company.value = data.company;
    
});
</script>

<template>
   
    <table class="border-2 border-gray-200 mb-2" style = "width:100%">
        <tr>
            <td class="py-1" align="center">
                <img :src="company.logo" class="h-[55px] px-1 py-1" alt="Company Logo">
            </td>
            <td class="border-r-2 border-gray-200 py-1">
                <p class="leading-tight text-md m-0 font-bold">{{ company.name }}</p>
                <p class="leading-tight text-sm m-0">{{ company.address_1 }}</p>
                <p class="leading-tight text-sm m-0">{{ company.phone }}</p>
            </td>
            <td width="50%" align="center">
                <p class="text-sm uppercase m-0 font-bold">
                    <slot/>
                </p>
                <!-- <p class="text-xs  m-0">Printed by : Jonah Benares - December 16, 2024</p> -->
            </td>
        </tr>
    </table>
</template>