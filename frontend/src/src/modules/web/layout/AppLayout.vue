<script setup lang="ts">
    import AppTopbar from '@web/layout/AppTopbar.vue';
    import AppFooter from '@web/layout/AppFooter.vue';
</script>

<template>
    <div class="layout-wrapper bg-background-light">
        <app-topbar></app-topbar>
        <div class="main p-0 min-h-screen font-sans bg-background-light">
            <router-view></router-view>
        </div>

        <app-footer></app-footer>
    </div>
</template>

<style scoped>
    .main {
        padding-top: 3rem;
    }

    @media (min-width: 1280px) {
        .main {
            padding-top: 6.7rem;
        }
    }
</style>
