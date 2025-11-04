<script setup lang="ts">
    import { ref, onMounted } from 'vue';
    import { useRouter } from 'vue-router';
    import { type ProductoDetalleData, type VarianteDisponible, ProductoService } from '@/modules/web/services/ProductoService';
    import MainGaleria from '@web/components/Producto/MainGaleria.vue';
    import { getParamId } from '@/utils/utils';
    import noImageSvg from '@/assets/img/no-image.svg';
import VariantesGaleria from '../components/Producto/VariantesGaleria.vue';
import InformacionAdicional from '../components/Producto/InformacionAdicional.vue';

    // --- PROPS Y HOOKS ---
    const router = useRouter();

    const idProductoVariante = getParamId();
    const loading = ref(false);
    const error = ref<string | null>(null);

    const productoDetalle = ref<ProductoDetalleData>();
    const imagenesAdicionales = ref<string[]>([]);
    const variantes = ref<VarianteDisponible[]>();
    const envioGratis = ref<boolean>();
    const showModalTalla = ref<boolean>();
    const mainImage = ref<string>('');

    const loadProducto = async (idProductoVariante: number) => {
        loading.value = true;
        error.value = null;

        try {
            productoDetalle.value = await ProductoService.getProductoDetalle(idProductoVariante);

            imagenesAdicionales.value = productoDetalle.value.variante_seleccionada.imagenes_adicionales;
            mainImage.value = productoDetalle.value.variante_seleccionada.imagen_principal ?? noImageSvg;
            imagenesAdicionales.value.unshift(mainImage.value);

            productoDetalle.value.variante_seleccionada.imagen_principal ?? noImageSvg;
            envioGratis.value = productoDetalle.value.variante_seleccionada.precio_con_descuento >= productoDetalle.value.envio.envio_gratis_desde;
            variantes.value = productoDetalle.value.variantes.map((variante) => ({
                ...variante,
                imagen_principal: variante.imagen_principal || noImageSvg,
            }));
        } catch (e) {
            router.push({ name: 'not-found' });
        } finally {
            loading.value = false;
        }
    };

    onMounted(() => {
        if (idProductoVariante.value) {
            loadProducto(idProductoVariante.value);
        }
    });

</script>

<template>
    <div class="xl:flex gap-10 w-full px-4 sm:px-10 xl:px-72 xl:py-6">
        <div class="container-producto-galeria xl:sticky xl:top-20 xl:h-full">
            <MainGaleria :imagenesAdicionales="imagenesAdicionales"/>
        </div>

        <div class="w-full">
            <div class="xl:flex xl:justify-between xl:w-full">
                <div>
                    <div class="font-semibold text-lg text-muted-light capitalize mb-2">
                        {{ productoDetalle?.producto.tipo }} - {{ productoDetalle?.producto.marca.nombre }}
                    </div>
                    <div class="font-bold text-3xl font-oswald capitalize">{{ productoDetalle?.producto.nombre }}</div>
                </div>

                <div class="xl:text-right">
                    <div class="mb-2">
                        IVA incluido
                        <span v-if="envioGratis">
                            más,
                            <span class="text-verde">Envío gratis</span>
                        </span>
                    </div>
                    <div class="font-bold text-4xl font-oswald capitalize">{{ productoDetalle?.variante_seleccionada.precio_con_descuento }} €</div>
                </div>
            </div>

            <div class="w-full mt-20 font-rubik">Color: {{ productoDetalle?.variante_seleccionada.color.nombre }}</div>
            <div class="flex">
                <VariantesGaleria :variantes="variantes"/>
            </div>

            <div class="flex flex-col gap-6 w-full mt-10 font-rubik">
                <button
                    @click="showModalTalla = true"
                    class="w-full font-semibold text-xl font-rubik bg-background-light border border-muted-light py-4 rounded-lg text-left ps-4">
                    Seleccionar Talla
                </button>
                <button
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
                <InformacionAdicional :productoDetalle="productoDetalle"/>
            </div>
        </div>
        <Dialog v-model:visible="showModalTalla" modal :style="{ width: '50vw' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }">
            <div class="grid grid-cols-12 gap-8">
                <div class="col-span-12 xl:col-span-6">
                    <img :src="mainImage" alt="" />
                </div>
                <div class="col-span-12 xl:col-span-6"></div>
            </div>
        </Dialog>
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
