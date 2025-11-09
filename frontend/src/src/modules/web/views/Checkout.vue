<script setup lang="ts">
import DatosEnvioForm from '../components/checkout/DatosEnvioForm.vue';

</script>



<template>
    <div class="card flex justify-center">
        <Stepper value="1" linear class="w-full">
            <StepList>
                <Step value="1">Datos de envío</Step>
                <Step value="2">Pago</Step>
                <Step value="3">Compra finalizada</Step>
            </StepList>
            <StepPanels>
                <StepPanel v-slot="{ activateCallback }" value="1">
                    <div class="grid grid-cols-12 gap-8 mx-auto xl:pt-20 xl:w-3/5 text-text-light">
                        <div class="col-span-12 xl:col-span-8 flex flex-col" style="min-height: 65vh">
                            <DatosEnvioForm :onNextStep="() => activateCallback('2')" />
                        </div>
                        <div class="col-span-12 xl:col-span-4 px-8 xl:p-0">
                            <div class="xl:sticky xl:top-32"></div>
                        </div>
                    </div>
                </StepPanel>
                <StepPanel v-slot="{ activateCallback }" value="2">
                    <div class="flex flex-col h-48">
                        <div
                            class="border-2 border-dashed border-surface-200 dark:border-surface-700 rounded bg-surface-50 dark:bg-surface-950 flex-auto flex justify-center items-center font-medium">
                            Content II
                        </div>
                    </div>
                    <div class="flex pt-6 justify-between">
                        <Button label="Back" severity="secondary" icon="pi pi-arrow-left" @click="activateCallback('1')" />
                        <Button label="Next" icon="pi pi-arrow-right" iconPos="right" @click="activateCallback('3')" />
                    </div>
                </StepPanel>
                <StepPanel v-slot="{ activateCallback }" value="3">
                    <div class="flex flex-col h-48">
                        <div
                            class="border-2 border-dashed border-surface-200 dark:border-surface-700 rounded bg-surface-50 dark:bg-surface-950 flex-auto flex justify-center items-center font-medium">
                            Content III
                        </div>
                    </div>
                    <div class="pt-6">
                        <Button label="Back" severity="secondary" icon="pi pi-arrow-left" @click="activateCallback('2')" />
                    </div>
                </StepPanel>
            </StepPanels>
        </Stepper>
    </div>
</template>

<style scoped>
    input.p-inputtext {
        padding-top: 1.2rem;
        padding-bottom: 1.2rem;
    }
</style>
