<script setup lang="ts">
    import { ref, onMounted, computed } from 'vue';
    import { useRouter } from 'vue-router';
    import { type ProductoDetalleData, type TallaStock, type VarianteDisponible, ProductoService } from '@/modules/web/services/ProductoService';
    import MainGaleria from '@web/components/Producto/MainGaleria.vue';
    import { getParamId } from '@/utils/utils';
    import noImageSvg from '@/assets/img/no-image.svg';
    import VariantesGaleria from '../components/Producto/VariantesGaleria.vue';
    import InformacionAdicional from '../components/Producto/InformacionAdicional.vue';
    import ModalTallasDisponibles from '../components/Producto/ModalTallasDisponibles.vue';

    // --- PROPS Y HOOKS ---
    const router = useRouter();

    const idProductoVariante = getParamId();
    const loading = ref(false);
    const error = ref<string | null>(null);

    const productoDetalle = ref<ProductoDetalleData>();
    const showModalTalla = ref<boolean>(false);
    const showModalCesta = ref<boolean>(false);
    const tallaSelecionada = ref<TallaStock | null>(null);

    const imagenesGaleria = computed(() => {
        if (!productoDetalle.value) return [];

        const imagenPrincipal = productoDetalle.value.variante_seleccionada.imagen_principal ?? noImageSvg;
        const adicionales = productoDetalle.value.variante_seleccionada.imagenes_adicionales;

        return [imagenPrincipal, ...adicionales];
    });

    const variantes = computed(() => {
        if (!productoDetalle.value) return [];

        return productoDetalle.value.variantes.map((variante) => ({
            ...variante,
            imagen_principal: variante.imagen_principal || noImageSvg,
        }));
    });

    const envioGratis = computed(() => {
        if (!productoDetalle.value) return false;

        const precioConDescuento = productoDetalle.value.variante_seleccionada.precio_con_descuento;
        const minimoEnvioGratis = productoDetalle.value.envio.envio_gratis_desde;

        return precioConDescuento >= minimoEnvioGratis;
    });

    const mainImage = computed(() => {
        return productoDetalle.value?.variante_seleccionada.imagen_principal ?? noImageSvg;
    });

    const loadProducto = async (idProductoVariante: number) => {
        loading.value = true;
        error.value = null;

        try {
            productoDetalle.value = await ProductoService.getProductoDetalle(idProductoVariante);
            console.log(productoDetalle.value);
            productoDetalle.value.variante_seleccionada.imagen_principal ?? noImageSvg;
        } catch (e) {
            router.push({ name: 'not-found' });
        } finally {
            loading.value = false;
        }
    };

    const handleTallaSelected = (tallaStock: TallaStock) => {
        showModalTalla.value = false;
        tallaSelecionada.value = tallaStock;
        console.log(tallaSelecionada.value);
    };

    const handleAddProducto = () => {
        if (tallaSelecionada.value) {
            showModalCesta.value = true;
        } else {
            showModalTalla.value = true;
        }
    };

    onMounted(() => {
        if (idProductoVariante.value) {
            loadProducto(idProductoVariante.value);
        }
    });
</script>

<template>
    <div class="xl:flex gap-10 w-full px-4 sm:px-10 2xl:px-72 xl:py-6">
        <div class="container-producto-galeria xl:sticky xl:top-20 xl:h-full">
            <MainGaleria :imagenes="imagenesGaleria" />
        </div>

        <div class="w-full">
            <div class="2xl:flex 2xl:justify-between 2xl:w-full">
                <div>
                    <div class="font-semibold text-lg text-muted-light capitalize mb-2">
                        {{ productoDetalle?.producto.tipo }} - {{ productoDetalle?.producto.marca.nombre }}
                    </div>
                    <div class="font-bold text-3xl font-oswald capitalize">{{ productoDetalle?.producto.nombre }}</div>
                </div>

                <div class="2xl:text-right">
                    <div class="mb-2">
                        IVA incluido
                        <span v-if="envioGratis">
                            más,
                            <span class="text-verde">Envío gratis</span>
                        </span>
                    </div>
                    <div class="font-bold text-4xl font-oswald" :class="{ 'text-rojo': productoDetalle?.variante_seleccionada.descuento_activo }">
                        <span v-if="productoDetalle?.variante_seleccionada.descuento_activo" class="text-muted-light text-3xl line-through mr-2">
                            {{ productoDetalle?.variante_seleccionada.precio }} €
                        </span>
                        {{ productoDetalle?.variante_seleccionada.precio_con_descuento }} €
                    </div>
                </div>
            </div>

            <div class="mt-8 xl:mt-16">
                <Tag v-for="etiqueta in productoDetalle?.etiquetas" severity="secondary" :value="etiqueta.nombre" class="mr-2 mb-2" />
            </div>
            <div class="w-full mt-4 font-rubik">Color: {{ productoDetalle?.variante_seleccionada.color.nombre }}</div>
            <div class="flex">
                <VariantesGaleria :variantes="variantes" />
            </div>

            <div class="flex flex-col gap-6 w-full mt-10 font-rubik">
                <button
                    @click="showModalTalla = true"
                    class="w-full font-semibold text-xl font-rubik bg-background-light border border-muted-light py-4 rounded-lg text-left ps-4">
                    <span v-if="tallaSelecionada" class="font-bold">
                        {{ tallaSelecionada.talla.numero }}
                    </span>
                    <span v-else>Seleccionar Talla</span>
                </button>
                <button
                    @click="handleAddProducto()"
                    class="w-full font-semibold text-xl font-rubik bg-background-dark border border-background-dark text-background-light py-4 rounded-lg hover:bg-white hover:text-black transition duration-300">
                    <i class="pi pi-shopping-bag mr-2" style="font-size: 1.25rem"></i>
                    Añadir a la cesta
                </button>
            </div>

            <div class="flex flex-col gap-2 mt-8 font-rubik text-lg text-muted-light">
                <div class="flex">
                    <i class="pi pi-truck mr-2" style="font-size: 1.75rem"></i>
                    Coste de envio: {{ productoDetalle?.envio.costo_envio }}
                </div>
                <div class="flex">
                    <i class="pi pi-box mr-2" style="font-size: 1.75rem"></i>
                    {{ productoDetalle?.envio.mensaje }}
                </div>
            </div>

            <div class="w-full">
                <InformacionAdicional :productoDetalle="productoDetalle" />
            </div>
        </div>

        <ModalTallasDisponibles
            v-model:visible="showModalTalla"
            :productoDetalle="productoDetalle"
            :mainImage="mainImage"
            @talla-seleccionada="handleTallaSelected" />

        <Drawer
            v-model:visible="showModalCesta"
            header="#"
            position="right"
            :pt="{
                root: { style: 'width: 33rem;' },
                title: { style: 'opacity: 0;' },
            }">
            <div class="flex border-2 px-6 py-4 rounded-lg border-verde">
                <i class="pi pi-check-circle mr-2 text-verde" style="font-size: 1.5rem" />
                El artículo se ha añadido a la cesta de la compra.
            </div>

            <div class="mt-6">
                <div class="text-center font-rubik font-semibold text-2xl mb-6">Tu cesta de la compra</div>
                <div class="flex gap-4">
                    <div style="height: 128px; width: 128px">
                        <img :src="mainImage" alt="" />
                    </div>
                    <div>
                        <p class="font-rubik text-lg mb-1">44 €</p>
                        <p class="font-rubik text-lg leading-5 capitalize text-muted-ligh">puma</p>
                        <p class="font-rubik text-lg leading-5 capitalize font-semibold mb-4">Fade Nitro LS</p>
                        <p class="font-rubik text-md leading-5 capitalize">
                            <span class="text-muted-light">Color:</span>
                            Plata
                        </p>
                        <p class="font-rubik text-md leading-5">
                            <span class="text-muted-light">Talla:</span>
                            1
                        </p>
                        <p class="font-rubik text-md leading-5">
                            <span class="text-muted-light">Cantidad:</span>
                            1
                        </p>
                    </div>
                </div>
            </div>
        </Drawer>
    </div>
</template>

<style scoped>
    .container-producto-galeria {
        width: 100%;
        margin-bottom: 2rem;
    }

    @media (min-width: 1280px) {
        .container-producto-galeria {
            width: 48.7rem;
            margin-bottom: 0;
        }
    }
</style>
