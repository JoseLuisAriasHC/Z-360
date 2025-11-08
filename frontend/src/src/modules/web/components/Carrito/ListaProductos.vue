<script setup lang="ts">
    import { useCestaStore } from '../../stores/cesta';

    const cesta = useCestaStore();
</script>

<template>
    <div v-for="item in cesta.items" class="flex gap-4">
        <div style="height: 164px; width: 164px">
            <img :src="item.imagen" :alt="item.nombre" />
        </div>
        <div class="flex-1 text-lg">
            <div class="capitalize text-muted-light">
                {{ item.marca }}
            </div>
            <div class="flex justify-between w-full">
                <RouterLink :to="{ name: 'producto-detalles', params: { id: item.id } }">
                    <div class="leading-5 capitalize font-semibold mb-4 transition duration-300 ease-in-out hover:text-muted-light">
                        {{ item.nombre }}
                    </div>
                </RouterLink>
                <div class="font-bold">
                    <span v-if="item.descuento_activo" class="text-muted-light line-through ml-2 font-normal">{{ item.precio }} €</span>
                    {{ item.precio_con_descuento }} €
                </div>
            </div>

            <p class="text-md capitalize">
                <span class="text-muted-light">Color:</span>
                {{ item.color }}
            </p>
            <p class="text-md">
                <span class="text-muted-light">Talla:</span>
                {{ item.talla }}
            </p>
            <div class="flex justify-center items-center border-2 w-max overflow-hidden mt-4" style="border-radius: 30px">
                <div
                    class="flex justify-center items-center hover:bg-text-dark h-[40px] w-[40px] cursor-pointer"
                    style="padding: 8px; border-radius: 30px"
                    @click="cesta.incrementarCantidad(item)">
                    <i class="pi pi-plus font-semibold"></i>
                </div>

                <div class="flex justify-center items-center w-[24px]">
                    <span class="font-semibold">
                        {{ item.cantidad }}
                    </span>
                </div>

                <div
                    class="flex justify-center items-center hover:bg-text-dark h-[40px] w-[40px] cursor-pointer"
                    style="padding: 8px; border-radius: 30px"
                    @click="cesta.decrementarCantidad(item)">
                    <i class="pi pi-minus font-semibold"></i>
                </div>
            </div>
        </div>
    </div>
</template>
