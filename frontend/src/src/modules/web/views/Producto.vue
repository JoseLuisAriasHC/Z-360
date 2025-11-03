<script setup lang="ts">
    import { ref, onMounted } from 'vue';
    import { useRouter } from 'vue-router';
    import { type ProductoDetalleData, type VarianteDisponible, ProductoService } from '@/modules/web/services/ProductoService';
    import { getParamId } from '@/utils/utils';
    import noImageSvg from '@/assets/img/no-image.svg';

    // --- PROPS Y HOOKS ---
    const router = useRouter();

    const idProductoVariante = getParamId();
    const productoDetalle = ref<ProductoDetalleData>();
    const loading = ref(false);
    const error = ref<string | null>(null);
    const imagenesAdicionales = ref<string[]>([]);
    const variantes = ref<VarianteDisponible[]>();
    const envioGratis = ref<boolean>();

    const loadProducto = async (idProductoVariante: number) => {
        loading.value = true;
        error.value = null;

        try {
            productoDetalle.value = await ProductoService.getProductoDetalle(idProductoVariante);
            imagenesAdicionales.value = productoDetalle.value.variante_seleccionada.imagenes_adicionales;
            variantes.value = productoDetalle.value.variantes.map((variante) => ({
                ...variante,
                imagen_principal: variante.imagen_principal || noImageSvg,
            }));
            envioGratis.value = productoDetalle.value.variante_seleccionada.precio_con_descuento >= productoDetalle.value.envio.envio_gratis_desde;
        } catch (e) {
            console.error('Error al cargar productos:', e);
            error.value = 'No se pudieron cargar los productos. Intenta de nuevo.';
        } finally {
            loading.value = false;
        }
    };

    onMounted(() => {
        if (idProductoVariante.value) {
            loadProducto(idProductoVariante.value);
        }
    });

    const responsiveOptionsProducto = ref([
        { breakpoint: '1024px', numVisible: 5, numScroll: 1 },
        { breakpoint: '768px', numVisible: 3, numScroll: 1 },
        { breakpoint: '560px', numVisible: 3, numScroll: 1 },
    ]);

    const responsiveOptionsVariantes = ref([
        { breakpoint: '600px', numVisible: 7, numScroll: 1 },
        { breakpoint: '550px', numVisible: 3, numScroll: 1 },
    ]);
</script>

<template>
    <div class="xl:flex gap-10 w-full px-4 sm:px-10 xl:px-72 xl:py-6">
        <div class="container-producto-galeria">
            <Galleria
                :value="imagenesAdicionales"
                :responsiveOptions="responsiveOptionsProducto"
                :numVisible="10"
                :circular="true"
                thumbnailsPosition="bottom"
                indicatorsPosition="bottom"
                :showItemNavigators="true"
                :pt="{
                    thumbnailsViewport: { style: 'height: 100%;' },
                    nextButton: { style: 'color: #1A1A1A;' },
                    prevButton: { style: 'color: #1A1A1A;' },
                }">
                <template #item="slotProps">
                    <div class="imagen-main">
                        <img :src="slotProps.item.url" :alt="slotProps.item.alt" class="w-full h-full object-contain" />
                    </div>
                </template>
                <template #thumbnail="slotProps">
                    <div class="imagen-miniatura">
                        <img :src="slotProps.item.url" :alt="slotProps.item.alt" />
                    </div>
                </template>
            </Galleria>
        </div>

        <div class="w-full">
            <div class="xl:flex xl:justify-between xl:w-full">
                <div>
                    <div class="font-semibold text-lg text-muted-light capitalize">nike</div>

                    <div class="font-bold text-3xl font-oswald capitalize">Air Force 1 '07</div>
                </div>

                <div class="xl:text-right">
                    <div>IVA incluido <span v-if="envioGratis">más, Envío gratis</span></div>
                    <div class="font-bold text-4xl font-oswald capitalize">119,99 €</div>
                </div>
            </div>

            <div class="w-full mt-20 font-rubik">Color: Negro</div>
            <div class="flex">
                <Galleria
                    :value="variantes"
                    :responsiveOptions="responsiveOptionsVariantes"
                    :numVisible="10"
                    :circular="true"
                    :showItemNavigators="true"
                    :pt="{
                        nextButton: { style: 'color: #1A1A1A;' },
                        prevButton: { style: 'color: #1A1A1A;' },
                        itemsContainer: { style: 'display: none;' },
                        root: { style: 'border: none;' },
                        thumbnailItem: { style: 'opacity: 1;' },
                    }">
                    <template #thumbnail="data">
                        <div
                            class="container-variantes-galeria mx-2"
                            :class="{
                                'border border-muted-light shadow-md': data.item.seleccionada,
                            }">
                            <img :src="data.item.imagen_principal" :alt="data.item.id" class="w-full h-full object-contain" />
                        </div>
                    </template>
                </Galleria>
            </div>
            <div class="flex flex-col gap-6 w-full mt-10 font-rubik">
                <button
                    class="w-full font-semibold text-xl font-rubik bg-background-light border border-muted-light py-4 rounded-lg text-left ps-4">
                    Seleccionar Talla
                </button>
                <button
                    class="w-full font-semibold text-xl font-rubik bg-background-dark border border-background-dark text-background-light py-4 rounded-lg hover:bg-white hover:text-black transition duration-300">
                    <i class="pi pi-shopping-bag mr-2" style="font-size: 1.25rem"></i>
                    Añadir a la cesta Talla
                </button>
            </div>

            <div class="flex flex-col gap-2 mt-8 font-rubik text-lg text-muted-light">
                <div class="flex">
                    <i class="pi pi-truck mr-2" style="font-size: 1.75rem;" ></i>
                    Coste de envio: {{ productoDetalle?.envio.costo_envio }}
                </div>
                <div class="flex">
                    <i class="pi pi-box mr-2" style="font-size: 1.75rem;" ></i>
                    {{ productoDetalle?.envio.mensaje }}
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
    .imagen-miniatura {
        width: 109px;
        height: 109px;
    }

    .container-variantes-galeria {
        height: 78px;
        width: 78px;
    }

    .container-producto-galeria {
        width: 100%;
        margin-bottom: 2rem;
    }

    @media (min-width: 1280px) {
        .container-producto-galeria {
            width: 48.7rem;
            margin-bottom: 0;
        }
        .imagen-main {
            width: 680px;
            height: 680px;
        }
    }
</style>
