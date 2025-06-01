<template>
<div class="container m-0 p-0">
    <pre-loader v-if="isLoading" />
    <div v-else class="card-shadow-primary card-border card">
        <div class="px-2 py-1 d-flex justify-content-between align-items-center">
            <span>{{ $t('user_total_purchases', { total: totalPurchases }) }}</span>
            <button type="button" class="btn btn-secondary btn-sm" @click="refreshPurchaseList()" :title="$t('Refresh user purchase')"><i class="bi bi-sync-alt"></i> </button>
        </div>
        <div class="p-3" v-if="purchaseList.length == 0">
            No purchases
        </div>
        <div class="list-group" v-else>
            <div href="#" class="list-group-item" v-for="(item, index) in purchaseList" v-bind:key="index">
                <h5 class="py-2">
                    <img :src="item.item_icon" width="48" height="48" class="me-1"/>
                    {{ item.item_name }}</h5>
                <ul class="list-group">
                    <li class="list-group-item p-1 d-flex justify-content-between align-items-center">
                        {{$t('Amount')}}
                        <span class="badge bg-primary badge-pill">{{ item.amount }}</span>
                    </li>
                    <li class="list-group-item p-1 d-flex justify-content-between align-items-center">
                        {{$t('Sold at')}} <span class="badge bg-primary badge-pill"> {{ item.sold_at }}</span>
                    </li>
                    <li class="list-group-item p-1 d-flex justify-content-between align-items-center">
                        {{$t('License')}} <span class="badge bg-primary badge-pill">{{ item.license }}</span>
                    </li>
                    <li class="list-group-item p-1 d-flex justify-content-between align-items-center">
                        {{$t('Support amount')}} <span class="badge bg-primary badge-pill"> {{ item.support_amount }}</span>
                    </li>
                    <li class="list-group-item p-1 d-flex justify-content-between align-items-center">
                        {{$t('Supported until')}}
                        <div class="">
                        <span class="badge bg-primary"> {{ item.supported_until }}</span>
                        <span v-if="item.supported_peroid == 'expired'" class="badge bg-danger">{{$t('Expired')}}</span>
                        <span v-else class="badge badge-success">{{ item.supported_peroid }}</span>
                        </div>
                    </li>
                    <li class="list-group-item p-2 d-flex justify-content-between align-items-center">
                        {{$t('Purchase code')}} <span class="badge bg-primary badge-pill"> {{ item.purchase_code }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <v-dialog/>
</div>
</template>

<script>
import axios from 'axios';
export default {
    data(){
        return {
            isLoading: false,
            purchaseList: [],
            totalPurchases: 0
        }
    },
    props: {
        userid: { type: Number }
    },
    mounted () {
        this.loadPurchaseList()
    },
    methods: {
        loadPurchaseList(){
            this.isLoading = true
            axios
            .post(this.$myaccount_url+this.userid+'/purchases')
            .then(response => {
                    this.isLoading = false
                    this.purchaseList = response.data.data;
                    this.totalPurchases = response.data.meta.total;
            })
        },
        refreshPurchaseList(){
            this.isLoading = true
            axios
            .post(this.$myaccount_url+this.userid+'/purchases/refresh')
            .then(response => {
                this.isLoading = false
                if(response.data.status == 'ok'){
                    this.$showSuccess(response.data.message)
                    this.loadPurchaseList()
                }else{
                    this.$showError(response.data.errors.join(', '))
                }
            })
        }
    }
}
</script>
