<script setup lang="ts">
    import { computed, ref } from 'vue';
    import { CuponService, type Cupon } from '../../services/CuponService';
    import { useCestaStore } from '../../stores/cesta';
    import { useSettingsStore } from '@/stores/settings';

    const cesta = useCestaStore();
    const settings = useSettingsStore();

    const op = ref();
    const codigoCupon = ref<string>('');
    const cuponActivo = ref<Cupon | null>(null);
    const msgErrorCupon = ref<string>('');

    const showModalErrorCupon = ref(false);

    const toggleInfoSubtotal = (event: any) => {
        op.value.toggle(event);
    };

    const envioGratis = computed(() => {
        if (!settings.envioSettings) return false;
        return cesta.total >= settings.envioSettings.free_coste_envio_from;
    });

    const costeEnvio = computed(() => {
        if (!settings.envioSettings) return 0;
        return envioGratis.value ? 0 : settings.envioSettings.coste_envio;
    });

    const descuentoCupon = computed(() => {
        if (!cuponActivo.value) return 0;

        if (cuponActivo.value.tipo === 'fijo') {
            return cuponActivo.value.descuento;
        } else {
            return cesta.total * (cuponActivo.value.descuento / 100);
        }
    });

    const totalMasEnvioCupon = computed(() => {
        return cesta.total - descuentoCupon.value + costeEnvio.value;
    });

    const handleAplicarCupon = async () => {
        const data = await CuponService.getCuponByCodigo(codigoCupon.value);

        if (data.success) {
            cuponActivo.value = data.data;
        } else {
            msgErrorCupon.value = data.message;
            showModalErrorCupon.value = true;
        }
    };
</script>
<template>
    <h2 class="font-semibold text-3xl text-text-light">Resumen</h2>
    <div class="py-4 border-b">
        <Accordion value="0">
            <AccordionPanel value="0">
                <AccordionHeader>
                    <span class="font-semibold text-lg">¿Tienes un código promocional?</span>
                </AccordionHeader>
                <AccordionContent>
                    <div class="flex gap-3">
                        <IconField class="w-full">
                            <InputIcon class="pi pi-barcode" />
                            <InputText v-model="codigoCupon" type="text" placeholder="Código" class="w-full" />
                        </IconField>
                        <Button
                            @click="handleAplicarCupon"
                            class="font-semibold"
                            severity="contrast"
                            outlined
                            :disabled="codigoCupon == ''"
                            style="border-radius: 2rem; font-size: 1.25rem; padding: 0.5rem 2rem">
                            Aplicar
                        </Button>
                    </div>
                </AccordionContent>
            </AccordionPanel>
        </Accordion>

        <div class="flex gap-3 mt-3">
            <p class="font-semibold text-lg">Subtotal</p>
            <Button type="button" icon="pi pi-question-circle" size="small" severity="contrast" @click="toggleInfoSubtotal" />

            <Popover ref="op">
                <div class="flex flex-col gap-4 w-[20rem]">
                    <p>El subtotal refleja el importe total de tu pedido antes de aplicar cualquier descuento. No incluye los gastos de envío.</p>
                </div>
            </Popover>
            <span class="font-semibold text-lg ml-auto">
                <span v-if="cesta.total">{{ cesta.total }} €</span>
                <span v-else>—</span>
            </span>
        </div>

        <div v-if="descuentoCupon != 0" class="flex gap-3 mt-3">
            <p class="font-semibold text-lg">Descuento</p>
            <span class="font-semibold text-lg ml-auto text-verde">{{ descuentoCupon }} €</span>
        </div>

        <div class="flex gap-3 mt-3">
            <p class="font-semibold text-lg">Gastos de envío y gestión estimados</p>
            <span class="font-semibold text-lg ml-auto">
                <span v-if="cesta.total">
                    <span v-if="envioGratis">Gratuito</span>
                    <span v-else>{{ settings.envioSettings?.coste_envio }} €</span>
                </span>
                <span v-else>—</span>
            </span>
        </div>
    </div>

    <div class="py-4 border-b">
        <div class="flex gap-3 mt-3">
            <p class="font-semibold text-lg">Total</p>
            <span class="font-semibold text-lg ml-auto">
                <span v-if="cesta.total">
                    {{ totalMasEnvioCupon }}
                </span>
                <span v-else>—</span>
            </span>
        </div>
    </div>

    <div class="pt-8">
        <RouterLink :to="{name: 'home'}">
            <Button class="font-semibold w-full" severity="contrast" :disabled="cesta.items.length == 0" style="border-radius: 2rem; font-size: 1.25rem; padding: 0.5rem 2rem">
                Pasa por caja
            </Button>
        </RouterLink>
    </div>

    <Dialog v-model:visible="showModalErrorCupon" modal header="Error" :style="{ width: '40rem' }">
        {{ msgErrorCupon }}
        <div class="flex justify-center items-center">
            <Button
                @click="showModalErrorCupon = false"
                class="font-semibold"
                severity="contrast"
                style="border-radius: 2rem; font-size: 1.25rem; padding: 0.5rem 2rem">
                Aceptar
            </Button>
        </div>
    </Dialog>
</template>

<style>
    div.p-dialog-header {
        padding: 3rem 3rem 2rem 3rem;
    }

    div.p-dialog-content {
        font-size: large;
        padding: 0 3rem 3rem 3rem;
    }

    span.p-dialog-title {
        font-size: 1.8rem;
    }
</style>
