<script setup lang="ts">
    import { ref } from 'vue';

    const op = ref();
    const toggleInfoSubtotal = (event: any) => {
        op.value.toggle(event);
    };
</script>

<template>
    <div class="grid grid-cols-12 gap-8 mx-auto lg:pt-20 lg:w-2/3">
        <div class="col-span-12 xl:col-span-8 flex flex-col" style="min-height: 80vh">
            <Card>
                <template #title>
                    <span class="text-naranja font-bold text-xl uppercase">Envio gratis</span>
                </template>
                <template #content>
                    <p class="font-semibold text-lg text-muted-light">
                        No te quedes sin ese producto que tanto deseas. Es el momento ideal para completar tu carrito, ya que tienes el envío por
                        nuestra cuenta al superar los 80€ de compra. ¡Aprovecha y haz tu pedido ahora mismo antes de que se agote tu talla!
                    </p>
                </template>
            </Card>

            <div class="mt-4">
                <router-link :to="{ name: 'carrito' }">
                    <Button severity="contrast">
                        <span class="text-2xl mr-3">Comenzar</span>
                        <i class="pi pi-arrow-right" style="font-size: 1.5rem" />
                    </Button>
                </router-link>
            </div>

            <div class="pt-4 mt-auto border-solid border-t-2 border-muted-border">
                <div class="font-semibold text-3xl">Novedades</div>
                <div class="flex">
                    <p class="font-semibold text-lg">¿Quieres resivir novedades?</p>
                    <div class="ml-2 font-semibold text-lg">
                        <a href="#" class="text-muted-dark hover:border-b border-black">Únete a nosotros</a>
                        <span class="mx-1">o</span>
                        <a href="#" class="text-muted-dark hover:border-b border-black">Iniciar sesión</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-span-12 xl:col-span-4">
            <h1 class="font-semibold text-3xl">Resumen</h1>
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
                                    <InputText type="text" placeholder="Código" class="w-full" />
                                </IconField>
                                <Button
                                    class="font-semibold"
                                    severity="contrast"
                                    outlined
                                    disabled
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

                    <Popover
                        ref="op">
                        <div class="flex flex-col gap-4 w-[20rem]">
                            <p>
                                El subtotal refleja el importe total de tu pedido antes de aplicar cualquier descuento. No incluye los gastos de
                                envío.
                            </p>
                        </div>
                    </Popover>
                    <span class="font-semibold text-lg ml-auto">—</span>
                </div>

                <div class="flex gap-3 mt-3">
                    <p class="font-semibold text-lg">Gastos de envío y gestión estimados</p>

                    <span class="font-semibold text-lg ml-auto">—</span>
                </div>
            </div>

            <div class="py-4 border-b">
                <div class="flex gap-3 mt-3">
                    <p class="font-semibold text-lg">Total</p>

                    <span class="font-semibold text-lg ml-auto">—</span>
                </div>
            </div>

            <div class="pt-8">
                <Button
                    class="font-semibold w-full"
                    severity="contrast"
                    disabled
                    style="border-radius: 2rem; font-size: 1.25rem; padding: 0.5rem 2rem">
                    Pasa por caja
                </Button>
            </div>
        </div>
    </div>
</template>
