<script setup lang="ts">
    import visaSvg from '@/assets/img/visa.svg';
</script>

<template>
    <div class="py-6 px-6 mx-0 mt-20 b-top lg:mx-20" style="border-top: 1px solid var(--surface-border)">
        <div class="grid grid-cols-12 gap-4">
            <div class="col-span-12 md:col-span-10">
                <div class="grid grid-cols-12 gap-8 text-center md:text-left">
                    <div class="col-span-12 md:col-span-3">
                        <h4 class="font-medium text-2xl leading-normal mb-4 text-surface-900 dark:text-surface-0 ps-4">Por qué comprar</h4>
                        <button
                            class="button-footer w-full text-lg font-semibold ps-4 py-2 rounded-md text-left transition ease-in hover:bg-gray-200">
                            Cómo comprar
                        </button>
                        <button
                            class="button-footer w-full text-lg font-semibold ps-4 py-2 rounded-md text-left transition ease-in hover:bg-gray-200">
                            Formas de pago
                        </button>
                        <button
                            class="button-footer w-full text-lg font-semibold ps-4 py-2 rounded-md text-left transition ease-in hover:bg-gray-200">
                            Gastos de envío
                        </button>
                        <button
                            class="button-footer w-full text-lg font-semibold ps-4 py-2 rounded-md text-left transition ease-in hover:bg-gray-200">
                            Cupones descuento
                        </button>
                        <button
                            class="button-footer w-full text-lg font-semibold ps-4 py-2 rounded-md text-left transition ease-in hover:bg-gray-200">
                            Preguntas frecuentes
                        </button>
                        <button
                            class="button-footer w-full text-lg font-semibold ps-4 py-2 rounded-md text-left transition ease-in hover:bg-gray-200">
                            Tarjetas regalo
                        </button>
                    </div>

                    <div class="col-span-12 md:col-span-3">
                        <h4 class="font-medium text-2xl leading-normal mb-4 text-surface-900 dark:text-surface-0 ps-4">Quiénes somos</h4>
                        <button
                            class="button-footer w-full text-lg font-semibold ps-4 py-2 rounded-md text-left transition ease-in hover:bg-gray-200">
                            Quiénes somos
                        </button>
                        <button
                            class="button-footer w-full text-lg font-semibold ps-4 py-2 rounded-md text-left transition ease-in hover:bg-gray-200">
                            Compromisos
                        </button>
                        <button
                            class="button-footer w-full text-lg font-semibold ps-4 py-2 rounded-md text-left transition ease-in hover:bg-gray-200">
                            Nuestras tiendas
                        </button>
                        <button
                            class="button-footer w-full text-lg font-semibold ps-4 py-2 rounded-md text-left transition ease-in hover:bg-gray-200">
                            Nuestras Marcas
                        </button>
                        <button
                            class="button-footer w-full text-lg font-semibold ps-4 py-2 rounded-md text-left transition ease-in hover:bg-gray-200">
                            Condiciones de compra
                        </button>
                        <button
                            class="button-footer w-full text-lg font-semibold ps-4 py-2 rounded-md text-left transition ease-in hover:bg-gray-200">
                            Aviso legal
                        </button>
                    </div>

                    <div class="col-span-12 md:col-span-3">
                        <h4 class="font-medium text-2xl leading-normal mb-4 text-surface-900 dark:text-surface-0 ps-4">Contactar</h4>
                        <button
                            class="button-footer w-full text-lg font-semibold ps-4 py-2 rounded-md text-left transition ease-in hover:bg-gray-200">
                            Contacto y Ayuda
                        </button>
                        <button
                            class="button-footer w-full text-lg font-semibold ps-4 py-2 rounded-md text-left transition ease-in hover:bg-gray-200">
                            Devoluciones y Garantías
                        </button>
                        <button
                            class="button-footer w-full text-lg font-semibold ps-4 py-2 rounded-md text-left transition ease-in hover:bg-gray-200">
                            Política de cookies
                        </button>
                        <button
                            class="button-footer w-full text-lg font-semibold ps-4 py-2 rounded-md text-left transition ease-in hover:bg-gray-200">
                            Publicidad
                        </button>
                        <button
                            class="button-footer w-full text-lg font-semibold ps-4 py-2 rounded-md text-left transition ease-in hover:bg-gray-200">
                            Trabaja con nosotros
                        </button>
                        <button
                            class="button-footer w-full text-lg font-semibold ps-4 py-2 rounded-md text-left transition ease-in hover:bg-gray-200">
                            Canal ético
                        </button>
                    </div>

                    <div class="col-span-12 md:col-span-3">
                        <h4 class="font-medium text-2xl leading-normal mb-4 text-surface-900 dark:text-surface-0 ps-4">Comunidad</h4>
                        <button
                            class="button-footer flex w-full text-lg font-semibold ps-4 py-2 rounded-md text-left transition ease-in hover:bg-gray-200">
                            <svg
                                class="h-6 mr-3"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                enable-background="new 0 0 24 24"
                                fill="inherit"
                                data-testid="link-icon">
                                <path
                                    d="M16.5,2h-9C4.5,2,2,4.5,2,7.5v9c0,3,2.5,5.5,5.5,5.5h9c3,0,5.5-2.5,5.5-5.5v-9C22,4.5,19.5,2,16.5,2z M20.2,16.5
c0,2.1-1.7,3.7-3.7,3.7h-9c-2.1,0-3.7-1.7-3.7-3.7v-9c0-2.1,1.7-3.7,3.7-3.7h9c2.1,0,3.7,1.7,3.7,3.7V16.5L20.2,16.5z"></path>
                                <path
                                    d="M12,6.8c-2.8,0-5.2,2.3-5.2,5.2c0,2.8,2.3,5.2,5.2,5.2s5.2-2.3,5.2-5.2C17.2,9.2,14.8,6.8,12,6.8z M12,15.4
c-1.9,0-3.4-1.5-3.4-3.4s1.5-3.4,3.4-3.4s3.4,1.5,3.4,3.4S13.9,15.4,12,15.4z"></path>
                                <circle cx="17.4" cy="6.6" r="1.3"></circle>
                            </svg>
                            Instagram
                        </button>
                        <button
                            class="button-footer flex w-full text-lg font-semibold ps-4 py-2 rounded-md text-left transition ease-in hover:bg-gray-200">
                            <svg
                                class="h-6 mr-3"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                enable-background="new 0 0 24 24"
                                fill="inherit"
                                data-testid="link-icon">
                                <path
                                    d="M2.6077 2.87183L10.1131 12.9073L2.5603 21.0666H4.26014L10.8726 13.923L16.2154 21.0666H22L14.0723 10.4666L21.1024 2.87183H19.4025L13.3128 9.45092L8.39233 2.87183H2.6077ZM5.10744 4.12393H7.76492L19.4999 19.8143H16.8424L5.10744 4.12393Z"></path>
                            </svg>
                            Twitter
                        </button>
                        <button
                            class="button-footer flex w-full text-lg font-semibold ps-4 py-2 rounded-md text-left transition ease-in hover:bg-gray-200">
                            <svg
                                class="h-6 mr-3"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                enable-background="new 0 0 24 24"
                                fill="inherit"
                                data-testid="link-icon">
                                <path
                                    d="M20.9 2h-17.8c-.6 0-1.1.5-1.1 1.1v17.8c0 .6.5 1.1 1.1 1.1h9.6v-7.7h-2.6v-3h2.6v-2.3c0-2.6 1.6-4 3.9-4 1.1 0 2.1.1 2.3.1v2.7h-1.6c-1.3 0-1.5.6-1.5 1.5v1.9h3l-.4 3h-2.6v7.8h5.1c.6 0 1.1-.5 1.1-1.1v-17.8c0-.6-.5-1.1-1.1-1.1z"></path>
                            </svg>
                            Facebook
                        </button>
                        <button
                            class="button-footer flex w-full text-lg font-semibold ps-4 py-2 rounded-md text-left transition ease-in hover:bg-gray-200">
                            <svg
                                class="h-6 mr-3"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                enable-background="new 0 0 24 24"
                                fill="inherit"
                                data-testid="link-icon">
                                <path
                                    d="M14.6,9.3l-4,3.8c-0.1,0.1-0.2,0.3-0.3,0.5l-0.1,1c0,0.1-0.2,0.2-0.2,0l-0.5-1.9c-0.1-0.2,0-0.4,0.2-0.6l4.9-3
C14.6,9.2,14.7,9.3,14.6,9.3z"></path>
                                <path
                                    d="M12,2C6.5,2,2,6.5,2,12s4.5,10,10,10s10-4.5,10-10S17.5,2,12,2z M15.1,16.5c-0.1,0.2-0.3,0.3-0.5,0.2l-2.5-1.8
c-0.1-0.1-0.4-0.1-0.5,0L10.2,16C10,16.1,9.8,16,9.7,15.8l-1-3.1l-2.5-0.9c-0.3-0.1-0.3-0.5,0-0.5l10.3-4c0.2-0.1,0.4,0.1,0.4,0.3
L15.1,16.5z"></path>
                            </svg>
                            Telegram
                        </button>
                        <button
                            class="button-footer flex w-full text-lg font-semibold ps-4 py-2 rounded-md text-left transition ease-in hover:bg-gray-200">
                            <svg
                                class="h-6 mr-3"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                enable-background="new 0 0 24 24"
                                fill="inherit"
                                data-testid="link-icon">
                                <path
                                    d="M22.5,6.7c-0.3-1-1-1.7-1.9-2C18.9,4.3,12,4.3,12,4.3s-6.9,0-8.6,0.5c-0.9,0.3-1.7,1-1.9,2C1,8.4,1,12,1,12s0,3.6,0.5,5.3
c0.3,1,1,1.7,1.9,2c1.7,0.5,8.6,0.5,8.6,0.5s6.9,0,8.6-0.5c0.9-0.3,1.7-1,1.9-2C23,15.6,23,12,23,12S23,8.4,22.5,6.7z M9.8,15.3V8.7
l5.8,3.3L9.8,15.3z"></path>
                            </svg>
                            Youtube
                        </button>
                        <button
                            class="button-footer flex w-full text-lg font-semibold ps-4 py-2 rounded-md text-left transition ease-in hover:bg-gray-200">
                            <svg
                                class="h-6 mr-3"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                enable-background="new 0 0 24 24"
                                fill="inherit"
                                data-testid="link-icon">
                                <path
                                    fill-rule="evenodd"
                                    clip-rule="evenodd"
                                    d="M16.3729 2C16.7001 4.83809 18.2702 6.53015 21 6.71015V9.90226C19.418 10.0583 18.0323 9.53624 16.4205 8.55221V14.5224C16.4205 22.1067 8.22506 24.4767 4.93022 19.0406C2.81296 15.5424 4.10948 9.40424 10.9014 9.15823V12.5243C10.384 12.6083 9.83085 12.7403 9.32532 12.9144C7.81469 13.4304 6.95827 14.3964 7.19616 16.1005C7.65411 19.3646 13.5896 20.3306 13.096 13.9524V2.006H16.3729V2Z"></path>
                            </svg>
                            TikTok
                        </button>
                        <button
                            class="button-footer flex w-full text-lg font-semibold ps-4 py-2 rounded-md text-left transition ease-in hover:bg-gray-200">
                            <svg
                                class="h-6 mr-3"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                enable-background="new 0 0 24 24"
                                fill="inherit"
                                data-testid="link-icon">
                                <path
                                    fill-rule="evenodd"
                                    clip-rule="evenodd"
                                    d="M2.14911 0L0.537109 4.119V20.955H6.26811V24H9.49211L12.5371 20.955H17.1941L23.4631 14.686V0H2.14911V0ZM21.3131 13.612L17.7311 17.194H12.0001L8.95511 20.239V17.194H4.11911V2.149H21.3131V13.612ZM17.7311 6.269V12.531H15.5821V6.269H17.7311ZM12.0001 6.269V12.531H9.85111V6.269H12.0001Z"></path>
                            </svg>
                            Twitch
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-span-12 md:col-span-2">
                <a class="flex flex-wrap items-center justify-center md:justify-start md:mb-0 mb-4">
                    <img :src="visaSvg" alt="visa" />
                </a>
            </div>
        </div>
    </div>
</template>
