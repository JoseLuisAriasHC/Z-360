<script setup lang="ts">
    import type { ProductoDetalleData } from '@web/services/ProductoService';

    const props = defineProps<{
        productoDetalle: ProductoDetalleData | undefined;
    }>();
</script>

<template>
    <Accordion
        class="mt-10"
        multiple
        :pt="{
            content: { style: 'padding: 0; font-size: 1rem; line-height: 1.375rem; font-weight: 400; letter-spacing: 0;' },
        }">
        <AccordionPanel value="0">
            <AccordionHeader>Datos generales</AccordionHeader>
            <AccordionContent
                :pt="{
                    content: {
                        style: 'font-family: Rubik; padding: 0.5rem 0 0 0; font-size: 1.2rem; line-height: 1.375rem; font-weight: 400; letter-spacing: 0;',
                    },
                }">
                <div class="flex flex-col gap-3">
                    <div>
                        <span class="font-semibold text-lg">Descripción</span>
                        <p>
                            {{ productoDetalle?.producto.descripcion }}
                        </p>
                    </div>
                    <div>
                        <span class="font-semibold text-lg capitalize">Color de fábrica</span>
                        <p class="capitalize">
                            {{ productoDetalle?.variante_seleccionada.color.nombre }}
                        </p>
                    </div>
                    <div>
                        <span class="font-semibold text-lg capitalize">¿Para Quién?</span>
                        <p class="capitalize">
                            {{ productoDetalle?.producto.genero }}
                        </p>
                    </div>
                    <div>
                        <span class="font-semibold text-lg capitalize">Tipo de producto</span>
                        <p class="capitalize">
                            {{ productoDetalle?.producto.tipo }}
                        </p>
                    </div>
                    <div>
                        <span class="font-semibold text-lg capitalize">Tipo de corte</span>
                        <p class="capitalize">Corte {{ productoDetalle?.producto.altura_suela }}</p>
                    </div>
                    <div>
                        <span class="font-semibold text-lg capitalize">Tipo de cierre</span>
                        <p class="capitalize">{{ productoDetalle?.producto.cierre }}</p>
                    </div>
                    <div>
                        <span class="font-semibold text-lg capitalize">Código artículo</span>
                        <p class="capitalize">
                            {{ productoDetalle?.variante_seleccionada.id }}
                        </p>
                    </div>
                </div>
            </AccordionContent>
        </AccordionPanel>
        <AccordionPanel value="1">
            <AccordionHeader>Material e instrucciones de cuidado</AccordionHeader>
            <AccordionContent
                :pt="{
                    content: {
                        style: 'font-family: Rubik; padding: 0.5rem 0 0 0; font-size: 1.2rem; line-height: 1.375rem; font-weight: 400; letter-spacing: 0;',
                    },
                }">
                <div class="flex flex-col gap-3">
                    <div>
                        <span class="font-semibold text-lg">Cuidados</span>
                        <p>
                            {{ productoDetalle?.detalles.cuidados }}
                        </p>
                    </div>
                    <div>
                        <span class="font-semibold text-lg capitalize">Especificaciones parte superior</span>
                        <p class="capitalize">
                            {{ productoDetalle?.detalles.parte_superior }}
                        </p>
                    </div>
                    <div>
                        <span class="font-semibold text-lg capitalize">Especificaciones parte inferior</span>
                        <p class="capitalize">
                            {{ productoDetalle?.detalles.parte_inferior }}
                        </p>
                    </div>
                    <div>
                        <span class="font-semibold text-lg capitalize">Infomraciíon de la suela</span>
                        <p class="capitalize">
                            {{ productoDetalle?.detalles.suela }}
                        </p>
                    </div>
                </div>
            </AccordionContent>
        </AccordionPanel>
    </Accordion>
</template>

<style scoped>
    /* Custom para el accordion */
    .p-accordionheader {
        padding: 0.8rem 0;
        color: #666666;
        font-weight: 700;
        font-size: large;
        font-family: 'Rubik';
    }
</style>
