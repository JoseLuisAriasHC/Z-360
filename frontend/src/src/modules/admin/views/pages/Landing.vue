<script setup lang="ts">
import FeaturesWidget from '@admin/components/landing/FeaturesWidget.vue';
import FooterWidget from '@admin/components/landing/FooterWidget.vue';
import HeroWidget from '@admin/components/landing/HeroWidget.vue';
import HighlightsWidget from '@admin/components/landing/HighlightsWidget.vue';
import PricingWidget from '@admin/components/landing/PricingWidget.vue';
import TopbarWidget from '@admin/components/landing/TopbarWidget.vue';
</script>

<template>
    <div class="bg-surface-0 dark:bg-surface-900">
        <div id="home" class="landing-wrapper overflow-hidden">
            <div class="py-6 px-6 mx-0 md:mx-12 lg:mx-20 lg:px-20 flex items-center justify-between relative lg:static">
                <TopbarWidget />
            </div>
            <HeroWidget />
            <FeaturesWidget />
            <HighlightsWidget />
            <PricingWidget />
            <FooterWidget />
        </div>
    </div>
</template>
