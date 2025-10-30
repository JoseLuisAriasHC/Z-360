<script setup lang="ts">
    import AppTopbar from '@web/layout/AppTopbar.vue';
    import AppFooter from '@web/layout/AppFooter.vue';
</script>

<template>
    <div class="layout-wrapper">
        <app-topbar></app-topbar>
        <div class="p-0 bg-gray-100 min-h-screen font-sans">
            <router-view></router-view>
        </div>
        <app-footer></app-footer>
        <div class="layout-mask animate-fadein"></div>
    </div>
</template>
