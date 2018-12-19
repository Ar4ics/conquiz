<template>
    <div class="card bg-white">
        <h5 class="card-header text-center">Кто здесь</h5>
        <div class="card-body">
                <span class="row no-gutters" v-for="(u, i) in online_users" :key="i.id">
                    {{ u.name }}
                </span>
        </div>
    </div>
</template>

<script>
    import {mapState} from 'vuex';

    export default {
        name: "GameUsers",
        props: [],
        computed: {
            ...mapState([
                'online_users'
            ]),
        },
        mounted() {

        }
    }
</script>

<style scoped>

</style>